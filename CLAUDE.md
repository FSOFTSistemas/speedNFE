# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

SpeedNFE — a Laravel 9 ERP/fiscal application for Brazilian businesses (NFe, NFCe, MDFe, NFCom, CTe
electronic documents, PDV/cupom, stock, orders, accounts receivable/payable, cash flow, Pix payments). Server-rendered
Blade + Livewire is the primary UI; a JSON API (`/api/v1`) and an Inertia+React frontend are being built out
alongside it. This is a real production codebase in active development — expect mixed styles and in-progress
areas (see `AGENTS.md` for the older contributor guide, largely superseded by this file but still accurate
on conventions).

## Commands

```bash
composer install                    # PHP dependencies
npm install                         # front-end tooling

php artisan serve                   # dev server
npm run dev                         # Vite dev server (Blade assets + Inertia/React)
npm run build                       # production front-end build

php artisan migrate                 # run migrations

php artisan test                    # run PHPUnit suite
vendor/bin/phpunit                  # same, direct
vendor/bin/phpunit --filter=TestName          # single test method
vendor/bin/phpunit tests/Feature/SomeTest.php # single test file

vendor/bin/pint                     # format PHP (Laravel Pint / PSR-12)

php artisan route:list --path=api/v1   # inspect API routes
```

Setup: copy `.env.example` to `.env`, then `php artisan key:generate`. API docs are served at
`/api/documentation` (Swagger UI) and `/docs/openapi.yaml`.

**Test DB caution**: `phpunit.xml` has its sqlite in-memory overrides commented out, and no test uses
`RefreshDatabase`/`DatabaseTransactions`. This means `php artisan test` runs against whatever
`DB_CONNECTION`/`DB_DATABASE` is set in `.env` — currently a real MySQL database, not an ephemeral one. Be
careful about data-mutating tests; consider pointing `.env`'s DB vars at a disposable database before
running the suite.

## Architecture

### Two parallel front ends over one domain layer

- **Web (Blade/Livewire/AdminLTE)**: `routes/web.php` → `app/Http/Controllers/*Controller.php` (flat,
  not namespaced) → `app/Services/*Service.php` → Eloquent models in `app/Models`. Session-based `web` auth
  guard. This is the primary, feature-complete surface.
- **JSON API v1**: `routes/api.php` → `app/Http/Controllers/Api/V1/*Controller.php` (namespaced,
  `auth:api` guard) → the **same** `app/Services/*Service.php` classes, calling dedicated `*Api()` methods
  (e.g. `ProdutosService::listarApi()`, `criarApi()`, `buscarPermitidoApi()`) rather than the web methods.
  API controllers extend `App\Http\Controllers\Api\ApiController`, which provides `success()`/`message()`
  JSON envelope helpers. Requests are validated via `App\Http\Requests\Api\V1\*` FormRequests and responses
  are shaped through `App\Http\Resources\*Resource` (`JsonResource`). Keep API and web logic in the same
  service class per resource — don't fork business logic into a separate API-only service.
- **Inertia + React**: newer pages live under `resources/js/Pages`, wired through `resources/js/app.jsx`.
  Small surface so far (e.g. `notasEntradas.jsx`); most UI is still Blade/Livewire.

### Auth guards

Two independent guards defined in `config/auth.php`:
- `web` — session driver, used by Blade routes.
- `api` — **JWT** driver (`tymon/jwt-auth`), used by `routes/api.php`. Sanctum is installed but only wired
  for the single `/api/user` stub route — the real v1 API uses JWT, not Sanctum tokens.

### Multi-tenancy via `empresa_id`

Almost every business table is scoped by `empresa_id` (belongs to `App\Models\Empresa`). **`empresa_id === 1`
is treated as a super/master tenant that can see and act across all companies** — this check
(`(int) $user->empresa_id !== 1`) recurs throughout `app/Services/*Service.php` API methods to decide
whether to scope a query to the current user's company or leave it unscoped. When adding new API endpoints
or service methods that query tenant data, replicate this scoping check rather than trusting the caller to
pass the right `empresa_id`.

