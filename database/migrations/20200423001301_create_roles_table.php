<?php

use App\Support\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRolesTable extends Migration
{
    public function up()
    {
        $this->schema->create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique('ux_role_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->schema->drop('roles');
    }
}
