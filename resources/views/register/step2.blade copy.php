@extends('layouts.app')

@section('title', 'Step 2 - Choose Events')

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
                <div class="progress-bar" style="width: 40%;" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">Step 2 of 5</div>
            </div>
        </div>
    </div>

    <!-- Formulir Pilih Produk -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center">Choose Events</h3>
                    <form action="{{ route('register.step2') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="event_type" class="bold-label">Events Type</label>
                            <select id="event_type" name="eventTypeid[]" class="form-control" required>
                                <option value="">Choose Event Type</option>
                                @foreach($eventTypes as $eventType)
                                    <option value="{{ $eventType->id }}" {{ old('event_type') == $eventType->id ? 'selected' : '' }}>{{ $eventType->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Kartu Pilihan Kategori (Events) -->
                        <div class="mb-3 row" id="event-options">
                            <!-- card untuk event option -->
                        </div>

                        <!-- Input tersembunyi untuk kategori yang dipilih -->
                        <input type="hidden" id="selected_categories" name="selected_categories[]">
                        

                        <div class="mb-3" id="category-details" style="display: none;">
                            <div class="card">
                                <div class="card-body">
                                    <h5 id="category-name"></h5>
                                    <p id="category-description"></p>
                                    <p><span class="large-price-text">Total Price:</span> <span class="large-price-text" id="category-price"></span></p>
                                    <p id="kuota"></p>
                                    <button type="button" id="remove-event" class="btn btn-danger btn-sm float-right" style="display: none;">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>

                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('register.step1') }}" class="btn btn-secondary w-auto">Back</a>
                            <button type="submit" class="btn btn-primary w-auto">Next</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

let selectedCategories = []; // Array untuk menyimpan kategori yang dipilih
let selectedDate = null; // Variabel untuk menyimpan tanggal yang telah dipilih
const today = new Date(); // Mengambil tanggal hari ini
const selectedCategory = @json($selectedCategory); // Ambil kategori dari sesi


