<?php

namespace ongoingcloud\laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'modules';

    protected $fillable = [
        'name',
    ];

    public function module_groups() {
    	return $this->hasMany('ongoingcloud\laravelcrud\Models\ModuleGroup');
    }
}
