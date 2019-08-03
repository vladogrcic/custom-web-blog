@extends('layouts.app')

@section('header')
    <section class="hero is-info is-bold m-t-50">
        <div class="hero-body">
            <div class="container">
            <h1 class="title">
                @if($other['main-title-type'] == 'Blog')
                    {{$other['main-title-type']}}: {{$other['comp']->name}}
                @elseif($other['main-title-type'] == 'Month')
                    {{$other['secondary-title-type']}}: {{$other['comp'][0]->display_name}}
                    {{$other['main-title-type']}}: {{$other['comp'][1]->display_name}}
                @elseif($other['main-title-type'] == 'Day')
                    {{$other['tertiary-title-type']}}: {{$other['comp'][0]->display_name}}
                    {{$other['secondary-title-type']}}: {{$other['comp'][1]->display_name}}
                    {{$other['main-title-type']}}: {{$other['comp'][2]->display_name}}
                @else
                    @if($other['comp']->display_name)
                        {{$other['main-title-type']}}: {{$other['comp']->display_name}}
                    @else
                        {{$other['main-title-type']}}: {{$other['comp']->name}}
                    @endif
                @endif
            </h1>
            <h2 class="subtitle">
                @if($other['main-title-type'] == 'Month')
                    {{$other['comp'][0]->description}}
                    {{$other['comp'][1]->description}}
                @elseif($other['main-title-type'] == 'Day')
                    {{$other['comp'][0]->description}}
                    {{$other['comp'][1]->description}}
                    {{$other['comp'][2]->description}}
                @else
                    {{$other['comp']->description}}
                @endif
            </h2>
            </div>
        </div>
    </section>
@endsection
@section('content')
    <section class="main-blog">
        <div class="columns is-multiline">
            <div class="column">
                @foreach ($articles as $article)
                    <!-- start of post -->
                    <div class="box">
                        <!-- box header -->
                        <div class="box-header">
                            <h4 class="title is-4">{{$article->title}}</h4>
                                <h3 class="subtitle is-5">
                                {{trans_choice('front-end/blog.category', count($article->categories))}}: 
                                <span class="labels is-dark">
                                    @foreach ($article->categories as $key=>$category)
                                        @if ($disLang)
                                            <a href="{{url('/').'/'.$blog_slug.'/'.$article->catUrl[$key]}}" class="has-text-info">{{$category->name}}</a>@if(count($article->categories)!==$key+1), @endif
                                        @else
                                            <a href="{{url('/').'/'.$article->language->slug.'/'.$blog_slug.'/'.$article->catUrl[$key]}}" class="has-text-info">{{$category->name}}</a>@if(count($article->categories)!==$key+1), @endif
                                        @endif
                                    @endforeach
                                </span>
                            </h3>
                            <div class="m-b-25">
                                <img class="user-avatar" width="50" height="50" src="{{$article->authorAvatar}}">
                                <a href="{{$article->authorUrl}}">
                                    {{$article->user->display_name}}
                                </a>
                                <span class="icon"><i class="fa fa-calendar"></i></span>
                                {{-- {!!$article->published_at->date!!}  --}}
                                <a href="{{url('/').'/'.$blog_slug.'/'.$article->published_at->dateYear.'/'.$article->published_at->dateMonth.'/'.$article->published_at->dateDay.'/'}}">{{$article->published_at->dateDay}}</a>.
                                <a href="{{url('/').'/'.$blog_slug.'/'.$article->published_at->dateYear.'/'.$article->published_at->dateMonth.'/'}}">{{$article->published_at->dateMonth}}</a>.
                                <a href="{{url('/').'/'.$blog_slug.'/'.$article->published_at->dateYear.'/'}}">{{$article->published_at->dateYear}}</a>.

                            </div>
                        </div>
                        <!-- end of box header -->
                        <!-- box content -->
                        <div class="box-content m-t-15">
                            <div class="columns">
                                <div class="column is-5">
                                    @if($article->featured_image)
                                        <div class="blog-image" style="background-image: url({{url('/').Storage::url('content/thumb-640/'.$article->featured_image)}});"> </div>
                                    @else
                                <div class="blog-image" style="background-image: url({{url('/')}}/images/placeholder-250x250.png);"> </div>
                                    @endif
                                </div>
                                <div class="column">
                                    <div class="box-content-text">
                                        @if($article->excerpt)
                                            {{ $article->excerpt }}
                                        @else 
                                            {{ strip_tags(substr($article->content, 0, 350)) }}
                                        @endif
                                    </div>
                                    <div class="has-text-left m-t-10">
                                        @if ($disLang)
                                            <a class="button is-small" href="{{url('/').'/'.$blog_slug.'/'.$article->url}}">{{__('front-end/blog.read-more')}}</a>
                                        @else
                                            <a class="button is-small" href="{{url('/').'/'.$article->language->slug.'/'.$blog_slug.'/'.$article->url}}">{{__('front-end/blog.read-more')}}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end box content -->
                        <div class="box-footer m-t-15">
                            @if(count($article->tags))
                                {{trans_choice('front-end/blog.tag', count($article->tags))}}: 
                                <span class="tags m-t-10">
                                    {{-- @foreach ($article->tags as $tag) --}}
                                    @foreach ($article->tags as $key=>$tag)
                                        @if ($disLang)
                                            <a href="{{url('/').'/'.$blog_slug.'/'.$article->tagUrl[$key]}}" class="tag is-info">{{$tag->name}}</a>
                                        @else
                                            <a href="{{url('/').'/'.$article->language->slug.'/'.$blog_slug.'/'.$article->tagUrl[$key].$tag->slug}}" class="tag is-info">{{$tag->name}}</a>
                                        @endif
                                    @endforeach
                                </span>
                            @endif
                        </div>
                    </div>
                    <!-- end of post -->
                @endforeach

                @if ($articles != null)
                    {{$articles->links()}}
                @endif
                <!-- end of pagination -->
            </div>
        </div>
    </section>
@endsection
