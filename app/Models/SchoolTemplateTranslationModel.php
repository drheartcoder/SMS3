<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model  as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;


class SchoolTemplateTranslationModel extends Eloquent
{
    use SoftDeletes;
    protected $table	 = 'tbl_school_template_translation'; 

	protected $fillable  = ['school_template_id','title','slug','options','locale']; 
 	 	
}
