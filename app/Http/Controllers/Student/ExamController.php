<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\ExamModel;
use App\Models\ResultModel;
use App\Models\ExamPeriodTranslationModel;
use App\Models\ExamTypeTranslationModel;
use App\Models\CourseTranslationModel;
use App\Models\LevelTranslationModel;
use App\Models\LevelClassModel;
use App\Models\ClassTranslationModel;
use App\Models\AssessmentScaleModel;
use App\Models\StudentModel;

use App\Common\Services\CommonDataService;
use App\Common\Traits\MultiActionTrait;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;

class ExamController extends Controller
{
    public function __construct(
    								
    								CommonDataService $common_data_service
    								
    							)
    {

    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.student_panel_slug')).'/exam';
        $this->module_title                 = translation("exam");
        $this->BaseModel                    = new ExamModel();   
        
        $this->module_view_folder           = "student.exam";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-exam';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->CommonDataService            = $common_data_service;
        $this->academic_year				= Session::get('academic_year');
        $this->level_class_id               = Session::get('level_class_id');
        $this->student_id                   = Session::get('student_id');    
        $this->StudentModel                 = new StudentModel();

        $this->first_name = $this->last_name ='';

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
        	$this->user_id    = $obj_data->id;
        	$this->first_name = $obj_data->first_name;
        	$this->last_name  = $obj_data->last_name;
        	$this->email      = $obj_data->email;
        }

		$this->ExamModel 	              = new ExamModel();
		$this->ExamPeriodTranslationModel = new ExamPeriodTranslationModel();
		$this->ExamTypeTranslationModel   = new ExamTypeTranslationModel();
		$this->CourseTranslationModel     = new CourseTranslationModel();
		$this->LevelTranslationModel      = new LevelTranslationModel();
		$this->ClassTranslationModel      = new ClassTranslationModel();
		$this->LevelClassModel 		      = new LevelClassModel();
		$this->AssessmentScaleModel 	  = new AssessmentScaleModel();
	    $this->ResultModel                = new ResultModel();

   		$this->arr_view_data['page_title']      = translation('exam');
   		$this->arr_view_data['module_title']    = translation('exam');
   		$this->arr_view_data['module_icon']     = 'fa fa-book';
   		$this->arr_view_data['module_url_path'] = $this->module_url_path;
   		$this->arr_view_data['theme_color']     = $this->theme_color;
   		$this->arr_view_data['create_icon']     = 'fa fa-plus-circle';
   		$this->arr_view_data['edit_icon']       = 'fa fa-edit-circle';

