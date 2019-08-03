@extends('layouts.manage', ['title_text' => '<i class="fa fa-user-plus"></i>', 'item_type' => 'Users',
'customButton' => '<a href="'.route('users.create').'" class="button is-warning is-pulled-right"><i class="fa fa-user-plus m-r-10"></i>Create User</a>'
])
@section('content')
  <div class="card">
    <div class="card-content">
      <table class="table is-narrow">
        <thead>
          <tr>
            <th>id</th>
            <th>Name</th>
            <th>Email</th>
            <th>Date Created</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($users as $user)
          <tr>
            <th>{{$user->id}}</th>
            <td>{{$user->name}}</td>
            <td>{{$user->email}}</td>
            <td>{{$user->created_at->toFormattedDateString()}}</td>
            <td class="has-text-centered tools-bar" style="width: 150px;">
              @permission('read-users')
              <a class="button is-success" href="{{route('users.show', $user->id)}}">
                <i class="fa fa-eye"></i>
              </a>
              @endpermission
              @permission('update-users')
              <a class="button is-info" href="{{route('users.edit', $user->id)}}">
                <i class="fa fa-pencil"></i>
              </a>
              @endpermission
              {{-- <button class="button is-danger">
                    <i class="fa fa-trash"></i>
                  </button> --}}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div> <!-- end of .card -->
  {{$users->links()}}
@endsection