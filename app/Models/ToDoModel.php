<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToDoModel extends Model
{
 
    protected $table = 'tbl_todo';
    protected $fillable = [
                            'school_id',
                            'level_class_id',
                            'student_id',     
                            'academic_year_id',
                            'todo_description',
                            'status'     
    					  ];
}
