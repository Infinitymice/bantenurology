<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Limitless</title>

    <!-- Link ke CSS Limitless -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/global_assets/css/icons/icomoon/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/css/bootstrap_limitless.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/css/layout.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/css/layout.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/css/components.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin/css/colors.min.css') }}" rel="stylesheet" type="text/css">

    <!-- <style>
		/* Custom CSS to make the login container white */
		.login-form .card {
			background-color: white;
			border: 1px solid #ddd; /* optional, adds a subtle border */
		}
	</style> -->
</head>
<body>
    <!-- Main Content -->
    @yield('content')

    <!-- JS Scripts -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/admin/global_assets/js/main/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/loaders/pace.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/loaders/blockui.min.js') }}"></script>
    <!-- <script src="{{ asset('assets/admin/global_assets/js/main/bootstrap.bundle.min.js') }}"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