   		/*literals*/	
    	$this->str_module_title    = 'module_title';
    	$this->str_module_url_path = 'module_url_path';

    }

    /*
    | index() 		: Redirect to exam list 
    | Auther        : Pooja K 
    | Date          : 29-05-2018
    */ 
    public function index()
    {	
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['page_title']      = str_plural(translation("manage")." ".$this->module_title);
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*
    | get_records() : exam listing using ajax 
    | Auther        : Pooja K  
    | Date          : 29-05-2018
    */ 
    public function get_records(Request $request)
    {
        $obj_custom = $this->get_exam_records($request);

        $role = Sentinel::findRoleBySlug(config('app.project.role_slug.student_role_slug'));
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;

        $json_result  = Datatables::of($obj_custom);

        $json_result = $json_result->blacklist(['id']);                    

  
        $json_result =  $json_result
                        ->editColumn('result',function($data)
                        {
                            $marks = '-';
                            if($data->result!=''){
                                $arr_result= json_decode($data->result,true);

                                if( array_key_exists($this->student_id, $arr_result) )  {
                                    $marks = $arr_result[$this->student_id];

                                }    
                            }
                            
                            return $marks;
                        })
                        ->editColumn('exam_date',function($data)
                        {
                            return getDateFormat($data->exam_date) ;
                        })
                        ->editColumn('exam_time',function($data)
                        {
                            return $data->exam_start_time.' - '.$data->exam_end_time ;
                        })
                        ->editColumn('build_action_btn',function($data) 
                        {
                            $build_view_action = '';
                         
                            $view_href =  $this->module_url_path.'/view/'.base64_encode($data->exam_id);
                            $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>'; 

                            return $build_view_action.'&nbsp;';  
                        })
                        ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);
    }

    /*
    | get_exam_records() : Exam listing using ajax 
    | Auther        : Pooja K  
    | Date          : 29-05-2018
    */
    public function get_exam_records(Request $request)
    {
        $school_id     = $this->school_id;
        $locale = '';

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
                      
  		$exam_table                    = $this->ExamModel->getTable();
  		$exam_period_translation_table = $this->ExamPeriodTranslationModel->getTable();
  		$exam_type_translation_table    = $this->ExamTypeTranslationModel->getTable();
  		$level_table                   = $this->LevelTranslationModel->getTable();	
  		$class_table                   = $this->ClassTranslationModel->getTable();
  		$course_table                  = $this->CourseTranslationModel->getTable();
        $assessment_scale              = $this->AssessmentScaleModel->getTable();   
        $result                        = $this->ResultModel->getTable();   

        $obj_custom = DB::table($exam_table)
                        ->select(DB::raw(   

                                            $exam_table.".id as exam_id,".
                                            $exam_table.".exam_no,".
                                            $exam_table.".exam_start_time,".
                                            $exam_table.".exam_end_time,".
                                            $exam_table.".exam_date,".
                                            $assessment_scale.".scale,".
                                            $exam_period_translation_table.".exam_name,".
                                            $exam_type_translation_table.".exam_type,".
                                            $course_table.".course_name,".
                                            $result.".result,".
                                            $exam_table.".status"
                                        ))
                        				->leftJoin($exam_period_translation_table,$exam_table.'.exam_period_id',' = ',$exam_period_translation_table.'.exam_id')
                        				->leftJoin($exam_type_translation_table,$exam_table.'.exam_type_id',' = ',$exam_type_translation_table.'.exam_type_id')
                        				->leftJoin($course_table,$exam_table.'.course_id',' = ',$course_table.'.course_id')
                                        ->leftJoin($assessment_scale,$exam_table.'.assessment_scale_id',' = ',$assessment_scale.'.id')
                                        ->leftJoin($result,$exam_table.'.id',' = ',$result.'.exam_id')
                                        ->where($exam_period_translation_table.'.locale','=',$locale)
                                        ->where($exam_type_translation_table.'.locale','=',$locale)
                                        ->where($course_table.'.locale','=',$locale)
                                        ->whereNull($exam_table.'.deleted_at')
                                        ->where($exam_table.'.school_id','=',$school_id)
                                        ->where($exam_table.'.status','=','APPROVED')
                                        ->where($exam_table.'.level_class_id','=',$this->level_class_id)
                                        ->orderBy($exam_table.'.created_at','DESC');

        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
            $obj_custom = $obj_custom
                                     ->whereRaw("( (".$exam_period_translation_table.".exam_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$exam_type_translation_table.".exam_type LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$exam_table.".exam_no LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$assessment_scale.".scale LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$course_table.".course_name LIKE '%".$search_term."%') )");
        }
        return $obj_custom ;
    }
    /*
    | view() : view exam
    | Auther        : Pooja K  
    | Date          : 29-05-2018
    */
    public function view($enc_id=FALSE)
    {
        if($enc_id)
        {
            $id = base64_decode($enc_id);    
        }
        else
        {
            return redirect()->back();
        }

        $arr_exam = [];
        $marks ='-';
        $obj_exam = $this->ExamModel
                                ->with('get_result')    
                                ->with(['get_level_class'=>function($q){
                                    $q->with('level_details');
                                    $q->with('class_details');
                                },'get_exam_period','get_exam_type','get_assessment','get_supervisor','get_course','exam_added_by','get_academic_year',
                                  'room_assignment'=>function($q){
                                    $q->with('get_room_management');
                                  }])
                                ->where('id',$id)->first();

        if(!empty($obj_exam))
        {
            $arr_exam = $obj_exam->toArray();

            $arr_result= json_decode($arr_exam['get_result']['result'],true);

            if($arr_result && array_key_exists($this->student_id, $arr_result) )  {
                $marks = $arr_result[$this->student_id];

            }   
        }

        $this->arr_view_data[$this->str_module_title] = translation("view")." ".$this->module_title;
        $this->arr_view_data['arr_data'] = $arr_exam;
        $this->arr_view_data['marks'] = $marks;

        return view($this->module_view_folder.'.view', $this->arr_view_data);
    }
}
