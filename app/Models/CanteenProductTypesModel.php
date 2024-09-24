<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanteenProductTypesModel extends Model
{
    protected $table = "tbl_canteen_product_types";

    protected $fillable = ['school_id','type'];

}
