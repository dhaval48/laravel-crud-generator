<?php

namespace Ongoingcloud\Laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;
use Lang;

/**
 * Class Languagetransletdetails.
 */
class Languagetransletdetails extends Model
{
    protected $table = 'language_translet_details';

    protected $fillable = [
        'key',
				'value',
				'translation',
				
        ];
}
