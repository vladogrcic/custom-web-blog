<?php

namespace App\Http\Requests\PostRequests;

use Illuminate\Foundation\Http\FormRequest;
use Elegant\Sanitizer\Laravel\SanitizesInput;
use Purifier;
use Auth;

class PostIndexRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $access = Auth::user()->isAbleTo('read-posts');
        // $access = true;
        return $access;
    }

    /**
     *  Validation rules to be applied to the input.
     *
     *  @return void
     */
    public function rules()
    {
        $method = $this->getResourceMethod();

        if ($method==='LOAD') {
            return [];
        }
        return [
            "orderBy" => "string|nullable|max:15",
            "orderDir" => "string|nullable|max:5",
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
        $method = $this->getResourceMethod();
        if ($method==='LOAD') {
            return [];
        }
        return [
            "orderBy" => "trim|strip_tags|cast:string",
            "orderDir" => "trim|strip_tags|cast:string",
            "foreignType" => "trim|strip_tags|cast:string",
            "table" => "trim|strip_tags|cast:string",

            "action" => "trim|strip_tags|cast:string",
            "_token" => "trim|strip_tags|cast:string",
            "_method" => "trim|strip_tags|cast:string"
        ];
    }
    public function getResourceMethod()
    {
        $input = $this->all();
        $post_id = $this->route('id');
        // $test=$this->request->method();

        if (!isset($input['_method'])) {
            $method='OTHER';
        } else {
            if (!$input['_method']&&$this->isMethod('GET')) {
                $method='LOAD';
            } else {
                $method = $input['_method'];
            }
        }

        return $method;
    }
}