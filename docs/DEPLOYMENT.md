# Free MVP deployment

Better Las Piñas is prepared for a single-origin deployment on Render. The Docker build compiles the React PWA, copies it into Laravel's public directory, and runs the SPA and API from one Render hostname. Supabase provides persistent PostgreSQL and private S3-compatible file storage.

## 1. Create the Supabase project

1. Create a free Supabase project.
2. In **Connect**, copy a PostgreSQL connection string suitable for a persistent server. Append `?sslmode=require` when it is not already present. This becomes Render's `DB_URL`.
3. In **Storage**, create a private bucket such as `report-evidence`.
4. Open **Storage > S3 connection** and create S3 access keys. Keep the access key, secret, endpoint, region, and bucket name available for Render.

Do not commit these values to Git.

## 2. Create the Render service

1. In Render, choose **New > Blueprint** and connect the GitHub repository.
2. Render reads `render.yaml` and creates the free `better-las-pinas` Docker web service.
3. Enter the secret environment values requested by the Blueprint:

| Render variable | Value |
| --- | --- |
| `APP_KEY` | Output of `php artisan key:generate --show` from the local `backend` directory |
| `DB_URL` | Supabase PostgreSQL connection string |
| `INITIAL_ADMIN_EMAIL` | Email for the first system administrator |
| `INITIAL_ADMIN_PASSWORD` | Strong password with at least 12 characters |
| `AWS_ACCESS_KEY_ID` | Supabase Storage S3 access key |
| `AWS_SECRET_ACCESS_KEY` | Supabase Storage S3 secret key |
| `AWS_DEFAULT_REGION` | Region shown in Supabase's S3 configuration |
| `AWS_BUCKET` | Private bucket name, for example `report-evidence` |
| `AWS_ENDPOINT` | Supabase S3 endpoint ending in `/storage/v1/s3` |

Path-style S3 addressing is enabled for Supabase. Use the exact endpoint and region shown in the project's S3 configuration.

## 3. First deployment

The container start command automatically:

1. runs outstanding database migrations;
2. seeds barangays, departments, categories, roles, and permissions idempotently;
3. creates the initial administrator only when that email does not exist;
4. caches Laravel production configuration; and
5. starts the web service on Render's assigned port.

After Render reports the service as live, open its `onrender.com` URL and sign in with the initial administrator credentials. Resident registration remains available from the public authentication screen.

## Free-tier expectations

- Render's free service sleeps after inactivity, so the first request can be slow.
- Supabase free projects may pause after extended inactivity.
- Render's local filesystem is not persistent. Report and resolution images therefore use the configured Supabase Storage bucket.
- Keep the Supabase project and Render service in geographically close regions when the available free choices permit it.

## Updating

Pushing to the connected GitHub branch triggers a new Render deployment. Database migrations run at startup and seeders update reference data without deleting resident reports.
