<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleUserModel extends Model
{
    protected $table = 'tbl_module_user';
    public function get_role()
    {
        return $this->hasOne('App\Models\RoleModel','id','role_id');
    }
    public function get_modules()
    {
        return $this->hasOne('App\Models\ModulesModel','id','module_id');
    }
}
