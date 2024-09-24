<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use \Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusModel extends Model
{
	use SoftDeletes;

    protected $table 				= 'tbl_bus';
    protected $fillable 			= [
			                            'school_id',
			                            'academic_year_id',
			                            'bus_no',
			                            'bus_plate_no',
			                            'bus_capacity',
			                            'driver_id',
			                            'bus_type'
			    					  ];

    public function driver_details()
    {
        return $this->belongsTo('App\Models\UserModel','driver_id','id');
    }

    public function get_fees_details(){
    	return $this->hasOne('App\Models\BusFeesModel','bus_id','id');
    }

    public function get_bus_transports(){
    	return $this->hasMany('App\Models\BusFeesModel','bus_id','id');
    }


}
