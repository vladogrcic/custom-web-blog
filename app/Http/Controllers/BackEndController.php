<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;
use App\Tag;
use App\Language;
use App\User;
class BackEndController extends Controller
{
    public function index()
    {
      return redirect()->route('manage.dashboard');
    }

    public function dashboard()
    {
      $number = [
        'posts' => Post::count(),
        'cats' => Category::count(),
        'tags' => Tag::count(),
        'langs' => Language::count(),
        'users' => User::count()
      ];
      return view('back-end.dashboard')->with('num', $number);
    }

    public function profile()
    {
      return view('back-end.profile');
    }

    public function settings()
    {
      return view('back-end.settings');
    }
}
