<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRisksConsequencesTable extends Migration
{
    public function up()
    {
        $this->schema->create('risks_consequences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('risk_type_id');
            $table->string('name');
            $table->text('description')->nullable();

            $table->foreign('risk_type_id', 'fk_risks_consequences_risks_types')
                ->references('id')
                ->on('risks_types');
        });
    }

    public function down()
    {
        $this->schema->drop('risks_consequences');
    }
}
