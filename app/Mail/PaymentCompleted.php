<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\Registrasi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Mpdf\Mpdf;
use Carbon\Carbon;


class PaymentCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $backgroundImage;

    /**
     * Create a new message instance.
     *
     * @param Payment $payment
     * @return void
     */
    public function __construct(Payment $payment,$emailData)
    {
        $this->payment = $payment;
        $this->backgroundImage = $emailData['backgroundImage']; // Menyimpan Base64 gambar
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $accommodations = $this->payment->registrasi->accommodations->map(function ($accommodation) {
            return [
                'name' => $accommodation->name,
                'quantity' => $accommodation->pivot->quantity,
                'check_in' => Carbon::parse($accommodation->pivot->check_in_date)->format('d F Y'),
                'check_out' => Carbon::parse($accommodation->pivot->check_out_date)->format('d F Y'),
            ];
        })->toArray();
    
        $data = [
            'full_name' => $this->payment->registrasi->full_name,
            'invoiceNumber' => $this->payment->invoice_number,
            'amount' => (float) $this->payment->amount,
            'status' => $this->payment->status,
            'ticketNumber' => $this->payment->ticket_number,
            'eventDetails' => $this->payment->registrasi->events->map(function ($event, $index) {
                return ($index + 1) . ". " . $event->name . " - " . $event->eventType->name;
            })->toArray(),
            'totalPayment' => $this->payment->amount,
            'backgroundImage' => $this->backgroundImage,
            'accommodations' => $accommodations, // Add accommodations data
        ];

        // Generate PDF dari view
        $mpdf = new Mpdf([
            'orientation' => 'L',
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);

        // Generate PDF content from view
        $html = view('emails.PaymentCompleted', $data)->render();
        $mpdf->WriteHTML($html);

        // Get PDF content as string
        $pdfContent = $mpdf->Output('', 'S');

        // Build email
        return $this->subject('Your Payment Has Been Successful')
                    ->view('emails.PaymentCompleted')
                    ->with($data)
                    ->attachData($pdfContent, 'ticket-' . $this->payment->invoice_number . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
