<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanteenProductsModel extends Model
{
    protected $table = "tbl_canteen_items";

    protected $fillable = ['school_id',
                            'student_id',
                            'product_id',
                            'product_type',
                            'product_name',
                            'product_image',
                            'description',
                            'price'];

    public function get_product_type()
    {
        return $this->hasOne('App\Models\CanteenProductTypesModel','id','product_type');
    }

    public function get_weekly_meals()                      
    {
        return $this->hasMany('App\Models\WeeklyMealsModel','item_id','id');
    }

}
