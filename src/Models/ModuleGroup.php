<?php

namespace ongoingcloud\laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleGroup extends Model
{
    protected $table = 'module_groups';

    protected $fillable = [
        'name', 'description', 'url', 'module_id', 'route', 'order', 'icon',
    ];
    
    public function permissions() {
    	return $this->hasMany('ongoingcloud\laravelcrud\Models\Permission');
    }
}
