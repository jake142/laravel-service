## Laravel Services

A package to devide Laravel into smaller services. Then main main goal of this package is:

1. Readable, the code is split into services
2. Scalable, run services as needed
3. Faster development, devide work between developers

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

The service is now created, but not active. To activate the service

> php artisan service:update $version/$service name (eg. V1/SampleService)

This will activate the service which means:

1. The routes will be accessible
2. The jobs can be run

To get an overview of all services, both active and inactive, run:

> php artisan service:list

#### Jobs

The jobs has two properties:

1. The queueName (that should not be changed)
2. The priority which can be low, medium or high

To run the jobs you simply run

> php artisan service:run

This will run all the jobs in services that are activated.
