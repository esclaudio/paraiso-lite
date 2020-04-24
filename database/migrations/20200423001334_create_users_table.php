<?php

use App\Support\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->schema->create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->unique('ux_users_username');
            $table->string('password');
            $table->string('name');
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_admin')->default(false);
            $table->string('remember_token')->nullable();
            $table->string('avatar')->nullable();
            $table->string('language')->nullable();
            $table->timestamps();
        });

        $this->schema->table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by', 'fk_roles_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_roles_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->table('roles', function (Blueprint $table) {
            $table->dropForeign('fk_roles_created_by');
            $table->dropForeign('fk_roles_updated_by');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
        
        $this->schema->drop('users');
    }
}
