<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model  as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Dimsav\Translatable\Translatable;


class SchoolTemplateModel extends Eloquent
{
    use SoftDeletes;
	use Translatable;

    protected $table	 = 'tbl_school_template'; 
    public $translationModel = 'App\Models\SchoolTemplateTranslationModel';
    public $translationForeignKey = 'school_template_id';
    public $translatedAttributes = ['title','options','slug','locale'];

	protected $fillable  = ['id','question_category_id','is_required','is_active','validations']; 

    public function get_question_category()
    {
        return $this->hasOne('App\Models\QuestionCategoryModel','id','question_category_id')
                    ->select('id','name','slug');
    }
    public function school_name()
    {
        return $this->hasOne('App\Models\SchoolTemplateTranslationModel','school_template_id','id')->where('locale',\Session::get('locale'))->where('slug','school_name');  
    }
    public function school_address()
    {
        return $this->hasOne('App\Models\SchoolTemplateTranslationModel','school_template_id','id')->where('locale',\Session::get('locale'))->where('slug','school_address');  
    }
    public function school_email()
    {
        return $this->hasOne('App\Models\SchoolTemplateTranslationModel','school_template_id','id')->where('locale',\Session::get('locale'))->where('slug','school_email');  
    }
    public function school_logo()
    {
        return $this->hasOne('App\Models\SchoolTemplateTranslationModel','school_template_id','id')->where('locale',\Session::get('locale'))->where('slug','school_logo'); 
    }
    public function school_latitude()
    {
        return $this->hasOne('App\Models\SchoolTemplateTranslationModel','school_template_id','id')->where('locale',\Session::get('locale'))->where('title','Latitude');  
    }
    public function school_longitude()
    {
        return $this->hasOne('App\Models\SchoolTemplateTranslationModel','school_template_id','id')->where('locale',\Session::get('locale'))->where('title','Longitude');  
    }
}
