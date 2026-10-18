# Architecture

## System boundary

Phase 1 has two independently runnable applications. `frontend/` is a React single-page PWA. `backend/` is a Laravel JSON API. No Phase 1 UI sends report data to the API. This boundary prevents a prototype action from being mistaken for a live submission.

## Frontend

`src/routes` owns route composition; `components/layout` contains the reusable public shell; `components/ui` and `components/shared` contain primitives; `features/reports` owns the report draft flow; `pages` owns route-level views; `lib` and `types` hold shared data and contracts. The report draft exists only in React memory and is discarded on reload.

Public routes: `/`, `/report`, `/track`, `/map`, `/announcements`, `/about`, and the fallback 404. `/staff/login` is a disabled authentication preview. `/staff/dashboard` is a hard gate with no dashboard implementation or bypass.

The service worker precaches only the application shell and static assets. There is no runtime API caching and no offline report queue.

## Backend

The API is versioned under `/api/v1`. Responses use `{ success, message, data }`; validation/error responses use `{ success, message, errors }`. Laravel handles security middleware and non-debug production errors. CORS defaults to the two local Vite origins and must be set explicitly in production.

Reference tables use UUID primary keys, unique names/slugs, active flags, timestamps, and indexes. Department data is intentionally unseeded because no official routing assignments were verified.

## Phase 2 report schema plan

Plan separate tables for reports, guest-access grants, report media, assignments, status history, and notifications. A report should contain a UUID, human-safe display reference, reporter name, optional normalized email, category and barangay foreign keys, title/description, PostGIS point, privacy/publication flags, and timestamps. Tracking access must use a high-entropy secret stored as a one-way hash; the display reference must never authenticate access. Media should use private object storage, validated MIME/size limits, malware scanning, and signed access URLs. Status transitions and staff actions should write append-only audit records.

## Security posture

No public admin user, password capture, login bypass, tracking token, or report endpoint exists. Secrets belong only in `backend/.env`. Future staff authorization should combine Sanctum sessions with Spatie roles/permissions and policy checks. Production CORS must list exact trusted origins.
