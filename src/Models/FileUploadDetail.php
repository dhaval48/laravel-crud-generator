<?php

namespace Ongoingcloud\Laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Location.
 */
class FileUploadDetail extends Model
{
    protected $table = 'file_upload_details';

    protected $fillable = [
        'file_upload_id','name','path_name',
    ];

}
