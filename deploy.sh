#!/bin/bash
set -e
artisan down
git pull origin master
chown -R www-data:www-data .
chmod -R a+w storage
composer install -o
php artisan migrate --force
artisan up
