<?php

namespace Ongoingcloud\Laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;
use Lang;

/**
 * Class Moduleinput.
 */
class Moduleinput extends Model
{
    protected $table = 'module_inputs';

    protected $fillable = [
        'formmodule_id',
        		'visible',
				'input_name',
				'db_name',
				'input_type',
				'key',
				'value',
				'table',
				
        ];

    public function form_module() {
	    return $this->belongsTo('Ongoingcloud\Laravelcrud\Models\Formmodule','formmodule_id','id');
	}
}
