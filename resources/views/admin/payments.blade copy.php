@extends('admin.template.admin')

@section('title', 'Data Pembayaran')

@section('content')

<div class="card">
    <div class="card-header">
        <h5>Data Pembayaran</h5>
            <div class="row">
                    <div class="col-md-3">
                        <select id="paymentStatusFilter" class="form-control">
                            <option value="">Pilih Status Pembayaran</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                            <option value="canceled">Canceled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="sourceFilter" class="form-control">
                            <option value="">Pilih Source</option>
                            <option value="onsite">Onsite</option>
                            <option value="web">Web</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
            </div>
    </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="payments-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>NIK</th>
                            <th>Email</th>
                            <th>Kategori</th>
                            <th>Telepon</th>
                            <th>Jenis Event</th>
                            <th>Nomor Invoice</th>
                            <th>Jumlah Pembayaran</th>
                            <th>Bank Pembayaran</th>
                            <th>Tanggal Pembayaran</th>
                            <th>Note</th>
                            <th>Status Pembayaran</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
                
                @foreach ($participants as $participant)
                    <div class="modal fade" id="paymentProofModal{{ $participant->id }}" data-id="{{ $participant->id }}" tabindex="-1" role="dialog" aria-labelledby="paymentProofModalLabel{{ $participant->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="paymentProofModalLabel{{ $participant->id }}">Detail Bukti Pembayaran</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div id="loading-spinner-{{ $participant->id }}" class="loading-spinner d-none">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <p>Memproses pembaruan, harap tunggu...</p>
                                </div>
                                <div class="modal-body">
                                    <!-- Informasi Peserta -->
                                    <div class="row mb-3">
                                        <div class="col-4 font-weight-bold">Nomor Invoice:</div>
                                        <div class="col-8" id="invoice-number{{ $participant->id }}">
                                            {{ optional($participant->payments->first())->invoice_number ?? 'Belum ada invoice' }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-4 font-weight-bold">Nama Peserta:</div>
                                        <div class="col-8">{{ $participant->full_name }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-4 font-weight-bold">Jumlah Pembayaran:</div>
                                        <div class="col-8">
                                            {{ $participant->payments->first() ? number_format($participant->payments->first()->amount, 2, ',', '.') : 'Belum ada pembayaran' }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-4 font-weight-bold">Bank Pembayaran:</div>
                                        <div class="col-8">
                                            @if (optional($participant->payments->first())->bank_name)
                                                {{ optional($participant->payments->first())->bank_name }}
                                            @else
                                                <input type="text" id="bank_name{{ $participant->id }}" name="bank_name" class="form-control" placeholder="Masukkan nama bank" value="{{ optional($participant->payments->first())->bank_name }}" required>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-4 font-weight-bold">Tanggal Pembayaran:</div>
                                        <div class="col-8">
                                            @if (optional($participant->payments->first())->payment_date)
                                                {{ \Carbon\Carbon::parse(optional($participant->payments->first())->payment_date)->format('d-m-Y') }}
                                            @else
                                                <input type="date" id="payment_date{{ $participant->id }}" name="payment_date" class="form-control" value="{{ optional($participant->payments->first())->payment_date }}">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-4 font-weight-bold">Status Pembayaran:</div>
                                        <div class="col-8">
                                            <select class="form-control" id="paymentStatus{{ $participant->id }}">
                                                <option value="pending" {{ optional($participant->payments->first())->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="paid" {{ optional($participant->payments->first())->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                                <option value="completed" {{ optional($participant->payments->first())->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="failed" {{ optional($participant->payments->first())->status == 'failed' ? 'selected' : '' }}>Failed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3" id="failedReasonRow{{ $participant->id }}" style="display: none;">
                                        <div class="col-4 font-weight-bold">Catatan:</div>
                                        <div class="col-8">
                                            <textarea class="form-control" id="failedReason{{ $participant->id }}" placeholder="Masukkan alasan pembayaran gagal">{{ optional($participant->payments->first())->failed_reason }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Gambar Bukti Pembayaran -->
                                    <div class="text-center">
                                        @if ($participant->payments->first() && $participant->payments->first()->proof_of_transfer)
                                            <img id="proofImage{{ $participant->id }}" 
                                                src="{{ asset('storage/proof_of_transfer/' . $participant->payments->first()->proof_of_transfer) }}" 
                                                alt="Bukti Pembayaran" 
                                                style="max-width: 100%; height: auto; cursor: pointer;">
                                        @else
                                            <p class="text-danger">Bukti pembayaran belum ada.</p>
                                        @endif
                                    </div>

                                    <!-- Form untuk Unggah Bukti Pembayaran -->
                                    @if (!$participant->payments->first() || !$participant->payments->first()->proof_of_transfer)
                                        <form action="{{ route('admin.upload.payment.proof') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                                            @csrf
                                            <input type="hidden" name="participant_id" value="{{ $participant->id }}">
                                            <div class="form-group">
                                                <label for="proof_of_transfer">Unggah Bukti Pembayaran</label>
                                                    <input type="file" id="proof_of_transfer{{ $participant->id }}" name="proof_of_transfer" class="form-control">
                                            </div>
                                            <button type="submit" class="btn btn-success">Unggah Bukti</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="text-center mt-3">
                                        <button class="btn btn-sm btn-primary" onclick="rotateImage({{ $participant->id }}, -90)">Rotate Left</button>
                                        <button class="btn btn-sm btn-primary" onclick="rotateImage({{ $participant->id }}, 90)">Rotate Right</button>
                                        <button class="btn btn-primary zoom-in" onclick="zoomImage({{ $participant->id }}, 0.1)">Zoom In</button>
                                        <button class="btn btn-danger zoom-out" onclick="zoomImage({{ $participant->id }}, -0.1)">Zoom Out</button>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                    <button type="button" class="btn btn-success btn-update-status" data-id="{{ $participant->id }}">Simpan Status</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
            </div>
        </div>
    </div>

   
@endsection

@push('scripts')

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>


<script>
   $(document).ready(function () {
    var table = $('#payments-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.participant.dataPayments") }}',
            data: function(d) {
                // Menambahkan parameter filter status pembayaran ke query data
                d.payment_status = $('#paymentStatusFilter').val(); // Ambil nilai filter status pembayaran
                d.source = $('#sourceFilter').val(); // Ambil nilai filter source
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
            { data: 'full_name', name: 'full_name' },
            { data: 'nik', name: 'nik' },
            { data: 'email', name: 'email' },
            { data: 'category', name: 'category' },
            { data: 'phone', name: 'phone' },
            { data: 'event_type', name: 'event_type', render: function(data) { return data ? data : '-'; } },
            { data: 'invoice_number', name: 'invoice_number' },
            { data: 'amount', name: 'amount' },
            { data: 'bank_name', name: 'bank_name' },
            { data: 'payment_date', name: 'payment_date'},
            { data: 'note', name: 'note'},
            { 
                data: 'status', 
                name: 'status', 
                render: function(data, type, row) {
                    let statusClass = '';
                    switch (data) {
                        case 'completed':
                            statusClass = 'bg-completed status-background';
                            break;
                        case 'paid':
                            statusClass = 'bg-paid status-background';
                            break;
                        case 'pending':
                            statusClass = 'bg-pending status-background';
                            break;
                        case 'failed':
                            statusClass = 'bg-failed status-background';
                            break;
                        case 'canceled':
                            statusClass = 'bg-canceled status-background';
                            break;
                        default:
                            statusClass = 'status-background';
                    }
                    return `<span class="badge ${statusClass}">${data}</span>`;
                }
            },
            { 
                data: null, 
                name: 'action', 
                orderable: false, 
                searchable: false, 
                render: function(data, type, row) {
                    if (row.proof_of_transfer) {
                        return `
                            <button class="btn btn-primary" data-toggle="modal" data-target="#paymentProofModal${row.id}">Lihat Bukti Pembayaran</button>
                        `;
                    } else {
                        return `
                            <button class="btn btn-warning btn-edit-proof" data-id="${row.id}" data-toggle="modal" data-target="#paymentProofModal${row.id}">
                                Unggah Bukti Pembayaran
                            </button>
                        `;
                    }
                }

            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
        },
        dom: 'Bfrtip',
        buttons: [
            {
                text: 'Export Excel',
                className: 'btn btn-success',
                action: function (e, dt, button, config) {
                    $.ajax({
                        url: '{{ route("admin.participant.dataPayments") }}',
                        method: 'GET',
                        data: {
                            export_all: true, // Parameter untuk ekspor semua data
                            payment_status: $('#paymentStatusFilter').val(), // Kirim status pembayaran
                            source: $('#sourceFilter').val()
                        },
                        success: function (response) {
                            // Tambahkan kolom nomor urut di data
                            const numberedData = response.map((row, index) => ({
                                No: index + 1, // Nomor urut
                                ...row // Gabungkan data lainnya
                            }));

                            // Konversi data JSON ke worksheet SheetJS
                            const worksheet = XLSX.utils.json_to_sheet(numberedData);

                            // Tambahkan gaya bold pada header (baris pertama)
                            const range = XLSX.utils.decode_range(worksheet['!ref']);
                            for (let C = range.s.c; C <= range.e.c; ++C) {
                                const cellAddress = XLSX.utils.encode_cell({ r: 0, c: C }); // Header ada di baris pertama
                                if (!worksheet[cellAddress]) continue; // Skip jika tidak ada cell
                                worksheet[cellAddress].s = {
                                    font: { bold: true }, // Gaya bold
                                    alignment: { horizontal: "center", vertical: "center" } // Rata tengah
                                };
                            }

                            // Atur lebar kolom menyesuaikan teks
                            const colWidths = Object.keys(numberedData[0]).map((key) => {
                                const maxWidth = Math.max(
                                    ...numberedData.map((row) => (row[key] ? row[key].toString().length : 10)), // Panjang data
                                    key.length // Panjang header
                                );
                                return { wch: maxWidth + 2 }; // Tambahkan padding
                            });

                            worksheet['!cols'] = colWidths; // Atur properti kolom

                            // Buat workbook dan tambahkan worksheet
                            const workbook = XLSX.utils.book_new();
                            XLSX.utils.book_append_sheet(workbook, worksheet, 'Data Pembayaran');

                            // Simpan file Excel
                            XLSX.writeFile(workbook, 'data_pembayaran.xlsx');
                        },
                        error: function (xhr, status, error) {
                            alert('Gagal mengunduh data.');
                            console.error(error);
                        }
                    });
                }
            }
        ]
    });

    // Ketika filter status pembayaran berubah, reload tabel dengan filter yang baru
    $('#paymentStatusFilter, #sourceFilter').change(function () {
        table.ajax.reload(); // Reload tabel dengan filter yang baru
    });


    // Modal event listener
    $('#participants-table').on('click', '.view-proof', function () {
        var proofUrl = $(this).data('proof'); // Ambil URL bukti pembayaran
        var participantName = $(this).data('name');
        var paymentAmount = $(this).data('amount');
        var paymentStatus = $(this).data('status');
        var failedReason = $(this).data('failed_reason') || '';
       


        $('#proof-image').attr('src', proofUrl);  // Menampilkan bukti pembayaran pada modal
        $('#participant-name').text(participantName);  // Nama peserta
        $('#payment-amount').text(paymentAmount);  // Jumlah pembayaran
        $('#payment-status').val(paymentStatus); 
        $('#failedReason').val(failed_reason);  // Status pembayaran


        $('#proofModal').modal('show');
    });

    // Update status pembayaran dengan tombol dinamis
$(document).on('click', '.btn-update-status', function () {
    const participantId = $(this).data('id');
    const updatedStatus = $('#paymentStatus' + participantId).val();
    const invoiceNumber = $('#invoice-number' + participantId).text();
    const failedReason = $('#failedReason' + participantId).val() || '';
    const bankName = $('#bank_name' + participantId).val();
    const proofOfTransfer = $('#proof_of_transfer' + participantId).prop('files') ? $('#proof_of_transfer' + participantId).prop('files')[0] : null;

    // Tangani payment_date
    const paymentDateInput = $('#payment_date' + participantId);
    let paymentDate = paymentDateInput.val();

    // Jika `payment_date` kosong, tambahkan tanggal sekarang
    if (!paymentDate) {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0'); // Bulan dimulai dari 0
        const day = String(now.getDate()).padStart(2, '0');
        paymentDate = `${year}-${month}-${day}`; // Format YYYY-MM-DD
        paymentDateInput.val(paymentDate); // Tambahkan ke input supaya user bisa lihat
    }

    // Tampilkan spinner
    $('#loading-spinner-' + participantId).removeClass('d-none');

    // Nonaktifkan tombol
    $(this).prop('disabled', true);

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}'); // CSRF token
    formData.append('participant_id', participantId);
    formData.append('status', updatedStatus);
    formData.append('invoice_number', invoiceNumber);
    formData.append('failed_reason', failedReason);
    formData.append('bank_name', bankName);
    formData.append('payment_date', paymentDate);

    // Tambahkan file jika ada
    if (proofOfTransfer) {
        formData.append('proof_of_transfer', proofOfTransfer);
    }

    // Kirim AJAX
    $.ajax({
        url: '{{ route("admin.update.payment.status") }}',
        type: 'POST',
        data: formData,
        processData: false, // Jangan proses data menjadi string
        contentType: false,
        success: function (response) {
            alert('Status pembayaran berhasil diperbarui!');
            $('#paymentProofModal' + participantId).modal('hide');
            table.ajax.reload(); // Reload the table
        },
        
        error: function (xhr) {
            console.error('Full Error Response:', xhr.responseText);
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.message) {
                    alert('Terjadi kesalahan: ' + response.message);
                } else {
                    alert('Terjadi kesalahan yang tidak diketahui.');
                }
            } catch (e) {
                alert('Respons server tidak dapat diproses.');
                console.error('Parsing error:', e);
            }
        },
        complete: function () {
            // Sembunyikan spinner
            $('#loading-spinner-' + participantId).addClass('d-none');

            // Aktifkan kembali tombol
            $('.btn-update-status').prop('disabled', false);
        }
    });
});

});
 


//Fungsi Untuk Rotate dan zoom
let croppers = {}; // Simpan cropper untuk setiap gambar

function initializeCropper(participantId) {
    const image = document.getElementById(`proofImage${participantId}`);
    if (croppers[participantId]) {
        croppers[participantId].destroy();
    }
    croppers[participantId] = new Cropper(image, {
        viewMode: 1,
        responsive: true,
        zoomable: true,
        rotatable: true,
        scalable: true,
        background: false
    });
}

function rotateImage(participantId, degree) {
    if (croppers[participantId]) {
        croppers[participantId].rotate(degree);
    }
}

function zoomImage(participantId, ratio) {
    if (croppers[participantId]) {
        croppers[participantId].zoom(ratio);
    }
}

// Inisialisasi Cropper.js saat modal dibuka
$('.modal').on('shown.bs.modal', function (e) {
    const modalId = $(this).attr('id'); // Ambil ID modal
    const participantId = modalId.replace('paymentProofModal', ''); // Ekstrak ID peserta
    initializeCropper(participantId);
});

// Hapus cropper saat modal ditutup
$('.modal').on('hidden.bs.modal', function (e) {
    const modalId = $(this).attr('id');
    const participantId = modalId.replace('paymentProofModal', '');
    if (croppers[participantId]) {
        croppers[participantId].destroy();
        delete croppers[participantId];
    }
});

//Munculkan text input catatan
$(document).ready(function () {

    // Ketika status pembayaran berubah
    $(document).on('change', '[id^="paymentStatus"]', function () {
        const participantId = $(this).attr('id').replace('paymentStatus', '');
        const selectedStatus = $(this).val();

        // Tampilkan atau sembunyikan text field alasan
        if (selectedStatus === 'failed') {
            $(`#failedReasonRow${participantId}`).show();
        } else {
            $(`#failedReasonRow${participantId}`).hide();
        }
    });

    // Ketika modal dibuka kembali, periksa status dan tampilkan alasan jika diperlukan
    $(document).on('show.bs.modal', '.modal', function () {
        const participantId = $(this).data('id'); // Ambil ID peserta dari modal
        const selectedStatus = $(`#paymentStatus${participantId}`).val(); // Ambil status pembayaran dari dropdown

        // Tampilkan atau sembunyikan text field alasan berdasarkan status pembayaran saat ini
        if (selectedStatus === 'failed') {
            $(`#failedReasonRow${participantId}`).show();
        } else {
            $(`#failedReasonRow${participantId}`).hide();
        }
    });



    // // Update status pembayaran dengan catatan
    // $(document).on('click', '.btn-success', function () {
    //     const participantId = $(this).data('id');
    //     const updatedStatus = $(`#paymentStatus${participantId}`).val();
    //     const invoiceNumber = $(`#invoice-number${participantId}`).text();
    //     const failedReason = $(`#failedReason${participantId}`).val() || '';
        

    //     console.log({
    //         _token: '{{ csrf_token() }}',
    //         participant_id: participantId,
    //         status: updatedStatus,
    //         invoice_number: invoiceNumber,
    //         failed_reason: failedReason
    //     });


    //     $.ajax({
    //         url: '{{ route("admin.update.payment.status") }}',
    //         type: 'POST',
    //         data: {
    //             _token: '{{ csrf_token() }}',
    //             participant_id: participantId,
    //             status: updatedStatus,
    //             invoice_number: invoiceNumber,

    //             failed_reason: failedReason // Send the failed reason if applicable
    //         },
    //         success: function (response) {
    //             alert('Status pembayaran berhasil diperbarui!');
    //             $('#paymentProofModal' + participantId).modal('hide');
    //             table.ajax.reload(); // Reload the table
    //         },
    //         error: function (xhr) {
    //             console.log(xhr.responseJSON); // Log the response to see validation errors
    //             alert('Terjadi kesalahan saat memperbarui status pembayaran: ' + xhr.responseJSON.message);
    //         }
    //     });
    // });
});



</script>
@endpush
