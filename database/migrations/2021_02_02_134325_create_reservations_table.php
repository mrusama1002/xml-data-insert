<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('GroupId')->nullable();
            $table->bigInteger('SourceId')->nullable();
            $table->bigInteger('AccommodationId')->nullable();
            $table->string('Property')->nullable();
            $table->bigInteger('ConfNumber')->nullable();
            $table->string('ArrivalDate')->nullable();
            $table->time('ArrivalTime')->nullable();
            $table->string('DepartureDate')->nullable();
            $table->time('DepartureTime')->nullable();
            $table->string('CancellationReason')->nullable();
            $table->string('CancellationNo')->nullable();
            $table->string('CancellationDate')->nullable();
            $table->string('OriginCode')->nullable();
            $table->string('CompanyName')->nullable();
            $table->string('InsertDate')->nullable();
            $table->string('InsertUser')->nullable();
            $table->string('UpdateDate')->nullable();
            $table->string('UpdateUser')->nullable();
            $table->string('CurrencyCode')->nullable();
            $table->string('ResvStatus')->nullable();
            $table->string('GuestNameID')->nullable();
            $table->string('LastName')->nullable();
            $table->string('MarketCode')->nullable();
            $table->string('PaymentMethod')->nullable();
            $table->string('RateCode')->nullable();
            $table->string('RoomNo')->nullable();
            $table->string('RoomType')->nullable();
            $table->string('SourceCode')->nullable();
            $table->string('RateAmount')->nullable();
            $table->string('TravelAgentName')->nullable();
            $table->string('Custom1')->nullable();
            $table->longText('ReservationNotes')->nullable();
            $table->string('Custom2')->nullable();
            $table->string('Custom3')->nullable();
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
        Schema::dropIfExists('reservations');
    }
}
