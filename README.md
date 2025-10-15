# convert-anything

The goal is to convert any uploaded file into another output format.
Supported file types in input are: CSV, JSON, XLSX, and ODS.
Output file formats are: JSON an

## Init

```
docker compose up -d --build
docker compose exec php ash

composer create-project symfony/skeleton app
cd app
composer require api symfony/framework-bundle symfony/orm-pack symfony/messenger symfony/validator symfony/http-client symfony/mime symfony/filesystem symfony/serializer
composer require doctrine/doctrine-bundle
composer require league/flysystem-bundle
composer require phpoffice/phpspreadsheet # XLSX/ODS parsing
composer require --dev phpunit/phpunit symfony/test-pack symfony/maker-bundle

docker compose exec php composer install
docker compose exec php php bin/console doctrine:migrations:diff
docker compose exec php php bin/console doctrine:migrations:migrate -n
```


## change xdebug mode on the fly
`docker compose exec php sh -lc 'export XDEBUG_MODE=debug && php -v'`
