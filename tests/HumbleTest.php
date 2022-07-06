<?php

namespace Fumeapp\Humble\Tests;

use Fumeapp\Humble\Tests\Models\User;

class HumbleTest extends TestCase
{
    public function test_session_token_can_be_created()
    {
        $user = User::create([
            'name' => 'John Doe',
        ]);

        $newToken = $user->createToken('action');
        $storedToken = $user->sessions()->first()->getKey();

        $this->assertEquals($newToken, $storedToken);
    }

    public function test_session_token_can_be_created_with_abilities()
    {
        $user = User::create([
            'name' => 'John Doe',
        ]);

        $abilities = ['create', 'update', 'delete'];

        $newToken = $user->createToken('action', $abilities);
        $storedToken = $user->sessions()->find($newToken);

        $this->assertEquals($storedToken->abilities, $abilities);
    }

    public function test_token_can_return_true_with_correct_abilities()
    {
        $user = User::create([
            'name' => 'John Doe',
        ]);

        $abilities = ['create', 'update', 'delete'];

        $newToken = $user->createToken('action', $abilities);
        $storedToken = $user->sessions()->find($newToken);

        $this->assertTrue($storedToken->can('delete'));
    }

    public function test_token_can_return_false_if_it_does_not_have_()
    {
        $user = User::create([
            'name' => 'John Doe',
        ]);

        $abilities = ['create', 'update'];

        $newToken = $user->createToken('action', $abilities);
        $storedToken = $user->sessions()->find($newToken);

        $this->assertFalse($storedToken->can('delete'));
    }
}
