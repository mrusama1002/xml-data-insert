<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stays', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('GroupId')->nullable();
            $table->bigInteger('SourceId')->nullable();
            $table->bigInteger('AccommodationId')->nullable();
            $table->bigInteger('ConfNumber')->nullable();
            $table->bigInteger('RoomRevenue')->nullable();
            $table->bigInteger('FBRevenue')->nullable();
            $table->bigInteger('TotalRevenue')->nullable();
            $table->string('OriginCode')->nullable();
            $table->string('CompanyName')->nullable();
            $table->string('CurrencyCode')->nullable();
            $table->string('DepartureDate')->nullable();
            $table->string('GuestNameID')->nullable();
            $table->string('MarketCode')->nullable();
            $table->string('PaymentMethod')->nullable();
            $table->string('Resort')->nullable();
            $table->string('RateCode')->nullable();
            $table->string('InsertDate')->nullable();
            $table->string('UpdateDate')->nullable();
            $table->string('RoomNo')->nullable();
            $table->string('RoomType')->nullable();
            $table->string('Custom1')->nullable();
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
        Schema::dropIfExists('stays');
    }
}
