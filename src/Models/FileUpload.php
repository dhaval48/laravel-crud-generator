<?php

namespace ongoingcloud\laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Location.
 */
class FileUpload extends Model
{
    protected $table = 'file_uploads';

    protected $fillable = [
        'user_id','type','type_id',
    ];

    public function file_upload_details() {
	    return $this->hasMany('ongoingcloud\laravelcrud\Models\FileUploadDetail','file_upload_id','id');
	}
}
