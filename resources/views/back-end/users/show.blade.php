@extends('layouts.manage', ['title_text' => '<i class="fa fa-user-plus m-r-10"></i>', 'item_type' => 'Users',
'customButton' => '<a href="'.route('users.edit', $user->id).'" class="button is-info is-pulled-right"><i class="fa fa-pencil m-r-10"></i>Edit User</a>'
])
@section('content')
    <div class="card p-b-25 m-b-25">
      <div class="card-content">
        <div class="columns">
          <div class="column">
            <div class="field">
              <label for="name" class="label">Name</label>
              <pre>{{$user->display_name}}</pre>
            </div>
            <div class="field">
              <label for="name" class="label">Username</label>
              <pre>{{$user->name}}</pre>
            </div>
            <div class="field">
              <div class="field">
                <label for="email" class="label">Email</label>
                <pre>{{$user->email}}</pre>
              </div>
            </div>
            @if(session('is_rand_pass_passed'))
            <div class="field">
              <div class="field">
                <label for="email" class="label">Random Password</label>
                <pre>{{session('pass')}}</pre>
              </div>
              <div class="field">
                <span class="has-text-danger has-text-weight-semibold">Remember this password. It will be shown only this time.</span>
              </div>
            </div>
            @endif
            <div class="field">
              <div class="field">
                <label for="email" class="label">Roles</label>
                <ul>
                  @forelse ($user->roles as $role)
                    <li>{{$role->display_name}} ({{$role->description}})</li>
                  @empty
                    <p>This user has not been assigned any roles yet</p>
                  @endforelse
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection
