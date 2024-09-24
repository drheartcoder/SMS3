<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use \Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamTypeModel extends Model
{
	use Rememberable;
	use Translatable;
	use SoftDeletes;

    protected $table 				= 'tbl_exam_type';
    public $translationModel      	= 'App\Models\ExamTypeTranslationModel';
    public $translationForeignKey 	= 'exam_type_id';
    public $translatedAttributes  	= [	 'exam_type_id',
	                                     'exam_type'];
																																						
    protected $fillable 			= [
			                            'school_id',
			                            'academic_year_id',
			                            'is_active'
			    					  ];

    public function get_exam_type(){
    	return $this->hasOne('App\Models\ExamTypeTranslationModel','exam_type_id','id')->where('locale',\Session::get('locale'));
    } 	
}
