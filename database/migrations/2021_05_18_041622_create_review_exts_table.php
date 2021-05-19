<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewExtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('review_exts', function (Blueprint $table) {
            $table->id();
            $table->string('display_name')->nullable();
            $table->text('review_photo')->nullable();
            $table->unsignedBigInteger('imageable_id')->nullable();   
            $table->string('imageable_type')->nullable();   
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
        Schema::dropIfExists('review_exts');
    }
}
