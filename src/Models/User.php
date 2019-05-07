<?php

namespace Ongoingcloud\Laravelcrud\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Lang;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'created_by', 'locale'
    ];

    public $formelements = [
        'name' => '', 
        'email' => '', 
        'password' => '',
        'password_confirmation' => '',
        'role_id' => '',
    ];

    public $searchelements = [
        'name',
        'email'
    ];

    public function list_data() {
        return  [
            Lang::get('users.name') => "name",
            Lang::get('users.email') => "email",
        ];
    }
        
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roles()
    {
        return $this->belongsToMany('Ongoingcloud\Laravelcrud\Models\Role', 'user_roles', 'user_id', 'role_id');
    }

    public function file_upload() {
        return $this->hasMany('Ongoingcloud\Laravelcrud\Models\FileUpload','user_id','id');
    }
}
