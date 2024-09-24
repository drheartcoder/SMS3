<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model  as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Dimsav\Translatable\Translatable;


class NotificationModel extends Eloquent
{
    use SoftDeletes;
    protected $table	 = 'tbl_notifications'; 

	protected $fillable  = ['from_user_id','to_user_id','user_type','notification_type','title','school_id','view_url'];


	public function user_details()
    {
        return $this->hasOne('App\Models\UserModel','id','from_user_id');
    }


    public function to_user_details()
    {
        return $this->hasOne('App\Models\UserModel','id','to_user_id');
    }

    public function role_users()
    {
    	return $this->hasOne('App\Models\UserRoleModel','user_id','from_user_id');	
    }

   
}
