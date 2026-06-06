# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# First-time setup (installs deps, creates .env, generates key, migrates, builds assets)
composer setup

# Start full dev environment (artisan serve + queue:listen + pail logs + npm dev, all concurrent)
composer dev

# Run all tests
composer test

# Run a single test class or method
php artisan test --filter TestName

# Build frontend assets for production
npm run build
```

## Architecture

Fresh **Laravel 12** scaffold with SQLite as the default database. Sessions, queues, and cache all use the `database` driver (backed by SQLite in development).

- **Models**: `app/Models/User.php` — the only model; standard authenticatable with `HasFactory` and `Notifiable`
- **Controllers**: `app/Http/Controllers/Controller.php` — base class only; no concrete controllers yet
- **Routes**: `routes/web.php` has a single `GET /` → `welcome` view; no `api.php` yet
- **Service providers**: `app/Providers/AppServiceProvider.php` — empty `register`/`boot`
- **Database**: migrations for users, password_reset_tokens, and sessions tables; `DatabaseSeeder` seeds one user (`test@example.com`)
- **Frontend**: Vite 7 + Tailwind CSS 4 + Axios; entry points are `resources/css/app.css` and `resources/js/app.js`

## Testing

PHPUnit 11 with two suites:

| Suite | Directory |
|-------|-----------|
| Unit | `tests/Unit/` |
| Feature | `tests/Feature/` |

Tests run against an **in-memory SQLite** database (`phpunit.xml` sets `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:`). Queue is `sync`, cache/session use `array` driver — no external services required.
