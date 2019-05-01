<?php

namespace Ongoingcloud\Laravelcrud\Http\Requests\Apimodule;

use Illuminate\Foundation\Http\FormRequest;
use Ongoingcloud\Laravelcrud\General\HandlePermission;

class DeleteApimoduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {        
        return HandlePermission::authorize('delete_apimodule');
    }

    /**
     * Get the validation rules that Ongoingcloud\Laravelcrudly to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
       
            ];
    }
}
