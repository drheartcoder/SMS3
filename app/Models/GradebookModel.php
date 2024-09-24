<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradebookModel extends Model
{
    protected $table='tbl_gradebook';

    protected $fillable = ['school_id','academic_year_id','exam_period','student_id','comments'];
}
