<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Services\ThermalPrinterServices;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    protected $thermalPrinter;

    public function __construct(ThermalPrinterServices $thermalPrinter)
    {
        $this->thermalPrinter = $thermalPrinter;
    }

    public function printQRCode(Request $request)
    {
        $qrCodeUrl = $request->input('qr_code_url');

        try {
            $this->thermalPrinter->printQRCode($qrCodeUrl);  // Perbaiki pemanggilan fungsi

            return response()->json(['status' => 'success', 'message' => 'QR Code berhasil dicetak!']);
        } catch (\Exception $e) {
            Log::error("Error mencetak QR Code: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal mencetak QR Code.']);
        }
    }
}
