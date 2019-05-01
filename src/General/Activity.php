<?php 

namespace Ongoingcloud\Laravelcrud\General;
use Auth;
use Ongoingcloud\Laravelcrud\Models\Activity as Module;

class Activity {
	public static function add($msg, $form, $type_id) {
		
		Module::Create([	
		    		'user_id' => Auth::user()->id,
		    		'message' => $msg,
		    		'type' => $form,
		    		'type_id' => $type_id
				]);
	}
}