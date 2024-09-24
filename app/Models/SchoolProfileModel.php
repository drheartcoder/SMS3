<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use \Dimsav\Translatable\Translatable;

class SchoolProfileModel extends Model
{
    use Rememberable;
    use Translatable;
    protected $table                = 'tbl_school_profile';
    public $translationModel      	= 'App\Models\SchoolProfileTranslationModel';
    public $translationForeignKey 	= 'school_profile_id';
    public $translatedAttributes  	= [	 'value',
	                                     'locale'
	                                 	 ];

    protected $fillable   			= [  'school_no',
    									 'school_template_id',
                                         'position'];

    public function school_details()
    {
        return $this->hasOne('App\Models\SchoolProfileTranslationModel','school_profile_id','id');
    }

    public function school_template()
    {
        return $this->hasMany('App\Models\SchoolTemplateModel','id','school_template_id');
    }
}
