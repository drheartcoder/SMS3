<?php

namespace App\Http\Controllers\Professor;

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
use App\Models\ResultModel;
use App\Models\SchoolAdminModel;
use App\Models\NotificationModel;
use App\Models\RoomManagementModel;
use App\Models\RoomAssignmentModel;
use App\Models\SchoolTimeTableModel;
use App\Common\Services\CommonDataService;
use App\Common\Services\EmailService;
use App\Common\Traits\MultiActionTrait;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use PDF;
use Datatables;

class ExamController extends Controller
{
    use MultiActionTrait;
	public function __construct(
    								
    								CommonDataService $common_data_service,
                                    EmailService $EmailService
    								
    							)
    {

    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.professor_panel_slug')).'/exam';
        $this->module_title                 = translation("exam");
        
        
        $this->module_view_folder           = "professor.exam";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-exam';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->CommonDataService            = $common_data_service;
        $this->EmailService                 = $EmailService;
        $this->academic_year				= Session::get('academic_year');

        $this->first_name = $this->last_name =$this->ip_address = $this->school_admin_id ='';
        $this->permissions = [];

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
        $this->BaseModel                  = new ExamModel();
        $this->StudentModel               = new StudentModel();
        $this->ResultModel                = new ResultModel();
        $this->SchoolAdminModel           = new SchoolAdminModel();
        $this->NotificationModel          = new NotificationModel();
        $this->SchoolTimeTableModel       = new SchoolTimeTableModel();
 
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

        $obj_permissions = $this->SchoolAdminModel->with('notification_permissions','get_user_details')->where('school_id',$this->school_id)->first();
        if(isset($obj_permissions) && count($obj_permissions)>0 && !is_null($obj_permissions))
        {
            $arr_permissions = $obj_permissions->toArray();
            $this->school_admin_id = $arr_permissions['user_id'];
            if(isset($arr_permissions['notification_permissions']['notification_permission']) && !empty($arr_permissions['notification_permissions']['notification_permission']))
            {   
                $this->permissions = json_decode($arr_permissions['notification_permissions']['notification_permission'],true);
            }
            $this->school_admin_email   = isset($arr_permissions['get_user_details']['email'])?$arr_permissions['get_user_details']['email']:'';
            $this->school_admin_contact = isset($arr_permissions['get_user_details']['mobile_no'])?$arr_permissions['get_user_details']['mobile_no']:'';

        }

    }

