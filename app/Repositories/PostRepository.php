<?php
namespace App\Repositories;

use Auth;
use Config;
use App\Post;
use App\Category;
use App\Tag;
use App\Media;
use App\User;
use Storage;
use App\Language;
use App\Containers\SettingsContainer as Settings;
use App\Repositories\BlogRepository as BlogRepo;

/**
 * Repository for posts.
 *
 * @method void post($post, $request, array $options=['method'=>'create']) Updates or adds posts to the database.
 * @method object edit($post, $request, array $options=['method'=>'create']) Retrieves values from the database for the post.
 *
 */
class PostRepository
{
    public $timepassed;
    public function __construct()
    {
        if (Config::has('app.edit_timeout')) {
            $this->timepassed = Config::get('app.edit_timeout')*60; //** 15 min - The time passed where the app will notify the user that it locked the current editing user out. */
        }
        else{
            $this->timepassed = 15*60; //** 15 min - The time passed where the app will notify the user that it locked the current editing user out. */
        }
    }
    /**
     * Handles post updating or creating.
     *
     * @param Post $post Instance of the Post class.
     * @param Request $request Input from front-end in Request type.
     * @param array $options Additional options for the method.
     * @return void
     *
     */
    public function post(Post $post, $request, array $options=['method'=>'create'])
    {
        if ($post::where('slug', '=', $request->slug)->exists()&&$options['method']=='create') {
            return;
        } else {
            $post->slug = $request->slug;
        }
        $post->title = $request->title;
        $post->featured_image = $request->featured_image;
        $authorCustom = isset($request->authorCustom)?filter_var($request->authorCustom, FILTER_VALIDATE_BOOLEAN):false;
        if (($options['method']==='create'&&!$authorCustom) || !$request->author_id) {
            $post->author_id = Auth::user()->id;
        }
        if(User::where('id', $request->author_id)->exists()){
            $inputUser = User::where('id', $request->author_id)->first();
            $permitted = $inputUser->hasRole('superadmin|testsuperadmin|admin|testadmin|editor|author|contributor');
            if ($authorCustom) {
                if ($permitted) {
                    $post->author_id = $request->author_id;
                }
            }
        }
        
        $meta = (new Settings)->get();
        $metaDisLang = $meta['disable_language_group'];
        $metaMainLang = $meta['main_lang'];
        if ($metaDisLang) {
            $post->language_id = (int)$metaMainLang;
        } else {
            $post->language_id = $request->language_id;
        }
        $post->excerpt = $request->excerpt;
        $post->content = $request->content;
        $filterByUser = Auth::user()->isAbleTo('publish-posts');
        if ($filterByUser) {
            if ($request->status == null) {
                $post->status = false;
                $post->published_at = date('');//*"0000-00-00 00:00:00";
            } else {
                if((int)$request->status){
                    $post->status = true;
                    $post->published_at = date('Y-m-d H:i:s');//*"2018-03-09 09:43:24";
                }
                else{
                    $post->status = false;
                    $post->published_at = date('');//*"2018-03-09 09:43:24";
                }
            }
        } else {
            $post->status;
        }
        $post->save();
        if (property_exists($request, 'tags')) {
            $newTags=[];
            for ($i=0; $i < count($request->tags); $i++) {
                $tag_to_check = Tag::where('name', $request->tagsNAME[$i])->first();
                if (!$tag_to_check) {
                    $tag = new Tag();
                    $idColMax = Tag::max('id');
                    $tag->id = $idColMax+1;
                    $tag->name = $request->tagsNAME[$i];
                    $tag->slug = str_slug($request->tagsNAME[$i]);
                    $tag->language_id = $request->language_id;
                    $tag->save();
                    $newTags[] = $idColMax+1;
                } else {
                    $check_lang = $tag_to_check->language_id == $post->language_id;
                    if($check_lang)
                        $newTags[] = (int)$request->tags[$i];
                    else{
                        // ! Add error message for it.
                    }
                }
            }
            if(is_array($newTags))
                $post->tags()->sync($newTags);
        }
        // if (property_exists($request, 'language_id')) {
        //     $post->languages()->sync($request->language_id, false);
        // }
        if (property_exists($request, 'categories')) {
            for ($i=0; $i < count($request->categories); $i++) {
                $category_to_check = Category::find($request->categories[$i]);
                $check_lang = $category_to_check->language_id == $post->language_id;
                if(!$check_lang) return;
            }
            $post->categories()->sync($request->categories);
        }
    }
    /**
     * Queries the database and passes post values to the front-end.
     *
     * @param Post $post Instance of the Post class.
     * @return array $output Outputs post data to update the edit page.
     *
     */
    public function edit($post)
    {
        $metaBlog = (new BlogRepo)->getSettings();
        $metaDisLang = $metaBlog['disable_language_group'];
        $metaMainLang = (int)$metaBlog['main_lang'];

        $post->author_name = User::find($post->author_id)->name;
        if ($post->language != null) {
            $post->language->name;
        }
        $created_at = $post->created_at->format('d.m.Y H:i:s');
        $post->created_at_custom = $created_at;
        $post->published_at = date_format(date_create($post->published_at), 'd.m.Y H:i:s');
        $user_id = $post->author_id;
        $email = $post->user->email;
        $avatar = (new User)->getAvatar($email, $user_id);
        $currentUser = (object)[
            'id' => $post->author_id,
            'name' => $post->author_name,
            'avatar' => $avatar
        ];
        $loggedUser = auth()->user()->id;
        // if($metaDisLang){
        //     $categories= Category::where('language_id', $metaMainLang)->get();
        // }
        // else{
        $categories= Category::All();
        // }
        $languages= Language::All();
        $defaultLangIndex = 0;
        
        // if($metaDisLang){
        //     $tags= Tag::where('language_id', $metaMainLang)->get();
        // }
        // else{
        $tags= Tag::All();
        // }

        if (Storage::disk('upload')->has($post->featured_image)) {
            $f_image_props = Media::outputFileProps($post->featured_image);
        } else {
            $f_image_props = [
                'thumbUrl'=> '',
                'name'=> '',
                'mimeType'=> '',
                'size'=> '',
                'resolution'=> [
                    'width'=>'',
                    'height'=>'',
                ],
                'modified'=> [
                    'date'=>'',
                    'time'=>'',
                ],
                'exif'=>[]
            ];
        }
        $tagList = [];
        $tagListInit = [];
        $tagListOBJ = [];
        $tagListLangIDInit = [];
        foreach ($tags as $tag) {
            $tagListInit[] = $tag->name;
            $tagListLangIDInit[] = $tag->language->id;
            // if($metaDisLang){
            //     if($metaDisLang == $tag->language->id){
            //         $tagList[] = $tag->name;
            //         $tagListOBJ[$tag->name] = $tag->id;
            //         if ($tag->language) {
            //             $tagListLang[] = $tag->language->name;
            //             $tagListLangID[] = $tag->language->id;
            //         }
            //     }
            // }
            // else{
            if ($post->language_id == $tag->language->id) {
                $tagList[] = $tag->name;
                $tagListOBJ[$tag->name] = $tag->id;
                if ($tag->language) {
                    $tagListLang[] = $tag->language->name;
                    $tagListLangID[] = $tag->language->id;
                }
            }
            // }
            
            // $tagListInit[] = $tag->name;
            // $tagListOBJ[$tag->name] = $tag->id;
            // if ($tag->language) {
            //     $tagListLangIDInit[] = $tag->language->name;
            // }
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
        $categoryList = [];
        foreach ($post->categories as $category) {
            $categoryList[] = $category->id;
        }
        $output = (object)[];
        $output->langJson = [];
        $output->url = '';
        if ($post->language) {
            $output->url = $post->language->slug;
        }
        $output->langUrl = [];
        for ($i=0; $i < count($languages); $i++) {
            $output->langJson[] = [
                'id' => $languages[$i]->id,
                'name' => $languages[$i]->name,
                'slug' => $languages[$i]->slug,
            ];
            $output->langUrl[$languages[$i]->id] = $languages[$i]->slug;
        }
        $output->currentUser = $currentUser;
        $output->post = $post;
        $output->post->content = $post->content;
        $output->post->language_id = (int)$post->language_id;
        $output->post->author_id = (int)$post->author_id;
        $output->categories = $categories;
        $output->languages = $languages;
        $output->categoryList = $categoryList;
        $output->postTagList = $postTagList;
        $output->postTagListID = $postTagListID;
        $output->tagListOBJ = $tagListOBJ;
        // $output->tagListLang = $tagListLang;
        $output->tagList = $tagList;
        $output->tagListInit = $tagListInit;
        $output->tagListLangIDInit = $tagListLangIDInit;
        $output->f_image_props = $f_image_props;
        $output->loggedUser = $loggedUser;
        
        return $output;
    }
    public function locker($request)
    {
        $post = Post::where('id', '=', $request->id)->first();
        $currentTime = strtotime(date('Y/m/d h:i:s a'));
        $out = [
            'locked_by'=>false
        ];
        if ($post) {
            $now = time();
            $savedTimeTimestamp= date("Y-m-d H:i:s", strtotime($post->locked));
            if (($request->action === 'editing' || $request->action === 'init') && (Auth::user()->id == $post->locked_by)) {
                $out['locked_by'] = false;
                // if($request->action === 'init'){
                //     $out['locked_by'] = User::find($post->locked_by)->display_name;
                // }
                $post->locked = date('Y-m-d H:i:s');
                $post->locked_by = Auth::user()->id;
                $post->save();
            }
            if (($now > (strtotime($savedTimeTimestamp)+$this->timepassed))) {
                $out['locked_by'] = false;
                // if($request->action === 'init'){
                //     $out['locked_by'] = User::find($post->locked_by)->display_name;
                // }
                $post->locked = date('Y-m-d H:i:s');
                $post->locked_by = Auth::user()->id;
                $post->save();
            } else {
                if ($post->locked_by !== Auth::user()->id) {
                    $out['locked_by'] = User::find($post->locked_by)->display_name;
                }
            }
            
            if ($request->action === 'force') {
                $out['locked_by'] = false;
                $post->locked = null;
                $post->locked_by = null;
                $post->save();
            }
        }
        return $out;
    }
}
