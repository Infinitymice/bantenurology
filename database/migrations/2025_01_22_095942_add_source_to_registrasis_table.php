<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourceToRegistrasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registrasis', function (Blueprint $table) {
            $table->string('source')->nullable()->after('address'); // Tambahkan kolom source setelah kolom address
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registrasis', function (Blueprint $table) {
            //
        });
    }
}
