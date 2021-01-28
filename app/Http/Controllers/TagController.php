<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tag;
use Auth;
use App\Category;
use App\Language;

use App\Builders\ItemBuilder\ItemBuilder as Item;
use App\Repositories\GroupRepository as GroupRepo;
use App\Containers\SettingsContainer as Settings;

use App\Http\Requests\GroupRequests\GroupIndexRequest as IndexRequest;
use App\Http\Requests\GroupRequests\GroupUpdateRequest as UpdateRequest;
use App\Http\Requests\GroupRequests\GroupDeleteRequest as DeleteRequest;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexRequest $request)
    {
        $access = Auth::user()->isAbleTo('read-general');
        if (!$access) {
            abort(403);
        }
        $ajax = $request->ajax();
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

        if (!property_exists($request, 'foreignType')) {
            $foreignType = '';
        } else {
            $foreignType = $request->foreignType;
        }
        if (!property_exists($request, 'table')) {
            $table = '';
        } else {
            $table = $request->table;
        }

        $getItems = Item::class(new Tag)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->foreignType($foreignType)
            ->table($table)
            ->get();

        $tagsJSON = $getItems['itemsJSON'];

        $tagAll = $getItems['itemAll'];
        $idColMax = $getItems['idColMax'];
        $meta = (new Settings)->get();
        $metaDisLang = $meta['disable_language_group'];
        // if($metaDisLang){
        //     $languages = null;
        // }
        // else{
            $languages = Language::All();
        // }
        if ($ajax) {
            return response()->json($tagsJSON);
        } else {
            return view('back-end.general.index')
                ->with('title_text', '<i class="fa fa-tags"></i>')
                ->with('item_type', 'tags')
                ->with('itemsJSON', $tagsJSON)
                ->with('items', $tagAll['items'])->with('idColMax', $idColMax)
                ->with('items_url', '')->withLanguages($languages);
        }
    }

    /**
     * Store a newly created resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UpdateRequest $request)
    {
        $access = Auth::user()->isAbleTo('create-general');
        if (!$access) {
            abort(403);
        }
        $ajax = $request->ajax();
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
        $tag = new Tag();
        (new GroupRepo)->post($tag, $request);
        if (!property_exists($request, 'foreignType')) {
            $foreignType = '';
        } else {
            $foreignType = $request->foreignType;
        }
        if (!property_exists($request, 'table')) {
            $table = '';
        } else {
            $table = $request->table;
        }

        $getItems = Item::class(new Tag)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->foreignType($foreignType)
            ->table($table)
            ->get();
        $tagsJSON = $getItems['itemsJSON'];
        $tagAll = $getItems['itemAll'];
        $idColMax = $getItems['idColMax'];
        $pageCount = $getItems['pageCount'];
        $languages = Language::All();

        if ($ajax) {
            return response()->json($tagsJSON);
        }
    }

    /**
     * Display the tag.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $access = Auth::user()->isAbleTo('read-general');
        if (!$access) {
            abort(403);
        }
        $id = (int)$id;
        $tag = Tag::find($id);
        $idColMax = Tag::max('id');
        $tagNames=[];
        $tagJson= $tag->posts;
        for ($i=0; $i < count($tag->posts); $i++) {
            $tagJson[$i]['tags']=$tag->posts[$i]->tags;
            for ($j=0; $j < count($tag->posts[$i]->tags); $j++) {
                $tagNames[$i][]=$tag->posts[$i]->tags[$j]->name;
            }
        }
        return view('back-end.tags.show')
            ->with('tag', $tag)
            ->with('itemJson', json_encode($tagJson))
            ->with('itemNames', json_encode($tagNames));
    }

    /**
     * Update the tag in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $access = Auth::user()->isAbleTo('update-general');
        if (!$access) {
            abort(403);
        }
        $id = (int)$id;
        $ajax = $request->ajax();
        $request = (object)$request->validated();

        if (!property_exists($request, 'orderBy')) {
            $orderBy = null;
        } else {
            $orderBy = $request->orderBy;
        }
        if (!property_exists($request, 'orderDir')) {
            $orderDir = null;
        } else {
            $orderDir = $request->orderDir;
        }
                
        $filterByUser = Auth::user()->hasRole('author|contributor');
        if ($filterByUser) {
            return;
        }
        $tag = Tag::find($id);
        // $tag = Tag::find(856);
        if ($tag) {
            (new GroupRepo)->post($tag, $request);
        }
        if (!property_exists($request, 'foreignType')) {
            $foreignType = '';
        } else {
            $foreignType = $request->foreignType;
        }
        if (!property_exists($request, 'table')) {
            $table = '';
        } else {
            $table = $request->table;
        }

        $getItems = Item::class(new Tag)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->foreignType($foreignType)
            ->table($table)
            ->get();
        $tagsJSON = $getItems['itemsJSON'];
        $languages = Language::All();
        
        if (!$tag) {
            return response()->json($tagsJSON, 404);
        }
        if ($ajax) {
            return response()->json($tagsJSON);
        }
    }

    /**
     * Remove the tag from database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteRequest $request, $id)
    {
        $access = Auth::user()->isAbleTo('destroy-general');
        if (!$access) {
            abort(403);
        }
        $ajax = $request->ajax();
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
        $filterByUser = Auth::user()->hasRole('author|contributor');
        if ($filterByUser) {
            return;
        }
        if ($id === "delete") {
            $tags = Tag::find($request->ids);
            // $tags = Tag::find([4524,4724]);
            
            if (!$tags->isEmpty()) {
                foreach ($tags as $tag) {
                    $tag->posts()->detach();
                    $tag->delete();
                }
            }
        } else {
            $id = (int)$id;
            $tag=Tag::find($id);
            // $tag=null;
            
            if ($tag) {
                $tag->posts()->detach();
                $tag->delete();
            }
        }
        
        if (!property_exists($request, 'foreignType')) {
            $foreignType = '';
        } else {
            $foreignType = $request->foreignType;
        }
        if (!property_exists($request, 'table')) {
            $table = '';
        } else {
            $table = $request->table;
        }

        $getItems = Item::class(new Tag)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->foreignType($foreignType)
            ->table($table)
            ->get();
        $tagsJSON = $getItems['itemsJSON'];
        $languages = Language::All();
        if (isset($tags)) {
            if ($tags->isEmpty()) {
                return response()->json($tagsJSON, 404);
            }
        }
        if (isset($tag)) {
            if (empty($tag)) {
                return response()->json($tagsJSON, 404);
            }
        }
        if ($ajax) {
            return response()->json($tagsJSON);
        }
    }
}
