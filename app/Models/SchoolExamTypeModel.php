<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use \Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolExamTypeModel extends Model
{
    use Rememberable;
	use SoftDeletes;

    protected $table 				= 'tbl_school_exam_type';															
    protected $fillable 			= [
      									'exam_type_id',
			                            'school_id',
			                            'academic_year_id',
			                            'is_active',
                                        'gradebook'
			    					  ];
    
	public function translations()
    {
        return $this->hasMany('App\Models\ExamTypeTranslationModel','exam_type_id','exam_type_id');
    }

    public function get_exam_type()
    {
        return $this->hasOne('App\Models\ExamTypeModel','id','exam_type_id');
    }	    					  
}
