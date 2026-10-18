# Better Las Piñas API

Laravel 12 API for the Better Las Piñas community reporting platform.

## Responsibilities

- Sanctum session authentication and resident registration.
- Role and permission enforcement for residents, intake officers, barangay officers, department officers, and system administrators.
- Reference data for barangays, report categories, departments, and officer coverage.
- Persisted report submission, private evidence, status history, assignments, resolutions, notifications, feedback, and audit logs.
- Resident dashboard and report history.
- Staff work queues, review, assignment, and workflow transitions.
- Administrator user management, categories, analytics, and audit-log endpoints.
- Privacy-limited public resolved-report map data.

## Setup

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Configure the database, frontend origin, Sanctum stateful domains, session cookie, and private filesystem settings in `.env`. Never commit `.env`, uploaded evidence, or a populated local database.

## Useful commands

```powershell
php artisan test
php artisan route:list --path=api/v1
php artisan migrate:fresh --seed
```

The default API base path is `/api/v1`; the health endpoint is `/api/v1/health`.
