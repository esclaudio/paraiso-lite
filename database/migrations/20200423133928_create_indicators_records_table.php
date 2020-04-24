<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIndicatorsRecordsTable extends Migration
{
    public function up()
    {
        $this->schema->create('indicators_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('indicator_id');
            $table->float('value', 0);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('indicator_id', 'fk_indicators_records_indicator')
                ->references('id')
                ->on('indicators')
                ->onDelete('cascade');

            $table->foreign('created_by', 'fk_indicators_records_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_indicators_records_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('indicators_records');
    }
}
