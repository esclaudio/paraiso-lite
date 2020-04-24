<?php

use App\Support\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRolesPermissionsTable extends Migration
{
    public function up()
    {
        $this->schema->create('roles_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('permission_id');

            $table->foreign('role_id', 'fk_roles_permissions_roles')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->foreign('permission_id', 'fk_roles_permissions_permissions')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');
                
            $table->primary(['role_id', 'permission_id']);
        });
    }

    public function down()
    {
        $this->schema->drop('roles_permissions');
    }
}
