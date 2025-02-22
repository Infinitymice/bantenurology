<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Payment;

class PayLaterNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $paymentDetails;

    // Mengubah konstruktor untuk menerima objek yang berisi invoice_number, amount, dan status
    public function __construct($paymentDetails)
    {
        $this->paymentDetails = $paymentDetails;
    }

    public function build()
    {
        // Mengirimkan data ke view email dengan penyesuaian data
        return $this->view('emails.PayLater')
                    ->subject('Payment Deferred Confirmation')
                    ->with([
                        'invoiceNumber' => $this->paymentDetails->invoice_number,
                        'amount' => $this->paymentDetails->amount,
                        'status' => $this->paymentDetails->status,
                        'paymentExpiry' => $this->paymentDetails->payment_expiry,
                    ]);
    }
}
