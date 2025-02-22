<?php


namespace App\Imports;

use App\Models\Registrasi;
use App\Models\Payment;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class ParticipantsImport implements ToModel
{
    public function model(array $row)
    {
        // Simpan data ke tabel registrasi
        $registrasi = Registrasi::create([
            'full_name' => $row[1], // Ubah menjadi indeks yang sesuai
            'nik' => $row[2],
            'institusi' => $row[5],
            'email' => $row[4],
            'phone' => $row[3],
            'category' => $row[6],
        ]);
        

        // Tentukan jumlah pembayaran berdasarkan kategori
        $amount = 0;
        switch ($row[7]) {
            case 'GENERAL PRACTITIONER/RESIDENT':
                $amount = 1500000;
                break;
            case 'GP / PARAMEDIC, HEALTHCARE PROFESSIONALS ( Symposium Only )':
                $amount = 1500000;
                break;
            case 'Overseas Participant ( Spesialist )':
                $amount = 1500000;
                break;
            case 'SPECIALIST':
                $amount = 3750000;
                break;
            default:
                $amount = 0; // Nilai default jika kategori tidak dikenali
        }

        // Simpan data ke tabel payment
        $payment = Payment::create([
            'registrasi_id' => $registrasi->id,
            'invoice_number' => 'INV-' . strtoupper(uniqid()), // Generate nomor invoice unik
            'amount' => $amount,
            'status' => 'pending',
        ]);

        // Generate nomor tiket jika belum ada
        if (empty($payment->ticket_number)) {
            // Generate nomor tiket
            $ticketNumber = $this->generateTicketNumber();
            $payment->ticket_number = $ticketNumber;

            // Generate QR Code untuk tiket
            try {
                $qrCode = new QrCode($ticketNumber);
                $qrCode->setSize(100)
                    ->setMargin(10)
                    ->setBackgroundColor(new \Endroid\QrCode\Color\Color(255, 255, 255, 0));
                $writer = new PngWriter();

                // Path tempat QR code disimpan
                $qrCodePath = public_path('qr-codes/' . $ticketNumber . '.png');

                // Menulis QR Code ke dalam file gambar PNG
                $writer->write($qrCode)->saveToFile($qrCodePath);

                // Simpan path file QR Code ke dalam database
                $payment->qr_code = 'qr-codes/' . $ticketNumber . '.png';
                $payment->save();
            } catch (\Exception $e) {
                // Handle error jika QR code gagal dibuat
                \Log::error('QR Code Generation Error: ' . $e->getMessage());
            }
        }

        return $registrasi;
    }

    /**
     * Generate nomor tiket unik.
     */
    public function generateTicketNumber()
    {
        do {
            // Ambil tanggal saat ini
            $date = Carbon::now()->format('ymd');

            // Ambil urutan nomor tiket berdasarkan tanggal
            $latestTicket = Payment::where('ticket_number', 'like', 'T' . $date . '%')
                ->orderBy('created_at', 'desc')
                ->first();

            // Tentukan urutan tiket berikutnya
            $ticketNumber = 1;
            if ($latestTicket) {
                $lastTicketNumber = (int) substr($latestTicket->ticket_number, -4);
                $ticketNumber = $lastTicketNumber + 1;
            }

            // Format nomor tiket
            $generatedTicketNumber = 'T' . $date . str_pad($ticketNumber, 4, '0', STR_PAD_LEFT);

            // Periksa apakah nomor tiket sudah ada di database
        } while (Payment::where('ticket_number', $generatedTicketNumber)->exists());

        return $generatedTicketNumber;
    }
}
