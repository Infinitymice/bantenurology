<?php

namespace App\Jobs;

use App\Models\Payment;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateQrCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payment;

    /**
     * Create a new job instance.
     *
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!empty($this->payment->qr_code)) {
            \Log::info('QR code already exists for payment ID:', ['payment_id' => $this->payment->id]);
            return;
        }
    
        try {
            $ticketNumber = $this->payment->ticket_number;
            $qrCodePath = 'qr-codes/' . $ticketNumber . '.png';
    
            // Generate QR Code
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($ticketNumber)
                ->size(100)
                ->margin(10)
                ->build();
    
            // Save QR Code
            Storage::disk('public')->put($qrCodePath, $result->getString());
    
            $this->payment->qr_code = $qrCodePath;
            $this->payment->save();
    
            \Log::info('QR Code generated successfully for payment ID:', ['payment_id' => $this->payment->id]);
        } catch (\Exception $e) {
            \Log::error('Error generating QR Code:', ['error' => $e->getMessage()]);
        }
    }
    
}
