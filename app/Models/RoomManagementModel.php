<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomManagementModel extends Model
{
	use SoftDeletes;
    protected $table = 'tbl_room_management';
 	
    protected $fillable = [
                            'school_id',
                            'tag_name',
                            'floor_no',     
                            'no_of_rooms'     
    					  ];
}
