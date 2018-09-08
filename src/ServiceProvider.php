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

    if ($this->runningInConsole()) {
      $this->publishConfig();
      $this->publishMigrations();
    }
  }

  public function publishMigrations()
  {

    if (class_exists('CreateSessionsTable')) {
      return;
    }

    $timestamp = date('Y_m_d_His', time());
    $stub = __DIR__.'/../migrations/create_sessions_table.php';
    $target = $this->app->databasePath().'/migrations/'.$timestamp.'_create_sessions_table.php';
    $this->publishes([$stub => $target], 'humble.migrations');

  }

  public function publishConfig()
  {
    $this->publishes([
      __DIR__.'/../config/humble.php' => config_path('humble.php')
    ], 'config');
  }

  /**
   * Determine if we are running in the console.
   *
   * Copied from Laravel's Application class, since we need to support 5.1.
   *
   * @return bool
   */
  protected function runningInConsole()
  {
    return php_sapi_name() == 'cli' || php_sapi_name() == 'phpdbg';
  }
}