document.addEventListener('DOMContentLoaded', function() {

    // Fungsi untuk memformat tanggal
    function formatEventDate(dateString) {
        const date = new Date(dateString);
        const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        return new Intl.DateTimeFormat('en-GB', options).format(date);
    }

    function formatEventDateRange(eventDate, eventDateDay2) {
    const firstDate = new Date(eventDate);
    const secondDate = eventDateDay2 ? new Date(eventDateDay2) : null;

    // Format tanggal pertama
    const firstDay = firstDate.getDate();
    const firstMonth = firstDate.toLocaleString('en-US', { month: 'long' });
    const firstYear = firstDate.getFullYear();

    if (secondDate) {
        // Format tanggal kedua jika ada
        const secondDay = secondDate.getDate();

        // Asumsi bulan dan tahun sama untuk kedua tanggal
        return `${firstDay} & ${secondDay} ${firstMonth} ${firstYear}`;
    }

    // Jika hanya satu tanggal
    return `${firstDay} ${firstMonth} ${firstYear}`;
}

    // Fungsi untuk memfilter event berdasarkan event type
    function filterEvents() {
    const eventType = document.getElementById('event_type').value;
    const eventOptionsContainer = document.getElementById('event-options');
    eventOptionsContainer.innerHTML = '';

    if (eventType) {
        fetch(`/events-by-type/${eventType}?category=${selectedCategory}`)
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    data.forEach(event => {
                        const card = document.createElement('div');
                        card.classList.add('col-12', 'col-md-6', 'mb-4');

                        // Create price display with discount if applicable
                        let priceDisplay = '';
                        if (event.discount_percentage > 0) {
                            priceDisplay = `
                                <div class="price-container">
                                    <del class="text-muted">Rp. ${new Intl.NumberFormat().format(event.original_price)}</del>
                                    <div class="discounted-price text-success">Rp. ${new Intl.NumberFormat().format(event.price)}</div>
                                    <small class="text-success">(-${event.discount_percentage}%)</small>
                                </div>
                            `;
                        } else {
                            priceDisplay = `
                                <div class="price-container">
                                    <div>Rp. ${new Intl.NumberFormat().format(event.price)}</div>
                                </div>
                            `;
                        }

                        card.innerHTML = `
                            <div class="card event-card" 
                                data-id="${event.id}" 
                                data-name="${event.name}" 
                                data-price="${event.price}"
                                data-original-price="${event.original_price}"
                                data-discount="${event.discount_percentage}"
                                data-kuota="${event.kuota}"
                                data-date="${event.event_date}"
                                style="cursor: pointer;"
                                onclick="toggleCategory(this)">
                                <div class="card-body">
                                    <h5 class="card-title">${event.name}</h5>
                                    ${priceDisplay}
                                    ${event.kuota !== null ? `<p class="card-text">Kuota: ${event.kuota}</p>` : ''}
                                    <p class="card-text">Event Date: ${formatEventDateRange(event.event_date, event.event_date_day2)}</p>
                                </div>
                            </div>
                        `;
                        eventOptionsContainer.appendChild(card);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading events. Please try again later.');
            });
    }
}

function updateEventCards(eventType) {
    const eventCards = document.querySelectorAll('.event-card');

    eventCards.forEach(card => {
        const cardDate = card.getAttribute('data-date');
        const cardId = parseInt(card.getAttribute('data-id'), 10);

        // Terapkan kelas 'selected' jika kartu dipilih
        if (selectedCategories.some(cat => cat.id === cardId)) {
            card.classList.add('selected'); // Tambahkan kelas 'selected'
        } else {
            card.classList.remove('selected'); // Hapus kelas 'selected'
        }

        if (eventType === '1') {
            // Nonaktifkan kartu jika tanggalnya sudah dipilih
            if (selectedCategories.some(cat => cat.date === cardDate)) {
                card.classList.add('disabled');
                card.style.pointerEvents = 'none';
                card.style.opacity = '0.6';
            } else {
                card.classList.remove('disabled');
                card.style.pointerEvents = 'auto';
                card.style.opacity = '1';
            }
        } else {
            // Nonaktifkan filter tanggal untuk simposium
            card.classList.remove('disabled');
            card.style.pointerEvents = 'auto';
            card.style.opacity = '1';
        }
    });
}

    // Fungsi untuk toggle kategori
    window.toggleCategory = function (card) {
    const eventTypeDropdown = document.getElementById('event_type');
    if (!eventTypeDropdown) return; // Guard clause

    const selectedEventTypeName = eventTypeDropdown.options[eventTypeDropdown.selectedIndex]?.text || '';
    const eventType = eventTypeDropdown.value;
    
    const categoryId = parseInt(card.getAttribute('data-id'), 10);
    const categoryName = card.getAttribute('data-name');
    const categoryPrice = parseFloat(card.getAttribute('data-price'));
    const originalPrice = parseFloat(card.getAttribute('data-original-price'));
    const discountPercentage = parseFloat(card.getAttribute('data-discount'));
    const categoryKuota = card.getAttribute('data-kuota');
    const eventDate = card.getAttribute('data-date');

    const index = selectedCategories.findIndex(cat => cat.id === categoryId);

    // Cek apakah jenis event adalah Workshop dan pastikan tanggalnya berbeda dari event lain yang sudah dipilih
    if (eventType === '1' && selectedCategories.some(cat => cat.date === eventDate)) {
        alert('You can only choose events on different dates.');
        return;
    }

    if (index === -1) {
        // Jika jenis event adalah 1 (workshop), terapkan filter
        if (eventType === '1') {
            if (selectedCategories.length === 0) {
                selectedDate = eventDate;
            }

            // Jika tanggalnya berbeda, tambahkan kategori ke array
            if (selectedCategories.every(cat => cat.date !== eventDate)) {
                selectedCategories.push({
                    id: categoryId,
                    name: categoryName,
                    price: categoryPrice,
                    originalPrice: originalPrice,
                    discountPercentage: discountPercentage,
                    kuota: categoryKuota,
                    date: eventDate,
                    eventTypeName: selectedEventTypeName,
                    eventTypeId: eventType
                });
                card.classList.add('selected');
            } else {
                alert('You can only choose events on different dates.');
                return;
            }
        } else {
            selectedCategories.push({
                id: categoryId,
                name: categoryName,
                price: categoryPrice,
                originalPrice: originalPrice,
                discountPercentage: discountPercentage,
                kuota: categoryKuota,
                date: eventDate,
                eventTypeName: selectedEventTypeName,
                eventTypeId: eventType
            });
            card.classList.add('selected');
        }
    } else {
        selectedCategories.splice(index, 1);
        card.classList.remove('selected');
        if (selectedCategories.length === 0) {
            selectedDate = null;
        }
    }

    updateCategoryDetails();
    updateEventCards(eventType);
};




    // // Update status kartu event
    // function updateEventCards(eventType) {
    //     const eventCards = document.querySelectorAll('.event-card');

    //     eventCards.forEach(card => {
    //         const cardDate = card.getAttribute('data-date');

    //         if (eventType === '1') {
    //             // Terapkan filter tanggal untuk workshop
    //             if (selectedCategories.some(cat => cat.date === cardDate)) {
    //                 // Jika tanggal event sudah ada dalam kategori yang dipilih, disable kartu
    //                 card.classList.add('disabled');
    //                 card.style.pointerEvents = 'none';
    //                 card.style.opacity = '0.6';
    //             } else {
    //                 // Jika tanggal event belum ada dalam kategori yang dipilih, aktifkan kartu
    //                 card.classList.remove('disabled');
    //                 card.style.pointerEvents = 'auto';
    //                 card.style.opacity = '1';
    //             }
    //         } else {
    //             // Nonaktifkan filter tanggal untuk simposium
    //             card.classList.remove('disabled');
    //             card.style.pointerEvents = 'auto';
    //             card.style.opacity = '1';
    //         }
    //     });
    // }

    // Fungsi untuk memperbarui event type dan kategori
    function addCategoryToSelected(category) {
        const eventTypeDropdown = document.getElementById('event_type');
        const selectedEventTypeName = eventTypeDropdown.options[eventTypeDropdown.selectedIndex]?.text || '';
        const selectedEventTypeId = eventTypeDropdown.value;  // Ambil ID event type

        console.log("Adding category:", category);
        console.log("Selected Event Type Name:", selectedEventTypeName);

        // Pastikan kategori yang dipilih memiliki eventTypeName yang benar
        selectedCategories.push({
            name: category.name,
            price: category.price,
            eventTypeName: selectedEventTypeName,  // Menyertakan eventTypeName yang benar
            eventTypeId: selectedEventTypeId // Menyertakan eventTypeId jika perlu
        });
    }

    // Update detail kategori yang terpilih
    function updateCategoryDetails() {
    const categoryDetails = document.getElementById('category-details');
    const categoryName = document.getElementById('category-name');
    const categoryPrice = document.getElementById('category-price');
    const removeButton = document.getElementById('remove-event');

    if (selectedCategories.length > 0) {
        categoryDetails.style.display = 'block';

        // Buat list HTML untuk kategori yang dipilih, mencakup event type yang sesuai untuk setiap kategori
        const listItems = selectedCategories.map(cat => {
            return `
                <li>
                    <strong>${cat.eventTypeName}</strong> - ${cat.name} - Rp. ${new Intl.NumberFormat().format(cat.price)}
                </li>
            `;
        }).join('');

        // Tampilkan list dan total harga
        categoryName.innerHTML = `<ul>${listItems}</ul>`;
        categoryPrice.textContent = `Rp. ${new Intl.NumberFormat().format(selectedCategories.reduce((sum, cat) => sum + cat.price, 0))}`;

        // Tampilkan tombol Remove jika ada event yang dipilih
        removeButton.style.display = 'inline-block';
    } else {
        categoryDetails.style.display = 'none';
        removeButton.style.display = 'none';
    }
}




    // Memperbarui input tersembunyi untuk kategori yang dipilih
    function updateSelectedCategoriesInput() {
        const selectedCategoriesInput = document.getElementById('selected_categories');
        if (selectedCategories.length > 0) {
            selectedCategoriesInput.value = selectedCategories.map(cat => cat.id).join(',');
        } else {
            selectedCategoriesInput.value = '';
        }
    }

    

    function removeEvent() {
        // console.log('Remove button clicked');
        if (selectedCategories.length > 0) {
            selectedCategories.pop();
            updateCategoryDetails();
            updateSelectedCategoriesInput();
            updateEventCards(document.getElementById('event_type').value);
        }
    }

    // Menambahkan event listener untuk tombol Remove
    document.getElementById('remove-event').addEventListener('click', removeEvent);

    // Inisialisasi dropdown
    document.getElementById('event_type').addEventListener('change', filterEvents);
});

</script>
@endsection
