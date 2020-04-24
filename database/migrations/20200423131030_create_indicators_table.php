<?php

use App\Support\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIndicatorsTable extends Migration
{
    public function up()
    {
        $this->schema->create('indicators', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('system_id');
            $table->unsignedBigInteger('process_id');
            $table->string('name')->unique('ux_indicators_name');
            $table->unsignedInteger('frequency');
            $table->unsignedBigInteger('decimals');
            $table->string('unit');
            $table->unsignedBigInteger('responsible_id');
            $table->date('start_date');
            $table->date('next_record_date');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('system_id', 'fk_indicators_systems')
                ->references('id')
                ->on('systems');

            $table->foreign('process_id', 'fk_indicators_processes')
                ->references('id')
                ->on('processes');

            $table->foreign('responsible_id', 'fk_indicators_responsible_id')
                ->references('id')
                ->on('users');

            $table->foreign('created_by', 'fk_indicators_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_indicators_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('indicators');
    }
}
