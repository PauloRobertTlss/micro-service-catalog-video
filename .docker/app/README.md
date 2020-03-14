app:
        build: .
        container_name: micro-videos-app
        entrypoint: dockerize -template ./.docker/app/.env:.env -template ./.docker/app/.env.testing:.env.testing -wait tcp://db:3306 -timeout 40s ./.docker/entrypoint.sh
        environment:
            - _DB_HOST=db
            - _DB_DATABASE=code_micro_videos
            - _DB_USERNAME=root
            - _DB_PASSWORD=@ge380134
            - _REDIS_HOST=micro-videos-redis
            - _DB_DATABASE_TEST=code_micro_videos_test
            
responsabilidade do template voi para o copiar.