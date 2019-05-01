<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class [UNAME].
 */

class [UNAME] extends Model
{

    use SoftDeletes;

    protected $table = '[TABLE_NAME]';

    protected $fillable = [
        [FILLEBLE_LINES]
        ];


    public $searchelements = [
        [SEARCHELEMENT_LINES]
        ];

    // [Relation]
}
