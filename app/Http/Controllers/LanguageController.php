<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Language;
use Auth;
use LaraFlash;

use App\Post;
use App\Category;
use App\Tag;

use App\Builders\ItemBuilder\ItemBuilder as Item;
use App\Repositories\GroupRepository as GroupRepo;

use App\Http\Requests\LanguageRequest;
use App\Http\Requests\GroupRequests\GroupIndexRequest as IndexRequest;
use App\Http\Requests\GroupRequests\GroupUpdateRequest as UpdateRequest;
use App\Http\Requests\GroupRequests\GroupDeleteRequest as DeleteRequest;

class LanguageController extends Controller
{
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

        $getItems = Item::class(new Language)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->get();
        $json = Item::class(new Language)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->toJson();

        $languagesJSON = $getItems['itemsJSON'];
        $languageAll = $getItems['itemAll'];
        $idColMax = $getItems['idColMax'];
        $languages = Language::All();

        if ($ajax) {
            return response()->json($languagesJSON);
        } else {
            return view('back-end.general.index')
                ->with('title_text', '<i class="fa fa-language"></i>')
                ->with('item_type', 'languages')
                ->with('itemsJSON', $languagesJSON)
                ->with('items', $languageAll['items'])->with('idColMax', $idColMax)
                ->with('items_url', '')
                ->with('json', $json);
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
            $orderBy = null;
        } else {
            $orderBy = $request->orderBy;
        }
        if (!property_exists($request, 'orderDir')) {
            $orderDir = null;
        } else {
            $orderDir = $request->orderDir;
        }

        $language = new Language();
        (new GroupRepo)->post($language, $request);
        $getItems = Item::class(new Language)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->get();

        foreach ($getItems['itemAll']['items'] as $languageOne) {
            $languagesJSON['items'][] = $languageOne;
            if ($languageOne->language != null) {
                $languageOne->language->name;
            }
        }
        $languagesJSON['maxCol'] = Language::max('id');
        return response()->json($languagesJSON);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $access = Auth::user()->can('read-general');
        if (!$access) {
            abort(403);
        }
        $id = (int)$id;
        $language = Language::find($id);
        $idColMax = Language::max('id');

        $languageNames=[];
        $languageJson= $language->posts;
        for ($i=0; $i < count($language->posts); $i++) {
            $languageJson[$i]['languages']=$language->posts[$i]->languages;
            for ($j=0; $j < count($language->posts[$i]->languages); $j++) {
                $languageNames[$i][]=$language->posts[$i]->languages[$j]->name;
            }
        }
        return view('back-end.languages.show')
            ->with('language', $language)
            ->with('languageJson', json_encode($languageJson))
            ->with('languageNames', json_encode($languageNames));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $access = Auth::user()->can('update-general');
        if (!$access) {
            abort(403);
        }
        $id = (int)$id;
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
        $language = Language::find($id);
        // $language = Language::find(245);
        if ($language) {
            (new GroupRepo)->post($language, $request);
        }
        $getItems = Item::class(new Language)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->get();
        $languagesJSON = $getItems['itemsJSON'];
        $languages = Language::All();
        if (!$language) {
            return response()->json($languagesJSON, 404);
        }
        if ($ajax) {
            return response()->json($languagesJSON);
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

        $itemNum = Language::count();
        $error = false;
        if ($itemNum > 1) {
            if ($id === "delete") {
                $languages = Language::find($request->ids);
                // $languages = Language::find([52,85674]);
                if (!$languages->isEmpty()) {
                    foreach ($languages as $language) {
                        $posts = Post::where('language_id', '=', $language->id)->get();
                        foreach ($posts as $key => $post) {
                            $post->tags()->detach();
                            $post->categories()->detach();
                            $post->delete();
                        }
                        $language->delete();
                        $tags = Tag::where('language_id', '=', $language->id)->get();
                        $categories = Category::where('language_id', '=', $language->id)->get();
                        if (!($categories->isEmpty())) {
                            foreach ($categories as $key => $category) {
                                $category->delete();
                            }
                        }
                        if (!($tags->isEmpty())) {
                            foreach ($tags as $key => $tag) {
                                $tag->delete();
                            }
                        }
                    }
                }
            } else {
                $id = (int)$id;
                $language=Language::find($id);
                // $language=Language::find(8524);
                
                if ($language) {
                    $language->delete();
                    $posts = Post::where('language_id', '=', $id)->get();
                    $tags = Tag::where('language_id', '=', $id)->get();
                    $categories = Category::where('language_id', '=', $id)->get();
                    if (!($posts->isEmpty())) {
                        foreach ($posts as $key => $post) {
                            $post->tags()->detach();
                            $post->categories()->detach();
                            $post->delete();
                        }
                    }
                    if (!($categories->isEmpty())) {
                        foreach ($categories as $key => $category) {
                            $category->delete();
                        }
                    }
                    if (!($tags->isEmpty())) {
                        foreach ($tags as $key => $tag) {
                            $tag->delete();
                        }
                    }
                }
            }
        } else {
            $error = true;
        }
        $getItems = Item::class(new Language)
            ->orderBy($orderBy)
            ->orderDir($orderDir)
            ->get();
        $languagesJSON = $getItems['itemsJSON'];
        $languages = Language::All();
        $languagesJSON['error_message'] = $error;
        if (isset($languages)) {
            if ($languages->isEmpty()) {
                return response()->json($languagesJSON, 404);
            }
        }
        if (isset($language)) {
            if (empty($language)) {
                return response()->json($languagesJSON, 404);
            }
        }
        if ($ajax) {
            return response()->json($languagesJSON);
        }
    }
}
