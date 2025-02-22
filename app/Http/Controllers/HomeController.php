<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Mpdf\Mpdf;

class HomeController extends Controller
{
    public function index () 
    {
        return view('register.home');
    }

    public function tt() 
    {
        $data = [];
        // Membuat instance mPDF dengan konfigurasi yang diinginkan
        $mpdf = new Mpdf([
            'orientation' => 'L',  // Landscape
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);

        // Render view menjadi HTML
        $html = view('emails.PaymentCompleted', $data)->render();

        // Menulis HTML ke dalam file PDF
        $mpdf->WriteHTML($html);

        // Untuk menampilkan PDF langsung di browser
        // $mpdf->Output(); // PDF akan muncul di browser

        // Untuk mengunduh PDF
        return response()->stream(function() use ($mpdf) {
            $mpdf->Output(); // Men-stream PDF untuk diunduh
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="payment_ticket.pdf"',
        ]);
    }
}
