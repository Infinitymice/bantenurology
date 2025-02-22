<div class="btn-group">
    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>
    <button class="btn btn-sm btn-danger" onclick="deleteUser({{ $user->id }})">Delete</button>
</div>

<script>
function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        $.ajax({
            url: "{{ route('admin.users.delete', '') }}/" + id,
            type: 'DELETE',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                alert(response.success);
                $('#userTable').DataTable().ajax.reload();
            }
        });
    }
}
</script>
