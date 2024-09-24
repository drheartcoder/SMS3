<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class LevelClassModel extends Model
{
    

    protected $table                = 'tbl_level_school_class';


    protected $fillable = [
                    		'school_id',
                            'level_id',
                            'class_id',
                            'academic_year_id',
                            'position'
                        ];

    public function level_details()
    {
        return $this->hasOne('App\Models\LevelTranslationModel','level_id','level_id')->where('locale',\Session::get('locale'));
    }

    public function class_details()
    {
    	 
        return $this->hasOne('App\Models\ClassTranslationModel','class_id','class_id')->where('locale',\Session::get('locale'));
    }

    public function get_level()
    {
    	return $this->hasOne("App\Models\LevelModel","id","level_id");
    }  

    public function get_class()
    {
        return $this->hasOne('App\Models\ClassModel','id','class_id');
    }

    public function get_periods()
    {
        return $this->hasOne('App\Models\SchoolTimeTableModel','level_class_id','id');
    }                	
}
