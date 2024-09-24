<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolPeriodTimingModel extends Model
{
	/* USED TO SAVE THE TIMING REGARDING PERIODS OF THAT PERTICULAR LEVEL CLASS FOR THAT YEAR*/
    protected $table = 'tbl_school_periods_timing';

    protected $fillable = [
                                'school_period_id',
    							'school_id',
    							'level_class_id',
    							'academic_year_id',
    							'period_no',
    							'period_start_time',
    							'period_end_time',
    							'is_break'
    					];

}
