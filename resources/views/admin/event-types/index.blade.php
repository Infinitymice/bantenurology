@extends('admin.template.admin')

@section('content')

<div class="card">
    <div class="card-header">
        <h5>List Event Types</h5>
        <a href="{{ route('admin.event-types.create') }}" class="btn btn-primary mb-3">Create Event Type</a>
    </div>
        <div class="card-body">
            <table class="table table-bordered" id="eventTypesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#eventTypesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.event-types.data') }}',
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
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ]
        });
    });
</script>
@endpush
