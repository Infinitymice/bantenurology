<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyVoucherTableMultipleDicount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            // Hapus kolom discount_percentage yang lama
            $table->dropColumn('discount_percentage');
            
            // Tambah kolom baru untuk menyimpan multiple discount
            $table->json('event_discounts')->nullable()->after('code');
        });
    }

    public function down()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->decimal('discount_percentage', 5, 2)->after('code');
            $table->dropColumn('event_discounts');
        });
    }
}
