# Better Las Piñas System Status

## Current implementation

Better Las Piñas has progressed beyond the original foundation phase into a working MVP spanning resident, staff, and administrator workflows.

### Resident experience

- Account registration, sign-in, protected sessions, and profile navigation.
- Guided report creation with validated category, title, description, barangay, address, draggable map pin, optional GPS, and photo evidence.
- Persisted report references, recent-report summaries, searchable report history, detailed report views, full-screen evidence preview, and status timelines.
- Account notifications for report submission and workflow changes.
- Resolution details and resident feedback.

### Public experience

- Branded onboarding and responsive guest home.
- Photographic Common concerns carousel and quick actions.
- Public map limited to resolved locations and non-sensitive fields.
- Announcements, informational tracking screen, and application overview.

### Staff operations

- Role-aware work queues for intake, barangay, department, and administrator accounts.
- Report review, assignment, reassignment, status transitions, notes, and resolution evidence.
- Scoped officer choices based on coverage.
- Report-location map with browser geolocation, green road route, step-by-step directions, and fallback destination line.
- Staff dashboard metrics and status distribution.

### Administrator operations

- Operational dashboard with report totals, status flow, category distribution, calendar activity, and recent reports.
- User creation, role updates, and officer scope management.
- Report category management.
- Persisted audit-log and notification views.
- Responsive single-column mobile navigation and tabbed user-role lists.

### Platform behavior

- Laravel Sanctum authentication and Spatie role/permission enforcement.
- UUID-based relational report data, private attachments, status history, assignments, resolutions, notifications, feedback, and audit logs.
- Mobile-first PWA metadata, safe-area-aware navigation, transitions, modal previews, and content-matched skeleton loaders.
- API error normalization and rate limits for authentication and report creation.

## Report workflow

1. A resident submits a concern with a chosen or GPS-derived location.
2. Intake staff review the submission and assign an authorized officer.
3. The assigned officer progresses the report through operational states and can request information when needed.
4. Resolution records capture notes and optional evidence.
5. The resident receives notifications, sees the timeline, and can provide feedback.
6. Resolved locations can appear on the privacy-limited public map.

## Current operational dependencies

- A configured Laravel-supported database and migrated schema.
- Private writable storage for report and resolution evidence.
- Correct Sanctum, session, CORS, and frontend API-origin configuration.
- Browser geolocation permission for live staff directions and optional resident GPS selection.
- Network access to OpenStreetMap tiles and OSRM for full map and road-routing functionality.

## Remaining production-readiness work

- Verified resident and staff identity lifecycle, password recovery, and account recovery.
- Production hosting, monitoring, backups, retention rules, privacy operations, and incident response.
- Durable object storage, malware scanning, and deployment-specific upload limits.
- Service ownership, staffing policies, escalation targets, and notification delivery beyond in-app database notifications.
- Formal accessibility, security, performance, and cross-device acceptance testing before public launch.

## Verification

The repository includes frontend lint, type-check, build, and Playwright commands plus backend PHPUnit tests and API route inspection. Verification should be run after environment configuration and before each deployment. This document describes implemented source behavior and does not claim a production deployment.
