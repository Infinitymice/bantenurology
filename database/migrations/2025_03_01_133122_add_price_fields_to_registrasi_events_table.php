<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceFieldsToRegistrasiEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registrasi_events', function (Blueprint $table) {
            $table->decimal('original_price', 12, 2)->after('event_id')->nullable();
            $table->decimal('final_price', 12, 2)->nullable();
            $table->string('discount_type')->nullable(); // 'early_bird', 'voucher', 'group'
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->string('discount_code')->nullable(); // untuk menyimpan kode voucher jika ada
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registrasi_events', function (Blueprint $table) {
            $table->dropColumn([
                'original_price',
                'final_price',
                'discount_type',
                'discount_percentage',
                'discount_code'
            ]);
        });
    }
}