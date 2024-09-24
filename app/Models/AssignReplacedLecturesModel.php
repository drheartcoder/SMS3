<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignReplacedLecturesModel extends Model
{
    protected $table 				= 'tbl_assign_lectures';
    protected $fillable 			= [
			                            'school_id',
                                        'assignment_id',
			                            'level_class_id',
                                        'course_id',
			                            'absent_professor_id',
			                            'absent_professor_no',
			                            'academic_year_id',
			                            'replaced_professor_id',
                                        'replaced_professor_no',
                                        'start_time',
                                        'end_time',
                                        'period_no',
                                        'date',
                                        'day'
			                          ];

    public function level_class_details()
    {
        return $this->hasOne('App\Models\LevelClassModel','id','level_class_id');
    }

    public function professor_details()
    {
        return $this->hasOne('App\Models\UserTranslationModel','user_id','replaced_professor_id');
    }
    public function course_details()
    {
        return $this->hasOne('App\Models\CourseModel','id','course_id');
    }
}
