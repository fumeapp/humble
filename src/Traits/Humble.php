<?php

namespace acidjazz\Humble\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use acidjazz\Humble\Models\Session;

trait Humble
{

  public function getSession(): Session
  {
    return $this->session;
  }

  public function sessions(): HasMany
  {
    return $this->hasMany(Session::class, 'user_id', 'id');
  }

  public function getHasActiveSessionAttribute(): bool
  {
    $active = false;
    foreach ($this->sessions as $session) {
      if ($session->updated_at->diffInSeconds() < 300) {
        $active = true;
      }
    }
    return $active;
  }

  public function getSessionAttribute(): Session
  {
    return Auth::session();
  }

  public function getLocationAttribute(): array
  {
    return $this->session->location;
  }

}
