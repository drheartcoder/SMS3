<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskModel extends Model
{
    protected $table = "tbl_tasks";

    protected $fillable = ['school_id','level_class_id','level_id','is_individual','task_name','priority','user_role','task_submission_date','task_submission_time','task_description','task_supervisor_id','task_status','academic_year_id','supervisor_role','added_by','added_by_role'];

    public function get_supervisor()
    {
    	return $this->hasOne('App\Models\UserModel','id','task_supervisor_id')->select('id');
    }
    public function get_level_class()
    {
        return $this->hasOne('App\Models\LevelClassModel','id','level_class_id');
    }
    public function level_details()
    {
        return $this->hasOne('App\Models\LevelTranslationModel','level_id','level_id')->where('locale',\Session::get('locale'));
    }
    public function get_user()
    {
        $obj_data          = \Sentinel::getUser();
        if($obj_data){
            
            $user_id           = $obj_data->id;
        }
        $obj = $this->hasOne('App\Models\TaskStudentModel','task_id','id')->where('user_id',$user_id);
        
        if(\Session::has('kid_id')){
            $obj->orWhere('user_id',\Session::get('kid_id'));
        }
        return $obj;
    }
    public function get_task_users(){
          return $this->hasMany('App\Models\TaskStudentModel','task_id','id');  
    }
    
   
}
