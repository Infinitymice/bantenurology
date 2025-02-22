<?php

namespace App\Http\Controllers\Admin;

use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use App\Models\Registrasi;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentCompleted;
use App\Mail\PaymentFailedByAdmin;
use App\Mail\PaymentUnpaid;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ParticipantsImport;
use App\Jobs\ImportParticipantsJob;
use App\Jobs\GenerateQrCode;
use App\Jobs\SendPaymentCompletedEmail;
use App\Jobs\GenerateInvoice;
use App\Jobs\SendUnpaidEmail;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File; 
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;



class ParticipantController extends Controller
{
    public function index()
    {
        // Mengambil data peserta untuk ditampilkan di view
        $participants = Registrasi::with(['payments', 'events'])->get();
        $events = \App\Models\Event::select('id', 'name', 'early_bid_price', 'onsite_price', 'early_bid_date')
        ->get()
        ->map(function ($event) {
            // Cek apakah tanggal sekarang melewati early_bid_date
            $event->current_price = now()->greaterThan($event->early_bid_date)
                ? $event->onsite_price
                : $event->early_bid_price;

            return $event;
        });
          // Mengambil semua peserta

        return view('admin.participants', compact('participants', 'events'));
    }

    public function getPaymentIndex()
    {
        // Mengambil data peserta untuk ditampilkan di view
        $participants = Registrasi::with(['payments', 'events'])->get();  // Mengambil semua peserta

        return view('admin.payments', compact('participants'));  
    }

