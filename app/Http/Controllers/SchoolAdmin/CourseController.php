<?php
namespace App\Http\Controllers\SchoolAdmin;
            
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Common\Traits\MultiActionTrait;
use App\Models\CourseModel;
use App\Models\SchoolCourseModel;
use App\Models\CourseTranslationModel;
use App\Models\LevelModel;
use App\Models\LevelTranslationModel;
use App\Models\ActivityLogsModel;   
use App\Common\Services\CommonDataService;

use Session;
use Validator;
use Flash;
use Sentinel;
use DB;
use Datatables;

class CourseController extends Controller
{
	use MultiActionTrait;
    public function __construct(ActivityLogsModel $activity_logs,
                                LanguageService $language,
                                LevelModel $level,
                                CommonDataService $CommonDataService,
                                LevelTranslationModel $level_translation,
                                SchoolCourseModel $school_course,
                                CourseModel $course,
                                CourseTranslationModel $translation) 
    {
        $this->arr_view_data          =   [];
        $this->CourseModel            =   $course;
        $this->SchoolCourseModel      =   $school_course;
        $this->BaseModel              =   $this->SchoolCourseModel;
        $this->CommonDataService      =   $CommonDataService;
        $this->CourseTranslationModel =   $translation;
        $this->LevelTranslationModel  =   $level_translation;
        $this->LevelModel             =   $level;
        $this->ActivityLogsModel      =   $activity_logs; 
        $this->LanguageService        =   $language;
        $this->module_url_path        =   url(config('app.project.school_admin_panel_slug')."/course");
        $this->module_view_folder     =   "schooladmin.course";
        $this->module_title           =   translation("course");
        $this->theme_color            =   theme_color();
        $this->school_id              =   Session::get('school_id');
        $this->academic_year          =   Session::get('academic_year');
        $this->first_name   =   $this->last_name =  $this->user_id  =  '';
        $obj_data                     =   Sentinel::getUser();

        $this->module_icon  = 'fa fa-file-code-o';
        
        $this->arr_view_data['page_title']  = ucfirst(translation("manage"))." ".strtolower(str_singular($this->module_title));
        $this->arr_view_data['module_icon'] = $this->module_icon;

        if($obj_data)
        {
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->user_id           = $obj_data->id;
        }
    }

