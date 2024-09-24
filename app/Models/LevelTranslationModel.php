<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LevelTranslationModel extends Model
{
	use SoftDeletes;
	
   	protected $table    = 'tbl_level_translation';
    protected $fillable = ['level_id','level_name','slug','locale'];
}
