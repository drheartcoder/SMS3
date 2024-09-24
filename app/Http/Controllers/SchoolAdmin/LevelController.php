<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Common\Services\CommonDataService;

use App\Common\Traits\MultiActionTrait;
/*level */
use App\Models\LevelModel;
use App\Models\LevelTranslationModel;
use App\Models\LevelClassModel;

use App\Models\AcademicYearModel;  
/*class */
use App\Models\ClassModel;
use App\Models\ClassTranslationModel;
/*Activity Log */
use App\Models\ActivityLogsModel;   
use Session;
use Validator;
use Flash;
use Sentinel;
use DB;
use Datatables;

class LevelController extends Controller
{
    use MultiActionTrait;

	public function __construct(ActivityLogsModel $activity_logs,
                                LanguageService $language,
                                LevelModel $level,
                                LevelTranslationModel $level_translation,
                                ClassModel $class,
                                ClassTranslationModel $class_translation,
                                LevelClassModel $level_class,
                                AcademicYearModel $year,
                                CommonDataService $CommonDataService
                            ) 
	{
        $this->arr_view_data 		= [];
		$this->LevelModel 	        = $level;
        $this->LevelTranslationModel= $level_translation;
        $this->AcademicYearModel    = $year;
        $this->CommonDataService    = $CommonDataService;

        $this->ClassModel           = $class;
        $this->ClassTranslationModel= $class_translation;
        $this->LevelClassModel      = $level_class;
        $this->BaseModel            = $this->LevelClassModel;
        $this->ActivityLogsModel    = $activity_logs; /* Activity Model */
        $this->LanguageService      = $language;
		$this->module_url_path 		= url(config('app.project.school_admin_panel_slug')."/level_class");
        $this->module_view_folder   = "schooladmin.level_class";
        $this->module_title         = translation("level_class");
        $this->theme_color          = theme_color();
        $this->module_icon          = 'fa fa-server';
        $this->create_icon          = 'fa fa-plus-circle';
        $this->school_id            = Session::get('school_id');
        $this->academic_year        = Session::get('academic_year');

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
    | index() : load level & class listing page
    | Auther : sayali bhirud
    | Date : 07-05-2018
    */
	public function index() 
	{	

		$arr_level  = $details =  [];
        $acdemic_year_arr = $this->CommonDataService->get_academic_year_less_than($this->academic_year);

        if($acdemic_year_arr){
            $acdemic_year_arr  = explode(',',$acdemic_year_arr);
        }
        
        $level = $this->LevelClassModel
                        ->with('class_details')
                        ->where('school_id',$this->school_id)
                        ->whereIn('academic_year_id',$acdemic_year_arr)
                        ->get();

        if(isset($level))
        {
            $details =$level->toArray();
        }

        /*dd($details);*/

        $obj_level = $this->LevelClassModel
                          ->with('level_details')
                          ->where('school_id',$this->school_id)
                          ->whereIn('academic_year_id',$acdemic_year_arr)
                          ->groupBy('level_id')
                          ->get();
                                
        if($obj_level != FALSE)
        {
            $arr_level = $obj_level->toArray();
        }

      	$this->arr_view_data['arr_level']                      = $arr_level;
        $this->arr_view_data['details']                        = $details;
        $this->arr_view_data['page_title'] 			           = translation("manage")." ".str_singular($this->module_title);
        $this->arr_view_data['module_title'] 		           = str_plural($this->module_title);
        $this->arr_view_data['module_icon']                    = $this->module_icon;
        $this->arr_view_data['module_url_path'] 	           = $this->module_url_path;
        $this->arr_view_data['theme_color']                    = $this->theme_color;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
	}


     /*
    | index() : load level & class create page
    | Auther : sayali bhirud
    | Date : 05-05-2018
    */
	public function create()
    {
        $arr_data = $arr_level = $arr_class = [];
        $obj_level  =   $this->LevelModel
                             ->where('is_active',1)
                             ->orderBy('level_order')
                             ->get();
        
        $obj_class  =   $this->ClassModel
                             ->where('is_active',1)
                             ->where('school_id',$this->school_id)
                             ->get();

        if(isset($obj_level) && !empty($obj_level))
        {
            $arr_level = $obj_level->toArray();
            
        }

        if(isset($obj_class) && !empty($obj_class)) 
        {
            $arr_class = $obj_class->toArray();
        }

        if(!empty($arr_level))
        {
            $arr_data['level'] = $arr_level;
        }

        if(!empty($arr_class))
        {
            $arr_data['class'] = $arr_class;
        }
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['page_title']      = translation("create")." ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']         = $this->module_icon;
        $this->arr_view_data['create_icon']         = $this->create_icon;


        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }
     /*
    | index() : store level & class against school
    | Auther : sayali bhirud
    | Date : 05-05-2018
    */
    public function store(Request $request)
    {
        $arr_rules['level']          = "required";  

        $arr_rules['class']          = "required";  

        $messages['required']    =   'This field is required';

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
             return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        
        if(!empty($request->input('class')))
        {
            $classes =    $request->input('class');
            foreach ($classes as $class) {
                $level  =   $this->LevelClassModel
                                 ->where('level_id',$request->input('level'))
                                 ->where('class_id',$class)
                                 ->first();
                if($level)
                {
                    continue;
                }
                else
                {
                    $level = $this->LevelModel->where('id',$request->input('level'))->first();

                    $data['level_id']          =   $request->input('level');
                    $data['class_id']          =   $class;
                    $data['school_id']         =   $this->school_id;
                    $data['academic_year_id']  =   $this->academic_year;
                    $data['position']  =   $level->level_order;
                    
                    $this->LevelClassModel->create($data);
                }
            }
        }

        Flash::success($this->module_title.' '.translation('assigned_successfully'));   
        return redirect()->back();
    }

    /*
    | index() : load level & class edit page
    | Auther : sayali bhirud
    | Date : 08-05-2018
    */
    public function edit($enc_id)
    {
        
        $id     =   base64_decode($enc_id);
        
        $arr_level = $arr_class = $arr_school_class =  [];
        
        $obj_level = $this->LevelModel->get();
        if($obj_level!=FALSE)
        {
            $arr_level = $obj_level->toArray();
        }

        $obj_class = $this->ClassModel->where('school_id',$this->school_id)->where('is_active',1)->get();
       
        if($obj_class!=FALSE)
        {
            $arr_class = $obj_class->toArray();
        }

        $arr_school_group_by_level_id = [];
        $obj_school_group_by_level_id = $this->LevelClassModel
                                             ->where('school_id',$this->school_id)
                                             ->groupBy('level_id')
                                             ->where('level_id',$id)
                                             ->first();

        $obj_school_classes           = $this->LevelClassModel
                                             ->where('school_id',$this->school_id)
                                             ->where('level_id',$id)
                                             ->get(['class_id']);


        if($obj_school_group_by_level_id!=FALSE)
        {
            $arr_school_group_by_level_id = $obj_school_group_by_level_id->toArray();
        }

        
        if($obj_school_classes!=FALSE)
        {
            $arr_school_class = $obj_school_classes->toArray();
        }
        
        if(isset($arr_school_group_by_level_id) && !empty($arr_school_group_by_level_id))
        {
            $this->arr_view_data['arr_school_level']             = $arr_school_group_by_level_id;
        }
        
        if(isset($arr_school_class) && !empty($arr_school_class))
        {
            $this->arr_view_data['arr_school_class']             = $arr_school_class;
        }

        if(isset($arr_level) && !empty($arr_level))
        {
            $this->arr_view_data['arr_level']             = $arr_level;
        }

        if(isset($arr_class) && !empty($arr_class))
        {
            $this->arr_view_data['arr_class']             = $arr_class;
        }
        
        $this->arr_view_data['id']              = $id;
        $this->arr_view_data['page_title']      = translation("edit")." ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['edit_icon']       = 'fa fa-edit';
        $this->arr_view_data['module_url_path'] = $this->module_url_path;

        return view($this->module_view_folder.'.edit',$this->arr_view_data);   
    }

     /*
    | index() : update level & class 
    | Auther : sayali bhirud
    | Date : 08-05-2018
    */
    public function update(Request $request,$enc_id)
    {
        //dd($this->academic_year,$request->all());
        $arr_rules['level']          = "required";  
        $arr_rules['class']          = "required";  

        $messages['required']    =   'This field is required';

        $validator = Validator::make($request->all(),$arr_rules,$messages);
        
        if($validator->fails())
        {
             return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        
        $level_id = $request->input('level');
        $class_id = $request->input('class');
        //dd($this->LevelClassModel->where('level_id',$level_id)->where('school_id',$this->school_id)->get()->toArray());

        $delete_status = $this->LevelClassModel->where('level_id',$level_id)->where('school_id',$this->school_id)->delete();

        if($delete_status>0)
        {
            if(sizeof($class_id)>0)
            {
                $arr_insert = array();
                foreach ($class_id as $class_key => $value) 
                {    
                    $level = $this->LevelModel->where('id',$level_id)->first();
                    
                    $arr_insert[$class_key]['school_id']         = $this->school_id;
                    $arr_insert[$class_key]['level_id']          = $level_id;
                    $arr_insert[$class_key]['class_id']          = $value;  
                    $arr_insert[$class_key]['academic_year_id']  = $this->academic_year;
                    $arr_insert[$class_key]['position']          = $level->level_order;  
                }
                /*iterate arrat and insert into SchoolLevelModel table*/
                if(isset($arr_insert) && sizeof($arr_insert)>0)
                {
                    foreach ($arr_insert as $insert) 
                    {
                        if(isset($insert) && sizeof($insert)>0)
                        {
                            $status = $this->LevelClassModel->create($insert);
                        }
                    }
                }
                if($status)
                {
                    Flash::success(str_singular($this->module_title).' '.translation('updated_successfully'));
                }
                else
                {
                    Flash::error(translation('problem_occured_while_updating').' '.str_singular($this->module_title));
                }
            }
        }  
        else
        {
            Flash::error(translation('problem_occured_while_updating').' '.str_singular($this->module_title));
        }
        return redirect()->back();
    }

    /*
    | index() : delete level & class 
    | Auther : sayali bhirud
    | Date : 08-05-2018
    */
    public function delete($enc_id)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_delete(base64_decode($enc_id)))
        {
            Flash::success($this->module_title.' deleted successfully');
        }
        else
        {
            Flash::error('Problem occured while '.$this->module_title.' deletion ');
        }
        return redirect()->back();
    }

     public function perform_delete($id)
    {
        $level              =   $this->LevelClassModel->where('level_id',$id)->delete();       
        if($level)
        {
            return TRUE;
        }

        return FALSE;
    }

    /*
    | index() : arrage level & class locale wise 
    | Auther : sayali bhirud
    | Date : 07-05-2018
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

    /*
    | index() : check existing level & class 
    | Auther : sayali bhirud
    | Date : 08-05-2018
    */
    public function checkLevel(Request $request)
    {
        $level =    $request->input('level');
        $exist =    $this->LevelClassModel->where('level_id',$level)->where('school_id',$this->school_id)->get();
        if($exist->count()>0)
        {
            return response()->json(array('status'=>'error','msg'=>translation('this_level_is_already_assigned')));
        }
        else
        {
            return response()->json(array('status'=>'success'));
        }
    }
}
