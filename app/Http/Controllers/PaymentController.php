<?php

namespace App\Http\Controllers;

// app/Http/Controllers/PaymentController.php
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Registrasi;
use App\Models\Event;
use Illuminate\Support\Facades\Mail;
use App\Mail\FinishRegistration;
use Illuminate\Support\Facades\Storage;


class PaymentController extends Controller
{
    public function uploadProof(Request $request, Payment $payment)
    {
        // Validasi input
        $validated = $request->validate([
            'proof_of_transfer' => 'required|file|mimes:jpg,jpeg,png|max:1048',
            'bank_name' => 'required|string|max:255',
            'payment_date' => 'required|date',
            'note' => 'nullable|string',
        ], [
            'proof_of_transfer.required' => 'The proof of transfer file is required.',
            'proof_of_transfer.file' => 'The uploaded file is not valid.',
            'proof_of_transfer.mimes' => 'The file must be an image (JPG, JPEG, PNG).',
            'proof_of_transfer.max' => 'The file size must not exceed 1 MB.',
        ]);
        

        // Cek apakah pembayaran valid
        if ($payment->status != 'pending') {
            return redirect()->route('register.transactionSuccess')->with('error', 'Invalid payment status.');
        }

        // Simpan file bukti transfer
        if ($request->hasFile('proof_of_transfer')) {
            $proofPath = $request->file('proof_of_transfer')->store('public/proof_of_transfer');
            $payment->proof_of_transfer = basename($proofPath);
        }


        $payment->bank_name = $request->input('bank_name');
        $payment->payment_date = $request->input('payment_date');
        $payment->note = $request->input('note');
        // Update status pembayaran menjadi "paid" setelah bukti diterima
        $payment->status = 'paid';
        $payment->save();

        // Kirim email notifikasi ke peserta
        $registrasi = Registrasi::find($payment->registrasi_id);
        Mail::to($registrasi->email)->send(new FinishRegistration($payment));

        $request->session()->flush();
        

        // // Redirect dengan notifikasi sukses
        return redirect()->route('register.transactionSuccess')->with('success', 'Payment proof uploaded successfully!');
    }

    public function transactionSuccess()
    {
        // Menampilkan view setelah transaksi berhasil
        return view('register.transactionSuccess');
    }
}
