<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyMealsModel extends Model
{       
    protected $table   =  'tbl_daily_meals';

	protected $fillable = [
                            'school_id',    
                            'weekly_meal_id',
                            'date',
                            'stock',
                            'available_stock',
                            'is_active'
                          ];

   
    public function weekly_meal()
    {
        return $this->belongsTo('App\Models\WeeklyMealsModel','weekly_meal_id','id');
    }

    public function cart_details()
    {
        return $this->hasOne('App\Models\CartModel','id','daily_meal_id');
    }
}
