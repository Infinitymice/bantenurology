@extends('admin.template.admin')

@section('content')
<div class="card">
    <div class="card">
        <div class="card-header">
            <h5>Data Akomodasi</h5>
            <a href="{{ route('admin.accommodation.create') }}" class="btn btn-primary mt-3">
                Tambah Akomodasi Baru
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="accommodations-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Harga</th>
                    <th>Jumlah Kamar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#accommodations-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.accommodation.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { data: 'price', name: 'price' },
            { data: 'qty', name: 'qty' },
            { data: 'is_active', name: 'is_active' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json"
        },
        dom: '<"row"<"col-sm-4"f><"col-sm-4 text-center"l><"col-sm-4 text-right"B>>t<"row"<"col-sm-6"i><"col-sm-6"p>>',
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Export Excel',
                className: 'btn btn-success'
            }
        ]
    });

    // Delete button handler
    $(document).on('click', '.delete-btn', function() {
        if (confirm('Apakah Anda yakin ingin menghapus akomodasi ini?')) {
            let id = $(this).data('id');
            $.ajax({
                url: `{{ url('admin/accommodation') }}/${id}`,
                method: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function() {
                    table.ajax.reload();
                    alert('Akomodasi berhasil dihapus!');
                }
            });
        }
    });
});
</script>
@endpush