    public function getData(Request $request)
    {
        $paymentStatus = $request->input('payment_status');
        $participants = Registrasi::with(['payments', 'events'])->orderBy('created_at', 'desc');
    
        // Filter status pembayaran
        if ($paymentStatus) {
            $participants->whereHas('payments', function ($query) use ($paymentStatus) {
                $query->where('status', $paymentStatus);
            });
        }
    
        // Export data
        if ($request->has('export_all') && $request->export_all) {
            $data = $participants->get();
    
            // Menyesuaikan struktur data ekspor seperti yang Anda inginkan
            $exportData = $data->map(function ($participant) {
                return [
                    'Nama Lengkap' => $participant->full_name,
                    'NIK' => $participant->nik,
                    'Institusi' => $participant->institusi, // Perbaiki penulisan '$participant' menjadi '$participant'
                    'Email' => $participant->email,
                    'Telepon' => $participant->phone,
                    'Kategori' => $participant->category,
                    'Alamat' => $participant->address,
                    'Akomodasi' => $participant->accommodations->map(function ($accommodation) {
                        return $accommodation->name . ' - ' . 
                            'Quantity: ' . $accommodation->pivot->quantity . ', ' .
                            'Check-in: ' . $accommodation->pivot->check_in_date . ', ' .
                            'Check-out: ' . $accommodation->pivot->check_out_date . ', ' ;
                    })->implode('<br><br>'),
                    'Nomor Invoice' => optional($participant->payments->first())->invoice_number ?? '-',
                    'Jumlah Pembayaran' => optional($participant->payments->first())->amount
                        ? number_format(optional($participant->payments->first())->amount, 2, ',', '.')
                        : '-',
                    'Status Pembayaran' => optional($participant->payments->first())->status ?? 'Belum Bayar'
                ];
            });
    
            // Kembalikan data sebagai JSON
            return response()->json($exportData);
        }
    
        // Mengembalikan data untuk DataTables
        return DataTables::of($participants)
            ->addColumn('event_type', function ($participant) {
                return $participant->events->map(function ($event) {
                    return '• ' . $event->eventType->name . ' - ' . $event->name;
                })->implode('<br>');
            })
            ->addColumn('invoice_number', function ($participant) {
                return optional($participant->payments->first())->invoice_number ?? 'Belum Ada Invoice';
            })
            ->addColumn('amount', function ($participant) {
                return optional($participant->payments->first())
                    ? number_format(optional($participant->payments->first())->amount, 2, ',', '.')
                    : '-';
            })
            ->addColumn('status', function ($participant) {
                return optional($participant->payments->first())->status ?? 'Belum Bayar';
            })
            ->addColumn('proof_of_transfer', function ($participant) {
                return optional($participant->payments->first())->proof_of_transfer ?? null;
            })
            ->addColumn('accommodation', function ($participant) {
                // Menampilkan data akomodasi
                return $participant->accommodations->map(function ($accommodation) {
                    return '• ' . $accommodation->name . 
                           '<br> Quantity: ' . $accommodation->pivot->quantity . 
                           '<br> Check-in: ' . \Carbon\Carbon::parse($accommodation->pivot->check_in_date)->format('d-m-Y') . 
                           '<br> Check-out: ' . \Carbon\Carbon::parse($accommodation->pivot->check_out_date)->format('d-m-Y') ; 
                })->implode('<br><br>'); // Menggabungkan hasil dengan <br> sebagai pemisah antar akomodasi
            })
            
            ->addColumn('actions', function ($participant) {
                return '
                    <div style="display: flex; align-items: center;">
                        <a href="'.route('admin.participants.edit', $participant->id).'" class="btn btn-sm btn-warning" style="margin-right: 5px;">Edit</a>
                        <form action="'.route('admin.participants.delete', $participant->id).'" method="POST" style="display: inline-block;">
                            '.csrf_field().method_field('DELETE').'
                            <button class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                        </form>
                    </div>
                ';
            })
            ->rawColumns(['event_type','accommodation','actions'])
            ->make(true); 
    }
    

    
    public function getDataPayments(Request $request)
    {
        // Ambil filter status pembayaran dan source dari request
        $paymentStatus = $request->input('payment_status');
        $source = $request->input('source');

        // Query dasar peserta dengan relasi 'payments' dan 'events'
        $participants = Registrasi::with(['payments', 'events'])
            ->orderBy('created_at', 'desc');

        // Jika ada filter status pembayaran, tambahkan kondisi filter
        if ($paymentStatus) {
            $participants = $participants->whereHas('payments', function ($query) use ($paymentStatus) {
                $query->where('status', $paymentStatus);
            });
        }

        // Filter source
        if ($source) {
            // Normalisasi nilai filter
            $normalizedSource = strtolower(str_replace(' ', '', $source));
            $participants = $participants->whereRaw("LOWER(REPLACE(source, ' ', '')) = ?", [$normalizedSource]);
        }

        // Debug query sebelum ekspor
        if ($request->has('export_all') && $request->export_all) {
            \Log::info('Export Query:', [$participants->toSql(), $participants->getBindings()]);
            
            // Ambil data yang difilter
            $data = $participants->get();

            // Siapkan data ekspor
            $exportData = $data->map(function ($participant) {
                return [
                    'Nama Lengkap' => $participant->full_name,
                    'NIK' => $participant->nik,
                    'Email' => $participant->email,
                    'Kategori' => $participant->category,
                    'Telepon' => $participant->phone,
                    'Jenis Event' => $participant->events->map(function ($event) {
                        return $event->eventType->name . ' - ' . $event->name;
                    })->implode(', '),
                    'Akomodasi' => $participant->accommodations->map(function ($accommodation) {
                        return $accommodation->name . ' - ' . 
                            'Quantity: ' . $accommodation->pivot->quantity . ', ' .
                            'Check-in: ' . $accommodation->pivot->check_in_date . ', ' .
                            'Check-out: ' . $accommodation->pivot->check_out_date . ', ' ;
                    })->implode('<br><br>'),
                    'Nomor Invoice' => optional($participant->payments->first())->invoice_number ?? '-',
                    'Batas Pembayaran' => optional($participant->payments->first())->payment_expiry 
                        ? \Carbon\Carbon::parse(optional($participant->payments->first())->payment_expiry)->format('d-m-Y H:i:s')
                        : '-',
                    // 'Batas Pembayaran' => optional($participant->payment->first())->payment_expiry   
                    //     ? \Carbon\Carbon::parse(optional($participant->payments->first())->payment_expiry)->format('d-m-Y')
                    //     : '-',
                    'Jumlah Pembayaran' => optional($participant->payments->first())->amount
                        ? number_format(optional($participant->payments->first())->amount, 2, ',', '.')
                        : '-',
                    'Status Pembayaran' => optional($participant->payments->first())->status ?? 'Belum Bayar',
                    'Bank Pembayaran' => optional($participant->payments->first())->bank_name ?? '-',
                    'Tanggal Pembayaran' => optional($participant->payments->first())->payment_date
                        ? \Carbon\Carbon::parse(optional($participant->payments->first())->payment_date)->format('d-m-Y')
                        : '-',
                    'Catatan' => optional($participant->payments->first())->note ?? '-',
                ];
            });

            // Kembalikan data sebagai JSON
            return response()->json($exportData);
        }

        // Mengembalikan data untuk DataTables
        return DataTables::of($participants)
            ->addColumn('event_type', function ($participant) {
                return $participant->events->map(function ($event) {
                    return '• ' . $event->eventType->name . ' - ' . $event->name;
                })->implode('<br>');
            })
            ->addColumn('accommodation', function ($participant) {
                // Menampilkan data akomodasi
                return $participant->accommodations->map(function ($accommodation) {
                    return '• ' . $accommodation->name . 
                           '<br> Quantity: ' . $accommodation->pivot->quantity . 
                           '<br> Check-in: ' . \Carbon\Carbon::parse($accommodation->pivot->check_in_date)->format('d-m-Y') . 
                           '<br> Check-out: ' . \Carbon\Carbon::parse($accommodation->pivot->check_out_date)->format('d-m-Y') ; 
                })->implode('<br><br>'); // Menggabungkan hasil dengan <br> sebagai pemisah antar akomodasi
            })
            ->addColumn('invoice_number', function ($participant) {
                return optional($participant->payments->first())->invoice_number ?? 'Belum Ada Invoice';
            })
            ->addColumn('payment_expiry', function ($participant) {
                $paymentExpiry = optional($participant->payments->first())->payment_expiry;
                return $paymentExpiry 
                    ? \Carbon\Carbon::parse($paymentExpiry)->format('d-m-Y H:i:s')  // Format dengan tanggal + jam
                    : 'Belum ada pembayaran';
            })            
            ->addColumn('amount', function ($participant) {
                return optional($participant->payments->first())
                    ? number_format(optional($participant->payments->first())->amount, 2, ',', '.')
                    : '-';
            })
            ->addColumn('status', function ($participant) {
                return optional($participant->payments->first())->status ?? 'Belum Bayar';
            })
            ->addColumn('proof_of_transfer', function ($participant) {
                return optional($participant->payments->first())->proof_of_transfer ?? null;
            })
            ->addColumn('bank_name', function ($participant) {
                return optional($participant->payments->first())->bank_name ?? 'Belum ada pembayaran';
            })
            ->addColumn('payment_date', function ($participant) {
                $paymentDate = optional($participant->payments->first())->payment_date;
                return $paymentDate ? \Carbon\Carbon::parse($paymentDate)->format('d-m-Y') : 'Belum ada pembayaran';
            })
            ->addColumn('note', function ($participant) {
                return optional($participant->payments->first())->note ?? '-';
            })
            ->addColumn('action', function ($participant) {
                $payment = $participant->payments->first();
                if ($payment && $payment->proof_of_transfer) {
                    return '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#paymentProofModal' . $participant->id . '">Lihat Bukti Pembayaran</button>';
                }
                return '<button type="button" class="btn btn-secondary" disabled>Belum Ada Bukti</button>';
            })
            ->rawColumns(['event_type','accommodation','action'])
            ->make(true);
    }

