<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('GroupId')->nullable();
            $table->bigInteger('SourceId')->nullable();
            $table->bigInteger('AccommodationId')->nullable();
            $table->string('Property')->nullable();
            $table->bigInteger('ProfileId')->nullable();
            $table->bigInteger('MasterProfileId')->nullable();
            $table->string('ProfileTypeId')->nullable();
            $table->string('Title')->nullable();
            $table->string('FirstName')->nullable();
            $table->string('MiddleName')->nullable();
            $table->string('LastName')->nullable();
            $table->string('Gender')->nullable();
            $table->string('LanguageCode')->nullable();
            $table->string('Language')->nullable();
            $table->string('Nationality')->nullable();
            $table->string('DateOfBirth')->nullable();
            $table->string('BirthPlace')->nullable();
            $table->string('Company')->nullable();
            $table->string('Notes')->nullable();
            $table->string('Preferences')->nullable();
            $table->string('Email')->nullable();
            $table->string('PhoneNumber')->nullable();
            $table->string('City')->nullable();
            $table->string('Country')->nullable();
            $table->string('PostalCode')->nullable();
            $table->integer('StatusId')->default(1);
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
        Schema::dropIfExists('profiles');
    }
}
