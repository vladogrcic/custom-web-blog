<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;
use App\User;
use App\Tag;
use App\Profile;
use App\Language;
use App\Repositories\BlogRepository as BlogRepo;
use Redirect;

class BlogController extends Controller
{
    private $order = 'desc';
    private $orderBy = 'published_at';
    public function getSingle($item=false, $item2=false, $item3=false, $item4=false, $item5=false, Request $request)
    {
        $routeName = \Request::route()->getName();
        $metaBlog = (new BlogRepo)->getSettings();
        $metaDisLang = $metaBlog['disable_language_group'];
        $perma_format = $metaBlog['perma_format'];
        $blog_slug = $request->route()->getAction()['blog_slug'];

        if ($metaBlog['disable_language_group']) {
            $lang = false;
            
            if($item&&$item2&&$item3&&$item4){
                $year = $item;
                $month = $item2;
                $day = $item3;
                $slug = $item4;
            }
            elseif($item&&$item2&&$item3){
                $year = $item;
                $month = $item2;
                $slug = $item3;
            }
            else{
                $slug = $item;
            }
            if (!$lang) {
                if ($routeName === "blog.single") {
                    $post = Post::where('status', '=', 1)->where('slug', '=', $slug)->orderBy($this->orderBy, $this->order)->first();
                } elseif ($routeName === "blog.singleYearMonth") {
                    $post = Post::where('status', '=', 1)->where('slug', '=', $slug)->whereYear('published_at', $year)->whereMonth('published_at', $month)->orderBy($this->orderBy, $this->order)->first();
                } elseif ($routeName === "blog.singleYearMonthDay") {
                    $post = Post::where('status', '=', 1)->where('slug', '=', $slug)->whereYear('published_at', $year)->whereMonth('published_at', $month)->whereDay('published_at', $day)->orderBy($this->orderBy, $this->order)->first();
                } else {
                    abort(404);
                }
            } else {
                abort(404);
            }
        } else {
            $lang = $item;
            if($item2&&$item3&&$item4&&$item5){
                $year = $item2;
                $month = $item3;
                $day = $item4;
                $slug = $item5;
            }
            elseif($item2&&$item3&&$item4){
                $year = $item2;
                $month = $item3;
                $slug = $item4;
            }
            else{
                $slug = $item2;
            }
            
            $langSlug = Language::where('slug', '=', $lang)->first();
            if(!$langSlug) abort(404);
            if ($lang) {
                if ($routeName === "blog.single") {
                    if($item&&$item2) $post = Post::where('status', '=', 1)->where('language_id', '=', $langSlug->id)->where('slug', '=', $slug)->orderBy($this->orderBy, $this->order)->first();
                    else abort(404);
                } elseif ($routeName === "blog.singleYearMonth") {
                    if($item&&$item2&&$item3&&$item4) $post = Post::where('status', '=', 1)->where('language_id', '=', $langSlug->id)->where('slug', '=', $slug)->whereYear('published_at', $year)->whereMonth('published_at', $month)->orderBy($this->orderBy, $this->order)->first();
                    else abort(404);
                }
                elseif ($routeName === "blog.singleYearMonthDay") {
                    if($item&&$item2&&$item3&&$item4&&$item5) $post = Post::where('status', '=', 1)->where('language_id', '=', $langSlug->id)->where('slug', '=', $slug)->whereYear('published_at', $year)->whereMonth('published_at', $month)->whereDay('published_at', $day)->orderBy($this->orderBy, $this->order)->first();
                    else abort(404);
                } else {
                    abort(404);
                }
            } else {
                abort(404);
            }
        }
        if ($post != null) {
            $date = $post->published_at;
            $post = (new BlogRepo)->prepare($post, true);
            $url = (new BlogRepo)->getUrl([
                'lang'=>$lang,
                'slug'=>$slug
            ], $date);
            if (!($routeName === "blog.single")) {
                if ($metaBlog['perma_format'] === "slug") {
                    return redirect()->away($url);
                }
            }
            if (!($routeName === "blog.singleYearMonth")) {
                if ($metaBlog['perma_format'] === "Y/m/slug") {
                    return redirect()->away($url);
                }
            }
            if (!($routeName === "blog.singleYearMonthDay")) {
                if ($metaBlog['perma_format'] === "Y/m/d/slug") {
                    return redirect()->away($url);
                }
            }
        } else {
            abort(404);
        }
        $siteMeta =
        [
            'site-title'=>$post->title,
            'site-description'=>$post->description,
            'site-image'=>$post->featured_image
        ];
        return view('front-end.single')->with('article', $post)->with('siteMeta', $siteMeta)->with('blog_slug', $blog_slug)->with('disLang', $metaDisLang);
    }
    public function getBlogDate($item=false, $item2=false, $item3=false, $item4=false, Request $request)
    {
        // $slug = 2018;
        $metaBlog = (new BlogRepo)->getSettings();
        $per_page = $metaBlog['per_page'];
        $metaDisLang = $metaBlog['disable_language_group'];
        $metaMainLang = (int)$metaBlog['main_lang'];
        $blog_slug = $request->route()->getAction()['blog_slug'];
        if ($metaDisLang) {
            $lang = false;
            $year = $item;
            $month = $item2;
            $day = $item3;
            if (!$lang) {
                if ($month&&!$day) {
                    $posts = Post::where('status', '=', 1)->whereHas('language', function ($q) use ($metaMainLang) {
                        $q->where('id', '=', $metaMainLang);
                    })->whereYear('published_at', $year)->whereMonth('published_at', $month)->orderBy('id', 'desc')->paginate($per_page);
                } 
                elseif($day){
                    $posts = Post::where('status', '=', 1)->whereHas('language', function ($q) use ($metaMainLang) {
                        $q->where('id', '=', $metaMainLang);
                    })->whereYear('published_at', $year)->whereMonth('published_at', $month)->whereDay('published_at', $day)->orderBy('id', 'desc')->paginate($per_page);
                }
                else {
                    $posts = Post::where('status', '=', 1)->whereHas('language', function ($q) use ($metaMainLang) {
                        $q->where('id', '=', $metaMainLang);
                    })->whereYear('published_at', $year)->orderBy('id', 'desc')->paginate($per_page);
                }
            } else {
                abort(404);
            }
        } else {
            $lang = $item;
            $year = $item2;
            $month = $item3;
            $day = $item4;
            if ($lang) {
                if ($month&&!$day) {
                    $posts = Post::whereHas('language', function ($q) use ($lang) {
                        $q->where('slug', '=', $lang);
                    })->whereYear('published_at', $year)->whereMonth('published_at', $month)->where('status', '=', 1)->orderBy('id', 'desc')->paginate($per_page);
                } 
                elseif($day) {
                    $posts = Post::whereHas('language', function ($q) use ($lang) {
                        $q->where('slug', '=', $lang);
                    })->whereYear('published_at', $year)->whereMonth('published_at', $month)->whereDay('published_at', $day)->where('status', '=', 1)->orderBy('id', 'desc')->paginate($per_page);
                }
                else {
                    $posts = Post::whereHas('language', function ($q) use ($lang) {
                        $q->where('slug', '=', $lang);
                    })->whereYear('published_at', $year)->where('status', '=', 1)->orderBy('id', 'desc')->paginate($per_page);
                }
            } else {
                abort(404);
            }
        }
        $posts = (new BlogRepo)->prepare($posts);
        $tags = Tag::all();
        if($month&&!$day){
            $other=[
                'main-title-type'=>'Month',
                'secondary-title-type'=>'Year'
            ];
            $cat_info_year = (object)[
                'display_name'=>$year,
                'description'=>''
            ];
            $cat_info_month = (object)[
                'display_name'=>$month,
                'description'=>''
            ];
            $other += ['comp'=> [$cat_info_year, $cat_info_month]];
        }
        elseif($day){
            $other=[
                'main-title-type'=>'Day',
                'secondary-title-type'=>'Month',
                'tertiary-title-type'=>'Year'
            ];
            $cat_info_year = (object)[
                'display_name'=>$year,
                'description'=>''
            ];
            $cat_info_month = (object)[
                'display_name'=>$month,
                'description'=>''
            ];
            $cat_info_day = (object)[
                'display_name'=>$day,
                'description'=>''
            ];
            $other += ['comp'=> [$cat_info_year, $cat_info_month, $cat_info_day]];
        }
        else{
            $other=['main-title-type'=>'Year'];
            $cat_info = (object)[
                'display_name'=>$year,
                'description'=>''
            ];
            $other += ['comp'=> $cat_info];
        }
        if (count($posts)) {
            return view('front-end.blog')->with('articles', $posts)->with('tags', $tags)->with('other', $other)->with('blog_slug', $blog_slug)->with('disLang', $metaDisLang);
        } else {
            return view('errors.noitems')->with('other', $other);
        }
    }
    public function getBlogCategory($item=false, $item2=false, Request $request)
    {
        $metaBlog = (new BlogRepo)->getSettings();
        $per_page = $metaBlog['per_page'];
        $metaDisLang = $metaBlog['disable_language_group'];
        $metaMainLang = (int)$metaBlog['main_lang'];
        $blog_slug = $request->route()->getAction()['blog_slug'];
        if ($metaDisLang) {
            $lang = false; 
            $slug = $item;
            if (!$lang) {
                $posts = Post::whereHas('categories', function ($q) use ($slug) {
                    $q->where('slug', '=', $slug);
                })->whereHas('language', function ($q) use ($metaMainLang) {
                    $q->where('id', '=', $metaMainLang);
                })->where('status', '=', 1)->orderBy('id', 'desc')->paginate($per_page);
            } else {
                abort(404);
            }
        } else {
            $lang = $item; 
            $slug = $item2; 
            if ($lang) {
                $posts = Post::whereHas('categories', function ($q) use ($slug) {
                    $q->where('slug', '=', $slug);
                })->whereHas('language', function ($q) use ($lang) {
                    $q->where('slug', '=', $lang);
                })->where('status', '=', 1)->orderBy('id', 'desc')->paginate($per_page);
            } else {
                abort(404);
            }
        }
        $posts = (new BlogRepo)->prepare($posts);
        $tags = Tag::all();
        $other=['main-title-type'=>'Category'];
        $cat_info = Category::where('slug', $slug)->get();
        $other += ['comp'=> $cat_info[0]];
        if (count($posts)) {
            return view('front-end.blog')->with('articles', $posts)->with('tags', $tags)->with('other', $other)->with('blog_slug', $blog_slug)->with('disLang', $metaDisLang);
        } else {
            return view('errors.noitems')->with('other', $other);
        }
    }
    public function getBlogTag($item=false, $item2=false, Request $request)
    {
        $metaBlog = (new BlogRepo)->getSettings();
        $per_page = $metaBlog['per_page'];
        $metaDisLang = $metaBlog['disable_language_group'];
        $metaMainLang = (int)$metaBlog['main_lang'];
        $blog_slug = $request->route()->getAction()['blog_slug'];
        if ($metaDisLang) {
            $lang = false; 
            $slug = $item;
            if (!$lang) {
                $posts = Post::whereHas('tags', function ($q) use ($slug) {
                    $q->where('slug', '=', $slug);
                })->whereHas('language', function ($q) use ($metaMainLang) {
                    $q->where('id', '=', $metaMainLang);
                })->where('status', '=', 1)->orderBy('id', 'desc')->paginate($per_page);
            } else {
                abort(404);
            }
        } else {
            $lang = $item; 
            $slug = $item2; 
            if ($lang) {
                $posts = Post::whereHas('tags', function ($q) use ($slug) {
                    $q->where('slug', '=', $slug);
                })->whereHas('language', function ($q) use ($lang) {
                    $q->where('slug', '=', $lang);
                })->where('status', '=', 1)->orderBy('id', 'desc')->paginate($per_page);
            } else {
                abort(404);
            }
        }
        $posts = (new BlogRepo)->prepare($posts);
        $tags = Tag::all();
        $tag_info = Tag::where('slug', $slug)->get();
        $other = ['main-title-type'=>'Tag'];
        $other += ['comp'=> $tag_info[0]];
        if (count($posts)) {
            return view('front-end.blog')->with('articles', $posts)->with('tags', $tags)->with('other', $other)->with('blog_slug', $blog_slug)->with('disLang', $metaDisLang);
        } else {
            return view('errors.noitems')->with('other', $other);
        }
    }
    public function getBlogAuthor($item=false, $item2=false, Request $request)
    {
        $metaBlog = (new BlogRepo)->getSettings();
        $per_page = $metaBlog['per_page'];
        $metaDisLang = $metaBlog['disable_language_group'];
        $metaMainLang = (int)$metaBlog['main_lang'];
        $blog_slug = $request->route()->getAction()['blog_slug'];
        if ($metaDisLang) {
            $lang = false; 
            $slug = $item;
            if (!$lang) {
                $posts = Post::whereHas('user', function ($q) use ($slug) {
                    $q->where('name', '=', $slug);
                })->whereHas('language', function ($q) use ($metaMainLang) {
                    $q->where('id', '=', $metaMainLang);
                })->where('status', '=', 1)->orderBy('id', 'desc')->paginate($per_page);
            } else {
                abort(404);
            }
        } else {
            $lang = $item; 
            $slug = $item2; 
            if ($lang) {
                $posts = Post::whereHas('user', function ($q) use ($slug) {
                    $q->where('name', '=', $slug);
                })->whereHas('language', function ($q) use ($lang) {
                    $q->where('slug', '=', $lang);
                })->where('status', '=', 1)->orderBy('id', 'desc')->paginate($per_page);
            } else {
                abort(404);
            }
        }
        $posts = (new BlogRepo)->prepare($posts);
        $tags = Tag::all();
        $authors = User::all();
        $author_info = User::where('name', $slug)->get();
        $other = ['main-title-type'=>'Author'];
        $other += ['comp'=> $author_info[0]];
        if (count($posts)) {
            return view('front-end.blog')->with('articles', $posts)->with('tags', $tags)->with('other', $other)->with('blog_slug', $blog_slug)->with('disLang', $metaDisLang);
        } else {
            return view('errors.noitems')->with('other', $other);
        }
    }
}
