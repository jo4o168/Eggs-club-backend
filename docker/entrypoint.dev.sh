#!/bin/sh
set -e

cd /var/www/html

if [ ! -d vendor ]; then
    composer install --no-interaction --prefer-dist
fi

echo "Aguardando MySQL..."
until php -r "
    \$dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s',
        getenv('DB_HOST') ?: 'mysql',
        getenv('DB_PORT') ?: '3306',
        getenv('DB_DATABASE') ?: 'laravel'
    );
    new PDO(\$dsn, getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '');
" 2>/dev/null; do
    sleep 2
done

if [ -z "$APP_KEY" ]; then
    echo "ERRO: APP_KEY não definida no .env da raiz (eggs-club/.env)."
    exit 1
fi

php artisan migrate --force --no-interaction
php artisan storage:link --force 2>/dev/null || true

if [ "${SEED_DATABASE:-false}" = "true" ]; then
    php artisan db:seed --force --no-interaction
fi

if [ "$#" -gt 0 ]; then
    exec "$@"
fi

exec php artisan serve --host=0.0.0.0 --port=8000
