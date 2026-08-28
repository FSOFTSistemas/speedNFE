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

Newer resources (e.g. `PreVenda`) additionally use standard Laravel authorization: a policy in
`app/Policies` (registered in `AuthServiceProvider::$policies`) checked via `$this->authorize(...)` in both
the web and API controllers. The policy re-implements the same `cargo` allowlist plus the `empresa_id === 1`
super-tenant check, so both controllers for a resource share identical authorization logic even though it's
duplicated (once in the route middleware string, once in the policy) rather than expressed only in routes.
Follow this policy pattern for new resources instead of relying solely on route middleware.

### Fiscal document integration

NFe/NFCe/MDFe/NFCom/CTe issuance goes through the `nfephp-org/sped-*` packages (`sped-nfe`, `sped-mdfe`,
`sped-nfcom`, `sped-cte`, `sped-da` for DANFE PDFs), wrapped by `app/Services/NFeService.php`,
`NFCeService.php`, `MDFeService.php`, `NFComService.php`, `CTeService.php`. Certificates are read
per-company from `storage/app/certificados/{razao}.pfx` (PFX + password stored on the `Empresa` model,
field `senhaCertificado`). Treat changes to these services as fiscal-compliance-sensitive — they build the
SEFAZ payloads.

NFCom (telecom services electronic invoice) and CTe (Conhecimento de Transporte Eletrônico, road modal) are
the newest document types: `NFComController`/`NFComService` build and transmit a document from an `NFCom`
header + `NFComItem` lines, gated by the `client-NFCom` role; `CTeController`/`CTeService` build a document
from a `CTe` header + `CTeDocumento` lines (the NFe/NFCe documents being transported), gated by the
`client-CTe` role. The `Servico` model (`ServicosController`/`ServicosService`) is a per-company catalog of
billable services used when building NFCom items.

### Other integrations

- **Pix payments**: `EfiPixService.php` (Efí/Gerencianet SDK) + `PixController`/`PixWebhookController`
  (webhook route is unauthenticated at `/api/pix/webhook`, so it self-validates the payload).
- **CORS**: `config/cors.php` only applies to `api/*` and `sanctum/csrf-cookie` paths; origins come from the
  `CORS_ALLOWED_ORIGINS` env var (comma-separated) — set this when pointing a separate frontend at the API.

## Coding conventions

- PSR-12 / Laravel conventions, 4-space indentation.
- Controllers stay thin (HTTP concerns only); business logic goes in the matching `app/Services/*Service`
  class.
- API controllers: return JSON, validate via `FormRequest`, shape output via `JsonResource`.
- Naming: StudlyCase classes (`ProdutosController`, `NFCeService`), snake_case database columns.
- Native PHP 8.1 backed enums (`app/Enums`, e.g. `PreVendaStatusEnum`) are the preferred way to represent
  fixed status/type columns on newer models — prefer this over magic strings for new fixed-value fields.
- Tests: `tests/Feature` for HTTP/workflow tests, `tests/Unit` for isolated logic, named after the behavior
  under test (e.g. `ProdutosControllerTest.php`). Add tests for new API routes, validation rules, permission
  boundaries, and fiscal flows.
- Commits: short Portuguese messages with `feat:`/`fix:` prefixes (e.g. `feat: login via api`,
  `fix: correcao de bug ao criar produto`).

## Security

Never commit `.env`, `.pfx` certificates, tokens, webhook secrets, or fiscal credentials. Payment, Pix, NFe,
NFCe, MDFe, JWT, and CORS configuration all live in environment variables. Review changes touching
authentication, `empresa_id` tenant scoping, fiscal XML generation, or webhook handlers with extra care.