    public function updatePaymentStatus(Request $request)
    {
        \Log::info('Incoming Request:', $request->all());

        $validated = $request->validate([
            'participant_id' => 'required|exists:registrasis,id',
            'status' => 'required|string|in:pending,paid,completed,failed,canceled,unpaid',
            'invoice_number' => 'required|string',
            'failed_reason' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',
            'proof_of_transfer' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
        ]);

        \Log::info('File Uploaded:', [
            'proof_of_transfer' => $request->file('proof_of_transfer') ? $request->file('proof_of_transfer')->getClientOriginalName() : 'No file'
        ]);

        // Cari data pembayaran berdasarkan invoice_number dan participant_id
        $payment = Payment::where('invoice_number', $request->invoice_number)
            ->where('registrasi_id', $request->participant_id)
            ->lockForUpdate()
            ->first();

        \Log::info('Query Log:', \DB::getQueryLog());

        if ($payment) {
            // Simpan file proof_of_transfer (jika ada)
            if ($request->hasFile('proof_of_transfer')) {
                $fileName = 'proof_' . time() . '.' . $request->file('proof_of_transfer')->getClientOriginalExtension();
                $request->file('proof_of_transfer')->storeAs('proof_of_transfer', $fileName, 'public');
                $payment->proof_of_transfer = $fileName;
            }

            // Update status pembayaran dan detail lainnya
            $payment->status = $request->status;
            $payment->failed_reason = $request->status === 'failed' ? $request->failed_reason : null;
            //cek apakah bank pembayaran memiliki value
            if (empty($payment->bank_name)) {
                $payment->bank_name = $request->bank_name;
            }            
            $payment->payment_date = $request->payment_date;
            $payment->save();

            // Jika status adalah 'completed', dispatch job untuk generate QR Code
            if ($request->status === 'completed') {
                if (empty($payment->ticket_number)) {
                    $payment->ticket_number = $this->generateTicketNumber();
                    $payment->save();
                }

                // Optimize and encode image background
                $imagePath = public_path('logo/bgtiket.jpg');
                
                // Read image and resize it to reduce size
                $image = \Image::make($imagePath);
                $image->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                
                // Convert to jpg with reduced quality
                $image->encode('jpg', 100);
                
                // Convert to base64
                $base64Image = 'data:image/jpeg;base64,' . base64_encode($image);

                // Prepare email data
                $emailData = [
                    'ticketNumber' => $payment->ticket_number,
                    'full_name' => $payment->registrasi->full_name,
                    'eventDetails' => $payment->registrasi->events->map(function ($event) {
                        return $event->name . ' - ' . $event->eventType->name;
                    }),
                    'totalPayment' => $payment->amount,
                    'backgroundImage' => $base64Image,
                ];

                // Dispatch email job
                SendPaymentCompletedEmail::dispatch($payment, $emailData);
                \Log::info('Dispatched SendPaymentCompletedEmail job for payment ID:', ['payment_id' => $payment->id]);

                // Dispatch job untuk generate QR Code
                GenerateQrCode::dispatch($payment);
                \Log::info('Dispatched GenerateQrCode job for payment ID:', ['payment_id' => $payment->id]);


                return response()->json(['success' => true, 'message' => 'QR Code generation dispatched.']);
            }

            if ($request->status === 'unpaid') {
                if (empty($payment->ticket_number)) {
                    $payment->ticket_number = $this->generateTicketNumber();
                    $payment->save();
                }

                // Dispatch email job
                // SendPaymentUnpaidEmail::dispatch($payment, $emailData);
                // \Log::info('Dispatched SendPaymentUnpaidEmail job for payment ID:', ['payment_id' => $payment->id]);

                // Dispatch job untuk generate QR Code
                GenerateQrCode::dispatch($payment);
                \Log::info('Dispatched GenerateQrCode job for payment ID:', ['payment_id' => $payment->id]);

                GenerateInvoice::dispatch($payment);
                \Log::info('Dispatched GenerateInvoice job for payment ID:', ['payment_id' => $payment->id]);

                SendUnpaidEmail::dispatch($payment);
                \Log::info('Dispatched SendUnpaidEmail job for payment ID:', ['payment_id' => $payment->id]);

            
                // // Kirim email
                // Mail::to($payment->registrasi->email)->send(new PaymentUnpaid($payment));

                return response()->json([
                    'success' => true, 
                    'message' => 'QR Code generation and Invoice created.', 
                    // 'invoice_url' => $publicUrl
                ]);
            }
      

            // Jika status adalah 'failed', kirim email dan simpan failed_reason
            if ($request->status === 'failed') {
                try {
                    // Kirim email pemberitahuan kegagalan pembayaran
                    Mail::to($payment->registrasi->email)->send(new PaymentFailedByAdmin($payment));

                    return response()->json(['success' => true, 'message' => 'Status pembayaran diperbarui dan email telah dikirim ke peserta.']);
                } catch (\Exception $e) {
                    \Log::error('Error sending email: ' . $e->getMessage());
                    return response()->json(['error' => 'Terjadi kesalahan saat mengirim email.'], 500);
                }
            }

            return response()->json(['success' => true, 'message' => 'Status pembayaran berhasil diperbarui.']);
        }

        return response()->json(['error' => 'Data tidak ditemukan atau tidak valid.'], 400);
    }

