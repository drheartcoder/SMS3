<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Common\Traits\MultiActionTrait;

use App\Http\Requests;
use App\Models\LevelModel;
use App\Models\ClassModel;
use App\Models\CourseModel;
use App\Models\LevelClassModel;
use App\Models\SchoolSubjectsModel;
Use App\Models\ClassTranslationModel;
Use App\Models\LevelTranslationModel;
use App\Models\SchoolCourseModel;

use App\Common\Services\CommonDataService;


use DB;
use Flash;
use Sentinel;
use Session;
use Validator;
use Datatables;

class AssignCoursesController extends Controller
{
    use MultiActionTrait;
    	
    public function __construct( 
                                SchoolSubjectsModel $school_subject,
                                ClassTranslationModel $class_translation_model,
                                LevelTranslationModel $level_translation_model,
                                CommonDataService $common_data_service,
                                SchoolCourseModel $SchoolCourseModel
                                )
    {
    	
    	$this->SchoolSubjectsModel        = $school_subject;
    	$this->BaseModel                  = $this->SchoolSubjectsModel;
        $this->ClassTranslationModel      = $class_translation_model;
        $this->LevelTranslationModel      = $level_translation_model;
        $this->CommonDataService          = $common_data_service;
        $this->SchoolCourseModel          = $SchoolCourseModel;
    	
		
		$this->module_url_path 	          = url(config('app.project.role_slug.school_admin_role_slug')."/assign_courses");
		$this->module_view_folder         = "schooladmin.assign_courses";
		$this->module_title               = translation('assigned_course');
		$this->theme_color                = theme_color();
		$this->module_icon                = 'fa fa-book';
		$this->create_icon                = 'fa fa-plus-circle';
		$this->edit_icon                  = 'fa fa-edit';
        $this->school_id                  =  \Session::has('school_id') ? \Session::get('school_id') : '0'; 
        $this->academic_year              = Session::get('academic_year');

        $this->arr_view_data['page_title']      = $this->module_title;

        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
            $this->user_id           = $obj_data->id;  
        }

