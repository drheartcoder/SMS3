<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use \Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeModel extends Model
{
	use SoftDeletes;

    protected $table 				= 'tbl_employee';
    protected $fillable 			= [
			                            'user_id',
			                            'school_id',
			                            'employee_no',
			                            'qualification_degree',
			                            'relation',
			                            'marital_status',
			                            'year_of_experience',
			                            'user_role',
			                            'license_no',
			                            'is_active',
			                            'academic_year_id',
                                        'language'
			    					  ];

    public function user_details()
    {
        return $this->hasOne('App\Models\UserModel','id','user_id');
    }
    
    public function get_user_details()
    {
    	return $this->hasOne("App\Models\UserModel","id","user_id");
    }

    public function notifications()
    {
    	return $this->hasOne("App\Models\NotificationSettingsModel","user_id","user_id")->where('role_id','>',8);
    }

}
