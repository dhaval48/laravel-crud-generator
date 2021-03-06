<?php

namespace Ongoingcloud\Laravelcrud\Models;

use Illuminate\Database\Eloquent\Model;
use Lang;

/**
 * Class Role.
 */
class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'name','description','created_by',
    ];

    public $formelements = [
        "name" => "","description" => ""
    ];

    public $searchelements = [
        "name","description"
    ];

    public function list_data() {
        return  [
            Lang::get('roles.name') => 'name',
			Lang::get('roles.description') => 'description'
        ];
    }

    public function users() {
        return $this->belongsToMany('Ongoingcloud\Laravelcrud\User', 'user_roles', 'role_id', 'user_id');
    }
    
    public function permissions() {
        return $this->belongsToMany('Ongoingcloud\Laravelcrud\Models\Permission', 'role_permissions', 'role_id', 'permission_id');
    }

    public function role_permission() {
        return $this->hasMany('Ongoingcloud\Laravelcrud\Models\RoleHasPermission','role_id','id');
    }
}
