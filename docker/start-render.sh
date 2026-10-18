#!/bin/sh
set -eu

export APP_URL="${RENDER_EXTERNAL_URL:-${APP_URL:-http://localhost:10000}}"

php artisan migrate --force
php artisan db:seed --force
php artisan optimize:clear
php artisan config:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
