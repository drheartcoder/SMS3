<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolPeriodsModel extends Model
{
    protected $table = 'tbl_school_periods';
	

	protected $fillable = ['school_id','class_id','level_id','academic_year_id','num_of_periods',
	'school_start_time','school_end_time','weekly_off','level_class_id'];


	



 
}
