@extends('admin.template.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Manage Users</h5>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add User</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="userTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
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
    var table = $('#userTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.users.data') }}",
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
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'username', name: 'username' },
            { data: 'email', name: 'email' },
            { data: 'is_admin', name: 'is_admin', render: function(data) {
                return data ? 'Admin' : 'User';
            }},
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Customize position of search and show entries
    $('.dataTables_length').addClass('float-left');
    $('.dataTables_filter').addClass('float-right');
});
</script>
@endpush
