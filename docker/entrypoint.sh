#!/usr/bin/env sh
set -e

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
mkdir -p database
chown -R www-data:www-data storage bootstrap/cache database

if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

if [ "${DB_CONNECTION}" = "sqlite" ]; then
  DB_FILE="${DB_DATABASE:-database/database.sqlite}"

  case "$DB_FILE" in
    /*) SQLITE_PATH="$DB_FILE" ;;
    *) SQLITE_PATH="/var/www/html/$DB_FILE" ;;
  esac

  mkdir -p "$(dirname "$SQLITE_PATH")"
  touch "$SQLITE_PATH"
  chown -R www-data:www-data "$(dirname "$SQLITE_PATH")"
fi

if [ -z "${APP_KEY}" ] && grep -q "^APP_KEY=$" .env 2>/dev/null; then
  php artisan key:generate --force --no-interaction || true
fi

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec "$@"
