<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamTypeTranslationModel extends Model
{
	use SoftDeletes;
	
   	protected $table    = 'tbl_exam_type_translation';
    protected $fillable = ['exam_type_id','exam_type','slug','locale'];

    
}
