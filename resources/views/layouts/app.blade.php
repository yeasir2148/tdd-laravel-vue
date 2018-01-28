<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.atwho.css') }}">

    <script>
        window.myApp = {!! json_encode([
                'signedIn' => Auth::check(),
                'user' => Auth::check() ? Auth::user() : null,
            ]) !!};
    </script>

    <!-- <script>
        window.myApp = {
                'signedIn' : {{ Auth::check() }},
                'user' : {!! Auth::check() ? Auth::user() : null !!},
            };
    </script> -->

</head>
<body>
    <div id="app">

        @include('layouts.top-nav')

        @yield('content')

        
        <flash message="{{ session('flash') }}" ></flash>
    </div>

    <!-- Scripts -->
    @routes
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- <script src="{{ asset('js/jquery.atwho.js') }}"></script> -->
    <!-- <script src="{{ asset('js/jquery.caret.js') }}"></script> -->
    @yield('page_specific_js')
</body>
</html>
