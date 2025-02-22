<!-- resources/views/admin/template/navbar.blade.php -->
<style>
    .navbar-container {
        position: fixed;
        top: -60px; /* Hide navbar by default */
        left: 0;
        right: 0;
        transition: top 0.3s ease-in-out;
        z-index: 1030;
    }

    .navbar-container:hover,
    .navbar-container.show-navbar {
        top: 0;
    }

    .navbar-trigger {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 10px;
        z-index: 1029;
    }
</style>

<div class="navbar-trigger"></div>
<div class="navbar-container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.auth.login') }}">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('qr.index') }}">Search & Cetak QR</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('qr.indexScan') }}">Absensi QR Scan</a>
                </li>
            </ul>
        </div>
    </nav>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navbarContainer = document.querySelector('.navbar-container');
    const navbarTrigger = document.querySelector('.navbar-trigger');
    let timeoutId;

    function showNavbar() {
        clearTimeout(timeoutId);
        navbarContainer.classList.add('show-navbar');
    }

    function hideNavbar() {
        timeoutId = setTimeout(() => {
            if (!navbarContainer.matches(':hover')) {
                navbarContainer.classList.remove('show-navbar');
            }
        }, 300);
    }

    navbarTrigger.addEventListener('mouseenter', showNavbar);
    navbarContainer.addEventListener('mouseenter', showNavbar);
    navbarContainer.addEventListener('mouseleave', hideNavbar);
    navbarTrigger.addEventListener('mouseleave', hideNavbar);
});
</script>
