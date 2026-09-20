# Dooeed

Dooeed is a personal finance application for understanding your financial position, planning a budget, and working toward savings goals. It brings everyday money management and longer-term financial planning into one workspace.

The project is under active development and is shared publicly for collaboration. The interface currently uses a mixture of Indonesian and English; this guide is written in English for contributors.

## Project status

The current application includes:

- Registration, login, logout, and authenticated profile endpoints using Laravel Sanctum bearer tokens.
- Password reset requests and password updates.
- Blade authentication pages and a dashboard entry point styled with Tabler.
- Database migrations for accounts, categories, contacts, transactions, transfers, debts, investments, physical assets, budgets, and savings goals.

The planned financial workspace covers:

1. Dashboard
2. Initial Wealth Profile
3. Savings Capacity Profile
4. Financial Health Check
5. Wealth Level
6. Budgeting Recommendations
7. Profit and Loss Report
8. Budget Management
9. Account Transactions
10. Dream Tracker
11. Cash Transfers / Savings
12. Income
13. Expenses
14. Debts
15. Receivables
16. Buying and Selling Assets
17. Investments

Financial migrations and navigation should not be interpreted as completed financial features. At present, the application API exposes authentication endpoints only. Financial calculations, data-entry workflows, and reports are still being developed.

## Technology stack

| Area | Technology |
| --- | --- |
| Backend | PHP, Laravel 13 |
| API authentication | Laravel Sanctum 4 |
| Views and styling | Blade, Tabler Core 1.5 |
| Frontend | JavaScript, Axios, Vite 8 |
| Default database configuration | PostgreSQL |
| Automated tests | Pest 4, PHPUnit 12, SQLite in memory |
| PHP formatting | Laravel Pint |

Tabler is the project's CSS framework. Do not introduce Tailwind CSS or a second component framework.

## Requirements

- PHP 8.4 for the project's development environment. The Composer manifest allows PHP `^8.3`; installed dependencies must also satisfy their platform requirements.
- Composer 2.
- Node.js 22.12 or newer on a supported release line. Vite also supports Node.js 20.19 or newer within the 20.x line.
- npm, Git, and PostgreSQL.
- PHP extensions required by Composer, plus `pdo_pgsql` for PostgreSQL and `pdo_sqlite` for the test suite.
- A local PHP web environment, such as Laravel Herd.

Use the committed `composer.lock` and `package-lock.json` files for reproducible dependency installation.

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/oimtrust/dooeed.git
cd dooeed
```

For Herd users, clone into a directory managed by Herd or link the project using Herd. Herd serves the application without an additional PHP development server.

### 2. Install dependencies

```bash
composer install
npm ci
```

### 3. Configure the environment

If `.env` was not created during installation, copy the example:

```bash
cp .env.example .env
```

Edit `.env` for your local environment:

```dotenv
APP_NAME=Dooeed
APP_ENV=local
APP_DEBUG=true
APP_URL=<your-local-application-url>

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dooeed
DB_USERNAME=<your-database-user>
DB_PASSWORD=<your-database-password>

