@extends('admin.template.admin')

@section('title', 'Manajemen Kode Grup')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Manajemen Kode Grup</h5>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addGroupModal">
            Tambah Grup Baru
        </button>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="groupCodes-table">
            <thead>
                <tr>
                    <th>Kode Grup</th>
                    <th>Nama Grup</th>
                    <th>Jumlah Anggota</th>
                    <th>Maksimal Anggota</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Tambah Grup -->
<div class="modal fade" id="addGroupModal" tabindex="-1" role="dialog" aria-labelledby="addGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addGroupModalLabel">Tambah Grup Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addGroupForm" action="{{ route('admin.group-codes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Grup</label>
                        <input type="text" name="group_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Maksimal Anggota</label>
                        <input type="number" name="max_members" class="form-control" min="1" value="6" required>
                    </div>
                    <div class="form-group">
                        <label>Kode Grup</label>
                        <div class="input-group">
                            <input type="text" name="code" class="form-control" readonly>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-secondary" onclick="generateCode()">Generate</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Grup -->
<div class="modal fade" id="editGroupModal" tabindex="-1" role="dialog" aria-labelledby="editGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGroupModalLabel">Edit Grup</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editGroupForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Grup</label>
                        <input type="text" name="group_name" id="edit_group_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Maksimal Anggota</label>
                        <input type="number" name="max_members" id="edit_max_members" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Kode Grup</label>
                        <input type="text" name="code" id="edit_code" class="form-control" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function generateCode() {
    const random = Math.random().toString(36).substring(2, 8).toUpperCase();
    document.querySelector('input[name="code"]').value = 'GRP-' + random;
}

$(document).ready(function() {
    $('#groupCodes-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.group-codes.data") }}',
        columns: [
            { data: 'code', name: 'code' },
            { data: 'group_name', name: 'group_name' },
            { data: 'current_members', name: 'current_members' },
            { data: 'max_members', name: 'max_members' },
            { data: 'is_active', name: 'is_active' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});

function editGroup(id) {
    $.ajax({
        url: `{{ route('admin.group-codes.edit', '') }}/${id}`, // Fix the URL
        method: 'GET',
        success: function(response) {
            $('#edit_group_name').val(response.group_name);
            $('#edit_max_members').val(response.max_members);
            $('#edit_code').val(response.code);
            $('#editGroupForm').attr('action', `{{ route('admin.group-codes.update', '') }}/${id}`); // Fix the form action URL
            $('#editGroupModal').modal('show');
        },
        error: function(xhr) {
            alert('Error fetching group data');
        }
    });
}

function toggleStatus(id) {
    if (confirm('Are you sure you want to change this group\'s status?')) {
        $.ajax({
            url: `{{ route('admin.group-codes.toggle', '') }}/${id}`, // Fix the toggle URL
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#groupCodes-table').DataTable().ajax.reload();
            },
            error: function(xhr) {
                alert('Error changing status');
            }
        });
    }
}
</script>
@endpush