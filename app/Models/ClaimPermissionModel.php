<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaimPermissionModel extends Model
{
	use SoftDeletes;

    protected $table 				= 'tbl_claim_module_permission';
    protected $fillable 			= [
			                            'school_id',
			                            'academic_year_id',
			                            'module_id',
			                            'is_active'
			    					  ];


}
