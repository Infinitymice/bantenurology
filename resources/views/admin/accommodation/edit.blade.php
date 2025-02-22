@extends('admin.template.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit Akomodasi</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.accommodation.update', $accommodation->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                    name="name" value="{{ old('name', $accommodation->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                    name="description" required>{{ old('description', $accommodation->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Harga</label>
                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                    name="price" value="{{ old('price', $accommodation->price) }}" required min="0">
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah Kamar</label>
                <input type="number" class="form-control @error('qty') is-invalid @enderror" 
                    name="qty" value="{{ old('qty', $accommodation->qty) }}" required min="1">
                @error('qty')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" 
                        id="is_active" {{ old('is_active', $accommodation->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label">Aktif</label>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.accommodation.index') }}" class="btn btn-secondary me-2">Kembali</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection