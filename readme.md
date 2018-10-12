## Package considered beeing in alpha state and needs more testing!

## Release note

Version 0.1.0 is a complete rewrite of the package. Version 0.1.0 now creates each service as a composer package and uses composer to add it to your project. It is tested in laravel version 5.6. Previous versions are not supported anymore.

PLEASE NOTE

laravel-services will add:

```json
"minimum-stability": "dev",
"prefer-stable": true
```

to your composer.json file.

## Laravel Services

A package to divide Laravel into smaller services. Then main goal of this package is:

1. Readable, the code is split into services
2. Scalable, run services as needed
3. Faster development, split work between developers

#### Installation

Simply run:

> composer require jake142/laravel-service

#### Usage

Start by running

> php artisan service:make

This will start a setup wizard where you define:

1. The name of the service
2. The version of the service (eg. V1)
3. If you want a sample controller (recommended)
4. If you want a sample job (recommended)
4. If you want a sample test (recommended)

The service is now created (added as a repository to your composer.json file), but not enabled. To enable the service

> php artisan service:enable $version/$service name (eg. V1/SampleService)

This will enable the service which means:

1. Composer runs require on your service
2. The tests will be added to the phpunit.xml and therefore able to run with the phpunit command

#### Disable a service

To disable a service run:

> php artisan service:disable $version/$service name (eg. V1/SampleService)

This will disable the service which means:

1. Composer runs remove on your service
2. The tests will be removed from the phpunit.xml

#### List services and their status

> php artisan service:list