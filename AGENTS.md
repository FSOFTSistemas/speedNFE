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
