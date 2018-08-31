<?php

namespace acidjazz\Humble\Guards;

use Illuminate\Contracts\Auth\Guard;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

use acidjazz\Humble\Models\Session;

class HumbleGuard implements Guard {

  protected $user;
  protected $session;

  /**
  * Determine if the current user is authenticated.
  *
  * @return bool
  */
  public function check()
  {

    if ($this->user != null) {
      return true;
    }


    $token = false;
    $token = request()->get('token') ?: request()->bearerToken() ?:  request()->cookie('token') ?: false;

    $this->session = Session::where('token', $token)->first();

    if ($this->session == null) {
      return false;
    }

    $user = config('humble.user')::where('id', $this->session->user_id)->first();

    if ($this->session == null) {
      return false;
    }

    $this->setUser($user);

    return true;
  }

  public function login(Authenticatable $user)
  {

    if ($this->check()) {
      $this->logout();
    }

    $this->session = Session::create([
      'token' => Session::hash(),
      'user_id' => $user->id,
      'source' => 'Auth::login',
      'cookie' => false,
      'verified' => true,
      'ip' => request()->ip(),
      'agent' => request()->Header('User-Agent'),
    ]);

    $this->setUser($user);

    return $this;
  }

  public function logout()
  {
    $this->session->delete();
    $this->session = null;
    $this->token = null;
  }

  public function attempt(Authenticatable $user)
  {

    $attempt = Session::create([
      'token' => Session::hash(),
      'user_id' => $user->id,
      'source' => 'attempt',
      'cookie' => Session::hash(),
      'verified' => false,
      'ip' => request()->ip(),
      'agent' => request()->Header('User-Agent'),
    ]);

    return $attempt;

  }

  public function verify(String $token, String $cookie)
  {
    $this->session = Session::orWhere([
      ['token', $token],
      ['verified', false],
      ['cookie', false]
    ])->orWhere([
     ['token', $token], 
     ['verified',false], 
     ['cookie', $cookie]
   ])->first();

    if ($this->session != null) {
      $this->session->verified = true;
      $this->session->save();
      $this->setUser(User::find($this->session->user_id));
      return $this->session;
    }

    return false;

  }

  /**
  * Determine if the current user is a guest.
  *
  * @return bool
  */
  public function guest()
  {
  }

  /**
  * Get the currently authenticated user.
  *
  * @return \Illuminate\Contracts\Auth\Authenticatable|null
  */
  public function user()
  {
    return $this->user;
  }

  /**
  * Get the current session.
  *
  * @return \acidjazz\Humble\Models\Session|null
  */
  public function session()
  {
    return $this->session;
  }

  /**
  * Get the ID for the currently authenticated user.
  *
  * @return int|null
  */
  public function id()
  {
  }

  /**
  * Validate a user's credentials.
  *
  * @param  array  $credentials
  * @return bool
  */
  public function validate(array $credentials = [])
  {
  }

  public function token()
  {
    return $this->session->token;
  }

  /**
  * Set the current user.
  *
  * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
  * @return void
  */
  public function setUser(Authenticatable $user)
  {
    $this->user = $user;
  }

}
