<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Payment;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payment;

    // Terima parameter Payment ke dalam job
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    // Logika untuk generate invoice PDF
    public function handle()
    {
        $payment = $this->payment;

        // Inisialisasi mPDF
        $mpdf = new \Mpdf\Mpdf([
            'useExternalLinks' => true,
            'useGraphics' => true,
            'tempDir' => storage_path('app/public/temp'),
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 20,
            'margin_footer' => 0
        ]);
        
        // Enable image processing
        $mpdf->showImageErrors = true;
        $mpdf->SetTitle('Invoice');

        // HTML untuk invoice
        $html = '
            <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 0;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 20px !important;
                        padding-bottom: 20px !important;
                        padding: 0;
                        position: relative;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 20;
                    }
                    .header img {
                        display: block;
                        margin-bottom: 20px;
                        padding-bottom: 20px !important;
                        width: 100%;
                    }
                    .footer {
                        margin: 0;
                        padding: 0;
                        text-align: center;
                        position: fixed;
                        bottom: 0;
                        left: 0;
                        right: 0;
                    }
                    .footer img {
                        display: block;
                        margin: 0;
                        padding: 0;
                        width: 100%;
                    }
                    .content {
                        padding: 20px;
                    }
                    
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    table, th, td {
                        border: 1px solid #ddd;
                    }
                    th, td {
                        padding: 8px;
                        text-align: left;
                    }
                </style>
            </head>
            <body>
                <!-- Header -->
                <div class="header">
                    <img src="' . public_path('logo/Kop Surat ATAS.jpg') . '" alt="Logo" style="width: 100%; height: auto;">
                </div>

                <!-- Content -->
                <h2 style="text-align: center">RECEIPT</h2>
                <div class="content">
                    <h3>Event:</h3>
                    <p><b>2nd BANTEN UROLOGY SYMPOSIUM</b><br>
                    Symposium | Workshop | Exhibition <br>
                    8<sup>th</sup>-11<sup>th</sup> May 2025<br>
                    Episode Hotel, Tangerang</p>

                    <table border: "0px !important" cellpadding="5" cellspacing="0">
                        <tr border: "0px !important">
                            <td><b>Invoice ID:</b> ' . $payment->invoice_number . '</td>
                            <td><b>Status:</b> <span style="color:red;">UNPAID</span></td>
                        </tr>
                        <tr border: "0px !important">
                            <td><b>Date:</b> ' . now()->format('d/m/Y') . '</td>
                            <td><b>Billing to:</b> ' . $payment->registrasi->full_name . '</td>
                        </tr>
                    </table>

                    <h3>Details</h3>
                    <table cellpadding="5" cellspacing="0">
                        <tr style="background-color:#eee;">
                            <th>No</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Amount</th>
                        </tr>';

                        // Event details
                        $counter = 1;
                        foreach ($payment->registrasi->events as $event) {
                            $today = now()->format('Y-m-d');

                            if ($today <= $event->early_bid_date) {
                                $price = $event->early_bid_price;
                            } else {
                                $price = $event->onsite_price;
                            }

                            $html .= '
                            <tr>
                                <td>' . $counter++ . '</td>
                                <td>' . $event->eventType->name . ' - ' . $event->name . '</td>
                                <td>1</td>
                                <td>Rp ' . number_format($price, 0, ',', '.') . '</td>
                                <td>Rp ' . number_format($price, 0, ',', '.') . '</td>
                            </tr>';
                        }

                        // Add accommodation details if exists
                        if ($payment->registrasi->accommodations->count() > 0) {
                            foreach ($payment->registrasi->accommodations as $accommodation) {
                                $accommodationTotal = $accommodation->pivot->quantity * $accommodation->pivot->total_price;
                                $html .= '
                                <tr>
                                    <td>' . $counter++ . '</td>
                                    <td>' . $accommodation->name . '<br>
                                        <small>Check-in: ' . date('d/m/Y', strtotime($accommodation->pivot->check_in_date)) . '<br>
                                        Check-out: ' . date('d/m/Y', strtotime($accommodation->pivot->check_out_date)) . '</small>
                                    </td>
                                    <td>' . $accommodation->pivot->quantity . '</td>
                                    <td>Rp ' . number_format($accommodation->pivot->total_price, 0, ',', '.') . '</td>
                                    <td>Rp ' . number_format($accommodationTotal, 0, ',', '.') . '</td>
                                </tr>';
                            }
                        }

                        // Totals
                        $html .= '
                        <tr>
                            <td colspan="4" align="right"><b>Amount</b></td>
                            <td>Rp ' . number_format($payment->amount, 0, ',', '.') . '</td>
                        </tr>
                        <tr>
                            <td colspan="4" align="right"><b>Admin Fee</b></td>
                            <td>Rp 0</td>
                        </tr>
                        <tr>
                            <td colspan="4" align="right"><b>Grand Total</b></td>
                            <td>Rp ' . number_format($payment->amount, 0, ',', '.') . '</td>
                        </tr>
                    </table>

                    <div style="text-align:center; margin-top: 100px;">
                        <p>If you have any questions, contact us at <b>bantenurologysymposium@gmail.com</b> or +62 811-2694-088</p>
                        <p style="margin-top: 50px;"><b>THANK YOU FOR YOUR REGISTRATION</b></p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="footer">
                    <img src="' . public_path('logo/Kop Surat BAWAH.jpg') . '" alt="Logo" style="width: auto; height: auto;">
                </div>
            </body>
            </html>
        ';

        // Tambahkan HTML ke PDF
        $mpdf->WriteHTML($html);

        
        // Perbaikan:
        if (!file_exists(storage_path('app/public/invoices'))) {
            mkdir(storage_path('app/public/invoices'), 0755, true);
        }
    
        // Pastikan direktori exists
        if (!file_exists(storage_path('app/public/invoices'))) {
            mkdir(storage_path('app/public/invoices'), 0755, true);
        }
    
        // Simpan PDF dengan nama yang konsisten
        $pdfPath = storage_path('app/public/invoices/' . $payment->invoice_number . '.pdf');
        $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);
    
        // Update database dengan path yang benar
        $payment->invoice_url = 'invoices/' . $payment->invoice_number . '.pdf';
        $payment->save();
    
        Log::info('Invoice generated:', [
            'payment_id' => $payment->id,
            'file_path' => $pdfPath,
            'url_saved' => $payment->invoice_url
        ]);

        // Buat symlink agar bisa diakses dari browser
        if (!file_exists(public_path('storage/invoices'))) {
            \Artisan::call('storage:link');
        }

        // Simpan path ke database
        $invoicePath = 'invoices/' . $payment->invoice_number . '.pdf';
        
        Log::info('Invoice generated for payment ID:', ['payment_id' => $payment->id, 'path' => $pdfPath]);

        // Update URL invoice di database
        $payment->invoice_url = $invoicePath;
        $payment->save();
    }
}
