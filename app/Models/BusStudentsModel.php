<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use \Dimsav\Translatable\Translatable;

class BusStudentsModel extends Model
{
    protected $table 				= 'tbl_bus_students';
    protected $fillable 			= [
			                            'bus_id_fk',
                                        'bus_fees_id',
			                            'route_id_fk',
			                            'stop_id_fk',
			                            'student_id',
                                        'status',
                                        'type',
                                        'pickup_distance',
                                        'drop_distance',
                                        'academic_year_id'
                                        
			    					  ];

    public function bus_details()
    {
        return $this->hasOne('App\Models\BusModel','id','bus_id_fk');
    }

    public function route_details()
    {
        return $this->belongsTo('App\Models\TransportRouteModel','route_id_fk','id');
    }

    public function stop_details()
    {
        return $this->belongsTo('App\Models\TransportRouteStopsModel','stop_id_fk','id');
    }

    public function student_details()
    {
        return $this->hasOne('App\Models\StudentModel','user_id','student_id');
    }
    public function get_fees_transactions(){
        
        return $this->hasMany('App\Models\FeesTransactionModel','school_fees_id','id');
    }
    public function fees_details()
    {
        return $this->hasOne('App\Models\BusFeesModel','id','bus_fees_id');
    }
}
