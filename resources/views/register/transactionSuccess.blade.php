@extends('layouts.app')

@section('title', 'Transaction Successful')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success text-center" role="alert">
                <h4 class="alert-heading">Transaction Successful!</h4>
                <p>Thank you for registering 2nd BUS (Banten Urology Symposium). Stay tuned for more updates and exciting announcements.</p>
                <hr>
                <p class="mb-0">You will receive a confirmation email shortly. If you have any questions, please contact our registration team.</p>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12 text-center">
            <a href="{{ route('user-home') }}" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
</div>
@endsection
