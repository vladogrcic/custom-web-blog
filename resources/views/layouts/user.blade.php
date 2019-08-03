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
    <link href="{{ asset('css/frontend.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body>

    @include('_includes.nav.main')
    @yield('header')
    <div id="app" class="main-body">
        <div class="columns">
            <div class="column">
                @yield('content')
            </div>
            
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/frontend.js') }}"></script>
    @include('_includes.notifications.toast')
    @yield('scripts')
</body>
</html>
