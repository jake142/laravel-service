## Laravel Services

A package to divide Laravel into smaller services. Then main goal of this package is:

1. Readable, the code is split into services
2. Scalable, run services as needed
3. Faster development, split work between developers

#### Installation

Simply run:

> composer require jake142/laravel-services

#### Usage

Start by running

> php artisan service:create

This will start a setup wizard where you define:

1. The name of the service
2. The version of the service (eg. V1)
3. If you want a sample controller (recommended)
4. If you want a sample job (recommended)
4. If you want a sample test (recommended)

The service is now created, but not active. To activate the service

> php artisan service:update $version/$service name (eg. V1/SampleService)

This will activate the service which means:

1. The routes will be accessible
2. The jobs can be run
3. The tests will be added to the phpunit.xml and therefore able to run with the phpunit command

The above will be reversed if you choose to deactivate your service.

To get an overview of all services, both active and inactive, run:

> php artisan service:list

#### Config

Each service can have one or more separate config files. To access these config values you run:

config('laravel-service.$version/$service.cfg.$nameOfCfgFile.VALUE_IN_THE_CFG_FILE')

#### Jobs

The jobs has two properties:

1. The queueName (that should not be changed)
2. The priority which can be low, medium or high

To run the jobs you simply run

> php artisan service:run

This will run all the jobs in services that are activated.
