<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomAvailabilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained();
            $table->date('date');
            $table->integer('available_rooms')->default(0);
            $table->timestamps();
            
            // Composite unique index
            $table->unique(['accommodation_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_availability');
    }
}
