<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParentModel extends Model
{
	use SoftDeletes;

    protected $table 				= 'tbl_parent';
    protected $fillable 			= [
			                            'user_id',
                                        'parent_no',
			                            'occupation',
			                            'qualification_degree',
			                            'relation',
			                            'marital_status',
			                            'alternate_mobile_no'
                                      ];
			    					  
    public function get_user_details()
    {
    	return $this->hasOne('App\Models\UserModel','id','user_id');
    } 	

    public function user_details()
    {
        return $this->belongsTo('App\Models\UserModel','user_id','id');
    }

    public function school_parent_details()
    {
        return $this->hasOne('App\Models\SchoolParentModel','parent_id','user_id');
    }

    public function notifications()
    {
        return $this->hasOne('App\Models\NotificationSettingsModel','user_id','user_id')->where0('role_id',6);
    }
}
