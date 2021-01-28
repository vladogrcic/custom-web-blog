<?php

namespace App\Http\Requests\GroupRequests;

use Illuminate\Foundation\Http\FormRequest;
use Waavi\Sanitizer\Laravel\SanitizesInput;
use Auth;

class GroupDeleteRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $access = Auth::user()->isAbleTo('delete-general');
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
            "id" => "nullable|numeric",
            "ids"=>"nullable|array",
            "ids.*"=>"nullable|numeric",

            "orderBy" => "string|nullable|max:10",
            "orderDir" => "string|nullable|max:10",
            "foreignType" => "string|nullable|max:15",
            "table" => "string|nullable|max:15",

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
            "id" => "cast:integer",
            "ids"=>"array",
            "ids.*"=>"cast:integer",

            "orderBy" => "trim|strip_tags|cast:string",
            "orderDir" => "trim|strip_tags|cast:string",
            "foreignType" => "trim|strip_tags|cast:string",
            "table" => "trim|strip_tags|cast:string",

            "action" => "trim|strip_tags|cast:string",
            "_token" => "trim|strip_tags|cast:string",
            "_method" => "trim|strip_tags|cast:string"
        ];
    }
}