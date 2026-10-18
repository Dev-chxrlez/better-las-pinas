FROM node:22-alpine AS frontend-builder

WORKDIR /app/frontend
COPY frontend/package.json frontend/package-lock.json ./
RUN npm ci
COPY frontend/ ./
RUN npm run build

FROM composer:2 AS php-dependencies

WORKDIR /app/backend
COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --no-scripts

FROM php:8.3-cli-bookworm AS application

RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev libpq-dev \
    && docker-php-ext-install bcmath mbstring pcntl pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY backend/ ./
COPY --from=php-dependencies /app/backend/vendor ./vendor
COPY --from=frontend-builder /app/frontend/dist/ ./public/
COPY docker/start-render.sh /usr/local/bin/start-render

RUN chmod +x /usr/local/bin/start-render \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && php artisan package:discover --ansi

ENV APP_ENV=production
ENV APP_DEBUG=false

EXPOSE 10000

CMD ["start-render"]
