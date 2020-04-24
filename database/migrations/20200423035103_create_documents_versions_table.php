<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentsVersionsTable extends Migration
{
    public function up()
    {
        $this->schema->create('documents_versions', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->unsignedBigInteger('document_id');
            $table->string('version');
            $table->text('changes');
            $table->string('file_extension')->nullable();
            $table->string('file_mimetype')->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->enum('status', ['draft', 'toreview', 'toapprove', 'published', 'rejected', 'archived'])->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->foreign('document_id', 'fk_documents_versions_document')
                ->references('id')
                ->on('documents')
                ->onDelete('cascade');
            
            $table->foreign('created_by', 'fk_documents_versions_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_documents_versions_updated_by')
                ->references('id')
                ->on('users');

            $table->foreign('reviewed_by', 'fk_documents_versions_reviewed_by')
                ->references('id')
                ->on('users');

            $table->foreign('approved_by', 'fk_documents_versions_approved_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('documents_versions');
    }
}
