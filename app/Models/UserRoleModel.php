<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRoleModel extends Model
{

    protected $table = 'role_users';
 	
    protected $fillable = [
                            'user_id',
                            'role_id',
    					  ];

    public function user_role_type_details()
    {
        return $this->belongsTo('App\Models\RoleModel','role_id','id')->select('id','name');
    }

    public function role_details()
    {
        return $this->hasOne('App\Models\RoleModel','id','role_id');
    }
}
