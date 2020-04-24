<?php

use App\Support\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRisksAssessmentsTable extends Migration
{
    public function up()
    {
        $this->schema->create('risks_assessments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('risk_id');
            $table->unsignedBigInteger('risk_likelihood_id');
            $table->unsignedBigInteger('risk_consequence_id');
            $table->unsignedBigInteger('risk_level_id');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('risk_id', 'fk_risks_assessments_risks')
                ->references('id')
                ->on('risks')
                ->onDelete('cascade');

            $table->foreign('risk_likelihood_id', 'fk_risks_assessments_risks_likelihoods')
                ->references('id')
                ->on('risks_likelihoods');

            $table->foreign('risk_consequence_id', 'fk_risks_assessments_risks_consequences')
                ->references('id')
                ->on('risks_consequences');

            $table->foreign('risk_level_id', 'fk_risks_assessments_risks_levels')
                ->references('id')
                ->on('risks_levels');

            $table->foreign('created_by', 'fk_risks_assessments_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_risks_assessments_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('risks_assessments');
    }
}
