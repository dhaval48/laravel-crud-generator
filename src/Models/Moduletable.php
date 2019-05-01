<?php

namespace Ongoingcloud\Laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;
use Lang;

/**
 * Class Moduletable.
 */
class Moduletable extends Model
{
    protected $table = 'module_tables';

    protected $fillable = [
        'name',
				'type',
				'validation',
				'default',
				'formmodule_id',
				
        ];
}
