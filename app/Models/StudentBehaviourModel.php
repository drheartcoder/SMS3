<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentBehaviourModel extends Model
{
    protected $table    = 'tbl_student_behaviour'; 
    protected $fillable = [
                            'course_id', 
                            'level_class_id',
                            'school_id',
                            'professor_id',
                            'period_id',
                            'behaviour_notation',
                            'behaviour_comments',
                            'week_month',
                            'academic_year_id'
                          ]; 

    public function get_behaviour_period(){
        return $this->hasOne('App\Models\StudentBehaviourPeriodModel','id','period_id');
    }
    public function get_course(){
        return $this->hasOne('App\Models\CourseModel','id','course_id');  
    }
    public function get_level_class()
    {
        return $this->hasOne('App\Models\LevelClassModel','id','level_class_id');
    }


}
