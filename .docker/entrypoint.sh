#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
cd backend

if [! -f ".env"]; then
cp .env.example .env
fi

if [! -f ".env.testing"]; then
cp .env.testing.example .env.testing

fi

composer install
php artisan cache:clear
php artisan key:generate

php-fpm
