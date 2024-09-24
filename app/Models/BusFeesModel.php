<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusFeesModel extends Model
{
	use SoftDeletes;
    protected $table = "tbl_bus_fees";

    protected $fillable = ['bus_id',
                            'transport_type',
                            'fees',
                            'route_name',
                            'academic_year_id'];
   
}
