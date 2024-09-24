<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolAdminModel extends Model
{
    use SoftDeletes; 
	protected $table    = 'tbl_school_admin';

    protected $fillable = ['user_id', 'school_id','language','is_active'];


    public function get_school_profile()
    {
        return $this->hasOne('App\Models\SchoolProfileModel','school_no','school_id');
    }

    public function get_user_details()
    {
        return $this->hasOne('App\Models\UserModel','id','user_id');
    }
    public function notification_permissions()
    {
        return $this->hasOne('App\Models\NotificationSettingsModel','user_id','user_id');   
    }
}
