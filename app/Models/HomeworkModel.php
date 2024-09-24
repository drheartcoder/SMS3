<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeworkModel extends Model
{
    protected $fillable = [
                    		'school_id',
                            'level_class_id',
                            'course_id',
                            'homework_added_by',
                            'description',
                            'added_date',
                            'due_date',
                            'academic_year_id'
                        ];
    protected $table = 'tbl_homework';

    public function get_course()
    {
        return $this->hasOne('App\Models\CourseTranslationModel','course_id','course_id')->where('locale',\Session::get('locale'));
    }
    public function homework_details()
    {
        return $this->hasOne('App\Models\HomeworkStudentModel','homework_id','id');
    }
    public function get_level_class()
    {
        return $this->hasOne('App\Models\LevelClassModel','id','level_class_id');
    }
    public function homework_added_by()
    {
       return $this->hasOne('App\Models\UserModel','id','homework_added_by'); 
    }
    public function get_homework_students()
    {
        return $this->hasMany('App\Models\HomeworkStudentModel','homework_id','id');
    }
    
}
