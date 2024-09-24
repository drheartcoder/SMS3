<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomAssignmentModel extends Model
{
  	use SoftDeletes;
    protected $table = 'tbl_room_assignement';
 	
    protected $fillable = [
                            'room_management_id',
                            'room_name',
                            'room_no',     
                            'level_class_id'
                               
    					  ];

    public function get_level_class()
    {

    	return $this->hasOne('App\Models\LevelClassModel','id','level_class_id');

    }

    public function get_room_management()
    {
    	return $this->hasOne('App\Models\RoomManagementModel','id','room_management_id');
    }
}
