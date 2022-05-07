<?php

namespace acidjazz\Humble\Guards;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use WhichBrowser;

use acidjazz\Humble\Models\Attempt;
use acidjazz\Humble\Models\Session;
use App\Models\User as UserModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class HumbleGuard implements Guard
{

    protected Authenticatable|null|User $user = null;

    /* @var ?Session $session */
    protected ?Session $session;

    /* @var mixed $action */
    public mixed $action = null;

    /**
     * check if we have a user
     *
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function hasUser(): bool
    {
        return $this->check();
    }


    /**
     * Validate a token
     *
     * @param $token
     * @return bool
     */
    private function validToken($token): bool
    {
        return strlen($token) === 64;
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function check(): bool
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

        if ($user === null) {
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function login(Authenticatable $user, string $source = null): static
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
    public function ip(): array|string|null
    {
        return request()->header('X-Forwarded-For') ?: request()->ip();
    }

    /**
     * Return a cleaned up GeoIP result
     * @return array
     */
    public function geoip(): array
    {
        if (strpos($this->ip(), ',') !== false) {
            $ip = explode(', ', $this->ip())[0];
        } else {
            $ip = $this->ip();
        }
        $loc = geoip($ip)->toArray();
        unset($loc['iso_code'], $loc['continent'], $loc['state_name'], $loc['default'], $loc['cached']);
        return $loc;
    }

    public static function device($userAgent = null): array
    {
        if ($userAgent !== null) {
            $agent = new WhichBrowser\Parser($userAgent);
        } else {
            $agent = new WhichBrowser\Parser(request()->Header('User-Agent'));
        }
        return [
            'string' => $agent->toString(),
            'platform' => $agent->os->toString(),
            'browser' => $agent->browser->toString(),
            'name' => $agent->device->toString(),
            'desktop' => $agent->isType('desktop'),
            'mobile' => $agent->isMobile(),
        ];
    }

    /**
     * Logout a User
     *
     * @throws Exception
     */
    public function logout(): void
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
     * @param mixed $action
     * @return mixed
     */
    public function attempt(Authenticatable $user, $action = null)
    {
        return Attempt::create(
            [
                'token' => Session::hash(),
                'user_id' => $user->id,
                'action' => $action,
                'ip' => $this->ip(),
                'agent' => request()->Header('User-Agent'),
            ]
        );
    }

    /**
     * Verify and activate a session based on its token
     * @param String $token
     * @return $this|false
     */
    public function verify(String $token)
    {
        $attempt = Attempt::where('token', $token)->first();
        if ($attempt != null) {
            $user = config('humble.user')::find($attempt->user_id);
            $this->action = $attempt->action;
            $attempt->delete();
            return $this->login($user, 'email');
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
     * @return Model|UserModel|Authenticatable|User|null
     */
    public function user(): Model|UserModel|Authenticatable|User|null
    {
        return $this->user;
    }

    /**
     * Get the current session.
     *
     * @return Session|null
     */
    public function session(): ?Session
    {
        return $this->session;
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id(): ?int
    {
        return $this->user->id;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return void
     */
    public function validate(array $credentials = []): void
    {
    }

    /**
     * Return the current sessions token
     *
     * @return ?string
     */
    public function token(): ?string
    {
        return $this->session->token;
    }

    /**
     * Set the current user.
     *
     * @param Authenticatable $user
     * @return void
     */
    public function setUser(Authenticatable $user): void
    {
        $this->user = $user;
    }

}
