<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolRoleModel extends Model
{
    protected $table      = 'tbl_school_roles';
    protected $primaryKey = 'id';

    protected $fillable   = [	
    							'role_id',
    							'school_id',
                                'role_for',
    							'permissions'
    						];

    public function translations()
    {
        return $this->hasOne('App\Models\RoleModel','id','role_id');

    }		

     public function role_details()
    {
        return $this->hasOne('App\Models\RoleModel','id','role_id');
    }				

}
