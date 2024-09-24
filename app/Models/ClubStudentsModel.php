<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubStudentsModel extends Model
{
    protected $table = 'tbl_club_students';

    protected $fillable 			= [
			                            'club_id',
			                            'student_id',
			                            'level_class_id'
			    					  ];
	public function get_level_class()
    {
        return $this->hasOne('App\Models\LevelClassModel','id','level_class_id');
    }
    public function get_user_details()
    {
        return $this->hasOne('App\Models\UserModel','id','student_id')->select('id');
    }
    public function get_fees_transactions(){
        return $this->hasMany('App\Models\FeesTransactionModel','school_fees_id','id');
    }

    public function get_club(){
        return $this->hasOne('App\Models\ClubModel','id','club_id');
    }
}
