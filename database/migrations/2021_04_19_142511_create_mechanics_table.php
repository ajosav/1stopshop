<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMechanicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mechanics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('encodedKey')->unique()->index()->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->string('shop_name')->nullable();
            $table->string('tax_identification_no')->nullable();
            $table->string('identification_type')->nullable();
            $table->string('identity_number')->nullable();
            $table->longText('professional_skill')->nullable();
            $table->longText('vehicle_type')->nullable();
            // $table->longText('specialization')->nullable();
            $table->integer('experience_years')->nullable();
            // $table->text('service_area')->nullable();
            $table->text('office_address')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('company_photo')->nullable();
            $table->text('shop_description')->nullable();
            $table->text('working_hours')->nullable();
            $table->text('working_hours_data')->nullable();
            $table->text('  ')->nullable();
            $table->text('vehicle_type_data')->nullable();
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
        Schema::dropIfExists('mechanic_details');
    }
}
