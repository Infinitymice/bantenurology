<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenisAbsensiToAbsensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->string('jenis_absensi')->nullable(); 
        });
    }
    
    public function down()
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn('jenis_absensi'); 
        });
    }
    
}
