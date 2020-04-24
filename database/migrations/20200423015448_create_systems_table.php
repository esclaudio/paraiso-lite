<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSystemsTable extends Migration
{
    public function up()
    {
        $this->schema->create('systems', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique('ux_systems_name');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by', 'fk_systems_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_systems_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('systems');
    }
}
