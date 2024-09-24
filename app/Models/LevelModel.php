<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use \Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class LevelModel extends Model
{
	use Rememberable;
	use Translatable;
	use SoftDeletes;

    protected $table = 'tbl_level';
 	public $translationModel      	= 'App\Models\LevelTranslationModel';
    public $translationForeignKey 	= 'level_id';
    public $translatedAttributes  	= [	 'level_id',
	                                     'level_name'];
    protected $fillable 			= [
			                            'level_order',
			                            'is_active'
			    					  ];

    public function get_level(){
    	 return $this->hasOne('App\Models\LevelTranslationModel','level_id','id')->where('locale',\Session::get('locale'));
    } 	
}
