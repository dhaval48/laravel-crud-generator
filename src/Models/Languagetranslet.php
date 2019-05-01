<?php

namespace Ongoingcloud\Laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

/**
 * Class Languagetranslet.
 */
class Languagetranslet extends Model
{

    use SoftDeletes;

    protected $table = 'language_translets';

    protected $fillable = [
        	'status',
			'locale',			
        	'created_by',
        ];

    public $formelements = [
        "status" => "",
		"locale" => "",

		"value" => [],
		"translation" => [],
			
			// [ModelArray]
    ];

    public $searchelements = [
        'status',
				'locale',
				
        ];

    public function list_data() {
        return  [
			Lang::get('language_translets.locale') => 'locale',			
        ];
    }

	public function language_translet_details() {
	    return $this->hasMany('Ongoingcloud\Laravelcrud\Models\Languagetransletdetails','languagetranslet_id','id');
	}
	// [Relation]
}
