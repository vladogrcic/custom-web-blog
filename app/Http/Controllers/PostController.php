<?php

namespace App\Http\Controllers;

//use App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use Storage;
use Auth;
use Purifier;

use App\Post;
use App\User;
use App\Category;
use App\Language;
use App\Tag;
use App\Profile;

use App\Builders\ItemBuilder\ItemBuilder as Item;
use App\Repositories\PostRepository as PostRepo;
use App\Repositories\BlogRepository as BlogRepo;

use App\Http\Requests\PostRequests\PostIndexRequest as IndexRequest;
use App\Http\Requests\PostRequests\PostUpdateRequest as UpdateRequest;
use App\Http\Requests\PostRequests\PostDeleteRequest as DeleteRequest;
use App\Http\Requests\PostRequests\PostCheckUniqRequest as CheckUniqRequest;
use App\Http\Requests\PostRequests\PostLockerRequest as CheckLockerRequest;

class PostController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(IndexRequest $request)
    {
        $ajax = $request->ajax();
        $access = Auth::user()->isAbleTo('read-posts');
        if (!$access) {
            abort(403);
        }
        $request = (object)$request->validated();
        if (!property_exists($request, 'orderBy')) {
            $orderBy = '';
        } else {
            $orderBy = $request->orderBy;
        }
        if (!property_exists($request, 'orderDir')) {
            $orderDir = '';
        } else {
            $orderDir = $request->orderDir;
        }
        if(property_exists($request, 'foreignType')){
            $foreignType = $request->foreignType;
        }
        else{
            $foreignType = '';
        }
        if(property_exists($request, 'table')){
            $table = $request->table;
        }
        else{
            $table = '';
        }
        $userID = Auth::id();
        $users = User::all();
        $postsJSON = [];
        
        
        $getItems = Item::class(new Post)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->foreignType($foreignType)
            ->table($table)
            ->permission('all-posts')
            ->get();
        $posts = $getItems['itemAll']['items'];

        foreach ($posts as $index=>$post) {
            $postsJSON[] = $post;
            $post->user->name;
            if ($post->categories != null) {
                $post->categories;
            } else {
                $post->categories = [];
            }
            if ($post->language != null) {
                $post->language->name;
            }
        }
        for ($i=0; $i < count($postsJSON); $i++) { 
            if (strtotime($postsJSON[$i]['published_at']) == false) {
                $postsJSON[$i]['published_at'] = false;
            }
        }
        
        if ($ajax) {
            return response()->json(['postsJSON' => $postsJSON]);
        } else {
            $postsJSON = json_encode($postsJSON);
            return view('back-end.posts.index')
                ->withPosts($posts)
                ->with('title_text', '<i class="fa fa-font"></i>')
                ->with('postsJSON', $postsJSON)
                ->with('posts_url', '');
        }
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        $access = Auth::user()->isAbleTo('create-posts');
        if (!$access) {
            abort(403);
        }
        $currentUser = Auth::user();//Auth::user();
        $user_id = $currentUser['id'];

        $metaBlog = (new BlogRepo)->getSettings();
        $metaDisLang = $metaBlog['disable_language_group'];
        $metaMainLang = (int)$metaBlog['main_lang'];
        $meta_blog_slug = $metaBlog['blog_slug'];

        $currentUser['avatar'] = (new User)->getAvatar($currentUser->email, $user_id);
        // if($metaDisLang){
        //     $categories= Category::where('language_id', $metaMainLang)->get();
        // }
        // else{
            $categories= Category::All();
        // }
        
        $languages= Language::All();
        $users= User::whereHas('roles', function($query)
        {
            $query->where('name', 'superadmin')
                ->orWhere('name', 'testadmin')
                ->orWhere('name', 'admin')
                ->orWhere('name', 'editor')
                ->orWhere('name', 'author')
                ->orWhere('name', 'contributor');
        })->get();
        // if($metaDisLang){
        //     $tags= Tag::where('language_id', $metaMainLang)->get();
        // }
        // else{
            $tags= Tag::All();
        // }
        $maxId = Post::max('id');
        $defaultLangIndex = 0;
        for ($i=0; $i < count($languages); $i++) { 
            if ($languages[$i]->id == $metaMainLang) {
                $defaultLangIndex = $i;
            }
        }
        $post = (object)[
            'id' => $maxId+1,
            'title' => '',
            'slug' => '',
            'status' => 0,
            'featured_image' => '',
            'excerpt' => '',
            'content' => json_encode('<p></p>'),
            'language_id' => $languages[$defaultLangIndex]->id,
            'language' => (object)['name' => $languages[$defaultLangIndex]->name],
            'tags' => [
                ],
            'categories' => $categories,
            'published_at' => false,
            'language' => (object)[
                'name' => $languages[$defaultLangIndex]->name,
                'slug' => $languages[$defaultLangIndex]->slug,
            ]
        ];
        $f_image_props = [
            'thumbUrl' => '',
            'name' => '',
            'mimeType' => '',
            'size' => '',
            'exif' => [],
            'resolution' => [
                'height' => '',
                'width' => '',
            ],
            'modified' => [
                'date' => '',
                'time' => '',
            ],
        ];
        $tagList = [];
        $tagListOBJ = [];
        $postTagList = [];
        $postTagListOBJ = [];
        $categoryList = [];
        $langUrl = [];
        
        $tagList = [];
        $tagListInit = [];
        $tagListOBJ = [];
        $tagListLang = [];
        $tagListLangID = [];
        $tagListLangIDInit = [];
        foreach ($tags as $tag) {
            $tagListInit[] = $tag->name;
            $tagListLangIDInit[] = $tag->language->id;

            if($languages[$defaultLangIndex]->id == $tag->language->id){
                $tagList[] = $tag->name;
                $tagListOBJ[$tag->name] = $tag->id;
                if ($tag->language) {
                    $tagListLang[] = $tag->language->name;
                    $tagListLangID[] = $tag->language->id;
                }
            }
        }
        $postTagList = [];
        $postTagListOBJ = [];
        $postTagListID = [];
        if (count($post->tags)) {
            foreach ($post->tags as $tag) {
                $postTagList[] = $tag->name;
                $postTagListOBJ[$tag->name] = $tag->id;
                $postTagListID[] = $tag->id;
            }
        }

        for ($i=0; $i < count($languages); $i++) {
            $langUrl[$languages[$i]->id] = $languages[$i]->slug;
        }
        
        $url = '';
        if ($metaDisLang) {
            $url = '/'.$meta_blog_slug;
            // $languages = false;
        } else {
            $url = $url.'/'.$meta_blog_slug;
        }
        $page = 'Create Post';
        $title_text = '<i class="fa fa-file-o m-r-10"></i>';
        $loggedUser = auth()->user()->id;
        $locker = false;
        return view('back-end.posts.create-edit')
            ->withcurrentUser($currentUser)
            ->withCategories($categories)
            ->withLanguages($languages)
            ->with('users', $users)
            ->with('url', $url)
            ->with('locker', $locker)
            ->with('langUrl', $langUrl)
            ->with('categoryList', $categoryList)
            ->with('postTagList', $postTagList)
            ->with('tagListOBJ', $tagListOBJ)
            ->with('tagList', $tagList)
            ->with('tagListInit', $tagListInit)
            // ->with('tagListLangID', $tagListLangID)
            ->with('tagListLangIDInit', $tagListLangIDInit)
            ->withPost($post)
            ->with('language_id', $metaMainLang)
            ->with('page', $page)
            ->with('featured_image_props', $f_image_props)
            ->with('title_text', $title_text)
            ->with('loggedUser', $loggedUser)
            ->with('timePassed', (int)(new PostRepo)->timepassed);
        }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(UpdateRequest $request)
    {
        $access = Auth::user()->isAbleTo('create-posts');
        if (!$access) {
            abort(403);
        }
        $ajax = $request->ajax();

        $request = (object)$request->validated();
        $user = new User();
        $post = new Post();
        $metaBlog = (new BlogRepo)->getSettings();
        $metaDisLang = $metaBlog['disable_language_group'];
        $meta_blog_slug = $metaBlog['blog_slug'];
        (new PostRepo)->post($post, $request);
        $output = (new PostRepo)->edit($post);
            // $output->post->content = json_encode($post->content);
            $users= User::All();
            if ($metaDisLang) {
                $output->url = '/'.$meta_blog_slug;
                $output->languages = false;
                $output->language_id = false;
            } else {
                $output->url = $output->url.'/'.$meta_blog_slug;
            }
            $page = 'Editing Post';
            $output->post->content = ($output->post->content);
            if (strtotime($output->post->published_at) == false) {
                $output->post->published_at = false;
            }
            $send = [
                'currentUser' => $output->currentUser,
                'categories' => $output->categories,
                'languages' => $output->languages,
                'users' => $users,
                'url' => $output->url,
                'langUrl' => $output->langUrl,
                'categoryList' => $output->categoryList,
                'postTagList' => $output->postTagList,
                'tagListOBJ' => $output->tagListOBJ,
                'postTagListID' => $output->postTagListID,
                // 'tagListLang' => $output->tagListLang,
                'tagListLangIDInit' => $output->tagListLangIDInit,
                'tagList' => $output->tagList,
                'tagListInit' => $output->tagList,
                'language_id' => (int)$output->post->language_id,
                'page' => $page,
                'post' => $output->post,
                'featured_image_props' => $output->f_image_props,
                'loggedUser' => $output->loggedUser
            ];
        if (!$post) {
            return response()->json([], 404);
        }
        if ($ajax) {
            return response()->json($send);
        } else {
            return redirect()->route('posts.index');
        }
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        $access = Auth::user()->isAbleTo('read-posts');
        if (!$access) {
            abort(403);
        }
        $id = (int)$id;
        if (!(Auth::user()->isAbleTo('all-posts'))) {
            $post = Post::where('id', $id)->where('author_id', auth()->user()->id)->first();
        } else {
            $post = Post::where('id', $id)->first();
        }
        if (!$post) {
            abort(404);
        }
        $output = (new PostRepo)->edit($post);
        return view('back-end.posts.show')
            ->withcurrentUser($output->currentUser)
            ->withCategories($output->categories)
            ->withLanguages($output->languages)
            ->with('langUrl', $output->langUrl)
            ->with('categoryList', $output->categoryList)
            ->with('postTagList', $output->postTagList)
            ->with('tagListOBJ', $output->tagListOBJ)
            // ->with('tagList', $output->tagListLang)
            ->with('tagList', $output->tagList)
            ->withPost(($output->post))
            ->with('featured_image_props', $output->f_image_props);
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function edit($id)
    {
        $access = Auth::user()->isAbleTo('update-posts');
        if (!$access) {
            abort(403);
        }
        $id = (int)$id;
        if (!(Auth::user()->isAbleTo('all-posts'))) {
            $post = Post::where('id', $id)->where('author_id', auth()->user()->id)->first();
        } else {
            $post = Post::where('id', $id)->first();
        }
        if (!$post) {
            abort(404);
        }
        $output = (new PostRepo)->edit($post);
        if (strtotime($output->post->published_at) == false) {
            $output->post->published_at = false;
        }
        $output->post->content = json_encode($post->content);
        $metaBlog = (new BlogRepo)->getSettings();
        $metaDisLang = $metaBlog['disable_language_group'];
        $meta_blog_slug = $metaBlog['blog_slug'];

        $users= User::whereHas('roles', function($query)
        {
            $query->where('name', 'superadmin')
                ->orWhere('name', 'testadmin')
                ->orWhere('name', 'admin')
                ->orWhere('name', 'editor')
                ->orWhere('name', 'author')
                ->orWhere('name', 'contributor');
        })->get();
        if ($metaDisLang) {
            $output->url = '/'.$meta_blog_slug;
            // $output->languages = false;
            $output->language_id = false;
        } else {
            $output->url = $output->url.'/'.$meta_blog_slug;
        }
        $page = 'Editing Post';
        $title_text = '<i class="fa fa-pencil m-r-10"></i>';
        $loggedUser = auth()->user()->id;
        $locker = false;
        $request = (object)[];
        $request->id = $id;
        $request->action = 'init';
        $locker = (new PostRepo)->locker($request)['locked_by'];
        return view('back-end.posts.create-edit')
            ->withcurrentUser($output->currentUser)
            ->withCategories($output->categories)
            ->withLanguages($output->languages)
            ->with('users', $users)
            ->with('locker', $locker)
            ->with('url', $output->url)
            ->with('langUrl', $output->langUrl)
            ->with('categoryList', $output->categoryList)
            ->with('postTagList', $output->postTagList)
            ->with('tagListOBJ', $output->tagListOBJ)
            // ->with('tagListLang', $output->tagListLang)
            ->with('tagList', $output->tagList)
            ->with('tagListInit', $output->tagListInit)
            ->with('tagListLangIDInit', $output->tagListLangIDInit)
            ->with('language_id', $output->post->language_id)
            ->with('page', $page)
            ->withPost($output->post)
            ->with('featured_image_props', $output->f_image_props)
            ->with('title_text', $title_text)
            ->with('loggedUser', $loggedUser)
            ->with('timePassed', (int)(new PostRepo)->timepassed);
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(UpdateRequest $request, $id)
    {
        $access = Auth::user()->isAbleTo('update-posts');
        if (!$access) {
            abort(403);
        }
        $id = (int)$id;
        $ajax = $request->ajax();
        $request = (object)$request->validated();
        $locking = (object)[];
        $locking->id = $id;
        $locking->action = 'editing';
        $locker = (new PostRepo)->locker($request)['locked_by'];
        if (!(Auth::user()->isAbleTo('all-posts'))) {
            $post = Post::where('id', $id)->where('author_id', auth()->user()->id)->first();
        } else {
            $post = Post::where('id', $id)->first();
        }
        // $post = Post::find(865);
        // if(!$post) return response()->json($send);
        if(!$locker && $post->locked_by !== Auth::user()->id){
            return response()->json(['locked_by' => User::find($post->locked_by)->display_name], 403);
        }
        $metaBlog = (new BlogRepo)->getSettings();
        $metaDisLang = $metaBlog['disable_language_group'];
        $meta_blog_slug = $metaBlog['blog_slug'];
        if ($post) {
            (new PostRepo)->post($post, $request, ['method'=>'update']);
            
            if (!$post) {
                abort(404);
            }
            $output = (new PostRepo)->edit($post);
            // $output->post->content = json_encode($post->content);
            $users= User::All();
            if ($metaDisLang) {
                $output->url = '/'.$meta_blog_slug;
                $output->languages = false;
                $output->language_id = false;
            } else {
                $output->url = $output->url.'/'.$meta_blog_slug;
            }
            $page = 'Editing Post';
            $output->post->content = ($output->post->content);
            if (strtotime($output->post->published_at) == false) {
                $output->post->published_at = false;
            }
            $send = [
                'currentUser' => $output->currentUser,
                'categories' => $output->categories,
                'languages' => $output->languages,
                'users' => $users,
                'url' => $output->url,
                'langUrl' => $output->langUrl,
                'categoryList' => $output->categoryList,
                'postTagList' => $output->postTagList,
                'tagListOBJ' => $output->tagListOBJ,
                'postTagListID' => $output->postTagListID,
                // 'tagListLang' => $output->tagListLang,
                'tagListLangIDInit' => $output->tagListLangIDInit,
                'tagList' => $output->tagList,
                'tagListInit' => $output->tagList,
                'language_id' => (int)$output->post->language_id,
                'page' => $page,
                'post' => $output->post,
                'featured_image_props' => $output->f_image_props,
                'loggedUser' => $output->loggedUser
            ];
        }
        if (!$post) {
            return response()->json([], 404);
        }
        if ($ajax) {
            return response()->json($send);
        } else {
            return $this->edit($id);
        }
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy(DeleteRequest $request, $id)
    {
        $access = Auth::user()->isAbleTo('delete-posts');
        if (!$access) {
            abort(403);
        }
        $request = (object)$request->validated();
        if (!property_exists($request, 'orderBy')) {
            $orderBy = '';
        } else {
            $orderBy = $request->orderBy;
        }
        if (!property_exists($request, 'orderDir')) {
            $orderDir = '';
        } else {
            $orderDir = $request->orderDir;
        }
        if(property_exists($request, 'foreignType')){
            $foreignType = $request->foreignType;
        }
        else{
            $foreignType = '';
        }
        if(property_exists($request, 'table')){
            $table = $request->table;
        }
        else{
            $table = '';
        }
        if ($id === "delete") {
            $posts = Post::find($request->ids);
            // $posts = Post::find([85,582]);
            if (!$posts->isEmpty()) {
                foreach ($posts as $key => $post) {
                    $post->tags()->detach();
                    $post->categories()->detach();
                    $post->delete();
                }
            }
        } else {
            $id = (int)$id;
            $post = Post::find($id);
            // $post = null;
            if (!$post) {
                $post->tags()->detach();
                $post->categories()->detach();
                Post::destroy($id);
            }
        }
        $getItems = Item::class(new Post)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->foreignType($foreignType)
            ->table($table)
            ->permission('all-posts')
            ->get();
        $postsJSON = [];
        foreach ($getItems['itemAll']['items'] as $index=>$postOne) {
            $postOne->user->name;
            if ($postOne->language != null) {
                $postOne->language->name;
            }
            if ($postOne->category != null) {
                $postOne->category->name;
            }
            $postsJSON[] = $postOne;
            
        }
        if (isset($posts)) {
            if ($posts->isEmpty()) {
                return response()->json($postsJSON, 404);
            }
        }
        if (isset($post)) {
            if (empty($post)) {
                return response()->json($postsJSON, 404);
            }
        }
        return response()->json($postsJSON);
    }
    /**
     *  Checks whether the slug exists.
     *
     *  @param CheckUniqRequest $request
     *  @return array
     */
    public function apiCheckUnique(CheckUniqRequest $request)
    {
        $request = (object)$request->validated();
        return json_encode(!Post::where('slug', '=', $request->slug)->exists());
    }
    public function apiLocker(CheckLockerRequest $request)
    {
        $request = (object)$request->validated();
        $out = (new PostRepo)->locker($request);
        return json_encode($out);
    }
}
