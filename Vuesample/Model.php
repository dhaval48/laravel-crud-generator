<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

/**
 * Class [UNAME].
 */
class [UNAME] extends Model
{

    use SoftDeletes;

    protected $table = '[TABLE_NAME]';

    protected $fillable = [
        [FILLEBLE_LINES]
        'created_by',
        ];

    public $formelements = [
        [FORMELEMENT_LINES]

        // [ModelArray]
    ];

    public $searchelements = [
        [SEARCHELEMENT_LINES]
        ];

    public function list_data() {
        return  [
            [LIST_DATA_LINES]
        ];
    }


    // [Relation]
}
