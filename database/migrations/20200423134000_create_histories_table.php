<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHistoriesTable extends Migration
{
    public function up()
    {
        $this->schema->create('histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('event');
            $table->text('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->index(['model_type', 'model_id'], 'ix_histories_model');

            $table->foreign('user_id', 'fk_histories_users')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('histories');
    }
}
