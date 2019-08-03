<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CheckUniqueRequest;
use App\Post;
use App\Language;
use App\Category;
use App\Tag;

class UniqueSlugController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }
    public function checkSlug(CheckUniqueRequest $request, $group)
    {
        if ($group==="posts") {
            $output = Post::where('slug', '=', $request->slug)->where('id', '!=', $request->id)->exists();
            return response()->json($output);
        }
        if ($group==="languages") {
            $output = Language::where('slug', '=', $request->slug)->where('id', '!=', $request->id)->exists();
            return response()->json($output);
        }
        if ($group==="categories") {
            $output = Category::where('slug', '=', $request->slug)->where('id', '!=', $request->id)->exists();
            return response()->json($output);
        }
        if ($group==="tags") {
            $output = Tag::where('slug', '=', $request->slug)->where('id', '!=', $request->id)->exists();
            return response()->json($output);
        }
    }
}
