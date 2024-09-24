<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentIllnessModel extends Model
{
    protected $table	=	"tbl_student_illness";
    protected $fillable	=	[
    							'reason_category',
    							'start_date',
    							'end_date',
    							'reason',
    							'parent_id',
    							'kid_id',
                                'level_class_id',
    							'academic_year_id',
    							'school_id'
    						];
    public function get_level_class()
    {
        return $this->hasOne('App\Models\LevelClassModel','id','level_class_id');
    }
    
}
