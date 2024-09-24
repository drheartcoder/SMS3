<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanteenBookingDetailModel extends Model
{
    protected $table = "tbl_canteen_booking_details";

    protected $fillable = ['booking_id',
                            'item_id',
                            'item_name',
                            'price',
                            'quantity',
                            'total_price',
                            'academic_year_id'];

    public function product_details()
    {
    	return $this->hasOne('App\Models\CanteenProductsModel','id','item_id');
    }


}
