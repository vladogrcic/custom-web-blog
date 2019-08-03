@php
    if (!function_exists('is_countable')) {
        function is_countable($var) {
            return (is_array($var) || $var instanceof Countable);
        }
    }
@endphp
@if(isset($languages))
    @if(is_countable($languages))
        @for ($i = 0; $i < count($languages); $i++)
        @if(isset($routeName))
            @if(
            $routeName == 'blog.single'||
            $routeName == 'blog.singleYear'||
            $routeName == 'blog.singleYearMonth'||
            $routeName == 'blog.singleYearMonthDay')
            @switch($settings['show_lang_switch'])
                @case('icons')
                <a href="{{ url('/locale').'/'.$languages[$i]->slug.'/single'}}"><span class="flag-icon flag-icon-{{$languages[$i]->slug}}"></span></a>
                @break
                @case('slugs')
                <a href="{{ url('/locale').'/'.$languages[$i]->slug.'/single'}}">{{strtoupper($languages[$i]->slug)}}</a>
                @break
                @case('iconsSlugs')
                <a href="{{ url('/locale').'/'.$languages[$i]->slug.'/single'}}"><span class="flag-icon flag-icon-{{$languages[$i]->slug}}"></span> {{strtoupper($languages[$i]->slug)}}</a>
                @break
                @case('fullName')
                <a href="{{ url('/locale').'/'.$languages[$i]->slug.'/single'}}">{{ucfirst($languages[$i]->name)}}</a>
                @break
                {{-- @default
                    Default case... --}}
            @endswitch
            @else
            @switch($settings['show_lang_switch'])
                @case('icons')
                <a href="{{ url('/locale').'/'.$languages[$i]->slug}}"><span class="flag-icon flag-icon-{{$languages[$i]->slug}}"></span></a>
                @break
                @case('slugs')
                <a href="{{ url('/locale').'/'.$languages[$i]->slug}}">{{strtoupper($languages[$i]->slug)}}</a>
                @break
                @case('iconsSlugs')
                <a href="{{ url('/locale').'/'.$languages[$i]->slug}}"><span class="flag-icon flag-icon-{{$languages[$i]->slug}}"></span> {{strtoupper($languages[$i]->slug)}}</a>
                @break
                @case('fullName')
                <a href="{{ url('/locale').'/'.$languages[$i]->slug}}">{{ucfirst($languages[$i]->name)}}</a>
                @break
                {{-- @default
                    Default case... --}}
            @endswitch
            @endif
        @endif
        @endfor
    @endif
@endif