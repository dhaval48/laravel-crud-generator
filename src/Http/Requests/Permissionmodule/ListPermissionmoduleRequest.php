<?php

namespace ongoingcloud\laravelcrud\Http\Requests\Permissionmodule;

use Illuminate\Foundation\Http\FormRequest;
use ongoingcloud\laravelcrud\General\HandlePermission;

class ListPermissionmoduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {        
        return HandlePermission::authorize('list_permissionmodule');
    }

    /**
     * Get the validation rules that ongoingcloud\laravelcrudly to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
       
            ];
    }
}
