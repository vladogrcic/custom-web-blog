<?php
namespace App\Repositories;

use App\Post;
use App\Group;
use App\User;
use App\Language;
use App\Repositories\MetaRepository as MetaRepo;
use App\Setting;
use App\Profile;
use Request;

/**
 * Repository for meta items like Profile and Settings page.
 *
 * @method void post(object $metaModel, Request $request) Updates or adds meta items to the database.
 *
 */
class BlogRepository
{
    public function __construct()
    {
    }
    
    /**
     * Gets site settings.
     *
     * @return array
     *
     */
    public function getSettings()
    {
        $siteMetaData = [];
        $model = new MetaRepo;
        $model->metaModel = new Setting;
        $siteMetaData['date_format'] = $model->getMeta('date_format');
        if (!$siteMetaData['date_format']) {
            $siteMetaData['date_format'] = 'd.m.Y';
        }
        $siteMetaData['time_format'] = $model->getMeta('time_format');
        if (!$siteMetaData['time_format']) {
            $siteMetaData['time_format'] = 'G:i';
        }
        $siteMetaData['perma_format'] = $model->getMeta('perma_format');
        if (!$siteMetaData['perma_format']) {
            $siteMetaData['perma_format'] = 'slug';
        }
        $siteMetaData['per_page'] = (int)$model->getMeta('per_page');
        if (!$siteMetaData['per_page']) {
            $siteMetaData['per_page'] = 5;
        }
        $siteMetaData['title'] = $model->getMeta('title');
        $siteMetaData['description'] = $model->getMeta('description');
        $siteMetaData['site_address'] = $model->getMeta('site_address');
        $siteMetaData['disable_language_group'] = (int)$model->getMeta('disable_language_group');
        $siteMetaData['blog_slug'] = $model->getMeta('blog_slug');
        if (!$siteMetaData['blog_slug']) {
            $siteMetaData['blog_slug'] = 'blog';
        }
        if (!$model->getMeta('main_lang')) {
            $siteMetaData['main_lang'] = 1;
        } else {
            $siteMetaData['main_lang'] = $model->getMeta('main_lang');
        }

        return $siteMetaData;
    }
    public function getUrl($urlProps=[], $date=false, $category=false)
    {
        $lang = $urlProps['lang'];
        $slug = $urlProps['slug'];
        $metaData = $this->getSettings();
        $perma_format = $metaData['perma_format'];
        $disable_language_group = $metaData['disable_language_group'];
        // $blog_slug = $metaData['blog_slug'];
        $url = url('/').'/';
        $year = date("Y", strtotime($date));
        $month = date("m", strtotime($date));
        $day = date("d", strtotime($date));
        if (!$category) {
            if ($perma_format=='slug') {
                $url = $slug;
            }
            if ($perma_format=='Y/m/slug') {
                $url = $year.'/'.$month.'/'.$slug;
            }
            if ($perma_format=='Y/m/d/slug') {
                $url = $year.'/'.$month.'/'.$day.'/'.$slug;
            }
        } else {
            if ($category === 'day') {
                $url = $year.'/'.$month.'/'.$day;
            }
            if ($category === 'month') {
                $url = $year.'/'.$month;
            }
            if ($category === 'year') {
                $url = $year;
            }
            if ($category === 'author') {
                $url = 'author/'.$slug;
            }
            if ($category === 'category') {
                $url = 'category/'.$slug;
            }
            if ($category === 'tag') {
                $url = 'tag/'.$slug;
            }
        }
       
        return $url;
    }
    /**
     * Changes the data which is received from the database to suit the page for which it is provided.
     *
     * @param Post $posts Instance of the Post model.
     * @return Post $posts
     *
     */
    public function prepare($posts, $singular=false)
    {
        $metaData = $this->getSettings();
        $date_format = $metaData['date_format'];
        $time_format = $metaData['time_format'];
        $perma_format = $metaData['perma_format'];
        $disable_language_group = $metaData['disable_language_group'];
        $known = [
            'm',
            'n',
            'F',
            'Y',
            'd',
            'j',
        ];
        
        $letters = str_split($date_format);
        for ($i=0; $i < count($letters); $i++) {
            for ($j=0; $j < count($known); $j++) {
                $wordToFind = $known[$j];
                $wrap_before = rtrim(chunk_split('<a href="[%'.$wordToFind.'%]">', 1, '\\'), '\\');
                $wrap_after  = '<\/\a\>';
                $matches = ["/($wordToFind)/i"];
                $replace = ["$wrap_before$1$wrap_after"];
                $letters[$i] = preg_replace($matches, $replace, $letters[$i]);
                if (strpos($letters[$i], $wordToFind) !== false) {
                    continue 2;
                }
            }
        }
        $date_format = implode("", $letters);
        if ($posts != null) {
            if ($singular) {
                $user_id = $posts->author_id;
                $email = $posts->user->email;
                $avatar = (new User)->getAvatar($email, $user_id);
                $posts->authorAvatar = $avatar;
                $posts = $this->assemble($posts, [
                    'date_format'=>$date_format,
                    'time_format'=>$time_format,
                    'perma_format'=>$perma_format,
                    'disable_language_group'=>$disable_language_group
                ]);
            } else {
                foreach ($posts as $post) {
                    $user_id = $post->author_id;
                    $email = $post->user->email;
                    $avatar = (new User)->getAvatar($email, $user_id);
                    $post->authorAvatar = $avatar;
                    $post = $this->assemble($post, [
                        'date_format'=>$date_format,
                        'time_format'=>$time_format,
                        'perma_format'=>$perma_format,
                        'disable_language_group'=>$disable_language_group
                    ]);
                }
            }
        }
        return $posts;
    }
    public function assemble($post, $formats)
    {
        $date = $post->published_at;
        $post->url = $this->getUrl([
            'lang'=>$post->language->slug,
            'slug'=>$post->slug
        ], $date, false);
        // $date = date("Y", strtotime($date));
        $post->authorUrl = $this->getUrl([
            'lang'=>$post->language->slug,
            'slug'=>$post->user->name
        ], false, 'author');
        $catUrl = [];
        $tagUrl = [];
        for ($i=0; $i < count($post->categories); $i++) {
            $catUrl[] = $this->getUrl([
                'lang'=>$post->language->slug,
                'slug'=>$post->categories[$i]->slug
            ], false, 'category');
        }
        for ($i=0; $i < count($post->tags); $i++) {
            $tagUrl[] = $this->getUrl([
                'lang'=>$post->language->slug,
                'slug'=>$post->tags[$i]->slug
            ], false, 'tag');
        }
        $post->catUrl = $catUrl;
        $post->tagUrl = $tagUrl;
        $urlYear = $this->getUrl([
            'lang'=>$post->language->slug,
            'slug'=>false
        ], $date, 'year');
        $urlMonth = $this->getUrl([
            'lang'=>$post->language->slug,
            'slug'=>false
        ], $date, 'month');
        $urlDay = $this->getUrl([
            'lang'=>$post->language->slug,
            'slug'=>false
        ], $date, 'day');
        $date_format = $formats['date_format'];
        $wrap_beforeYear = rtrim(chunk_split($urlYear, 1, '\\'), '\\');
        $date_format = str_replace("[\%\Y\%\]", $wrap_beforeYear, $date_format);
        
        $wrap_beforeMonth = rtrim(chunk_split($urlMonth, 1, '\\'), '\\');
        $date_format = str_replace("[\%\m\%\]", $wrap_beforeMonth, $date_format);

        $date_format = str_replace("[\%\F\%\]", $wrap_beforeMonth, $date_format);
        
        $date_format = str_replace("[\%\\n\%\]", $wrap_beforeMonth, $date_format);

        $wrap_beforeDay = rtrim(chunk_split($urlDay, 1, '\\'), '\\');
        $date_format = str_replace("[\%\d\%\]", $wrap_beforeDay, $date_format);
        
        $date_format = str_replace("[\%\j\%\]", $wrap_beforeDay, $date_format);

        $time_format = $formats['time_format'];
        $perma_format = $formats['perma_format'];
        $disable_language_group = $formats['disable_language_group'];
        $date = $post->published_at;
        if ($post->categories != null) {
            $post->categories;
        }
        $meta_key = Profile::where('user_id', '=', $post->user->id)->where('meta_key', '=', 'avatar')->first();
        if ($meta_key) {
            $post->avatar = $meta_key->meta_value;
        }
        $meta_key = Profile::where('user_id', '=', $post->user->id)->where('meta_key', '=', 'gravatar')->first();
        if ($meta_key) {
            $post->gravatar = $meta_key->meta_value;
        }
        $email = $post->user->email;
        $post->gravatarUrl = (new User)->getGravatar($email);
        $post->checkGravatar = (new User)->checkGravatar($post->user->email);
        $date = $post->published_at;
        // $post->published_at = (object)[
        //     "date" => date($date_format, strtotime($post->published_at)),
        //     "time" => date($time_format, strtotime($post->published_at)),
        // ];
        $post->published_at = (object)[
            "date" => date($date_format, strtotime($post->published_at)),
            "dateDay" => date('d', strtotime($post->published_at)),
            "dateMonth" => date('m', strtotime($post->published_at)),
            "dateYear" => date('Y', strtotime($post->published_at)),
            "time" => date($time_format, strtotime($post->published_at)),
        ];
        return $post;
    }
}
