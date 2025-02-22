@extends('admin.template.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit User</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @include('admin.users.partials.form')
        </form>
    </div>
</div>
@endsection
