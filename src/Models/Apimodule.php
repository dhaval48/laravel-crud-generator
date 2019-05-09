<?php

namespace Ongoingcloud\Laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

/**
 * Class Apimodule.
 */
class Apimodule extends Model
{

    use SoftDeletes;

    protected $table = 'api_modules';

    protected $fillable = [
        		'parent_form',
				'parent_module',
				'main_module',
				'is_model',
				'is_public',
				'table_name',
				'created_by',
        ];

    public $formelements = [
        "parent_form" => "",
		"parent_module" => "",
		"main_module" => "",
		"is_model" => "",
		"is_public" => "",
		"table_name" => "",
		

         "apimodule_id" => [],
			"name" => [],
			"type" => [],
			"validation" => [],
			"default" => [],
			
			// [ModelArray]
    ];

    public $searchelements = [
        'parent_form',
				'parent_module',
				'main_module',
				'is_model',
				'is_public',
				'table_name',
				
        ];

    public function list_data() {
        return  [
            // Lang::get('api_modules.parent_form') => 'parent_form',
			// Lang::get('api_modules.parent_module') => 'parent_module',
			Lang::get('api_modules.main_module') => 'main_module',
			Lang::get('api_modules.is_model') => 'is_model',
			Lang::get('api_modules.is_public') => 'is_public',
			Lang::get('api_modules.table_name') => 'table_name',
			
        ];
    }
     
	public function api_tables() {
	    return $this->hasMany('Ongoingcloud\Laravelcrud\Models\Apitable','apimodule_id','id');
	}
	// [Relation]
}
