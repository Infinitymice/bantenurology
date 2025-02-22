<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\Payment;
use App\Models\Registrasi;

class FinishRegistration extends Mailable
{
    public $payment;

   
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function build()
    {
        return $this->view('emails.FinishRegistration')
                    ->with([
                        'invoice_number' => $this->payment->invoice_number,
                        'amount' => $this->payment->amount,
                    ]);
    }
}
