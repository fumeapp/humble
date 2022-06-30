<?php

namespace acidjazz\Humble;

use acidjazz\Humble\Guards\HumbleGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class HumbleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (! app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . '/../config/humble.php', 'humble');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // extend auth guard
        Auth::extend('humble', function ($app, $name, array $config) {
            return new HumbleGuard(Auth::createUserProvider($config['provider']));
        });

        if (app()->runningInConsole()) {

            // migrations
            $this->publishes([
                __DIR__ . '/../migrations' => database_path('migrations'),
            ], 'humble.migrations');

            // config
            $this->publishes([
                __DIR__ . '/../config/humble.php' => config_path('humble.php'),
            ], 'humble.config');
        }
    }
}
