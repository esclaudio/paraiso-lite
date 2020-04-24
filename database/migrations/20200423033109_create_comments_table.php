<?php

use App\Support\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommentsTable extends Migration
{
    public function up()
    {
        $this->schema->create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('message');
            $table->morphs('model');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by', 'fk_comments_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_comments_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('comments');
    }
}
