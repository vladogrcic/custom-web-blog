<?php

namespace App\Http\Requests\PostRequests;

use Illuminate\Foundation\Http\FormRequest;
use Waavi\Sanitizer\Laravel\SanitizesInput;
use Purifier;
use Auth;

class PostUpdateRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $access = Auth::user()->can('update-posts') || Auth::user()->can('create-posts');

        return $access;
    }

    /**
     *  Validation rules to be applied to the input.
     *
     *  @return void
     */
    public function rules()
    {
        return [
            "id" => "required|nullable|numeric",
            "title" => "required|string|max:100",
            "slug" => "required|string|max:100",
            "excerpt" => "string|nullable|max:250",
            "content" => "string|nullable",
            "tagsNAME" => "nullable|array",
            "tagsNAME.*" => "string|nullable|max:100",
            "tags" => "nullable|array",
            "tags.*" => "nullable|numeric|max:100",
            "status" => "numeric|nullable|max:50",
            "authorCustom" => "boolean|nullable",
            "language_id" => "required|nullable|numeric",
            "author_id" => "required|nullable|numeric",
            "categories" => "nullable|array",
            "categories.*" => "nullable|numeric",
            "featured_image" => "string|nullable|max:50",
            
            "action" => "string|nullable|max:100",
            "_token" => "string|nullable|max:100",
            "_method" => "string|nullable|max:100"
        ];
    }

    /**
     *  Filters to be applied to the input.
     *
     *  @return array
     */
    public function filters()
    {
        return [
            "id"=>"cast:integer",
            "title" => "trim|strip_tags|cast:string",
            "slug" => "trim|strip_tags|cast:string|strip",
            "excerpt" => "trim|strip_tags|cast:string",
            "content" => "trim|strip_danger_tags|cast:string",
            "tags" => "array",
            "tags.*" => "cast:integer",
            "tagsNAME" => "array",
            "tagsNAME.*" => "trim|strip_tags|cast:string",
            "status" => "trim|strip_tags|cast:integer",
            "authorCustom" => "trim|strip_tags|cast:boolean",
            "language_id"=>"cast:integer",
            "author_id"=>"cast:integer",
            "categories"=>"array",
            "categories.*"=>"cast:integer",
            "featured_image"=>"trim|strip_tags|cast:string",

            "action" => "trim|strip_tags|cast:string",
            "_token" => "trim|strip_tags|cast:string",
            "_method" => "trim|strip_tags|cast:string"
        ];
    }
}