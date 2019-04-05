<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ManufacturersProductsFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('manufacturers_products_files', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('manufacturers_products_id');
            $table->string('src');
            $table->integer('is_active')->default('1');
            $table->integer('created_at');
            $table->integer('updated_at');
            $table->foreign('manufacturers_products_id')->references('id')->on('manufacturers_products')->onDelete('cascade')->onTruncate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manufacturers_products_files');
    }
}
