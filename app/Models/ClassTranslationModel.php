<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassTranslationModel extends Model
{
	use SoftDeletes;
	
   	protected $table    = 'tbl_class_translation';
    protected $fillable = ['class_id','class_name','locale'];
}
