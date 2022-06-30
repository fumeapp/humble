<?php

namespace acidjazz\Humble\Http\Middleware;

use acidjazz\Humble\Exceptions\MissingAbilityException;
use Illuminate\Auth\AuthenticationException;

class CheckAbilities
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$abilities
     * @return \Illuminate\Http\Response
     *
     * @throws AuthenticationException|MissingAbilityException
     */
    public function handle($request, $next, ...$abilities)
    {
        if (! $request->user() || ! $request->user()->getSession()) {
            throw new AuthenticationException;
        }

        foreach ($abilities as $ability) {
            if (! $request->user()->tokenCan($ability)) {
                throw new MissingAbilityException($ability);
            }
        }

        return $next($request);
    }
}
