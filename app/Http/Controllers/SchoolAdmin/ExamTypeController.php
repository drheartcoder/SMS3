<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Common\Traits\MultiActionTrait;
use App\Models\ExamTypeModel;
use App\Models\SchoolExamTypeModel;

use App\Models\ExamTypeTranslationModel;
use App\Models\ActivityLogsModel;   
use App\Models\AcademicYearModel; 
use App\Common\Services\CommonDataService;

use Session;
use Validator;
use Flash;
use Sentinel;
use DB;
use Datatables;
                                
class ExamTypeController extends Controller
{                         
    use MultiActionTrait;

    public function __construct(ActivityLogsModel $activity_logs,
                                CommonDataService $CommonDataService,
                                LanguageService $language,
                                SchoolExamTypeModel $school_exam_type,
                                ExamTypeModel $exam,
                                ExamTypeTranslationModel $translation,
                                AcademicYearModel $year) 
    {
        $this->arr_view_data            =   [];
        $this->ExamTypeModel            =   $exam;
        $this->SchoolExamTypeModel      =   $school_exam_type;
        $this->BaseModel                =   $this->SchoolExamTypeModel;
        $this->ExamTypeTranslationModel =   $translation;
        $this->CommonDataService        =   $CommonDataService;
        $this->ActivityLogsModel        =   $activity_logs; 
        $this->LanguageService          =   $language;
        $this->AcademicYearModel        =   $year;
        $this->module_url_path          =   url(config('app.project.school_admin_panel_slug')."/exam_type");
        $this->module_view_folder       =   "schooladmin.exam_type";
        $this->module_title             =   translation("exam_type");
        $this->theme_color              =   theme_color();
        $this->school_id                =   Session::get('school_id');
        $this->academic_year            =   Session::get('academic_year');
        $this->first_name               =   $this->last_name =$this->ip_address ='';

        $this->arr_view_data['page_title']          = $this->module_title;

        $obj_data                       =   Sentinel::getUser();
            
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->ip_address        = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;  
        }
    }
    /*
    | index() : Exam Type listing 
    | Auther : Gaurav 
    | Date : 09-05-2018
    */
    public function index() 
    {
       
        $this->arr_view_data['module_title']        = ucfirst(translation("manage"))." ".$this->module_title;
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['theme_color']         = $this->theme_color;
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    /*
    | create() : Exam Type create 
    | Auther : Gaurav 
    | Date : 09-05-2018
    */
    public function create()
    {
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['module_title']    = translation("add")." ".str_singular( $this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }
        /*
        | store() : Exam Type store 
        | Auther : Gaurav 
        | Date : 09-05-2018
        */        
    public function store(Request $request)
    {

        
        $suggession_count = 0;
        $academic_year_id = $this->academic_year;
        $arr_suggession_exam_type = [];
        
        $arr_lang = $this->LanguageService->get_all_language();

        $arr_rules['exam_type'] = "required|regex:/^[a-zA-Z \-]+$/";
        $arr_rules['gradebook'] = ["required","regex:/^(test|other)$/"];
        
            
        $messages['required']   =   translation('this_field_is_required');
        $messages['regex']      =   translation('please_enter_valid_text_format');

        $validator = Validator::make($request->all(),$arr_rules,$messages);
       
        if($validator->fails())
        {
             return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $slug                      =  strslug($request->input('exam_type'));
        $school_id                 = $this->school_id;
        $obj_selected_suggession   = $this->ExamTypeTranslationModel->where('slug',$slug)->first();
        if($request->gradebook == 'other')
            $gradebook = 1;
        else
            $gradebook = 0;
        
        if(isset($obj_selected_suggession) && count($obj_selected_suggession)>0 ) 
        {
            $exam_type_id             = isset($obj_selected_suggession->exam_type_id)?$obj_selected_suggession->exam_type_id:0; 
            $arr_suggession_exam_type = $obj_selected_suggession->toArray();
            $suggession_count         = count($arr_suggession_exam_type);
            
            $does_exists = $this->SchoolExamTypeModel->where('exam_type_id',$exam_type_id)
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
                $arr_data['exam_type_id']      = $exam_type_id;
                $arr_data['academic_year_id']  = $academic_year_id;
                $arr_data['gradebook']         = $gradebook;
                $school_exam                   = $this->SchoolExamTypeModel->create($arr_data);
                if ($school_exam) 
                {
                   Flash::success($this->module_title .' '.translation('created_successfully')); 
                }
            }
        }    
        else
        {
            $exam                      = $this->ExamTypeModel->create(array('school_id'=>$school_id));
            $form_data                 = $request->all();
            $exam_type_id              = $exam->id;
            if($exam)
            {
            
             $arr_data['school_id']         = $school_id;
             $arr_data['exam_type_id']      = $exam_type_id;
             $arr_data['academic_year_id']  = $academic_year_id;
             $arr_data['gradebook']         = $gradebook;
             
             $school_exam                   = $this->SchoolExamTypeModel->create($arr_data);

                $arr_event                 = [];
                $arr_event['ACTION']       = 'ADD';
                $arr_event['MODULE_TITLE'] = $this->module_title;

                $this->save_activity($arr_event);

                if(sizeof($arr_lang) > 0 )
                {  
                    foreach ($arr_lang as $lang) 
                    {            
                        $arr_data     = array();
                        
                        $exam_type   = 'exam_type';
                        if(isset($form_data[$exam_type]) && $form_data[$exam_type] != '')
                        {  
                            $translation                 = $exam->translateOrNew($lang['locale']);
                            $translation->exam_type      = $form_data[$exam_type];
                            $translation->slug           = $slug;
                            $translation->exam_type_id   = $exam_type_id;
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
    | get_exam_type_details() : Exam Type details using ajax 
    | Auther                  : Gaurav 
    | Date                    : 09-05-2018
    */
    function get_exam_type_details(Request $request)
    {     
        $school_id        = $this->school_id;
        $academic_year_id = $this->academic_year;
       
        $str                =  $this->CommonDataService->get_academic_year_less_than($academic_year_id);
        $arr_academic_year  = explode(',', $str);
        $school_exam_type_trans_details            = $this->BaseModel->getTable();
        $prefixed_school_exam_type_details   = DB::getTablePrefix().$this->BaseModel->getTable();

        $exam_type_trans_details            = $this->ExamTypeTranslationModel->getTable();
        $prefixed_exam_type_trans_details   = DB::getTablePrefix().$this->ExamTypeTranslationModel->getTable();


        $obj_exam_type = DB::table($school_exam_type_trans_details)
                                ->select(DB::raw($prefixed_school_exam_type_details.".id as id,".
                                                 $prefixed_school_exam_type_details.".is_active as status,".
                                                 $prefixed_school_exam_type_details.".school_id,".
                                                 $prefixed_school_exam_type_details.".exam_type_id,".
                                                 $prefixed_school_exam_type_details.".gradebook,". 
                                                 $prefixed_exam_type_trans_details.".exam_type"))
                                ->join($exam_type_trans_details,$school_exam_type_trans_details.'.exam_type_id','=',$exam_type_trans_details.'.exam_type_id')
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
            $obj_exam_type = $obj_exam_type->WhereRaw("( (".$prefixed_exam_type_trans_details.".exam_type LIKE '%".$search_term."%') )");
        }
        return $obj_exam_type;   
    }

    /*
    | get_records() : Exam Type get_records 
    | Auther        : Gaurav 
    | Date          : 09-05-2018
    */
    public function get_records(Request $request)
    {
        $arr_current_user_access =[];
        
        $arr_current_user_access = $this->CommonDataService->current_user_access();

        
        $obj_exam_type        = $this->get_exam_type_details($request);

        

        $json_result     = Datatables::of($obj_exam_type);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('exam_type.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data) 
                            {
                                return base64_encode($data->id);
                            });
        }                    

        $json_result     = $json_result
                            ->editColumn('exam_type',function($data) 
                            { 
                                 
                                if($data->exam_type!=null && $data->exam_type!=''){
                                    return  ucfirst($data->exam_type);
                                }else{
                                    return  '-';
                                }

                            })
                            ->editColumn('build_gradebook',function($data) 
                            { 
                                 
                                if($data->gradebook=='1'){
                                    return  translation('yes');
                                }else{
                                    return  translation('no');
                                }

                            }) 
                            ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                            {
                                    $build_status_btn = '';
                                if(array_key_exists('exam_type.update',$arr_current_user_access))
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
                                    
                                if(array_key_exists('exam_type.delete',$arr_current_user_access))
                                {
                                    $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                    $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                }

                                return $build_status_btn.'&nbsp;'.$build_delete_action.'&nbsp;';
                            
                            })
                            ->editColumn('build_checkbox',function($data) use($arr_current_user_access){
                                $build_checkbox='';
                                if(array_key_exists('exam_type.update',$arr_current_user_access) || array_key_exists('exam_type.delete',$arr_current_user_access))
                                {
                                    $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                    
                                    return $build_checkbox;
                                }
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }
        /*
        | get_exam_type_suggession() : Exam Type get_exam_type_suggession 
        | Auther      : Gaurav 
        | Date        : 10-05-2018
        */
    public function get_exam_type_suggession(Request $request)
    {
        $arr_exam_type = [];
    
            $obj_exam_type = $this->ExamTypeModel
                              ->whereHas('get_exam_type',function($q)use($request){
                                    $q->where('exam_type','LIKE', '%'.$request->keyword.'%');
                              })  
                              ->with(['get_exam_type'=>function($q) use($request){
                                    $q->where('exam_type','LIKE', '%'.$request->keyword.'%'); 
                              }])
                              ->where('is_active',1)
                              ->get();                      
            if(isset($obj_exam_type) && count($obj_exam_type)>0)
            {
                $arr_exam_type = $obj_exam_type ->toArray(); 
               
            }
        $data = json_encode($arr_exam_type);       
        return $data;
    }

    /*
    | arrange_locale_wise() : Exam Type arrange_locale_wise 
    | Auther                : Gaurav 
    | Date                  : 09-05-2018
    */
    public function arrange_locale_wise(array $arr_data)
    {
        if(sizeof($arr_data)>0)
        {
            foreach ($arr_data as $key => $data) 
            {
                unset($arr_data[$key]);
                $arr_data[$data['locale']] = $data;                    
            }

            return $arr_data;
        }
        else
        {
            return [];
        }
    } 
}
