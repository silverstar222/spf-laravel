<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ManufacturersProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('manufacturers_products', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('manufacturers_id');
            $table->text('title');
            $table->string('logo')->nullable();
            $table->string('price');
            $table->text('description')->nullable();
            $table->integer('is_active')->default('1');
            $table->integer('created_at');
            $table->integer('updated_at');
            $table->foreign('manufacturers_id')->references('id')->on('manufacturers')->onDelete('cascade')->onTruncate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manufacturers_products');
    }
}
