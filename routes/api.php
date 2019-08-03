<?php

use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {
  Route::get('/posts/unique', 'PostController@apiCheckUnique')->name('api.posts.unique');
  Route::post('/posts/locker', 'PostController@apiLocker')->name('api.posts.locker');
  Route::post('/unique/slug/{group}', 'UniqueSlugController@checkSlug')->name('api.groups.unique');
});
