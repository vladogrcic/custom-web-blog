<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\User;
use App\Category;
use App\Tag;
use App;
use Auth;
use App\Profile;
use App\Repositories\BlogRepository as BlogRepo;
use App\Setting;
use Carbon\Carbon;

class FrontEndController extends Controller
{
    private $order = 'desc';
    private $orderBy = 'published_at';
    public function __construct()
    {
    }
    public function blog($lang = false, Request $request)
    {
        $blog_slug = $request->route()->getAction()['blog_slug'];

        $metaBlog = (new BlogRepo)->getSettings();
        $per_page = $metaBlog['per_page'];
        $metaDisLang = $metaBlog['disable_language_group'];
        $metaMainLang = (int)$metaBlog['main_lang'];

        if ($metaDisLang) {
            if (!$lang) {
                $posts = Post::where('status', '=', 1)->whereHas('language', function ($q) use ($metaMainLang) {
                    $q->where('id', '=', $metaMainLang);
                })->orderBy('published_at', 'desc')->paginate($per_page);
            } else {
                abort(404);
            }
        } else {
            if ($lang) {
                $posts = Post::where('status', '=', 1)->orderBy('published_at', 'desc')->whereHas('language', function ($q) use ($lang) {
                    $q->where('slug', '=', $lang);
                })->paginate($per_page);
            }
            else{
                abort(404);
            }
        }
        $other=['main-title-type'=>'Blog'];
        $other += ['comp'=> (object) ['name' => '', 'description' => '']];
        if (count($posts)) {
            $posts = (new BlogRepo)->prepare($posts, false, $blog_slug);
            $tags = Tag::all();
            return view('front-end.blog')->with('articles', $posts)->with('tags', $tags)->with('other', $other)->with('blog_slug', $blog_slug)->with('disLang', $metaDisLang);
        } else {
            return view('errors.noitems')->with('other', $other);
        }
    }
}
