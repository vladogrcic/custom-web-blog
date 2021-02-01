<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Elegant\Sanitizer\Laravel\SanitizesInput;
use Elegant\Sanitizer\Contracts\Filter;
use Elegant\Sanitizer\Sanitizer;

class AvatarRequest extends FormRequest
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
            'file' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
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
            "action" => "trim|strip_tags|cast:string",
            "_token" => "trim|strip_tags|cast:string",
            "_method" => "trim|strip_tags|cast:string"
        ];
    }
}