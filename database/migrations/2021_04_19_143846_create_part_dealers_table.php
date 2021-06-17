<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartDealersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_dealers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('encodedKey')->unique()->index()->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->string('shop_name')->nullable();
            $table->string('tax_identification_no')->nullable();
            $table->string('identification_type')->nullable();
            $table->string('identity_number')->nullable();
            $table->text('office_address')->nullable();
            $table->text('shop_description')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('company_photo')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('part_dealers');
    }
}
