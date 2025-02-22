@extends('admin.template.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Manage Invoices</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="invoiceTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Invoice</th>
                    <th>Nama Peserta</th>
                    <th>Jumlah Pembayaran</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#invoiceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.invoices.data') }}",
            type: "GET",
            error: function(xhr, status, error) {
                if (xhr.status == 401) {
                    alert('You are not authorized to view this data. Please log in.');
                    window.location.href = '/login';
                } else {
                    alert('An error occurred: ' + xhr.status + ' ' + error);
                }
            }
        },
        columns: [
            { 
                    data: null, 
                    name: 'nomor',
                    orderable: false, 
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1; // Nomor otomatis
                    }
            },
            { data: 'invoice_number', name: 'invoice_number' },
            { data: 'registrasi.full_name', name: 'registrasi.full_name' },
            { data: 'amount', name: 'amount', render: function(data) {
                return 'Rp ' + data.toLocaleString('id-ID', { minimumFractionDigits: 2 });
            }},
            { data: 'status_label', name: 'status' },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                render: function(data) {
                    return data;
                }
            }
        ]
    });

    // Customize position of search and show entries
    $('.dataTables_length').addClass('float-left');
    $('.dataTables_filter').addClass('float-right');
});

function downloadInvoice(invoiceNumber) {
    fetch(`{{ url('admin/invoices/download') }}/${invoiceNumber}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/pdf'
        }
    })
    .then(response => {
        if (!response.ok) {
            if (response.status === 404) {
                throw new Error('Invoice tidak ditemukan');
            }
            throw new Error('Terjadi kesalahan saat mengunduh invoice');
        }
        return response.blob();
    })
    .then(blob => {
        if (blob.size === 0) {
            throw new Error('File kosong');
        }
        
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = `invoice_${invoiceNumber}.pdf`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Download error:', error);
        alert(error.message || 'Gagal mengunduh invoice. Silakan coba lagi.');
    });
}
</script>
@endpush
