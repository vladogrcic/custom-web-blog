@extends('layouts.manage', ['title_text' => '<i class="fa fa-dashboard"></i>', 'item_type' => ' Dashboard'])
@section('content')
    <div class="columns">
        <div class="column">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        Number of Posts
                    </p>
                    
                </header>
                <div class="card-content">
                    <div class="content">
                        <h1>{{$num['posts']}}</h1>
                    </div>
                </div>
                {{-- <footer class="card-footer">
                    <a href="#" class="card-footer-item">Save</a>
                    <a href="#" class="card-footer-item">Edit</a>
                    <a href="#" class="card-footer-item">Delete</a>
                </footer> --}}
            </div>
        </div>
        <div class="column">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        Number of Categories
                    </p>
                    
                </header>
                <div class="card-content">
                    <div class="content">
                        <h1>{{$num['cats']}}</h1>
                    </div>
                </div>
                {{-- <footer class="card-footer">
                    <a href="#" class="card-footer-item">Save</a>
                    <a href="#" class="card-footer-item">Edit</a>
                    <a href="#" class="card-footer-item">Delete</a>
                </footer> --}}
            </div>
        </div>
    </div>
    <div class="columns">
        <div class="column">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        Number of Tags
                    </p>
                    
                </header>
                <div class="card-content">
                    <div class="content">
                        <h1>{{$num['tags']}}</h1>
                    </div>
                </div>
                {{-- <footer class="card-footer">
                    <a href="#" class="card-footer-item">Save</a>
                    <a href="#" class="card-footer-item">Edit</a>
                    <a href="#" class="card-footer-item">Delete</a>
                </footer> --}}
            </div>
        </div>
        <div class="column">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        Number of Languages
                    </p>
                    
                </header>
                <div class="card-content">
                    <div class="content">
                        <h1>{{$num['langs']}}</h1>
                    </div>
                </div>
                {{-- <footer class="card-footer">
                    <a href="#" class="card-footer-item">Save</a>
                    <a href="#" class="card-footer-item">Edit</a>
                    <a href="#" class="card-footer-item">Delete</a>
                </footer> --}}
            </div>
        </div>
        @permission('read-users')
        <div class="column">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        Number of Users
                    </p>
                    
                </header>
                <div class="card-content">
                    <div class="content">
                        <h1>{{$num['users']}}</h1>
                    </div>
                </div>
                {{-- <footer class="card-footer">
                    <a href="#" class="card-footer-item">Save</a>
                    <a href="#" class="card-footer-item">Edit</a>
                    <a href="#" class="card-footer-item">Delete</a>
                </footer> --}}
            </div>
        </div>
        @endpermission
    </div>
@endsection