        /*
        | index() : Course listing 
        | Auther  : Gaurav 
        | Date    : 11-05-2018
        */
    public function index() 
    {
        $this->arr_view_data['module_title']        = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['theme_color']         = $this->theme_color;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }
        /*
        | create() : Course create 
        | Auther   : Gaurav 
        | Date     : 11-05-2018
        */
    public function create()
    {
        $arr_level = [];
    	$obj_level = $this->CommonDataService->get_levels($this->academic_year);
        if($obj_level && count($obj_level)>0){
            $arr_level = $obj_level->toArray();
        }
        
        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['page_title']      = translation('add')." ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['arr_level']       = $arr_level;

        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    /*
        | store() : Course store 
        | Auther  : Gaurav 
        | Date    : 11-05-2018
        */        
    public function store(Request $request)
    {
        $form_data = array();
        $suggession_count = 0;
        $academic_year_id = $this->academic_year;  
        
        $arr_suggession_exam_type = [];

        $arr_lang =  $this->LanguageService->get_all_language();
        
        $arr_rules['course_name']     = ['required','regex:/^[a-zA-Z0-9 \-]+$/'];
        $arr_rules['school_level_id'] = "required";
        $arr_rules['coefficient']     = "required|numeric";

        $messages['required']         = translation('this_field_is_required');
        $messages['regex']            = translation('please_enter_valid_text_format');

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
             return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $slug                         = strslug(trim($request->input('course_name')));
        $school_level_id              = trim($request->input('school_level_id'));
        $coefficient                  = trim($request->input('coefficient'));
        $school_id             	      = $this->school_id;

        $arr_data=[];
        
        $obj_selected_suggession      = $this->CourseTranslationModel->where('slug',$slug)->first();

        if (isset($obj_selected_suggession) && count($obj_selected_suggession)>0) 
        {
            $arr_data['school_id']        = $school_id;
            $arr_data['school_level_id']  = $school_level_id;
            $arr_data['coefficient']      = $coefficient; 

            $course_id                = isset($obj_selected_suggession->course_id)?$obj_selected_suggession->course_id:0; 
            $arr_suggession_exam_type = $obj_selected_suggession->toArray();
            $suggession_count         = count($arr_suggession_exam_type);
            
            $does_exists = $this->SchoolCourseModel->where('course_id',$course_id)
                                                     ->where('school_id',$school_id)   
                                                     ->where('school_level_id',$school_level_id)   
                                                     ->count();
                                                     
            if($does_exists>0)
            {
                Flash::error(str_singular($this->module_title).' '.translation('already_exists'));
                return redirect()->back();
            }
			            	  	  
            if ($suggession_count>0) 
	        {
                $arr_data['course_id']         = $course_id;
                $arr_data['academic_year_id']  = $academic_year_id;
                $school_exam                   = $this->SchoolCourseModel->create($arr_data);
                if ($school_exam) 
                {
                   Flash::success($this->module_title .' '.translation('created_successfully')); 
                }
            }
        }
  		else
  		{
	  		$course                       = $this->CourseModel->create($arr_data);
            $arr_data['school_id']        = $school_id;
            $arr_data['school_level_id']  = $school_level_id;
            $arr_data['coefficient']      = $coefficient; 
	        $form_data                    = $request->all();
	        $course_id                    = $course->id;
	        		
	        if($course)
	        {   
	        	$arr_data['course_id']          = $course_id;  
                $arr_data['academic_year_id']  = $academic_year_id;        
	        	$this->SchoolCourseModel->create($arr_data);

	            $arr_event                 = [];
	            $arr_event['ACTION']       = 'ADD';
	            $arr_event['MODULE_TITLE'] = $this->module_title;

	            $this->save_activity($arr_event);

	            if(sizeof($arr_lang) > 0 )
	            {  
	                foreach ($arr_lang as $lang) 
	                {            
	                    $arr_data     = array();
	                    
	                    $course_name   = 'course_name';
	                    if(isset($form_data[$course_name]) && $form_data[$course_name] != '')
	                    {  
	                        $translation                 = $course->translateOrNew($lang['locale']);
	                        $translation->course_name    = $form_data[$course_name];
	                        $translation->slug           = $slug;
	                        $translation->course_id      = $course_id;
	                        $translation->save();
	                        
	                        Flash::success($this->module_title .' '.translation('created_successfully'));
	                    }
	                }
	            } 
	            else
	            {
	                Flash::success(translation('problem_occurred_while_creating'),' '.$this->module_title);
	            }
	        }  
  		}
        
    	return redirect()->back();
    }

	/*
    | get_exam_type_details() : Course details using ajax 
    | Auther                  : Gaurav 
    | Date                    : 09-05-2018
    */
    function get_course_details(Request $request)
    {     
        $school_id     = $this->school_id;
        $academic_year_id = $this->academic_year;  

        $str                =  $this->CommonDataService->get_academic_year_less_than($academic_year_id);
        $arr_academic_year  = explode(',', $str);   

        $exam_type_details                  = $this->CourseModel->getTable();
        $prefixed_exam_type_details         = DB::getTablePrefix().$this->CourseModel->getTable();

        $school_exam_type_trans_details            = $this->BaseModel->getTable();
        $prefixed_school_exam_type_details   = DB::getTablePrefix().$this->BaseModel->getTable();

        $exam_type_trans_details            = $this->CourseTranslationModel->getTable();
        $prefixed_exam_type_trans_details   = DB::getTablePrefix().$this->CourseTranslationModel->getTable();

        $obj_exam_type = DB::table($school_exam_type_trans_details)
                                ->select(DB::raw($prefixed_school_exam_type_details.".id as id,".
                                                 $prefixed_school_exam_type_details.".is_active as status,".
                                                 $prefixed_school_exam_type_details.".school_id,".
                                                 $prefixed_school_exam_type_details.".course_id,".
                                                 $prefixed_school_exam_type_details.".school_level_id,".
                                                 $prefixed_school_exam_type_details.".coefficient,".
                                                 $prefixed_exam_type_trans_details.".course_name"))
                                ->join($exam_type_trans_details,$school_exam_type_trans_details.'.course_id','=',$exam_type_trans_details.'.course_id')
                                ->where($exam_type_trans_details.'.locale','=',Session::get('locale'))
                                ->whereNull($prefixed_school_exam_type_details.'.deleted_at')
                                ->where($prefixed_school_exam_type_details.'.school_id','=',$school_id);
                                if (isset($arr_academic_year) && count($arr_academic_year)>0) 
                                {
                                  $obj_exam_type->whereIn($prefixed_school_exam_type_details.'.academic_year_id',$arr_academic_year);        
                                }
                                $obj_exam_type->orderBy($prefixed_school_exam_type_details.'.created_at','DESC');
                                                                                     
        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
            $obj_exam_type = $obj_exam_type->WhereRaw("( (".$prefixed_exam_type_trans_details.".course_name LIKE '%".$search_term."%') ")
                                           ->orWhereRaw("(".$prefixed_school_exam_type_details.".coefficient LIKE '%".$search_term."%') )"); 
        }
        return $obj_exam_type;   
    }

