<?php

use App\Support\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRisksTypesTable extends Migration
{
    public function up()
    {
        $this->schema->create('risks_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique('ux_risks_types_name');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by', 'fk_risks_types_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_risks_types_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('risks_types');
    }
}
