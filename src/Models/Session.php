<?php

namespace Fumeapp\Humble\Models;

use Eloquent;
use Fumeapp\Humble\Contracts\HasAbilities;
use Fumeapp\Humble\Guards\HumbleGuard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Session
 *
 * @mixin Eloquent
 */
class Session extends Model implements HasAbilities
{
    protected $primaryKey = 'token';

    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'abilities' => 'json',
        'location' => 'array',
    ];

    public $appends = ['device', 'current'];

    public static function hash(): string
    {
        return hash('sha256', mt_rand());
    }

    public function getDeviceAttribute(): array
    {
        return HumbleGuard::device($this->agent);
    }

    public function getCurrentAttribute(): bool
    {
        $token = request()->get('token') ?: request()->bearerToken() ?: request()->cookie('token') ?: false;

        return $this->token === $token;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('humble.user'));
    }

    /**
     * Determine if the token has a given ability.
     *
     * @param  string  $ability
     * @return bool
     */
    public function can($ability): bool
    {
        return is_null($this->abilities) || in_array('*', $this->abilities) ||
               array_key_exists($ability, array_flip($this->abilities));
    }

    /**
     * Determine if the token is missing a given ability.
     *
     * @param  string  $ability
     * @return bool
     */
    public function cant($ability): bool
    {
        return ! $this->can($ability);
    }
}
