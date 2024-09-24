<?php


namespace App\Common\Services;
use App\Models\StudentModel;

use Session;
class StudentService
{
	public function __construct(){
		$this->StudentModel = new StudentModel();
	}

	function get_student_details($student_id){
		$obj_student = $this->StudentModel
							->with('get_user_details','get_level_class.level_details','get_level_class.class_details','get_parent_details')
							->where('id',$student_id)
							->first();

		if(isset($obj_student) && count($obj_student)>0){
			return $obj_student;
		}
		else{
			return FALSE;	
		}
	}

	function get_student_details_by_user_id($student_id){
		$obj_student = $this->StudentModel
							->with('get_user_details')
							->where('user_id',$student_id)
							->where('school_id',Session::get('school_id'))
							->where('academic_year_id',Session::get('academic_year'))
							->where('has_left',0)
							->first();

		if(isset($obj_student) && count($obj_student)>0){
			return $obj_student;
		}
		else{
			return FALSE;	
		}
	}
}