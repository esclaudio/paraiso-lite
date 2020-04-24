<?php

use App\Support\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRisksLevelsTable extends Migration
{
    public function up()
    {
        $this->schema->create('risks_levels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('risk_type_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color');

            $table->foreign('risk_type_id', 'fk_risks_levels_risks_types')
                ->references('id')
                ->on('risks_types');
        });
    }

    public function down()
    {
        $this->schema->drop('risks_levels');
    }
}
