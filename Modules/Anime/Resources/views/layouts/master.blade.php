<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    @include('anime::includes.meta')

    <link
        href="https://fonts.googleapis.com/css2?family=Baloo+Tamma+2:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap"
        rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/font-awesome-line-awesome/css/all.min.css"
        rel="stylesheet">

    <link href="{{ asset('modules/anime/css/app.css') }}" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    @include('anime::includes.header')

    <div id="body">
        <div class="container">
            @yield('content')
        </div>
    </div>

    @include('anime::includes.footer')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/4.2.8/js/tooltipster.bundle.min.js" type="text/javascript"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/js/bootstrap.min.js" type="text/javascript"></script>

    <script src="{{ asset('modules/anime/js/app.js') }}"></script>
</body>

</html>
