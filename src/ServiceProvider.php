<?php

namespace acidjazz\Humble;

use acidjazz\Humble\Guards\HumbleGuard;
use Illuminate\Support\Facades\Auth; 

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
  public function boot()
  {
    Auth::extend('humble', function ($app, $name, array $config) {
      return new HumbleGuard(Auth::createUserProvider($config['provider']));
    });
  }
}
