<?php

namespace ongoingcloud\laravelcrud\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use ongoingcloud\laravelcrud\General\HandlePermission;

class DeleteUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {        
        return HandlePermission::authorize('delete_user');
    }

    /**
     * Get the validation rules that ongoingcloud\laravelcrudly to the request.
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
