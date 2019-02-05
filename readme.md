## This package is considered beeing in an alpha state and needs more testing!

## Release note

Version 0.3.0 Renamed to "Laravel pods", suggested a new scaffold model, common + pods 

Version 0.2.1 enabled automatic update of service configs. More info below.

Version 0.2.0 includes generic jobs and queues. This is needed so one service can push data on to a queue without knowing about the code in another service job.

Version 0.1.X is a complete rewrite of the package. Version 0.1.X now creates each service as a composer package and uses composer to add it to your project. It is tested in laravel version 5.6. Previous versions are not supported anymore.

PLEASE NOTE

laravel-pods will add:

```json
"minimum-stability": "dev",
"prefer-stable": true
```

to your composer.json file.


## Laravel Pods

A package to divide Laravel projects into smaller pods. Then main goal of this package is:

1. Readable, the code is split into pods
2. Scalable, run pods as needed
3. Faster development, split work between developers

#### Installation

Simply run:

> composer require lindstream/laravel-pods

#### Usage

Start by running

> php artisan pods:create

This will start a setup wizard where you define:

1. The name of the pod
2. The version of the pod (eg. V1)
3. If you want a sample controller (recommended)
4. If you want a sample job (recommended)
4. If you want a sample test (recommended)

The pod is now created (added as a repository to your composer.json file), but not enabled.

#### Enable a pod

To enable a pod run:

> php artisan pod:enable $version/$pod name (eg. V1/SamplePod)

This will enable the pod which means:

1. Composer runs require on your pod
2. The tests will be added to the phpunit.xml and therefore able to run with the phpunit command

#### Disable a pod

To disable a pod run:

> php artisan pods:disable $version/$pod name (eg. V1/SamplePod)

This will disable the pod which means:

1. Composer runs remove on your pod
2. The tests will be removed from the phpunit.xml

#### List pods and their status

> php artisan pods:list

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

use Jake142\LaravelPods\Queue\Jobs\Generic as GenericJob;

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

You can either use vendor:publish to publish the config files for each pod or you can publish the laravel-pods config file which will get updated when you add new configs to a pod. If you use the laravel-pods config file you access the config values as following:

```php
config('laravel-service.<version>.<service>.<config_file>.<value>');
```
