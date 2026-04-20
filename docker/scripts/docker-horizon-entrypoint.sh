#!/bin/sh
set -e

echo "[horizon] Caching Laravel framework..."
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan view:cache

echo "[horizon] Starting Horizon..."
exec php artisan horizon
