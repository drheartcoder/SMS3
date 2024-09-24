<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseMaterialModel extends Model
{
    use SoftDeletes;

    protected $table 				= 'tbl_course_material';

    protected $fillable 			= [
    									'id',
			                            'school_id',
			                            'level_class_id',
			                            'material_added_by',
			                            'course_id',
			                            'academic_year_id'
			    					  ];

	public function get_level_class()
    {
        return $this->hasOne('App\Models\LevelClassModel','id','level_class_id');
    }
    public function get_course()
    {
        return $this->hasOne('App\Models\CourseModel','id','course_id');
    }
    public function get_material_details()
    {
        return $this->hasMany('App\Models\CourseMaterialDetailsModel','course_material_id','id');
    }
    public function user_details()
    {
        return $this->hasOne('App\Models\UserModel','id','material_added_by');
    }
   

}
