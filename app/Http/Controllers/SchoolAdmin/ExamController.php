<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\ExamModel;
use App\Models\ExamTypeModel;
use App\Models\SchoolExamTypeModel;
use App\Models\ExamPeriodSchoolModel;
use App\Models\SchoolCourseModel;
use App\Models\ExamPeriodTranslationModel;
use App\Models\ExamTypeTranslationModel;
use App\Models\CourseTranslationModel;
use App\Models\LevelTranslationModel;
use App\Models\LevelClassModel;
use App\Models\ClassTranslationModel;
use App\Models\AssessmentScaleModel;
use App\Models\ProfessorModel;
use App\Models\StudentModel;
use App\Models\RoomManagementModel;
use App\Models\RoomAssignmentModel;
use App\Models\ResultModel;
use App\Common\Services\CommonDataService;
use App\Models\CalendarModel;
use App\Models\SchoolTimeTableModel;
use App\Models\NotificationModel;
use App\Common\Traits\MultiActionTrait;
use App\Common\Services\EmailService;
use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;
class ExamController extends Controller
{

    public function __construct(
    								CommonDataService $common_data_service,
                                    EmailService $EmailService
    							)
    {

    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/exam';
        $this->module_title                 = translation("exam");
        $this->BaseModel                    = new ExamModel();
        
        $this->module_view_folder           = "schooladmin.exam";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-exam';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->CommonDataService            = $common_data_service;
        $this->EmailService                 = $EmailService;
        $this->academic_year				= Session::get('academic_year');
        
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
		$this->ExamTypeModel              = new ExamTypeModel();
		$this->ExamPeriodSchoolModel      = new ExamPeriodSchoolModel();
		$this->ExamPeriodTranslationModel = new ExamPeriodTranslationModel();
		$this->ExamTypeTranslationModel   = new ExamTypeTranslationModel();
		$this->CourseTranslationModel     = new CourseTranslationModel();
		$this->LevelTranslationModel      = new LevelTranslationModel();
		$this->ClassTranslationModel      = new ClassTranslationModel();
		$this->LevelClassModel 		      = new LevelClassModel();
		$this->SchoolExamTypeModel 		  =	new SchoolExamTypeModel();
		$this->SchoolCourseModel 		  = new SchoolCourseModel();
		$this->AssessmentScaleModel 	  = new AssessmentScaleModel();
		$this->ProfessorModel 			  = new ProfessorModel();
        $this->RoomManagementModel        = new RoomManagementModel();
        $this->RoomAssignmentModel        = new RoomAssignmentModel();
        $this->CalendarModel              = new CalendarModel();
        $this->ResultModel                = new ResultModel();
        $this->StudentModel               = new StudentModel();
        $this->SchoolTimeTableModel       = new SchoolTimeTableModel();
        $this->NotificationModel          = new NotificationModel();

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
    | Date          : 21-05-2018
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
    | Date          : 21-05-2018
    */ 
    public function get_records(Request $request)
    {
        $obj_custom = $this->get_exam_records($request);

        $role = Session::get('role');
        $arr_current_user_access = $this->CommonDataService->current_user_access();
       

        $json_result  = Datatables::of($obj_custom);

        $json_result = $json_result->blacklist(['id']);                    

  
        $json_result =  $json_result->editColumn('exam_time',function($data)
                        {
                            return $data->exam_start_time.' - '.$data->exam_end_time ;
                        })
                        ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                        {
                            $build_edit_action = $build_delete_action = $build_view_action = $build_result_action= $build_upload_action='';
                            if($data->status!='PENDING')
                            {    
                                if(array_key_exists('exam.delete',$arr_current_user_access))
                                {    
                                    $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->exam_id);
                                    if($data->exam_added_by==$this->user_id)
                                    {
                                        $build_delete_action = '<a href="'.$delete_href.'" class="red-color" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';     
                                    }
                                    else
                                    {
                                        $build_delete_action = '<a style="position: relative;" class="red-color" href="javascript:void(0)" title="'.translation('access_denied').'" ><i class="fa fa-trash" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                    }
                                }
                                      
                                if(array_key_exists('exam.update',$arr_current_user_access))
                                {
                                    if($data->status!='REJECTED'){
                                        $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->exam_id);
                                        $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';

                                        $exam_date = date_create($data->exam_date);
                                        $today = date_create(date('Y-m-d'));
                                        $diff = date_diff($exam_date, $today);
                                        
                                        if($diff->format('%R%a')>=0 && $data->exam_added_by==$this->user_id)
                                        {
                                            $result_href =  $this->module_url_path.'/result/'.base64_encode($data->exam_id);
                                            $build_result_action = '<a class="light-blue-color" href="'.$result_href.'" title="'.translation('result').'"><i class="fa fa-graduation-cap" ></i></a>';
                                        } 

                                        $build_upload_action = '<a class="lime-color upload-link" onclick="setID(\''.base64_encode($data->exam_id).'\')" data-toggle="modal" data-target="#import_modal" title="'.translation('upload_result_csv').'"><i class="fa fa-upload" ></i></a>'; 

                                    }
                                }
                            }    

                            $view_href =  $this->module_url_path.'/view/'.base64_encode($data->exam_id);
                            $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>'; 

                            if(array_key_exists('exam.delete',$arr_current_user_access) && $build_delete_action==''){
                                $build_delete_action = '<a style="position: relative;" class="red-color" href="javascript:void(0)" title="'.translation('access_denied').'" ><i class="fa fa-trash" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                            }
                            if(array_key_exists('exam.update',$arr_current_user_access) && $build_edit_action==''){
                                $build_edit_action = '<a style="position: relative;" class="orange-color" href="javascript:void(0)" title="'.translation('access_denied').'" ><i class="fa fa-edit" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                            }
                            if(array_key_exists('exam.update',$arr_current_user_access) && $build_result_action==''){
                                $build_result_action = '<a style="position: relative;" class="light-blue-color" href="javascript:void(0)" title="'.translation('access_denied').'" ><i class="fa fa-graduation-cap" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                            }

                            if(array_key_exists('exam.update',$arr_current_user_access) &&  $build_upload_action==''){
                                $build_upload_action = '<a style="position: relative;" class="lime-color" href="javascript:void(0)" title="'.translation('access_denied').'" ><i class="fa fa-upload" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                            }

                            return $build_view_action.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_delete_action.'&nbsp;'.$build_result_action.'&nbsp;'.$build_upload_action;  
                        })
                        ->editColumn('build_checkbox',function($data) use ($arr_current_user_access){
                        	$build_checkbox ='';
                            if(array_key_exists('exam.update',$arr_current_user_access) || array_key_exists('exam.delete',$arr_current_user_access) )
                            {
                                if($data->exam_added_by==$this->user_id)
                                {
                                    $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->exam_id).'" value="'.base64_encode($data->exam_id).'" /><label for="mult_change_'.base64_encode($data->exam_id).'"></label></div>';     
                                }
                                else
                                {
                                    $build_checkbox = '-';
                                }
                            }
                            return $build_checkbox;
                        })
                        ->editColumn('build_status',function($data) use ($arr_current_user_access){
                            $build_status ='';
                            
                                if($data->status=='APPROVED')
                                {
                                    $build_status = '<a class="light-blue-color" style="color:white">&nbsp;'.translation('approved').'&nbsp;</a>';
                                }
                                elseif($data->status=='PENDING') 
                                {
                                    if(array_key_exists('exam.update',$arr_current_user_access)){
                                        $approve =  $this->module_url_path.'/approve/'.base64_encode($data->exam_id);
                                        $build_approve_action = '<a class="orange-color" href="'.$approve.'" title="'.translation('approved').'"><i class="fa fa-check" ></i></a>';

                                        $reject =  $this->module_url_path.'/reject/'.base64_encode($data->exam_id);
                                        $build_reject_action = '<a class="orange-color" href="'.$reject.'" title="'.translation('rejected').'"><i class="fa fa-times" ></i></a>';    

                                        $build_status = $build_approve_action.'&nbsp;'.$build_reject_action; 
                                    }
                                    else{
                                        $build_status = '<a class="lime-color" style="color:white">&nbsp;'.translation('pending').'&nbsp;</a>';
                                    }
                                }
                                else
                                {
                                    $build_status = '<a class="red-color" style="color:white">&nbsp;'.translation('rejected').'&nbsp;&nbsp;&nbsp;&nbsp;</a>';
                                }              
                            
                            return $build_status;
                        })                    
                        ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);
    }

    /*
    | get_exam_records() : Exam listing using ajax 
    | Auther        : Pooja K  
    | Date          : 21-05-2018
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
  		$exam_type_translationtable    = $this->ExamTypeTranslationModel->getTable();
  		$level_class_table             = $this->LevelClassModel->getTable();
  		$level_table                   = $this->LevelTranslationModel->getTable();	
  		$class_table                   = $this->ClassTranslationModel->getTable();
  		$course_table                  = $this->CourseTranslationModel->getTable();

        $obj_custom = DB::table($exam_table)
                        ->select(DB::raw(   

                                            $exam_table.".id as exam_id,".
                                            $exam_table.".exam_no,".
                                            $exam_table.".exam_start_time,".
                                            $exam_table.".exam_end_time,".
                                            $exam_period_translation_table.".exam_name,".
                                            $exam_type_translationtable.".exam_type,".
                                            $level_table.".level_name,".
                                            $class_table.".class_name,".
                                            $course_table.".course_name,".
                                            $exam_table.".status,".
                                            $exam_table.".exam_date,".
                                            $exam_table.".exam_added_by"
                                        ))
                        				->leftJoin($exam_period_translation_table,$exam_table.'.exam_period_id',' = ',$exam_period_translation_table.'.exam_id')
                        				->leftJoin($exam_type_translationtable,$exam_table.'.exam_type_id',' = ',$exam_type_translationtable.'.exam_type_id')
                        				->leftJoin($course_table,$exam_table.'.course_id',' = ',$course_table.'.course_id')
                        				->leftJoin($level_class_table,$exam_table.'.level_class_id',' = ',$level_class_table.'.id')
                        				->leftJoin($level_table,$level_class_table.'.level_id',' = ',$level_table.'.level_id')
                        				->leftJoin($class_table,$level_class_table.'.class_id',' = ',$class_table.'.class_id')
                                        ->where($exam_period_translation_table.'.locale','=',$locale)
                                        ->where($exam_type_translationtable.'.locale','=',$locale)
                                        ->where($level_table.'.locale','=',$locale)
                                        ->where($class_table.'.locale','=',$locale)
                                        ->where($course_table.'.locale','=',$locale)
                                        ->whereNull($exam_table.'.deleted_at')
                                        ->where($exam_table.'.school_id','=',$school_id)
                                        ->where($exam_table.'.academic_year_id','=',$this->academic_year)
                                        ->orderBy($exam_table.'.created_at','DESC');

        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
           $obj_custom = $obj_custom->WhereRaw("( (".$level_table.".level_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$exam_table.".exam_no LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$class_table.".class_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$exam_period_translation_table.".exam_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$exam_type_translationtable.".exam_type LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$course_table.".course_name LIKE '%".$search_term."%') )");
        }
        return $obj_custom ;
    }

    /*
    | create() : create exam
    | Auther        : Pooja K  
    | Date          : 22-05-2018
    */
    public function create()
    {
    	$arr_academic_year = '';
     
    	$academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }    
    	$arr_levels = $arr_exam_period = $arr_exam_type = $arr_course = $arr_professor =[];

    	$obj_levels = $this->CommonDataService->get_levels($this->academic_year);
    	if(!empty($obj_levels))
    	{
			$arr_levels = $obj_levels -> toArray();    		
    	}

    	$obj_exam_period = $this->ExamPeriodSchoolModel
                                            ->whereHas('get_exam_period',function(){})
    										->with('get_exam_period')
    										->where('school_id',$this->school_id)
    										->whereIn('academic_year_id',$arr_academic_year)
    										->where('is_active',1)
    										->get();
    	if(!empty($obj_exam_period))
    	{
    		$arr_exam_period = $obj_exam_period ->toArray();
    	}

    	$obj_exam_type = $this->SchoolExamTypeModel
    										->with('get_exam_type')
    										->where('school_id',$this->school_id)
    										->whereIn('academic_year_id',$arr_academic_year)
    										->where('is_active',1)
    										->get();
    	if(!empty($obj_exam_type))
    	{
    		$arr_exam_type = $obj_exam_type ->toArray();
    	}

    	$obj_course = $this->CommonDataService->get_courses($this->academic_year,config('app.project.school_admin_panel_slug'),$this->user_id);
    	if(!empty($obj_course))
    	{
    		$arr_course = $obj_course ->toArray();
    	}

    	$obj_professor = $this->ProfessorModel
    										->whereHas('get_user_details',function($q){})
                                            ->with(['get_user_details'=>function($q){
                                                $q->select('id','national_id');
                                            }])
    										->where('school_id',$this->school_id)
    										->whereIn('academic_year_id',$arr_academic_year)
    										->where('has_left',0)
    										->where('is_active',1)
    										->get();
    	if(!empty($obj_professor))
    	{
    		$arr_professor = $obj_professor ->toArray();
    	}

        $obj_building = $this->RoomManagementModel
                                            ->where('school_id',$this->school_id)
                                            ->groupBy('tag_name')
                                            ->get();
        if(!empty($obj_building))
        {
            $arr_building = $obj_building ->toArray();
        }

    	$this->arr_view_data[$this->str_module_title] = translation('add').' '.str_plural($this->module_title);
        $this->arr_view_data['page_title']            = $this->module_title;
    	$this->arr_view_data['arr_levels']            = $arr_levels ;
    	$this->arr_view_data['arr_exam_period']       = $arr_exam_period ;
    	$this->arr_view_data['arr_exam_type']         = $arr_exam_type ;
    	$this->arr_view_data['arr_course']            = $arr_course ;
    	$this->arr_view_data['arr_professor']         = $arr_professor ;
        $this->arr_view_data['arr_building']          = $arr_building ;
    
        return view($this->module_view_folder.'.create', $this->arr_view_data);

    }


