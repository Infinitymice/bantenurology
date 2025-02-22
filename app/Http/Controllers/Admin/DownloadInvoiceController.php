<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DownloadInvoiceController extends Controller
{
    public function index()
    {
        // Ambil data dari tabel payments dan relasikan dengan tabel registrasi
        $data = Payment::with('registrasi:id,full_name,nik')
            ->select('id', 'registrasi_id', 'qr_code', 'invoice_url', 'invoice_number', 'amount', 'status')
            ->get();

        return view('admin.invoice.download-inv', compact('data'));
    }


    public function getData()
    {
        $payments = Payment::with('registrasi:id,full_name,nik')
            ->select('id', 'registrasi_id', 'invoice_number', 'amount', 'invoice_url', 'status')
            ->where('status', 'unpaid')
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($payments)
            ->addColumn('action', function ($payment) {
                if ($payment->invoice_url) {
                    return '<button onclick="downloadInvoice(\'' . $payment->invoice_number . '\')" class="btn btn-primary">Download</button>';
                }
                return '<span class="text-muted">Invoice Tidak Tersedia</span>';
            })
            ->addColumn('status_label', function ($payment) {
                return ucfirst($payment->status);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function download($fileName)
    {
        try {
            $payment = Payment::where('invoice_number', $fileName)->firstOrFail();
            
            // Pastikan invoice_url tidak null
            if (!$payment->invoice_url) {
                return abort(404, 'Invoice belum dibuat');
            }

            $filePath = storage_path('app/public/' . $payment->invoice_url);

            if (!file_exists($filePath)) {
                return abort(404, 'File tidak ditemukan');
            }

            return response()->download($filePath, 'invoice_' . $fileName . '.pdf', [
                'Content-Type' => 'application/pdf'
            ]);

        } catch (\Exception $e) {
            \Log::error('Download invoice error: ' . $e->getMessage());
            return abort(404, 'File tidak ditemukan');
        }
    }
}
