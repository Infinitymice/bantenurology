@extends('admin.template.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit Event Types</h5>
    </div>
    <form method="POST" action="{{ isset($eventType) ? route('admin.event-types.update', $eventType->id) : route('admin.event-types.store') }}">
        @csrf
        @if(isset($eventType))
            @method('PUT')
        @endif
        <div class="card-body">
        <div class="form-group">
            <label for="name">Event Type Name</label>
            <input type="text" name="name" class="form-control" value="{{ $eventType->name ?? old('name') }}" required>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
@endsection
