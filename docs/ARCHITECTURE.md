# Better Las Piñas Architecture

## System overview

Better Las Piñas consists of a React PWA in `frontend/` and a Laravel JSON API in `backend/`. The browser communicates with the API through versioned `/api/v1` endpoints and Laravel Sanctum stateful sessions. Responses follow the shared `{ success, message, data }` envelope; validation failures add an `errors` object.

## Frontend

`src/routes` composes public and protected routes. `components/layout` contains the shared resident/public shell, `components/ui` contains reusable controls and loading states, `features/reports` owns the report wizard, and `features/portal` owns resident, staff, and administrator experiences.

Public surfaces include onboarding, home, community map, announcements, tracking information, and about content. Authenticated residents receive dashboard, report, report-history, notification, detail, timeline, and profile routes. Staff use a separate responsive workspace whose navigation and available modules reflect their role.

Leaflet renders report-selection, public resolved-report, and staff navigation maps. Residents can place or drag a pin independently of GPS. Staff routing uses the browser location API and OSRM to render an in-app road route; a direct destination line remains available when routing fails.

## Backend

The API persists users, roles, permissions, barangays, categories, departments, reports, evidence attachments, staff coverage, assignments, status history, resident-visible updates, resolutions, notifications, feedback, and audit logs.

Role scopes:

- `resident`: submit and view owned reports.
- `intake-officer`: review incoming reports and assign authorized officers.
- `barangay-officer`: manage assigned reports within barangay scope.
- `city-department-officer`: manage assigned reports within department scope.
- `system-administrator`: full operational, access-management, category, analytics, and audit visibility.

Report states are `submitted`, `under_review`, `assigned`, `in_progress`, `needs_information`, `resolved`, `closed`, `rejected`, and `duplicate`. Server-side workflow rules validate allowed transitions and write history and audit records atomically.

## Data and file access

Reports use UUID identifiers plus resident-facing LPC references. Coordinates are stored as latitude and longitude values. Report attachments and resolution evidence remain on private storage and are returned only through authorized controller actions. Public map output exposes a limited resolved-report view rather than private report content.

## Security model

Registration and login are rate-limited. Public registration cannot create staff roles. Route middleware, permissions, policies, assignment scope, and administrator gates provide layered authorization. Production deployments must use exact trusted origins, HTTPS-only cookies, a protected application key, durable private file storage, backups, and operational identity verification.

## External services and resilience

OpenStreetMap provides map tiles and OSRM provides road routing. The core reporting workflow does not depend on routing availability: staff still receive the reported coordinates and a direct map line if an OSRM route cannot be loaded. The PWA caches application assets, not authenticated API data or pending reports.
