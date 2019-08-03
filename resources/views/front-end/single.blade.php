@extends('layouts.app')

@section('content')
{{$article->slugDir}}
<!-- page header -->
<div class="main-header">
    <section class="hero">
        <div class="hero-body">
            {{-- <div class="container">
                <div class="has-text-left">
                    <h3 class="title is-3">{{$article->title}}</h3>
                </div>
            </div> --}}
        </div>
    </section>
</div>
<!-- end of page header -->

<!-- main page content -->
<div class="main-content">
    <div class="container">
        <div class="columns is-multiline">
            <div class="column is-8">
                <!-- start of post -->
                <div class="box">
                    <!-- box header -->
                    <div class="box-header">
                        <h4 class="title is-4">
                            {{$article->title}}
                            <span class="labels is-pulled-right">
                                @foreach ($article->categories as $key=>$category)
                                    <a href="{{$article->catUrl[$key]}}" class="has-text-info">{{$category->name}}</a>@if(count($article->categories)!==$key+1), @endif
                                @endforeach
                            </span>
                        </h4>
                        <div class="m-b-25">
                            @if($article->checkGravatar&&$article->gravatar)
                                <img class="user-avatar" width="50" height="50" src="{{$article->gravatarUrl}}">
                            @else
                                @if($article->avatar)
                                    <img class="user-avatar" width="50" height="50" src="{{url('storage/avatars/thumb-50/'.$article->avatar)}}">
                                @else
                                    <img class="user-avatar" width="50" height="50" src="{{url('/')}}/images/user_anonymous.svg">
                                @endif
                            @endif
                            <a href="{{$article->authorUrl}}">
                                {{$article->user->display_name}}
                            </a>
                            <span class="icon"><i class="fa fa-calendar"></i></span>
                            {!!$article->published_at->date!!} 
                            <span class="icon"><i class="fa fa-clock-o"></i></span>
                            {{$article->published_at->time}}
                        </div>
                    </div>
                    <!-- end of box header -->
                    <!-- box content -->
                    <div class="box-content">
                        <div class="box-content-text">
                            @if($article->featured_image)
                            <div class="blog-image" style="background-image: url({{url('/').Storage::url('content/thumb-640/'.$article->featured_image)}});"> </div>
                            @endif
                            {!!$article->content!!}
                        </div>
                        <div class="has-text-right">
                            <a class="button is-small" href="{{ url()->previous()==url()->current()?url('/blog'):url()->previous() }}">Back
                                to Home</a>
                        </div>
                    </div>
                    <!-- end box content -->
                </div>
                <!-- end of post -->



            </div>
            <!-- end of post -->

            <!-- sidebar content -->
            <div class="column is-4 is-narrow">
                <!-- sidebar subscriptions -->
                <div class="card-wrapper">
                    <div class="card">
                        <header class="card-header">
                            <p class="card-header-title">
                                Subscribe
                            </p>
                        </header>
                        <div class="card-content">
                            <div class="content">
                                <p>Some text about subscribing. Whatever you might want. You know.</p>
                                <form>
                                    <div class="field">
                                        <p class="control">
                                            <input class="input" type="email" placeholder="Email">
                                        </p>
                                    </div>
                                    <div class="field is-grouped">
                                        <p class="control">
                                            <button class="button">Subscribe</button>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end of sidebar subscriptions -->
            </div>
        </div>
    </div>
</div>
<!-- end of main page content -->
@endsection