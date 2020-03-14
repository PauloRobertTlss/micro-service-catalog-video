#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
cp .env.example .env
cp .env.testing.example .env.testing

composer install
php artisan cache:clear
php artisan key:generate

chmod -R 0777 storage
php artisan migrate

php-fpm
