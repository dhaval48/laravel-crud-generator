<?php

namespace Ongoingcloud\Laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

/**
 * Class Permissionmodule.
 */
class Permissionmodule extends Model
{

    use SoftDeletes;

    protected $table = 'permission_modules';

    protected $fillable = [
                'name',
                'created_by',
				
        ];

    public $formelements = [
        "name" => "",
		

         // [ModelArray]
    ];

    public $searchelements = [
        'name',
				
        ];

    public function list_data() {
        return  [
            Lang::get('permission_modules.name') => 'name',
			
        ];
    }


     // [Relation]
}