MAIL_MAILER=log
```

Replace all angle-bracket placeholders. Set `APP_URL` to the address shown by your local web environment, including the correct scheme and port. Password reset links depend on this setting.

Create an empty PostgreSQL database named `dooeed` and ensure the configured user can create tables and indexes. Database sessions, cache, and queues are enabled by default in `.env.example`.

Generate the application key for this new installation:

```bash
php artisan key:generate --no-interaction
composer check-platform-reqs
```

Keep `.env` local. Never commit credentials, application keys, or bearer tokens.

### 4. Create the database tables

```bash
php artisan migrate --no-interaction
```

No seed data is required. Create your own account through `/register` after starting the application.

For an existing installation, back up your database before applying schema changes. Editing a migration that has already run does not update the existing table. Do not use `migrate:fresh` to resolve a mismatch on a database containing data you want to keep.

### 5. Build the frontend

```bash
npm run build
```

Open `/register` or `/login` on your local application host. The document root must be the project's `public/` directory; `storage/` and `bootstrap/cache/` must be writable by PHP.

## Local development

When using Herd, leave PHP serving to Herd and run Vite for frontend hot reload:

```bash
npm run dev
```

For development without Herd, the repository includes a combined process script:

```bash
composer run dev
```

This starts a PHP development server, queue listener, log viewer, and Vite. Use the address printed by the server and update `APP_URL` accordingly. Do not run this combined script alongside Herd unless you intentionally need a separate server.

If you need a queue worker while using Herd, run it in a separate terminal:

```bash
php artisan queue:work --tries=1
```

With `MAIL_MAILER=log`, outgoing email content is written to the application logs rather than delivered to an inbox. Password reset links can be found in `storage/logs/laravel.log` with the default logging configuration. Configure a mail service when testing actual delivery.

## Authentication API

All endpoints below are relative to your application host. Send `Accept: application/json`, and use `Content-Type: application/json` when sending a JSON body.

| Method | Path | Request fields | Bearer token |
| --- | --- | --- | --- |
| POST | `/api/v1/auth/register` | `name`, `email`, `password`, `password_confirmation` | No |
| POST | `/api/v1/auth/login` | `email`, `password` | No |
| GET | `/api/v1/auth/me` | None | Required |
| POST | `/api/v1/auth/logout` | None | Required |
| POST | `/api/v1/auth/forgot-password` | `email` | No |
| POST | `/api/v1/auth/reset-password` | `token`, `email`, `password`, `password_confirmation` | No |

Registration requires a unique lowercase email address and a password of at least eight characters. Password confirmation must match. Login and registration return the bearer token in `data.token` and user information in `data.user`.

For protected requests, send the complete token, including its ID prefix and `|` separator:

```text
Authorization: Bearer <data.token>
```

The reset-password `token` comes from the password reset link; it is not a login bearer token. Login and registration are rate-limited to 10 requests per minute; password reset endpoints are limited to 5 requests per minute.

## Project structure

```text
app/Domains/Auth/Actions/       Authentication use cases
app/Http/Controllers/Api/      API controllers
app/Http/Requests/             Request validation
app/Http/Resources/            API response resources
app/Models/                    Eloquent models
database/migrations/          Database schema history
resources/views/               Blade pages and layouts
resources/css/                 Tabler imports and application styles
resources/js/domains/auth/     Authentication API client and page behavior
resources/js/lib/              Shared HTTP utilities
routes/api.php                 Versioned API routes
routes/web.php                 Web page routes
tests/                         Pest feature and unit tests
```

## Testing and code style

```bash
php artisan test --compact
vendor/bin/pint --dirty --format agent
npm run build
```

The test configuration uses an in-memory SQLite database, array-backed sessions and cache, and synchronous queues. It does not validate the schema of your existing local PostgreSQL database.

For authentication changes, test a real login-issued bearer token against `/api/v1/auth/me`. Tests using only `Sanctum::actingAs()` bypass token lookup and cannot detect every token-related regression.

## Troubleshooting

### Login succeeds, but `/api/v1/auth/me` returns 401

Check that the request sends the current, complete bearer token and uses the same application host as login. A logged-out or revoked token cannot authenticate a request.

Also check that the database schema, model key type, and Sanctum token owner column agree. A UUID stored in `users.id` must be treated as a string by Eloquent, with UUID-compatible foreign keys and token owner references. An integer model key can truncate a UUID when issuing a token and cause subsequent requests to fail. This project has encountered that mismatch on existing local databases; passing tests on a freshly created database does not rule it out.

### Frontend changes do not appear

Run `npm run dev` during development or rebuild with `npm run build`. If Vite reports a missing manifest entry, verify that the asset is registered in `vite.config.js` and rebuild.

### Environment changes do not take effect

```bash
php artisan config:clear --no-interaction
```

### Database connection or migration errors

Confirm that PostgreSQL is running, the database exists, and the `.env` credentials are correct. Verify that the PHP runtime has `pdo_pgsql` enabled. Keep the database engine used for a change in mind: passing SQLite tests does not guarantee PostgreSQL-specific behavior.

## Contributing

1. Create a branch for a focused change.
2. Read [AGENTS.md](AGENTS.md) and follow the conventions in neighboring files.
3. Keep validation in Form Requests and follow the existing domain-action structure for authentication logic.
4. Reuse Tabler components and keep page behavior in the appropriate JavaScript domain directory.
5. Add meaningful tests for behavioral changes, format modified PHP, and build the frontend when applicable.
6. Submit a pull request describing the problem, solution, validation performed, and any database or setup changes.

Do not include real financial records, passwords, or access tokens in issues, test fixtures, or pull requests. Report security issues privately to the repository maintainer rather than posting exploit details publicly.

## License

`composer.json` currently declares the MIT license. A standalone project license file has not yet been added. Third-party dependencies retain their respective licenses.
