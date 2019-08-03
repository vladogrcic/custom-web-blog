<?php

namespace App\Http\Requests\PostRequests;

use Illuminate\Foundation\Http\FormRequest;
use Waavi\Sanitizer\Laravel\SanitizesInput;
use Purifier;
use Auth;

class PostLockerRequest extends FormRequest
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
                "id"=>"required|numeric|max:100",
                
                "action" => "string|nullable|max:100",
                "api_token" => "string|required|max:100",
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
                "id"=>"digit",

                "action" => "trim|strip_tags|cast:string",
                "api_token" => "trim|strip_tags|cast:string",
                "_method" => "trim|strip_tags|cast:string"
            ];
    }
}
