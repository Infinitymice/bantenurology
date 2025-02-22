<?php

namespace App\Jobs;

use App\Mail\PaymentCompleted;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;


class SendPaymentCompletedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payment;
    public $backgroundImage;

    // Konstruktor untuk menerima Payment
    public function __construct(Payment $payment,$emailData)
    {
        $this->payment = $payment;
        $this->backgroundImage = $emailData['backgroundImage']; // Menyimpan Base64 gambar
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $emailData = [
                'ticketNumber' => $this->payment->ticket_number,
                'full_name' => $this->payment->registrasi->full_name,
                'eventDetails' => $this->payment->registrasi->events->map(function ($event) {
                    return $event->name . ' - ' . $event->eventType->name;
                }),
                'totalPayment' => $this->payment->amount,
                'backgroundImage' => $this->backgroundImage,
                'accommodations' => $this->payment->registrasi->accommodations->map(function ($accommodation) {
                    return [
                        'name' => $accommodation->name,
                        'quantity' => $accommodation->pivot->quantity,
                        'check_in' => Carbon::parse($accommodation->pivot->check_in_date)->format('d F Y'),
                        'check_out' => Carbon::parse($accommodation->pivot->check_out_date)->format('d F Y'),
                    ];
                })->toArray(),
            ];
    
            Mail::to($this->payment->registrasi->email)
                ->send(new PaymentCompleted($this->payment, $emailData));
            \Log::info('Email berhasil dikirim ke peserta.', ['participant_email' => $this->payment->registrasi->email]);
        } catch (\Exception $e) {
            \Log::error('Error sending email: ' . $e->getMessage());
        }
    }
}
