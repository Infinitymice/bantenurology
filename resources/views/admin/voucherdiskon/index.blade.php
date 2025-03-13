@extends('admin.template.admin')

@section('title', 'Manage Vouchers')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Manage Vouchers</h5>
    </div>
    <div class="card-body">
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#voucherModal">Add New Voucher</button>
        
        <table class="table table-bordered" id="vouchers-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Discount (%)</th>
                    <th>Max Uses</th>
                    <th>Times Used</th>
                    <th>Valid Until</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Add/Edit Voucher Modal -->
<div class="modal fade" id="voucherModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Voucher</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="voucherForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="voucher_id">
                    <div class="form-group">
                        <label>Voucher Code</label>
                        <input type="text" class="form-control" name="code" required>
                    </div>
                    <div class="form-group">
                        <label>Event Discounts</label>
                        @foreach($eventTypes as $type)
                            <div class="mb-3">
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">{{ $type->name }}</div>
                                    </div>
                                    <select class="form-control discount-type" 
                                            name="discount_types[{{ $type->id }}]" 
                                            data-type-id="{{ $type->id }}">
                                        <option value="">Select Type</option>
                                        <option value="percentage">Percentage (%)</option>
                                        <option value="fixed">Fixed Amount (Rp)</option>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control discount-value" 
                                           name="event_discounts[{{ $type->id }}]" 
                                           min="0"
                                           step="0.01" 
                                           placeholder="Enter discount value"
                                           data-type-id="{{ $type->id }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text discount-suffix-{{ $type->id }}">%</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-group">
                        <label>Max Uses</label>
                        <input type="number" class="form-control" name="max_uses" min="1">
                    </div>
                    <div class="form-group">
                        <label>Valid Until</label>
                        <input type="date" class="form-control" name="valid_until">
                    </div>
                    <!-- <div class="form-group">
                        <label>Apply to Event Types</label>
                        <select class="form-control" name="event_types[]" multiple required>
                            @foreach($eventTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple types</small>
                    </div> -->
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" id="is_active">
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#vouchers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.vouchers.data') }}",
            error: function(xhr, error, thrown) {
                console.error('DataTables error:', error);
                alert('Error loading voucher data. Please try refreshing the page.');
            }
        },
        columns: [
            {data: 'code', name: 'code'},
            {
                data: 'discount_display',
                name: 'discount_display',
                render: function(data) {
                    return data || '-';
                }
            },
            {data: 'max_uses', name: 'max_uses', render: function(data) {
                return data || '-';
            }},
            {data: 'times_used', name: 'times_used', render: function(data) {
                return data || '0';
            }},
            {data: 'valid_until', name: 'valid_until'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    // Form submission handling
    $('#voucherForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#voucher_id').val();
        var url = id ? "{{ route('admin.vouchers.update', '') }}/" + id 
                    : "{{ route('admin.vouchers.store') }}";
        
        // Kumpulkan data form
        var formData = new FormData(this);
        
        // Filter input kosong dari event_discounts
        var eventDiscounts = {};
        $('input[name^="event_discounts"]').each(function() {
            if ($(this).val() !== '') {
                var typeId = $(this).attr('name').match(/\[(\d+)\]/)[1];
                eventDiscounts[typeId] = $(this).val();
            }
        });
        
        // Hapus event_discounts lama dan tambahkan yang baru
        formData.delete('event_discounts');
        for (var typeId in eventDiscounts) {
            formData.append(`event_discounts[${typeId}]`, eventDiscounts[typeId]);
        }
        
        formData.append('_method', id ? 'PUT' : 'POST');
        formData.append('is_active', $('#is_active').is(':checked') ? '1' : '0');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#voucherModal').modal('hide');
                $('#voucherForm')[0].reset();
                table.ajax.reload();
                alert('Voucher saved successfully!');
            },
            error: function(xhr) {
                console.log(xhr);
                let errors = xhr.responseJSON ? xhr.responseJSON.errors : {};
                let errorMessage = '';
                for (let field in errors) {
                    errorMessage += errors[field].join('\n') + '\n';
                }
                alert(errorMessage || 'Error saving voucher');
            }
        });
    });

    // Add this inside your $(document).ready function
    $('#vouchers-table').on('click', '.edit-voucher', function() {
        var id = $(this).data('id');
        $('#voucherForm')[0].reset();
        $('#voucher_id').val(id);
        
        $.get("{{ route('admin.vouchers.show', '') }}/" + id, function(data) {
            console.log('Valid Until:', data.valid_until);
            $('input[name="code"]').val(data.code);
            $('input[name="max_uses"]').val(data.max_uses);
            
            // Konversi format tanggal untuk input type="date"
            if (data.valid_until) {
                const date = new Date(data.valid_until);
                const formattedDate = date.toISOString().split('T')[0];
                $('input[name="valid_until"]').val(formattedDate);
            }
            
            $('input[name="is_active"]').prop('checked', data.is_active);
            
            // Populate discount values and types
            if (data.event_discounts && data.discount_types) {
                const eventDiscounts = JSON.parse(data.event_discounts);
                const discountTypes = JSON.parse(data.discount_types);
                
                for (const typeId in eventDiscounts) {
                    $(`select[name="discount_types[${typeId}]"]`).val(discountTypes[typeId] || 'percentage');
                    $(`input[name="event_discounts[${typeId}]"]`).val(eventDiscounts[typeId]);
                    
                    // Trigger change event to update suffix and input attributes
                    $(`select[name="discount_types[${typeId}]"]`).trigger('change');
                }
            }
            
            $('#voucherModal').modal('show');
        });
    });

    // Add delete handler
    $('#vouchers-table').on('click', '.delete-voucher', function() {
        if (confirm('Are you sure you want to delete this voucher?')) {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('admin.vouchers.destroy', '') }}/" + id,
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function() {
                    table.ajax.reload();
                }
            });
        }
    });

    // Add this after your DataTable initialization
    $('[data-toggle="modal"][data-target="#voucherModal"]').on('click', function() {
        $('#voucherForm')[0].reset();
        $('#voucher_id').val('');
    });

    //type diskon
    $('.discount-type').on('change', function() {
        var typeId = $(this).data('type-id');
        var discountInput = $(`input[name="event_discounts[${typeId}]"]`);
        var suffix = $(`.discount-suffix-${typeId}`);
        
        if ($(this).val() === 'percentage') {
            discountInput.attr('max', '100');
            discountInput.attr('step', '0.01');
            suffix.text('%');
        } else if ($(this).val() === 'fixed') {
            discountInput.attr('max', '');
            discountInput.attr('step', '1000');
            suffix.text('Rp');
        }
    });
});
</script>
@endpush
@endsection
