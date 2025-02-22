@extends('admin.template.admin')

@section('content')
<div class="container">
    <form method="POST" action="{{ route('admin.events.store') }}">
        @csrf

        @if ($errors->any())
            {!! implode(
                '',
                $errors->all('<div class="alert alert-danger alert-dismissible">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    :message
                </div>')
            ) !!}
        @endif

        <div class="form-group">
            <label for="name">Event Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label for="event_type_id">Event Type</label>
            <select name="event_type_id" class="form-control" required>
                @foreach($eventTypes as $eventType)
                    <option value="{{ $eventType->id }}">{{ $eventType->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="early_bid_price">Early Bid Price</label>
            <input type="text" name="early_bid_price" class="form-control" value="{{ old('early_bid_price') }}" required>
        </div>

        <div class="form-group">
            <label for="onsite_price">Onsite Price</label>
            <input type="text" name="onsite_price" class="form-control" value="{{ old('onsite_price') }}" required>
        </div>

        <div class="form-group">
            <label for="early_bid_date">Early Bird Date</label>
            <input type="date" name="early_bid_date" class="form-control" value="{{ old('early_bid_date') }}" required>
        </div>

        <div class="form-group">
            <label for="event_date">Event Date</label>
            <input type="date" name="event_date" class="form-control" value="{{ old('event_date') }}" required>
        </div>

        <div class="form-group">
            <label for="event_date_day2">Event Date Day 2</label>
            <input type="date" name="event_date_day2" class="form-control" value="{{ old('event_date_day2') }}">
        </div>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" id="enable_kuota" class="form-check-input">
                <label class="form-check-label" for="enable_kuota">Enable Kuota</label>
            </div>
        </div>

        <div id="kuota_input_wrapper" style="display: none;">
            <div class="form-group">
                <label for="kuota">Kuota</label>
                <input type="number" name="kuota" id="kuota" class="form-control" value="{{ old('kuota') }}">
            </div>
        </div>

        <input type="hidden" name="kuota_hidden" id="kuota_hidden_value" value="">

        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const enableKuotaCheckbox = document.getElementById('enable_kuota');
        const kuotaInputWrapper = document.getElementById('kuota_input_wrapper');
        const kuotaInput = document.getElementById('kuota');
        const kuotaHiddenInput = document.getElementById('kuota_hidden_value');

        // Set visibility on page load
        kuotaInputWrapper.style.display = enableKuotaCheckbox.checked ? '' : 'none';

        // Save kuota value to hidden input if checkbox is checked
        enableKuotaCheckbox.addEventListener('change', function () {
            if (this.checked) {
                kuotaInputWrapper.style.display = '';
            } else {
                kuotaInputWrapper.style.display = 'none';
                kuotaInput.value = ''; // Reset kuota input value
            }
        });

        // Ensure kuota value is copied to hidden input on form submit
        document.querySelector('form').addEventListener('submit', function () {
            kuotaHiddenInput.value = enableKuotaCheckbox.checked ? kuotaInput.value : '';
        });
    });
</script>
@endsection
