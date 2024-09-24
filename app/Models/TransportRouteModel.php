<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use \Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportRouteModel extends Model
{
	use SoftDeletes;

    protected $table 				= 'tbl_transport_route';
    protected $fillable 			= [
			                            'school_id',
			                            'academic_year_id',
                                        'bus_id_fk',
                                        'transport_type',
			                            'route_name',
			                            'target_location',
			                            'target_location_lat',
			                            'target_location_lang'
			    					  ];

    public function bus_details()
    {
        return $this->belongsTo('App\Models\BusModel','bus_id_fk','id');
    }

    public function route_stop_details()
    {
        return $this->hasMany('App\Models\TransportRouteStopsModel','route_id_fk','id');
    }

    public function student_assigned_to_bus()
    {
        return $this->hasMany('App\Models\BusStudentsModel','route_id_fk','id');
    }
    
}
