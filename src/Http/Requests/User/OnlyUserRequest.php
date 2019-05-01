<?php

namespace Ongoingcloud\Laravelcrud\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Ongoingcloud\Laravelcrud\General\HandlePermission;

class OnlyUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {        
        return HandlePermission::authorize('only_user');
    }

    /**
     * Get the validation rules that Ongoingcloud\Laravelcrudly to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        //     'name'  =>'required',
        //     'email' => 'required|email|unique:users,email',
        //    // 'password' => 'required|min:8|confirmed',
        //     'roles'  => 'required'
         ];
    }
}
