<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Post;
use App\Language;
use App\Tag;
use Auth;
use App\Builders\ItemBuilder\ItemBuilder as Item;
use App\Repositories\GroupRepository as GroupRepo;
use App\Containers\SettingsContainer as Settings;

use App\Http\Requests\GroupRequests\GroupIndexRequest as IndexRequest;
use App\Http\Requests\GroupRequests\GroupUpdateRequest as UpdateRequest;
use App\Http\Requests\GroupRequests\GroupDeleteRequest as DeleteRequest;

class CategoryController extends Controller
{
    public function __construct()
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexRequest $request)
    {
        $access = Auth::user()->can('read-general');
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

        $getItems = Item::class(new Category)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->foreignType($foreignType)
            ->table($table)
            ->get();

        $categoriesJSON = $getItems['itemsJSON'];
        $categoryAll = $getItems['itemAll'];
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
            return response()->json($categoriesJSON);
        } else {
            return view('back-end.general.index')
                ->with('item_type', 'categories')
                ->with('title_text', '<i class="fa fa-list-alt"></i>')
                ->with('itemsJSON', $categoriesJSON)
                ->with('items', $categoryAll['items'])
                ->with('idColMax', $idColMax)
                ->with('items_url', '')->withLanguages($languages);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UpdateRequest $request)
    {
        $access = Auth::user()->can('create-general');
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

        $category = new Category();
        (new GroupRepo)->post($category, $request);
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

        $getItems = Item::class(new Category)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->foreignType($foreignType)
            ->table($table)
            ->get();
        $categoriesJSON = $getItems['itemsJSON'];
        $languages = Language::All();

        if ($ajax) {
            return response()->json($categoriesJSON);
        }
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
        $access = Auth::user()->can('update-general');
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

        $id = (int)$id;
        $category = Category::find($id);
        // $category = Category::find(856);
        if ($category) {
            (new GroupRepo)->post($category, $request);
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

        $getItems = Item::class(new Category)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->foreignType($foreignType)
            ->table($table)
            ->get();
        $categoriesJSON = $getItems['itemsJSON'];
        $languages = Language::All();
        if (!$category) {
            return response()->json($categoriesJSON, 404);
        }
        if ($ajax) {
            return response()->json($categoriesJSON);
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
        $access = Auth::user()->can('delete-general');
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

        if ($request->action === "deleteBulk") {
            $categories = Category::find($request->ids);
            // $categories = Category::find([5647,7645]);
            
            if (!$categories->isEmpty()) {
                foreach ($categories as $category) {
                    $category->posts()->detach();
                    $category->delete();
                }
            }
        } else {
            $id = (int)$id;
            $category=Category::find($id);
            // $category=null;
            
            if ($category) {
                $category->posts()->detach();
            
                $category->delete();
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

        $getItems = Item::class(new Category)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->foreignType($foreignType)
            ->table($table)
            ->get();
        $categoriesJSON = $getItems['itemsJSON'];
        $languages = Language::All();
        if (isset($categories)) {
            if ($categories->isEmpty()) {
                return response()->json($categoriesJSON, 404);
            }
        }
        if (isset($category)) {
            if (empty($category)) {
                return response()->json($categoriesJSON, 404);
            }
        }
        if ($ajax) {
            return response()->json($categoriesJSON);
        }
    }
}
