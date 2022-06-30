<?php

namespace Fumeapp\Humble\Tests;

use Fumeapp\Humble\Exceptions\MissingAbilityException;
use Fumeapp\Humble\Http\Middleware\CheckForAnyAbility;
use Illuminate\Auth\AuthenticationException;
use Mockery;
use PHPUnit\Framework\TestCase;

class CheckForAnyAbilityTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    public function test_middleware_passes_request_if_any_given_abilities_are_set_on_session_token()
    {
        $middleware = new CheckForAnyAbility();
        $request = Mockery::mock();
        $request->shouldReceive('user')->andReturn($user = Mockery::mock());
        $user->shouldReceive('getSession')->andReturn(Mockery::mock());
        $user->shouldReceive('tokenCan')->with('read')->andReturn(true);
        $user->shouldReceive('tokenCan')->with('write')->andReturn(true);

        $response = $middleware->handle($request, fn () => 'response', 'read', 'write');

        $this->assertSame('response', $response);
    }

    public function test_exception_is_thrown_if_session_token_is_missing_ability()
    {
        $this->expectException(MissingAbilityException::class);

        $middleware = new CheckForAnyAbility();
        $request = Mockery::mock();
        $request->shouldReceive('user')->andReturn($user = Mockery::mock());
        $user->shouldReceive('getSession')->andReturn(Mockery::mock());
        $user->shouldReceive('tokenCan')->with('read')->andReturn(false);
        $user->shouldReceive('tokenCan')->with('write')->andReturn(false);

        $middleware->handle($request, fn () => 'response', 'read', 'write');
    }

    public function test_exception_is_thrown_if_no_authenticated_request_user()
    {
        $this->expectException(AuthenticationException::class);

        $middleware = new CheckForAnyAbility();
        $request = Mockery::mock();
        $request->shouldReceive('user')->once()->andReturn(null);

        $middleware->handle($request, fn () => 'response', 'read', 'write');
    }

    public function test_exception_is_thrown_if_no_session_token()
    {
        $this->expectException(AuthenticationException::class);

        $middleware = new CheckForAnyAbility();
        $request = Mockery::mock();
        $request->shouldReceive('user')->andReturn($user = Mockery::mock());
        $user->shouldReceive('getSession')->andReturn(null);

        $middleware->handle($request, fn () => 'response', 'read', 'write');
    }
}
