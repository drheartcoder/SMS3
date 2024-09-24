<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubModel extends Model
{
    protected $table = 'tbl_club';

    protected $fillable 			= [
			                            'school_id',
			                            'club_no',
			                            'supervisor_id',
			                            'club_name',
			                            'description',
			                            'place',
			                            'club_fee',
			                            'is_free',
			                            'academic_year_id'
			    					  ];

	public function get_supervisor(){
    	return $this->hasOne('App\Models\UserModel','id','supervisor_id')->select('id');
    }

    public function get_students(){
    	return $this->hasMany('App\Models\ClubStudentsModel','club_id','id');
    }
}
