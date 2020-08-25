<?php

namespace acidjazz\Humble;

use acidjazz\Humble\Guards\HumbleGuard;
use Illuminate\Support\Facades\Auth;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
  public function register()
  {
    $configPath = __DIR__ . '/../config/humble.php';
    $this->mergeConfigFrom($configPath, 'humble');
  }
  public function boot()
  {
    Auth::extend('humble', function ($app, $name, array $config) {
      return new HumbleGuard(Auth::createUserProvider($config['provider']));
    });

    $this->publishConfig();
    $this->publishMigrations();
  }

  public function publishMigrations()
  {

    if (class_exists('CreateSessionsTable')) {
      return;
    }

    $timestamp = date('Y_m_d_His', time());
    $stub = __DIR__ . '/../migrations/create_humble_tables.php';
    $target = $this->app->databasePath().'/migrations/'.$timestamp.'_create_humble_tables.php';
    $this->publishes([$stub => $target], 'humble.migrations');

  }

  public function publishConfig()
  {
    $stub =  __DIR__.'/../config/humble.php';
    $target = config_path('humble.php');
    $this->publishes([$stub => $target], 'humble.config');
  }

}
