<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYearModel extends Model
{
    protected $table = "tbl_academic_year";

    protected $fillable = ['school_id','academic_year','start_date','end_date'];

    public function get_id()
    {
    	return $this->hasOne('App\Models\AcademicYearModel','start_date')->select('id');
    }
}
