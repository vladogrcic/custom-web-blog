<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Storage;
use File;
use Session;
use App\Setting;
use App\Media;
use App\Language;

use App\Http\Requests\SettingRequest;
use App\Http\Requests\FaviconRequest;
use App\Repositories\MetaRepository as MetaRepo;

class SettingController extends Controller
{
    public $info_config = [
        // 'favicon',
        'title',
        'description',
        'site_address',
        'timezone',
        'date_format',
        'time_format',
        'perma_format',
        'disable_language_group',
        'per_page',
        'show_lang_switch',
        'main_lang',
        'blog_slug',
    ];
    public $inFolderLocs = [
        'orig'=>'public',
    ];
    public function __construct()
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SettingRequest $request)
    {
        $access = Auth::user()->isAbleTo('read-settings');
        if (!$access) {
            abort(403);
        }
        $configDisks = config('filesystems.disks.'.$this->inFolderLocs['orig']);
        $name = [];
        $items = [];
        for ($i=0; $i < count($this->info_config); $i++) {
            $input = $this->info_config[$i];
            $model = new MetaRepo;
            $model->metaModel = new Setting;
            $items[$input] = $model->getMeta($input);
        }
        if (!$items['date_format']) {
            $items['date_format'] = 'd.m.Y';
        }
        if (!$items['time_format']) {
            $items['time_format'] = 'G:i';
        }
        if (!$items['perma_format']) {
            $items['perma_format'] = 'slug';
        }
        if (!$items['per_page']) {
            $items['per_page'] = 5;
        }
        if (!$items['show_lang_switch']) {
            $items['show_lang_switch'] = 'icons';
        }
        if (!$items['main_lang']) {
            $items['main_lang'] = 1;
        }
        $timeZoneGroups = [
            'EUROPE' => \DateTimeZone::listIdentifiers(\DateTimeZone::EUROPE),
            'AMERICA' => \DateTimeZone::listIdentifiers(\DateTimeZone::AMERICA),
            'AUSTRALIA' => \DateTimeZone::listIdentifiers(\DateTimeZone::AUSTRALIA),
            'AFRICA' => \DateTimeZone::listIdentifiers(\DateTimeZone::AFRICA),
            'ANTARCTICA' => \DateTimeZone::listIdentifiers(\DateTimeZone::ANTARCTICA),
            'ASIA' => \DateTimeZone::listIdentifiers(\DateTimeZone::ASIA),
            'ATLANTIC' => \DateTimeZone::listIdentifiers(\DateTimeZone::ATLANTIC),
            'PACIFIC' => \DateTimeZone::listIdentifiers(\DateTimeZone::PACIFIC)
        ];
        $checkFavicon = File::exists('storage/favicon.png');
        if ($checkFavicon) {
            $items['favicon'] = $checkFavicon;
            if ($items['favicon']) {
                $items['favicon'] = Storage::url('favicon.png');
            }
        } else {
            $items['favicon'] = false;
        }
        $languages = Language::All();
        return view('back-end.settings')
        ->with('languages', $languages)
        ->with('items', $items)
        ->with('items_url', '')
        ->with('timeZoneGroups', $timeZoneGroups);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Setting  $profile
     * @return \Illuminate\Http\Response
     */
    public function edit(SettingRequest $profile)
    {
        // $this->index();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Setting  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(SettingRequest $request)
    {
        $access = Auth::user()->isAbleTo('update-settings');
        if (!$access) {
            abort(403);
        }
        $request = (object)$request->validated();
        if ($request->action!=='updateSettings') {
            return;
        }
        for ($i=0; $i < count($this->info_config); $i++) {
            $currentName = $this->info_config[$i];
            $model = new MetaRepo;
            $model->metaModel = new Setting;
            $items = $model->setMeta($currentName, $request->info[$currentName]);
            if ($currentName == 'disable_language_group') {
                Session::forget('locale');
            }
            if (!$items) {
                continue;
            }
        }
        
        return response()->json(('success'));
    }

    /**
     * Add favicon to server.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Setting  $profile
     * @return \Illuminate\Http\Response
     */
    
    public function file(FaviconRequest $request)
    {
        $access = Auth::user()->isAbleTo('update-settings');
        if (!$access) {
            abort(403);
        }
        if (!($request->action!=='uploadFavicon' || $request->action!=='deleteFavicon')) {
            return;
        }
        $configDisks = config('filesystems.disks.public');
        $input = $request->validated();
        $output = [];
        $media = new Media();
        $currentName = 'favicon';
        $filename = 'favicon.png';
        if ($request->action == 'deleteFavicon') {
            Storage::disk($this->inFolderLocs['orig'])->delete($filename);
        }
        if ($request->action=='uploadFavicon') {
            $input['file']->customSize = 300;
            $input['file']->square = true;
            $input['file']->filename = $filename;
            
            $output = $media->uploadFiles([$input['file']], null, $this->inFolderLocs);
        }
        return response()->json($output);
    }
}
