<?php

namespace Ongoingcloud\Laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

/**
 * Class Setting.
 */
class Setting extends Model
{

    use SoftDeletes;

    protected $table = 'settings';

    protected $fillable = [
        'enable_registration',
				
        'created_by',
        ];

    public $formelements = [
        "enable_registration" => "",
		

         // [ModelArray]
    ];

    public $searchelements = [
        'enable_registration',
				
        ];

    public function list_data() {
        return  [
            Lang::get('settings.enable_registration') => 'enable_registration',
			
        ];
    }


     // [Relation]
}
