<?php

namespace App\Http\Requests\PostRequests;

use Illuminate\Foundation\Http\FormRequest;
use Waavi\Sanitizer\Laravel\SanitizesInput;
use Purifier;
use Auth;

class PostDeleteRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $access = Auth::user()->can('delete-posts');
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
                "id"=>"nullable|numeric",
                "ids"=>"nullable|array",
                "ids.*"=>"nullable|numeric",
                
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
                "ids"=>"array",
                "ids.*"=>"cast:integer",

                "action" => "trim|strip_tags|cast:string",
                "_token" => "trim|strip_tags|cast:string",
                "_method" => "trim|strip_tags|cast:string"
            ];
    }
}
