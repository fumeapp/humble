<?php

namespace acidjazz\Humble\Guards;

use acidjazz\Humble\Models\Session;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;

class HumbleGuard implements Guard
{

    /* @var Authenticatable $user */
    protected $user;

    /* @var Session $session */
    protected $session;


    /**
     * check if we have a user
     *
     * @return bool
     */
    public function hasUser()
    {
        return $this->check();
    }


    /**
     * Validate a token
     *
     * @param $token
     * @return bool
     */
    private function validToken($token)
    {
        return strlen($token) === 64;
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        if ($this->user !== null) {
            return true;
        }

        $token = request()->get('token')
            ?: request()->bearerToken()
            ?: request()->cookie('token')
            ?: false;

        if (!$this->validToken($token)) {
            return false;
        }

        $this->session = Session::where('token', $token)->first();

        if ($this->session == null) {
            return false;
        }

        $user = config('humble.user')::where('id', $this->session->user_id)->first();

        if ($this->session === null) {
            return false;
        }

        $this->setUser($user);

        return true;
    }

    /**
     * Login a User
     *
     * @param Authenticatable $user
     * @param string|null $source
     * @return $this
     */
    public function login(Authenticatable $user, string $source = null)
    {
        if ($this->check()) {
            $this->logout();
        }

        $this->session = Session::create(
            [
                'token' => Session::hash(),
                'user_id' => $user->id,
                'source' => $source,
                'ip' => $this->ip(),
                'location' => $this->geoip(),
                'agent' => request()->Header('User-Agent'),
            ]
        );

        $this->setUser($user);
        $this->setUser(config('humble.user')::find($this->session->user_id));
        return $this;
    }

    /**
     * Return an IP Address
     *
     * @return array|string|null
     */
    private function ip()
    {
        return request()->header('X-Forwarded-For') ?: request()->ip();
    }

    /**
     * Return a cleaned up GeoIP result
     * @return array
     */
    private function geoip()
    {
        $loc = geoip($this->ip())->toArray();
        unset($loc['iso_code'], $loc['continent'], $loc['state_name'], $loc['default'], $loc['cached']);
        return $loc;
    }

    /**
     * Logout a User
     *
     * @throws \Exception
     */
    public function logout()
    {
        $this->session->delete();
        $this->session = null;
        $this->token = null;
    }

    /**
     * Perform an attempt to login
     *
     * @param Authenticatable $user
     * @param string $source
     * @param null $to
     * @return mixed
     */
    public function attempt(Authenticatable $user, $source = 'e-mail', $to = null)
    {
        $attempt = Session::create(
            [
                'token' => Session::hash(),
                'user_id' => $user->id,
                'source' => $source,
                'to' => $to,
                'ip' => $this->ip(),
                'location' => $this->geoip(),
                'agent' => request()->Header('User-Agent'),
            ]
        );

        return $attempt;
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
     * @return Authenticatable|null
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * Get the current session.
     *
     * @return Session|null
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
        return $this->user->id;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
    }

    /**
     * Return the current sessions token
     *
     * @return mixed
     */
    public function token()
    {
        return $this->session->token;
    }

    /**
     * Set the current user.
     *
     * @param Authenticatable $user
     * @return void
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }

}
