composer require --dev barryvdh/laravel-ide-helper


php artisan ide-helper:generate
php artisan ide-helper:meta
php artisan ide-helper:models >>> [no] para não gerar na model a document

adicionar os arquivos no gitignore

gatilho do composer. para sempre atualizar quando adicionar novas vendors

post-autoload-dump: {
    php artisan ide-helper:generate
}

github: finishproject https://github.com/joaopaulolndev/code-micro-videos/tree/master/frontend/src/components
