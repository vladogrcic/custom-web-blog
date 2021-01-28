<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Waavi\Sanitizer\Laravel\SanitizesInput;
use Waavi\Sanitizer\Contracts\Filter;
use Waavi\Sanitizer\Sanitizer;

class SettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $access = Auth::user()->isAbleTo('read-settings') || Auth::user()->isAbleTo('update-settings');
        return $access;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // "info.favicon"=>"string|nullable|max:100",
            "info.title"=>"string|nullable|max:100",
            "info.description"=>"string|nullable|max:100",
            "info.site_address"=>"string|nullable|max:100",
            "info.timezone"=>"string|nullable|max:100",
            "info.date_format"=>"string|nullable|max:20",
            "info.time_format"=>"string|nullable|max:20",
            "info.perma_format"=>"string|nullable|max:20",
            "info.disable_language_group"=>"boolean|nullable|max:100",
            "info.per_page"=>"nullable|numeric|max:255",
            "info.show_lang_switch"=>"string|nullable|max:20",
            "info.main_lang"=>"nullable|numeric|max:255",
            "info.blog_slug"=>"string|nullable|max:100",

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
            // "info.favicon" => "trim|strip_tags|cast:string",
            "info.title" => "trim|strip_tags|cast:string",
            "info.description" => "trim|strip_tags|cast:string",
            "info.site_address" => "trim|strip_tags|cast:string",
            "info.timezone" => "trim|strip_tags|cast:string",
            "info.date_format" => "trim|strip_tags|cast:string",
            "info.time_format" => "trim|strip_tags|cast:string",
            "info.perma_format" => "trim|strip_tags|cast:string",
            "info.disable_language_group" => "trim|strip_tags|cast:boolean",
            "info.per_page" => "trim|strip_tags|cast:integer",
            "info.show_lang_switch" => "trim|strip_tags|cast:string",
            "info.main_lang" => "trim|strip_tags|cast:integer",
            "info.blog_slug" => "trim|strip_tags|cast:string",

            "action" => "trim|strip_tags|cast:string",
            "_token" => "trim|strip_tags|cast:string",
            "_method" => "trim|strip_tags|cast:string"
        ];
    }
}
