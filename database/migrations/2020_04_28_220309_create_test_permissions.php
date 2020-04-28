<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permission = new \App\Permission();
        $permission->name = 'live happily ever after';
        $permission->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Permission::query()->whereIn('name', ['live happily ever after'])->delete();
    }
}
