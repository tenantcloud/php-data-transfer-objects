# Laravel Data transfer objects

Laravel data transfer objects.

## Requirements

-   PHP version >=7.4.1
-   Docker (optional)

## Installation

Require this package with composer.

```
composer require tenantcloud/php-data-transfer-objects
```

## Examples

    // Create empty data object.
    $data = ExampleDTO::create();

    // Serialize for array or json
    $data->toArray();
    $data->toJson();

    // Create from existing data
    $data = ExampleDTO::from(['foo']);

    // Check is property filled
    $data->hasFoo();

    // Get foo property
    $data->getFoo();

    // Set property
    $data->setFoo($foo);

### Commands

Install dependencies:
`docker run -it --rm -v $PWD:/app -w /app composer install`

Run tests:
`docker run -it --rm -v $PWD:/app -w /app php:7.4-cli vendor/bin/phpunit`

Run php-cs-fixer on self:
`docker run -it --rm -v $PWD:/app -w /app composer cs-fix`
