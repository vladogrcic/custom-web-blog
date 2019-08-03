{{-- @extends('layouts.manage', ['title_text' => $title_text, 'item_type' => $page]) --}}
@extends('layouts.manage', ['title_text' => $title_text, 'item_type' => $page, 'customButton' => 
'<button form="form" class="button is-success is-pulled-right"><i class="fa fa-save"></i><span class="m-l-15">Save</span></button>'
])
@section('content')
    @if (!Route::is('users.create'))
        <form id="form" action="{{route('users.update', $user->id)}}" method="post">
    @else
        <form id="form" action="{{route('users.store')}}" method="post">
    @endif
        {{ csrf_field() }}
        @if (!Route::is('users.create'))
            {{method_field('PUT')}}
        @endif
        <div class="columns is-marginless">
            <div class="column is-paddingless">
                <div class="card full-height">
                    <div class="card-content">
                        <div class="field">
                            <label for="name" class="label">Name:</label>
                            <p class="control">
                                <input type="text" class="input" name="display_name" id="name" value="{{$user->display_name}}">
                            </p>
                        </div>
                        <div class="field">
                            <label for="username" class="label">Username:</label>
                            <p class="control">
                                <input type="text" class="input" name="name" id="username" value="{{$user->name}}">
                            </p>
                        </div>
                        <div class="field">
                            <label for="email" class="label">Email:</label>
                            <p class="control">
                                <input type="text" class="input" name="email" id="email" value="{{$user->email}}">
                            </p>
                        </div>
                        <div class="field">
                            <label for="password" class="label">Password</label>
                            @if (!(Route::current()->getName() === 'users.create'))
                                <div class="field">
                                    <b-radio type="is-info" name="password_options" v-model="password_options" native-value="keep">Do Not Change Password</b-radio>
                                </div>
                            @endif
                            <div class="field">
                                <b-radio type="is-info" name="password_options" v-model="password_options" native-value="auto">Auto-Generate New Password</b-radio>
                            </div>
                            <div class="field">
                                <b-radio type="is-info" name="password_options" v-model="password_options" native-value="manual">Manually Set New Password</b-radio>
                                <p class="control">
                                    <input type="text" class="input" pattern=".{6,}" title="Six or more characters" name="password" id="password" v-if="password_options == 'manual'" placeholder="Manually give a password to this user">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end of .column -->
            <div class="column is-paddingless">
                <div class="card full-height">
                    <div class="card-content">                    
                        <label for="roles" class="label">Roles:</label>
                        <input type="hidden" name="roles[]" :value="item" v-for="item in rolesSelected" v-if="rolesSelected"/>
                        @foreach ($roles as $role)
                            <div class="field">
                                <b-checkbox type="is-info" v-model="rolesSelected" :native-value="{{$role->id}}">{{$role->display_name}}</b-checkbox>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
    var app = new Vue({
        el: '#app',
        data: {
            password_options: "{{Route::current()->getName() === 'users.create'?'auto':'keep'}}",
            rolesSelected: {!! Route::current()->getName() === 'users.create'?json_encode([]):$user->roles->pluck('id') !!}
        }
    });
    </script>
@endsection
