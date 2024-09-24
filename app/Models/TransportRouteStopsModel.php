<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use \Dimsav\Translatable\Translatable;

class TransportRouteStopsModel extends Model
{
    protected $table 				= 'tbl_transport_route_stops';
    protected $fillable 			= [
			                            'route_id_fk',
			                            'stop_no',
			                            'stop_name',
			                            'landmark',
			                            'stop_lat',
			                            'stop_lang',
			                            'stop_fees',
			                            'stop_radius'
			    					  ];

}
