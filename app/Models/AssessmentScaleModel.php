<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentScaleModel extends Model
{
    protected $table   = 'tbl_assessment_scale';

    protected $fillable     = [ 
    							'course_id', 
                                'school_id',
								'scale',
                                'type'
                              ];
    public function course_name()
    {
    	return $this->hasOne('App\Models\CourseModel','id','course_id');
    }
}
