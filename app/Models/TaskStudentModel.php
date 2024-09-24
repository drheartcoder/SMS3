<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskStudentModel extends Model
{
    protected $table = "tbl_task_students";

    protected $fillable = ['task_id','user_id','status'];

    public function get_user()
    {
    	return $this->hasOne('App\Models\UserModel','id','user_id')->select('id','national_id');
    }
}


