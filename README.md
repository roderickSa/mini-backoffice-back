## docker

- ejecutar:
```bash
    docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```
- clonar .env.example a .env
- sail up
- sail artisan migrate --seed

## sin docker

- clonar .env.example a .env
- levantar todo el setup como un proyecto normal de laravel
- sail artisan migrate --seed
