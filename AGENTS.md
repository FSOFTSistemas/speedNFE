# Repository Guidelines

## Project Structure & Module Organization

This is a Laravel 9 application with web controllers and a versioned JSON API. Core PHP code lives in `app/`, with controllers in `app/Http/Controllers`, API requests in `app/Http/Requests/Api/V1`, API resources in `app/Http/Resources`, services in `app/Services`, models in `app/Models`, and helpers in `app/Utils`. Routes are split across `routes/web.php`, `routes/api.php`, and `routes/auth.php`. Blade and front-end files are in `resources/`; Swagger/OpenAPI docs live in `public/docs`. Tests are in `tests/Feature` and `tests/Unit`.

## Build, Test, and Development Commands

- `composer install`: install PHP dependencies.
- `npm install`: install front-end tools.
- `php artisan serve`: run the Laravel development server.
- `npm run dev`: start Vite for local asset development.
- `npm run build`: build production front-end assets.
- `php artisan migrate`: apply database migrations.
- `php artisan test` or `vendor/bin/phpunit`: run the PHPUnit test suite.
- `vendor/bin/pint`: format PHP code with Laravel Pint.
- `php artisan route:list --path=api/v1`: inspect API routes.

For a fresh setup, copy `.env.example` to `.env`, then run `php artisan key:generate`. API docs are available at `/api/documentation` and `/docs/openapi.yaml`.

## Coding Style & Naming Conventions

Follow PSR-12/Laravel conventions and use 4-space indentation in PHP. Keep controllers focused on HTTP concerns and place reusable business logic in `app/Services`. API controllers must return JSON, use `FormRequest` validation where practical, and expose stable payloads through `JsonResource`. Use StudlyCase class names such as `ProdutosController` and `NFCeService`; use snake_case for database columns.

## Testing Guidelines

Use PHPUnit. Put HTTP/workflow tests in `tests/Feature`, isolated logic tests in `tests/Unit`, and name files after the behavior under test, for example `ProdutosControllerTest.php`. Run `php artisan test` before opening a pull request, and add tests for new API routes, validation rules, permission boundaries, and fiscal flows.

## Commit & Pull Request Guidelines

Recent history uses short Portuguese messages with `feat:` and `fix:` prefixes, for example `feat: login via api` and `fix: correcao de bug ao criar produto`. Prefer that style with a concise summary. Pull requests should describe the change, list validation steps, link related issues or tasks, and include screenshots for UI changes in Blade, Livewire, or React views. Note migration, environment, or payment/fiscal integration impacts explicitly.

## Security & Configuration Tips

Do not commit `.env`, certificates, tokens, webhook secrets, or fiscal credentials. Keep payment, Pix, NFe, NFCe, MDFe, JWT, and CORS configuration in environment variables; set `CORS_ALLOWED_ORIGINS` for separated frontends. Review authentication, authorization, fiscal XML, and webhook changes carefully.

## NFS-e Nacional

The `nfse` branch contains the initial NFS-e Nacional implementation, including the contributor emission flow, domain catalogs, DPS XML generation, XML signature/validation services, Sefin Nacional client, permissions, company settings, service catalog fields, and NFS-e listing/detail screens.

### NFS-e migrations

The NFS-e database changes are applied in this order:

- `database/migrations/2026_08_24_000000_add_client_nfse_permission.php`
- `database/migrations/2026_08_24_010000_add_nfse_fields_to_empresas_table.php`
- `database/migrations/2026_08_24_011000_create_nfses_tables.php`
- `database/migrations/2026_08_24_020000_create_nfse_domain_tables.php`
- `database/migrations/2026_08_24_030000_add_nfse_fields_to_servicos_table.php`

In a production deployment, after the application files and dependencies are updated, run the normal pending migrations command:

```bash
php artisan migrate --force
```

Do not use `--path` in production unless there is a deliberate reason to run only a subset. Confirm the result with `php artisan migrate:status`. The permission migration creates `client-NFSe`, but access still has to be assigned to the appropriate users/roles according to the application's permission workflow.

### NFS-e domain data and schemas

The domain tables created by `2026_08_24_020000_create_nfse_domain_tables.php` must be populated after migration with:

```bash
php artisan nfse:importar-dominios
```

The importer reads the versioned files under `resources/domains/nfse/v1.01/`. The XML validator reads the official XSD files under `resources/schemas/nfse/v1.01/`. Both directories must be included in the release artifact. The importer is idempotent and can be executed again when the official domain files are updated.

### NFS-e production configuration

Configure the NFS-e variables in the production environment without committing them to `.env`:

- `NFSE_LAYOUT_VERSION`
- `NFSE_VER_APLIC`
- `NFSE_SEFIN_RESTRITA_URL`
- `NFSE_SEFIN_PRODUCAO_URL`
- `NFSE_SEFIN_TIMEOUT`

Each issuing company also needs a valid municipal registration, NFS-e/DPS series and numbering configuration, the correct environment, and the digital certificate/private key required by the Sefin integration. Keep certificates and credentials outside version control.

### NFS-e frontend conventions

The service catalog uses Select2 for the national service code, NBS code, and tax-operation indicator. The emission form supports municipality selection by IBGE code, optional CEP lookup through ViaCEP, and calculates IBS at `0.10%` and CBS at `0.90%` in the backend as well as in the form display. Service creation/editing is organized into tabs for basic data, NFS-e Nacional, and NFCom taxes.
