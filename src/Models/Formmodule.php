<?php

namespace Ongoingcloud\Laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

/**
 * Class Formmodule.
 */
class Formmodule extends Model
{

    use SoftDeletes;

    protected $table = 'form_modules';

    protected $fillable = [
        'parent_form',
				'parent_module',
				'main_module',
				'table_name',
				'created_by',
				
        ];

    public $formelements = [
        "parent_form" => "",
		"parent_module" => "",
		"main_module" => "",
		"table_name" => "",
		

         "name" => [],
			"type" => [],
			"validation" => [],
			"default" => [],
			"formmodule_id" => [],
			
			"visible" => [],
			"input_name" => [],
			"db_name" => [],
			"input_type" => [],
			"key" => [],
			"value" => [],
			"table" => [],
			
			// [ModelArray]
    ];

    public $searchelements = [
        'parent_form',
				'parent_module',
				'main_module',
				'table_name',
				
        ];

    public function list_data() {
        return  [
			Lang::get('form_modules.parent_module') => 'parent_module',
			Lang::get('form_modules.main_module') => 'main_module',
			Lang::get('form_modules.table_name') => 'table_name',
        ];
    }

    public function grid_data() {
        return  [
            Lang::get('grid_modules.parent_form') => 'parent_form',
			// Lang::get('form_modules.parent_table') => 'parent_table',
			// Lang::get('form_modules.parent_module') => 'parent_module',
			Lang::get('form_modules.main_module') => 'main_module',
			// Lang::get('form_modules.module_label') => 'module_label',
			Lang::get('form_modules.table_name') => 'table_name',
        ];
    }

     
	public function module_tables() {
	    return $this->hasMany('Ongoingcloud\Laravelcrud\Models\Moduletable','formmodule_id','id');
	}
	
	public function module_inputs() {
	    return $this->hasMany('Ongoingcloud\Laravelcrud\Models\Moduleinput','formmodule_id','id');
	}
	// [Relation]
}