      /*
    | index() 		: Redirect to exam list 
    | Auther        : Pooja K 
    | Date          : 21-05-2018
    */ 
    public function index()
    {	
        
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data[$this->str_module_title]    = translation("manage")." ".$this->module_title;
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

        $role = Sentinel::findRoleBySlug(config('app.project.role_slug.professor_role_slug'));
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;

        $json_result  = Datatables::of($obj_custom);

        $json_result = $json_result->blacklist(['id']);                    

  
        $json_result =  $json_result->editColumn('exam_time',function($data)
                        {
                            return $data->exam_start_time.' - '.$data->exam_end_time ;
                        })
                        ->editColumn('build_status',function($data) use ($arr_current_user_access)
                        {
                            $status = '';
                            if($data->status == 'APPROVED')
                            {
                                $status = '<a class="light-blue-color" style="color:white">&nbsp;Approved&nbsp;</a>';
                            }
                            else if($data->status == 'PENDING')
                            {
                                $status = '<a class="light-red-color" style="color:white">&nbsp;Pending&nbsp;</a>';
                            }
                            else if($data->status == 'REJECTED')
                            {
                                $status = '<a class="lime-color" style="color:white">&nbsp;Rejected&nbsp;&nbsp;&nbsp;&nbsp;</a>';
                            }

                            return $status;
                            
                        })
                        ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                        {
                            $build_edit_action = $build_delete_action = $build_view_action = $build_result_action= $build_upload_action='';
                            /*if($data->status!='APPROVED')
                            {*/    
                                
                                if(array_key_exists('exam.delete',$arr_current_user_access))
                                {     
                                    $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->exam_id);

                                    if($data->status!='APPROVED')
                                    {
                                        if($data->exam_added_by==$this->user_id)
                                        {
                                            $build_delete_action = '<a href="'.$delete_href.'" class="red-color" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';     
                                        }
                                        else
                                        {
                                            $build_delete_action = '<a style="position: relative;" class="red-color" href="javascript:void(0)" title="'.translation('access_denied').'" ><i class="fa fa-trash" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                        }
                                    }
                                    else
                                    {
                                        $build_delete_action = '<a style="position: relative;" class="red-color" href="javascript:void(0)" title="'.translation('access_denied').'" ><i class="fa fa-trash" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                    } 

                                    
                                }

                                if(array_key_exists('exam.update',$arr_current_user_access))
                                {
                                    $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->exam_id);
                                    if($data->status!='APPROVED' && $data->status!='REJECTED')
                                    {
                                        if($data->exam_added_by==$this->user_id)
                                        {
                                            $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';
                                        }
                                        else
                                        {
                                            $build_edit_action = '<a style="position: relative;" class="orange-color" href="javascript:void(0)" title="'.translation('access_denied').'"><i class="fa fa-edit" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                        }
                                    }
                                    else
                                    {
                                        $build_edit_action = '<a style="position: relative;" class="orange-color" href="javascript:void(0)" title="'.translation('access_denied').'"><i class="fa fa-edit" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                    }

                                    $result_href =  $this->module_url_path.'/result/'.base64_encode($data->exam_id);
                                    
                                    if($data->status=='APPROVED')
                                    {
                                        $exam_date = date_create($data->exam_date);
                                        $today = date_create(date('Y-m-d'));
                                        $diff = date_diff($exam_date, $today);
                                        
                                        if($diff->format('%R%a')>=0 && $data->exam_added_by==$this->user_id)
                                        {
                                            $build_result_action = '<a class="light-blue-color" href="'.$result_href.'" title="'.translation('result').'"><i class="fa fa-graduation-cap" ></i></a>';
                                        }
                                        else
                                        { 
                                            $build_result_action = '<a style="position: relative;" class="light-blue-color" href="javascript:void(0)" title="'.translation('access_denied').'"><i class="fa fa-graduation-cap" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                        }
                                    }
                                    else
                                    {
                                        $build_result_action = '<a style="position: relative;" class="light-blue-color" href="javascript:void(0)" title="'.translation('access_denied').'"><i class="fa fa-graduation-cap" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                    }

                                    if($data->status=='APPROVED')
                                    {
                                        if($data->exam_added_by==$this->user_id)
                                        {
                                            $build_upload_action = '<a class="lime-color upload-link" onclick="setID(\''.base64_encode($data->exam_id).'\')" data-toggle="modal" data-target="#import_modal" title="'.translation('upload_result_csv').'"><i class="fa fa-upload" ></i></a>';
                                        }
                                        else
                                        {
                                            $build_upload_action = '<a style="position: relative;"  class="lime-color upload-link" onclick="javascript:void(0)" title="'.translation('access_denied').'"><i class="fa fa-upload" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                        }
                                    }
                                    else
                                    {
                                        $build_upload_action = '<a style="position: relative;"  class="lime-color upload-link" onclick="javascript:void(0)" title="'.translation('access_denied').'"><i class="fa fa-upload" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                    }
                                }

                            /*}  
                            else
                            {
                                $build_delete_action = '<a style="position: relative;" class="red-color" href="javascript:void(0)" title="'.translation('delete').'" ><i class="fa fa-trash" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                            }  */

                            $view_href =  $this->module_url_path.'/view/'.base64_encode($data->exam_id);
                            $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>';

                            return $build_view_action.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_delete_action.'&nbsp;'.$build_result_action.'&nbsp;'.$build_upload_action;
                        })
                        ->editColumn('build_checkbox',function($data) use ($arr_current_user_access){
                        	$build_checkbox ='';
                            if(array_key_exists('exam.update',$arr_current_user_access) || array_key_exists('exam.delete',$arr_current_user_access) )
                            {
                                if($data->status!='APPROVED')
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
                                else
                                {
                                    $build_checkbox = '-';
                                }

                                
                            }
                            return $build_checkbox;
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
    public function get_exam_records(Request $request,$type='',$fun_type='')
    {
    	$user_id = $this->user_id;
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
                                            $exam_table.".exam_name,".
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
                                        ->whereRaw('('.$exam_table.'.exam_added_by = '.$user_id.' OR '.$exam_table.'.supervisor_id ='.$user_id.' )')
                                        ->where($exam_table.'.school_id',$this->school_id)
                                        ->where($exam_table.'.academic_year_id','=',$this->academic_year)
                                        ->orderBy($exam_table.'.created_at','DESC');

        if($fun_type == 'export'){
            $search_term = $request->input('search');
        }else{
            $search = $request->input('search');
            $search_term = $search['value'];
        }

        if($request->has('search') && $search_term!="")
        {
            $obj_custom = $obj_custom->WhereRaw("( (".$level_table.".level_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$exam_table.".exam_no LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$class_table.".class_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$exam_period_translation_table.".exam_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$exam_type_translationtable.".exam_type LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$exam_table.".status LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$course_table.".course_name LIKE '%".$search_term."%') )");
        }

        if($fun_type=="export"){
            return $obj_custom->get();
        }else{
            return $obj_custom;
        }
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

    	$obj_levels = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id);
        
    	if(!empty($obj_levels))
    	{
			$arr_levels = $obj_levels -> toArray();    		
    	}

    	$obj_exam_period = $this->ExamPeriodSchoolModel
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

    	/*$arr_course = $this->CommonDataService->get_professor_courses($level,$class,$this->user_id);*/
    	/*if(!empty($obj_course))
    	{
    		$arr_course = $obj_course ->toArray();
    	}*/

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

    	$this->arr_view_data[$this->str_module_title] = translation("add")." ".$this->module_title;
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

        $obj_class = $this->CommonDataService->get_class($level_id,$this->user_id);
    
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
  				foreach($arr_assessment_scale as $value)
  				{
  					$options .= '<option value='.$value['id'].'>'.$value['scale'].'</option>';
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

        $options =  $room_options ='';
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
                foreach($arr_floors as $value)
                { 
                    $options .= '<option value='.$value['id'];
                    if($request->has('floor'))
                    {
                        
                        if($request->input('floor')==$value['floor_no'])
                        {
                            $options .= 'selected';
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
                foreach($arr_floors as $value)
                {
                    $options .= '<option value='.$value['id'];
                    if($request->has('floor'))
                    {
                        if($request->input('floor')==$value['room_no'])
                        {
                            $options .= 'selected';
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
        $arr_rules['gradebook']        = ['required','regex:/^(test|other)$/'];
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

        $level = $request->input('level');
        $class = $request->input('class');

        $obj_level_class = $this->LevelClassModel->where('level_id',$level)->where('class_id',$class)->where('school_id',$this->school_id)->first();

        if($request->input('exam_place')=='red')
        {
            $arr_data['place_type'] = 'PREMISES';
            $arr_data['room_assignment_id'] = $request->room;
        }
        else
        {
            $arr_data['place_type'] = 'OTHER';
            $arr_data['place_name'] = $request->place;   
            $arr_data['building'] = trim($request->other_building);
            $arr_data['floor_no'] = trim($request->other_floor);
            $arr_data['room'] = trim($request->other_room);
        }
        $arr_data['level_class_id']      = $request->class;
        $arr_data['exam_period_id']      = $request->input('exam_period');
        $arr_data['course_id']           = $request->input('course');
        $arr_data['exam_name']           = trim($request->input('exam_name'));
        $arr_data['exam_start_time']     = $request->input('exam_start_time');
        $arr_data['exam_end_time']       = $request->input('exam_end_time');
        $arr_data['supervisor_id']       = $request->input('supervisor');
        $arr_data['exam_type_id']        = $request->input('exam_type');
        $arr_data['assessment_scale_id'] = $request->input('assessment_scale');
        $arr_data['exam_added_by']       = $this->user_id;
        $arr_data['exam_description']    = trim($request->exam_description);
        $arr_data['status']              = "PENDING";

        if($request->gradebook=='test'){
            $arr_data['gradebook']    = 0;
        }
        else{
            $arr_data['gradebook']    = 1;
        }
        $date = $request->input('exam_date');

        $arr_data['exam_date'] = $date;
        $arr_data['exam_no'] = $this->generate_exam_number();
            
        $exam = $this->ExamModel->create($arr_data);
        if($exam)
        {
            $result = $this->send_notifications($exam);
            
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
        if(!$enc_id)
        {
            return redirect()->back();
        }        

        $arr_exam = [];            
        $obj_exam = $this->ExamModel->where('id',$enc_id)->first();

        if(!empty($obj_exam))
        {
            $arr_exam = $obj_exam->toArray();
        }
        /*dd($arr_exam);*/
       
        $arr_academic_year = '';
     
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }    
        $arr_levels = $arr_exam_period = $arr_exam_type = $arr_course = $arr_professor =[];

        $obj_levels = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id);
        if(!empty($obj_levels))
        {
            $arr_levels = $obj_levels -> toArray();         
        }

        $obj_exam_period = $this->ExamPeriodSchoolModel
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


       // dd($arr_exam_type[0]['exam_type_id']);
        $this->arr_view_data[$this->str_module_title] = translation("edit")." ".$this->module_title;
        $this->arr_view_data['arr_levels']            = $arr_levels ;
        
        $this->arr_view_data['arr_exam_period']       = $arr_exam_period ;
        $this->arr_view_data['arr_exam_type']         = $arr_exam_type ;
        $this->arr_view_data['arr_professor']         = $arr_professor ;
        $this->arr_view_data['arr_building']          = $arr_building ;

        $level_class = $this->LevelClassModel->where('id',$arr_exam['level_class_id'])->first();
        $arr_exam['level'] = isset($level_class->id) ? $level_class->level_id :0;
         if(!empty($arr_exam))
        {
            $obj_course = $this->CommonDataService->get_professor_courses($arr_exam['level_class_id'],$this->user_id);
        }
        
        if(!empty($obj_course))
        {
            $this->arr_view_data['arr_courses']           = $obj_course->toArray() ;
        }
        $arr_class = '';
        $obj_class = $this->ExamModel->select('level_class_id')->with('get_level_class.class_details')->where('level_class_id','=',$arr_exam['level_class_id'])->get();

        if(!empty($obj_class))
        {
            $arr_class = $obj_class->toArray();
            $this->arr_view_data['arr_class']           = $arr_class;
        }

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
        $arr_rules['gradebook']        = ['required','regex:/^(test|other)$/'];
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

        $level = $request->input('level');
        $class = $request->input('class');

        $obj_level_class = $this->LevelClassModel->where('level_id',$level)->where('class_id',$class)->where('school_id',$this->school_id)->first();

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
        $arr_data['exam_start_time']     = $request->input('exam_start_time');
        $arr_data['exam_end_time']       = $request->input('exam_end_time');
        $arr_data['supervisor_id']       = $request->input('supervisor');
        $arr_data['exam_type_id']        = $request->input('exam_type');
        $arr_data['assessment_scale_id'] = $request->input('assessment_scale');
        $arr_data['exam_added_by']       = $this->user_id;
        $arr_data['exam_description']    = trim($request->exam_description);
       

        if($request->gradebook=='test'){
            $arr_data['gradebook']    = 0;
        }
        else{
            $arr_data['gradebook']    = 1;
        }

        $date = $request->input('exam_date');
        $date = date_create($date);
        $date = date_format($date,'Y-m-d');

        $current_date  = date('Y-m-d');
        $arr_data['exam_date'] = $date;
        $exam = $this->ExamModel->where('id',base64_decode($enc_id))->first();
        $this->ExamModel->where('id',base64_decode($enc_id))->update($arr_data);

        $previous_date = date($exam->exam_date);

        $nxt_date      = date($date);
        
        if(($previous_date!=$nxt_date) && ($nxt_date>$current_date))
        {
            if(($previous_date<$nxt_date) || ($nxt_date<$previous_date) || (strtotime($exam->exam_start_time)!=strtotime($request->input('exam_start_time'))) || (strtotime($exam->exam_end_time)!=strtotime($request->input('exam_end_time'))))
            {
                $result = $this->send_notifications($exam,'update',$arr_data);
            }
        }
        Flash::success(translation('exam_updated_successfully'));
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
    | result() : redirecting to result page
    | Auther        : Pooja K  
    | Date          : 17-07-2018
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
    | store_result() : store result
    | Auther        : Pooja K  
    | Date          : 17-07-2018
    */
    public function store_result($enc_id=FALSE,Request $request){
        $id = base64_decode($enc_id);
        
        if(is_numeric($id)){
            
            $exam = $this->ExamModel->with('get_assessment')->where('id',$id)->first();
            
            $scale = isset($exam->get_assessment->scale) ? $exam->get_assessment->scale : 0;

            $marks = $request->marks;
            $flag =0;
            foreach($marks as $key=>$mark){
                $marks[$key] = trim($mark);

                

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
                    Flash::success(translation('some_records_are_not_valid'));    
                
                return redirect()->back();
            }    
        }
        Flash::error(translation('something_went_wrong'));
        return redirect($this->module_url_path);
    }

    /*
    | download_result() : download result format
    | Auther        : Pooja K  
    | Date          : 17-07-2018
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
        $option = '';
        $level = $request->input('level');
        $class = $request->input('class');
        $course = $request->input('course');
        $professor = $this->CommonDataService->get_professor_courses($class,$this->user_id);
        
        if(isset($professor) && $professor!=null)
        {
            $arr_professor = $professor->toArray(); 
            if(isset($arr_professor) && count($arr_professor)>0)
            {                                                   
                $option .= '<option value="">'.translation('select_course').'</option>';
                foreach ($arr_professor as $key => $professor) 
                {
                    $option .= '<option value="';
                    $option .= isset($professor['course_id'])?$professor['course_id']:'';
                    if($course)
                    {
                        if($professor['course_id'] == $course)
                        {
                            $option .= 'selected';
                        }
                    }
                    $option .= '">';
                    $option .= isset($professor['professor_subjects']['course_name'])?$professor['professor_subjects']['course_name']:'';
                    $option .= '</option>';
                }
            }
        }
        return $option;
       
    }

    public function send_notifications($exam,$status=FALSE,$arr_data=FALSE)
    {
        $exam_details = $this->SchoolTimeTableModel
                             ->with('notifications','level_details','class_details','course_details','user_details')
                             ->where('course_id',$exam->course_id)
                             ->where('level_class_id',$exam->level_class_id)
                             ->where('school_id',$this->school_id)
                             ->where('academic_year_id',$this->academic_year)
                             ->first();

        if(isset($exam_details) && count($exam_details) && $exam_details!=null)
        {
            $exam_details = $exam_details->toArray();
        }
        
        $level = isset($exam_details['level_details']['level_name'])?$exam_details['level_details']['level_name']:'';
        $class = isset($exam_details['class_details']['class_name'])?$exam_details['class_details']['class_name']:'';
        $course= isset($exam_details['course_details']['course_name'])?ucwords($exam_details['course_details']['course_name']):'';

        if(isset($this->permissions) && count($this->permissions)>0)
        {
            if(array_key_exists('exam.app',$this->permissions))
            {
             
               /* $arr_notification = [];
                $arr_notification['school_id']          =  $this->school_id;
                $arr_notification['from_user_id']       =  $this->user_id;
                $arr_notification['to_user_id']         =  $this->school_admin_id;
                $arr_notification['user_type']          =  config('app.project.role_slug.professor_role_slug');
                $arr_notification['notification_type']  =  'Exam Add';
                $arr_notification['title']              =  'New exam added: New exam '.$exam->exam_name.' is scheduled for '.$level.' '.$class.' '.$course.' subject on date '.getDateFormat($exam->exam_date).' '.getTimeFormat($exam->exam_start_time).' - '.getTimeFormat($exam->exam_end_time);
                $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.school_admin_role_slug').'/exam';*/
                

                if($status!='' && $status=='update')
                {
                    $arr_notification['notification_type']  =  'Exam';
                    $arr_notification['title']              =  'Exam :Exam schedule of '.ucwords($exam->exam_name).' for '.$level.' '.$class.' '.$course.' changed from '.getDateFormat($exam->exam_date).' '.$exam->exam_start_time.' - '.$exam->exam_end_time.' to '.getDateFormat($arr_data['exam_date']).' '.getTimeFormat($arr_data['exam_start_time']).' - '.getTimeFormat($arr_data['exam_end_time']);
                    $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.school_admin_role_slug').'/exam';
                }
                elseif($status!='' && $status=='delete')
                {
                    $arr_notification['notification_type']  =  'Exam';
                    $arr_notification['title']              =  'Exam :'.$exam->exam_name.' of '.$level.' '.$class.' for '.$course.' scheduled on '.$exam->exam_date.' is cancelled for some reason.';
                }
                else
                {
                    $arr_notification['notification_type']  =  'Exam Added';
                    $arr_notification['title']              =  'Exam Added:New exam '.$exam->exam_name.' is scheduled for '.$level.' '.$class.' '.$course.' subject on date '.getDateFormat($exam->exam_date).' '.getTimeFormat($exam->exam_start_time).' - '.getTimeFormat($exam->exam_end_time);
                    $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.school_admin_role_slug').'/exam';
                }
                    $this->NotificationModel->create($arr_notification);
            }

            $details          = [
                                        'course_name' =>  isset($course)?ucwords($course):'',
                                        'level'       =>  isset($level)?$level:'',
                                        'class'       =>  isset($class)?$class:'',
                                        'email'       =>  $this->school_admin_email,
                                        'mobile_no'   =>  $this->school_admin_contact,
                                        'exam_name'   =>  isset($exam->exam_name)?ucwords($exam->exam_name):'',
                                        'start_time'  =>  isset($exam->exam_start_time)?getTimeFormat($exam->exam_start_time):'',
                                        'end_time'    =>  isset($exam->exam_end_time)?getTimeFormat($exam->exam_end_time):'',
                                        'exam_date'   =>  isset($exam->exam_date)?getDateFormat($exam->exam_date):''
                                ];
            
            if(array_key_exists('exam.sms',$this->permissions))
            {
                $arr_sms_data = $this->built_sms_data($details,$status,$arr_data);
                $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
            }
            if(array_key_exists('exam.email',$this->permissions))
            {
                $arr_mail_data = $this->built_mail_data($details,$status,$arr_data);
                $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
            }
        }
    }

    public function built_mail_data($arr_data,$status=FALSE,$update_data=FALSE)
    {
    
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [];

            if($status == 'update')
            {
                $arr_built_content = [
                                      'FIRST_NAME'         => 'School Admin',
                                      'COURSE'             => $arr_data['course_name'],
                                      'EXAM_ADDED_BY'      => 'Professor',
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
                                      'FIRST_NAME'         => 'School Admin',
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
                                      'FIRST_NAME'         => 'School Admin',
                                      'COURSE'             => $arr_data['course_name'],
                                      'EXAM_ADDED_BY'      => 'Professor',
                                      'LEVEL'              => $arr_data['level'],
                                      'SCHOOL_ADMIN'       => $this->CommonDataService->get_school_name($this->school_id) ,
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
                $arr_mail_data['email_template_slug'] = 'add_exam';                         
            }
            
            $arr_mail_data['arr_built_content']   = $arr_built_content;
            $arr_mail_data['user']                = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_sms_data($arr_data,$status=FALSE,$update_data=FALSE)
    {
        
        if(isset($arr_data) && sizeof($arr_data)>0)
        {

            $arr_built_content = [];

            if($status == 'update')
            {
                $arr_built_content = [
                                      'COURSE'             => $arr_data['course_name'],
                                      'EXAM_ADDED_BY'      => 'Professor',
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
                                      'EXAM_ADDED_BY'      => 'Professor',
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
                $arr_sms_data['sms_template_slug'] = 'add_exam'; 
            }
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }

    /*
    | export() : Export List
    | Auther  : Vrajesh
    | Date    : 14-12-2018
    */

    public function export(Request $request)
    {
        $file_type = config('app.project.export_file_formate');
        $obj_data = $this->get_exam_records($request,'','export');

        if(sizeof($obj_data)<=0){
            Flash::error(translation("no_records_found_to_export"));
            return redirect()->back();
        }
        if(sizeof($obj_data)>500 && $request->file_format == $file_type ){
            Flash::error(translation("too_many_records_to_export"));
            return redirect()->back();
        }
        if($request->file_format == $file_type){
            \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel) use($obj_data)
                {
                    $excel->sheet(ucwords($this->module_title), function($sheet) use($obj_data) 
                    {
                        $arr_fields['sr_no']        = translation('sr_no');
                        $arr_fields['exam_number']  = translation('exam_number');
                        $arr_fields['level']        = translation('level');
                        $arr_fields['class']        = translation('class');
                        $arr_fields['exam_type']    = translation('exam_type');
                        $arr_fields['exam_name']    = translation('exam_name');
                        $arr_fields['course']       = translation('course');
                        $arr_fields['status']       = translation('status');
                        $arr_fields['exam_period']  = translation('exam_period');

                        $sheet->row(2, [ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                        $sheet->row(4, $arr_fields);
                        
                        // To set Colomn head
                        $j = 'A'; $k = '4';
                        for($i=0; $i<=8;$i++)
                        {
                            $sheet->cell($j.$k, function($cells) {
                                $cells->setBackground('#495b79');
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                                $cells->setFontColor('#ffffff');
                            });
                            $j++;
                        }

                        if(sizeof($obj_data)>0)
                        {
                            $arr_tmp = [];
                            $count = 1;
                            foreach($obj_data as $key => $result)
                            {
                                $arr_tmp[$key]['sr_no']         = $count++;
                                $arr_tmp[$key]['exam_number']   = $result->exam_no;
                                $arr_tmp[$key]['level']         = $result->level_name;
                                $arr_tmp[$key]['class']         = $result->class_name;
                                $arr_tmp[$key]['exam_type']     = $result->exam_type;
                                $arr_tmp[$key]['exam_name']     = $result->exam_name;
                                $arr_tmp[$key]['course']        = $result->course_name;
                                $arr_tmp[$key]['status']        = $result->status;
                                $arr_tmp[$key]['exam_period']   = $result->exam_start_time.'-'.$result->exam_end_time;
                            }
                            $sheet->rows($arr_tmp);
                        }
                    });
                })->export($file_type);     
        }
        
        if($request->file_format == 'pdf')
        {
            $this->arr_view_data['arr_data'] = $obj_data;
            
            $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
            return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
        }
    }

}
