<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradebookFieldsModel extends Model
{
    protected $table = 'tbl_gradebook_fields';
    protected $fillable = ['type','name','is_active','school_id','default_value1','default_value2','default_value3'];
}
