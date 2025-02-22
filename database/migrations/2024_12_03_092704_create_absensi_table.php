<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('registrasi_id')
                  ->constrained('registrasi')  
                  ->onDelete('cascade');  
            $table->foreignId('event_id')  
                  ->nullable() 
                  ->constrained('events') 
                  ->onDelete('set null'); 
            $table->string('status_absen');  
            $table->timestamp('waktu_absen')->nullable();  
            $table->integer('event_day')->nullable();
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensi');
    }

}
