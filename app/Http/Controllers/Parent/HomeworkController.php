<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\HomeworkModel;
use App\Models\HomeworkStudentModel;
use App\Models\LevelClassModel;
use App\Models\LevelTranslationModel;
use App\Models\ClassTranslationModel;
use App\Models\CourseTranslationModel;
use App\Models\StudentModel;

use App\Common\Services\CommonDataService;

use Session;
use Sentinel;
use DB;
use Datatables;
use Validator;
use Flash;

class HomeworkController extends Controller
{
	
    public function __construct(HomeworkModel $homework_model,
                                LevelClassModel $level_class,
                                LevelTranslationModel $level_translation,
                                ClassTranslationModel $class_translation,
                                CourseTranslationModel $course_translation,
                                CommonDataService $common_data_service,
                                HomeworkStudentModel $homework_student,
                                StudentModel $student)
    {
    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.parent_panel_slug')).'/homework';
        $this->module_title                 = translation('homework');
 
        $this->module_view_folder           = "parent.homework";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-book';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->level_class_id                = Session::get('level_class_id');

    	$this->HomeworkModel = $homework_model;
        $this->LevelClassModel    = $level_class;
        $this->LevelTranslationModel = $level_translation;
        $this->ClassTranslationModel = $class_translation;
        $this->CourseTranslationModel = $course_translation;
        $this->CommonDataService      = $common_data_service;
        $this->HomeworkStudentModel   = $homework_student;
        $this->StudentModel           = $student;

    	$this->arr_view_data['page_title']      = translation('homework');
    	$this->arr_view_data['module_url_path'] = $this->module_url_path;
    	$this->arr_view_data['theme_color']     = $this->theme_color;
    	$this->arr_view_data['module_icon']     = $this->module_icon;
    	$this->arr_view_data['edit_icon']       = $this->edit_icon;
    	$this->arr_view_data['create_icon']     = $this->create_icon;

    	$obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;
        }
    }
    /*
    | index() : redirecting to homework listing  
    | Auther        : Pooja K  
    | Date          : 5 Jun 2018
    */
    public function index()
    {
    	
        $this->arr_view_data['module_title']    = translation("manage")." ".$this->module_title;
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*
    | view() : view homework
    | Auther        : Pooja K  
    | Date          : 5 Jun 2018
    */ 
    public function view(Request $request,$enc_id)
    {
        $id = base64_decode($enc_id);

        $obj_homework = $this->HomeworkModel
                                        ->with(['get_course','homework_details'=>function($q){
                                            $q->where('student_id',\Session::get('student_id'));
                                        },'homework_added_by'])
                                        ->where('id',$id)
                                        ->first();

        if($obj_homework)
        {
            $arr_data = $obj_homework->toArray();
        }
        $this->arr_view_data['arr_data'] = $arr_data;
        
        $this->arr_view_data['module_title']    = translation("view")." ".$this->module_title;
        return view($this->module_view_folder.'.view', $this->arr_view_data);
    }

    public function get_events()
    {
        $events ='';
        $obj_events = $this->HomeworkModel
                                        ->with(['get_course','homework_details'=>function($q){
                                            $q->where('student_id',\Session::get('student_id'));
                                        }])
                                        ->where('school_id',$this->school_id)
                                        ->where('level_class_id',$this->level_class_id)
                                        ->where('academic_year_id',$this->academic_year)
                                        ->get();
        if($obj_events)
        {
            $arr_events = $obj_events->toArray();
            $arr_data = [];

            foreach($arr_events as $value)
            {
            
                $start = date_create($value['added_date']);
                $start = date_format($start,'Y-m-d');

                $temp_arr = [];
                $temp_arr['title'] =  $value['get_course']['course_name'].' homework ('.$value['homework_details']['status'] .')';
                $temp_arr['start'] =  $start;
                $temp_arr['allDay'] = true ;
                $temp_arr['id'] =  $value['id'];
                $temp_arr['url'] =  $this->module_url_path.'/view/'.base64_encode($value['id']);
                if($value['homework_details']['status']=="PENDING")
                {
                    $temp_arr['color'] = "#FE413B" ; 
                }
                else if($value['homework_details']['status']=="REJECTED")
                {
                    $temp_arr['color'] = "#2892BC" ;
                }
                else
                {
                    $temp_arr['color'] = "#8CC227" ;
                }
                array_push($arr_data,$temp_arr); 
            
            }
            $events = json_encode($arr_data);
        }                               
        return $events;
    }

}
