<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartModel extends Model
{
    protected $table = "tbl_cart_items";

    protected $fillable = [ 'school_id',
    						'daily_meal_id',
    						'user_id',
                            'product_id',
                            'price',
                            'date',
                            'quantity'];

    public function get_product_details()                      
    {
    	return $this->hasOne('App\Models\CanteenProductsModel','id','product_id');
    }

    public function get_daily_meals()
    {
        return $this->hasOne('App\Models\DailyMealsModel','id','daily_meal_id');   
    }

}
