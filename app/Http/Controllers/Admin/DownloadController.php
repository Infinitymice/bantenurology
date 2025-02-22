<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Yajra\DataTables\Facades\DataTables;


class DownloadController extends Controller
{
    public function index()
    {
        $data = Payment::with('registrasi:id,full_name,nik')
            ->select('id', 'registrasi_id', 'qr_code')
            ->get();

        return view('admin..downloadqr.download-qr', compact('data'));

    }
    public function downloadQRCode($id)
    {
        $payment = Payment::findOrFail($id);

        if (!$payment->qr_code) {
            return redirect()->route('admin.download.index')->with('error', 'QR Code tidak ditemukan.');
        }

        // Ambil nama peserta (full_name) yang terkait dengan payment
        $fullName = $payment->registrasi->full_name ?? 'Unnamed Participant';
        // Sanitasi nama peserta agar tidak mengandung karakter yang tidak valid untuk nama file
        $sanitizedFullName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fullName);

        // Ambil file name tanpa folder qr-codes/
        $qrCodeFile = basename($payment->qr_code);

        // Menggunakan base_path untuk mengarah ke folder app/storage/qr-codes/
        $path = base_path('public/storage/qr-codes/' . $qrCodeFile);

        if (!file_exists($path)) {
            return redirect()->route('admin.download.index')->with('error', 'File QR Code tidak ditemukan.');
        }

        // Mengubah nama file download menjadi nama peserta
        $downloadFileName = $sanitizedFullName . '-' . $payment->id . '.png';

        return response()->download($path, $downloadFileName);
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
    public function getData()
    {
        $data = Payment::with('registrasi:id,full_name,nik')
            ->select('id', 'registrasi_id', 'qr_code')
            ->whereIn('status', ['completed','unpaid']) 
            ->orderBy('created_at', 'desc') // Urutkan berdasarkan ID secara menurun
            ->get();


        return DataTables::of($data)
            ->addColumn('name', function ($row) {
                return $row->registrasi->full_name ?? '-';
            })
            ->addColumn('nik', function ($row) {
                return $row->registrasi->nik ?? '-';
            })
            ->addColumn('qr_code', function ($row) {
                return basename($row->qr_code); // Hanya kirim nama file QR Code
            })
            ->addColumn('action', function ($row) {
                $downloadUrl = route('admin.download.qr-code', $row->id);
                $printAction = "printQrCode('qrCode{$row->id}', 'name{$row->id}')";

                return '
                    <a href="' . $downloadUrl . '" class="btn btn-primary btn-sm">Download</a>
                    <button class="btn btn-success btn-sm" onclick="' . $printAction . '">Cetak</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

}
