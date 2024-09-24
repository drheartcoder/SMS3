<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeworkStudentModel extends Model
{
    protected $fillable = [
                    		'homework_id',
                            'student_id',
                            'status',
                            'status_changed_by'
                        ];
    protected $table = 'tbl_homework_students';

    public function get_student_details()
    {
    	return $this->hasOne('App\Models\StudentModel','id','student_id');	
    }
}