        if(Session::has('locale'))
        {
            $this->locale = Session::get('locale');
        }
        else
        {
            $this->locale = 'en';
        }

     }

    /*
    | index() : List  
    | Auther  : Padmashri
    | Date    : 28-05-2018
    */
    public function index(){

        
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_title']    = translation('manage').' '.str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    /*
    | get_details() : To get the List Subjects Assigned
    | Auther  : Padmashri
    | Date    : 29-05-2018
    */
    public function get_details(Request $request){

    	$locale = $this->locale;

       
        $assignDetails             = $this->BaseModel->getTable();
        $prefixed_assignDetails    = DB::getTablePrefix().$this->BaseModel->getTable();
        $level_trans 			   = $this->LevelTranslationModel->getTable();
        $class_trans               = $this->ClassTranslationModel->getTable();
        
        $obj_user = DB::table($assignDetails)
                                ->select(DB::raw(
                                				 $prefixed_assignDetails.".id  as id,".
                                				 $prefixed_assignDetails.".level_id, ".
                                                 $prefixed_assignDetails.".class_id, ".
                                                 $prefixed_assignDetails.".json_subjects, ".
                                                 $level_trans.".level_name,".
                                             	 $class_trans.".class_name"))
                                ->join($level_trans,$level_trans.'.level_id', ' = ',$prefixed_assignDetails.'.level_id')
                                ->join($class_trans,$class_trans.'.class_id', ' = ',$prefixed_assignDetails.'.class_id')
                                ->where($prefixed_assignDetails.'.academic_year_id',$this->academic_year)
                                ->where($level_trans.'.locale','=',$locale)
                                ->where($class_trans.'.locale','=',$locale)
                                ->where($prefixed_assignDetails.'.school_id','=',$this->school_id)
                                ->whereNull($prefixed_assignDetails.'.deleted_at')
                                ->orderBy($prefixed_assignDetails.'.id','desc');


         
        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
            $obj_user = $obj_user->WhereRaw("((".$class_trans.".class_name LIKE '%".$search_term."%')   ")
                                     ->orWhereRaw("(".$level_trans.".level_name LIKE '%".$search_term."%')) ");
        }
        /* ---------------- Filtering Logic ----------------------------------*/                    
        return $obj_user;
    }

    /*
    | get_records() : To get the List Room Assignment
    | Auther  : Padmashri
    | Date    : 8-05-2018
    */
    public function get_records(Request $request){

        $arr_current_user_access =[];
    
        $role = Sentinel::findRoleBySlug(config('app.project.school_admin_panel_slug'));
        
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;

        $obj_user        = $this->get_details($request);
        $current_context = $this;

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
      
            $json_result     = $json_result->editColumn('enc_id',function($data) use ($current_context){
                                return base64_encode($data->id);
                            });
                          
        
            
             $json_result     = $json_result->editColumn('build_action_btn',function($data) use ($current_context,$arr_current_user_access){
                                           
                                $build_delete_action =  $build_edit_action =  ''; 
                               if(array_key_exists('course_assignement.update', $arr_current_user_access))
                               {
                                    $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                    $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';
                               }
                                return $build_edit_action.'&nbsp;';
                                });
         
        $json_result =      $json_result->editColumn('build_checkbox',function($data){
                           
                                $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                
                                return $build_checkbox;
                                })
       						    ->editColumn('subjects',function($data){
                                    $strSubjects = '';
                                    if($data->json_subjects!=''){
                                        $strSubjects = $this->CommonDataService->get_class_wise_subjects($data->json_subjects); 
                                        
                                    }
                                return $strSubjects;
                                })
                                ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    /*
    | create() : Create Course Assignment
    | Auther  : Padmashri
    | Date    : 28-05-2018
    */
    public function create(){

        $obj_level = $arr_level = [];
        $obj_level  =   $this->CommonDataService->get_levels($this->academic_year);
        
    	if($obj_level){
    		$arr_level = $obj_level->toArray();
    	}	
        
        /*$course     = [];
        $obj_course = $this->CommonDataService->get_courses($this->academic_year,config('app.project.role_slug.school_admin_role_slug'),$this->user_id);
        if($obj_course){
            $course = $obj_course->toArray();
        }*/
        $this->arr_view_data['module_title']    = translation('assign_courses');
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['create_icon'] = $this->create_icon;
        $this->arr_view_data['arr_level']   = $arr_level;
        /*$this->arr_view_data['course']      = $course;*/
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    /*
    | store() : Store  Room Assignment
    | Auther  : Padmashri
    | Date    : 8-05-2018
    */
    public function store(Request $request){
        
        $arr_rules = [];
        $arr_rules['assign_level']    		= 'required';
        $flag = 0;
        $cnt_duplicate = 0;


        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        
        $level_id     =   trim($request->input('assign_level'));
        $class_id     =   $request->input('assign_class');
		$subject      =   $request->input('subject');

        if(!empty($class_id)){
            for($i=0;$i<count($class_id);$i++){

                $arr_data = [];     
                $arr_data['school_id']          = $this->school_id;
                $arr_data['academic_year_id']   = $this->academic_year;
                $arr_data['level_id']           = $level_id;
                $arr_data['class_id']           = $class_id[$i];
                $arr_data['json_subjects']      = $subject;

                $is_duplicate = $this->is_duplicate_class_section($arr_data);
                
                if($is_duplicate == 'true'){
                    $cnt_duplicate++;
                }else{
                    $res = $this->SchoolSubjectsModel->create($arr_data);
                    $flag = 1;
                }
            }
        }else{
            Flash::error("something_went_wrong_while_creating ".$this->module_title);
            return redirect()->back();
        }
        
        /*GTE LEVEL CLASS ID */
        //$levelClassId = $this->getLevelClassId($assign_level,$assign_class);
        /*GTE LEVEL CLASS ID */

        /* Bring the total no of rooms as per floor  */
        if($cnt_duplicate > 0){
            Flash::error($this->module_title." ".translation("some_records_are_duplicate"));
        }else{
            Flash::success($this->module_title." ".translation("created_successfully"));
            
        }        
        return redirect()->back();

    }
     /*
    | edit()  : Edit   
    | Auther  : Padmashri
    | Date    : 29-05-2018
    */
    public function edit($enc_id=FALSE)
    {
        $id = base64_decode($enc_id);

        $arr_course_assign = $obj_course_assign = $subject =  array();
        
        $academic_year = $this->academic_year;
        $academic_year = $this->CommonDataService->get_academic_year_less_than($academic_year);
        $arr_academic_year = explode(',',$academic_year);

        $obj_course_assign = SchoolSubjectsModel::with('get_level','get_class')->where('school_id',$this->school_id)->where('id','=',$id)->first();
        if($obj_course_assign){
            $arr_course_assign = $obj_course_assign->toArray();
            
            $subject =$arr_course_assign['json_subjects'];
            
        }  
        
        $obj_level = $arr_level = [];
        $obj_level  =   LevelClassModel::where('school_id',$this->school_id)
                                        ->whereHas('get_level',function($q){
                                                $q->where('is_active','=',1);
                                            })
                                        ->with(['get_level' => function($q){
                                                $q->where('is_active','=',1);
                                        }])
                                        ->groupBy('level_id')
                                        ->get();
        if($obj_level){
            $arr_level = $obj_level->toArray();
        }   
        

        $obj_class = $arr_class = array();
        $obj_class = LevelClassModel::where('school_id',$this->school_id)
                                        ->whereHas('get_class',function($q){
                                                $q->where('is_active','=',1);
                                            })
                                        ->with(['get_class' => function($q){
                                                $q->where('is_active','=',1);
                                            }])
                                        ->where('level_id','=',$arr_course_assign['level_id'])
                                        ->get();
        if($obj_class){
            $arr_class = $obj_class->toArray();
        }
        


        $course     = [];
        $obj_course =    $this->SchoolCourseModel
                              ->with('get_course')
                              ->where('school_level_id',$arr_course_assign['level_id'])
                              ->where('school_id',$this->school_id)
                              ->whereIn('academic_year_id',$arr_academic_year)
                              ->get();
        if($obj_course){
            $course = $obj_course->toArray();
        }

        $this->arr_view_data['page_title']      = translation('edit')." ".$this->module_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['enc_id']          = $enc_id;
        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['edit_icon']   = $this->edit_icon;
        $this->arr_view_data['arr_courses'] = $course;
        $this->arr_view_data['arr_class']   = $arr_class;
        $this->arr_view_data['arr_level']   = $arr_level;
        $this->arr_view_data['arr_data']    = $arr_course_assign;
        $this->arr_view_data['subject']     = $subject;
        
        
        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }

    /*
    | edit()  : Update  Room Assignment
    | Auther  : Padmashri
    | Date    : 8-05-2018
    */
    public function update(Request $request,$enc_id=FALSE)
    {

        $id = base64_decode($enc_id);     

        $arr_rules['assign_level']         = 'required';
        $arr_rules['assign_class']         = 'required';

        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $level_id     =   trim($request->input('assign_level'));
        $class_id     =   trim($request->input('assign_class'));
        $subject      =   $request->input('subject');

        $arr_data = [];     
        $arr_data['school_id']          = $this->school_id;
        $arr_data['academic_year_id']   = $this->academic_year;
        $arr_data['level_id']           = $level_id;
        $arr_data['class_id']           = $class_id;
        $arr_data['json_subjects']      = json_encode($subject);
        
        $is_duplicate = $this->is_duplicate_class_section($arr_data,$id);

        if($is_duplicate == 'TRUE'){
            Flash::error($this->module_title." ".translation("already_exists"));
            return redirect()->back();
        }else{
            $res = $this->SchoolSubjectsModel->where('id',$id)->update($arr_data);
        }
        
        if($res){
            Flash::success($this->module_title." ".translation("updated_successfully"));
        }else{
            Flash::error("something_went_wrong_while_updating ".$this->module_title);
        }        
        return redirect()->back();
       
    }
 

    public function is_duplicate_class_section($arr_insert,$id=0)
    {   
        if($id>0){
            $count_obj_class_section = $this->BaseModel->where([
                                                                'school_id'         => $this->school_id,
                                                                'class_id'          => $arr_insert['class_id'],
                                                                'level_id'          => $arr_insert['level_id'],
                                                                'academic_year_id'  =>$this->academic_year,
                                                        ])
                                                    ->where('id','<>',$id)
                                                    ->count();

        }else{
            $count_obj_class_section = $this->BaseModel->where([
                                                                'school_id'         => $this->school_id,
                                                                'class_id'          => $arr_insert['class_id'],
                                                                'level_id'          => $arr_insert['level_id'],
                                                                'academic_year_id'  =>$this->academic_year,
                                                        ])
                                                      ->count();
        }

        if($count_obj_class_section > 0)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function get_courses(Request $request)
    {
         
        $data = '';
        $subject = [];
        $level_id  = $request->input('level');

        if($request->has('enc_id'))
        {
            $id          = base64_decode($request->input('enc_id'));
            $obj_details = $this->SchoolSubjectsModel->where('id',$id)->first();
            $subject     = $obj_details->json_subjects;
        }

        $academic_year = $this->academic_year;

        $academic_year = $this->CommonDataService->get_academic_year_less_than($academic_year);
        $arr_academic_year = explode(',',$academic_year);


        $obj_data  = $this->SchoolCourseModel
                          ->with('get_course')
                          ->where('school_level_id',$level_id)
                          ->where('school_id',$this->school_id)
                          ->whereIn('academic_year_id',$arr_academic_year)
                          ->get();

        if(isset($obj_data) && count($obj_data)>0)
        {
            $arr_data = $obj_data->toArray();
            foreach ($arr_data as $key => $value) 
            {
                $data .= '<option value="';
                $data .= isset($value['course_id'])?$value['course_id']:0;

                $data .= '" ';
                if(in_array($value['course_id'], $subject))
                {
                    $data .= 'selected';
                }
                $data .='>';
                $data .= isset($value['get_course']['course_name'])?ucwords($value['get_course']['course_name']):'';
                $data .= '</option>';
            }
                
            return response()->json(array('status'=>'success','data'=>$data));

        }
        else
        {
            $data.=translation('no_course_assigned_to_selected_level');
            return response()->json(array('status'=>'error','data'=>$data));
        }
        
    }
     
}


