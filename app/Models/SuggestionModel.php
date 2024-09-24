<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuggestionModel extends Model
{

    protected $table 				= 'tbl_suggestions';
    protected $fillable 			= [
			                            'school_id',
			                            'user_id',
			                            'subject',
			                            'description',
			                            'category',
			                            'academic_year_id',
			                            'user_role',
			                            'suggestion_date',
			                            'duration',
			                            'status',
			                            'poll_raised',
			                            'poll_raised_date',
			                            'like_count',
			                            'dislike_count',
			                            'assigned_roles'
			    					  ];

   public function get_polling_details()
   {
   		return $this->hasMany('App\Models\SuggestionPollingModel','suggestion_id','id');
   }

   public function get_category()
   {
   		return $this->hasOne('App\Models\SuggestionCategoriesModel','id','category');
   }


   public function get_user_details()
   {
   		return $this->hasOne('App\Models\UserTranslationModel','user_id','user_id');
   }
     	
}
