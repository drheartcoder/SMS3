<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyMealsModel extends Model
{
    protected $table = "tbl_weekly_meals";

    protected $fillable = [ 'school_id',
                            'item_id',
                            'week_day',
                            'stock'     ];

    public function get_product_details()                      
    {
    	return $this->hasOne('App\Models\CanteenProductsModel','id','item_id');
    }

    public function get_daily_meals()                      
    {
    	return $this->hasMany('App\Models\DailyMealsModel','weekly_meal_id','id');
    }
}
