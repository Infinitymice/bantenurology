@extends('admin.template.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Daftar QR Code</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="qrCodeTable">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 30%;">Name</th>
                    <th style="width: 20%;">NIK</th>
                    <th style="width: 25%;">QR Code</th>
                    <th style="width: 20%;">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#qrCodeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.qr-codes.data') }}",
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
            { data: 'name', name: 'name' },
            { data: 'nik', name: 'nik' },
            {
                data: 'qr_code',
                render: function(data, type, row) {
                    const imgUrl = `/storage/qr-codes/${data}`;
                    return `
                        <img src="${imgUrl}" alt="QR Code" id="qrCode${row.id}" style="width: 20mm;">
                        <span id="name${row.id}" style="display: none;">${row.name}</span>`;
                }
            },
            { data: 'action', name: 'action' }
        ]
    });
});

function printQrCode(qrCodeId, nameId) {
    Swal.fire({
        title: 'Sedang Mencetak QR',
        text: 'Mohon tunggu...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => { Swal.showLoading(); }
    });

    const qrCodeElement = document.getElementById(qrCodeId);
    const nameElement = document.getElementById(nameId);

    if (!qrCodeElement || !nameElement) {
        console.error('Elemen tidak ditemukan:', qrCodeId, nameId);
        Swal.fire('Error', 'Elemen tidak ditemukan!', 'error');
        return;
    }

    const qrCodeUrl = qrCodeElement.src;
    const fullName = nameElement.innerText;

    const printArea = `
        <div style="text-align: center; width: 100%;">
            <div>
                ${fullName}
                <hr style="border: 1px solid black; width: 100%; margin: 0; padding: 0;">
                <img src="${qrCodeUrl}" alt="QR Code" style="width: 20mm; margin-top: 2mm;">
            </div>
        </div>
    `;

    const printFrame = document.createElement('iframe');
    document.body.appendChild(printFrame);
    printFrame.style.display = 'none';
    printFrame.contentDocument.write(`
        <html>
            <title>Cetak QR Code</title>
            <body>
            <style>
               @page {
                    padding-top: 3mm;
                    margin: 0;
                }

                body {
                    margin: 0;
                    padding-top: 3mm;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    font-family: Arial, sans-serif;
                    text-align: center;

                }

                div {
                    width: 100%;
                    margin: 0 auto;
                }
                img {
                    max-width: 30mm;
                    max-height: 30mm;
                }
            </style>
            ${printArea}
        </body>
        </html>
    `);

    printFrame.contentDocument.close();

    printFrame.contentWindow.onload = () => {
        printFrame.contentWindow.print();
        Swal.close();
        document.body.removeChild(printFrame);
    };
}

</script>
@endpush
