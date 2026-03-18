#!/usr/bin/env sh
set -eu

cd /var/www/html
umask 0002

wait_for_service() {
    host="$1"
    port="$2"
    name="$3"

    if [ -z "$host" ] || [ -z "$port" ]; then
        return 0
    fi

    attempts=0

    until nc -z "$host" "$port" >/dev/null 2>&1; do
        attempts=$((attempts + 1))

        if [ "$attempts" -ge 30 ]; then
            echo "Timed out waiting for ${name} at ${host}:${port}" >&2
            exit 1
        fi

        echo "Waiting for ${name} at ${host}:${port}..."
        sleep 2
    done
}

normalize_laravel_permissions() {
    mkdir -p \
        bootstrap/cache \
        storage/app/public \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/testing \
        storage/framework/views \
        storage/logs

    touch storage/logs/laravel.log

    chown -R www-data:www-data storage bootstrap/cache >/dev/null 2>&1 || true

    find storage bootstrap/cache -type d -exec chmod 0775 {} \; >/dev/null 2>&1 || true
    find storage bootstrap/cache -type f -exec chmod 0664 {} \; >/dev/null 2>&1 || true
}

normalize_laravel_permissions

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
    echo "Installing Composer dependencies..."
    composer install --prefer-dist --no-interaction
fi

normalize_laravel_permissions

wait_for_service "${DB_HOST:-}" "${DB_PORT:-3306}" "database"
wait_for_service "${REDIS_HOST:-}" "${REDIS_PORT:-6379}" "redis"

if [ ! -L public/storage ] && [ ! -e public/storage ]; then
    php artisan storage:link >/dev/null 2>&1 || true
fi

normalize_laravel_permissions

exec "$@"
