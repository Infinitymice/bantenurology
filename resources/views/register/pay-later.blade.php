<!-- resources/views/register/pay-later.blade.php -->

@extends('layouts.app')

@section('title', 'Pay Later - Enter Invoice')

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


    <div class="d-flex align-items-center mb-4">
        <h2><strong>Pay Later</strong></h2>
    </div>
    <!-- Form untuk memasukkan nomor invoice -->
    <form action="{{ route('register.pay-later.details') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="invoice_number"><strong>Invoice Number</strong></label>
            <input type="text" name="invoice_number" id="invoice_number" class="form-control" required>
        </div>
        <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('user-home') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>

                <button type="submit" class="btn btn-primary">
                    Submit
                </button>
        </div>
    </form>
</div>

@endsection
