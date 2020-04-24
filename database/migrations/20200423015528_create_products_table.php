<?php

use App\Support\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $this->schema->create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique('ux_products_code');
            $table->string('description');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by', 'fk_products_created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by', 'fk_products_updated_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        $this->schema->drop('products');
    }
}
