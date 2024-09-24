<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseTranslationModel extends Model
{
	use SoftDeletes;
	
    protected $fillable = [
                    		'course_id',
                            'course_name',
                            'slug',
                            'locale' ];

    protected $table = 'tbl_course_translation';
}