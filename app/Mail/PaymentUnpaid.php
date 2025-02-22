<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Payment;
use App\Models\Registrasi;

class PaymentUnpaid extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.PaymentUnpaid')
                    ->subject('Receipt Registration 2nd BUS 2025')
                    ->with([
                        'invoice_number' => $this->payment->invoice_number,
                        'amount' => $this->payment->amount,
                        'status' => $this->payment->status,
                    ]);
    }
}
