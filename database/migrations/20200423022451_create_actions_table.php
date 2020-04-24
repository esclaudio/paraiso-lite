<?php

use App\Support\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActionsTable extends Migration
{
    public function up()
    {
        $this->schema->create('actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('model');
            $table->unsignedInteger('number');
            $table->text('description');
            $table->date('due_date');
            $table->unsignedBigInteger('responsible_id');
            $table->timestamp('completed_at')->nullable();
            $table->text('evidence')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('responsible_id', 'fk_actions_responsible_id')
                ->references('id')
                ->on('users');

            $table->foreign('created_by', 'fk_actions_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_actions_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('actions');
    }
}
