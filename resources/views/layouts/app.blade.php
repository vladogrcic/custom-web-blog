<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(File::exists('storage/favicon.png'))
        <link rel="icon" type="image/png" href="{{ Storage::url('favicon.png') }}">
    @else
        <link rel="icon" type="image/png" href="{{ URL::to('/').'/favicon.ico' }}">
    @endif
    
    @inject('settings', 'App\Containers\SettingsContainer')

    @php
        $settings = $settings->get();
    @endphp
    @if(isset($settings['disable_language_group']))
        @if($settings['disable_language_group'])
            @php
                $languages = '';
            @endphp
        @else 
            @inject('languages', 'App\Language')
            @php
                $languages = $languages->get();
            @endphp
        @endif
    @endif

    @if(isset($siteMeta['site-title']))
        @if($settings['title'])
            <title>{{ $siteMeta['site-title'] }} - {{ $settings['title'] }}</title>
            <meta property="og:title" content="{{ $siteMeta['site-title'] }} - {{ $settings['title'] }}">
        @else
            <title>{{ $siteMeta['site-title'] }} - {{ config('app.name', 'Laravel') }}</title>
            <meta property="og:title" content="{{ $siteMeta['site-title'] }} - {{ config('app.name', 'Laravel') }}">
        @endif
    @else
        @if($settings['title'])
            <title>{{ $settings['title'] }}</title>
            <meta property="og:title" content="{{ $settings['title'] }}">
        @else
            <title>{{ config('app.name', 'Laravel') }}</title>
            <meta property="og:title" content="{{ config('app.name', 'Laravel') }}">
        @endif
    @endif

    @if(isset($siteMeta['site-description']))
            <meta property="og:description" content="{{ $siteMeta['site-description'] }}">
    @else
        @if($settings['description'])
            <meta property="og:description" content="{{ $settings['description'] }}">
        @else
            <meta property="og:description" content="{{ config('app.name', 'Laravel') }}">
        @endif
    @endif

    @if(isset($siteMeta['site-image']))
        <meta property="og:image" content="{{ asset('storage/content/original/'.$siteMeta['site-image']) }}">
    @else
        @if($settings['image'])
            <meta property="og:image" content="{{ asset('storage/content/original/'.$settings['image']) }}">
        @else
            <meta property="og:image" content="">
        @endif
    @endif

    @if(isset($siteMeta['site-url']))
        <meta property="og:url" content="{{ $siteMeta['site-url'] }}">
    @else
        @if($settings['url'])
            <meta property="og:url" content="{{ $settings['url'] }}">
        @else
            <meta property="og:url" content="">
        @endif
    @endif

    @if($settings['description'])
        <meta name="description" content="{{ $settings['description'] }}">
    @else
        <meta name="description" content="{{ config('app.name', 'Laravel') }}">
    @endif
        
    <!-- Styles -->
    <link href="{{ asset('css/frontend.css') }}" rel="stylesheet">
    @yield('styles')
    <style>[v-cloak] { display: none; }</style>

    <script>
        window.siteUrl = '{{url("/")}}';
    </script>
    @php
        $routeName = '';
        if(request()->route())$routeName = request()->route()->getName();
    @endphp
    <!--
        Google Analytics
    -->
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-134060015-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-134060015-2');
    </script>
</head>
<body style="min-height: 92vh;">
    @include('_includes.nav.main', ['languages'=>$languages, 'settings'=>$settings, 'routeName'=> $routeName])
    @yield('header')
    @include('cookieConsent::index')

    <div id="app" class="main-body">
        <div class="columns">
            <div class="column">
                @yield('content')
            </div>
            @if(!empty($articles))
                <div class="column right-sidebar">
                    @include('_includes.nav.right-sidebar')
                </div>
            @endif
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/frontend.js') }}"></script>
    @include('_includes.notifications.toast')
    @yield('scripts')
</body>
</html>
