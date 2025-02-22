<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use App\Models\Event;
use App\Models\Registrasi;
use App\Models\Payment;
use App\Models\Accommodation;
use App\Models\RoomAvailability;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\PayLaterNotification;
use App\Mail\FinishRegistration;
use Carbon\Carbon; 
use Illuminate\Support\Facades\DB;
use App\Models\GroupCode;
use Illuminate\Validation\ValidationException;




class RegisterController extends Controller
{
  
    public function checkGroupCode($code)
    {
        try {
            $groupCode = GroupCode::where('code', $code)
                ->where('is_active', true)
                ->first();

            if (!$groupCode) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Kode grup tidak valid atau sudah tidak aktif'
                ]);
            }

            if ($groupCode->current_members >= $groupCode->max_members) {
                return response()->json([
                    'valid' => false,
                    'message' => 'The group is full'
                ]);
            }

            return response()->json([
                'valid' => true,
                'message' => 'Kode grup valid',
                'remaining_slots' => $groupCode->max_members - $groupCode->current_members
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Terjadi kesalahan saat memvalidasi kode grup'
            ]);
        }
    }

    // Step 1: Menampilkan Formulir Pendaftaran
    public function showStep1(Request $request)
    {
        $formData = $request->session()->get('form_data', []);

        return view('register.step1', compact('formData'));
    }

    // // Step 1: Menyimpan Data Formulir
    // public function storeStep1(Request $request)
    // {
    //     // Validasi data formulir
    //     $validated = $request->validate([
    //         'full_name' => 'required|string|max:255',
    //         'nik' => 'required|string|max:25',
    //         'institusi' => 'required|string|max:255',
    //         'email' => 'required|email',
    //         'category' => 'required|string|in:Student,General Practitioner/Resident,Specialist',
    //         'specialistDetail' => 'nullable|string',
    //         'phone' => 'required|string',
    //         'address' => 'required|string',
    //         'group_code' =>'nullable|string',
    //     ]);

    //     // Validate group code if group registration is selected
    //     if ($request->has('is_group') && $request->is_group) {
    //         try {
    //             $groupCode = GroupCode::where('code', $request->group_code)
    //                 ->where('is_active', true)
    //                 ->first();

    //             if (!$groupCode) {
    //                 throw ValidationException::withMessages([
    //                     'group_code' => ['Kode grup tidak valid atau sudah tidak aktif']
    //                 ]);
    //             }

    //             if ($groupCode->current_members >= $groupCode->max_members) {
    //                 throw ValidationException::withMessages([
    //                     'group_code' => ['Grup sudah penuh']
    //                 ]);
    //             }

    //             // Increment current_members
    //             $groupCode->current_members += 1;
    //             $groupCode->save();
    //         } catch (ValidationException $e) {
    //             return redirect()->back()
    //                 ->withInput()
    //                 ->withErrors(['group_code' => $e->getMessage()]);
    //         }
    //     }

    //     // Rest of your validation
    //     $validated = $request->validate([
    //         'full_name' => 'required|string|max:255',
    //         'nik' => 'required|string|max:25',
    //         'institusi' => 'required|string|max:255',
    //         'email' => 'required|email',
    //         'category' => 'required|string|in:Student,General Practitioner/Resident,Specialist',
    //         'specialistDetail' => 'nullable|string',
    //         'phone' => 'required|string',
    //         'address' => 'required|string',
    //     ]);

    //     // Add group code to session data if present
    //     if ($request->has('is_group') && $request->is_group) {
    //         $validated['group_code'] = $request->group_code;
    //     }

    //     $request->session()->put('form_data', $validated);
    //     return redirect()->route('register.step2');

    //     // Menyimpan data ke session
    //     $request->session()->put('form_data', $validated);
        

    //     // Redirect ke Step 2: Pilih Produk
    //     return redirect()->route('register.step2');
    // }

    public function storeStep1(Request $request)
    {
        // First validate the basic form data
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'nik' => 'required|string|max:25',
            'institusi' => 'required|string|max:255',
            'email' => 'required|email',
            'category' => 'required|string|in:Student,General Practitioner/Resident,Specialist',
            'specialistDetail' => 'nullable|string',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

        // Only validate group code if group registration is selected
        if ($request->has('is_group') && $request->is_group) {
            $request->validate([
                'group_code' => 'required|string'
            ]);

            $groupCode = GroupCode::where('code', $request->group_code)
                ->where('is_active', true)
                ->first();

            if (!$groupCode) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['group_code' => 'Kode grup tidak valid atau sudah tidak aktif']);
            }

            if ($groupCode->current_members >= $groupCode->max_members) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['group_code' => 'Grup sudah penuh']);
            }

            // // If validation passes, increment the group members
            // $groupCode->current_members += 1;
            // $groupCode->save();

            // // Cek apakah peserta ke-6 dan beri harga gratis untuk symposium
            // $isFreeParticipant = $groupCode->current_members + 1 === 6;

            // Tambahkan logika untuk menentukan apakah harga harus gratis atau tidak
            // if ($isFreeParticipant) {
            //     // Misalnya, kamu ingin memberikan harga 0 untuk symposium (adjust price logic)
            //     $validated['is_free_participant'] = true;
            // }


            // Add group code to validated data
            $validated['group_code'] = $request->group_code;
        }

        // Save to session and proceed
        $request->session()->put('form_data', $validated);
        return redirect()->route('register.step2');
    }

    // Step 2: Menampilkan Pilihan Produk
    public function showStep2(Request $request)
    {
        // Ambil data dari session dengan prioritas
        $registrationData = session('registration_data', []);
        $formData = $registrationData['form_data'] ?? session('form_data', []);
        
        // Coba ambil selected categories dari berbagai sumber session
        $selectedCategories = [];
        
        // 1. Coba dari registration_data
        if (!empty($registrationData['selected_categories'])) {
            $selectedCategories = $registrationData['selected_categories'];
        }
        // 2. Coba dari selected_categories langsung
        elseif (session()->has('selected_categories')) {
            $selectedCategories = session('selected_categories');
        }
        // 3. Coba dari selected_events
        elseif (session()->has('selected_events')) {
            $selectedCategories = session('selected_events');
        }

        $selectedCategory = $formData['category'] ?? null;

        // Filter berdasarkan kategori yang dipilih
        if ($selectedCategory === 'Student') {
            $eventTypes = EventType::where('id', 2)->get(); 
            $events = Event::where('id', 3)->get(); 
        } elseif ($selectedCategory === 'General Practitioner/Resident') {
            $eventTypes = EventType::where('id', 2)->get(); 
            $events = Event::where('id', 4)->get();
        } elseif ($selectedCategory === 'Specialist') {
            $eventTypes = EventType::whereIn('id', [1, 2])->get();
            $events = Event::where('id', 5)->get(); 
        } else {
            // Default events jika tidak ada kategori yang dipilih
            $eventTypes = EventType::all();
            $events = Event::all();
        }


        return view('register.step2', compact('eventTypes', 'events', 'selectedCategory', 'selectedCategories'));
    }


    // public function getEventsByType($eventTypeId)
    // {
    //     $events = Event::where('event_type_id', $eventTypeId)->get();
    //     return response()->json($events);
    // }


    // Step 2: Menyimpan Pilihan Event
    public function storeStep2(Request $request)
    {
        // Validasi input
        $request->validate([
            'selected_categories' => 'required',
        ]);

        // Pisahkan kategori yang dipilih menjadi array
        $selectedCategories = explode(',', $request->input('selected_categories')[0]); 
        $selectedCategories = array_map('intval', $selectedCategories);

        // Simpan ke session dengan multiple key untuk konsistensi
        session([
            'selected_categories' => $selectedCategories,
            'selected_events' => $selectedCategories,
            'registration_data' => [
                'form_data' => session('form_data'),
                'selected_categories' => $selectedCategories
            ]
        ]);

        return redirect()->route('register.accommodation');
    }

    public function showAccommodation()
    {
        // Ambil data dari berbagai kemungkinan session key
        $registrationData = session('registration_data', []);
        $accommodationData = session('accommodation_booking', 
            session('previous_accommodation',
                $registrationData['accommodation'] ?? []
            )
        );

        $accommodations = Accommodation::where('is_active', true)->get();

        return view('register.accommodation', compact('accommodations', 'accommodationData'));
    }

    public function checkAvailability(Request $request)
    {
        $roomId = $request->room_id;
        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);

        // Get minimum available rooms for the selected dates
        $minAvailable = RoomAvailability::where('accommodation_id', $roomId)
            ->whereBetween('date', [$checkIn->format('Y-m-d'), $checkOut->subDay()->format('Y-m-d')])
            ->min('available_rooms');

        // If no records found, use the accommodation's total qty
        if ($minAvailable === null) {
            $accommodation = Accommodation::find($roomId);
            $minAvailable = $accommodation->qty;
        }

        return response()->json([
            'available' => $minAvailable
        ]);
    }

    public function storeAccommodation(Request $request)
    {
        // Cek apakah ada kamar yang dipilih
        $hasSelectedRooms = false;
        $selectedRooms = [];
        
        if (is_array($request->rooms)) {
            foreach ($request->rooms as $roomId => $quantity) {
                if ((int)$quantity > 0) {
                    $hasSelectedRooms = true;
                    $room = Accommodation::find($roomId);
                    $selectedRooms[$roomId] = [
                        'room_type' => $room->room_type,
                        'price' => $room->price,
                        'quantity' => (int)$quantity
                    ];
                }
            }
        }

        // Simpan data booking dengan multiple key untuk konsistensi
        $bookingData = [
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'rooms' => $selectedRooms
        ];

        session([
            'accommodation_booking' => $bookingData,
            'previous_accommodation' => $bookingData,
            'registration_data' => array_merge(
                session('registration_data', []),
                ['accommodation' => $bookingData]
            )
        ]);

        // Pastikan data events tetap ada
        if (!session()->has('selected_categories')) {
            session([
                'selected_categories' => session('selected_events', [])
            ]);
        }

        return redirect()->route('register.step3');
    }


    public function showStep3(Request $request)
    {

        //dd($request);

        // Mengambil data dari session
        $formData = $request->session()->get('form_data');
        $selectedCategories = $request->session()->get('selected_categories', []);
        $categories = Event::whereIn('id', $selectedCategories)->with('eventType')->get();
        $quotaReduced = $request->session()->get('quota_reduced', false); // Cek apakah kuota sudah dikurangi

        // Pastikan data event_type dan selected_categories ada
        if (!$formData || empty($selectedCategories)) {
            return redirect()->route('register.step1')->with('error', 'Please complete Step 1 and Step 2 before proceeding.');
        }

        // Mengambil kategori berdasarkan selected_categories yang ada di session
        $categories = Event::whereIn('id', $selectedCategories)->get();

        foreach ($categories as $category) {
            $today = now();
            // Set early_bid_date sampai pukul 23:59:59
            $earlyBirdDeadline = \Carbon\Carbon::parse($category->early_bid_date)->endOfDay();
            $isEarlyBird = $today <= $earlyBirdDeadline;
            $category->price = $isEarlyBird ? $category->early_bid_price : $category->onsite_price;
        }
        

        // Hanya kurangi kuota jika belum pernah dikurangi
         if (!$quotaReduced) {
        foreach ($selectedCategories as $eventId) {
            $event = Event::find($eventId);

            if ($event) {
                // Jika kuota bernilai 0, hentikan proses dengan pesan error
                if ($event->kuota === 0) {
                    return redirect()->route('register.step1')->with('error', 'The quota for ' . $event->name . ' is full.');
                }

                // Jika kuota lebih besar dari 0, kurangi kuota
                if ($event->kuota !== null && $event->kuota > 0) {
                    $event->kuota -= 1;
                    $event->save();
                }

                // Jika kuota null, lanjutkan proses tanpa pengurangan
            } else {
                return redirect()->route('register.step1')->with('error', 'Event dengan ID ' . $eventId . ' tidak ditemukan.');
            }
        }

        // Tandai kuota sebagai sudah dikurangi di session
        $request->session()->put('quota_reduced', true);
    }
        

        // Mengirimkan data ke view
        return view('register.step3', compact('formData', 'categories'));
    }


    // Step 3: Menyelesaikan Pendaftaran
    public function submitRegistration(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validasi inputan dari pengguna
            $request->validate([
                'full_name' => 'required|string|max:255',
                'nik' => 'required|string|max:25',
                'institusi' => 'required|string|max:255',
                'email' => 'required|email',
                'category' => 'required|string|in:Student,General Practitioner/Resident,Specialist',
                'other' => 'nullable|string|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:255',
            ]);

            // Ambil kategori yang dipilih dari session
            $selectedCategories = $request->session()->get('selected_categories', []);
            $other = null;

            // Jika kategori adalah Specialist, ambil detail lainnya
            if ($request->category == 'Specialist') {
                $other = $request->specialistDetail;
            }

            // Simpan data registrasi
            $registrasi = new Registrasi();
            $registrasi->full_name = $request->full_name;
            $registrasi->nik = $request->nik;
            $registrasi->institusi = $request->institusi;
            $registrasi->email = $request->email;
            $registrasi->category = $request->category;
            $registrasi->other = $request->category == 'Specialist' ? $request->specialistDetail : null;
            $registrasi->phone = $request->phone;
            $registrasi->address = $request->address;
            $registrasi->source = 'web';
            $registrasi->save();

            // Simpan hubungan many-to-many antara registrasi dan events
            $selectedCategories = session('selected_categories', []);
            $registrasi->events()->sync($selectedCategories);

            // Simpan accommodation jika ada
            if (session()->has('accommodation_booking')) {
                $accommodationData = session('accommodation_booking');
                $checkIn = \Carbon\Carbon::parse($accommodationData['check_in_date']);
                $checkOut = \Carbon\Carbon::parse($accommodationData['check_out_date']);
                
                foreach ($accommodationData['rooms'] as $roomId => $room) {
                    if ($room['quantity'] > 0) {
                        $accommodation = Accommodation::find($roomId);
                        
                        // Generate array tanggal antara check-in dan check-out
                        $dates = [];
                        $currentDate = $checkIn->copy();
                        while ($currentDate < $checkOut) {
                            $dates[] = $currentDate->format('Y-m-d');
                            $currentDate->addDay();
                        }

                        // Cek ketersediaan untuk setiap tanggal
                        foreach ($dates as $date) {
                            $availability = \App\Models\RoomAvailability::firstOrCreate(
                                [
                                    'accommodation_id' => $roomId,
                                    'date' => $date
                                ],
                                [
                                    'available_rooms' => $accommodation->qty
                                ]
                            );

                            // Validasi ketersediaan
                            if ($availability->available_rooms < $room['quantity']) {
                                DB::rollBack();
                                return redirect()->back()->with('error', 
                                    "Sorry, room {$accommodation->name} is not available for date {$date}. Available: {$availability->available_rooms}");
                            }

                            // Kurangi ketersediaan
                            $availability->available_rooms -= $room['quantity'];
                            $availability->save();

                            \Log::info("Room availability updated for date: {$date}", [
                                'room' => $accommodation->name,
                                'booked' => $room['quantity'],
                                'remaining' => $availability->available_rooms
                            ]);
                        }

                        // Hitung total price
                        $nights = count($dates);
                        $total_price = $room['price'] * $room['quantity'] * $nights;
                        
                        // Attach ke pivot table
                        $registrasi->accommodations()->attach($roomId, [
                            'check_in_date' => $accommodationData['check_in_date'],
                            'check_out_date' => $accommodationData['check_out_date'],
                            'quantity' => $room['quantity'],
                            'total_price' => $total_price
                        ]);
                    }
                }
            }

            // Hitung total amount
            $amount = 0;
            foreach ($selectedCategories as $categoryId) {
                $event = Event::find($categoryId);
                if ($event) {
                    $price = now()->timezone('Asia/Jakarta') <= \Carbon\Carbon::parse($event->early_bid_date)->endOfDay()->timezone('Asia/Jakarta')
                        ? $event->early_bid_price
                        : $event->onsite_price;
                    $amount += $price;
                }
            }

            // Tambahkan accommodation amount jika ada
            if (session()->has('accommodation_booking')) {
                $accommodationData = session('accommodation_booking');
                $checkIn = \Carbon\Carbon::parse($accommodationData['check_in_date']);
                $checkOut = \Carbon\Carbon::parse($accommodationData['check_out_date']);
                $nights = $checkIn->diffInDays($checkOut);

                foreach ($accommodationData['rooms'] as $room) {
                    $amount += $room['price'] * $room['quantity'] * $nights;
                }
            }

            // Set payment expiry dengan benar
            $currentTime = now('Asia/Jakarta');
            $expiryTime = $currentTime->copy()->addDay(); // Menggunakan addDay()

            // Debug timestamps
            \Log::info('Payment Times:', [
                'current' => $currentTime->format('Y-m-d H:i:s'),
                'expiry' => $expiryTime->format('Y-m-d H:i:s')
            ]);

            // Generate invoice number
            $today = now()->format('ymd');
            $latestInvoice = Payment::whereDate('created_at', today())->latest('id')->first();
            $invoiceNumber = 'INV' . $today . str_pad(($latestInvoice ? $latestInvoice->id + 1 : 1), 5, '0', STR_PAD_LEFT);

            // Create payment
            $payment = new Payment();
            $payment->registrasi_id = $registrasi->id;
            $payment->invoice_number = $invoiceNumber;
            $payment->amount = $amount;
            $payment->payment_expiry = $expiryTime;
            $payment->status = 'pending';
            $payment->save();

            DB::commit();

            return redirect()->route('register.step4');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Registration Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('register.step1')
                ->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    public function showStep4()
    {
        try {
            // Ambil data registrasi dari session
            $registrationData = session('registration_data');
            if (!$registrationData) {
                return redirect()->route('register.step1')
                    ->with('error', 'Please complete registration first');
            }

            // Ambil payment terakhir yang dibuat
            $payment = Payment::with('registrasi')->latest()->first();
            if (!$payment) {
                return redirect()->route('register.step1')
                    ->with('error', 'Payment information not found');
            }

            // Gunakan relationship 'registrasi' yang benar
            $registrasi = $payment->registrasi;
            if (!$registrasi) {
                return redirect()->route('register.step1')
                    ->with('error', 'Registration data not found');
            }

            $events = $registrasi->events;
            
            // Hitung total events
            $eventsTotal = 0;
            foreach ($events as $event) {
                $price = now()->timezone('Asia/Jakarta') <= \Carbon\Carbon::parse($event->early_bid_date)->endOfDay()->timezone('Asia/Jakarta')
                    ? $event->early_bid_price
                    : $event->onsite_price;
                $eventsTotal += $price;
            }

            // Tambahkan total accommodation dari pivot table
            $accommodationTotal = 0;
            foreach ($registrasi->accommodations as $accommodation) {
                $checkIn = \Carbon\Carbon::parse($accommodation->pivot->check_in_date);
                $checkOut = \Carbon\Carbon::parse($accommodation->pivot->check_out_date);
                $nights = $checkIn->diffInDays($checkOut);
                
                $accommodationTotal += $accommodation->price * $accommodation->pivot->quantity * $nights;
            }

            // Update payment amount
            $payment->amount = $eventsTotal + $accommodationTotal;
            $payment->save();

            return view('register.step4', compact('payment', 'registrasi', 'events'));

        } catch (\Exception $e) {
            \Log::error('Step 4 Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('register.step1')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    


    // public function showPayLaterForm(Request $request)
    // {
    //     //$invoice = $request->session()->get('invoice_number');
    //     $invoice = $request->get('invoice_number');

    //     if ($invoice) {
    //         // Ambil detail pembayaran berdasarkan invoice_number
    //         $payment = Payment::where('invoice_number', $invoice)->first();

    //         if ($payment) {
    //             // Ambil detail registrasi berdasarkan payment_id
    //             $registrasi = $payment->registrasi; 
    //             $eventDetails = $registrasi->eventType;
    //             $categoryDetails = $registrasi->event;
                

    //             //dd($categoryDetails);
                
    //             // Cek apakah bukti transfer sudah ada
    //             $proofUploaded = $payment->proof_of_transfer ? true : false;

    //             $request->session()->forget('invoice_number');

    //             return view('register.pay-later', [
    //                 'registrasi' => $registrasi,
    //                 'eventDetails' => $eventDetails,
    //                 'categoryDetails' => $categoryDetails,
    //                 'payment' => $payment,
    //                 'proofUploaded' => $proofUploaded,  // Kirimkan variabel ini ke view
    //             ]);
    //         }
    //     }

    //     return view('register.pay-later');
    // }


    
    // public function payLater(Request $request)
    // {
    //     // Ambil data form dari session
    //     $formData = $request->session()->get('form_data');
    //     //$selectedCategories = $request->session()->get('selected_categories', []);

        
    
    //     if (!$formData) {
    //         return redirect()->route('register.step1')->with('error', 'Complete the data before selecting Pay Later.');
    //     }
    
    //     // Simpan data ke tabel Registrasi
    //     $registrasi = new Registrasi();
    //     $registrasi->fill($formData);
    //     $registrasi->save();
    
    //     // Generate nomor invoice
    //     $today = now()->format('ymd');
    //     $latestInvoice = Payment::whereDate('created_at', today())->latest('id')->first();
    //     $invoiceNumber = 'INV' . $today . str_pad(($latestInvoice ? $latestInvoice->id + 1 : 1), 5, '0', STR_PAD_LEFT);
    
    //     // Simpan data pembayaran
    //     $payment = new Payment();
    //     $payment->registrasi_id = $registrasi->id;
    //     $payment->invoice_number = $invoiceNumber;
    //     //$payment->amount = 0; // Total amount will be calculated based on selected events
    //     $payment->payment_expiry = now()->addHours(24); 
    //     $payment->status = 'pending';
        
    
    //     // Loop through selected categories (event IDs) and attach them to the registrasi
    //     $totalAmount = 0; // Initialize total amount for the payment
    
    //     // foreach ($selectedCategories as $eventId) {
    //     //     $event = Event::find($eventId);
    //     //     if ($event) {
    //     //         // Lampirkan setiap event ke registrasi 
    //     //         $registrasi->events()->attach($event);
        
    //     //         // Tambahkan harga event ke total amount
    //     //         $totalAmount += $event->price;
        
    //     //         // Kurangi kuota event sebesar 1 dan simpan
    //     //         if ($event->kuota > 0) { 
    //     //             $event->kuota -= 1;
    //     //             $event->save();
    //     //         } else {
    //     //             return redirect()->route('register.step1')->with('error', 'Kuota untuk ' . $event->name . ' sudah penuh.');
    //     //         }
    //     //     }
    //     // }
        
    
    //     // Update the payment amount after attaching all events
    //     $payment->amount = $totalAmount;
    //     $payment->save();
    
    //     // Kirim email konfirmasi
    //     Mail::to($registrasi->email)->send(new PayLaterNotification($payment));
    
    //     // Clear session data
    //     $request->session()->flush();
    
    //     // Redirect to transaction page with payment info
    //     return redirect()->route('register.transactionLater', ['payment_id' => $payment->id])
    //                     ->with('success', 'You have chosen to pay later. Payment information has been sent to your email.');
    // }

    public function payLater(Request $request)
    {
        // Ambil data form dari session
        $formData = $request->session()->get('form_data');
        $selectedEvents = $request->session()->get('selected_categories'); 

        // Cek jika data form atau selected events tidak ada
        if (!$formData || !$selectedEvents) {
            return redirect()->route('register.step1')->with('error', 'Complete the data before selecting Pay Later.');
        }

        // Ambil registrasi berdasarkan email yang ada di session
        $registrasi = Registrasi::where('email', $formData['email'])->latest()->first();
        
        if (!$registrasi) {
            return redirect()->route('register.step1')->with('error', 'No registration found.');
        }

        // Ambil pembayaran yang terkait dengan registrasi ini
        $payment = Payment::where('registrasi_id', $registrasi->id)->latest()->first();

        // Cek apakah pembayaran ada atau tidak
        if (!$payment) {
            return redirect()->route('register.step1')->with('error', 'No payment record found.');
        }

        // Kirim email konfirmasi dengan informasi pembayaran
        $paymentDetails = new \stdClass();
        $paymentDetails->invoice_number = $payment->invoice_number;
        $paymentDetails->amount = $payment->amount;
        $paymentDetails->status = $payment->status;
        $paymentDetails->payment_expiry = $payment->payment_expiry;

        // Kirim email konfirmasi ke user
        Mail::to($formData['email'])->send(new PayLaterNotification($paymentDetails));

        // Clear session data setelah pengiriman email
        $request->session()->flush();

        // Redirect ke halaman transaksi atau halaman sukses
        return redirect()->route('register.transactionLater')
                        ->with('success', 'You have chosen to pay later. Payment information has been sent to your email.');
    }

    public function showTransactionLater()
    {
        // Menampilkan view setelah transaksi berhasil
        return view('register.transactionLater');
    }

    //Cek invoice
    // public function processInvoice(Request $request)
    // {
    //     $validated = $request->validate([
    //         'invoice_number' => 'required|string|exists:payments,invoice_number',
    //     ]);

    //     // Simpan nomor invoice ke session
    //     $request->session()->put('invoice_number', $validated['invoice_number']);

    //     return redirect()->route('register.pay-laterForm');
    // }

    // public function processInvoice(Request $request)
    // {
    //     $validated = $request->validate([
    //         'invoice_number' => [
    //             'required',
    //             'string',
    //             'exists:payments,invoice_number'
    //         ]
    //     ], [
    //         'invoice_number.exists' => 'Nomor invoice yang Anda masukkan tidak valid atau tidak ditemukan.',
    //         'invoice_number.required' => 'Silakan masukkan nomor invoice.',
    //     ]);

    //     // Cari payment berdasarkan nomor invoice
    //     $payment = Payment::where('invoice_number', $validated['invoice_number'])->first();

    //     if ($payment) {
    //         // Simpan nomor invoice ke session
    //         $request->session()->put('invoice_number', $validated['invoice_number']);
            
    //         // Redirect ke form pay-later dengan payment_id
    //         return redirect()->route('register.pay-laterForm', ['payment_id' => $payment->id]);
    //     } else {
    //         // Kembali ke halaman sebelumnya dengan pesan error jika invoice tidak ditemukan
    //         return redirect()->back()->withErrors([
    //             'invoice_number' => 'Nomor invoice yang dimasukkan tidak ditemukan.'
    //         ]);
    //     }
    // }


    public function processPayLaterPayment(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|exists:payments,invoice_number',
            'proof_of_transfer' => 'required|file|mimes:jpg,jpeg,png|max:1048',
            'bank_name' => 'required|string|max:255',
            'payment_date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        // Cari data pembayaran berdasarkan nomor invoice
        $payment = Payment::where('invoice_number', $request->invoice_number)->first();
    
        if ($payment && $payment->status === 'pending' || $payment->status === 'failed') {
            // Simpan bukti transfer
            if ($request->hasFile('proof_of_transfer')) {
                $filePath = $request->file('proof_of_transfer')->store('public/proof_of_transfer');
                $payment->proof_of_transfer = basename($filePath);
                $payment->status = 'paid';
                $payment->bank_name = $request->input('bank_name');
                $payment->payment_date = $request->input('payment_date');
                $payment->note = $request->input('note');
                $payment->save();
    
                // Kirim email ke peserta
                Mail::to($payment->registrasi->email)->send(new FinishRegistration($payment));
            }
    
            // Redirect setelah email dikirim
            return redirect()->route('register.transactionSuccess')
                             ->with('success', 'Payment proof uploaded successfully. Waiting for confirmation.');
        }
    
        return back()->withErrors(['invoice_number' => 'Invoice number not found or already paid.']);
    }

    public function showInvoiceForm()
    {
        // Tampilkan form untuk memasukkan nomor invoice
        return view('register.pay-later');
    }
    
    // public function showPayLaterForm(Request $request)
    // {
    //     // Ambil nomor invoice dari form input
    //     $invoice = $request->input('invoice_number');
        
    //     // Ambil detail pembayaran berdasarkan invoice_number
    //     $payment = Payment::where('invoice_number', $invoice)->first();

    //     if ($payment) {
    //         // Ambil detail registrasi berdasarkan payment_id
    //         $registrasi = $payment->registrasi; 
    //         $eventDetails = $registrasi->eventType;
    //         $categoryDetails = $registrasi->event;
    //         $proofUploaded = $payment->proof_of_transfer ? true : false;

    //         return view('register.pay-later-details', [
    //             'registrasi' => $registrasi,
    //             'eventDetails' => $eventDetails,
    //             'categoryDetails' => $categoryDetails,
    //             'payment' => $payment,
    //             'proofUploaded' => $proofUploaded,
    //         ]);
    //     }

    //     // Jika invoice tidak ditemukan, kembali ke form dengan pesan error
    //     return redirect()->route('register.pay-later')->withErrors(['invoice_number' => 'Invoice tidak ditemukan.']);
    // }

    public function showPayLaterForm(Request $request)
    {
        // Validasi input invoice_number
        $request->validate([
            'invoice_number' => 'required|string|exists:payments,invoice_number',
        ], [
            'invoice_number.exists' => 'Invoice not found or invalid.',
        ]);
    

        $invoice = $request->input('invoice_number');
        session(['invoice_number' => $invoice]);

    
        // Cari payment berdasarkan nomor invoice
        $payment = Payment::where('invoice_number', $invoice)->first();
    
        if ($payment) {
            // Ambil detail registrasi terkait dengan payment
            $registrasi = $payment->registrasi;
    
            // Ambil detail event type dan kategori
            $eventDetails = $registrasi->eventType;
            $categoryDetails = $registrasi->category;
    
            // Mengecek apakah bukti pembayaran sudah diupload
            $proofUploaded = $payment->proof_of_transfer ? true : false;
    
            // Ambil semua event yang dipilih dari tabel pivot registrasi_events
            $selectedEvents = $registrasi->events;
    
            // Menentukan harga total dan harga per event berdasarkan early bird atau onsite price
            $amount = 0;
            $today = now();
            $eventPrices = [];
    
            foreach ($selectedEvents as $event) {
                $isEarlyBid = $today <= \Carbon\Carbon::parse($event->early_bid_date);
                $price = $isEarlyBid ? $event->early_bid_price : $event->onsite_price;
                $amount += $price;

    
                // // Simpan harga per event ke dalam array
                // $eventPrices[] = [
                //     'event_name' => $event->name,
                //     'price' => $price,
                //     'is_early_bid' => $isEarlyBid,
                // ];

            }
    
            // Tampilkan detail pembayaran di view
            return view('register.pay-later-details', [
                'registrasi' => $registrasi,
                'eventDetails' => $eventDetails,
                'categoryDetails' => $categoryDetails,
                'selectedEvents' => $selectedEvents, // Detail event yang dipilih
                'payment' => $payment,
                'proofUploaded' => $proofUploaded,
                'amount' => $amount, // Menampilkan jumlah total pembayaran
                'eventPrices' => $eventPrices,
            ]);
        }
    
        // Jika invoice tidak ditemukan, kembali ke form dengan pesan error
        return redirect()->route('register.pay-laterInvoice')->withErrors(['invoice_number' => 'Invoice tidak ditemukan atau tidak valid.']);
    }
    
}
