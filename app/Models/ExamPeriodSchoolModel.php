<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use Illuminate\Database\Eloquent\SoftDeletes;
class ExamPeriodSchoolModel extends Model
{
	use SoftDeletes;
    use Rememberable;

    protected $table      = "tbl_exam_period_school";
    protected $primaryKey = 'id';
    protected $fillable   = ['exam_id','school_id','academic_year_id','is_active'];

    public function get_exam_period()
    {
    	return $this->hasOne('App\Models\ExamPeriodModel','id','exam_id');
    }
}
