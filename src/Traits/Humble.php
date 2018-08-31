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
    return $this->HasMany(Session::class);
  }

}
