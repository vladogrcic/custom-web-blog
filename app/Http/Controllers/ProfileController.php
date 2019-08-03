<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Storage;
use Hash;

use App\Media;
use App\User;
use App\Profile;

use App\Helpers\General\GeneralHelper as General;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AvatarRequest;

class ProfileController extends Controller
{
    public $info_config = [
        'gravatar',
        'first_name',
        'last_name',
        'gender',
        'birthday',
        'mobile_phone',
        'address',
        'city',
        'country',
    ];
    public $info_config_users = [
        'email',
        'username',
        'displayname',
    ];
    public $inFolderLocs = [
        'orig'=>'avatars',
        'thumbs'=>[
            'avatars_thumb-640', 'avatars_thumb-360', 'avatars_thumb-50'
        ]
    ];
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProfileRequest $request)
    {
        $configDisks = config('filesystems.disks.'.$this->inFolderLocs['orig']);
        $user_id = Auth::id();
        $name = [];
        $items = [];
        for ($i=0; $i < count($this->info_config); $i++) {
            $input = $this->info_config[$i];
            $meta_key = Profile::where('user_id', '=', $user_id)->where('meta_key', '=', $input)->first();
            if ($meta_key) {
                $name = Profile::find($meta_key->id);
                if ($name->meta_value) {
                    $items[$input] = $name->meta_value;
                } else {
                    $items[$input] = '';
                }
            } else {
                $items[$input] = '';
            }
            if ($input == 'birthday') {
                $items[$input] = '1993-12-23';
            }
        }
        $user = Auth::user();
        for ($j = 0; $j < count($this->info_config_users); $j++) {
            $inputUser = $this->info_config_users[$j];
            $user_data = $user->name;
            $items[$inputUser] = $user->email;
        }
        $getAvatar = Profile::where('user_id', '=', $user_id)->where('meta_key', '=', 'avatar')->first();
        if ($getAvatar) {
            $items['avatar'] = Profile::find($getAvatar->id)->meta_value;
            if ($items['avatar']) {
                $items['avatar'] = Storage::url($configDisks['storageUrl'].'/'.$items['avatar']);
            }
        } else {
            $items['avatar'] = '';
        }
        $items['email'] = $user->email;
        $items['username'] = $user->name;
        $items['displayname'] = $user->display_name;
        return view('back-end.profile')
            ->with('items', $items)
            ->with('user', $user)
            ->with('items_url', '');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function edit(ProfileRequest $profile)
    {
        $this->index();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(ProfileRequest $request)
    {
        $request = (object)$request->validated();
        if ($request->action!=='updateProfile') {
            return;
        }
        $user_id = Auth::id();
        $info = Profile::where('user_id', $user_id)->first();
        for ($i=0; $i < count($this->info_config); $i++) {
            $currentName = $this->info_config[$i];
            $meta_key = Profile::where('user_id', '=', $user_id)->where('meta_key', '=', $currentName)->first();
            $sentValue = $request->info[$currentName];
            if (!$sentValue) {
                if ($meta_key) {
                    $meta_key->delete();
                }
                continue;
            }
            if ($meta_key) {
                $name = Profile::find($meta_key->id);
            } else {
                $name = new Profile();
                $name->meta_key = $currentName;
            }
            $name->user_id = $user_id;
            $name->meta_value = ($sentValue);
            $name->save();
        }
        $user = User::find($user_id);
        if ($user->display_name) {
            $user->display_name =  ($request->info['displayname']);
        }
        if ($user->email) {
            $user->email =  ($request->info['email']);
        }
        if ($user->name) {
            $user->name =  ($request->info['username']);
        }
        if ($request->info['password']) {
            $user->password =  Hash::make($request->info['password']);
        }
        $user->save();
        return response()->json(('success'));
    }

    /**
     * Add avatar to server.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    
    public function file(AvatarRequest $request)
    {
        if (!($request->action!=='uploadAvatar' || $request->action!=='deleteAvatar')) {
            return;
        }
        $configDisks = config('filesystems.disks.avatars');
        $user_id = Auth::id();
        $input = $request->validated();
        $output = [];
        $media = new Media();
        $currentName = 'avatar';
        $meta_key = Profile::where('user_id', '=', $user_id)->where('meta_key', '=', $currentName)->first();
        if ($request->action == 'deleteAvatar') {
            Storage::disk($this->inFolderLocs['orig'])->delete($meta_key->meta_value);
            foreach ($this->inFolderLocs['thumbs'] as $value) {
                Storage::disk($value)->delete($meta_key->meta_value);
            }
            if ($meta_key) {
                $meta_key->delete();
            }
        }
        $filename = General::getRandomWord(10);
        $filename_backup = General::getRandomWord(10);
        if ($request->action=='uploadAvatar') {
            $input['file']->customSize = 300;
            $input['file']->square = true;
            if ($meta_key) {
                $name = Profile::find($meta_key->id);
                $input['file']->filename = $meta_key->meta_value;
            } else {
                $r=0;
                $input['file']->filename = General::getRandomWord(10).'.jpg';
                while (file_exists($configDisks['root'].'/'.$input['file']->filename) && $r<55) {
                    $input['file']->filename = General::getRandomWord(10).'.jpg';
                    $r++;
                }
                $name = new Profile();
                $name->meta_key = $currentName;
            }
            $output = $media->uploadFiles([$input['file']], null, $this->inFolderLocs);
            if (!$output) {
                if ($meta_key) {
                    $meta_key->delete();
                }
            }
            $name->user_id = $user_id;
            $name->meta_value = strip_tags($output);
            $name->save();
        }
        return response()->json($output);
    }
}
