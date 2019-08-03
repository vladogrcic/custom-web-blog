@extends('layouts.user')

@section('content')

<div class="columns">
  <div class="column is-half is-offset-one-quarter m-t-100">
    <div class="card">
      <div class="card-content">
        <h1 class="title">Log In</h1>

        <form action="{{route('login')}}" method="POST" role="form">
          {{csrf_field()}}
          <div class="field">
            <label for="email" class="label">Email or Username</label>
            <p class="control">
              <input id="login" type="text" class="input"
                    class="form-control{{ $errors->has('name') || $errors->has('email') ? ' is-invalid' : '' }}"
                    name="login" value="{{ old('name') ?: old('email') }}" required autofocus>
      
              
            </p>
            @if ($errors->has('name') || $errors->has('email'))
                  <span class="invalid-feedback help is-danger is-size-6 has-text-weight-bold">
                      {{ $errors->first('name') ?: $errors->first('email') }}
                  </span>
              @endif
            {{-- @if ($errors->has('email'))
              <p class="help is-danger">{{$errors->first('email')}}</p>
            @endif --}}
          </div>
          <div class="field">
            <label for="password" class="label">Password</label>
            <p class="control">
              <input class="input {{$errors->has('password') ? 'is-danger' : ''}}" type="password" name="password" id="password">
            </p>
            @if ($errors->has('password'))
              <p class="help is-danger">{{$errors->first('password')}}</p>
            @endif

          </div>

          {{-- <b-checkbox name="remember" class="m-t-20">Remember Me</b-checkbox> --}}
          <input id="remember" type="checkbox" name="remember" class="m-t-20"/>
          <label for="remember">Remember Me</label>

          <button class="button is-success is-outlined is-fullwidth m-t-30">Log In</button>
        </form>
      </div> <!-- end of .card-content -->
    </div> <!-- end of .card -->
    <h5 class="has-text-centered m-t-20"><a href="{{route('password.request')}}" class="is-muted">Forgot Your Password?</a></h5>
  </div>
</div>

@endsection

