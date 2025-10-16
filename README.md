# convert-anything

The goal is to convert any uploaded file into another output format.
Supported file types in input are: CSV, JSON, XLSX, and ODS.
Output file formats are: JSON an

# Symfony Docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework,
with [FrankenPHP](https://frankenphp.dev) and [Caddy](https://caddyserver.com/) inside!

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up --wait` to set up and start a fresh Symfony project
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

### install important vendors
```
docker compose exec php bash
composer require symfony/orm-pack api symfony/messenger symfony/doctrine-messenger symfony/validator symfony/http-client symfony/mime symfony/filesystem symfony/serializer
composer require league/flysystem-bundle
composer require --dev phpunit/phpunit symfony/test-pack symfony/maker-bundle

# database stuff
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:database:create --env=test --if-not-exists
php bin/console doctrine:migrations:migrate --env=test

# not required?
composer require phpoffice/phpspreadsheet # XLSX/ODS parsing
```

## API-Platform
Browse via http://localhost/api
Create resource
`php bin/console make:entity --api-resource`


## Notes
docker compose up -d
php bin/console make:migration
rm -rf var/cache/test var/cache/dev && php ./vendor/bin/phpunit --testdox







## Docs
1. [Options available](docs/options.md)
2. [Using Symfony Docker with an existing project](docs/existing-project.md)
3. [Support for extra services](docs/extra-services.md)
4. [Deploying in production](docs/production.md)
5. [Debugging with Xdebug](docs/xdebug.md)
6. [TLS Certificates](docs/tls.md)
7. [Using MySQL instead of PostgreSQL](docs/mysql.md)
8. [Using Alpine Linux instead of Debian](docs/alpine.md)
9. [Using a Makefile](docs/makefile.md)
10. [Updating the template](docs/updating.md)
11. [Troubleshooting](docs/troubleshooting.md)

