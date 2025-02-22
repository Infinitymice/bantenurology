<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrasiAccomodation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('registrasi_accommodation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_id')->constrained()->onDelete('cascade');
            $table->foreignId('accommodation_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->date('check_in_date');
            $table->date('check_out_date'); 
            $table->decimal('total_price', 10, 2); 
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
        Schema::dropIfExists('registrasi_accomodation');
    }
}
