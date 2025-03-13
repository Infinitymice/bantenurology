@extends('layouts.app')

@section('title', 'Transaction Successful')

@section('content')
<div class="container mt-5">
    @if ($errors->any())
        {!! implode(
            '',
            $errors->all('<div class="alert alert-danger alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                :message
            </div>')
        ) !!}
    @endif
    <div class="row">
        <!-- Progress Bar -->
        <div class="col-12 mb-4">
            <div class="progress">
                <div class="progress-bar" style="width: 100%;" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Step 4 of 4</div>
            </div>
        </div>

        <!-- Transaction Success Message -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="text-center"><strong>Transaction Successful</strong></h1>
                    <p class="text-center">Thank you for completing your registration for the 2nd Banten Urology Symposium 2025.</p>

                    <div class="mb-4 row d-flex justify-content-between">
                        <!-- Customer Information -->
                        <div class="col-md-6">
                            <h5><strong>Customer Information</strong></h5>
                            <p><strong>Name:</strong> {{ $registrasi->full_name }}</p>
                            <p><strong>NIK:</strong> {{ $registrasi->nik }}</p>
                            <p><strong>Email:</strong> {{ $registrasi->email }}</p>
                            <p><strong>Category:</strong> {{ $registrasi->category }}</p>
                            @if($registrasi->category === 'Specialist')
                                <p><strong>Title:</strong> {{ $registrasi->other }}</p>
                            @endif
                            <p><strong>Institution:</strong> {{ $registrasi->institusi }}</p>
                            <p><strong>Phone:</strong> {{ $registrasi->phone }}</p>
                            <p><strong>Address:</strong> {{ $registrasi->address }}</p>
                        </div>

                        <!-- Order Information -->
                        <div class="col-md-6 text-end">
                            <h5><strong>Order Details</strong></h5>
                            <p><strong>Order Invoice:</strong> {{ $payment->invoice_number }}</p>
                            <p><strong>Issue Date:</strong> {{ $payment->created_at->format('d-m-Y') }}</p>
                            <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($payment->payment_expiry)->format('d-m-Y') }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($payment->status) }}</p>
                        </div>
                    </div>

                    <!-- Events Table -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Events Detail</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Event</th>
                                            <th class="text-end">Price</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php 
                                        $categoriesDetails = session('categories_details', []);
                                        $eventsTotal = array_sum(array_column($categoriesDetails, 'price'));
                                    @endphp
                                    @foreach ($categoriesDetails as $index => $category)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $category['eventTypeName'] }} - {{ $category['name'] }}</td>
                                            <td class="text-end">
                                                @if(isset($category['discountPercentage']) && $category['discountPercentage'] > 0)
                                                    <strike>Rp. {{ number_format($category['originalPrice']) }}</strike><br>
                                                    <span class="text-success">Rp. {{ number_format($category['price']) }}</span>
                                                @else
                                                    Rp. {{ number_format($category['price']) }}
                                                @endif
                                            </td>
                                            <td class="text-center">1</td>
                                            <td class="text-end">Rp. {{ number_format($category['price']) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Events Total</strong></td>
                                            <td class="text-end"><strong>Rp {{ number_format($eventsTotal, 0, ',', '.') }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($registrasi->accommodations->isNotEmpty())
                        <!-- Accommodation Table -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-hotel me-2"></i>Accommodation Detail</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Room Type</th>
                                                <th>Check In</th>
                                                <th>Check Out</th>
                                                <th class="text-end">Price/Night</th>
                                                <th class="text-center">Nights</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @php $accommodationTotal = 0; @endphp
                                        @foreach($registrasi->accommodations as $accommodation)
                                            @php
                                                $checkIn = \Carbon\Carbon::parse($accommodation->pivot->check_in_date);
                                                $checkOut = \Carbon\Carbon::parse($accommodation->pivot->check_out_date);
                                                $nights = $checkIn->diffInDays($checkOut);
                                                $subtotal = $accommodation->price * $accommodation->pivot->quantity * $nights;
                                                $accommodationTotal += $subtotal;
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $accommodation->name }}</td>
                                                <td>{{ $checkIn->format('d M Y') }}</td>
                                                <td>{{ $checkOut->format('d M Y') }}</td>
                                                <td class="text-end">Rp {{ number_format($accommodation->price, 0, ',', '.') }}</td>
                                                <td class="text-center">{{ $nights }}</td>
                                                <td class="text-center">{{ $accommodation->pivot->quantity }}</td>
                                                <td class="text-end">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="7" class="text-end"><strong>Accommodation Total</strong></td>
                                                <td class="text-end"><strong>Rp {{ number_format($accommodationTotal, 0, ',', '.') }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Grand Total Card -->
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Payment Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="80%" class="text-end">Events Total:</td>
                                            <td class="text-end"><strong>Rp {{ number_format($eventsTotal, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-end">Accommodation Total:</td>
                                            <td class="text-end"><strong>Rp {{ number_format($accommodationTotal, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="text-end"><h5 class="mb-0">Grand Total:</h5></td>
                                            <td class="text-end"><h5 class="mb-0">Rp {{ number_format($eventsTotal + $accommodationTotal, 0, ',', '.') }}</h5></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-4">
                        <div class="card manual-payment-card">
                            <div class="card-body">
                                <h5><strong>Manual Payment via Bank Mandiri</strong></h5>
                                <p><strong>Account Name:</strong> PRIMA GLORIA INDONESIA</p>
                                <p><strong>Bank Account Number:</strong> 123-456-7890</p>
                                <p><strong>Bank Branch:</strong> Bank Mandiri, Jakarta</p>
                                <p><strong>Amount:</strong> Rp {{ number_format($payment->amount) }}</p>
                                <p><strong>Payment Deadline:</strong> After registering, payment must be made within 1x24 hours.</p>
                                <p><strong>Note:</strong> After completing the payment, kindly upload the proof of payment on this page.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Payment Proof Section
                    @if ($payment->proof_of_transfer)
                        <div class="mb-4">
                            <h5>Payment Proof</h5>
                            <a href="{{ asset('storage/'.$payment->proof_of_transfer) }}" target="_blank">View Payment Proof</a>
                        </div>
                    @else -->
                        <div class="mb-4">
                            <h5>Upload Payment Proof</h5>
                            <div class="form-group">
                                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#uploadPaymentModal">
                                    Upload
                                </button>
                            </div>
                            <div class="modal fade" id="uploadPaymentModal" tabindex="-1" aria-labelledby="uploadPaymentModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold" id="uploadPaymentModalLabel">Payment Confirmation</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Upload Form -->
                                            <form id="paymentForm" action="{{ route('finish.registration', $payment->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf

                                                <div class="form-group">
                                                    <label for="invoice_number" class="fw-bold">Invoice Number</label>
                                                    <input type="text" name="invoice_number" id="invoice_number" class="form-control" value="{{ $payment->invoice_number }}" readonly>
                                                </div>

                                                <div class="form-group mt-3">
                                                    <label for="payment_total" class="fw-bold">Payment Total</label>
                                                    <input type="text" name="payment_total" id="payment_total" class="form-control" value="Rp {{ number_format($payment->amount, 0, ',', '.') }}" readonly>
                                                </div>

                                                <div class="form-group mt-3">
                                                    <label for="bank_name" class="fw-bold">Sending Bank Name<span class="text-danger">*</span></label>
                                                    <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Enter Bank Name" required>
                                                    @error('bank_name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Payment Date -->
                                                <div class="form-group mt-3">
                                                    <label for="payment_date" class="fw-bold">Payment Date<span class="text-danger">*</span></label>
                                                    <input type="date" 
                                                           name="payment_date" 
                                                           id="payment_date" 
                                                           class="form-control" 
                                                           min="{{ now()->format('Y-m-d') }}" 
                                                           value="{{ now()->format('Y-m-d') }}"
                                                           required>
                                                    <small class="text-muted">Select payment date (today or future date)</small>
                                                    @error('payment_date')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="form-group mt-3">
                                                    <label for="note" class="fw-bold">Note</label>
                                                    <input type="text" name="note" id="note" class="form-control" placeholder="Note (Optional)">
                                                    @error('note')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                        <small class="form-text text-muted" style="font-style: italic">
                                                            For GL Please Enter your Company Name.                                              
                                                        </small>
                                                   
                                                </div>

                                                <!-- Upload Proof -->
                                                <div class="form-group mt-3">
                                                    <label for="proof_of_transfer" class="fw-bold">Upload Proof of Transfer</label>
                                                    <input type="file" name="proof_of_transfer" id="proof_of_transfer" class="form-control" required>
                                                    <small class="form-text text-muted">
                                                        File must be in JPG, JPEG, or PNG format. Maximum file size is 1 MB.
                                                    </small>
                                                    @error('proof_of_transfer')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Button -->
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Save</button>
                                                    <!-- <button type="button" class="btn btn-primary">Save</button> -->
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Button Finish -->
                    @if(!$payment->proof_of_transfer)
                        <div class="mb-4">
                            <button id="finishRegistrationButton" class="btn btn-primary">Finish Registration</button>
                            <p>Want to pay later? <a href="{{ route('register.pay-later', ['payment_id' => $payment->id]) }}">Click here to pay later</a>.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<div id="loading-popup" class="loading-popup d-none">
    <div class="loading-popup-content">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-3">Processing, please wait...</p>
    </div>
</div>

<script>
   document.getElementById("finishRegistrationButton").addEventListener("click", function () {
        // Tampilkan popup
        const loadingPopup = document.getElementById("loading-popup");
        loadingPopup.classList.remove("d-none");

        // Disable tombol untuk mencegah klik berulang
        this.disabled = true;

        // Submit form
        document.getElementById("paymentForm").submit();
    });

</script>
@endsection
