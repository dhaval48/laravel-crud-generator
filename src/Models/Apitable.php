<?php

namespace Ongoingcloud\Laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;
use Lang;

/**
 * Class Apitable.
 */
class Apitable extends Model
{
    protected $table = 'api_tables';

    protected $fillable = [
        'apimodule_id',
				'name',
				'type',
				'validation',
				'default',
				
        ];
}
