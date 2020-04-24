<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNonconformitiesTable extends Migration
{
    public function up()
    {
        $this->schema->create('nonconformities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('system_id');
            $table->unsignedBigInteger('process_id');
            $table->text('description');
            $table->date('occurrence_date');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->text('five_why')->nullable();
            $table->text('root_cause')->nullable();
            // TODO: Effectiveness
            $table->enum('status', ['draft', 'analyzed', 'implementing', '', 'effective', 'ineffective'])->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('system_id', 'fk_nonconformities_systems')
                ->references('id')
                ->on('systems');

            $table->foreign('process_id', 'fk_nonconformities_processs')
                ->references('id')
                ->on('processes');

            $table->foreign('product_id', 'fk_nonconformities_products')
                ->references('id')
                ->on('products');

            $table->foreign('customer_id', 'fk_nonconformities_customers')
                ->references('id')
                ->on('customers');

            $table->foreign('created_by', 'fk_nonconformities_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_nonconformities_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('nonconformities');
    }
}
