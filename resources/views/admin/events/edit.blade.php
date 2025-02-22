@extends('admin.template.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit Events</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($event) ? route('admin.events.update', $event->id) : route('admin.events.store') }}">
            @csrf
            @if(isset($event))
                @method('PUT')
            @endif

            <div class="form-group">
                <label for="name">Event Name</label>
                <input type="text" name="name" class="form-control" value="{{ $event->name ?? old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="event_type_id">Event Type</label>
                <select name="event_type_id" class="form-control" required>
                    @foreach($eventTypes as $eventType)
                        <option value="{{ $eventType->id }}" {{ isset($event) && $event->event_type_id == $eventType->id ? 'selected' : '' }}>
                            {{ $eventType->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="early_bid_price">Early Bid Price</label>
                <input type="text" name="early_bid_price" class="form-control" value="{{ $event->early_bid_price ?? old('early_bid_price') }}" required>
            </div>

            <div class="form-group">
                <label for="onsite_price">Onsite Price</label>
                <input type="text" name="onsite_price" class="form-control" value="{{ $event->onsite_price ?? old('onsite_price') }}" required>
            </div>

            <div class="form-group">
                <label for="early_bid_date">Early Bird Date</label>
                <input type="date" name="early_bid_date" class="form-control" value="{{ $event->early_bid_date ? \Carbon\Carbon::parse($event->early_bid_date)->format('Y-m-d') : old('early_bid_date') }}" required>
            </div>

            <div class="form-group">
                <label for="event_date">Event Date</label>
                <input type="date" name="event_date" class="form-control" value="{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') : old('event_date') }}" required>
            </div>

            <div class="form-group">
                <label for="event_date_day2">Event Date Day 2</label>
                <input type="date" name="event_date_day2" class="form-control" value="{{ $event->event_date_day2 ? \Carbon\Carbon::parse($event->event_date_day2)->format('Y-m-d') : old('event_date_day2') }}">
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" id="enable_kuota" class="form-check-input" {{ isset($event->kuota) ? 'checked' : '' }}>
                    <label class="form-check-label" for="enable_kuota">Enable Kuota</label>
                </div>
            </div>

            <div id="kuota_input_wrapper" style="{{ isset($event->kuota) ? '' : 'display: none;' }}">
                <div class="form-group">
                    <label for="kuota">Kuota</label>
                    <input type="number" name="kuota" id="kuota" class="form-control" value="{{ $event->kuota ?? old('kuota') }}">
                </div>
            </div>

            <input type="hidden" name="kuota_hidden" id="kuota_hidden_value" value="{{ $event->kuota ?? '' }}">

            <button type="submit" class="btn btn-success">Save</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const enableKuotaCheckbox = document.getElementById('enable_kuota');
        const kuotaInputWrapper = document.getElementById('kuota_input_wrapper');
        const kuotaInput = document.getElementById('kuota');
        const kuotaHiddenInput = document.getElementById('kuota_hidden_value');

        // Atur visibilitas saat halaman dimuat
        kuotaInputWrapper.style.display = enableKuotaCheckbox.checked ? '' : 'none';

        // Menyimpan nilai kuota ke hidden input jika checkbox dicentang
        enableKuotaCheckbox.addEventListener('change', function () {
            if (this.checked) {
                kuotaInputWrapper.style.display = '';
                kuotaHiddenInput.value = kuotaInput.value;  // Set nilai kuota ke hidden input
            } else {
                kuotaInputWrapper.style.display = 'none';
                kuotaHiddenInput.value = '';  // Set nilai kuota ke kosong jika tidak dicentang
                kuotaInput.value = ''; // Reset nilai input kuota
            }
        });

        // Memastikan nilai kuota disalin ke hidden input saat form disubmit
        document.querySelector('form').addEventListener('submit', function () {
            if (enableKuotaCheckbox.checked) {
                kuotaHiddenInput.value = kuotaInput.value; // Set nilai hidden input dengan nilai input kuota
            } else {
                kuotaHiddenInput.value = ''; // Kosongkan nilai hidden input jika checkbox tidak dicentang
            }
        });
    });
</script>
@endsection