### Role-based access (web routes)

Web routes use a custom `access.permission:role1|role2|...` middleware
(`app/Http/Middleware/AccessPermission.php`) that checks `$request->user()->cargo` (a plain string column
on `users`) against the pipe-separated role list — **not** the `spatie/laravel-permission` package's
roles/permissions system, even though that package is installed and its tables exist. Known `cargo` values
seen in routes: `master`, `admin`, `client-advanced1`, `client-advanced2`, `client-advanced3`, `client-NFe`,
`client-NFCe`, `client-MDFe`, `client-NFCom`, `client-CTe`. Web routes are further gated by a
`check.subscription` middleware group.

### Fiscal document integration

NFe/NFCe/MDFe/NFCom/CTe issuance goes through the `nfephp-org/sped-*` packages (`sped-nfe`, `sped-mdfe`,
`sped-nfcom`, `sped-cte`, `sped-da` for DANFE PDFs), wrapped by `app/Services/NFeService.php`,
`NFCeService.php`, `MDFeService.php`, `NFComService.php`, `CTeService.php`. Certificate password is stored
on the `Empresa` model (`senhaCertificado`). Treat changes to these services as fiscal-compliance-sensitive
— they build the SEFAZ payloads.

**Certificate storage** (all fiscal services, including NFS-e, resolve certificates the same way via
`app/Services/EmpresaCertificate.php`): the PFX content now lives encrypted in the `empresas.certificado_conteudo`
column (cast `encrypted` on the model) rather than only as a file. `EmpresaCertificate::content()` tries, in
order: the DB column, a legacy file at `storage/app/certificados/{razao}.pfx` (or the path in the legacy
`certificado` column), then a legacy raw-binary value left in the `certificado` column itself — throwing if
none resolve. Use `EmpresaCertificate::withTemporaryFile()` when a library needs a real file path (it
materializes to a 0600 temp file and unlinks it afterward). `php artisan certificados:importar-banco
--empresa=ID` migrates a company's on-disk PFX into the encrypted column (`--delete-files` additionally
removes the legacy file once verified); `php artisan certificado:migrar` is an older one-off command that
just relocated files out of the public disk and encrypted plaintext passwords. Don't reintroduce direct
`file_get_contents(storage_path('app/certificados/...'))` calls in new fiscal code — go through
`EmpresaCertificate`.

NFCom (telecom services electronic invoice) and CTe (Conhecimento de Transporte Eletrônico, road modal) are
the newest document types: `NFComController`/`NFComService` build and transmit a document from an `NFCom`
header + `NFComItem` lines, gated by the `client-NFCom` role; `CTeController`/`CTeService` build a document
from a `CTe` header + `CTeDocumento` lines (the NFe/NFCe documents being transported), gated by the
`client-CTe` role. The `Servico` model (`ServicosController`/`ServicosService`) is a per-company catalog of
billable services used when building NFCom items.

### NFS-e Nacional (`app/Services/NFSeService.php`, `app/Services/NFSe/*`)

The newest fiscal document type, gated by the `client-NFSe` role: `NFSeController`/`NFSeService` build and
transmit a DPS (Declaração de Prestação de Serviços) to the national Sefin Nacional platform, with dedicated
helpers under `app/Services/NFSe/` for XML signing (`NFSeSigner`), the Sefin HTTP client (`NFSeClient`), and
XSD validation (`NFSeSchemaValidator`). Two supporting data directories must ship with any release:

- `resources/domains/nfse/v1.01/` — versioned domain spreadsheets, imported into the domain tables (created
  by migration `2026_08_24_020000_create_nfse_domain_tables.php`) via `php artisan nfse:importar-dominios`
  (idempotent; re-run when the official domain files are updated).
- `resources/schemas/nfse/v1.01/` — official XSD files used by `NFSeSchemaValidator`.

