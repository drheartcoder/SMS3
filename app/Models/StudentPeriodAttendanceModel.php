<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use \Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentPeriodAttendanceModel extends Model
{
	use SoftDeletes;

    protected $table 				= 'tbl_student_period_attendance';
    protected $fillable 			= [
			                            'school_id',
			                            'level_class_id',
			                            'course_id',
			                            'professor_id',
			                            'period_no',
			                            'attendance',
			                            'attendance_date',
			                            'start_time',
			                            'end_time',
			                            'academic_year_id'
			    					  ];

}
