<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ALI Seafood IS</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@200&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css"/>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Raleway', sans-serif;
        }
    </style>
    <!-- Styles -->
</head>
<body>
    <div class="container-fluid">
        <div class="d-flex flex-column min-vh-100 justify-content-center align-items-center">
            <div class="row">
                <img src="{{ asset('/images/ali-logo.png') }}"  width="100" height="100">
            </div>
            <div class="row p-3">
                <h2>PT. ANUGRAH LAUT INDONESIA</h2>
            </div>
            <div class="row text-center p-5" style="display: inline;">
                @if (Route::has('login'))
                @auth
                <a class="text-secondary text-decoration-none" href="{{ url('/home') }}">Dashboard</a>
                @else
                <a class="text-secondary text-decoration-none" href="{{ route('login') }}">ALI IS</a>
                @endauth
                @endif
                <a class="text-secondary text-decoration-none" target="_blank" href="https://aliseafood.co.id/">Official website</a>
            </div>
        </div>
    </div>
</body>
</html>
