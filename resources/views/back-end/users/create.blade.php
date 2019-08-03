@extends('layouts.manage')

@section('content')
  <div class="flex-container">
    <div class="columns m-t-10">
      <div class="column">
        <h1 class="title">Create New User</h1>
        @if ($errors->any())
          <div class="is-size-6 has-text-danger has-text-weight-bold">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
        @endif
      </div>
    </div>
    <hr class="m-t-0">
    <form action="{{route('users.store')}}" method="POST">
      {{csrf_field()}}
      <div class="columns">
        <div class="column">
          <div class="field">
            <label for="name" class="label">Name</label>
            <p class="control">
              <input type="text" class="input" name="display_name" id="name">
            </p>
          </div>
          <div class="field">
            <label for="username" class="label">Username</label>
            <p class="control">
              <input type="text" class="input" name="name" id="username">
            </p>
          </div>
          <div class="field">
            <label for="email" class="label">Email:</label>
            <p class="control">
              <input type="text" class="input" name="email" id="email">
            </p>
          </div>
          <div class="field">
            <label for="password" class="label">Password</label>
            <p class="control">
              <input type="text" class="input" name="password" id="password" v-if="!auto_password" placeholder="Manually give a password to this user">
              <b-checkbox type="is-info" name="auto_generate" class="m-t-15" v-model="auto_password">Auto Generate Password</b-checkbox>
            </p>
          </div>
        </div> <!-- end of .column -->
        <div class="column">
          <label for="roles" class="label">Roles:</label>
          <input type="hidden" name="roles[]" :value="item" v-for="item in rolesSelected"/>
            @foreach ($roles as $role)
              <div class="field">
                <b-checkbox type="is-info" v-model="rolesSelected" :native-value="{{$role->id}}">{{$role->display_name}}</b-checkbox>
              </div>
            @endforeach
        </div>
      </div> <!-- end of .columns for forms -->
      <div class="columns">
        <div class="column">
          <hr />
          <button class="button is-info is-pulled-right" style="width: 250px;">Create New User</button>
        </div>
      </div>
    </form>
  </div> <!-- end of .flex-container -->
@endsection

@section('scripts')
  <script>
    var app = new Vue({
      el: '#app',
      data: {
        auto_password: true,
        rolesSelected: []
      }
    });
  </script>
@endsection