    public function generateTicketNumber()
    {
        // Ambil tanggal saat ini
        $date = Carbon::now()->format('ymd');

        // Mulai transaksi
        DB::beginTransaction();

        try {
            // Ambil tiket terbaru berdasarkan tanggal hari ini dengan lock database
            $latestTicket = Payment::where('ticket_number', 'like', 'T' . $date . '%')
                ->orderBy('ticket_number', 'desc')
                ->lockForUpdate()  // Kunci baris untuk memastikan tidak ada yang memodifikasi data yang sama
                ->first();

            // Tentukan urutan tiket berikutnya
            $ticketNumber = 1;
            if ($latestTicket) {
                $lastTicketNumber = (int) substr($latestTicket->ticket_number, -4);
                $ticketNumber = $lastTicketNumber + 1;
            }

            // Format nomor tiket
            $generatedTicketNumber = 'T' . $date . str_pad($ticketNumber, 4, '0', STR_PAD_LEFT);

            // Cek apakah tiket yang sama sudah ada di database, jika ada coba lagi
            if (Payment::where('ticket_number', $generatedTicketNumber)->exists()) {
                // Ulangi proses pembuatan tiket jika sudah ada tiket dengan nomor yang sama
                DB::rollBack();
                return $this->generateTicketNumber(); // Rekursif jika ada duplikat
            }

            // Simpan tiket baru ke dalam database
            DB::commit(); // Selesaikan transaksi jika tidak ada masalah

            return $generatedTicketNumber;

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika ada error
            throw $e;
        }
    }


