@extends('admin.template.admin')

@section('title', 'Edit Data Peserta')

@section('content')

<div class="card">
    <div class="card-header">
        <h5>Edit Data Peserta</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.participants.update', $participant->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="full_name">Nama Lengkap</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="{{ old('full_name', $participant->full_name) }}" required>
            </div>

            <div class="form-group">
                <label for="nik">NIK</label>
                <input type="text" class="form-control" id="nik" name="nik" value="{{ old('nik', $participant->nik) }}" required>
            </div>

            <div class="form-group">
                <label for="institusi">Institusi</label>
                <input type="text" class="form-control" id="institusi" name="institusi" value="{{ old('institusi', $participant->institusi) }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $participant->email) }}" required>
            </div>

            <div class="form-group">
                <label for="phone">Telepon</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $participant->phone) }}" required>
            </div>

            <div class="form-group">
                <label for="category">Kategori</label>
                <select id="category" name="category" class="form-control" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Student" {{ old('category', $participant->category) == 'Student' ? 'selected' : '' }}>Student</option>
                    <option value="General Practitioner/Resident" {{ old('category', $participant->category) == 'General Practitioner/Resident' ? 'selected' : '' }}>General Practitioner/Resident</option>
                    <option value="Specialist" {{ old('category', $participant->category) == 'Specialist' ? 'selected' : '' }}>Specialist</option>
                </select>
            </div>

            <div class="form-group">
                <label for="address">Alamat</label>
                <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $participant->address) }}" required>
            </div>
            <div class="mb-3">
                <label for="source" class="bold-label">Source</label>
                <select id="source" name="source" class="form-control">
                    <option value="" selected>Choose Source</option>
                    <option value="On Site">On Site</option>
                    <option value="Register Online">Register Online</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Simpan Perubahan</button>
        </form>
    </div>
</div>

@endsection
