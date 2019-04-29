<?php

namespace App\Http\Requests\Permissionmodule;

use Illuminate\Foundation\Http\FormRequest;
use App\General\HandlePermission;

class OnlyPermissionmoduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {        
        return HandlePermission::authorize('only_permissionmodule');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
       
            ];
    }
}
