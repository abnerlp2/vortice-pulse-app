# --- assets (Vite) ---
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST
ARG VITE_REVERB_PORT=443
ARG VITE_REVERB_SCHEME=https
RUN npm run build

# --- app: nginx + php-fpm + reverb + queue worker (todo bajo s6) ---
FROM serversideup/php:8.4-fpm-nginx
ENV AUTORUN_ENABLED=true \
    AUTORUN_LARAVEL_MIGRATION=true \
    AUTORUN_LARAVEL_STORAGE_LINK=false \
    PHP_OPCACHE_ENABLE=1

# gd lo exige phpoffice/phpspreadsheet (maatwebsite/excel).
USER root
RUN install-php-extensions gd
USER www-data

WORKDIR /var/www/html
COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

COPY --chown=www-data:www-data . .
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build
# .dockerignore excluye estos directorios (solo traen basura de caché), pero
# Laravel los necesita presentes y escribibles.
RUN mkdir -p bootstrap/cache storage/framework/cache/data storage/framework/sessions \
             storage/framework/views storage/logs \
    && composer dump-autoload --optimize

USER root
COPY docker/s6-rc.d/ /etc/s6-overlay/s6-rc.d/
RUN chmod +x /etc/s6-overlay/s6-rc.d/reverb/run /etc/s6-overlay/s6-rc.d/worker/run \
    && touch /etc/s6-overlay/s6-rc.d/user/contents.d/reverb \
             /etc/s6-overlay/s6-rc.d/user/contents.d/worker
USER www-data

# 8080 = HTTP (nginx), 8081 = WebSocket (reverb)
EXPOSE 8080 8081
