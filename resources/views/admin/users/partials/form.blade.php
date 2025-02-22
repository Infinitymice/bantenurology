<div class="form-group">
    <label for="name">Name</label>
    <input type="text" name="name" class="form-control" value="{{ $user->name ?? old('name') }}" required>
</div>
<div class="form-group">
    <label for="name">Username</label>
    <input type="text" name="username" class="form-control" value="{{ $user->username ?? old('username') }}" required>
</div>

<div class="form-group">
    <label for="email">Email</label>
    <input type="email" name="email" class="form-control" value="{{ $user->email ?? old('email') }}" required>
</div>

<div class="form-group">
    <label for="password">Password</label>
    <input type="password" name="password" class="form-control">
    <small class="text-muted">Leave blank if not changing password.</small>
</div>

<div class="form-group">
    <label for="is_admin">Role</label>
    <select name="is_admin" class="form-control">
        <option value="0" {{ (isset($user) && !$user->is_admin) ? 'selected' : '' }}>User</option>
        <option value="1" {{ (isset($user) && $user->is_admin) ? 'selected' : '' }}>Admin</option>
    </select>
</div>

<button type="submit" class="btn btn-primary">Save</button>
