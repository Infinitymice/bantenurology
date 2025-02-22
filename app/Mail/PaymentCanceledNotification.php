<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentCanceledNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $payment; // Menyimpan informasi pembayaran yang dibatalkan

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function build()
    {
        return $this->view('emails.PaymentCanceled') 
                    ->subject('Your Payment Has Been Canceled')
                    ->with([
                        'registrasi' => $this->payment->registrasi,
                        'invoice_number' => $this->payment->invoice_number,
                        'event_name' => $this->payment->registrasi->event->name,
                    ]);
    }
}

