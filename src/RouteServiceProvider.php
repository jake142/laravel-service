<?php namespace Jake142\Service;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $services = config('appservices');
        if(!empty($services))
        {
            foreach($services as $key => $value)
            {
                if($value==1)
                {
                    Route::namespace('\\App\\Services\\'.str_replace("/", "\\", $key).'\\Http\\Controllers')->group(app_path('Services/'.$key.'/routes/web.php'));
                    Route::namespace('\\App\\Services\\'.str_replace("/", "\\", $key).'\\Http\\Controllers')->group(app_path('Services/'.$key.'/routes/api.php'));
                }
            }
        }
    }
}
