<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Common\Traits\MultiActionTrait;
/*level */
use App\Models\LevelModel;
use App\Models\LevelTranslationModel;
use App\Models\LevelClassModel;
/*class */
use App\Models\ClassModel;
use App\Models\ClassTranslationModel;
use App\Common\Services\CommonDataService;
/*Activity Log */
use App\Models\ActivityLogsModel;   
use Session;
use Validator;
use Flash;
use Sentinel;
use DB;
use Datatables;
use PDF;

class ClassController extends Controller
{
    use MultiActionTrait;

	public function __construct(ActivityLogsModel $activity_logs,
                                LanguageService $language,
                                LevelModel $level,
                                LevelTranslationModel $level_translation,
                                ClassModel $class,
                                ClassTranslationModel $class_translation,
                                LevelClassModel $level_class,
                                CommonDataService $CommonDataService
                            ) 
	{
        $this->arr_view_data 		= [];
		$this->LevelModel 	        = $level;
        $this->LevelTranslationModel= $level_translation;
        $this->ClassModel           = $class;
        $this->ClassTranslationModel= $class_translation;
        $this->BaseModel            = $this->ClassModel;
        $this->LevelClassModel      = $level_class;
        $this->ActivityLogsModel    = $activity_logs; /* Activity Model */
        $this->LanguageService      = $language;
		$this->module_url_path 		= url(config('app.project.school_admin_panel_slug')."/level_class");
        $this->module_view_folder   = "schooladmin.level_class";
        $this->module_title         = translation("level_class");
        $this->theme_color          = theme_color();

        $this->module_icon          = 'fa fa-database';
        $this->create_icon          = 'fa fa-plus-circle';
        $this->school_id            = Session::has('school_id')?Session::get('school_id'):0;
        $this->academic_year        = Session::has('academic_year')?Session::get('academic_year'):'';
        $this->CommonDataService            = $CommonDataService;

          /* Activity Section */
        $this->first_name = $this->last_name =$this->ip_address ='';
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->ip_address        = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;  
        }
        /* Activity Section */
	}

    /*
    | index() : load class manage listing page
    | Auther : sayali bhirud
    | Date : 08-05-2018
    */
    public function manage_school_classes()
    {
        
        $this->arr_view_data['page_title']          = translation("manage")." ".translation('new_classes');
        $this->arr_view_data['module_title']        = str_plural(translation('new_classes'));
        $this->arr_view_data['module_icon']         = $this->module_icon;
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['theme_color']         = $this->theme_color;
        return view($this->module_view_folder.'.manage',$this->arr_view_data);
    }

    /*
    | index() : get class records
    | Auther : sayali bhirud
    | Date : 08-05-2018
    */
    public function get_class_records(Request $request)
    {
       

        $role = Sentinel::findRoleBySlug(config('app.project.school_admin_panel_slug'));
        
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;
        
        $obj_class        = $this->get_class_details($request);

        $json_result     = Datatables::of($obj_class);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('level_class.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data) 
                            {
                                return base64_encode($data->id);
                            });
        }                    

        $json_result     = $json_result->editColumn('class_name',function($data) 
                            { 
                                $class_name =   '';
                                if($data->class_name != null)
                                {
                                    $class_name     =   $data->class_name; 
                                }  
                                        
                                return $class_name;

                            })
                            ->editColumn('build_checkbox',function($data){
                           
                                 return '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>';
                                
                            })                        
                            ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
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
                                
                                    if(array_key_exists('level_class.update',$arr_current_user_access)){
                                        $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                        $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                    }

                                   return $build_status_btn.'&nbsp;'.$build_delete_action.'&nbsp;';
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    /*
    | index() : get class details
    | Auther : sayali bhirud
    | Date : 08-05-2018
    */
    function get_class_details(Request $request,$fun_type='')
    {     
        //dd($request->all());
        $locale     = '';
        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
        $class_details                  = $this->BaseModel->getTable();
        
        $class_trans_details            = $this->ClassTranslationModel->getTable();
        $prefixed_class_trans_details   = DB::getTablePrefix().$this->ClassTranslationModel->getTable();

        $obj_class  =   DB::table($class_details)
                                  ->select(DB::raw($class_details.".id as id,".
                                                   $class_details.".is_active as status,".
                                                   $prefixed_class_trans_details.".class_name as class_name"))
                                  ->join($class_trans_details,$class_details.'.id','=',$class_trans_details.'.class_id')
                                  ->whereNull($class_details.'.deleted_at')
                                  ->where($class_details.'.school_id','=',$this->school_id)
                                  ->where($class_trans_details.'.locale','=',$locale)
                                  ->orderBy($class_details.'.created_at','DESC');
    
                           
        /* ---------------- Filtering Logic ----------------------------------*/ 
        if($fun_type=='export'){
            $search_term = $request->input('search');
        }else{

            $search = $request->input('search');
            $search_term = $search['value'];
        }                    

        if($request->has('search') && $search_term!="")
        {
            $obj_class = $obj_class->WhereRaw("( (".$class_trans_details.".class_name LIKE '%".$search_term."%'))");
        }

        if($fun_type=="export"){
            return $obj_class->get();
        }else{
            return $obj_class;
        }
       
    }

    /*
    | index() : load class create pages
    | Auther : sayali bhirud
    | Date : 08-05-2018
    */
    public function add_class()
    {
        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['page_title']          = translation('add_new_class');
        $this->arr_view_data['module_title']        = translation('new_classes');
        $this->arr_view_data['module_icon']         = $this->module_icon;
        $this->arr_view_data['create_icon']         = $this->create_icon;
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['theme_color']         = $this->theme_color;

        return view($this->module_view_folder.'.class',$this->arr_view_data);
    }

    /*
    | index() : store class against school
    | Auther : sayali bhirud
    | Date : 08-05-2018
    */
    public function store(Request $request)
    {
        
        $form_data = array();
        /* Fetch All Languages*/
        $arr_lang =  $this->LanguageService->get_all_language();
        
        $arr_rules['class_name']          = ["required","regex:/^[a-zA-Z0-9 ]+$/"];  
           
        $messages['required'] = translation('this_field_is_required');
        $messages['regex']    = translation('please_enter_valid_text_format');

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
             return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        $name =  strslug($request->input('class_name_'.$arr_lang[0]['locale']));
        $does_exists = $this->BaseModel->where('school_id',$this->school_id)->whereHas('translations',function($query) use($name){
                                                        $query->where('class_name','=',trim($name));
                                                    })
                                                   ->count();
        
        if($does_exists>0)
        {
            Flash::error(translation('class').' '.translation('already_exists'));
            return redirect()->back();
        }  
        $data['school_id']          = $this->school_id;
        $data['academic_year_id']   = $this->academic_year;
        $class                      = $this->BaseModel->create($data);
        $form_data                  = $request->all();
        $class_id                   = $class->id;
        
        if($class)
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
                       
                        
                        $class_name   = 'class_name';
                        
                        if(isset($form_data[$class_name]) && $form_data[$class_name] != '')
                        {  
                            $translation = $class->translateOrNew($lang['locale']);
                            
                            $translation->class_name      = $form_data[$class_name];
                            $translation->class_id        = $class_id;

                            $translation->save();
                            
                            Flash::success(translation('class') .' '.translation('created_successfully'));
                        }
                    }
                } 
                else
                {
                    Flash::success(translation('problem_occured_whil_creating').' '.translation('class'));
                }
        }

        return redirect()->back();
    }


    public function export(Request $request)
    {      
        $obj_data = $this->get_class_details($request,'export');
        if(sizeof($obj_data)<=0){
            Flash::error(translation("no_records_found_to_export"));
            return redirect()->back();
        }
        if(sizeof($obj_data)>500 && $request->file_format == 'csv'){
            Flash::error(translation("too_many_records_to_export"));
            return redirect()->back();
        }
        if($request->file_format == 'csv'){
            \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel) use($obj_data) 
                {
                    $excel->sheet(ucwords($this->module_title), function($sheet) use($obj_data) 
                    {
                        $arr_fields['id']             = translation('sr_no');
                        $arr_fields['Class Name']     = translation('class_name');
                        
                        
                        $sheet->row(2, ['',ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                        $sheet->row(4, $arr_fields);
                        if(sizeof($obj_data)>0) 
                        {
                            
                            $arr_tmp = [];
                            foreach($obj_data as $key => $result)
                            { 
                                
                                $arr_tmp[$key]['id']             = intval($key+1);
                                $arr_tmp[$key]['class_name']   = $result->class_name;
                            }
                            $sheet->rows($arr_tmp);
                        }
                    });
                })->export('csv');     
        }
        
        if($request->file_format == 'pdf')
        {
            $school_name = $this->CommonDataService->get_school_name();

            $school_address = $this->CommonDataService->get_school_address();

            $school_email = $this->CommonDataService->get_school_email();

            $school_logo = $this->CommonDataService->get_school_logo();

            $this->arr_view_data['arr_data']      = $obj_data;
            $this->arr_view_data['school_name']   = $school_name;    
            $this->arr_view_data['school_address']= $school_address;
            $this->arr_view_data['school_email']  = $school_email;
            $this->arr_view_data['school_logo']   = $school_logo;

            $pdf = PDF::loadView($this->module_view_folder.'.pdf', $this->arr_view_data);
            return $pdf->stream($this->module_view_folder.'.pdf', $this->arr_view_data);
        }
    }

}
