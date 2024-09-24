<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrotherhoodModel extends Model
{

    protected $table 				= 'tbl_brotherhood';
    protected $fillable 			= [
			                            'school_id',
			                            'kid_no',
			                            'discount',
			                            'academic_year_id'
			                            
			    					  ];	
 
}
