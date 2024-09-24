<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SchoolSubjectsModel extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_school_subjects';
    protected $fillable = [	
    							'school_id', 
    							'academic_year_id',
    							'class_id',
                                'level_id',
    							'json_subjects'
    				];

    public function setJsonSubjectsAttribute($arr_value) 
    {
        $this->attributes['json_subjects'] = json_encode($arr_value);
    }
    public function getJsonSubjectsAttribute()
    {
        return json_decode($this->attributes['json_subjects']);
    }
    public function get_level()
    {
        return $this->hasOne('App\Models\LevelModel','id','level_id');
    }
    public function get_class()
    {
        return $this->hasOne('App\Models\ClassModel','id','class_id');
    }



}
