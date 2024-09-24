<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamModel extends Model
{
    protected $table = "tbl_exams";
    protected $fillable = [
                            'exam_no',
    						'school_id',
    						'level_class_id',
    						'exam_period_id',
    						'exam_type_id',
    						'assessment_scale_id',
    						'supervisor_id',
    						'course_id',
                            'exam_name',
    						'exam_added_by',
    						'exam_description',
    						'exam_date',
    						'exam_start_time',
    						'exam_end_time',
    						'status',
                            'place_type',
                            'room_assignment_id',
                            'place_name',
                            'building',
                            'floor_no',
                            'room',
                            'academic_year_id'

    						];

    public function get_level_class()
    {
        return $this->hasOne('App\Models\LevelClassModel','id','level_class_id');
    }
    public function get_exam_period()
    {
        return $this->hasOne('App\Models\ExamPeriodModel','id','exam_period_id');
    }
    public function get_exam_type()
    {
        return $this->hasOne('App\Models\ExamTypeModel','id','exam_type_id');
    }
    public function get_assessment()
    {
        return $this->hasOne('App\Models\AssessmentScaleModel','id','assessment_scale_id');
    }
    public function get_supervisor()
    {
        return $this->hasOne('App\Models\UserModel','id','supervisor_id');
    }
    public function get_course()
    {
        return $this->hasOne('App\Models\CourseModel','id','course_id');
    }
    public function exam_added_by()
    {
        return $this->hasOne('App\Models\UserModel','id','exam_added_by');
    }
    public function room_assignment()
    {
        return $this->hasOne('App\Models\RoomAssignmentModel','id','room_assignment_id');
    }
    public function get_academic_year()
    {
        return $this->hasOne('App\Models\AcademicYearModel','id','academic_year_id');   
    }

    public function get_result()
    {
        return $this->hasOne('App\Models\ResultModel','exam_id','id');   
    }
    public function get_school_exam_type()
    {
        return $this->hasOne('App\Models\SchoolExamTypeModel','exam_type_id','exam_type_id');   
    }
    public function get_school_course()
    {
        return $this->hasOne('App\Models\SchoolCourseModel','course_id','course_id');   
    }

}


