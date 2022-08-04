<?php

namespace Fumeapp\Humble\Traits;

use Fumeapp\Humble\Models\Session;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

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
            if ($session->updated_at->diffInSeconds() < config('humble.active_session_time', 300)) {
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

    /**
     * Determine if the current session token has a given scope
     *
     * @param  string  $ability
     * @return bool
     */
    public function tokenCan(string $ability)
    {
        return Auth::session() && Auth::session()->can($ability);
    }

    /**
     * Determine if the current session token does not have a given scope
     *
     * @param  string  $ability
     * @return bool
     */
    public function tokenCannot(string $ability)
    {
        return Auth::session() && Auth::session()->cant($ability);
    }

    /**
     * Create a new session token for the user
     *
     * @param  string  $source
     * @param  array  $abilities
     * @return string
     */
    public function createToken(string $source, array $abilities = ['*']): string
    {
        return $this->sessions()->create([
            'token' => Session::hash(),
            'source' => $source,
            'abilities' => $abilities,
            'ip' => auth()->ip() ?? request()->ip(),
            'location' => auth()->geoip() ?? null,
            'agent' => request()->Header('User-Agent'),
        ])
            ->getKey();
    }
}