        /*
        | get_records() : Course get_records 
        | Auther        : Gaurav 
        | Date          : 11-05-2018
        */
    public function get_records(Request $request)
    {
       
        
        $arr_current_user_access =[];
    
        $arr_current_user_access = $this->CommonDataService->current_user_access();
       
        $obj_exam_type  = $this->get_course_details($request);

        $json_result     = Datatables::of($obj_exam_type);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('course.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data) 
                            {
                                return base64_encode($data->id);
                            });
        }                    

        $json_result     = $json_result->editColumn('course_name',function($data)
                            { 
                                 
                                if($data->course_name!=null && $data->course_name!=''){
                                    return  ucfirst($data->course_name);
                                }else{
                                    return  '-';
                                }

                            }) 
        					->editColumn('level_class',function($data) 
                            { 
                               $level_id   = $data->school_level_id;
                           	   $level_class = get_level_class($level_id);
                           	   return $level_class;
                            }) 
                            ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                            {
                                $build_status_btn = '';
                                if(array_key_exists('course.update', $arr_current_user_access))
                                {
                                    if($data->status != null && $data->status == "0")
                                    {   
                                        $build_status_btn = '<a class="blue-color" title="'.translation('activate').'" href="'.$this->module_url_path.'/activate/'.base64_encode($data->id).'" 
                                        onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-lock"></i></a>';
                                    }
                                    elseif($data->status != null && $data->status == "1")
                                    {
                                        $build_status_btn = '<a class="light-blue-color" title="'.translation('deactivate').'" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-unlock"></i></a>';
                                    }
                                }
                                $build_delete_action ='';
                                
                                if(array_key_exists('course.delete',$arr_current_user_access))
                                {
	                                $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
	                                $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                }

                               return $build_status_btn.'&nbsp;'.$build_delete_action.'&nbsp;';
                            })
                            ->editColumn('build_checkbox',function($data) use($arr_current_user_access){
                                $build_checkbox='';
                                if(array_key_exists('course.update',$arr_current_user_access) || array_key_exists('course.delete',$arr_current_user_access))
                                {
                                    $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 

                                    
                                }
                                return  $build_checkbox;
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    /*
    | get_course_name_suggession() : Course get_course_name_suggession 
    | Auther        : Gaurav 
    | Date          : 11-05-2018
    */
    public function get_course_name_suggession(Request $request)
    {
    	$data ="";
        $arr_roles = [];
    	$obj_roles = $this->CourseModel
                      ->whereHas('get_course',function($q)use($request){
                        $q->where('course_name','LIKE', '%'.$request->keyword.'%');
                      })
                      ->with(['get_course'=>function($q)use($request){
                        $q->where('course_name','LIKE', '%'.$request->keyword.'%');
                    }])
                      ->get();
        
        if(isset($obj_roles) && count($obj_roles)>0)
        {
            $arr_roles = $obj_roles ->toArray();
            
          	$data = json_encode($arr_roles);      
        }   
        return $data;             
    }
    
}
