<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Models\ExamPeriodTranslationModel;
use App\Models\ExamPeriodSchoolModel;
use App\Common\Traits\MultiActionTrait;
use App\Models\ExamPeriodModel;
use App\Common\Services\CommonDataService;                            
use Validator;
use Flash;
use Session;
use DB;
use Sentinel;
use Datatables;

class ExamPeriodController extends Controller
{
	use MultiActionTrait;
    public function __construct(
								LanguageService  $language,
                                CommonDataService $CommonDataService,
								ExamPeriodModel  $exam_period,
                                ExamPeriodSchoolModel $exam_period_school,
								ExamPeriodTranslationModel  $exam_period_translation
    						   )
    {
		$this->arr_view_data 	          = [];
		$this->ExamPeriodModel            = $exam_period;
        $this->ExamPeriodSchoolModel      = $exam_period_school;
		$this->LanguageService            = $language;
		$this->BaseModel                  = $this->ExamPeriodSchoolModel;
        $this->CommonDataService          = $CommonDataService;
		$this->ExamPeriodTranslationModel = $exam_period_translation;
		$this->module_url_path 	          = url(config('app.project.school_admin_panel_slug')."/exam_period");
		$this->module_view_folder         = "schooladmin.exam_period";
		$this->module_title               = translation('exam_period');
		$this->theme_color                = theme_color();
        $this->school_id                  = Session::get('school_id');
        $this->academic_year              = Session::get('academic_year');
		$this->module_icon                = 'fa fa-book';
		$this->create_icon                = 'fa fa-plus-circle';
		$this->edit_icon                  = 'fa fa-edit';

        $this->arr_view_data['page_title']      = translation('exam_period');
    }
        /*
        | index() : Exam Period listing 
        | Auther : Gaurav 
        | Date : 10-05-2018
        */
    public function index()
    {
        
        $this->arr_view_data['module_title']    = translation("manage")." ".$this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;       

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    } 

