<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaimModel extends Model
{
	use SoftDeletes;
    protected $table = "tbl_claims";

    protected $fillable = ['school_id',
                            'student_id',
                            'professor_id',
                            'level_class_id',
                            'student_national_id',
                            'parent_id',
                            'title',
                            'description',
                            'status',
                            'academic_year_id'];


    public function get_parent_details()
    {
        return $this->hasOne('App\Models\UserTranslationModel','user_id','parent_id');
    }
    public function get_professor_details()
    {
        return $this->hasOne('App\Models\UserTranslationModel','user_id','professor_id');
    }
    public function get_student_details()
    {
        return $this->hasOne('App\Models\UserTranslationModel','user_id','student_id');
    }
    public function get_level_class()
    {
        return $this->hasOne('App\Models\LevelClassModel','id','level_class_id');
    }
    public function professor_notifications()
    {
        return $this->hasOne('App\Models\NotificationSettingsModel','user_id','professor_id');
    }
    public function parent_notifications()
    {
        return $this->hasOne('App\Models\NotificationSettingsModel','user_id','parent_id');
    }
    public function get_parent()
    {
        return $this->hasOne('App\Models\UserModel','id','parent_id');
    }
    public function get_professor()
    {
        return $this->hasOne('App\Models\UserModel','id','professor_id');
    }
    public function get_student()
    {
        return $this->hasOne('App\Models\UserModel','id','student_id');
    }
}
