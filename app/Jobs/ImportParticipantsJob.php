<?php

namespace App\Jobs;

use App\Models\Registrasi;
use App\Models\Payment;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ImportParticipantsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    /**
     * Create a new job instance.
     *
     * @param string $filePath Path to the CSV file
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            if (!file_exists($this->filePath)) {
                Log::error("CSV file not found: " . $this->filePath);
                return;
            }

            $csvFile = fopen($this->filePath, 'r');
            if (!$csvFile) {
                Log::error("Failed to open CSV file: " . $this->filePath);
                return;
            }

            $headers = fgetcsv($csvFile); // Ambil header

            if (!$headers) {
                Log::error("CSV file is empty or headers are missing.");
                fclose($csvFile);
                return;
            }

            Log::info('Starting CSV import with headers: ' . json_encode($headers));

            $index = 0;
            while (($row = fgetcsv($csvFile)) !== false) {
                $index++;

                // Debug jumlah kolom
                Log::info("Row {$index} column count: " . count($row) . " | Expected: " . count($headers));

                if (count($row) !== count($headers)) {
                    Log::error("Row {$index} has mismatched columns. Headers count: " . count($headers) . ", Row count: " . count($row));
                    Log::error("Row data: " . json_encode($row));
                    continue;
                }

                $rowData = array_combine($headers, $row);

                if (!$rowData) {
                    Log::error("Failed to map row {$index} to headers.");
                    continue;
                }

                try {
                    // Validasi minimal ada nama dan email
                    if (empty($rowData['Full Name & Title']) || empty($rowData['Email'])) {
                        Log::warning("Skipping row {$index}: Missing required fields.");
                        continue;
                    }

                    // Simpan data registrasi
                    $registrasi = Registrasi::create([
                        'full_name' => trim($rowData['Full Name & Title']),
                        'nik' => trim($rowData['NIK (for Indonesian only), Passport number for Foreign Participants']),
                        'institusi' => trim($rowData['Institution']),
                        'email' => trim($rowData['Email']),
                        'phone' => trim($rowData['Phone Number (WhatsApp Number)']),
                        'category' => trim($rowData['Select Category']),
                    ]);

                    // Cek apakah ada event yang sesuai
                    if (!empty($rowData['Events'])) {
                        $event = Event::where('name', trim($rowData['Events']))->first();
                        
                        if ($event) {
                            $registrasi->events()->attach($event->id, [
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } else {
                            Log::warning("Event not found: {$rowData['Events']} for registration ID: {$registrasi->id}");
                        }
                    }

                    // Tentukan jumlah pembayaran berdasarkan kategori
                    // $amount = $this->determineAmount($registrasi->category);

                    $amount = trim($rowData['Price Final']); // Mendapatkan nilai dari rowData

                    // Menghapus simbol 'Rp' dan koma
                    $cleanAmount = str_replace(['Rp', ','], '', $amount);
                    
                    // Mengkonversi ke tipe data numerik (misalnya, float)
                    $numericAmount = (float) $cleanAmount;
                    
                    // Memasukkan data ke database
                    $payment = Payment::create([
                        'registrasi_id' => $registrasi->id,
                        'invoice_number' => 'INV-' . strtoupper(uniqid()),
                        'amount' => $numericAmount, // Menggunakan nilai numerik yang sudah dibersihkan
                        'status' => 'completed',
                    ]);
                    

                    // Buat tiket dan QR Code
                    $this->generateTicketAndQRCode($payment);

                    Log::info("Successfully processed row {$index} for participant: {$registrasi->full_name}");
                } catch (\Exception $e) {
                    Log::error("Error processing row {$index}: " . $e->getMessage());
                    continue;
                }
            }

            fclose($csvFile);
        } catch (\Exception $e) {
            Log::error("Fatal error during CSV import: " . $e->getMessage());
        }
    }

    /**
     * Generate nomor tiket dan QR code untuk pembayaran.
     */
    protected function generateTicketAndQRCode($payment)
    {
        $ticketNumber = 'T' . Carbon::now()->format('ymd') . str_pad($payment->id, 4, '0', STR_PAD_LEFT);
        $payment->ticket_number = $ticketNumber;

        // Pastikan folder QR code ada
        $qrCodeFolder = storage_path('app/public/qr-codes');
        if (!File::exists($qrCodeFolder)) {
            File::makeDirectory($qrCodeFolder, 0755, true);
        }

        // Buat QR Code
        $qrCode = new QrCode($ticketNumber);
        $qrCode->setSize(100)->setMargin(10);
        $writer = new PngWriter();

        $qrCodePath = $qrCodeFolder . '/' . $ticketNumber . '.png';
        $writer->write($qrCode)->saveToFile($qrCodePath);

        // Simpan path QR code di database
        $payment->qr_code = 'qr-codes/' . $ticketNumber . '.png';
        $payment->save();
    }
}