        /*
        | get_records() : Exam Period listing using ajax 
        | Auther        : Gaurav 
        | Date          : 10-05-2018
        */ 
    public function get_records(Request $request)
    {
        $obj_custom = $this->get_exam_period_records($request);

        $role = Sentinel::findRoleBySlug(config('app.project.role_slug.school_admin_role_slug'));
        //$arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;
        $arr_current_user_access = $this->CommonDataService->current_user_access();

        $json_result  = Datatables::of($obj_custom);

        $json_result = $json_result->blacklist(['id']);                    

  
        $json_result =  $json_result->editColumn('enc_id',function($data)
                        {
                            return  base64_encode(($data->exam_id));
                        })
                        ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                        {

                            $build_status_btn = '';
                            if(array_key_exists('exam_period.update',$arr_current_user_access))
                            { 
                                if($data->is_active != null && $data->is_active == "0")
                                {   
                                    $build_status_btn = '<a class="blue-color" title="'.translation('activate').'" href="'.$this->module_url_path.'/activate/'.base64_encode($data->exam_id).'" 
                                    onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-lock"></i></a>';
                                }
                                elseif($data->is_active != null && $data->is_active == "1")
                                {
                                    $build_status_btn = '<a title="'.translation('deactivate').'" class="light-blue-color" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->exam_id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-unlock"></i></a>';
                                }
                            }  
                            $build_delete_action ='';        
                            if(array_key_exists('exam_period.delete',$arr_current_user_access))
                            {      

                                $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->exam_id);
                                $build_delete_action = '<a href="'.$delete_href.'" class="red-color" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                            }

                            return $build_status_btn.'&nbsp;'.$build_delete_action;  
                        })
                        ->editColumn('build_checkbox',function($data) use($arr_current_user_access){
                            $build_checkbox='';
                                if(array_key_exists('exam_period.update',$arr_current_user_access) || array_key_exists('exam_period.delete',$arr_current_user_access))
                                {
                                $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->exam_id).'" value="'.base64_encode($data->exam_id).'" /><label for="mult_change_'.base64_encode($data->exam_id).'"></label></div>'; 
                                }
                            return $build_checkbox;
                        })
                                              
                        ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);
    }

        /*
        | get_records() : Exam Period listing using ajax 
        | Auther        : Gaurav 
        | Date          : 10-05-2018
        */
    public function get_exam_period_records(Request $request)
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
        $academic_year_id = $this->academic_year;
       
        $str                =  $this->CommonDataService->get_academic_year_less_than($academic_year_id);
        $arr_academic_year  = explode(',', $str);                
        $custom_table = $this->ExamPeriodSchoolModel->getTable();   
        $prefixed_custom_table = DB::getTablePrefix().$this->ExamPeriodSchoolModel->getTable();                                             

        $custom_translation_table = $this->ExamPeriodTranslationModel->getTable();   
        $prefixed_translation_table = DB::getTablePrefix().$this->ExamPeriodTranslationModel->getTable();                                             
      
        $obj_custom = DB::table($custom_table)
                        ->select(DB::raw(   
                                            $prefixed_custom_table.".id as exam_id,".
                                            $prefixed_translation_table.".exam_name as exam_name,".
                                            $prefixed_custom_table.".is_active,".
                                            $prefixed_custom_table.".school_id"
                                        ))
                                        ->where($custom_translation_table.'.locale','=',$locale)
                                        ->whereNull($prefixed_custom_table.'.deleted_at')
                                        ->leftJoin($custom_translation_table,$custom_translation_table.'.exam_id',' = ',$custom_table.'.exam_id')
                                        ->where($prefixed_custom_table.'.school_id','=',$school_id);
                                        if (isset($arr_academic_year) && count($arr_academic_year)>0) 
                                        {
                                        $obj_custom->whereIn($prefixed_custom_table.'.academic_year_id',$arr_academic_year);        
                                        }
                                        $obj_custom->orderBy($prefixed_custom_table.'.created_at','DESC');

        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
            $obj_custom = $obj_custom->WhereRaw("( (".$prefixed_translation_table.".exam_name LIKE '%".$search_term."%') )");
        }
        return $obj_custom ;
    }

        /*
        | create() : Exam Period create 
        | Auther   : Gaurav 
        | Date     : 10-05-2018
        */
    public function create()
    {
        $arr_lang = [];
        $arr_lang = $this->LanguageService->get_all_language();

        $this->arr_view_data['module_title'] = translation('add')." ".$this->module_title;
                                    
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_lang']        = $arr_lang;

        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['create_icon'] = $this->create_icon;
        
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }
        /*
        | store() : Exam Period store 
        | Auther  : Gaurav 
        | Date    : 10-05-2018
        */
    public function store(Request $request)
    {   
        $arr_lang =  $this->LanguageService->get_all_language(); 
        $academic_year_id = $this->academic_year;     
		$arr_rules  = [];
        
        $arr_rules['exam_name'] = ["required","regex:/^[a-zA-Z \-]+$/"];
        
        $messages = array(
                    "required" => translation('this_field_is_required'),
                    "regex" => translation('please_enter_valid_text_format')
                );    

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $slug       =  strslug(trim($request->input('exam_name')));

        $school_id  = $this->school_id;
        $obj_selected_suggession   = $this->ExamPeriodTranslationModel->where('slug',$slug)->first();
        if ($obj_selected_suggession) 
        {
            $exam_type_id             = isset($obj_selected_suggession->exam_id)?$obj_selected_suggession->exam_id:0; 
            $arr_suggession_exam_type = $obj_selected_suggession->toArray();
            $suggession_count         = count($arr_suggession_exam_type);
            
            $does_exists = $this->ExamPeriodSchoolModel->where('exam_id',$exam_type_id)
                                                     ->where('school_id',$school_id)   
                                                     ->count();
            if($does_exists>0)
            {
                Flash::error(str_singular($this->module_title).' '.translation('already_exists'));
                return redirect()->back();
            }
            if ($suggession_count>0) 
            {
                $arr_data['school_id']         = $school_id;
                $arr_data['exam_id']           = $exam_type_id;
                $arr_data['academic_year_id']  = $academic_year_id;
                
                $school_exam            = $this->ExamPeriodSchoolModel->create($arr_data);
                if ($school_exam) 
                {
                   Flash::success($this->module_title .' '.translation('created_successfully')); 
                }
            }
        }
        else
        {
            $exam                      = $this->ExamPeriodModel->create(array('school_id'=>$school_id));
            $form_data                 = $request->all();
            $exam_type_id              = $exam->id;
            if($exam)
            {
                $arr_data['school_id']         = $school_id;
                $arr_data['exam_id']           = $exam_type_id;
                $arr_data['academic_year_id']  = $academic_year_id;
                $school_exam               = $this->ExamPeriodSchoolModel->create($arr_data);

                $arr_event                 = [];
                $arr_event['ACTION']       = 'ADD';
                $arr_event['MODULE_TITLE'] = $this->module_title;
                 
                $this->save_activity($arr_event);

                if(sizeof($arr_lang) > 0 )
                {  
                    foreach ($arr_lang as $lang) 
                    {            
                        $arr_data     = array();
                            
                        $exam_type   = 'exam_name';

                        if(isset($form_data[$exam_type]) && $form_data[$exam_type] != '')
                        { 
                            $translation                 = $exam->translateOrNew($lang['locale']);
                            $translation->exam_name      = $form_data[$exam_type];
                            $translation->slug           = $slug;
                            $translation->exam_id        = $exam_type_id;
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
        | get_exam_period_suggession() : Exam Period get_exam_period_suggession 
        | Auther                       : Gaurav 
        | Date                         : 10-05-2018
        */
    public function get_exam_period_suggession(Request $request)
    {
        $arr_exam_period = [];
            $obj_exam_period = $this->ExamPeriodModel
                              ->whereHas('get_exam_period',function($q)use($request){
                                    $q->where('exam_name','LIKE', '%'.$request->keyword.'%');
                              })  
                              ->with(['get_exam_period'=>function($q) use($request){
                                    $q->where('exam_name','LIKE', '%'.$request->keyword.'%'); 
                              }])
                              ->where('is_active',1)
                              ->get();                      
            if(isset($obj_exam_period) && count($obj_exam_period)>0)
            {
                $arr_exam_period = $obj_exam_period ->toArray(); 
               
            }
        $data = json_encode($arr_exam_period);       
        return $data;
    }

 
}
