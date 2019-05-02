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

        // Publishing app.js.
        $this->publishes([
            __DIR__.'/resources/js/app.js' => base_path('resources/js/app.js'),
        ]);

        // Publishing bootstrap.js.
        $this->publishes([
            __DIR__.'/resources/js/bootstrap.js' => base_path('resources/js/bootstrap.js'),
        ]);

        // Publishing component.js.
        $this->publishes([
            __DIR__.'/resources/js/component.js' => base_path('resources/js/component.js'),
        ]);

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
