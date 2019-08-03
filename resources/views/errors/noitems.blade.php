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
                @else
                    {{$other['main-title-type']}}: {{$other['comp']->display_name}}
                @endif
            </h1>
            <h2 class="subtitle">
                @if($other['main-title-type'] == 'Month')
                    {{$other['comp'][0]->description}}
                    {{$other['comp'][1]->description}}
                @else
                    {{$other['comp']->description}}
                @endif
            </h2>
            </div>
        </div>
    </section>
@endsection
@section('content')
    <section class="main-blog" style="margin-top: 100px; margin-bottom: 5vh; text-align:center;">
        <div class="columns is-multiline">
            <div class="column">
                <div class="card">
                    <div class="card-content">
                        <p style="font-size: 35px;">Nothing to see here.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