    /*
    | get_class() : get list of classes 
    | Auther        : Pooja K  
    | Date          : 23-05-2018
    */ 
    public function get_class(Request $request)
    {
    	$level_id = $request->input('level');

    	$options ='';

    	$obj_class = $this->CommonDataService->get_class($level_id);
    
    	if(!empty($obj_class))
    	{
			$arr_class  = $obj_class -> toArray();
			if(count($arr_class)>0)
  			{
                $options .= '<option value="">'.translation('select_class').'</option>';    
  				foreach($arr_class as $value)
  				{
  					$options .= '<option value='.$value['id'];

                    if($request->has('level_class_id'))
                    {
                       
                        if($request->input('level_class_id')==$value['id'])
                        {
                            $options .= ' selected';
                        }
                    }   

                    $options .= '>'.$value['class_details']['class_name'].'</option>';
  				}
  			}	
    	}

    	return $options;
    }

    /*
    | get_assessment_scale() : get assessment scale of course 
    | Auther        : Pooja K  
    | Date          : 23-05-2018
    */ 
    public function get_assessment_scale(Request $request)
    {
    	$course_id = $request->input('course');
    	$options ='';

    	$obj_assessment_scale = $this->AssessmentScaleModel
                                            ->where('school_id',$this->school_id)
                                            ->where('course_id',$course_id)
                                            ->get();
    	if(!empty($obj_assessment_scale))
    	{
    		$arr_assessment_scale = $obj_assessment_scale ->toArray();
    		if(count($arr_assessment_scale)>0)
  			{
                $options .= '<option value="">'.translation('select_assessment_scale').'</option>';
  				foreach($arr_assessment_scale as $value)
  				{
                    $options .= '<option value='.$value['id'];

                    if($request->has('scale'))
                    {
                       
                        if($request->input('scale')==$value['id'])
                        {
                            $options .= ' selected';
                        }
                    }   

                    $options .= '>'.$value['scale'].'</option>';
  				}
  			}
    	}
    	return $options;
    }

