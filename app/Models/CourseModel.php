<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use \Dimsav\Translatable\Translatable;
    
class CourseModel extends Model
{
    use Rememberable;
    use Translatable;
    protected $table                = 'tbl_courses';
    public $translationModel      	= 'App\Models\CourseTranslationModel';
    public $translationForeignKey 	= 'course_id';
    public $translatedAttributes  	= [	 'course_name',
                                         'slug',   
	                                     'locale'
	                                 	 ];
                                     
    protected $fillable   			= [  
                                        
                                        'id'
                                      ];


    public function get_course(){
            return $this->hasOne('App\Models\CourseTranslationModel','course_id','id')->where('locale',\Session::get('locale'));
    }
}
