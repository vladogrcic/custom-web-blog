<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Waavi\Sanitizer\Laravel\SanitizesInput;
use Waavi\Sanitizer\Contracts\Filter;
use Waavi\Sanitizer\Sanitizer;

class ProfileRequest extends FormRequest
{
    use SanitizesInput;
     /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "info.gravatar" => "boolean|nullable",
            "info.first_name" => "string|nullable|max:100",
            "info.last_name" => "string|nullable|max:100",
            "info.gender" => "string|nullable|max:10",
            "info.birthday" => "string|nullable|max:10",
            "info.mobile_phone" => "string|nullable|max:25",
            "info.address" => "string|nullable|max:100",
            "info.city" => "string|nullable|max:100",
            "info.country" => "string|nullable|max:100",

            "info.username" => "string|nullable|max:100",
            "info.displayname" => "string|nullable|max:100",
            "info.email" => "string|email|nullable|max:100",
            "info.password" => "string|nullable|max:100",

            "action" => "string|nullable|max:25",
            "_token" => "string|nullable|max:100",
            "_method" => "string|nullable|max:10"
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
            "info.gravatar" => "trim|cast:boolean",
            "info.first_name" => "trim|strip_tags|cast:string",
            "info.last_name" => "trim|strip_tags|cast:string",
            "info.gender" => "trim|strip_tags|cast:string",
            "info.birthday" => "trim|strip_tags|cast:string",
            "info.mobile_phone" => "trim|strip_tags|cast:string",
            "info.address" => "trim|strip_tags|cast:string",
            "info.city" => "trim|strip_tags|cast:string",
            "info.country" => "trim|strip_tags|cast:string",

            "info.username" => "trim|strip_tags|cast:string",
            "info.displayname" => "trim|strip_tags|cast:string",
            "info.email" => "trim|strip_tags|cast:string",
            "info.password" => "trim|strip_tags|cast:string",

            "action" => "trim|strip_tags|cast:string",
            "_token" => "trim|strip_tags|cast:string",
            "_method" => "trim|strip_tags|cast:string"
        ];
    }
}