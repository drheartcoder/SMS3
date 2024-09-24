<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfessorModel extends Model
{
	use SoftDeletes;

    protected $table 				= 'tbl_professor';
    protected $fillable 			= [
			                            'user_id',
			                            'school_id',
			                            'subject_id',
			                            'professor_no',
			                            'year_of_experience',
			                            'qualification_degree',
			                            'joining_date',
										'academic_year_id',
										'status',
			                            'is_active',
			                            'language'
			    					  ];

    public function get_user_details()
    {
    	return $this->hasOne("App\Models\UserModel","id","user_id");
    }
    public function get_user_name()
    {
        return $this->hasOne("App\Models\UserTranslationModel","id","user_id");
    } 
    public function get_course_details()
    {
    	return $this->hasOne("App\Models\ProfessorCoursesmodel","professor_id","user_id");	
    }
    public function notifications()
    {
    	return $this->hasOne("App\Models\NotificationSettingsModel","user_id","user_id")->where('role_id',4);
    }
}

