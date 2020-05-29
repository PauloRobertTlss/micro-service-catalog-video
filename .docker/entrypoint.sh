#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh

#FRONT-END
#configuração para node não ir na internet quando build container
npm config set cache /var/www/.npm-cache --global

#quando build install dinamico
cd /var/www/frontend && npm install && cd ..


#BACK-END
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
