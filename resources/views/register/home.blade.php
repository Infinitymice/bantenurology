@extends('layouts.app')

@section('title', 'Home - Event Registration')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body text-center">
                    <!-- Logo -->
                    <img src="{{ asset('logo/LOGO.png') }}" alt="Logo" class="mb-4 mx-auto d-block" style="width: 220px; height: auto;">
                    
                    <!-- Title -->
                    <h3 class="card-title mb-3" style="font-weight: bold; font-size: 2rem; color: #2C3E50;">Welcome to the Event Registration</h3>

                    <!-- Description Text -->
                    <p class="card-text mt-3" style="font-size: 1.1rem; color: #34495E;">
                        Register now and be a part of the 2nd Banten Urology Symposium!
                    </p>
                    
                    <!-- Buttons -->
                    <div class="d-flex justify-content-center mt-5">
                        <!-- Register Now Button -->
                        <a href="{{ route('register.step1') }}" class="btn btn-primary btn-lg mx-2 px-4 py-2" style="font-size: 1.1rem; border-radius: 30px; transition: all 0.3s ease;">
                            Register Now
                        </a>

                        <!-- Pay Later Button -->
                        <a href="{{ route('register.pay-laterInvoice') }}" class="btn btn-secondary btn-lg mx-2 px-4 py-2" style="font-size: 1.1rem; border-radius: 30px; transition: all 0.3s ease;">
                            Pay Later
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Additional CSS for better UI/UX -->
@section('styles')
<style>
    /* Background Gradient */
    body {
        background: linear-gradient(135deg, #A1C4FD 0%, #C2E9FB 100%);
    }

    /* Button Hover Effects */
    .btn-primary:hover {
        background-color: #2980B9;
        transform: translateY(-5px);
    }

    .btn-secondary:hover {
        background-color: #7F8C8D;
        transform: translateY(-5px);
    }

    /* Card Styling */
    .card {
        background-color: #ffffff;
        box-shadow: 0px 4px 30px rgba(0, 0, 0, 0.1);
        border-radius: 20px;
    }

    .card-body {
        padding: 40px;
    }

    /* Card Text */
    .card-text {
        color: #2C3E50;
        font-weight: 500;
    }

    /* Adjusting spacing */
    .container {
        padding-top: 60px;
        padding-bottom: 60px;
    }
</style>
@endsection
