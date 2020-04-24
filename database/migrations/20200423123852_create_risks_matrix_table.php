<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRisksMatrixTable extends Migration
{
    public function up()
    {
        $this->schema->create('risks_matrix', function (Blueprint $table) {
            $table->unsignedBigInteger('risk_type_id');
            $table->unsignedBigInteger('risk_likelihood_id');
            $table->unsignedBigInteger('risk_consequence_id');
            $table->unsignedBigInteger('risk_level_id');

            $table->foreign('risk_type_id', 'fk_risks_matrix_risks_types')
                ->references('id')
                ->on('risks_types');

            $table->foreign('risk_likelihood_id', 'fk_risks_matrix_risks_likelihoods')
                ->references('id')
                ->on('risks_likelihoods');

            $table->foreign('risk_consequence_id', 'fk_risks_matrix_risks_consequences')
                ->references('id')
                ->on('risks_consequences');

            $table->foreign('risk_level_id', 'fk_risks_matrix_risks_levels')
                ->references('id')
                ->on('risks_levels');

            $table->primary(['risk_type_id', 'risk_likelihood_id', 'risk_consequence_id'], 'pk_risks_matrix');
        });
    }

    public function down()
    {
        $this->schema->drop('risks_matrix');
    }
}
