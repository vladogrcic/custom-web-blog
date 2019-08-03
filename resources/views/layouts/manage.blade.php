<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @if(File::exists('storage/favicon.png'))
            <link rel="icon" type="image/png" href="{{ Storage::url('favicon.png') }}">
        @else
            <link rel="icon" type="image/png" href="{{URL::to('/').'/favicon.ico'}}">
        @endif

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @inject('settings', 'App\Containers\SettingsContainer')
        @php
            $settings = $settings->get();
        @endphp
        @if($settings['title'])
            <title>{{ $settings['title'] }} - Management</title>
        @else
            <title>{{ config('app.name', 'Laravel') }} - Management</title>
        @endif

        @if($settings['description'])
            <meta name="description" content="{{ $settings['description'] }}">
        @else
            <meta name="description" content="{{ config('app.name', 'Laravel') }}">
        @endif
        <!-- Styles -->
        <link href="{{ asset('css/backend.css') }}" rel="stylesheet">
        {{-- <script src="{{ asset('js/highlight/highlight.js') }}"></script>
        <link href="{{ asset('js/highlight/styles/default.css') }}" rel="stylesheet"> --}}
        <link rel="stylesheet"
        href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/atelier-dune-dark.min.css">
        {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> --}}
        <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
        @yield('styles')
        <script>
            window.siteUrl = '{{url("/")}}';
        </script>
    </head>
    <body>
        @include('_includes.nav.main')
        <div id="main-content">
            @include('_includes.nav.manage')
            @yield('menu-content')

            <div class="management-area" id="app">
                <div class="flex-container">
                    @if(isset($title_text) && isset($item_type))
                    <div class="card m-b-10">
                        <div class="card-content">
                            <div class="columns">
                                <div class="column">
                                    <h1 class="title">{!! $title_text !!} {!! ucfirst($item_type) !!}</h1>
                                </div>
                                <div class="column">
                                    @if(isset($customButton))
                                        {!! $customButton !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @yield('content')
                </div>
            </div>
            
            <!-- Scripts -->
            <script src="{{ asset('js/backend.js') }}"></script>
            {{-- <script src="{{ asset('js/tinymce/tinymce.js') }}"></script> --}}
            @include('_includes.notifications.toast')
            @yield('scripts')
            @yield('menu-scripts')
        {{-- <script src="{{asset("js/highlight/highlight.js")}}"></script>

            <script>hljs.initHighlightingOnLoad();</script> --}}
            
        </div>
    </body>
</html>