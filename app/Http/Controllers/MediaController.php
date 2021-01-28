<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Media;
use App\User;

use Auth;
use Storage;
use Session;
use File;
use Intervention\Image\ImageManagerStatic as Image;
use App\Http\Requests\MediaRequests\MediaGenRequest as GenRequest;
use App\Http\Requests\MediaRequests\MediaDeleteRequest as DeleteRequest;

class MediaController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(GenRequest $request)
    {
        $access = Auth::user()->isAbleTo('read-media');
        if (!$access) {
            abort(403);
        }
        $ajax = $request->ajax();
        $request = (object)$request->validated();
        $url = strip_tags('');
        if ($ajax) {
            $urLink = $request->url;
        } else {
            $urLink = '';
        }
        $folderTree = explode("/", $urLink);
        $directory = public_path();
        $media = Storage::files('public'.'/content/'.$urLink);
        $folders = Storage::directories('public'.'/content/'.$urLink);
        $output = Media::outputFolderList($urLink);
        if ($ajax) {
            $output2 = json_encode($output, JSON_PARTIAL_OUTPUT_ON_ERROR);
            return response($output2);
        } else {
            $output2 = json_encode($output, JSON_PARTIAL_OUTPUT_ON_ERROR);
            $error = json_last_error_msg();
            return view('back-end.media.index')
                ->with('initFileProp', $output2)
                ->with('media', $media)
                ->with('folders', $folders)
                ->with('folderTree', $folderTree)
                ->with('folderUrl', $url);
        }
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(GenRequest $request)
    {
        $access = Auth::user()->isAbleTo('create-media');
        if (!$access) {
            abort(403);
        }
        $request = $request->validated();
        $input = $request;
        $urLink = $input['url'];
        if ($input['action']==='uploadFile') {
            $media = new Media();
            $media->uploadFiles($input['files'], $urLink);
        }
        if ($input['action']==='createFolder') {
            Storage::disk('upload')->makeDirectory($input['name']);
        }
        $output = Media::outputFolderList($input['url']);
        return response()->json($output);
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function edit($id)
    {
        $access = Auth::user()->isAbleTo('update-media');
        if (!$access) {
            abort(403);
        }
        $id = (int)$id;
        return view("back-end.media.edit")->withPost($post);
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(GenRequest $request, $id)
    {
        $access = Auth::user()->isAbleTo('update-media');
        if (!$access) {
            abort(403);
        }
        $id = (int)$id;
        $request = (object)$request->validated();
        return redirect()->route('posts.index', $id);
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy(DeleteRequest $request)
    {
        $access = Auth::user()->isAbleTo('delete-media');
        if (!$access) {
            abort(403);
        }
        $request = (object)$request->validated();
        $filterByUser = Auth::user()->isAbleTo('delete-media');
        if (!$filterByUser) {
            $output = Media::outputFolderList($request->url);
            return response()->json($output);
        }
        if (!empty($request->items)) {
            Storage::disk('upload')->delete($request->items);
            Storage::disk('thumb-360')->delete($request->items);
            Storage::disk('thumb-640')->delete($request->items);
        }
        if (!empty($request->itemsFolder)) {
            for ($i=0; $i < count($request->itemsFolder); $i++) {
                Storage::disk('upload')->deleteDirectory($request->itemsFolder[$i]);
                Storage::disk('thumb-360')->deleteDirectory($request->itemsFolder[$i]);
                Storage::disk('thumb-640')->deleteDirectory($request->itemsFolder[$i]);
            }
        }
        $urLink = $request->name;
        $publicFolder=substr($urLink, 0, 6);
        if ($publicFolder!='public') {
            $urLink = 'public'.'/'.$urLink;
        }
        $output = Media::outputFolderList($request->url);
        return response()->json($output);
    }
    public static function folderManagement($url)
    {
        $url = strip_tags($url);
        $folderTree = explode("/", $url);
        return view("back-end.media.index")->with('folderTree', $folderTree);
    }
}
