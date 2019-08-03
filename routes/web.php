<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Repositories\BlogRepository as BlogRepo;
use App\Language;

Auth::routes();
Route::prefix('manage')->middleware('role:superadmin|testsuperadmin|admin|testadmin|editor|author|contributor')->group(function () {
    Route::get('/', 'BackEndController@index');
    Route::get('/dashboard', 'BackEndController@dashboard')->name('manage.dashboard');
    Route::resource('/settings', 'SettingController');
    Route::resource('/profile', 'ProfileController');
    Route::resource('/users', 'UserController');
    Route::resource('/permissions', 'PermissionController', ['except' => 'destroy'])->middleware('role:superadmin');
    Route::resource('/roles', 'RoleController', ['except' => 'destroy'])->middleware('role:superadmin');
    Route::resource('/posts', 'PostController');
    Route::resource('/categories', 'CategoryController', ["except" => ["create"]]);
    Route::resource('/languages', 'LanguageController', ["except" => ["create"]]);
    Route::resource('/tags', 'TagController', ["except" => ["create"]]);
    Route::get('media/{folder?}', 'MediaController@index')->where('folder', '.*');
    Route::resource('media', 'MediaController');
    Route::post('upload/avatar', 'ProfileController@file');
    Route::post('upload/favicon', 'SettingController@file');
});
Route::get('/home', 'HomeController@index')->name('home');
if(Schema::hasTable('settings') && Schema::hasTable('languages')){
    $metaBlog = (new BlogRepo)->getSettings();
    $blog_slug = $metaBlog['blog_slug'];
    // $blog_slug = 'hgfhdfgh';
    if ($metaBlog['disable_language_group']) {
        Route::get('/', function () use($blog_slug){
            return redirect($blog_slug);
        });
        Route::get('/{lang}/'.$blog_slug, function () use($blog_slug) {
            return redirect($blog_slug);
        });
        
        Route::prefix($blog_slug)->group(function ($lang) use($blog_slug) {
            Route::get('/', ['uses' => 'FrontEndController@blog', 'blog_slug' => $blog_slug])
                ->name('blog');
            Route::get('/{year?}/{month?}/{day?}', ['uses' => 'BlogController@getBlogDate', 'blog_slug' => $blog_slug])
                ->name('blog.date')
                ->where('year', '[0-9]+')
                ->where('month', '[0-9]+')
                ->where('day', '[0-9]+')
                ->defaults('year', false)
                ->defaults('month', false)
                ->defaults('day', false);
            Route::get('/{slug?}', ['uses' => 'BlogController@getSingle', 'blog_slug' => $blog_slug])
                ->name('blog.single')
                ->where('slug', '[\w\d\-\_]+');
            Route::get('/{year}/{month}/{slug}', ['uses' => 'BlogController@getSingle', 'blog_slug' => $blog_slug])
                ->name('blog.singleYearMonth')
                ->where('year', '[0-9]+')
                ->where('month', '[0-9]+')
                ->where('slug', '[\w\d\-\_]+');
            Route::get('/{year}/{month}/{day}/{slug}', ['uses' => 'BlogController@getSingle', 'blog_slug' => $blog_slug])
                ->name('blog.singleYearMonthDay')
                ->where('year', '[0-9]+')
                ->where('month', '[0-9]+')
                ->where('day', '[0-9]+')
                ->where('slug', '[\w\d\-\_]+');

            Route::get('/category/{slug?}', ['uses' => 'BlogController@getBlogCategory', 'blog_slug' => $blog_slug])
                ->name('blog.category')
                ->where('slug', '[\w\d\-\_]+');
            Route::get('/tag/{slug?}', ['uses' => 'BlogController@getBlogTag', 'blog_slug' => $blog_slug])
                ->name('blog.tag')
                ->where('slug', '[\w\d\-\_]+');
            Route::get('/author/{slug?}', ['uses' => 'BlogController@getBlogAuthor', 'blog_slug' => $blog_slug])
                ->name('blog.author')
                ->where('slug', '[\w\d\-\_]+');
        });
    } else {
        $lang = Language::All();
        Route::get('/{slug?}', function () use ($lang, $blog_slug) {
            $localCode = app()->getLocale();
            if (!$localCode) {
                $localCode = $lang[0]->slug;
            }
            return redirect($localCode.'/'.$blog_slug);
        });
        Route::get('/'.$blog_slug, function () use ($lang, $blog_slug) {
            $localCode = app()->getLocale();
            if (!$localCode) {
                $localCode = $lang[0]->slug;
            }
            // return redirect(App::getLocale().'/blog');
            return redirect($localCode.'/'.$blog_slug);
        });
        // Route::get('/{slug}', function () {
        //     $localCode = app()->getLocale();
        //     if(!$localCode) $localCode = $lang[0]->slug;
        //     // return redirect(App::getLocale().'/blog');
        //     return redirect($localCode.'/'.$blog_slug);
        // });
        Route::prefix('{lang?}/'.$blog_slug)->group(function ($lang) use ($blog_slug) {
            // App::setLocale($lang);
            Route::get('/', ['uses' => 'FrontEndController@blog', 'blog_slug' => $blog_slug])
                ->name('blog')
                ->where('lang', '[\w\d\-\_]+')
                ->defaults('lang', 'en');
            Route::get('/{year?}/{month?}/{day?}', ['uses' => 'BlogController@getBlogDate', 'blog_slug' => $blog_slug])
                ->name('blog.date')
                ->where('lang', '[\w\d\-\_]+')
                ->where('year', '[0-9]+')
                ->where('month', '[0-9]+')
                ->where('day', '[0-9]+')
                ->defaults('lang', false)
                ->defaults('year', false)
                ->defaults('month', false)
                ->defaults('day', false);
            Route::get('/{slug?}', ['uses' => 'BlogController@getSingle', 'blog_slug' => $blog_slug])
                ->name('blog.single')
                ->where('slug', '[\w\d\-\_]+')
                ->where('lang', '[\w\d\-\_]+')
                ->defaults('lang', false);
            Route::get('/{year}/{month}/{slug}', ['uses' => 'BlogController@getSingle', 'blog_slug' => $blog_slug])
                ->name('blog.singleYearMonth')
                ->where('year', '[0-9]+')
                ->where('month', '[0-9]+')
                ->where('slug', '[\w\d\-\_]+')
                ->where('lang', '[\w\d\-\_]+')
                ->defaults('lang', false);
            Route::get('/{year}/{month}/{day}/{slug}', ['uses' => 'BlogController@getSingle', 'blog_slug' => $blog_slug])
                ->name('blog.singleYearMonthDay')
                ->where('year', '[0-9]+')
                ->where('month', '[0-9]+')
                ->where('day', '[0-9]+')
                ->where('slug', '[\w\d\-\_]+')
                ->where('lang', '[\w\d\-\_]+')
                ->defaults('lang', false);

            Route::get('/category/{slug?}', ['uses' => 'BlogController@getBlogCategory', 'blog_slug' => $blog_slug])
                ->name('blog.category')
                ->where('slug', '[\w\d\-\_]+')
                ->where('lang', '[\w\d\-\_]+')
                ->defaults('lang', false);
            Route::get('/tag/{slug?}', ['uses' => 'BlogController@getBlogTag', 'blog_slug' => $blog_slug])
                ->name('blog.tag')
                ->where('slug', '[\w\d\-\_]+')
                ->where('lang', '[\w\d\-\_]+')
                ->defaults('lang', false);
            Route::get('/author/{slug?}', ['uses' => 'BlogController@getBlogAuthor', 'blog_slug' => $blog_slug])
                ->name('blog.author')
                ->where('slug', '[\w\d\-\_]+')
                ->where('lang', '[\w\d\-\_]+')
                ->defaults('lang', false);
        });
    }

    Route::get('locale/{locale}/{page?}', function ($locale, $page=false) use($blog_slug) {
        \Session::put('locale', $locale);
        $in = url()->previous();
        $count = strlen(url('/'))+1;
        $in2 = substr($in, $count);
        $in2 = strstr($in2, '/');
        if ($page == 'single') {
            return redirect(url('/').'/'.$locale.'/'.$blog_slug);
        } else {
            return redirect(url('/').'/'.$locale.$in2);
        }
    });
}