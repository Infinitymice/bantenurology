<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Mail\PaymentCanceledNotification;
use Illuminate\Support\Facades\Mail;

class CheckExpiredPayments extends Command
{
    protected $signature = 'payments:check-expired';
    protected $description = 'Check for expired payments and update their status to canceled, and restore event quota';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Mendapatkan pembayaran yang kedaluwarsa
        $expiredPayments = Payment::where('status', 'pending')
            ->where('payment_expiry', '<', Carbon::now())
            ->get();

            foreach ($expiredPayments as $payment) {
                // Ubah status
                $payment->status = 'canceled';
                $payment->save();
            
                // Periksa apakah ada data registrasi dan event
                if ($payment->registrasi && $payment->registrasi->event) {
                    $event = $payment->registrasi->event;
                    $event->kuota += 1;
                    $event->save();

                    $updatedEvent = Event::find($event->id);
                    \Log::info("Event quota after saving: {$updatedEvent->kuota}");
            
                    // Kirim email kepada peserta
                    Mail::to($payment->registrasi->email)->send(new PaymentCanceledNotification($payment));
            
                    $this->info("Payment {$payment->invoice_number} has expired, status changed to canceled, and event quota restored.");
                } else {
                    $this->error("No registrasi or event found for payment {$payment->invoice_number}");
                    \Log::info("Payment details: ", ['payment' => $payment]);
                }
            }
            
            
    }
}