    /*
    | get_floor() : get floors of building  
    | Auther        : Pooja K  
    | Date          : 23-05-2018
    */
    public function get_floor(Request $request)
    {
        $building = $request->input('building');

        $options  ='';
        $arr_floors = [];
        $obj_building = $this->RoomManagementModel
                                            ->where('school_id',$this->school_id)
                                            ->where('tag_name',$building)
                                            ->get();
                                            
        if(!empty($obj_building))
        {
            $arr_floors = $obj_building ->toArray();
            
            if(count($arr_floors)>0)
            {
                $options .= '<option value="">'.translation('select_floor').'</option>';
                foreach($arr_floors as $value)
                { 
                    $options .= '<option value='.$value['id'];
                    if($request->has('floor'))
                    {
                        
                        if($request->input('floor')==$value['floor_no'])
                        {
                            $options .= ' selected';
                        }
                    }
                    $options .= '>'.$value['floor_no'].'</option>';
                }
            }
        }

        return $options;
    }

    /*
    | get_rooms() : get rooms on floor  
    | Auther        : Pooja K  
    | Date          : 23-05-2018
    */
    public function get_rooms(Request $request)
    {

        $id = $request->input('id');

        $options ='';

        $obj_building = $this->RoomAssignmentModel
                                            ->where('room_management_id',$id)
                                            ->get();
                                            
        if(!empty($obj_building))
        {
            $arr_floors = $obj_building ->toArray();
            if(count($arr_floors)>0)
            {
                $options .= '<option value="">'.translation('select_room').'</option>';
                foreach($arr_floors as $value)
                {
                    $options .= '<option value='.$value['id'];
                    if($request->has('room'))
                    {
                        if($request->input('room')==$value['room_no'])
                        {
                            $options .= ' selected';
                        }
                    }
                    $options .= '>'.$value['room_name'].'</option>';
                }
            }
        }

        return $options;
    }

    /*
    | store() : store exam
    | Auther        : Pooja K  
    | Date          : 23-05-2018
    */
    public function store(Request $request)
    {
        $messages = $arr_rules = [];
        $date = date('Y-m-d');
        $day_before = date( 'Y-m-d', strtotime( $date . ' -1 day' ) );
        $arr_rules['level']            = 'required|numeric';
        $arr_rules['exam_period']      = 'required|numeric';
        $arr_rules['course']           = 'required|numeric';
        $arr_rules['exam_name']        = 'required|regex:/^[a-zA-Z0-9 \-]+$/';
        $arr_rules['exam_start_time']  = 'required|regex:/^[a-zA-Z0-9 \:]+$/';
        $arr_rules['exam_end_time']    = 'required|regex:/^[a-zA-Z0-9 \:]+$/';
        $arr_rules['supervisor']       = 'required|numeric';
        $arr_rules['class']            = 'required|numeric';
        $arr_rules['exam_type']        = 'required|numeric';
        $arr_rules['assessment_scale'] = 'required|numeric';
        $arr_rules['exam_date']        = 'required|date|after:'.$day_before;
        $arr_rules['exam_description'] = 'required|regex:/^[a-zA-Z0-9 \-]+$/';
        $arr_rules['exam_place']       = ['required','regex:/^(red|green)$/'];

        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'digits_between:10,14' => translation('please_enter_telephone_no_within_range_of_10_-_14'),
                            'digits_between:6,14'  => translation('please_enter_telephone_no_within_range_of_6_-_14'),
                            'required'             => translation('this_field_is_required') 

                        );
        
        $validator                          = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 
        
        $arr_data =[]; 
        $arr_data['school_id'] = $this->school_id;
        $arr_data['academic_year_id'] = $this->academic_year;

        if($request->input('exam_place')=='red')
        {
            $arr_data['place_type'] = 'PREMISES';
            $arr_data['room_assignment_id'] = $request->room;

        }
        else
        {
            $arr_data['place_type'] = 'OTHER';
            $arr_data['place_name'] = trim($request->place);   
            $arr_data['building'] = trim($request->other_building);
            $arr_data['floor_no'] = trim($request->other_floor);
            $arr_data['room'] = trim($request->other_room);
        }
  
        $arr_data['level_class_id']      = $request->class;
        $arr_data['exam_period_id']      = $request->input('exam_period');
        $arr_data['course_id']           = $request->input('course');
        $arr_data['exam_name']           = trim($request->input('exam_name'));
        $arr_data['exam_start_time']     = trim($request->input('exam_start_time'));
        $arr_data['exam_end_time']       = trim($request->input('exam_end_time'));
        $arr_data['supervisor_id']       = trim($request->input('supervisor'));
        $arr_data['exam_type_id']        = trim($request->input('exam_type'));
        $arr_data['assessment_scale_id'] = $request->input('assessment_scale');
        $arr_data['exam_added_by']       = $this->user_id;
        $arr_data['exam_description']    = trim($request->exam_description);

        $date = $request->input('exam_date');
        $date = date_create($date);
        $date = date_format($date,'Y-m-d');

        $arr_data['exam_date'] = $date;

        $arr_data['exam_no'] = $this->generate_exam_number();

        $exam = $this->ExamModel->create($arr_data);

        $arr_calendar = []; 
        $arr_calendar['school_id'] = $this->school_id;
        $arr_calendar['event_type'] = 'EXAM';
        $arr_calendar['level_class_id'] = $request->class;

        $time                            = date_create($request->input('exam_start_time'));
        $time                            = date_format($time,'H:i:s');
        $date_time                       = $date.' '.$time;
        $final_start_time                = date_create($date_time);
        $final_start_time                = date_format($final_start_time,'D M d Y h:i:s');
        $arr_calendar['event_date_from'] = $final_start_time;

        $time                          = date_create($request->input('exam_end_time'));
        $time                          = date_format($time,'H:i:s');
        $date_time                     = $date.' '.$time;
        $final_end_time                = date_create($date_time);
        $final_end_time                = date_format($final_end_time,'D M d Y h:i:s');
        $arr_calendar['event_date_to'] = $final_end_time;

        $arr_calendar['user_type']         = 'professor,parent,student';
        $arr_calendar['event_title']       = trim($request->input('exam_name'));
        $arr_calendar['all_day']           = 0;
        $arr_calendar['event_description'] = trim($request->exam_description);
        $arr_calendar['is_individual']     = '1';
        $arr_calendar['exam_id']           = $exam->id;
        $arr_calendar['academic_year_id']  = $this->academic_year;

        $create_data = $this->CalendarModel->create($arr_calendar);

