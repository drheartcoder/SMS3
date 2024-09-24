<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use \Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAttendanceModel extends Model
{
	use SoftDeletes;

    protected $table 				= 'tbl_employee_attendence';
    protected $fillable 			= [
			                            'school_id',
			                            'attendance',
			                            'date',
			                            'academic_year_id',
			                            'user_role'
			    					  ];

}
