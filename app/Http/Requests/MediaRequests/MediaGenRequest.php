<?php

namespace App\Http\Requests\MediaRequests;

use Illuminate\Foundation\Http\FormRequest;
use Elegant\Sanitizer\Laravel\SanitizesInput;
use Auth;

class MediaGenRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $access = Auth::user()->hasRole('superadmin|testadmin|admin|editor|author|contributor');
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
            "files" => 'nullable|array',
            "files.*" => 'nullable|image|mimes:jpg,jpeg,png,gif,webp',
            "items" => "nullable|array",
            "items.*" => "string|nullable|max:100",
            "itemsFolder" => "nullable|array",
            "itemsFolder.*" => "string|nullable|max:100",
            "name" => "string|nullable|max:100",
            "currentFolder" => "string|nullable|max:100",
            "url" => "string|nullable|max:100",

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
            "files" => 'array',
            // "files.*" => 'nullable|image|mimes:jpg,jpeg,png,gif,webp',
            "items" => "array",
            "items.*" => "trim|strip_tags|cast:string",
            "itemsFolder" => "array",
            "itemsFolder.*" => "trim|strip_tags|cast:string",
            "name" => "trim|strip_tags|cast:string",
            "currentFolder" => "trim|strip_tags|cast:string",
            "url" => "trim|strip_tags|cast:string|strip_tags",

            "action" => "trim|strip_tags|cast:string",
            "_token" => "trim|strip_tags|cast:string",
            "_method" => "trim|strip_tags|cast:string"
        ];

    }
}