    //crud peserta
    public function edit($id)
    {
        $participant = Registrasi::findOrFail($id);
        return view('admin.edit_participants', compact('participant'));
    }

    public function update(Request $request, $id)
    {
        $participant = Registrasi::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'nik' => 'required|string|max:20',
            'institusi' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
            'category' => 'required|string',
            'address' => 'required|string|max:255',
        ]);

        $participant->update($validated);

        return redirect()->route('admin.participants')->with('success', 'Peserta berhasil diupdate');
    }

    public function destroy($id)
    {
        // Temukan peserta berdasarkan ID
        $participant = Registrasi::findOrFail($id);

        // Hapus semua data terkait di tabel payments terlebih dahulu
        \DB::table('payments')->where('registrasi_id', $id)->delete();

        // Hapus peserta
        $participant->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('admin.participants')->with('success', 'Peserta berhasil dihapus');
    }

    public function import(Request $request)
    {

        $request->validate([
            'file' => 'required|mimes:csv,txt,xls,xlsx|max:2048', // Tambahkan 'txt' untuk file CSV yang mungkin memiliki MIME type berbeda
        ]);
        
    
        $file = $request->file('file');
        $filePath = $file->store('imports');
    
        // Dispatch job
        ImportParticipantsJob::dispatch(storage_path('app/' . $filePath));
    
        return redirect()->back()->with('success', 'Data sedang diproses. Anda akan diberi notifikasi setelah selesai.');
    }


    public function registerAdd(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'nik' => 'required|string|max:20',
            'email' => 'required|email',
            'category' => 'required|string|max:100',
            'institution' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'event_type' => 'required|exists:events,id',
            'source' => 'required|string|max:255',
            'special_price' => 'nullable|boolean', // Validasi harga spesial
        ]);

        // Simpan data peserta
        $participant = new Registrasi();
        $participant->full_name = $request->full_name;
        $participant->nik = $request->nik;
        $participant->email = $request->email;
        $participant->category = $request->category;
        $participant->institusi = $request->institution;
        $participant->phone = $request->phone;
        $participant->address = $request->address;
        $participant->source = $request->source;
        $participant->save();

        // Simpan data ke tabel pivot `registrasi_event`
        $eventId = $request->event_type;
        DB::table('registrasi_events')->insert([
            'registrasi_id' => $participant->id,
            'event_id' => $eventId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Ambil harga event berdasarkan tanggal atau special price
        $event = \App\Models\Event::find($eventId);

        if ($request->has('special_price') && $request->special_price) {
            $amount = 5250000; // Harga spesial
        } else {
            $amount = now()->greaterThan($event->early_bid_date) 
                ? $event->onsite_price 
                : $event->early_bid_price;
        }

        // Simpan data pembayaran
        $payment = new Payment();
        $payment->registrasi_id = $participant->id;
        $payment->status = 'pending'; // Status awal
        $payment->amount = $amount; // Atur jumlah berdasarkan pilihan harga
        $payment->invoice_number = 'INV-' . strtoupper(uniqid());
        $payment->save();

        return response()->json(['success' => true, 'message' => 'Peserta berhasil didaftarkan!']);
    }


    public function uploadPaymentProof(Request $request)
    {
        // Validasi input
        $request->validate([
            'participant_id' => 'required|exists:registrasis,id', // Pastikan participant_id valid
            'proof_of_transfer' => 'required|file|mimes:jpg,png,jpeg|max:2048', // Validasi file
            'bank_name' => 'required|string|max:255', // Validasi nama bank
            'payment_date' => 'required|date', // Validasi tanggal pembayaran
        ]);
    
        // Cari peserta berdasarkan ID
        $participant = Registrasi::findOrFail($request->participant_id);
    
        // Ambil data pembayaran pertama peserta
        $payment = $participant->payments->first();
    
        if (!$payment) {
            // Jika data pembayaran belum ada
            $payment = new Payment();
            $payment->registrasi_id = $participant->id; // Hubungkan dengan peserta
        }
    
        // Simpan file bukti pembayaran
        if ($request->hasFile('proof_of_transfer')) {
            $fileName = 'proof_' . time() . '.' . $request->file('proof_of_transfer')->getClientOriginalExtension();
            $request->file('proof_of_transfer')->storeAs('proof_of_transfer', $fileName, 'public');
            $payment->proof_of_transfer = $fileName;
        }
    
        // Perbarui data pembayaran
        $payment->bank_name = $request->bank_name; // Nama bank
        $payment->payment_date = $request->payment_date; // Tanggal pembayaran
        $payment->status = 'pending'; // Status default
        $payment->save(); // Simpan perubahan ke database
    
        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah.');
    }
    

}
