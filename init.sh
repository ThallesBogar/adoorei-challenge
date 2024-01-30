docker compose build api
docker compose up -d
docker compose exec api composer install
docker compose exec api ./vendor/bin/pest --init
docker compose exec api php artisan key:generate
docker compose exec api php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
docker compose exec api php artisan l5-swagger:generate
docker compose exec api php artisan migrate --seed
