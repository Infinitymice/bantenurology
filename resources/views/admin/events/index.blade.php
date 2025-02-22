@extends('admin.template.admin')

@section('content')

<div class="card">
    <div class="card-header">
        <h5>List Events</h5>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary mb-3">Create Event</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="eventsTable">
        @if(session('success'))
            <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
            </div>
        @endif
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Event Type</th>
                    <th>Early Bird Price</th>
                    <th>On Site Price</th>
                    <th>Early Bird Date</th>
                    <th>Event Date</th>
                    <th>Event Date Day 2</th>
                    <th>Kuota</th>
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
        $('#eventsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.events.data') }}',
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
                // { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'event_type', name: 'event_type' },
                { data: 'early_bid_price', name: 'early_bid_price'},
                { data: 'onsite_price', name: 'onsite_price'},
                { data: 'early_bid_date', name: 'early_bid_date'},
                { data: 'event_date', name: 'event_date'},
                { data: 'event_date_day2', name: 'event_date_day2'},
                { data: 'kuota', name: 'kuota' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
                
            ]
        });
    });

    //Untuk alert
    setTimeout(function() {
            var alert = document.getElementById('success-alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                // Menghilangkan alert sepenuhnya setelah animasi selesai
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 300)
            }
        }, 3000);
</script>
@endpush
