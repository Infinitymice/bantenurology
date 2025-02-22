<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentUnpaid;

class SendUnpaidEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function handle()
    {
        try {
            Mail::to($this->payment->registrasi->email)
                ->send(new PaymentUnpaid($this->payment));
                
            \Log::info('Unpaid email sent successfully to:', ['email' => $this->payment->registrasi->email]);
        } catch (\Exception $e) {
            \Log::error('Error sending unpaid email: ' . $e->getMessage());
        }
    }
}
