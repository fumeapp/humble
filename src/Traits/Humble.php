<?php

namespace acidjazz\Humble\Traits;

use Illuminate\Support\Facades\Auth; 
use acidjazz\Humble\Models\Session;
use Illuminate\Http\Request;

trait Humble
{

  public function getSession()
  {
    return $this->session;
  }

  public function sessions()
  {
    return $this->hasMany(Session::class);
  }

  public function getSessionAttribute()
  {
    $token = request()->get('token') ?: request()->bearerToken() ?:  request()->cookie('token') ?: false;
    if ($token) {
      return Session::where('token', $token)->first();
    }
    return false;
  }

}
