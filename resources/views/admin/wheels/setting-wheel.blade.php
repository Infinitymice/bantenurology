@extends('admin.template.admin')

@section('content')
<div class="container">
    <h1>Setting Wheel</h1>

    <!-- Form Input Angka -->
    <form action="{{ route('admin.setting-wheel') }}" method="POST" class="mb-4">
        @csrf
        <div class="form-group">
            <label for="jumlah">Masukkan jumlah peserta yang akan di undi:</label>
            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Submit</button>
    </form>

    <!-- Tampilkan Data Registrasi -->
    @isset($registrasis)
        <h2>Data Registrasi ({{ $jumlah }} teratas berdasarkan nama):</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($registrasis as $index => $registrasi)
                    <tr>
                        <td>{{ $index + 1 }}</td> <!-- Nomor Urut -->
                        <td>{{ $registrasi->full_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endisset

</div>
@endsection
