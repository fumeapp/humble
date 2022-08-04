<?php

namespace Fumeapp\Humble\Tests;

use Fumeapp\Humble\HumbleServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            HumbleServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $this->migrateDatabase();
    }

    public function migrateDatabase()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('token', 64)->unique();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('source')->nullable();
            $table->json('abilities')->nullable();
            $table->string('ip', 300)->nullable();
            $table->string('agent')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
            $table->primary('token');
        });

        Schema::create('attempts', function (Blueprint $table) {
            $table->string('token', 64)->unique();
            $table->json('action')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('ip', 300)->nullable();
            $table->string('agent')->nullable();
            $table->timestamps();
            $table->primary('token');
        });
    }
}
