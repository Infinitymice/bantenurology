<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_type_id');
            $table->string('name'); 
            $table->decimal('early_bid_price', 10, 2)->nullable();
            $table->decimal('onsite_price', 10, 2)->nullable();
            $table->date('early_bid_date')->nullable();
            $table->date('event_date')->nullable();
            $table->date('event_date_day2')->nullable();
            $table->integer('kuota')->nullable();
            $table->timestamps();
    
            // Foreign key ke event_types
            $table->foreign('event_type_id')->references('id')->on('event_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
