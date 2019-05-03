<?php

namespace Ongoingcloud\Laravelcrud;

use Illuminate\Support\ServiceProvider;

class LaravelCrudServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/Routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravelcrud');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // Register the service the package provides.
        $this->app->singleton('laravelcrud', function ($app) {
            return new laravelcrud;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravelcrud'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the views.
        // $this->publishes([
        //     __DIR__.'/resources/views' => base_path('resources/views'),
        // ]);

        // Publishing Components.
        $this->publishes([
            __DIR__.'/resources' => base_path('resources'),
        ]);

        // Create directory and file if not exist
        if(!file_exists(base_path().'/routes/Common.php')){
            file_put_contents(base_path().'/routes/Common.php', file_get_contents(__DIR__.'/Common.php'));
        }

        if(!\File::exists(base_path()."/tests/Browser/Backend")) {
            \File::makeDirectory(base_path()."/tests/Browser/Backend", $mode = 0777, true, true);
        }

        if(!\File::exists(base_path()."/tests/Unit/Api")) {
            \File::makeDirectory(base_path()."/tests/Unit/Api", $mode = 0777, true, true);
        }

        if(!\File::exists(base_path()."/app/Models")) {
            \File::makeDirectory(base_path()."/app/Models", $mode = 0777, true, true);
        }

        if(!\File::exists(base_path()."/app/General/ModuleConfig")) {
            \File::makeDirectory(base_path()."/app/General/ModuleConfig", $mode = 0777, true, true);
        }

        if(!file_exists(base_path().'/app/General/ModuleConfig.php')){
            file_put_contents(base_path().'/app/General/ModuleConfig.php', file_get_contents(__DIR__.'/ModuleConfig.php'));            
        }    

        if(!\File::exists(base_path()."/app/Http/Controllers/API")) {
            \File::makeDirectory(base_path()."/app/Http/Controllers/API", $mode = 0777, true, true);
        }

        if(!\File::exists(base_path()."/app/Http/Controllers/Backend")) {
            \File::makeDirectory(base_path()."/app/Http/Controllers/Backend", $mode = 0777, true, true);
        }

        if(!file_exists(base_path().'/app/Http/Controllers/Backend/CommonController.php')){
            file_put_contents(base_path().'/app/Http/Controllers/Backend/CommonController.php', file_get_contents(__DIR__.'/CommonController.php'));            
        } 

        if(!\File::exists(base_path()."/app/Http/Requests")) {
            \File::makeDirectory(base_path()."/app/Http/Requests", $mode = 0777, true, true);
        }

        // append in route file web.php
        $route_append = "\nRoute::group(['namespace' => 'Backend'], function () {
    Route::group(['middleware' => ['auth', 'locale:en']], function () {
        require (__DIR__ . '/Common.php');
    });
});\n"."// [RouteArray]";
        $content = file_get_contents(base_path().'/routes/web.php');

        if(!strpos(file_get_contents(base_path().'/routes/web.php'),"// [RouteArray]")) {
            file_put_contents(base_path().'/routes/web.php', $route_append, FILE_APPEND);
        }

        // append in route file api.php
        $route_append = "\nRoute::get('file/delete', 'Backend\\CommonController@fileDelete');\n"."// [RouteArray]";
        $content = file_get_contents(base_path().'/routes/api.php');

        if(!strpos(file_get_contents(base_path().'/routes/api.php'),"// [RouteArray]")) {
            file_put_contents(base_path().'/routes/api.php', $route_append, FILE_APPEND);
        }

        // Publishing app.js.
        $this->publishes([
            __DIR__.'/resources/js/app.js' => base_path('resources/js/app.js'),
        ]);

        // Publishing bootstrap.js.
        $this->publishes([
            __DIR__.'/resources/js/bootstrap.js' => base_path('resources/js/bootstrap.js'),
        ]);

        // Publishing component.js.
        if(!file_exists(base_path().'/resources/js/component.js'){
            $this->publishes([
                __DIR__.'/resources/js/component.js' => base_path('resources/js/component.js'),
            ]);
        }

        // Publishing User.php.
        $this->publishes([
            __DIR__.'/User.php' => base_path('app/User.php'),
        ]);

        // Publishing the translation files.
        // $this->publishes([
        //     __DIR__.'/resources/lang' => resource_path('lang/vendor/crud'),
        // ]);

        // Publishing Migrations.
        // $this->publishes([
        //     __DIR__.'/../database/migrations' => database_path('migrations'),
        // ]);

        // Registering package commands.
        $this->commands([
            // Console\InstallCommand::class,
        ]);
    }
}
