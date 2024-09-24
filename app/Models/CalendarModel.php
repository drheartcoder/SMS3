<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarModel extends Model
{
    protected $table = 'tbl_calender';
    protected $fillable 			= [
			                            'id',
			                            'school_id',
			                            'academic_year_id',
			                            'event_date_from',
			                            'event_date_to',
			                            'event_type',
			                            'event_title',
			                            'event_description',
			                            'academic_year_id',
			                            'all_day',
			                            'user_type',
			                            'level_class_id',
			                            'is_individual',
			                            'exam_id'
			    					  ];
	public function get_level_class(){
		return $this->hasOne('App\Models\LevelClassModel','id','level_class_id');
	}
}
