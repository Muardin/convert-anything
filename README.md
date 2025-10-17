# convert-anything (MVP)

The goal is to convert any uploaded file into another output format.
Since it's an MVP, it comes with limited supported types, but will be easy to extend this.

Supported file types in input are: 
* [CSV](src/Service/Conversion/InputParser/CsvInputParser.php)

Output file formats are:
* [JSON](src/Service/Conversion/OutputWriter/JsonWriter.php)

## Workflow
- Upload any supported file via POST /jobs. Payload: 
  - file = the file to be converted
  - output = the format you want to convert to e.g. json
- save file and create a [database entry](src/Entity/ConversionJob.php) to track progress, ConversionJob.status = 'queued'
- [async job](src/MessageHandler/RunConversionHandler.php) will start and grab one entry
  - sets ConversionJob.status = 'running', to prevent having multiple Jobs working on the same entry
- [Converter](src/Service/Conversion/ConverterPipeline.php) will do the conversion in following steps:
  - [Parse input file](src/Service/Conversion/InputParser/InputParserFactory.php) based on file extension
  - [write a new file](src/Service/Conversion/OutputWriter/OutputWriterFactory.php) based on the given `output` of the POST call
  - save new ConversionJob.status ('failed' or 'done')
- User can track conversion via GET /jobs/{id}
- Once conversion is done, the file can be downloaded via GET /jobs/{id}/result

## What's next
- Support multiple way to convert files, not just via API (move logic from Controller into a Service) 
- support more input- and output-formats:
  - openspout/openspout for XLS/ODS
- config option for the different InputParsers and OutputWriters e.g. define different separator for CSV
- `size-aware workers`: improve runtimes for large files, e.g. by having different queues (with different CPU/RAM) for different file sizes


## Setup

### Init
```
docker compose build --pull --no-cache
docker compose up -d --wait

# install vendors
docker compose exec php bash
composer require symfony/orm-pack api symfony/messenger symfony/doctrine-messenger symfony/validator symfony/http-client symfony/mime symfony/filesystem symfony/serializer
composer require league/flysystem-bundle
composer require --dev phpunit/phpunit symfony/test-pack symfony/maker-bundle

# database stuff
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate
php bin/console doctrine:database:create --env=test --if-not-exists
php bin/console doctrine:migrations:migrate --env=test
```

### Notes
* Web access: `https://localhost` and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
* Build a migration: `php bin/console make:migration`
* Run tests: `rm -rf var/cache/test var/cache/dev && php ./vendor/bin/phpunit --testdox`


## Base on Symfony Docker
Based on [Symfony Docker](https://github.com/dunglas/symfony-docker)

### Docs
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

