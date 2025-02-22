<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\Registrasi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentFailedByAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Payment Failure Notification')
                    ->view('emails.PaymentFailedByAdmin')
                    ->with([
                        'name' => $this->payment->registrasi->full_name,
                        'invoiceNumber' => $this->payment->invoice_number,
                        'failedReason' => $this->payment->failed_reason,
                    ]);
    }
}
