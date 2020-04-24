<?php

use App\Support\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttachmentsTable extends Migration
{
    public function up()
    {
        $this->schema->create('attachments', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('name');
            $table->string('extension');
            $table->string('mimetype');
            $table->unsignedInteger('size');
            $table->morphs('model');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by', 'fk_attachments_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_attachments_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('attachments');
    }
}
