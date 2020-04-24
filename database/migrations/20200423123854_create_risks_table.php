<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRisksTable extends Migration
{
    public function up()
    {
        $this->schema->create('risks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('system_id');
            $table->unsignedBigInteger('process_id');
            $table->unsignedBigInteger('risk_type_id');
            $table->text('description');
            $table->text('impact');
            $table->unsignedBigInteger('risk_likelihood_id');
            $table->unsignedBigInteger('risk_consequence_id');
            $table->unsignedBigInteger('risk_level_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('system_id', 'fk_risks_systems')
                ->references('id')
                ->on('systems');

            $table->foreign('process_id', 'fk_risks_processes')
                ->references('id')
                ->on('processes');

            $table->foreign('risk_type_id', 'fk_risks_risks_types')
                ->references('id')
                ->on('risks_types');

            $table->foreign('risk_likelihood_id', 'fk_risks_risks_likelihoods')
                ->references('id')
                ->on('risks_likelihoods');

            $table->foreign('risk_consequence_id', 'fk_risks_risks_consequences')
                ->references('id')
                ->on('risks_consequences');

            $table->foreign('risk_level_id', 'fk_risks_risks_levels')
                ->references('id')
                ->on('risks_levels');

            $table->foreign('created_by', 'fk_risks_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_risks_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('risks');
    }
}
