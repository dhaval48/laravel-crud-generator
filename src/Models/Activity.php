<?php

namespace Ongoingcloud\Laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Location.
 */
class Activity extends Model
{
    protected $table = 'activities';

    protected $fillable = [
        'user_id','message','time','type','type_id',
    ];

    public $formelements = [
        "name" => ""
    ];

    public $searchelements = [
        "name"
    ];

    public $list_data = [
        "Name" => "name"
    ];
}
