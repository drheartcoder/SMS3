<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use \Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassModel extends Model
{
	use Rememberable;
	use Translatable;
	use SoftDeletes;

    protected $table = 'tbl_class';
 	public $translationModel      	= 'App\Models\ClassTranslationModel';
    public $translationForeignKey 	= 'class_id';
    public $translatedAttributes  	= [	 'class_id',
	                                     'class_name'];
    protected $fillable 			= [
			                            'id',
			                            'school_id',
			                            'is_active'
			    					  ];

     	
}
