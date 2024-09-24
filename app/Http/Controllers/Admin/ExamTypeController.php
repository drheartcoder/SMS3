<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Common\Traits\MultiActionTrait;
use App\Models\ExamTypeModel;
use App\Models\ExamTypeTranslationModel;
/*Activity Log */
use App\Models\ActivityLogsModel;   
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
                                LanguageService $language,
                                ExamTypeModel $exam,
                                ExamTypeTranslationModel $translation) 
	{
        $this->arr_view_data 		    =   [];
		$this->ExamTypeModel            =   $exam;
        $this->BaseModel                =   $this->ExamTypeModel;
        $this->ExamTypeTranslationModel =   $translation;
        $this->ActivityLogsModel        =   $activity_logs; /* Activity Model */
        $this->LanguageService          =   $language;
		$this->module_url_path 		    =   url(config('app.project.admin_panel_slug')."/exam_type");
        $this->module_view_folder       =   "admin.exam_type";
        $this->module_title             =   translation("exam_type");
        $this->theme_color              =   theme_color();
        $this->module_icon              =   'fa fa-book';
        $this->create_icon              =   'fa fa-plus-circle';
        $this->edit_icon                =   'fa fa-edit';
        $this->view_icon                =   'fa fa-eye';

          /* Activity Section */
        $this->first_name               =   $this->last_name =$this->ip_address ='';
        $obj_data                       =   Sentinel::getUser();
        
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->ip_address        = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;  
        }
        /* Activity Section */
	}

	public function index() 
	{
        $this->arr_view_data['page_title'] 			= translation("manage")." ".str_singular($this->module_title);
        $this->arr_view_data['module_title'] 		= $this->module_title;
        $this->arr_view_data['module_url_path'] 	= $this->module_url_path;
        $this->arr_view_data['theme_color']         = $this->theme_color;
        $this->arr_view_data['module_icon']         = $this->module_icon;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
	}

	public function create()
    {
        $this->arr_view_data['page_title']      = translation("add")." ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']         = $this->module_icon;
        $this->arr_view_data['create_icon']         = $this->create_icon;


        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    public function store(Request $request)
    {
        $form_data = array();
        $arr_lang =  $this->LanguageService->get_all_language();


        $arr_rules['exam_type'] =  ["required","regex:/^[a-zA-Z \-]+$/"];;
        
       $messages = array(
                "required" => translation('this_field_is_required'),
                "regex" => translation('please_enter_valid_text_format')
            );    

        $validator = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails())
        {
             return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

         
        $slug =  strslug($request->input('exam_type'));
        $does_exists = $this->BaseModel->whereHas('translations',function($query) use($slug){
                                                        $query->where('slug','=',trim($slug));
                                                    })
                                                   ->count();

        if($does_exists>0)
        {
            Flash::error(str_singular($this->module_title).' '.translation('already_exists'));
            return redirect()->back();
        }  

        $exam           =    $this->BaseModel->create();
        $form_data      =    $request->all();
        $exam_type_id   =    $exam->id;
        /* Fetch All Languages*/
        
        if($exam)
        {
             /*-------------------------------------------------------
                |   Activity log Event
                --------------------------------------------------------*/
                    $arr_event                 = [];
                    $arr_event['ACTION']       = 'ADD';
                    $arr_event['MODULE_TITLE'] = $this->module_title;

                    $this->save_activity($arr_event);

                /*----------------------------------------------------------------------*/ 
                
            if(sizeof($arr_lang) > 0 )
            {  
                foreach ($arr_lang as $lang) 
                {            
                    $arr_data     = array();
                    
                    $exam_type   = 'exam_type';
                    if(isset($form_data[$exam_type]) && $form_data[$exam_type] != '')
                    {  
                        $translation = $exam->translateOrNew($lang['locale']);
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

        return redirect()->back();
    }
    public function edit($enc_id)
    {
       
        $id       = base64_decode($enc_id);
        if(!is_numeric($id))
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }
        $arr_lang = $arr_data = [];

        $arr_lang = $this->LanguageService->get_all_language();

        $obj_data = $this->BaseModel->where('id',$id)->first();

        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }
        else
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }

        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['enc_id']          = $enc_id;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['page_title']      = translation("edit")." ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['edit_icon']       = $this->edit_icon;

        return view($this->module_view_folder.'.edit',$this->arr_view_data);   
    }
    public function checkExamType(Request $request)
    {
        
        if(($this->ExamTypeTranslationModel->where('exam_type',$request->Input('exam_type'))->count())> 0)
        {
            
            return response()->json(array('status'=>'error','msg'=>translation('this_exam_type_is_already_exist')));
        }
        else
        {
            return response()->json(array('status'=>translation('success')));
        }
    }
    public function update(Request $request,$enc_id)
    {
        $form_data = array();
        $id =base64_decode($enc_id);
        /* Fetch All Languages*/
        $arr_lang =  $this->LanguageService->get_all_language();
        
        $arr_rules['exam_type'] =  ["required","regex:/^[a-zA-Z \-]+$/"];;
        
       $messages = array(
                "required" => translation('this_field_is_required'),
                "regex" => translation('please_enter_valid_text_format')
            );    
        
        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {

             return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $slug =  strslug($request->input('exam_type_'.$arr_lang[0]['locale']));
        $does_exists = $this->BaseModel->whereHas('translations',function($query) use($slug){
                                                        $query->where('slug','=',trim($slug));
                                                    })
                                                   ->where('id','!=',$id)
                                                   ->count();

        if($does_exists>0)
        {
            Flash::error(str_singular($this->module_title).' '.translation('already_exists'));
            return redirect()->back();
        }

        $fetched_exam_type = $this->BaseModel->where('id',$id)
                                        ->first();
        $form_data      =   $request->all();
        $exam_type_id   =   $fetched_exam_type->id;

        if($fetched_exam_type)
        {
                 /*-------------------------------------------------------
                |   Activity log Event
                --------------------------------------------------------*/
                    $arr_event                 = [];
                    $arr_event['ACTION']       = 'EDIT';
                    $arr_event['MODULE_TITLE'] = $this->module_title;

                    $this->save_activity($arr_event);

                /*----------------------------------------------------------------------*/ 
                     
                if(sizeof($arr_lang) > 0 )
                {  
                    foreach ($arr_lang as $lang) 
                    {       
                        $arr_data     = array();
                        $exam_type   = 'exam_type';
                        if(isset($form_data[$exam_type]) && $form_data[$exam_type] != '')
                        {  
                            $translation = $fetched_exam_type->translateOrNew($lang['locale']);
                            $translation->exam_type             = $form_data[$exam_type];
                            $translation->slug                  = $slug;
                            $translation->save();
                            
                            Flash::success($this->module_title .' '.translation('updated_successfully'));
                        }

                    }
                } 
                else
                {
                    Flash::success(translation('problem_occurred_while_updating').' '.$this->module_title);
                }
        }

        return redirect()->back();
    }
    function get_exam_type_details(Request $request)
    {     
      

        $exam_type_details                  = $this->BaseModel->getTable();
        $prefixed_exam_type_details         = DB::getTablePrefix().$this->BaseModel->getTable();

        $exam_type_trans_details            = $this->ExamTypeTranslationModel->getTable();
        $prefixed_exam_type_trans_details   = DB::getTablePrefix().$this->ExamTypeTranslationModel->getTable();

        $obj_exam_type = DB::table($exam_type_details)
                                ->select(DB::raw($prefixed_exam_type_details.".id as id,".
                                                 $prefixed_exam_type_details.".is_active as status,".
                                                 $prefixed_exam_type_details.".school_id,".
                                                 $prefixed_exam_type_trans_details.".exam_type"))
                                ->join($exam_type_trans_details,$exam_type_details.'.id','=',$exam_type_trans_details.'.exam_type_id')
                                ->where($exam_type_trans_details.'.locale','=',Session::get('locale'))
                                ->whereNull($exam_type_details.'.deleted_at')
                                ->orderBy($exam_type_details.'.created_at','DESC');
     
        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
            $obj_exam_type = $obj_exam_type->WhereRaw("( (".$exam_type_trans_details.".exam_type LIKE '%".$search_term."%' ) )");
        }

        return $obj_exam_type;
    }


    public function get_records(Request $request)
    {
        $arr_current_user_access =[];
        
        $role = Sentinel::findRoleById(1);
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;
       
        $obj_exam_type        = $this->get_exam_type_details($request);

        $current_context  = $this;

        $json_result     = Datatables::of($obj_exam_type);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('exam_type.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data) use ($current_context)
                            {
                                return base64_encode($data->id);
                            });
        }                    

        $json_result     = $json_result->editColumn('exam_type',function($data) use ($current_context,$arr_current_user_access)
                            { 
                                 
                                if($data->exam_type!=null && $data->exam_type!=''){
                                    return  ucfirst($data->exam_type);
                                }else{
                                    return  '-';
                                }

                            }) 
                            ->editColumn('build_action_btn',function($data) use ($current_context,$arr_current_user_access)
                            {
                                if($data->school_id == 0)
                                {
                                    if($data->status != null && $data->status == "0")
                                    {   

                                         $build_status_btn = '<a class="blue-color" title="'.translation('activate').'" href="'.$this->module_url_path.'/activate/'.base64_encode($data->id).'" 

                                        onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-lock"></i></a>';
                                    }
                                    elseif($data->status != null && $data->status == "1")
                                    {

                                        $build_status_btn = '<a class="light-blue-color" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" title="'.translation('deactivate').'"><i class="fa fa-unlock"></i></a>';

                                    }
                                    $build_edit_action = '';
                                    if(array_key_exists('exam_type.update',$arr_current_user_access)){

                                    $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                    $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';
                                    }
                                    $build_delete_action ='';
                                    if(array_key_exists('exam_type.update',$arr_current_user_access)){
                                    $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                    $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                    }

                                   return $build_status_btn.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_delete_action.'&nbsp;';
                               }
                            })
                            ->editColumn('build_checkbox',function($data){
                                if($data->school_id == 0){
                                    $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                    
                                    return $build_checkbox;
                                }
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    public function arrange_locale_wise(array $arr_data)
    {
        if(sizeof($arr_data)>0)
        {
            foreach ($arr_data as $key => $data) 
            {
                $arr_tmp = $data;
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