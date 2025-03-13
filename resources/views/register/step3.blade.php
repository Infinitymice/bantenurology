@extends('layouts.app')

@section('title', 'Step 3 - Preview')

@section('content')


<div class="container mt-5">
@if ($errors->any())
                {!! implode(
                    '',
                    $errors->all('<div class="alert alert-danger alert-dismissible">
                                                                                                                                                                                                                                <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                                                                                                                                                                                                                :message
                                                                                                                                                                                                                            </div>'),
                ) !!}
            @endif
    <div class="row">
        <!-- Progress Bar -->
        <div class="col-12 mb-4">
            <div class="progress">
                <div class="progress-bar" style="width: 75%;" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">Step 4 of 5</div>
            </div>
        </div>

    

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center">Preview Data</h3>

                    <form action="{{ route('register.step3') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="full_name" class="bold-label">Full Name</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Full Name" value="{{ $formData['full_name'] }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="nik" class="bold-label">NIK / National Identity Number</label>
                            <input type="text" id="nik" name="nik" class="form-control" placeholder="NIK" value="{{ $formData['nik'] }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="institusi" class="bold-label">Affiliation Or Institution/City</label>
                            <input type="text" id="institusi" name="institusi" class="form-control" placeholder="Affiliation/Institution" value="{{ $formData['institusi'] }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="bold-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Email" value="{{ $formData['email'] }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="bold-label">Category</label>
                            <input type="category" id="category" name="category" class="form-control" placeholder="Category" value="{{ $formData['category'] }}" required>
                        </div>
                        <div class="mb-3" id="specialist-input" style="display: {{ old('category', $formData['category'] ?? '') === 'Specialist' ? 'block' : 'none' }}">
                            <label for="specialistDetail" class="bold-label">Sp.U (or other specialist)</label>
                            <input type="text" id="specialistDetail" name="specialistDetail" class="form-control" placeholder="Sp.U (or other specialist)" value="{{ $formData['specialistDetail'] }}">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="bold-label">Phone/WhatsApp</label>
                            <input type="text" id="phone" name="phone" class="form-control" placeholder="Phone/WhatsApp Number" value="{{ $formData['phone'] }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="bold-label">Address</label>
                            <input type="text" id="address" name="address" class="form-control" placeholder="Address" value="{{ $formData['address'] }}" required>
                        </div>
                        <div class="mb-3">
                            <h6 class="bold-label">Selected Events</h6>
                            <div class="row"> 
                            @foreach (session('categories_details') as $category)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6><strong>{{ $category['eventTypeName'] }} - {{ $category['name'] }}</strong></h6>
                                            @if($category['discountPercentage'] > 0)
                                                <p>Original Price: <del>Rp. {{ number_format($category['originalPrice']) }}</del></p>
                                                <p>Discount: {{ $category['discountPercentage'] }}%</p>
                                                <p>Final Price: Rp. {{ number_format($category['price']) }}</p>
                                            @else
                                                <p>Price: Rp. {{ number_format($category['price']) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                            <div class="alert alert-info">
                                <strong>Total Events: Rp. {{ number_format(array_sum(array_column(session('categories_details'), 'price'))) }}</strong>
                            </div>
                        </div>
                        @if(session('accommodation_booking'))
                            <div class="mb-3">
                                <h6 class="bold-label">Selected Accommodation</h6>
                                <div class="row">
                                    @php
                                        $checkIn = \Carbon\Carbon::parse(session('accommodation_booking.check_in_date'));
                                        $checkOut = \Carbon\Carbon::parse(session('accommodation_booking.check_out_date'));
                                        $nights = $checkOut->diffInDays($checkIn);
                                        $totalAccommodationPrice = 0;
                                    @endphp
                                    
                                    @foreach(session('accommodation_booking.rooms') as $roomId => $room)
                                        @php
                                            $roomTotal = $room['price'] * $room['quantity'] * $nights;
                                            $totalAccommodationPrice += $roomTotal;
                                        @endphp
                                        <div class="col-md-4 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6><strong>{{ $room['room_type'] }}</strong></h6>
                                                    <p>Price/night: Rp. {{ number_format($room['price']) }}</p>
                                                    <p>Quantity: {{ $room['quantity'] }} room(s)</p>
                                                    <p>Check-in: {{ $checkIn->format('d-m-Y') }}</p>
                                                    <p>Check-out: {{ $checkOut->format('d-m-Y') }}</p>
                                                    <p>Total nights: {{ $nights }}</p>
                                                    <p class="fw-bold">Subtotal: Rp. {{ number_format($roomTotal) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <strong>Total Accommodation: Rp. {{ number_format($totalAccommodationPrice) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @php
                            $totalPrice = array_sum(array_column(session('categories_details'), 'price'));
                            if(session('accommodation_booking')) {
                                $totalPrice += $totalAccommodationPrice ?? 0;
                            }
                        @endphp
                        <div class="alert alert-success">
                            <strong>Total Price: Rp. {{ number_format($totalPrice) }}</strong>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mt-3">Submit</button>
                    </form>
                </div>
            </div>
        </div>   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
