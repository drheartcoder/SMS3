<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolTimeTableModel extends Model
{
    protected $table = 'tbl_school_time_table';

    protected $fillable = [
    							'school_id',
                                'level_class_id',
    							'class_id',
    							'level_id',
    							'professor_id',
    							'course_id',
    							'academic_year_id',
    							'day',
    							'periods_no',
    							'level_order',
                                'period_start_time',
                                'period_end_time'
    					];

    public function professor_details()
    {
     	return $this->hasOne('App\Models\ProfessorModel','user_id','professor_id');
    } 
    public function user_details()
    {
    	return $this->hasOne('App\Models\UserModel','id','professor_id');
    }
	public function professor_subjects()
    {
        /*return $this->belongsTo('App\Models\CourseModel','course_id','id');*/
        return $this->hasOne('App\Models\CourseModel','id','course_id');
    }
    public function teaching_hours()
    {
        return $this->hasOne('App\Models\SchoolProfessorTeachingHours','professor_id','professor_id');
    }
    public function notifications()
    {
        return $this->hasOne('App\Models\NotificationSettingsModel','user_id','professor_id')->select('user_id','notification_permission')->where('role_id',4);
    }
    public function level_details()
    {
        return $this->hasOne('App\Models\LevelTranslationModel','level_id','level_id')->where('locale',\Session::get('locale'));
    }
    public function class_details()
    {
        return $this->hasOne('App\Models\ClassTranslationModel','class_id','class_id')->where('locale',\Session::get('locale'));
    } 
    public function course_details()
    {
        return $this->hasOne('App\Models\CourseModel','id','course_id');
    }
   
     
}
