<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentsProcessesTable extends Migration
{
    public function up()
    {
        $this->schema->create('documents_processes', function (Blueprint $table) {
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('process_id');

            $table->foreign('document_id', 'fk_documents_processes_documents')
                ->references('id')
                ->on('documents')
                ->onDelete('cascade');

            $table->foreign('process_id', 'fk_documents_processes_processes')
                ->references('id')
                ->on('processes');

            $table->primary(['document_id', 'process_id']);
        });
    }

    public function down()
    {
        $this->schema->drop('documents_processes');
    }
}
