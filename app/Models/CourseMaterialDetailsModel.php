<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseMaterialDetailsModel extends Model
{
    protected $table 				= 'tbl_course_material_details';

    protected $fillable 			= [
			                            'course_material_id',
			                            'type',
			                            'path'
			    					  ];
}
