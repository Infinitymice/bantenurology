<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Home Dashboard')</title>

    <!-- Include Limitless CSS -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/global_assets/css/icons/icomoon/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/css/bootstrap_limitless.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/css/layout.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/css/components.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/css/colors.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/css/custom.css') }}" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- KioskBoard CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/kioskboard@2.3.0/dist/kioskboard-2.3.0.min.css">
</head>
<body>
    <!-- Navbar -->
    @include('admin.layoutshome.navbar')

    <!-- Page Container -->
    <div class="page-container">
        <!-- Page Content -->
        <div class="page-content">
            <div class="content-wrapper">
                <div class="content">
                    @yield('content')
                </div>
            </div>
        </div>
        <!-- /Page Content -->
    </div>
    <!-- /Page Container -->

    <!-- Include Limitless JS -->


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/admin/global_assets/js/main/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/loaders/pace.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/loaders/blockui.min.js') }}"></script>
    <script src="{{ asset('assets/admin/global_assets/js/main/bootstrap.bundle.min.js') }}"></script>
      <!-- KioskBoard JS -->
    <script src="https://cdn.jsdelivr.net/npm/kioskboard@2.3.0/dist/kioskboard-2.3.0.min.js"></script> 
      
   

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.0.1/minified/html5-qrcode.min.js"></script>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particlesjs/2.2.2/particles.min.js"></script>


    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script> -->
    <!-- <script src="{{ asset('assets/js/bower.json') }}"></script> -->
    <!-- <script src="{{ asset('assets/js/package.json') }}"></script> -->
    <script src="{{ asset('assets/js/particles.min.js') }}"></script>


    @yield('scripts')
</body>
</html>
