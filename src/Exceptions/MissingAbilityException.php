<?php

namespace Fumeapp\Humble\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;

class MissingAbilityException extends AuthorizationException
{
    /**
     * The abilities that the user did not have.
     *
     * @var array<string>
     */
    protected $abilities;

    /**
     * Create a new missing scope exception.
     *
     * @param  array|string  $abilities
     * @param  string  $message
     * @return void
     */
    public function __construct($abilities = [], $message = 'Invalid ability provided for token.')
    {
        parent::__construct($message);

        $this->abilities = Arr::wrap($abilities);
    }

    /**
     * Get the abilities that the user did not have.
     *
     * @return array
     */
    public function abilities(): array
    {
        return $this->abilities;
    }
}
