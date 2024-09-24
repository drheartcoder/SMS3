<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model  as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;


class QuestionCategoryModel extends Eloquent
{
    use SoftDeletes;
    protected $table	 = 'tbl_question_category'; 
    protected $fillable = ['name','slug'];
}
