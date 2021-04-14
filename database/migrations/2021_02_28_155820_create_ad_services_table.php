<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ad_product_type')->nullable();
            $table->string('encodedKey');
            $table->string('product_title');
            $table->string('product_no')->unique();
            $table->string('keyword')->nullable()->index();
            $table->string('condition')->nullable();
            $table->string('year')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('warranty')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price', 50, 2)->nullable();
            $table->tinyInteger('negotiable')->default(0);
            $table->text('product_photo')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_services');
    }
}
