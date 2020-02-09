## This package is considered beeing in an alpha state and needs more testing!

## Release note

Version 0.2.7 Override the deprecated function getProcess of the Composer class to support laravel 5.8 and above.

Version 0.2.6 the dev-master param was added to the composer require command run when you enable a service.

Version 0.2.5 Fixed broken update package command. Now you should be able to run

> php artisan laravel-service:update-package

If you have been using version 0.2.2 -> 0.2.4 you will have to fix your composer files and phpunit file manually. Sorry!

```
Note: If you have dependencies between your services (eg. common, core services etc) you should manually disable all service before running the laravel-service:update-package command.
```

Version 0.2.3 service:make command renamed to laravel-service:make.

Version 0.2.2 Due to Composer 2.x more strict naming rules this package has new naming of the services. I have created an artisan upgrade command to make the changes for you. The command will disable all your services and you have to manually enable them when you have run the command.

> php artisan laravel-service:update-package

I have also updated all commands with the prefix laravel-service (instead of service as before)

Version 0.2.1 enabled automatic update of service configs. More info below.

Version 0.2.0 includes generic jobs and queues. This is needed so one service can push data on to a queue without knowing about the code in another service job.

Version 0.1.X is a complete rewrite of the package. Version 0.1.X now creates each service as a composer package and uses composer to add it to your project. It is tested in laravel version 5.6. Previous versions are not supported anymore.

PLEASE NOTE

laravel-service will add:

```json
"minimum-stability": "dev",
"prefer-stable": true
```

to your composer.json file.

## Laravel Service

A package to divide Laravel into smaller services. Then main goal of this package is:

1. Readable, the code is split into services
2. Scalable, run services as needed
3. Faster development, split work between developers

#### Installation

Simply run:

> composer require jake142/laravel-service

#### Usage

Start by running

> php artisan laravel-service:make

This will start a setup wizard where you define:

1. The name of the service
2. The version of the service (eg. V1)
3. If you want a sample controller (recommended)
4. If you want a sample job (recommended)
4. If you want a sample test (recommended)

The service is now created (added as a repository to your composer.json file), but not enabled.

#### Enable a service

To enable a service run:

> php artisan service:enable laravel-service/$version-$service (eg. laravel-service/v1-sampleservice)

This will enable the service which means:

1. Composer runs require on your service
2. The tests will be added to the phpunit.xml and therefore able to run with the phpunit command

#### Disable a service

To disable a service run:

> php artisan service:disable laravel-service/$version-$service (eg. laravel-service/v1-sampleservice)

This will disable the service which means:

1. Composer runs remove on your service
2. The tests will be removed from the phpunit.xml

#### List services and their status

> php artisan service:list

#### Generic Queues and Jobs

To call the generic queue:
```php
(new GenericQueue('Services\\V1\\Test\\Jobs\\ExampleJob', ['param'=>'test'], $queue = null, $options = []))->dispatch();
```
To run the generic job:

```php
<?php namespace Services\V1\Test\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Jake142\Service\Queue\Jobs\Generic as GenericJob;

/**
 * An example job
 */
class ExampleJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, GenericJob;

    public function handle() {
        print_r($this->data);
    }

}
```
#### Config

You can either use vendor:publish to publish the config files for each service or you can publish the laravel-service config file which will get updated when you add new configs to a service. If you use the laravel-service config file you access the config values as following:

```php
config('laravel-service.<version>.<service>.<config_file>.<value>');
```
