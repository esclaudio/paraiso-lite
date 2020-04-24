<?php

use App\Support\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProcessesTable extends Migration
{
    public function up()
    {
        $this->schema->create('processes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique('ux_processes_name');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by', 'fk_processes_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_processes_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('processes');
    }
}
