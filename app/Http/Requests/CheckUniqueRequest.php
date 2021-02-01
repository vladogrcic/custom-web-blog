<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Elegant\Sanitizer\Laravel\SanitizesInput;
use Purifier;

class CheckUniqueRequest extends FormRequest
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
     *  Validation rules to be applied to the input.
     *
     *  @return void
     */
    public function rules()
    {
            return [
                "slug"=>"required|string|max:100",
                
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
                "slug"=>"trim|strip_tags|cast:string|strip",

                "action" => "trim|strip_tags|cast:string",
                "_token" => "trim|strip_tags|cast:string",
                "_method" => "trim|strip_tags|cast:string"
            ];
    }
}
