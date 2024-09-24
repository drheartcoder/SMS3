<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuggestionPollingModel extends Model
{

    protected $table 				= 'tbl_suggestion_polling';
    protected $fillable 			= [
			                            'suggestion_id',
			                            'vote',
			                            'from_user_id'
			    					  ];
	public function user_name()
	{
		return $this->hasMany('App\Models\UserTranslationModel','user_id','from_user_id')->where('locale',\Session::get('locale'));
	}

	public function user_role()
	{
		return $this->hasMany('App\Models\UserRoleModel','user_id','from_user_id');
	}
     	
}
