<?php

use App\Support\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentsTypesTable extends Migration
{
    public function up()
    {
        $this->schema->create('documents_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique('ux_documents_types_name');
            $table->string('prefix')->nullable()->unique('ux_documents_types_prefix');
            $table->unsignedInteger('next_number')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by', 'fk_documents_types_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_documents_types_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('documents_types');
    }
}
