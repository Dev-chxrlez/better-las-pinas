# Better Las Piñas

Better Las Piñas is a mobile-first community reporting platform for Las Piñas residents and authorized operations teams. Residents can create accounts, submit geolocated concerns with evidence, track progress, receive notifications, and review resolutions. Staff and administrators can triage, assign, route, update, resolve, and analyze persisted reports from responsive dashboards.

## Current capabilities

- Resident registration, sign-in, protected sessions, and role-based routing.
- Guided report submission with category, description, draggable map pin, GPS support, and optional photo evidence.
- Resident dashboard, report history, detailed progress timeline, notifications, image preview, and resolution feedback.
- Public community map showing privacy-limited resolved-report locations.
- Staff work queues scoped by role, assignment, and officer coverage.
- In-app directions from the assigned officer's live position to the reported location.
- Administrator dashboard with analytics, calendars, categories, users and roles, notifications, and audit logs.
- Mobile-first responsive layouts, content-matched skeleton loaders, transitions, and installable PWA metadata.

## Technology

- Frontend: React 19, TypeScript, Vite, React Router, Leaflet, Lucide, Tailwind CSS tooling, vite-plugin-pwa, and Playwright.
- Backend: Laravel 12, PHP 8.2+, Sanctum session authentication, Spatie Permission, PHPUnit, and a relational database supported by Laravel.
- Maps: OpenStreetMap tiles through Leaflet and OSRM road routing for in-app directions.

## Repository structure

```text
frontend/  React PWA for public, resident, staff, and administrator experiences
backend/   Laravel API, authentication, policies, workflows, storage, and tests
docs/      Architecture, design system, and current system status
```

## Local setup

Requirements: Node.js 22+, npm, PHP 8.2+, Composer 2, and the PHP extensions required by Laravel. PostgreSQL is recommended for deployment; SQLite can be used for local development and automated tests.

```powershell
cd frontend
npm install
Copy-Item .env.example .env
npm run dev
```

In a second terminal:

```powershell
cd backend
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

The frontend defaults to `http://localhost:5173`; the API defaults to `http://localhost:8000` with health information at `/api/v1/health`.

## Authentication and data protection

Authentication uses Laravel Sanctum's stateful cookie flow. Public registration creates resident accounts only; staff creation and role changes are administrator-controlled. Policies and permissions restrict resident, staff, officer, and administrator access. Uploaded report and resolution files use private storage and policy-protected download endpoints. Status history, assignments, notifications, feedback, and security-relevant actions are persisted.

Before a production launch, configure HTTPS, trusted CORS origins, Sanctum stateful domains, secure session cookies, durable database and object storage, identity verification, backups, monitoring, and an operational privacy/retention policy.

## Verification commands

```powershell
cd frontend
npm run lint
npm run typecheck
npm run build
npm run test:e2e

cd ..\backend
php artisan test
php artisan route:list --path=api/v1
```

See [Architecture](docs/ARCHITECTURE.md), [Design System](docs/DESIGN-SYSTEM.md), and [System Status](docs/PHASE-1-REPORT.md).

## Free MVP hosting

The repository includes a production Docker build and Render Blueprint that serve the compiled React PWA and Laravel API from one origin. Supabase PostgreSQL and private S3-compatible Storage provide durable data and report evidence. Follow the [deployment guide](docs/DEPLOYMENT.md) to configure the required secrets and launch the free MVP.