        if($create_data)
        {
            $data = $this->send_notifications($exam);
            Flash::success(translation('exam_added_successfully'));
            return redirect()->back();    
        }
        
    }

    /*
    | edit() : edit exam
    | Auther        : Pooja K  
    | Date          : 24-05-2018
    */
    public function edit($enc_id=FALSE)
    {
        $enc_id = base64_decode($enc_id);
        if(!is_numeric($enc_id))
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }        

        $arr_exam = [];            
        $obj_exam = $this->ExamModel->where('id',$enc_id)->first();

        if(!empty($obj_exam))
        {
            $arr_exam = $obj_exam->toArray();
        }

        $arr_academic_year = '';
     
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }    
        $arr_levels = $arr_exam_period = $arr_exam_type = $arr_course = $arr_professor =[];

        $obj_levels = $this->CommonDataService->get_levels($this->academic_year);
        if(!empty($obj_levels))
        {
            $arr_levels = $obj_levels -> toArray();         
        }

        $obj_exam_period = $this->ExamPeriodSchoolModel
                                            ->whereHas('get_exam_period',function($q){
                                                $q->where('is_active',1);
                                            })
                                            ->with(['get_exam_period'=>function($q){
                                                $q->where('is_active',1);
                                            }])
                                            ->where('school_id',$this->school_id)
                                            ->whereIn('academic_year_id',$arr_academic_year)
                                            ->where('is_active',1)
                                            ->get();
        if(!empty($obj_exam_period))
        {
            $arr_exam_period = $obj_exam_period ->toArray();
        }

        $obj_exam_type = $this->SchoolExamTypeModel
                                            ->whereHas('get_exam_type',function($q){
                                                $q->where('is_active',1);
                                            })
                                            ->with(['get_exam_type'=>function($q){
                                                $q->where('is_active',1);
                                            }])
                                            ->where('school_id',$this->school_id)
                                            ->whereIn('academic_year_id',$arr_academic_year)
                                            ->where('is_active',1)
                                            ->get();
        if(!empty($obj_exam_type))
        {
            $arr_exam_type = $obj_exam_type ->toArray();
        }

        $level_class = $this->LevelClassModel->where('id',$arr_exam['level_class_id'])->first();
        
        $arr_exam['level'] = isset($level_class->id) ? $level_class->level_id :0;

        $arr_course = $this->CommonDataService->get_courses($this->academic_year,config('app.project.school_admin_panel_slug'),$this->user_id,$level_class->level_id,$level_class->class_id);

        $obj_professor = $this->ProfessorModel
                                            ->whereHas('get_user_details',function(){})
                                            ->with(['get_user_details'=>function($q){
                                                $q->select('id','national_id');
                                            }])
                                            ->where('school_id',$this->school_id)
                                            ->whereIn('academic_year_id',$arr_academic_year)
                                            ->where('has_left',0)
                                            ->where('is_active',1)
                                            ->get();
        if(!empty($obj_professor))
        {
            $arr_professor = $obj_professor ->toArray();
        }

        $obj_building = $this->RoomManagementModel
                                            ->where('school_id',$this->school_id)
                                            ->groupBy('tag_name')
                                            ->get();
        if(!empty($obj_building))
        {
            $arr_building = $obj_building ->toArray();
        }

        $this->arr_view_data[$this->str_module_title] = translation("edit")." ".$this->module_title;
        $this->arr_view_data['arr_levels']            = $arr_levels ;
        $this->arr_view_data['arr_exam_period']       = $arr_exam_period ;
        $this->arr_view_data['arr_exam_type']         = $arr_exam_type ;
        $this->arr_view_data['arr_course']            = $arr_course ;
        $this->arr_view_data['arr_professor']         = $arr_professor ;
        $this->arr_view_data['arr_building']          = $arr_building ;

        $arr_exam['floor']= 0;

        if($arr_exam['room_assignment_id']!=0)
        {
            $building = $this->RoomAssignmentModel->with('get_room_management')->where('id',$arr_exam['room_assignment_id'])->first();
            $arr_exam['building']= $building->get_room_management->tag_name;
            $arr_exam['floor']= $building->get_room_management->floor_no;
            $arr_exam['room']= $building->room_no;                
        }
        
        $exam_date = $arr_exam['exam_date'];
       
        $arr_exam['exam_date'] = $exam_date;

        $this->arr_view_data['arr_exam']              = $arr_exam ;

        return view($this->module_view_folder.'.edit', $this->arr_view_data);
    }

    /*
    | update() : update exam
    | Auther        : Pooja K  
    | Date          : 24-05-2018
    */
    public function update($enc_id=FALSE, Request $request)
    {
        $id = base64_decode($enc_id);
        if(!is_numeric($id))
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }
        $messages = $arr_rules = [];
        $date = date('Y-m-d');
        $day_before = date( 'Y-m-d', strtotime( $date . ' -1 day' ) );
        $arr_rules['level']            = 'required|numeric';
        $arr_rules['exam_period']      = 'required|numeric';
        $arr_rules['course']           = 'required|numeric';
        $arr_rules['exam_name']        = 'required|regex:/^[a-zA-Z0-9 \-]+$/';
        $arr_rules['exam_start_time']  = 'required|regex:/^[a-zA-Z0-9 \:]+$/';
        $arr_rules['exam_end_time']    = 'required|regex:/^[a-zA-Z0-9 \:]+$/';
        $arr_rules['supervisor']       = 'required|numeric';
        $arr_rules['class']            = 'required|numeric';
        $arr_rules['exam_type']        = 'required|numeric';
        $arr_rules['assessment_scale'] = 'required|numeric';
        $arr_rules['exam_date']        = 'required|date|after:'.$day_before;
        $arr_rules['exam_description'] = 'required|regex:/^[a-zA-Z0-9 \-]+$/';
        $arr_rules['exam_place']       = ['required','regex:/^(red|green)$/'];

        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'digits_between:10,14' => translation('please_enter_telephone_no_within_range_of_10_-_14'),
                            'digits_between:6,14'  => translation('please_enter_telephone_no_within_range_of_6_-_14'),
                            'required'             => translation('this_field_is_required') 

                        );
        $validator                          = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 
        
        $arr_data =[]; 
        $arr_data['school_id'] = $this->school_id;
        $arr_data['academic_year_id'] = $this->academic_year;

       
        if($request->input('exam_place')=='red')
        {
            $arr_data['place_type'] = 'PREMISES';
            $arr_data['room_assignment_id'] = $request->room;
        }
         else
        {
            $arr_data['place_type'] = 'OTHER';
            $arr_data['place_name'] = trim($request->place);   
            $arr_data['building'] = trim($request->other_building);
            $arr_data['floor_no'] = trim($request->other_floor);
            $arr_data['room'] = trim($request->other_room);
        }
  
        $arr_data['level_class_id']      = $request->class;
        $arr_data['exam_period_id']      = $request->input('exam_period');
        $arr_data['course_id']           = $request->input('course');
        $arr_data['exam_name']           = $request->input('exam_name');
        $arr_data['exam_start_time']     = $request->input('exam_start_time');
        $arr_data['exam_end_time']       = $request->input('exam_end_time');
        $arr_data['supervisor_id']       = $request->input('supervisor');
        $arr_data['exam_type_id']        = $request->input('exam_type');
        $arr_data['assessment_scale_id'] = $request->input('assessment_scale');
        $arr_data['exam_added_by']       = $this->user_id;
        $arr_data['exam_description']    = trim($request->exam_description);

        $date = $request->input('exam_date');
        $date = date_create($date);
        $date = date_format($date,'Y-m-d');

        $current_date  = date('Y-m-d');
        $arr_data['exam_date'] = $date;

        $exam = $this->ExamModel->where('id',$id)->first();

        $this->ExamModel->where('id',$id)->update($arr_data);

        $previous_date = date($exam->exam_date);

        $nxt_date      = date($date);
        
        if(($previous_date!=$nxt_date) && ($nxt_date>$current_date))
        {
            if(($previous_date<$nxt_date) || ($nxt_date<$previous_date) || (strtotime($exam->exam_start_time)!=strtotime($request->input('exam_start_time'))) || (strtotime($exam->exam_end_time)!=strtotime($request->input('exam_end_time'))))
            {
                $result = $this->send_notifications($exam,'update',$arr_data);
            }
        }

        $arr_calendar = []; 
        $arr_calendar['school_id'] = $this->school_id;
        $arr_calendar['event_type'] = 'EXAM';
        $arr_calendar['level_class_id'] = $request->class;

        $time                            = date_create($request->input('exam_start_time'));
        $time                            = date_format($time,'H:i:s');
        $date_time                       = $date.' '.$time;
        $final_start_time                = date_create($date_time);
        $final_start_time                = date_format($final_start_time,'D M d Y h:i:s');
        $arr_calendar['event_date_from'] = $final_start_time;

        $time                          = date_create($request->input('exam_end_time'));
        $time                          = date_format($time,'H:i:s');
        $date_time                     = $date.' '.$time;
        $final_end_time                = date_create($date_time);
        $final_end_time                = date_format($final_end_time,'D M d Y h:i:s');
        $arr_calendar['event_date_to'] = $final_end_time;

        $arr_calendar['user_type']         = 'professor,parent,student';
        $arr_calendar['event_title']       = trim($request->input('exam_name'));
        $arr_calendar['all_day']           = 0;
        $arr_calendar['event_description'] = trim($request->exam_description);
        $arr_calendar['is_individual']     = '1';

        $this->CalendarModel->where('exam_id',base64_decode($enc_id))->update($arr_calendar);

        Flash::success(translation('exam_updated_successfully'));
        return redirect()->back();
    }

    /*
    | view() : view exam
    | Auther        : Pooja K  
    | Date          : 24-05-2018
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

        $obj_exam = $this->ExamModel
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
        }

        $this->arr_view_data[$this->str_module_title] = translation("view")." ".$this->module_title;
        $this->arr_view_data['arr_data'] = $arr_exam;

        return view($this->module_view_folder.'.view', $this->arr_view_data);
    }
    /*
    | approve() : approve exam
    | Auther        : Pooja K  
    | Date          : 24-05-2018
    */
    public function approve($enc_id = FALSE)
    {
        $id = base64_decode($enc_id);
        $this->ExamModel->where('id',$id)->update(array('status'=>'APPROVED'));
        $exam_details = $this->ExamModel->where('id',$id)->first();
        $exam_details->status = 'APPROVED';
        $exam_details->save();

        $arr_calendar = []; 
        $arr_calendar['school_id'] = $this->school_id;
        $arr_calendar['event_type'] = 'EXAM';
        $arr_calendar['level_class_id'] = $exam_details->level_class_id;

        $date = $exam_details->exam_date;
        $date = date_create($date);
        $date = date_format($date,'Y-m-d');

        $time                            = date_create($exam_details->exam_start_time);
        $time                            = date_format($time,'H:i:s');
        $date_time                       = $date.' '.$time;
        $final_start_time                = date_create($date_time);
        $final_start_time                = date_format($final_start_time,'D M d Y h:i:s');
        $arr_calendar['event_date_from'] = $final_start_time;
        
        $time                          = date_create($exam_details->exam_end_time);
        $time                          = date_format($time,'H:i:s');
        $date_time                     = $date.' '.$time;
        $final_end_time                = date_create($date_time);
        $final_end_time                = date_format($final_end_time,'D M d Y h:i:s');
        $arr_calendar['event_date_to'] = $final_end_time;

        $arr_calendar['user_type']         = 'professor,parent,student';
        $arr_calendar['event_title']       = $exam_details->exam_name;
        $arr_calendar['all_day']           = 0;
        $arr_calendar['event_description'] = $exam_details->exam_description;
        $arr_calendar['is_individual']     = '1';
        $arr_calendar['exam_id']           = $id;
        $arr_calendar['academic_year_id']  = $this->academic_year;

        $this->CalendarModel->create($arr_calendar);

        Flash::success(translation('exam_approved'));
        return redirect()->back();
    }

    /*
    | reject() : reject exam
    | Auther        : Pooja K  
    | Date          : 24-05-2018
    */
    public function reject($enc_id = FALSE)
    {
        $id = base64_decode($enc_id);
        $this->ExamModel->where('id',$id)->update(array('status'=>'REJECTED'));

        Flash::success(translation('exam_rejected'));
        return redirect()->back();
    }

    /*
    | result() : redirecting to result page
    | Auther        : Pooja K  
    | Date          : 12-07-2018
    */
    public function result($enc_id){
        $id = base64_decode($enc_id);
        if(!is_numeric($id)) {
            Flash::error(translation('something_went_wrong'));
            return redirect($this->module_url_path);
        }
        else{

            $obj = $this->ExamModel->with('get_result')->where('id',$id)->first();
            if(isset($obj)  && count($obj)>0){
                $arr_students = [];
                $obj_students = $this->CommonDataService->get_students($obj->level_class_id);
                $arr_students = $obj_students->toArray();
                $arr_result = isset($obj->get_result) && $obj->get_result!=null && $obj->get_result->result!='' ? json_decode($obj->get_result->result,true) : [];
                
                $this->arr_view_data['module_title'] = translation("result");
                $this->arr_view_data['arr_students'] = $arr_students;
                $this->arr_view_data['arr_result'] = $arr_result;
                $this->arr_view_data['exam_id'] = $id;
                return view($this->module_view_folder.'.result', $this->arr_view_data);
            }
            else{

                Flash::error(translation('something_went_wrong'));
                return redirect($this->module_url_path);

            }
        }

    }

    /*
    | multi_action() : multiaction exam
    | Auther        : Pooja K  
    | Date          : 24-05-2018
    */
    public function multi_action(Request $request)
    {
        $arr_rules = array();
        $arr_rules['multi_action'] = "required";
        $arr_rules['checked_record'] = "required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            Flash::error(translation('please_select_record_to_perform_multiaction'));
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $multi_action = $request->input('multi_action');
        $checked_record = $request->input('checked_record');

        /* Check if array is supplied*/
        if(is_array($checked_record) && sizeof($checked_record)<=0)
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction') );
            return redirect()->back();
        }

        foreach ($checked_record as  $record_id) 
        {  
            if($multi_action=="delete")
            {
               $this->perform_delete(base64_decode($record_id));    
               Flash::success($this->module_title.' '.translation('deleted_succesfully')); 
            } 
            else
            {
                Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction') ); 
            }
        }

        return redirect()->back();
    }

    /*
    | delete() : delete exam
    | Auther        : Pooja K  
    | Date          : 30-05-2018
    */    
    public function delete($enc_id = FALSE)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_delete(base64_decode($enc_id)))
        {
            Flash::success($this->module_title.' '.translation('deleted_succesfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction'));
        }

        return redirect()->back();
    }

    public function perform_delete($id)
    {
        $current_date = date('Y-m-d');
        $exam   = $this->BaseModel->where('id',$id)->first();
        $delete = $this->BaseModel->where('id',$id)->delete();
        $delete = $this->CalendarModel->where('exam_id',$id)->delete();
        if($exam->exam_date>$current_date)
        {
            $this->send_notifications($exam,'delete');
        }
        
        if($delete)
        {
            return TRUE;
        }

        return FALSE;
    }

    /*
    | generate_exam_number() : generate exam number
    | Auther        : Pooja K  
    | Date          : 12-07-2018
    */  
    public function generate_exam_number(){

        $today = date('Ymd');
        $new_number = rand(0,9999);
        $new_number = str_pad($new_number,4,'0',STR_PAD_LEFT);

        $exam_no  =   'EXAM'.$today.$new_number; 
        
        $exist = $this->ExamModel->where('exam_no',$exam_no)->first();
        if($exist)
        {
            return $this->generate_exam_number();
        }
        
        return  $exam_no;
    }

    /*
    | store_result() : store result
    | Auther        : Pooja K  
    | Date          : 16-07-2018
    */
    public function store_result($enc_id=FALSE,Request $request){
        $id = base64_decode($enc_id);

        if(is_numeric($id)){

            $exam = $this->ExamModel->with('get_assessment')->where('id',$id)->first();
            
            $scale = isset($exam->get_assessment->scale) ? $exam->get_assessment->scale : 0;

            $marks = $request->marks;
 
            foreach($marks as $key=>$mark){
                $marks[$key] = trim($mark);

                $flag =0;

                if(strstr($scale, '-')){
                   $assessment = explode('-',$scale); 

                   if(!is_numeric($mark)){
                        $marks[$key]=0; 
                        $flag =1;
                   }     
                   else{
                       if($assessment[0]<=$mark && $mark<=$assessment[1]) {

                            $flag =0;
                       }
                       else{

                            $marks[$key]=0; 
                            $flag =1;
                       }
                   }
                }
                else{

                    $assessment = explode(',',$scale); 

                    if(!is_string($mark)){
                        $marks[$key]=0;    
                        $flag =1;
                    }
                    else{
                       if(!in_array($mark,$assessment)){
                            $marks[$key]=0; 
                            $flag =1;
                       }
                   }    
                }

                if(strstr($scale, '-')){
                   if(!is_numeric($mark)){
                     $marks[$key]=0; 
                   }     
                }
                else{
                    if(!is_string($mark)){
                        $marks[$key]=0;    
                    }    
                }
            }
            if(count($marks)>0){

                $arr_data['school_id'] = $this->school_id;
                $arr_data['academic_year_id'] = $this->academic_year;
                $arr_data['result'] = json_encode($marks);
                $arr_data['added_by'] = $this->user_id;
                $arr_data['exam_id'] = $id;

                $this->ResultModel->updateOrCreate(['exam_id'=>$id],$arr_data);

                if($flag ==0)
                    Flash::success(translation('result_added_successfully'));
                else
                    Flash::error(translation('please_enter_valid_marks_as_assessment_scale_is')." ".$scale);    
                return redirect()->back();
            }    
        }
        Flash::error(translation('something_went_wrong'));
        return redirect($this->module_url_path);
    }

    /*
    | download_result() : download result format
    | Auther        : Pooja K  
    | Date          : 16-07-2018
    */
    public function download_doc($format){

        if($format=='xls')
        {
           
            \Excel::create('RESULT-DETAILS', function($excel)
            {
                        $excel->sheet(translation('report_sample'), function($sheet)  
                        {
                            $sheet->setWidth(array(
                                'A'     =>  30,
                                'B'     =>  30
                            ));

                            //setting first 100 rows height to 25
                            $arr_height = array();
                            for ($i=1; $i <=100 ; $i++) 
                            { 
                              $arr_height[$i] = 25;
                            }
                            $sheet->setHeight($arr_height);
                            
                            $sheet->row(1, function($row) {
                               $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '10',
                                'bold'       => true
                                ));
                            });

                            $sheet->row(1,array(
                                                    translation('student_number'),
                                                    translation('result')
                                                    ));

                            $sheet->row(2,array(
                                                    'STUD000111',
                                                    '50'
                                                    ));
                    });                             

            })->export('xls');
        }
    }
    /*
    | upload() : upload result
    | Auther        : Pooja K  
    | Date          : 17-07-2018
    */
    public function upload(Request $request){
       
        if($request->has('exam_id')){
            $id = base64_decode($request->exam_id);
            if(is_numeric($id)){

                $exam_exist = $this->ExamModel->with('get_assessment')->where('id',$id)->first();

                if(isset($exam_exist) && count($exam_exist)>0){

                    $scale = isset($exam_exist->get_assessment->scale) ? $exam_exist->get_assessment->scale : 0;

                    if($request->hasFile('upload_file'))
                    {
                        $file = $request->file('upload_file');
                        $validator = Validator::make(['file'=>$file], ['file'=>'required']);

                        if($validator->fails())
                        {
                            Flash::error(translation('uploaded_file_is_not_valid_file_please_upload_valid_file'));
                            return redirect()->back();
                        }

                        $file_ext = $file->getClientOriginalExtension();

                        if($file_ext!='xlsx' && $file_ext!='xls' && $file_ext!='xltm' && $file_ext!='xltx' && $file_ext!='xlsm')
                        {
                            Flash::error(translation('uploaded_file_is_not_valid_file_please_upload_valid_file'));
                            return redirect()->back();
                        }

                        $results = '';
                        $results = \Excel::load($file, function($reader) {
                                                $reader->formatDates(false);
                                        })->get();

                        $record_inserted = 0;
                        $success_record_count = $error_record_count = $duplicate_record_count =$skipped_record_count = 0;
                        $arr_marks = [];
                        if(isset($results) && sizeof($results)>0)
                        {   
                            foreach($results as $result_key => $file_data)
                            {
                                $assessment =[];
                                if($result_key>=0)
                                {
                                    $data = $file_data->toArray();
                                    
                                    if(isset($data) && sizeof($data)>0)
                                    {

                                            /*-----------------------------------------------
                                            |Check if document is valid or not
                                            -----------------------------------------------*/
                                            $is_vaild_document = $this->is_vaild_document($data);
                                            
                                            if($is_vaild_document==true)
                                            {
                                                
                                                $student_number = isset($data['student_number']) && $data['student_number']!="" ? $data['student_number'] : '';
                                                $result         = isset($data['result']) && $data['result']!="" ? $data['result'] : '';

                                                $exist = $this->StudentModel->where('student_no',$student_number)->where('level_class_id',$exam_exist->level_class_id)->first();
                                                
                                                if(isset($exist) && count($exist)>0){
                                                    
                                                    

                                                    $arr_marks[$exist->id] = trim($result);

                                                    $flag=0;

                                                    if(strstr($scale, '-')){
                                                       $assessment = explode('-',$scale); 

                                                       if(!is_numeric($result)){
                                                            $arr_marks[$exist->id]=0; 
                                                            $flag =1;
                                                       }     
                                                       else{
                                                           if($assessment[0]<=$result && $result<=$assessment[1]) {

                                                                $flag =0;
                                                           }
                                                           else{

                                                                $arr_marks[$exist->id]=0; 
                                                                $flag =1;
                                                           }
                                                       }
                                                    }
                                                    else{

                                                        $assessment = explode(',',$scale); 

                                                        if(!is_string($result)){
                                                            $arr_marks[$exist->id]=0;    
                                                            $flag =1;
                                                        }
                                                        else{
                                                           if(!in_array($result,$assessment)){
                                                                $arr_marks[$exist->id]=0; 
                                                                $flag =1;
                                                           }
                                                       }    
                                                    }
                                                          
                                                    if($flag ==0)
                                                        $success_record_count++;
                                                    else
                                                        $error_record_count++;
                                                }
                                                else{
                                                    $error_record_count++;
                                                }
                                            }
                                            else
                                            {
                                                $skipped_record_count++;
                                            }
                                            /*---------------------------------------------*/
                                    }
                                }
                            }
                          
                            if($success_record_count>0)
                            {
                                $arr_data = [];
                                $arr_data['school_id'] = $this->school_id;
                                $arr_data['academic_year_id'] = $this->academic_year;
                                $arr_data['result'] = json_encode($arr_marks);
                                $arr_data['added_by'] = $this->user_id;
                                $arr_data['exam_id'] = $id;

                                $this->ResultModel->updateOrCreate(['exam_id'=>$id],$arr_data);

                                $error_msg = '';
                                $error_msg .= translation('your_data_imported_successfully');
                                if($duplicate_record_count>0 )
                                {   
                                    $error_msg .= ', '.translation('also_some_records_are_duplicate');
                                    
                                }
                                if( $error_record_count > 0 || $skipped_record_count > 0){
                                    $error_msg .= ' '.translation('some_records_are_skipped');
                                }
                                
                                Flash::success($error_msg);
                            }
                            else 
                            {
                                $error_msg = '';
                                if($error_record_count>0)
                                {
                                    $error_msg .= translation('some_records_are_not_valid');
                                    if($duplicate_record_count>0)
                                    {
                                        $error_msg .= ', '.translation('also_some_records_are_duplicate');
                                    }
                                    $error_msg .= ', '.translation('some_records_are_skipped');
                                }
                                else if($duplicate_record_count>0)
                                {
                                    $error_msg .= translation('some_records_are_duplicate');
                                    $error_msg .= ', '.translation('some_records_are_skipped');
                                }
                                else
                                {
                                    $error_msg = translation('error_while_importing_data');
                                }
                                Flash::error($error_msg);
                            }
                            return redirect($this->module_url_path);
                        }
                        else
                        {
                            Flash::error(translation('error_while_importing_data'));
                        }    
                    }    
                }
                else{
                    Flash::error(translation("something_went_wrong"));    
                }
                
            }
            else{
                Flash::error(translation("something_went_wrong"));
            }
                
        }
        
        return redirect($this->module_url_path);
        
    }
     /*
    | is_vaild_document(): Validate the documents details
    | Auther  : Pooja Kothawade
    | Date    : 17-07-2018
    */
    public function is_vaild_document($data)
    {

        if(isset($data['student_number']) && $data['student_number']!="" &&
           isset($data['result']) && $data['result']!=""
          )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function get_courses(Request $request)
    {
        $data ='';
        $level_class_id = $request->input('class');
       
        $obj_details    = $this->LevelClassModel->where('id',$level_class_id)->first();
        
        $arr_courses  = $this->CommonDataService->get_courses($this->academic_year, config('app.project.school_admin_panel_slug'),$this->user_id, $obj_details->level_id, $obj_details->class_id);
        
        if(isset($arr_courses) && count($arr_courses)>0)
        {
            $data .= '<option value="">'.translation('select_course').'</option>';
            foreach ($arr_courses as $key => $value) {
                $data .= '<option value="';
                $data .= isset($value['id'])?$value['id']:0;
                $data .= '">';
                $data .= isset($value['course_name'])?$value['course_name']:'';
                $data .= '</option>';
            }
            return response()->json(array('status'=>'success','data'=>$data));
        }
        else
        {
            $data .= translation('course_is_not_assigned_for_selected_level_and_class');
            return response()->json(array('status'=>'error','data'=>$data));
        }

    }

    public function send_notifications($exam,$status=FALSE,$details=FALSE)
    {
        
        $result = '';
        $arr_data = [];
        $data = $this->SchoolTimeTableModel
                     ->with('notifications','level_details','class_details','course_details','user_details')
                     ->where('course_id',$exam->course_id)
                     ->where('level_class_id',$exam->level_class_id)
                     ->where('school_id',$this->school_id)
                     ->where('academic_year_id',$this->academic_year)
                     ->first();

        $students = $this->CommonDataService->get_permissions(config('app.project.role_slug.student_role_slug'),$this->academic_year,$this->school_id,$exam->level_class_id);
        
        $supervisor = $this->CommonDataService->get_user_permissions($exam->supervisor_id,config('app.project.role_slug.professor_role_slug'),$this->academic_year);
        
        if(isset($data) && count($data)>0)
        {
            $arr_data = $data->toArray();
        }

        $level_name  =   isset($arr_data['level_details']['level_name'])?$arr_data['level_details']['level_name']:'';
        $class_name  =   isset($arr_data['class_details']['class_name'])?$arr_data['class_details']['class_name']:'';
        $course_name =   isset($arr_data['course_details']['course_name'])?$arr_data['course_details']['course_name']:'';
            
        if(isset($arr_data['notifications']['notification_permission']) && $arr_data['notifications']['notification_permission']!=null)
        {
            $arr_permissions = json_decode($arr_data['notifications']['notification_permission'],true);
            $result = $this->notifications($arr_permissions,$arr_data['notifications']['user_id'],config('app.project.role_slug.professor_role_slug'),$level_name,$class_name,$course_name,$exam,$arr_data['user_details'],$status,$details);
        }

        if(isset($supervisor['notifications']['notification_permission']) && $supervisor['notifications']['notification_permission']!=null)
        {
            $arr_permissions = json_decode($supervisor['notifications']['notification_permission'],true);
            $result = $this->notifications($arr_permissions,$supervisor['user_id'],config('app.project.role_slug.professor_role_slug'),$level_name,$class_name,$course_name,$exam,$supervisor['get_user_details'],$status,$details,'supervisor');
        }
        if(isset($students) && count($students)>0)
        {
            foreach ($students as $key => $value) {
                
                if(isset($value['notifications']['notification_permission']) && $value['notifications']['notification_permission']!=null)
                {
                    $arr_permissions = json_decode($value['notifications']['notification_permission'],true);
                    $result = $this->notifications($arr_permissions,$value['user_id'],config('app.project.role_slug.student_role_slug'),$level_name,$class_name,$course_name,$exam,$value['get_user_details'],$status,$details);
                }
            }
        }
        return $result;
    }

    public function notifications($arr_permissions,$user_id,$role,$level_name,$class_name,$course_name,$exam,$user_details,$status=FALSE,$arr_data=FALSE,$supervisor=FALSE)
    {
        
        if(array_key_exists('exam.app',$arr_permissions))
        {
         
            $arr_notification = [];
            $arr_notification['school_id']          =  $this->school_id;
            $arr_notification['from_user_id']       =  $this->user_id;
            $arr_notification['to_user_id']         =  $user_id;
            $arr_notification['user_type']          =  config('app.project.role_slug.school_admin_role_slug');
            
            if($supervisor !='')
            {
                if($status!='' && $status=='update')
                {
                    $arr_notification['notification_type']  =  'Exam';
                    $arr_notification['title']              =  'Exam :Exam schedule of '.ucwords($exam->exam_name).' for '.$level_name.' '.$class_name.' '.$course_name.' changed from '.getDateFormat($exam->exam_date).' '.$exam->exam_start_time.' - '.$exam->exam_end_time.' to '.getDateFormat($arr_data['exam_date']).' '.$arr_data['exam_start_time'].' - '.$arr_data['exam_end_time'];
                    $arr_notification['view_url']           =  url('/').'/'.$role.'/exam';
                }
                elseif($status!='' && $status=='delete')
                {
                    $arr_notification['notification_type']  =  'Exam';
                    $arr_notification['title']              =  'Exam :'.$exam->exam_name.' of '.$level_name.' '.$class_name.' for '.$course_name.' scheduled on '.$exam->exam_date.' is cancelled for some reason.';
                }
                else
                {
                    $arr_notification['notification_type']  =  'Exam Added';
                    $arr_notification['title']              =  'Exam Added:New exam '.$exam->exam_name.' is scheduled for '.$level_name.' '.$class_name.' '.$course_name.' subject on date '.getDateFormat($exam->exam_date).' '.getTimeFormat($exam->exam_start_time).' - '.getTimeFormat($exam->exam_end_time).' and you are supervisor for that exam.';
                    $arr_notification['view_url']           =  url('/').'/'.$role.'/exam';
                }
                
            }
            else
            {
                if($status!='' && $status=='update')
                {
                    $arr_notification['notification_type']  =  'Exam';
                    $arr_notification['title']              =  'Exam :Exam schedule of '.ucwords($exam->exam_name).' for '.$level_name.' '.$class_name.' '.$course_name.' changed from '.getDateFormat($exam->exam_date).' '.$exam->exam_start_time.' - '.$exam->exam_end_time.' to '.getDateFormat($arr_data['exam_date']).' '.$arr_data['exam_start_time'].' - '.$arr_data['exam_end_time'];
                    $arr_notification['view_url']           =  url('/').'/'.$role.'/exam';
                }
                elseif($status!='' && $status=='delete')
                {
                    $arr_notification['notification_type']  =  'Exam';
                    $arr_notification['title']              =  'Exam :'.$exam->exam_name.' of '.$level_name.' '.$class_name.' for '.$course_name.' scheduled on '.$exam->exam_date.' is cancelled for some reason.';
                }
                else
                {
                    $arr_notification['notification_type']  =  'Exam Added';
                    $arr_notification['title']              =  'Exam Added:New exam '.$exam->exam_name.' is scheduled for '.$level_name.' '.$class_name.' '.$course_name.' subject on date '.getDateFormat($exam->exam_date).' '.getTimeFormat($exam->exam_start_time).' - '.getTimeFormat($exam->exam_end_time);
                    $arr_notification['view_url']           =  url('/').'/'.$role.'/exam';    
                }
                
            }

            $result = $this->NotificationModel->create($arr_notification);
        }

        $details          = [
                                    'first_name'  =>  isset($user_details['first_name'])?ucwords($user_details['first_name']):'',
                                    'course_name' =>  isset($course_name)?ucwords($course_name):'',
                                    'level'       =>  isset($level_name)?$level_name:'',
                                    'class'       =>  isset($class_name)?$class_name:'',
                                    'email'       =>  isset($user_details['email'])?$user_details['email']:'',
                                    'mobile_no'   =>  isset($user_details['mobile_no'])?$user_details['mobile_no']:'',
                                    'exam_name'   =>  isset($exam->exam_name)?ucwords($exam->exam_name):'',
                                    'start_time'  =>  isset($exam->exam_start_time)?getTimeFormat($exam->exam_start_time):'',
                                    'end_time'    =>  isset($exam->exam_end_time)?getTimeFormat($exam->exam_end_time):'',
                                    'exam_date'   =>  isset($exam->exam_date)?getDateFormat($exam->exam_date):''
                            ];
        if(array_key_exists('exam.sms',$arr_permissions))
        {
            if($supervisor!='')
            {
                $arr_sms_data = $this->built_sms_data($details,$status,'supervisor',$arr_data);
            }
            else
            {
                $arr_sms_data = $this->built_sms_data($details,$status,'',$arr_data);
            }
            $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
        }
        if (array_key_exists('exam.email',$arr_permissions))
        {
            if($supervisor!='')
            {
                $arr_mail_data = $this->built_mail_data($details,$status,'supervisor',$arr_data);
            }
            else
            {
                $arr_mail_data = $this->built_mail_data($details,$status,'',$arr_data);
            }
            //$arr_mail_data = $this->built_mail_data($details,'add',$arr_data); 
            $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
        }
        
        return $result;
    }

    public function built_mail_data($arr_data,$status=FALSE,$supervisor=FALSE,$update_data=FALSE)
     {
        

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [];

            if($status == 'update')
            {
                $arr_built_content = [
                                      'FIRST_NAME'         => $arr_data['first_name'],
                                      'COURSE'             => $arr_data['course_name'],
                                      'EXAM_ADDED_BY'      => 'School Admin',
                                      'LEVEL'              => $arr_data['level'],
                                      'SCHOOL_ADMIN'       => $this->CommonDataService->get_school_name($this->school_id),
                                      'CLASS'              => $arr_data['class'],
                                      'NEW_START_TIME'     => $update_data['exam_start_time'],
                                      'NEW_END_TIME'       => $update_data['exam_end_time'],
                                      'EXAM_NAME'          => $arr_data['exam_name'],
                                      'PREVIOUS_EXAM_DATE' => $arr_data['exam_date'],
                                      'PREVIOUS_START_TIME'=> $arr_data['start_time'],
                                      'PREVIOUS_END_TIME'  => $arr_data['end_time'],
                                      'NEW_EXAM_DATE'      => getDateFormat($update_data['exam_date'])
                                     ];    
            }
            elseif($status == 'delete')
            {
                $arr_built_content = [
                                      'FIRST_NAME'         => $arr_data['first_name'],
                                      'COURSE'             => $arr_data['course_name'],
                                      'LEVEL'              => $arr_data['level'],
                                      'SCHOOL_ADMIN'       => $this->CommonDataService->get_school_name($this->school_id),
                                      'CLASS'              => $arr_data['class'],
                                      'EXAM_NAME'          => $arr_data['exam_name'],
                                      'EXAM_DATE'          => $arr_data['exam_date']
                                     ];    
            }
            else
            {

                $arr_built_content = [
                                      'FIRST_NAME'         => $arr_data['first_name'],
                                      'COURSE'             => $arr_data['course_name'],
                                      'EXAM_ADDED_BY'      => 'School Admin',
                                      'LEVEL'              => $arr_data['level'],
                                      'SCHOOL_ADMIN'       => $this->CommonDataService->get_school_name($this->school_id),
                                      'CLASS'              => $arr_data['class'],
                                      'EXAM_NAME'          => $arr_data['exam_name'],
                                      'EXAM_DATE'          => $arr_data['exam_date'],
                                      'START_TIME'         => $arr_data['start_time'],
                                      'END_TIME'           => $arr_data['end_time']
                                     ];     
            }
            
    
            $arr_mail_data                        = [];
            if($status =='update')
            {
                $arr_mail_data['email_template_slug'] = 'edit_exam';                
            }
            elseif($status =='delete')
            {
                $arr_mail_data['email_template_slug'] = 'cancel_exam';                
            }
            else
            {
                if($supervisor!='')
                {
                    $arr_mail_data['email_template_slug'] = 'add_exam_to_supervisor';                   
                }
                else
                {
                    $arr_mail_data['email_template_slug'] = 'add_exam';                       
                }
                
            }
            
            $arr_mail_data['arr_built_content']   = $arr_built_content;
            $arr_mail_data['user']                = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_sms_data($arr_data,$status=FALSE,$supervisor=FALSE,$update_data=FALSE)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {

            $arr_built_content = [];

            if($status == 'update')
            {
                $arr_built_content = [
                                      'COURSE'             => $arr_data['course_name'],
                                      'EXAM_ADDED_BY'      => 'School Admin',
                                      'LEVEL'              => $arr_data['level'],
                                      'CLASS'              => $arr_data['class'],
                                      'NEW_START_TIME'     => getTimeFormat($update_data['exam_start_time']),
                                      'NEW_END_TIME'       => getTimeFormat($update_data['exam_end_time']),
                                      'EXAM_NAME'          => $arr_data['exam_name'],
                                      'PREVIOUS_EXAM_DATE' => $arr_data['exam_date'],
                                      'PREVIOUS_START_TIME'=> $arr_data['start_time'],
                                      'PREVIOUS_END_TIME'  => $arr_data['end_time'],
                                      'NEW_EXAM_DATE'      => getDateFormat($update_data['exam_date'])
                                     ];    
            }
            elseif($status == 'delete')
            {
                $arr_built_content = [
                                      'COURSE'             => $arr_data['course_name'],
                                      'LEVEL'              => $arr_data['level'],
                                      'CLASS'              => $arr_data['class'],
                                      'EXAM_NAME'          => $arr_data['exam_name'],
                                      'EXAM_DATE'          => $arr_data['exam_date']
                                     ];    
            }
            else
            {

                $arr_built_content = [
                                      'COURSE'             => $arr_data['course_name'],
                                      'EXAM_ADDED_BY'      => 'School Admin',
                                      'LEVEL'              => $arr_data['level'],
                                      'EXAM_NAME'          => $arr_data['exam_name'],
                                      'SCHOOL_ADMIN'       => $this->CommonDataService->get_school_name($this->school_id),
                                      'CLASS'              => $arr_data['class'],
                                      'EXAM_DATE'          => $arr_data['exam_date'],
                                      'START_TIME'         => $arr_data['start_time'],
                                      'END_TIME'           => $arr_data['end_time']
                                     ];     
            }

            $arr_sms_data                      = [];
            if($status =='update')
            {
                $arr_sms_data['sms_template_slug'] = 'edit_exam';                
            }
            elseif($status =='delete')
            {
                $arr_sms_data['sms_template_slug'] = 'cancel_exam';                
            }
            else
            {
                if($supervisor!='')
                {
                    $arr_sms_data['sms_template_slug'] = 'add_exam_to_supervisor';                   
                }
                else
                {
                    $arr_sms_data['sms_template_slug'] = 'add_exam';                       
                }
                
            }
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }
}
