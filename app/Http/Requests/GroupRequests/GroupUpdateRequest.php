<?php

namespace App\Http\Requests\GroupRequests;

use Illuminate\Foundation\Http\FormRequest;
use Elegant\Sanitizer\Laravel\SanitizesInput;
use Auth;

class GroupUpdateRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $access = Auth::user()->isAbleTo('update-general') || Auth::user()->isAbleTo('create-general');
        return $access;

    }

    /**
     *  Validation rules to be applied to the input.
     *
     *  @return void
     */
    public function rules()
    {
        $input = $this->All();
        if($input['item_type'] == 'languages'){
            return [
                "id" => "required|numeric",
                "name" => "required|string|max:100",
                "slug" => "required|string|max:100",
                "desc" => "string|nullable|max:250",
                "orderBy" => "string|nullable|max:10",
                "orderDir" => "string|nullable|max:10",
                "page"=>"required|nullable|numeric",
                "foreignType" => "string|nullable|max:15",
                "table" => "string|nullable|max:15",
                
                "action" => "string|nullable|max:100",
                "_token" => "string|nullable|max:100",
                "_method" => "string|nullable|max:100"
            ];
        }
        else{
            return [
                "id" => "required|numeric",
                "name" => "required|string|max:100",
                "slug" => "required|string|max:100",
                "desc" => "string|nullable|max:250",
                "language_id"=>"required|nullable|numeric",
                "orderBy" => "string|nullable|max:10",
                "orderDir" => "string|nullable|max:10",
                "page"=>"required|nullable|numeric",
                "foreignType" => "string|nullable|max:15",
                "table" => "string|nullable|max:15",
                
                "action" => "string|nullable|max:100",
                "_token" => "string|nullable|max:100",
                "_method" => "string|nullable|max:100"
            ];
        }
    }

    /**
     *  Filters to be applied to the input.
     *
     *  @return array
     */
    public function filters()
    {
        $input = $this->All();
        if($input['item_type'] == 'languages'){
            return [
                "id" => "cast:integer",
                "name" => "trim|strip_tags|cast:string",
                "slug" => "trim|strip_tags|cast:string|strip",
                "desc" => "trim|strip_tags|cast:string",
                "orderBy" => "trim|strip_tags|cast:string",
                "orderDir" => "trim|strip_tags|cast:string",
                "page"=>"cast:integer",
                "foreignType" => "trim|strip_tags|cast:string",
                "table" => "trim|strip_tags|cast:string",

                "action" => "trim|strip_tags|cast:string",
                "_token" => "trim|strip_tags|cast:string",
                "_method" => "trim|strip_tags|cast:string"
            ];
        }
        else{
            return [
                "id" => "cast:integer",
                "name" => "trim|strip_tags|cast:string",
                "slug" => "trim|strip_tags|cast:string|strip",
                "desc" => "trim|strip_tags|cast:string",
                "orderBy" => "trim|strip_tags|cast:string",
                "orderDir" => "trim|strip_tags|cast:string",
                "language_id"=>"cast:integer",
                "page"=>"cast:integer",
                "foreignType" => "trim|strip_tags|cast:string",
                "table" => "trim|strip_tags|cast:string",

                "action" => "trim|strip_tags|cast:string",
                "_token" => "trim|strip_tags|cast:string",
                "_method" => "trim|strip_tags|cast:string"
            ];
        }
    }
}