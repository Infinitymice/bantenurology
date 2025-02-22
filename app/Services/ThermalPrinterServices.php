<?php 

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\EscposImage;

class ThermalPrinterServices
{
    // public function printQRCode($qrCodeUrl)
    // {
    //     // Menghubungkan ke printer
    //     $connector = new WindowsPrintConnector("PT-210_E531"); 
    //     $printer = new Printer($connector);

    //     // Mencetak teks
    //     $printer->text("QR Code Peserta\n");
        
    //     // Mencetak QR Code
    //     $printer->setStyles(['width' => 1, 'height' => 1]);
    //     $qrCodeImage = EscposImage::load($qrCodeUrl, false); 
    //     $printer->bitImage($qrCodeImage);

    //     // Menyelesaikan pencetakan
    //     $printer->cut();
    //     $printer->close();
    // }
    public function printQRCode($qrCodeUrl)
    {
        try {
            // Cek apakah URL QR Code valid
            if (empty($qrCodeUrl)) {
                throw new \Exception("QR Code URL tidak ditemukan.");
            }

            // $connector = new WindowsPrintConnector("PT-210_E531"); 
            // $printer = new Printer($connector);

            // $printer->text("QR Code Peserta\n");
            // $printer->qrCode($qrCodeUrl);
            // $printer->cut();
            // $printer->close();

            return view ('absensi/searchqr');

            Log::info("QR Code berhasil dicetak: " . $qrCodeUrl);

        } catch (\Exception $e) {
            Log::error("Gagal mencetak QR Code: " . $e->getMessage());
            throw $e;  
        }
    }
}
