<!-- resources/views/admin/home.blade.php -->
@extends('admin.layoutshome.app')

@section('title', 'Home Page')

@section('content')
<div class="container mt-5">
    <div class="row">
        <!-- Overview Section -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Overview</h4>
                </div>
                <div class="card-body">
                    <p><strong>Total Users:</strong> 150</p>
                    <p><strong>Total Absensi:</strong> 1000</p>
                    <p><strong>Last Login:</strong> Today, 12:30 PM</p>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Recent Activities</h4>
                </div>
                <div class="card-body">
                    <ul>
                        <li>User John Doe logged in at 11:00 AM</li>
                        <li>QR Code for Absensi generated at 09:30 AM</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Quick Actions</h4>
                </div>
                <div class="card-body">
                    <a href="{{ route('qr.index') }}" class="btn btn-success btn-block">Search & Print QR</a>
                    <a href="{{ route('qr.indexScan') }}" class="btn btn-info btn-block">Absensi QR Scan</a>
                    <a href="{{ route('admin.auth.login') }}" class="btn btn-primary btn-block">Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
