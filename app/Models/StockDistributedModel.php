<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockDistributedModel extends Model
{

    protected $table 				= 'tbl_stock_distributed';
    protected $fillable 			= [
			                            'school_id',
			                            'level_id',
			                            'class_id',
			                            'user_role',
			                            'user_id',
			                            'product_id',
			                            'quantity_distributed',
										'distribution_date',
										'academic_year_id'
			    					  ];
 
    public function get_stock()
    {
        return $this->hasOne('App\Models\StockReceivedModel','id','product_id');
    }
}

