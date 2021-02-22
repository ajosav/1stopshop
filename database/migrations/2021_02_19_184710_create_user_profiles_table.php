<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('encodedKey')->unique()->index()->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('tax_identification_no')->nullable();
            $table->string('identification_type')->nullable();
            $table->string('identity_number')->nullable();
            $table->string('professional_skill')->nullable();
            $table->string('specialization')->nullable();
            $table->integer('experience_years')->nullable();
            $table->text('service_area')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->boolean('isVerified')->default(false);
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
        Schema::dropIfExists('user_profiles');
    }
}
