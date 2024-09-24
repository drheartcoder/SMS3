<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentModel extends Model
{
	use SoftDeletes;

    protected $table 				= 'tbl_student';
    protected $fillable 			= [
			                            'user_id',
			                            'school_id',
			                            'student_no',
			                            'parent_id',
                                        'previous_level',
			                            'level_class_id',
			                            'parent_national_id',
			                            'brotherhood_id',
			                            'pramoted_on',
			                            'academic_year_id',
                                        'bus_transport',
			                            'pickup_location',
			                            'drop_location',
			                            'admission_no',
			                            'admission_date',
			                            'educational_board',
			                            'is_active',
			                            'has_left',
			                            'relation',
                                        'pickup_address',
                                        'drop_address',
                                        'language'
			    					  ];	

    public function get_parent()
    {
        return $this->hasOne('App\Models\UserTranslationModel','user_id','parent_id');
    }
    public function get_parent_details()
    {
    	return $this->hasOne('App\Models\UserModel','id','parent_id');
    }
    public function get_user_details()
    {
    	return $this->hasOne('App\Models\UserModel','id','user_id');
    }
    public function get_student_details()
    {
        return $this->hasOne('App\Models\UserTranslationModel','user_id','student_id');
    }
    public function get_level_class()
    {
    	return $this->hasOne('App\Models\LevelClassModel','id','level_class_id');
    }
    public function get_educational_board()
    {
    	return $this->hasOne('App\Models\EducationalBoardModel','id','educational_board');
    }

    public function get_attendance()
    {
        return $this->hasMany('App\Models\StudentPeriodAttendanceModel','level_class_id','level_class_id')->select('level_class_id','attendance','period_no');
    }
    
    public function get_student_assigned_bus_stop_details()
    {
        
        return $this->hasOne('App\Models\BusStudentsModel','student_id','user_id')
                    ->with(['bus_details'=>function($q){
                        $q->where('school_id',\Session::get('school_id'));
                        
                    }]);
                    //->whereRaw('((id = "'.\Session::get("bus_id").'" AND type = "'.\Session::get("transport_type").'"))');
                    //->where('id',\Session::get("bus_id"))
                    //->where('type',\Session::get("transport_type"));
    }

    public function get_bus_assigned_to_student()
    {
        return $this->hasOne('App\Models\BusStudentsModel','student_id','user_id')->where('bus_id_fk','=',\Session::get('bus_id'))->where('type','=',\Session::get('transport_type'));
    }

    public function get_documents(){
        return $this->hasMany('App\Models\DocumentsModel','student_id','user_id');
       
    }
    public function get_behaviour(){
        return $this->hasMany('App\Models\StudentBehaviourModel','level_class_id','level_class_id');  
    }

    public function notifications()
    {
        return $this->hasOne('App\Models\NotificationSettingsModel','user_id','user_id')->where('role_id',5);
    }

    public function parent_notifications()
    {
        return $this->hasOne('App\Models\NotificationSettingsModel','user_id','parent_id')->where('role_id',6);
    }
    

}
