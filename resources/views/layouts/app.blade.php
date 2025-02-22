<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2ndBUS2025 - @yield('title')</title>

    <!-- Link ke CSS dari template -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    

    <style>
        .text-home {
            color: white;
        }
        /* Media Queries untuk responsif */
        @media (max-width: 768px) {
            /* Header Kontak menjadi kolom pada layar kecil */
            .header-contact {
                display: block; /* Menyusun elemen secara vertikal */
                text-align: left; /* Menyusun teks di kiri */
            }

            .header-contact i {
                margin-right: 5px;
            }

            .header-contact a {
                font-size: 0.9rem; /* Mengurangi ukuran font di layar kecil */
                margin-bottom: 5px;
            }

            /* Menyusun elemen navigasi secara vertikal */
            .header-nav {
                display: block;
                text-align: left;
                margin-top: 10px;
            }

            .header-nav a {
                font-size: 0.9rem; /* Ukuran font lebih kecil untuk teks navigasi */
                margin: 5px 0; /* Memberikan jarak antar link */
            }

            /* Menyesuaikan logo */
            .logo {
                width: 150px;
                height: auto;
                margin: 0 auto; /* Membuat logo terpusat */
            }

            h1 {
                font-size: 1.5rem; /* Ukuran font judul lebih kecil */
            }
        }

        /* Responsif untuk ukuran tablet dan lebih kecil */
        @media (max-width: 480px) {
            h1 {
                font-size: 1.3rem; /* Ukuran font judul lebih kecil di perangkat sangat kecil */
            }

            .btn {
                font-size: 0.9rem; /* Ukuran font tombol lebih kecil */
                padding: 10px 20px;
            }
            .whatapps {
                display: none;
            }
            .text-home {
                color: white !important;
                margin-left: -50px;
            }
            
        }
        @media (max-width: 768px) {
            .whatsapp-popup-btn {
                display: block;
                position: fixed;
                top: 20px;
                right: 20px;
                background-color: #25D366;
                color: white;
                padding: 15px 20px;
                border-radius: 50%;
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.3);
                font-size: 16px;
                text-align: center;
                z-index: 1000;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .whatsapp-popup-btn:hover {
                transform: scale(1.1);
            }

            .whatsapp-icon {
                margin-right: 10px;
            }
        }

        /* Mengatur tampilan untuk desktop */
        @media (min-width: 769px) {
            .whatsapp-popup-btn {
                display: none; /* Tidak tampil di layar besar */
            }
           
        }
    </style>

</head>
<body class="bg-slate-800">
    <!-- Header Bagian Atas -->
    <div class="container-fluid bg-dark-blue p-0" style="height: 30px;"> 
        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center py-1 px-5 header-contact">
                <div class="d-flex align-items-center">
                    <div class="whatapps">
                        <i class="fab fa-whatsapp text-white mr-2"></i> 
                        <a href="https://wa.me/628112694088?text=Halo,%20saya%20ingin%20bertanya!" target="_blank" class="text-white mr-4">+62 811-2694-088 </a> 
                    </div>
                    <i class="fas fa-envelope text-white mr-2"></i> 
                    <a href="mailto:contact.infinitymanagement@gmail.com" class="text-white">contact.infinitymanagement@gmail.com</a>
                </div>
                <div class="whatsapp-popup-btn" onclick="openWhatsappPopup()">
                    <i class="fab fa-whatsapp whatsapp-icon"></i>
                </div>
                
                <!-- <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="navbar-nav">
                        <a class="nav-link text-white" href="#">About</a>
                        <a class="nav-link text-white" href="#">Privacy</a>
                        <a class="nav-link text-white" href="#">Contact Us</a>
                    </div>
                </div> -->
            </div>
        </div>
    </div>

    <!-- Bagian Header -->
    <div class="container-fluid bg-dark px-3 py-3">
        <div class="row">
            <div class="col-12 text-left">
                <h1 class="text-white font-weight-bold ml-5">
                    <a href="{{ route('user-home') }}" class="text-home" style="text-decoration: none;">2nd BUS</a>
                </h1>
            </div>
        </div>
    </div>

    <!-- Bagian Konten -->
    <div class="container">
        @yield('content') 
    </div>

</body>

<script>
        // Fungsi untuk membuka WhatsApp di jendela popup
        function openWhatsappPopup() {
            const url = "https://wa.me/628112694088?text=Halo,%20saya%20ingin%20bertanya!";
            const width = 600;
            const height = 600;
            const left = (window.innerWidth / 2) - (width / 2);
            const top = (window.innerHeight / 2) - (height / 2);

            window.open(url, 'WhatsApp', `width=${width},height=${height},top=${top},left=${left}`);
        }
    </script>
</html>
