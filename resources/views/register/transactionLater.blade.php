@extends('layouts.app')

@section('title', 'Transaction Deferred')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success text-center" role="alert">
                <h4 class="alert-heading">Transaction Pending!</h4>
                <p>Thank you for your registration. A confirmation email will be sent shortly.</p>
                <hr>
                <p class="mb-0">If you have any questions, please contact our registration team.</p>
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