NFS-e migrations must run in this order (`php artisan migrate --force` respects it automatically; don't use
`--path` to run a subset without a specific reason — though production deploys deliberately do run them
one-by-one with `--path`, see `docs/deploy-nfse-nacional.md`):
`2026_08_24_000000_add_client_nfse_permission.php` →
`2026_08_24_010000_add_nfse_fields_to_empresas_table.php` →
`2026_08_24_011000_create_nfses_tables.php` →
`2026_08_24_020000_create_nfse_domain_tables.php` →
`2026_08_24_030000_add_nfse_fields_to_servicos_table.php` →
`2026_08_27_010000_add_certificado_conteudo_to_empresas_table.php` →
`2026_08_27_020000_add_ibscbs_fields_to_nfses_table.php`.
The permission migration only creates the `client-NFSe` cargo value — it still needs to be assigned to
users/roles through the normal permission workflow.

Production also needs env vars `NFSE_LAYOUT_VERSION`, `NFSE_VER_APLIC`, `NFSE_SEFIN_RESTRITA_URL`,
`NFSE_SEFIN_PRODUCAO_URL`, `NFSE_SEFIN_TIMEOUT`, plus a per-company municipal registration, NFS-e/DPS series
and numbering config, and the digital certificate/private key for Sefin — never commit these. On the
frontend, the `Servico` catalog uses Select2 for national service code / NBS code / tax-operation indicator,
the emission form supports municipality lookup by IBGE code and optional CEP lookup via ViaCEP, and IBS
(0.10%) / CBS (0.90%) are calculated both backend and in the form display.

### Other integrations

- **Pix payments**: `EfiPixService.php` (Efí/Gerencianet SDK) + `PixController`/`PixWebhookController`
  (webhook route is unauthenticated at `/api/pix/webhook`, so it self-validates the payload). See
  `docs/integracao-efi-pix.md` for the full request flow (immediate charge → QR code → client-side polling
  → webhook reconciliation) and how the `.p12` mTLS certificate/credentials are configured.
- **CORS**: `config/cors.php` only applies to `api/*` and `sanctum/csrf-cookie` paths; origins come from the
  `CORS_ALLOWED_ORIGINS` env var (comma-separated) — set this when pointing a separate frontend at the API.

### Deploy runbooks in `docs/`

Operational steps that don't belong in this file live under `docs/`: `deploy-nfse-nacional.md` is the
step-by-step NFS-e Nacional production rollout (migration order, cert import, domain import, diagnostics,
controlled emission/cancellation test, rollback plan); `deploy-mdfe-xml-banco.md` covers moving stored MDFe
XML into the database. Consult these before touching NFS-e or MDFe deploy/migration behavior.
`cadastro-autonomo-clientes.md` is the (not yet started) plan for customer self-signup — read it before
touching `/register`, plans, trial or subscription checks. The old public `/register` route (which trusted
`cargo` from the request) was removed; `tests/Feature/RegistroPublicoDesativadoTest.php` keeps it out.

## Coding conventions

- PSR-12 / Laravel conventions, 4-space indentation.
- Controllers stay thin (HTTP concerns only); business logic goes in the matching `app/Services/*Service`
  class.
- API controllers: return JSON, validate via `FormRequest`, shape output via `JsonResource`.
- Naming: StudlyCase classes (`ProdutosController`, `NFCeService`), snake_case database columns.
- Tests: `tests/Feature` for HTTP/workflow tests, `tests/Unit` for isolated logic, named after the behavior
  under test (e.g. `ProdutosControllerTest.php`). Add tests for new API routes, validation rules, permission
  boundaries, and fiscal flows.
- Commits: short Portuguese messages with `feat:`/`fix:` prefixes (e.g. `feat: login via api`,
  `fix: correcao de bug ao criar produto`).

## Security

Never commit `.env`, `.pfx` certificates, tokens, webhook secrets, or fiscal credentials. Payment, Pix, NFe,
NFCe, MDFe, JWT, and CORS configuration all live in environment variables. Review changes touching
authentication, `empresa_id` tenant scoping, fiscal XML generation, or webhook handlers with extra care.
