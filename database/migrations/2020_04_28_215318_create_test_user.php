<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = new \App\User();
        $user->name = 'test';
        $user->email = 'test@test.com';
        $user->email_verified_at = \Carbon\Carbon::now();
        $user->password = \Illuminate\Support\Facades\Hash::make('test');
        $user->save();
        $user = new \App\User();
        $user->name = 'foo';
        $user->email = 'foo@test.com';
        $user->email_verified_at = \Carbon\Carbon::now();
        $user->password = \Illuminate\Support\Facades\Hash::make('test');
        $user->save();
        $user = new \App\User();
        $user->name = 'bar';
        $user->email = 'bar@test.com';
        $user->email_verified_at = \Carbon\Carbon::now();
        $user->password = \Illuminate\Support\Facades\Hash::make('test');
        $user->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\User::query()->whereIn('name', ['test', 'foo', 'bar'])->delete();
    }
}
