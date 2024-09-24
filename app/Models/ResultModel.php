<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultModel extends Model
{
    protected $table = "tbl_result";

    protected $fillable =['school_id','level_class_id','added_by','exam_id','academic_year_id','result'];
}
