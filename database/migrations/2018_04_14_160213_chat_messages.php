<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChatMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('chat_id');
            $table->string('from');
            $table->string('to');
            $table->string('from_type')->nullable();
            $table->string('to_type')->nullable();
            $table->string('type_message')->default('text');
            $table->text('message')->nullable();
            $table->integer('created_at');
            $table->integer('updated_at');
            $table->foreign('chat_id')->references('id')->on('chat')->onDelete('cascade')->onTruncate('cascade');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
}
