<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProfessorTeachingHours extends Model
{

    protected $table 				= 'tb_school_professor_teaching_hours';
    protected $fillable 			= [
			                            'school_id',
			                            'professor_id',
			                            'academic_year_id',
			                            'total_periods',
                                        'assigned_periods'
			                          ];

    public function professor_details()
    {
     	/*return $this->belongsTo('App\Models\ProfessorModel','professor_id','user_id');*/
        return $this->hasOne('App\Models\ProfessorModel','user_id','professor_id');
    } 
	 
    public function user_details()
    {
        /*return $this->belongsTo('App\Models\UserModel','professor_id','id');*/
        return $this->hasOne('App\Models\UserModel','id','professor_id');

    }
    public function professor_subjects()
    {
        return $this->hasOne('App\Models\ProfessorCoursesmodel','professor_id','professor_id');
    }

    public function get_professor_timetable(){
        return $this->hasMany('App\Models\SchoolTimeTableModel','professor_id','professor_id');
    }
}
