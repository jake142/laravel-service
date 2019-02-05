<?php namespace Jake142\LaravelPods;

use Jake142\LaravelPods\Composer;
use Jake142\LaravelPods\Commands\ListPods;
use Jake142\LaravelPods\Commands\SetupPods;
use Jake142\LaravelPods\Commands\CreatePods;
use Jake142\LaravelPods\Commands\EnablePods;
use Jake142\LaravelPods\Commands\DisablePods;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //Publish the cfg
        $this->publishes([
            __DIR__.'/config/pods.php' => config_path('pods.php'),
        ]);
        //Setting up commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupPods::class,
                CreatePods::class,
                ListPods::class,
                EnablePods::class,
                DisablePods::class,
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
