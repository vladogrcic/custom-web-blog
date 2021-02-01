<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Elegant\Sanitizer\Laravel\SanitizesInput;
use Auth;
use Illuminate\Validation\Rule;
class UserRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $access = Auth::user()->isAbleTo('read-users') || Auth::user()->isAbleTo('update-users');
        return $access;
    }

    /**
     *  Validation rules to be applied to the input.
     *
     *  @return void
     */
    public function rules()
    {
        // * Rule::unique('users')->ignore($this->user) = This was introduced in Laravel 5.7.
        $password_options = $this->input()['password_options'];
        if($password_options === 'manual'){
            return [
                '_method' => 'max:10',
                '_token' => 'required|max:255',

                'display_name' => 'required|max:255|string',
                'roles' => 'required|array|max:255',
                'roles.*' => 'nullable|numeric|max:55',

                'name' => 'required|string|max:255',
                // 'email' => 'required|string|email|max:255|unique:users',
                'email' => ['required', 'string', 'email', 'max:255',
                    Rule::unique('users')->ignore($this->user),
                ],
                'password' => 'required|string|min:6',
                'password_options' => 'required|string|max:10',
            ];
        }
        else{
            return [
                '_method' => 'max:10',
                '_token' => 'required|max:255',

                'display_name' => 'required|max:255|string',
                'roles' => 'required|array|max:255',
                'roles.*' => 'nullable|numeric|max:55',

                'name' => 'required|string|max:255',
                // 'email' => 'required|string|email|max:255|unique:users',
                'email' => ['required', 'string', 'email', 'max:255',
                    Rule::unique('users')->ignore($this->user),
                ],
                'password_options' => 'required|string|max:10',
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
        return [
            '_token' => 'trim|strip_tags|cast:string',
            '_method' => 'trim|strip_tags|cast:string',

            'display_name' => 'trim|strip_tags|cast:string',
            // 'roles' => 'cast:array',
            'roles.*' => 'cast:integer',

            'name' => 'trim|strip_tags|cast:string',
            'email' => 'trim|strip_tags|cast:string',
            'password' => 'trim|strip_tags|cast:string',
            'password_options' => 'trim|strip_tags|cast:string',
        ];
    }
}
