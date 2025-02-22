<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_id')->constrained('registrasis');  // Relasi ke tabel Registrasi
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 15, 2); 
            $table->string('proof_of_transfer')->nullable(); // Path ke bukti transfer
            $table->timestamp('payment_expiry');
            $table->string('bank_name')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['pending','paid','completed', 'failed','canceled'])->default('pending');
            $table->text('failed_reason')->nullable();
            $table->string('ticket_number')->nullable();
            $table->text('qr_code')->nullable();
            $table->text('invoice_url')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
