<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SchoolCourseModel extends Model
{
    use SoftDeletes;
    protected $table   = 'tbl_school_course';

    protected $fillable     = [ 
    							'course_id', 
                                'school_id',
								'coefficient',
                                'school_level_id',
                                'academic_year_id',
                                'is_active'
                              ];
    public function get_course()
    {
        return $this->hasOne('App\Models\CourseModel','id','course_id');
    }
}
