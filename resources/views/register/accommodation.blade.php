@extends('layouts.app')

@section('title', 'Step 3 - Choose Accommodation')

@section('content')
<div class="container mt-5">
    @if(session('error'))
        <div id="alert-box" class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Progress Bar -->
        <div class="col-12 mb-4">
            <div class="progress">
                <div class="progress-bar" style="width: 60%;" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">Step 3 of 5</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center mb-4">Choose Accommodation (Optional)</h3>
                    <form method="POST" action="{{ route('register.accommodation') }}" id="accommodationForm">
                        @csrf
                        <input type="hidden" name="selected_categories" value="{{ implode(',', session('selected_categories', [])) }}">
                        <input type="hidden" name="rooms" value="[]">

                        <!-- Date Range Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="check_in_date">Check-in Date</label>
                                    <input type="date" class="form-control" id="check_in_date" name="check_in_date" min="{{ date('Y-m-d') }}" 
                value="{{ $accommodationData['check_in_date'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="check_out_date">Check-out Date</label>
                                    <input type="date" class="form-control" id="check_out_date" name="check_out_date" min="{{ date('Y-m-d') }}"
                                    value="{{ $accommodationData['check_out_date'] ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <!-- Accommodation Cards -->
                        <div class="row">
                            @foreach($accommodations as $accommodation)
                            <div class="col-md-6 mb-4">
                                <div class="card room-card {{ isset($accommodationData['rooms'][$accommodation->id]) && 
                                    $accommodationData['rooms'][$accommodation->id]['quantity'] > 0 ? 'selected' : '' }}" 
                                    data-id="{{ $accommodation->id }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5 class="card-title">{{ $accommodation->name }}</h5>
                                            <span class="badge bg-primary" id="available-rooms-{{ $accommodation->id }}">
                                                Loading rooms...
                                            </span>
                                        </div>
                                        <p class="card-text">{{ $accommodation->description ?? 'Deluxe Room' }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Rp. {{ number_format($accommodation->price) }}/night</h6>
                                            <div class="room-quantity" style="display: {{ isset($accommodationData['rooms'][$accommodation->id]) && 
                                                $accommodationData['rooms'][$accommodation->id]['quantity'] > 0 ? 'block' : 'none' }};">
                                                <label>Rooms:</label>
                                                <select name="rooms[{{ $accommodation->id }}]" class="form-select form-select-sm ms-2" style="width: 80px;">
                                                    <option value="0">0</option>
                                                    @for($i = 1; $i <= $accommodation->qty; $i++)
                                                        <option value="{{ $i }}" 
                                                            {{ isset($accommodationData['rooms'][$accommodation->id]) && 
                                                               $accommodationData['rooms'][$accommodation->id]['quantity'] == $i ? 'selected' : '' }}>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Price Summary -->
                        <div class="card mt-4" id="price-summary" style="display: none;">
                            <div class="card-body">
                                <h5>Booking Summary</h5>
                                <div id="booking-details"></div>
                                <div class="mt-3">
                                    <strong>Total Nights: </strong><span id="total-nights">0</span><br>
                                    <strong>Total Price: <span id="total-price">Rp. 0</span> </strong>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('register.step2') }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Next</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi card yang sudah dipilih
    let hasSelectedRooms = false;
    document.querySelectorAll('.room-card').forEach(card => {
        const select = card.querySelector('select');
        if (select && parseInt(select.value) > 0) {
            hasSelectedRooms = true;
            card.classList.add('selected');
            card.querySelector('.room-quantity').style.display = 'block';
        }
    });

    // Jika ada kamar yang dipilih, langsung tampilkan booking summary
    if (hasSelectedRooms) {
        const checkInDate = document.getElementById('check_in_date');
        const checkOutDate = document.getElementById('check_out_date');
        
        if (checkInDate.value && checkOutDate.value) {
            calculateAndDisplaySummary();
        }
    }

    // Date handling
    const checkInDate = document.getElementById('check_in_date');
    const checkOutDate = document.getElementById('check_out_date');
    
    checkInDate.addEventListener('change', function() {
        if (this.value) {
            // Set check-out date ke hari berikutnya
            const nextDay = new Date(this.value);
            nextDay.setDate(nextDay.getDate() + 1);
            
            // Format tanggal ke YYYY-MM-DD
            const formattedDate = nextDay.toISOString().split('T')[0];
            checkOutDate.value = formattedDate;
            
            // Set minimum date untuk check-out
            checkOutDate.min = formattedDate;
        }
    });
    
    checkOutDate.addEventListener('change', updateTotal);

    // Room selection handling
    document.querySelectorAll('.room-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.tagName === 'SELECT' || e.target.tagName === 'OPTION') {
                return;
            }
            toggleRoom(this);
        });

        const select = card.querySelector('select');
        select.addEventListener('change', function(e) {
            e.stopPropagation();
            const card = this.closest('.room-card');
            updateRoomSelection(card, this.value);
            updateTotal();
        });
    });

    // Tambahkan validasi form submission
    document.getElementById('accommodationForm').addEventListener('submit', async function(e) {
        e.preventDefault(); // Tahan submit form dulu
        
        const checkInDate = document.getElementById('check_in_date').value;
        const checkOutDate = document.getElementById('check_out_date').value;
        const selectedRooms = document.querySelectorAll('.room-card select');
        let hasSelectedRooms = false;

        // Cek apakah ada kamar yang dipilih
        selectedRooms.forEach(select => {
            if (parseInt(select.value) > 0) {
                hasSelectedRooms = true;
            }
        });

        // Jika salah satu field diisi (tanggal atau kamar), semua menjadi required
        if (checkInDate || checkOutDate || hasSelectedRooms) {
            if (!checkInDate) {
                alert('Please select check-in date');
                return;
            }
            if (!checkOutDate) {
                alert('Please select check-out date');
                return;
            }
            if (!hasSelectedRooms) {
                alert('Please select at least one room');
                return;
            }
        }

        // Jika form optional (tidak ada yang dipilih) atau semua validasi berhasil
        this.submit();
    });

    function checkAvailability() {
        const checkIn = checkInDate.value;
        const checkOut = checkOutDate.value;
        const priceSummary = document.getElementById('price-summary');

        if (checkIn && checkOut) {
            document.querySelectorAll('.room-card').forEach(card => {
                const roomId = card.dataset.id;
                const badge = card.querySelector('.badge');
                const select = card.querySelector('select');
                const roomQuantityDiv = card.querySelector('.room-quantity');

                fetch(`/check-availability?room_id=${roomId}&check_in_date=${checkIn}&check_out_date=${checkOut}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.available <= 0) {
                            badge.textContent = 'Full Booked';
                            card.classList.add('disabled');
                            
                            // Reset dan sembunyikan pilihan kamar
                            if (select) {
                                while (select.options.length > 1) {
                                    select.remove(1);
                                }
                                select.value = '0';
                                card.classList.remove('selected');
                                roomQuantityDiv.style.display = 'none';
                            }
                            
                            if (!document.querySelector('.room-card.selected')) {
                                priceSummary.style.display = 'none';
                            }
                        } else {
                            badge.textContent = `${data.available} rooms left`;
                            card.classList.remove('disabled');
                            // Reset dan update opsi select sesuai ketersediaan
                            if (select) {
                                // Simpan nilai yang dipilih sekarang
                                const currentValue = select.value;
                                
                                // Hapus semua opsi kecuali opsi 0
                                while (select.options.length > 1) {
                                    select.remove(1);
                                }
                                
                                // Tambah opsi sesuai jumlah kamar yang tersedia
                                for (let i = 1; i <= data.available; i++) {
                                    select.add(new Option(i, i));
                                }
                                
                                // Kembalikan nilai yang dipilih jika masih valid
                                if (currentValue <= data.available) {
                                    select.value = currentValue;
                                }
                            }
                        }
                        
                        // Update booking summary
                        updateTotal();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        badge.textContent = 'Error loading';
                    });
            });
        }
    }

    // Check availability saat tanggal berubah
    document.getElementById('check_in_date').addEventListener('change', checkAvailability);
    document.getElementById('check_out_date').addEventListener('change', checkAvailability);
    
    // Initial check jika tanggal sudah terisi
    if (document.getElementById('check_in_date').value && 
        document.getElementById('check_out_date').value) {
        checkAvailability();
    }
});

function toggleRoom(card) {
    const checkInDate = document.getElementById('check_in_date').value;
    const checkOutDate = document.getElementById('check_out_date').value;
    
    if (!checkInDate || !checkOutDate) {
        alert('Please select check-in and check-out dates first');
        return;
    }
    
    const quantityDiv = card.querySelector('.room-quantity');
    const select = quantityDiv.querySelector('select');
    
    if (card.classList.contains('selected')) {
        card.classList.remove('selected');
        quantityDiv.style.display = 'none';
        select.value = '0';
        
        // Cek apakah masih ada kamar yang dipilih
        const hasSelectedRooms = Array.from(document.querySelectorAll('.room-card select'))
            .some(select => parseInt(select.value) > 0);
            
        if (!hasSelectedRooms) {
            document.getElementById('price-summary').style.display = 'none';
        }
    } else {
        card.classList.add('selected');
        quantityDiv.style.display = 'block';
        if (select.value === '0') {
            select.value = '1';
        }
    }
    updateTotal();
}

function updateRoomSelection(card, quantity) {
    if (parseInt(quantity) > 0) {
        card.classList.add('selected');
        card.querySelector('.room-quantity').style.display = 'block';
    } else {
        card.classList.remove('selected');
        card.querySelector('.room-quantity').style.display = 'none';
        
        // Cek apakah masih ada kamar yang dipilih
        const hasSelectedRooms = Array.from(document.querySelectorAll('.room-card select'))
            .some(select => parseInt(select.value) > 0);
            
        if (!hasSelectedRooms) {
            document.getElementById('price-summary').style.display = 'none';
        }
    }
    updateTotal();
}

function calculateAndDisplaySummary() {
    const checkInDate = document.getElementById('check_in_date').value;
    const checkOutDate = document.getElementById('check_out_date').value;
    const summaryDiv = document.getElementById('price-summary');
    const bookingDetails = document.getElementById('booking-details');
    const totalNightsSpan = document.getElementById('total-nights');
    const totalPriceSpan = document.getElementById('total-price');
    
    if (!checkInDate || !checkOutDate) return;
    
    const checkIn = new Date(checkInDate);
    const checkOut = new Date(checkOutDate);
    const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
    
    let totalPrice = 0;
    let detailsHTML = '';
    
    document.querySelectorAll('.room-card').forEach(card => {
        const select = card.querySelector('select');
        const quantity = parseInt(select.value);
        
        if (quantity > 0) {
            const roomType = card.querySelector('.card-title').textContent;
            const priceText = card.querySelector('h6').textContent;
            const pricePerNight = parseInt(priceText.replace(/[^0-9]/g, ''));
            const roomTotal = pricePerNight * quantity * nights;
            totalPrice += roomTotal;
            
            detailsHTML += `
                <div class="mb-2">
                    <div>${roomType} x ${quantity} room(s)</div>
                    <div>Rp. ${pricePerNight.toLocaleString('id-ID')} x ${nights} nights</div>
                    <div>Subtotal: Rp. ${roomTotal.toLocaleString('id-ID')}</div>
                </div>
                <hr>
            `;
        }
    });
    
    if (detailsHTML) {
        summaryDiv.style.display = 'block';
        bookingDetails.innerHTML = detailsHTML;
        totalNightsSpan.textContent = nights;
        totalPriceSpan.textContent = `Rp. ${totalPrice.toLocaleString('id-ID')}`;
    }
}

// Update fungsi updateTotal untuk menggunakan calculateAndDisplaySummary
function updateTotal() {
    calculateAndDisplaySummary();
}

// Update event listener form
document.getElementById('accommodationForm').onsubmit = function(e) {
    const checkInDate = document.getElementById('check_in_date').value;
    const checkOutDate = document.getElementById('check_out_date').value;
    const hasSelectedRooms = Array.from(document.querySelectorAll('.room-card select'))
        .some(select => parseInt(select.value) > 0);
    
    // Jika salah satu tanggal diisi, maka keduanya harus diisi
    if ((checkInDate || checkOutDate) && (!checkInDate || !checkOutDate)) {
        e.preventDefault();
        alert('Please select both check-in and check-out dates');
        return false;
    }
    
    // Validasi jika ada kamar yang dipilih
    if (hasSelectedRooms && (!checkInDate || !checkOutDate)) {
        e.preventDefault();
        alert('Please select check-in and check-out dates for your room booking');
        return false;
    }
    
    return true;
};
</script>
@endsection