<?php

namespace autoengine\crudpack;

use Illuminate\Support\ServiceProvider;

class CrudpackServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__.'/resources/views', 'crudpack');

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
        $this->mergeConfigFrom(__DIR__.'/../config/crudpack.php', 'crudpack');

        // Register the service the package provides.
        $this->app->singleton('crudpack', function ($app) {
            return new crudpack;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['crudpack'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/crudpack.php' => config_path('crudpack.php'),
        ]);
       // dd(config_path('crudpack.php'));

        // Publishing the views.
        $this->publishes([
            __DIR__.'/resources/views' => base_path('resources/views/vendor/autoengine'),
        ]);

        // Publishing Components.
        $this->publishes([
            __DIR__.'/resources/js/components' => base_path('resources/js/components/autoengine'),
        ]);

        // Publishing the translation files.
        $this->publishes([
            __DIR__.'/resources/lang' => resource_path('lang/vendor/autoengine'),
        ]);

        // Publishing Migrations.
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ]);
        // // Registering package commands.
        // $this->commands([]);
    }
}
