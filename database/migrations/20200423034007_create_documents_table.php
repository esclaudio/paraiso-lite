<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentsTable extends Migration
{
    public function up()
    {
        $this->schema->create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('system_id');
            $table->unsignedBigInteger('process_id');
            $table->unsignedBigInteger('document_type_id');
            $table->string('code')->nullable()->unique('ux_documents_code');
            $table->string('name')->unique('ux_documents_name');
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedBigInteger('approver_id');
            $table->unsignedInteger('review_frequency');
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('reviewer_id', 'fk_documents_reviewer')
                ->references('id')
                ->on('users');

            $table->foreign('approver_id', 'fk_documents_approver')
                ->references('id')
                ->on('users');

            $table->foreign('created_by', 'fk_documents_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_documents_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('documents');
    }
}
