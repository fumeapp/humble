<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateSessionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {

    Schema::create('sessions', function (Blueprint $table) {
      $table->string('token', 64)->unique();
      $table->bigInteger('user_id')->unsigned();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->string('source')->nullable();

      // user metadata
      $table->string('to')->nullable();
      $table->string('active')->nullable();
      $table->string('ip', 36)->nullable();
      $table->string('agent')->nullable();

      $table->string('location')->nullable();

      $table->timestamps();
      $table->primary('token');
    });

  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('sessions');
  }

}
