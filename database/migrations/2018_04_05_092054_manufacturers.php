<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Manufacturers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('admins_id');
            $table->text('company_name');
            $table->string('logo')->nullable();
            $table->string('location');
            $table->text('website')->nullable();
            $table->text('merchant_id')->nullable();
            $table->text('stripe_acc_id')->nullable();
            $table->string('dop_amount')->nullable();
            $table->integer('is_active')->default('1');
            $table->integer('is_slider')->default('1');
            $table->integer('created_at');
            $table->integer('updated_at');
            $table->foreign('admins_id')->references('id')->on('admins')->onDelete('cascade')->onTruncate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manufacturers');
    }
}
