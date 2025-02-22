@extends('admin.template.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Add User</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            @include('admin.users.partials.form')
        </form>
    </div>
</div>
@endsection
