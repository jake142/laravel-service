<?php namespace Jake142\Service;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Jake142\Service\Commands\MakeService;
use Jake142\Service\Commands\ListService;
use Jake142\Service\Commands\EnableService;
use Jake142\Service\Commands\DisableService;
use Jake142\Service\Composer;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /*$this->publishes([
            __DIR__ . '/config/laravel-service.php' => config_path('laravel-service.php')
        ]);*/
        //Setting up commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeService::class,
                ListService::class,
                EnableService::class,
                DisableService::class,
            ]);
        }
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('composer', function ($app) {
            return new Composer($app['files'], $app->basePath());
        });
        $this->app->singleton('phpunitxml', function ($app) {
            return new PhpunitXML();
        });
    }
}