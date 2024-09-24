<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolParentModel extends Model
{
    protected $table = "tbl_school_parent";

    protected $fillable   			= [  'school_id',
    									 'parent_id',
                                         'is_active',
                                         'language'];

    public function user_details()
    {
        return $this->hasOne('App\Models\UserModel','id','parent_id');
    }

    public function get_user_details()
    {
        return $this->hasOne('App\Models\UserModel','id','parent_id');
    }

    public function parent_details()
    {
        return $this->hasOne('App\Models\ParentModel','user_id','parent_id');
    }       
    public function notifications()
    {
        return $this->hasOne('App\Models\NotificationSettingsModel','user_id','parent_id')->where('role_id',6);
    }                              

}
