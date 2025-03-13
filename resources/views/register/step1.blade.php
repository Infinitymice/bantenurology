@extends('layouts.app')

@section('title', 'Step 1 - Registration Form')

@section('content')
<div class="container mt-5">    
    @if(session('error'))
        <div id="alert-box" class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row" id="form-container">
        <div class="col-md-6" style="padding-bottom: 20px;">
            <div class="card">
                <div class="card-body">
                    <img src="{{ asset('logo/LOGO.png') }}" alt="Logo" class="mb-3 mx-auto d-block" style="width: 220px; height: auto;">
                    <h3 class="text-center" style="font-weight: bold">Registration Form</h3>
                    <form action="{{ route('register.step1') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="full_name" class="bold-label">Full Name</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Full Name" value="{{ old('full_name', $formData['full_name'] ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="nik" class="bold-label">NIK / National Identity Number</label>
                            <input type="text" id="nik" name="nik" class="form-control" placeholder="NIK" value="{{ old('nik', $formData['nik'] ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="institusi" class="bold-label">Affiliation Or Institution/City</label>
                            <input type="text" id="institusi" name="institusi" class="form-control" placeholder="Affiliation/Institution" value="{{ old('institusi', $formData['institusi'] ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="bold-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Email" value="{{ old('email', $formData['email'] ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="event_type" class="bold-label">Category</label>
                            <select id="category" name="category" class="form-control" onchange="toggleSpecialistInput()" required>
                                <option value="">Choose Category Type</option>
                                <option value="Student" {{ old('category', $formData['category'] ?? '') == 'Student' ? 'selected' : '' }}>Student</option>
                                <option value="General Practitioner/Resident" {{ old('category', $formData['category'] ?? '') == 'General Practitioner/Resident' ? 'selected' : '' }}>General Practitioner/Resident</option>
                                <option value="Specialist" {{ old('category', $formData['category'] ?? '') == 'Specialist' ? 'selected' : '' }}>Specialist</option>
                            </select>
                        </div>
                        <div class="mb-3" id="specialist-input" style="display: {{ old('category', $formData['category'] ?? '') === 'Specialist' ? 'block' : 'none' }}">
                            <label for="specialistDetail" class="bold-label">Sp.U (or other specialist)</label>
                            <input type="text" id="specialistDetail" name="specialistDetail" class="form-control" placeholder="Sp.U (or other specialist)" value="{{ old('specialistDetail', $formData['specialistDetail'] ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="bold-label">Phone/WhatsApp</label>
                            <input type="text" id="phone" name="phone" class="form-control" placeholder="Phone/WhatsApp Number" value="{{ old('phone', $formData['phone'] ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="bold-label">Address</label>
                            <input type="text" id="address" name="address" class="form-control" placeholder="Address" value="{{ old('address', $formData['address'] ?? '') }}" required>
                        </div>

                        <!-- Tambahkan setelah form yang ada -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="inputVoucher" name="voucher" 
                                    {{ old('voucher', session('voucher_code') ? 'checked' : '') }}>
                                <label class="form-check-label" for="inputVoucher">Have Voucher?</label>
                            </div>
                        </div>
                        
                        <div id="groupRegistrationInfo" style="display: {{ old('voucher', session('voucher_code')) ? 'block' : 'none' }}">
                            <div class="mb-3">
                                <label for="voucher_code" class="bold-label">Code Voucher</label>
                                <input type="text" id="voucher_code" name="voucher_code" class="form-control" 
                                    value="{{ old('voucher_code', session('voucher_code')) }}" 
                                    placeholder="Input Your Voucher Code">
                            </div>
                        </div>

                        <script>
                            document.getElementById('inputVoucher').addEventListener('change', function() {
                                const groupInfo = document.getElementById('groupRegistrationInfo');
                                if (this.checked) {
                                    groupInfo.style.display = 'block';
                                } else {
                                    groupInfo.style.display = 'none';
                                    document.getElementById('voucher_code').value = '';
                                }
                            });
                            
                            document.querySelector('form').addEventListener('submit', function(e) {
                                const voucherChecked = document.getElementById('inputVoucher').checked;
                                const voucherCode = document.getElementById('voucher_code').value;

                                if (voucherChecked && !voucherCode) {
                                    e.preventDefault();
                                    alert('Please enter a voucher code or uncheck the voucher option');
                                    return false;
                                }
                            });
                        </script>
                        <script>
                            // Fungsi untuk menampilkan/menghilangkan input deskripsi berdasarkan kategori yang dipilih
                            function toggleSpecialistInput() {
                                var categoryType = document.getElementById('category').value;
                                var specialistInput = document.getElementById('specialist-input');
                                
                                if (categoryType === 'Specialist') {
                                    specialistInput.style.display = 'block';
                                } else {
                                    specialistInput.style.display = 'none';
                                    specialistDetail.value = '';
                                }
                            }
                        
                            // Memanggil toggleSpecialistInput() jika kategori sudah diset sebelumnya di form
                            window.onload = function() {
                                toggleSpecialistInput();
                            };
                            
                            // JavaScript untuk menghilangkan alert setelah 5 detik
                            setTimeout(function() {
                                var alertBox = document.getElementById('alert-box');
                                var formContainer = document.getElementById('form-container');
                                if (alertBox) {
                                    alertBox.classList.remove('show');
                                    alertBox.classList.add('fade');
                                    formContainer.style.marginTop = "0";
                                }
                            }, 5000); // 5000ms = 5 detik
                        </script>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="agree" required>
                            <label class="form-check-label" for="agree">I agree to the <a href="#">terms and conditions</a></label>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary w-100 mt-3">Next</button>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('user-home') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-arrow-left"></i> Back to Home
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Informasi dan panduan tambahan -->
        <div class="col-md-6 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="text-center mb-4" style="font-weight: bold">Information</h3>
                    <p class="text-center">                         
                        Here, you can find guidelines and information related to registration. Please ensure you fill out all fields with accurate information.
                    </p>
                    <ul>
                        <li>First and last names are required.</li>
                        <li>Make sure your email address is valid.</li>
                        <li>Ensure the phone number entered is active.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk menampilkan/menghilangkan input deskripsi berdasarkan kategori yang dipilih
    function toggleSpecialistInput() {
        var categoryType = document.getElementById('category').value;
        var specialistInput = document.getElementById('specialist-input');
        
        if (categoryType === 'Specialist') {
            specialistInput.style.display = 'block';
        } else {
            specialistInput.style.display = 'none';
            specialistDetail.value = '';
        }
    }

    // Memanggil toggleSpecialistInput() jika kategori sudah diset sebelumnya di form
    window.onload = function() {
        toggleSpecialistInput();
    };
    
    // JavaScript untuk menghilangkan alert setelah 5 detik
    setTimeout(function() {
        var alertBox = document.getElementById('alert-box');
        var formContainer = document.getElementById('form-container');
        if (alertBox) {
            alertBox.classList.remove('show');
            alertBox.classList.add('fade');
            formContainer.style.marginTop = "0";
        }
    }, 5000); // 5000ms = 5 detik
    // Add this before the closing 
</script> 
<script>
        // Fungsi untuk menampilkan/menghilangkan input deskripsi berdasarkan kategori yang dipilih
        function toggleSpecialistInput() {
            var categoryType = document.getElementById('category').value;
            var specialistInput = document.getElementById('specialist-input');
            
            if (categoryType === 'Specialist') {
                specialistInput.style.display = 'block';
            } else {
                specialistInput.style.display = 'none';
                specialistDetail.value = '';
            }
        }
    
        // Memanggil toggleSpecialistInput() jika kategori sudah diset sebelumnya di form
        window.onload = function() {
            toggleSpecialistInput();
        };
        
        // JavaScript untuk menghilangkan alert setelah 5 detik
        setTimeout(function() {
            var alertBox = document.getElementById('alert-box');
            var formContainer = document.getElementById('form-container');
            if (alertBox) {
                alertBox.classList.remove('show');
                alertBox.classList.add('fade');
                formContainer.style.marginTop = "0";
            }
        }, 5000); // 5000ms = 5 detik
        // Add this before the closing 
</script>
<script>
            // Add group code validation
            document.getElementById('voucher_code').addEventListener('blur', function() {
                const code = this.value;
                const inputVoucherChecked = document.getElementById('inputVoucher').checked;
                
                if (code && inputVoucherChecked) {
                    fetch('/check-voucher-code', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ code: code })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const input = document.getElementById('voucher_code');
                        const helpText = document.getElementById('voucher_codeHelp');
                        
                        if (data.valid) {
                            input.classList.remove('is-invalid');
                            input.classList.add('is-valid');
                            helpText.textContent = `Valid voucher: ${data.discount_percentage}% discount`;
                            helpText.classList.remove('text-danger');
                            helpText.classList.add('text-success');
                        } else {
                            input.classList.remove('is-valid');
                            input.classList.add('is-invalid');
                            helpText.textContent = data.message;
                            helpText.classList.remove('text-success');
                            helpText.classList.add('text-danger');
                            // Clear the input if invalid
                            this.value = '';
                        }
                    });
                }
            });
</script>
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const voucherChecked = document.getElementById('inputVoucher').checked;
        const voucherCode = document.getElementById('voucher_code').value;
        const voucherValid = document.getElementById('voucher_code').classList.contains('is-valid');

        if (voucherChecked && voucherCode && !voucherValid) {
            e.preventDefault();
            alert('Please enter a valid voucher code or uncheck the voucher option');
            return false;
        }
    });
</script>
@endsection
