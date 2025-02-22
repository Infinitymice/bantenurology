@extends('admin.template.admin')

@section('title', 'Data Peserta')

@section('content')

<div class="card">
<div class="card">
        <div class="card-header">
            <h5>Data Peserta</h5>
                <!-- Filter berdasarkan status pembayaran -->
                <div class="row">
                            <div class="col-md-3">
                                <select id="participantsStatusFilter" class="form-control">
                                    <option value="">Pilih Status Pembayaran</option>
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="completed">Completed</option>
                                    <option value="failed">Failed</option>
                                    <option value="canceled">Canceled</option>
                                </select>
                            </div>
                        </div>
                </div>              
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addParticipantModal">Tambah Peserta</button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="participants-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>NIK</th>
                            <th>Institusi</th>
                            <th>Email</th>
                            <th>Kategori</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th>Jenis Event</th>
                            <th>Nomor Invoice</th>
                            <th>Jumlah Pembayaran</th>
                            <th>Status Pembayaran</th>
                            <th>Source</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
                
                @foreach ($participants as $participant)
                    <div class="modal fade" id="paymentProofModal{{ $participant->id }}" data-id="{{ $participant->id }}" tabindex="-1" role="dialog" aria-labelledby="paymentProofModalLabel{{ $participant->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="paymentProofModalLabel{{ $participant->id }}">Detail Bukti Pembayaran</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
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
                                        <div class="col-8" id="participant-name{{ $participant->id }}">{{ $participant->full_name }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-4 font-weight-bold">Jumlah Pembayaran:</div>
                                        <div class="col-8" id="payment-amount{{ $participant->id }}">
                                            {{ $participant->payments->first() ? number_format($participant->payments->first()->amount, 2, ',', '.') : 'Belum ada pembayaran' }}
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
                                            <textarea class="form-control" id="failedReason{{ $participant->id }}" placeholder="Masukkan alasan pembayaran gagal">{{ optional($participant->payments->first())->failed_reason ?? 'Belum Ada Pembayaran' }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Gambar Bukti Pembayaran
                                    <div class="text-center">
                                        @if ($participant->payments->first() && $participant->payments->first()->proof_of_transfer)
                                            <img id="proofImage{{ $participant->id }}" 
                                                src="{{ asset('storage/proof_of_transfer/' . $participant->payments->first()->proof_of_transfer) }}" 
                                                alt="Bukti Pembayaran" 
                                                style="max-width: 100%; height: auto; cursor: pointer;">
                                        @else
                                            <p class="text-danger">Bukti pembayaran belum ada.</p>
                                        @endif
                                    </div> -->

                                    <!-- Kontrol Zoom dan Rotate
                                    <div class="text-center mt-3">
                                        <button class="btn btn-sm btn-primary" onclick="rotateImage({{ $participant->id }}, -90)">Rotate Left</button>
                                        <button class="btn btn-sm btn-primary" onclick="rotateImage({{ $participant->id }}, 90)">Rotate Right</button>
                                        <button class="btn btn-sm btn-success" onclick="zoomImage({{ $participant->id }}, 0.1)">Zoom In</button>
                                        <button class="btn btn-sm btn-danger" onclick="zoomImage({{ $participant->id }}, -0.1)">Zoom Out</button>
                                    </div> -->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                    <button type="button" class="btn btn-success" data-id="{{ $participant->id }}">Simpan Status</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal Import Peserta -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data Peserta</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.import-participants') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="file">Pilih File CSV</label>
                        <input type="file" name="file" class="form-control" accept=".csv" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Impor Peserta</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Tambah Peserta -->
<div class="modal fade" id="addParticipantModal" tabindex="-1" role="dialog" aria-labelledby="addParticipantModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addParticipantModalLabel">Tambah Peserta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addParticipantForm">
                    @csrf
                    <div class="mb-3">
                        <label for="full_name" class="bold-label">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Full Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="nik" class="bold-label">NIK / National Identity Number</label>
                        <input type="text" id="nik" name="nik" class="form-control" placeholder="NIK" required>
                    </div>
                    <div class="mb-3">
                        <label for="institusi" class="bold-label">Affiliation Or Institution/City</label>
                        <input type="text" id="institution" name="institution" class="form-control" placeholder="Affiliation/Institution" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="bold-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="bold-label">Category</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="">Choose Category Type</option>
                            <option value="Resident">RESIDENT</option>
                            <option value="GP / PARAMEDIC, HEALTHCARE PROFESSIONALS">GP / PARAMEDIC, HEALTHCARE PROFESSIONALS ( Symposium Only )</option>
                            <option value="Specialist">SPECIALIST ( Symposium & Demonstration & Lectures )</option>
                            <option value="Overseas Participant ( Spesialist )">Overseas Participant ( Spesialist )</option>
                            <option value="Overseas Participant ( General Practitioner / Resident )">Overseas  Participant ( General Practitioner / Resident )</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="event_type" class="bold-label">Jenis Event</label>
                        <select id="event_type" name="event_type" class="form-control" required>
                            <option value="">Pilih Jenis Event</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" data-price="{{ $event->current_price }}">
                                    {{ $event->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="event_price" id="harga_event" class="bold-label">Harga Event</label>
                        <input type="text" id="event_price" name="event_price" class="form-control" placeholder="Harga Event" readonly>
                    </div>
                    <div class="mb-3">
                        <input type="hidden" name="special_price" value="0">
                        <input type="checkbox" id="special_price" name="special_price" value="1">
                        <label for="special_price" class="bold-label">Gunakan Harga Spesial (Rp 5.250.000)</label>
                    </div>

                    <!-- <div class="mb-3" id="specialist-input" style="display: none;">
                        <label for="specialistDetail" class="bold-label">Sp.U (or other specialist)</label>
                        <input type="text" id="specialistDetail" name="specialistDetail" class="form-control" placeholder="Sp.U (or other specialist)">
                    </div> -->
                    <div class="mb-3">
                        <label for="phone" class="bold-label">Phone/WhatsApp</label>
                        <input type="text" id="phone" name="phone" class="form-control" placeholder="Phone/WhatsApp Number" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="bold-label">Address</label>
                        <input type="text" id="address" name="address" class="form-control" placeholder="Address" required>
                    </div>
                    <div class="mb-3">
                        <label for="source" class="bold-label">Source</label>
                        <select id="source" name="source" class="form-control">
                            <option value="" selected>Choose Source</option>
                            <option value="On Site">On Site</option>
                            <option value="Register Online">Register Online</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
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
    // Inisialisasi DataTable
        var table = $('#participants-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.participants.data") }}',
                data: function(d) {
                    // Menambahkan parameter filter status pembayaran ke query data
                    d.payment_status = $('#participantsStatusFilter').val();  // Ambil nilai filter status pembayaran
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
                { data: 'institusi', name: 'institusi' },
                { data: 'email', name: 'email' },
                { data: 'category', name: 'category' },
                { data: 'phone', name: 'phone' },
                { data: 'address', name: 'address' },
                { data: 'event_type', name: 'event_type' },
                { data: 'invoice_number', name: 'invoice_number' },
                { data: 'amount', name: 'amount' },
                { 
                    data: 'status', 
                    name: 'status', 
                    render: function(data) {
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
                { data: 'source', name: 'source' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
            },
            // dom: '<"row"<"col-sm-4"f><"col-sm-4 text-center"l><"col-sm-4 text-right"B>>t<"row"<"col-sm-6"i><"col-sm-6"p>>',
            dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Import Peserta',
                    className: 'btn btn-primary mx-2',
                    action: function () {
                        // Tampilkan modal import saat tombol diklik
                        $('#importModal').modal('show');
                    }
                },
                {
                    text: 'Export Excel',
                    className: 'btn btn-success mx-2',
                    action: function (e, dt, button, config) {
                        $.ajax({
                            url: '{{ route("admin.participants.data") }}',
                            method: 'GET',
                            data: {
                                export_all: true, // Parameter untuk ekspor semua data
                                payment_status: $('#participantsStatusFilter').val(), // Kirim status pembayaran
                                // source: $('sourceFilter').val()
                            },
                            success: function (response) {
                                // Menambahkan kolom nomor urut di data
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
                                XLSX.utils.book_append_sheet(workbook, worksheet, 'Data Peserta');

                                // Simpan file Excel
                                XLSX.writeFile(workbook, 'data_peserta.xlsx');
                            },
                            error: function (xhr, status, error) {
                                alert('Gagal mengunduh data.');
                                console.error(error);
                            }
                        });
                    }
                },
            ]
        });

        // Event listener untuk perubahan filter
        $('#participantsStatusFilter').on('change', function() {
            table.ajax.reload();  // Reload data table dengan filter yang baru
    });


    // Modal event listener
    $('#participants-table').on('click', '.view-proof', function () {
        var proofUrl = $(this).data('proof'); // Ambil URL bukti pembayaran
        var participantName = $(this).data('name');
        var paymentAmount = $(this).data('amount');
        var paymentStatus = $(this).data('status');

        $('#proof-image').attr('src', proofUrl);  // Menampilkan bukti pembayaran pada modal
        $('#participant-name').text(participantName);  // Nama peserta
        $('#payment-amount').text(paymentAmount);  // Jumlah pembayaran
        $('#payment-status').val(paymentStatus);  // Status pembayaran

        $('#proofModal').modal('show');
    });

    // Update status pembayaran dengan tombol dinamis
    // $(document).on('click', '.btn-success', function () {
    //     var participantId = $(this).data('id'); // Gunakan data-id untuk mendeteksi ID peserta
    //     var updatedStatus = $('#paymentStatus' + participantId).val();
    //     var invoiceNumber = $('#invoice-number' + participantId).text();

    //     $.ajax({
    //         url: '{{ route("admin.update.payment.status") }}',
    //         type: 'POST',
    //         data: {
    //             _token: '{{ csrf_token() }}',
    //             status: updatedStatus,
    //             invoice_number: invoiceNumber,
    //             participant_id: participantId,
    //             // failed_reason: failedReason
    //         },
    //         success: function (response) {
    //             alert('Status pembayaran berhasil diperbarui!');
    //             $('#paymentProofModal' + participantId).modal('hide');
    //             table.ajax.reload(); // Reload tabel
    //         },
    //         error: function (xhr) {
    //             alert('Terjadi kesalahan saat memperbarui status pembayaran.');
    //         }
    //     });
    // });
});

document.addEventListener('DOMContentLoaded', function () {
    const eventTypeDropdown = document.getElementById('event_type');
    const eventPriceInput = document.getElementById('event_price');

    eventTypeDropdown.addEventListener('change', function () {
        const selectedOption = eventTypeDropdown.options[eventTypeDropdown.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        eventPriceInput.value = price ? `Rp ${parseFloat(price).toLocaleString('id-ID')}` : '';
    });
});

 


// //Fungsi Untuk Rotate dan zoom
// let croppers = {}; // Simpan cropper untuk setiap gambar

// function initializeCropper(participantId) {
//     const image = document.getElementById(`proofImage${participantId}`);
//     if (croppers[participantId]) {
//         croppers[participantId].destroy();
//     }
//     croppers[participantId] = new Cropper(image, {
//         viewMode: 1,
//         responsive: true,
//         zoomable: true,
//         rotatable: true,
//         scalable: true,
//         background: false
//     });
// }

// function rotateImage(participantId, degree) {
//     if (croppers[participantId]) {
//         croppers[participantId].rotate(degree);
//     }
// }

// function zoomImage(participantId, ratio) {
//     if (croppers[participantId]) {
//         croppers[participantId].zoom(ratio);
//     }
// }

// // Inisialisasi Cropper.js saat modal dibuka
// $('.modal').on('shown.bs.modal', function (e) {
//     const modalId = $(this).attr('id'); // Ambil ID modal
//     const participantId = modalId.replace('paymentProofModal', ''); // Ekstrak ID peserta
//     initializeCropper(participantId);
// });

// // Hapus cropper saat modal ditutup
// $('.modal').on('hidden.bs.modal', function (e) {
//     const modalId = $(this).attr('id');
//     const participantId = modalId.replace('paymentProofModal', '');
//     if (croppers[participantId]) {
//         croppers[participantId].destroy();
//         delete croppers[participantId];
//     }
// });

// //Munculkan text input catatan
// $(document).ready(function () {

//     // Ketika status pembayaran berubah
//     $(document).on('change', '[id^="paymentStatus"]', function () {
//         const participantId = $(this).attr('id').replace('paymentStatus', '');
//         const selectedStatus = $(this).val();

//         // Tampilkan atau sembunyikan text field alasan
//         if (selectedStatus === 'failed') {
//             $(`#failedReasonRow${participantId}`).show();
//         } else {
//             $(`#failedReasonRow${participantId}`).hide();
//         }
//     });

//     // Ketika modal dibuka kembali, periksa status dan tampilkan alasan jika diperlukan
//     $(document).on('show.bs.modal', '.modal', function () {
//         const participantId = $(this).data('id'); // Ambil ID peserta dari modal
//         const selectedStatus = $(`#paymentStatus${participantId}`).val(); // Ambil status pembayaran dari dropdown

//         // Tampilkan atau sembunyikan text field alasan berdasarkan status pembayaran saat ini
//         if (selectedStatus === 'failed') {
//             $(`#failedReasonRow${participantId}`).show();
//         } else {
//             $(`#failedReasonRow${participantId}`).hide();
//         }
//     });



//     // Update status pembayaran dengan catatan
//     $(document).on('click', '.btn-success', function () {
//         const participantId = $(this).data('id');
//         const updatedStatus = $(`#paymentStatus${participantId}`).val();
//         const invoiceNumber = $(`#invoice-number${participantId}`).text();
//         const failedReason = $(`#failedReason${participantId}`).val(); // Ambil alasan gagal jika ada

//         $.ajax({
//             url: '{{ route("admin.update.payment.status") }}',
//             type: 'POST',
//             data: {
//                 _token: '{{ csrf_token() }}',
//                 status: updatedStatus,
//                 invoice_number: invoiceNumber,
//                 participant_id: participantId,
//                 failed_reason: failedReason // Kirim alasan gagal
//             },
//             success: function (response) {
//                 alert('Status pembayaran berhasil diperbarui!');
//                 $('#paymentProofModal' + participantId).modal('hide');
//                 table.ajax.reload(); // Reload tabel
//             },
//             error: function (xhr) {
//                 alert('Terjadi kesalahan saat memperbarui status pembayaran.');
//             }
//         });
//     });
// });

$(document).ready(function() {
    $('#addParticipantForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '{{ route("admin.participant.register") }}', // Ganti dengan route yang sesuai
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    alert('Peserta berhasil ditambahkan!');
                    $('#addParticipantModal').modal('hide');
                    $('#participants-table').DataTable().ajax.reload(); // Reload tabel
                } else {
                    alert('Gagal menambahkan peserta: ' + response.message);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseJSON); // Log the response to see validation errors
                alert('Terjadi kesalahan saat menambahkan peserta: ' + xhr.responseJSON.message);
            }
        });
    });
});

//listener untuk spesial price
document.addEventListener('DOMContentLoaded', function () {
        const eventTypeSelect = document.getElementById('event_type');
        const eventPriceInput = document.getElementById('event_price');
        const specialPriceCheckbox = document.getElementById('special_price');

        // Set harga berdasarkan pilihan event
        eventTypeSelect.addEventListener('change', function () {
            const selectedOption = eventTypeSelect.options[eventTypeSelect.selectedIndex];
            const price = selectedOption.dataset.price || 0;

            if (!specialPriceCheckbox.checked) {
                eventPriceInput.value = parseInt(price).toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                });
            }
        });

        // Ubah harga jika special price dicentang
        specialPriceCheckbox.addEventListener('change', function () {
            if (specialPriceCheckbox.checked) {
                eventPriceInput.value = (5250000).toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                });
            } else {
                // Kembalikan ke harga event yang dipilih
                const selectedOption = eventTypeSelect.options[eventTypeSelect.selectedIndex];
                const price = selectedOption.dataset.price || 0;

                eventPriceInput.value = parseInt(price).toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                });
            }
        });
    });

</script>
@endpush
