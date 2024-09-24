<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

use Session;
class FeesSchoolModel extends Model
{
    use Rememberable;

    protected $table      = "tbl_fees_school";
    protected $primaryKey = 'id';
    protected $fillable   = ['fees_id','school_id','level_id','frequency','amount','is_optional','is_active','academic_year_id'];

    function get_level()
    {
    	return $this->hasOne('App\Models\LevelModel','id','level_id');
    }
    function get_fees()
    {
    	return $this->hasOne('App\Models\FeesModel','id','fees_id');
    }
    function fees_transaction(){
        return $this->hasMany('App\Models\FeesTransactionModel','school_fees_id','id');   
    }
    function level_exists(){
        return $this->hasOne('App\Models\LevelClassModel','level_id','level_id')->where('school_id',Session::get('school_id'));   
    }
}
