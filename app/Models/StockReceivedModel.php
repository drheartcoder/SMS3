<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReceivedModel extends Model
{
    protected $table = "tbl_stock_received";

    protected $fillable = ['school_id',
                            'product_name',
                            'product_id',
                            'image',
                            'date_created',
                            'quantity',
                            'available_stock',
                            'price',
                            'total_price',
                            'academic_year_id'];

    public function get_distribution_data()
    {
        return $this->hasMany('App\Models\StockDistributedModel','product_id','id');
    }

}
