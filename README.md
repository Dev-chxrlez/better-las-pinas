# Better Las Piñas

Better Las Piñas is a mobile-first community reporting PWA for Las Piñas residents. Public information remains available without an account; authenticated residents can submit and track persisted reports, while authorized staff review, assign, update, and resolve them.

## Stack

- Frontend: React 19, TypeScript, Vite, Tailwind CSS, React Router, Lucide, Leaflet packages, vite-plugin-pwa, Playwright
- Backend: Laravel 12, PHP 8.2+, PostgreSQL, PHPUnit, Sanctum cookie authentication, and Spatie Permission role-based access
- Planned geospatial support: PostgreSQL PostGIS (not required by the Phase 1 schema)

## Structure

```text
frontend/  React PWA, public pages, resident portal, staff/admin workspaces
backend/   Laravel API, authentication, policies, workflow data, feature tests
docs/      Architecture, design system, and implementation handoffs
```

## Prerequisites

- Node.js 22 LTS or newer and npm
- PHP 8.2+ with `pdo_pgsql`, `mbstring`, `openssl`, `fileinfo`, `xml`, and `curl`
- Composer 2
- PostgreSQL 15+; PostGIS may be enabled now or in Phase 2

This machine has PHP 8.2 and Composer, but PostgreSQL/`psql` and PHP `pdo_pgsql` were not detected during Phase 1. On Windows/XAMPP, enable `extension=pdo_pgsql` and `extension=pgsql` in the active `php.ini`, restart terminals/services, install PostgreSQL, then verify with `php -m` and `psql --version`.

## Local setup

Frontend:

```powershell
cd frontend
npm install
npm run dev
```

Backend:

```powershell
cd backend
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Create a PostgreSQL database named `las_pinas_connect`, set local credentials in `backend/.env`, and copy `frontend/.env.example` to `frontend/.env`, then run:

```powershell
php artisan migrate --seed
php artisan serve
```

The frontend runs at `http://localhost:5173`. The API health endpoint is `http://localhost:8000/api/v1/health`.

Authentication uses Laravel Sanctum's stateful cookie flow. The browser first requests `/sanctum/csrf-cookie`; all API requests include credentials. Public self-registration always creates a `resident`. Staff accounts and role changes are administrator-only. Demo accounts are seeded only in `local` or `testing` when `DEMO_ACCOUNT_PASSWORD` is explicitly set to a strong value.

## Production auth configuration

Serve the frontend and API over HTTPS on the same parent domain (for example `app.example.com` and `api.example.com`). Set `VITE_API_BASE_URL`, `APP_URL`, `CORS_ALLOWED_ORIGINS`, and `SANCTUM_STATEFUL_DOMAINS` to the exact production hosts. Set `SESSION_DOMAIN=.example.com`, `SESSION_SECURE_COOKIE=true`, and keep `SESSION_SAME_SITE=lax`. Never include localhost, preview deployments, or wildcard origins in production. Run migrations before serving traffic and do not configure demo accounts in production.

Report photos use Laravel's private `local` disk and are not exposed by a public storage symlink. API policies enforce resident ownership, staff assignment scope, and administrator access. Status history, resident-visible updates, resolutions, notifications, feedback, and security-relevant actions are persisted.

Because this demonstration intentionally has no email, SMS, or identity verification, account ownership is not independently verified. Public registration and identity assurance must be reassessed before the system processes real citizen complaints.

## Verification commands

```powershell
cd frontend
npm run lint
npm run build
npx playwright test

cd ..\backend
php artisan test
php artisan route:list --path=api/v1
```

See [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md), [docs/DESIGN-SYSTEM.md](docs/DESIGN-SYSTEM.md), and [docs/PHASE-1-REPORT.md](docs/PHASE-1-REPORT.md).
