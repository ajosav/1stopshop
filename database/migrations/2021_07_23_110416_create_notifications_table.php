<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('action')->nullable();
            $table->unsignedInteger('notifiable_id');
            $table->string('notifiable_type');
            $table->unsignedInteger('author_id')->nullable();
            $table->string('author_type')->nullable();
            $table->unsignedInteger('owner_id')->nullable();
            $table->string('owner_type')->nullable();
            $table->enum('status', ['read', 'unread'])->default('unread');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
