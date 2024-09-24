<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessorCoursesmodel extends Model
{
    
    protected $table 				= 'tbl_professor_course';
    protected $fillable 			= [
			                            'school_id',
			                            'professor_id',
			                            'academic_year_id',
			                            'course_id',
			                            'levels'
			    					  ];
    
     /*Relationship with class_master table*/
    public function class_from_details()
    {
        return $this->belongsTo('App\Models\CourseModel','class_from','id');
    }
     /*Relationship with class_master table*/
    public function class_to_details()
    {
        return $this->belongsTo('App\Models\CourseModel','class_to','id');
    }

    public function course_details()
    {
        return $this->hasMany('App\Models\CourseModel','id','course_id');
    }
    public function level_from_details()
    {
        return $this->hasOne('App\Models\LevelModel','id','class_from');
    }
    public function level_to_details()
    {
        return $this->hasOne('App\Models\LevelModel','id','class_to');
    }
    public function get_user_details()
    {
        return $this->hasOne("App\Models\UserModel","id","professor_id");
    } 
    /*public function getcourse_idAttribute($value)
    {
        return json_decode($value,TRUE);
    } */
}
