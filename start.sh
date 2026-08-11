#!/bin/bash

set -e

php artisan migrate --force

php artisan storage:link

php artisan package:discover --ansi

php artisan config:cache

php artisan route:cache

php artisan view:cache

exec apache2-foreground