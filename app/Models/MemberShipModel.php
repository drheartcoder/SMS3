<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberShipModel extends Model
{	
	use SoftDeletes;

    protected $table 				= 'membership_plans';

    protected $fillable 			= [
			                            'plan_name',
			                            'duration_type',
			                            'duration_value',
			                            'price',
			                            'stackholders',
			                            'is_active'
			    					  ];
}
