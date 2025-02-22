@extends('admin.template.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Tambah Akomodasi</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.accommodation.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                    name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                    name="description" required>{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Harga</label>
                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                    name="price" value="{{ old('price') }}" required min="0">
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah Kamar</label>
                <input type="number" class="form-control @error('qty') is-invalid @enderror" 
                    name="qty" value="{{ old('qty') }}" required min="1">
                @error('qty')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                        id="is_active" {{ old('is_active') ? 'checked' : '' }}>
                    <label class="form-check-label">Aktif</label>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.accommodation.index') }}" class="btn btn-secondary me-2">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection