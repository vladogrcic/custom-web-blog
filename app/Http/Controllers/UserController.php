<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use DB;
use Session;
use Hash;
use Input;
use Auth;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $access = Auth::user()->isAbleTo('read-users');
        if (!$access) {
            abort(403);
        }
        $users = User::orderBy('id', 'desc')->paginate(10);
        return view('back-end.users.index')->withUsers($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $access = Auth::user()->isAbleTo('create-users');
        if (!$access) {
            abort(403);
        }

        $roles = Role::all();
        $maxId = User::max('id');

        $user = (object)[
          'id'=>$maxId+1,
          'display_name'=> '',
          'name'=> '',
          'email'=> '',
          'roles'=> collect(['id'=>'']),
        ];
        $page = 'Create User';
        $title_text = '<i class="fa fa-user-plus m-r-10"></i>';
        return view("back-end.users.create-edit")
        ->withUser($user)
        ->withRoles($roles)
        ->with('page', $page)
        ->with('title_text', $title_text);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $access = Auth::user()->isAbleTo('create-users');
        if (!$access) {
            abort(403);
        }
        $maxId = User::max('id');
        // if (!empty($request->password)) {
        //   $password = trim($request->password);
        // } else {
        //   # set the manual password
        //   $length = 10;
        //   $keyspace = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        //   $password = '';
        //   $max = mb_strlen($keyspace, '8bit') - 1;
        //   for ($i = 0; $i < $length; ++$i) {
        //       $password .= $keyspace[random_int(0, $max)];
        //   }
        //   $password = $password;
        // }
        $user = new User();
        $user->id = $maxId+1;
        $user->display_name = $request->display_name;
        $user->name = $request->name;
        $user->email = $request->email;
        $password = '';
        $is_rand_pass_passed = false;
        if ($request->password_options == 'auto') {
            $length = 10;
            $keyspace = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
            $password = '';
            $max = mb_strlen($keyspace, '8bit') - 1;
            for ($i = 0; $i < $length; ++$i) {
                $password .= $keyspace[random_int(0, $max)];
            }
            $user->password = Hash::make($password);
            $is_rand_pass_passed = true;
        } elseif ($request->password_options == 'manual') {
            $user->password = Hash::make($request->password);
        }
        // $user->password = Hash::make($password);
        $user->save();
        if ($request->roles) {
            $user->syncRoles($request->roles);
        }
        // return redirect()->route('users.show', $user->id)->with('password', $password);
        return redirect()->route('users.show', $user->id)->with('pass', $password)->with('is_rand_pass_passed', $is_rand_pass_passed);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $access = Auth::user()->isAbleTo('read-users');
        if (!$access) {
            abort(403);
        }
        $id = (int)$id;
        $user = User::where('id', $id)->with('roles')->first();
        return view("back-end.users.show")->withUser($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $access = Auth::user()->isAbleTo('update-users');
        if (!$access) {
            abort(403);
        }
        $id = (int)$id;
        $roles = Role::all();
        $user = User::where('id', $id)->with('roles')->first();
        $page = 'Editing User';
        $title_text = '<i class="fa fa-user-plus m-r-10"></i>';
        return view("back-end.users.create-edit")
        ->withUser($user)
        ->withRoles($roles)
        ->with('page', $page)
        ->with('title_text', $title_text);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $access = Auth::user()->isAbleTo('update-users');
        if (!$access) {
            abort(403);
        }
        $id = (int)$id;
        $user = User::findOrFail($id);
        $user->display_name = $request->display_name;
        $user->email = $request->email;
        $user->name = $request->name;
        $password = '';
        $is_rand_pass_passed = false;
        if ($request->password_options == 'auto') {
            $length = 10;
            $keyspace = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
            $password = '';
            $max = mb_strlen($keyspace, '8bit') - 1;
            for ($i = 0; $i < $length; ++$i) {
                $password .= $keyspace[random_int(0, $max)];
            }
            $user->password = Hash::make($password);
            $is_rand_pass_passed = true;
        } elseif ($request->password_options == 'manual') {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        $user->syncRoles($request->roles);
        return redirect()->route('users.show', $id)->with('pass', $password)->with('is_rand_pass_passed', $is_rand_pass_passed);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $access = Auth::user()->isAbleTo('delete-users');
        if (!$access) {
            abort(403);
        }
    }
}
