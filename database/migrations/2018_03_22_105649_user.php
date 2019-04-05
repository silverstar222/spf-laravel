<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class User extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('users', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('email');
            $table->text('company_name');
            $table->string('password');
            $table->string('temporary_password')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('business_name')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('manager_name')->nullable();
            $table->string('permissions')->nullable();
            $table->string('customer_stripe_id')->nullable();
            $table->integer('is_active')->default('1');
            $table->integer('is_new')->default('1');
            $table->integer('is_paid')->default('0');
            $table->integer('created_at');
            $table->integer('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
