<?php

namespace Fumeapp\Humble\Tests\Models;

use Fumeapp\Humble\Models\Session;
use Fumeapp\Humble\Traits\Humble;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $guarded = [];

    use Humble;

    /**
     * Override the default session model since in testing we don't have the request object.
     */
    public function createToken(string $source, array $abilities = ['*']): string
    {
        return $this->sessions()->create([
            'token' => Session::hash(),
            'source' => $source,
            'abilities' => $abilities,
            'ip' => null,
            'location' => null,
            'agent' => null,
        ])
            ->getKey();
    }
}
