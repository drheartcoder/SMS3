<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;
use App\Common\Services\CommonDataService;

use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\StudentModel;
use App\Models\UserRoleModel;   
use App\Models\RoleModel;
use App\Models\ActivationModel;
use App\Models\LevelModel;
use App\Models\LevelTranslationModel;
use App\Models\ParentModel;
/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   

use App\Models\LevelClassModel;
use App\Models\ClassModel;
use App\Models\ClassTranslationModel;
/*Activity Log */

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use PDF;
use Datatables;

class StudentController extends Controller
{
    use MultiActionTrait;
    public function __construct(    
                                    UserModel $user,
                                    UserTranslationModel $translation,
                                    UserRoleModel $user_role_model,
                                    RoleModel $role_model,
                                    ActivityLogsModel $activity_logs,
                                    StudentModel $student,
                                    LevelModel $level,
                                    ParentModel $parent,
                                    LevelTranslationModel $LevelTranslationModel,
                                    LevelClassModel $LevelClassModel,
                                    ClassModel $ClassModel,
                                    ClassTranslationModel $ClassTranslationModel,
                                    CommonDataService $CommonDataService 

                                )
    {
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->StudentModel                 = $student;
        $this->BaseModel                    = $this->UserModel;
        $this->ActivityLogsModel            = $activity_logs; /* Activity Model */   
        $this->LevelModel                   = $level;
        $this->LevelTranslationModel        = $LevelTranslationModel;
        $this->ParentModel                  = $parent;
        $this->LevelClassModel              = $LevelClassModel;
        $this->ClassModel                   = $ClassModel;
        $this->ClassTranslationModel        = $ClassTranslationModel;
        $this->CommonDataService            = $CommonDataService;

        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/users/student");
        
        $this->module_title                 = translation("student");
        $this->modyle_url_slug              = translation("student");

        $this->module_view_folder           = "admin.users";
        $this->theme_color                  = theme_color();

        $this->first_name = $this->last_name =$this->ip_address ='';

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
           
        }
        /* Activity Section */



    }   

    public function index(Request $request)
    {   
        $page_title = translation("manage")." ".str_plural($this->module_title);
        $this->arr_view_data['role']            = 'student';
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    function get_users_details(Request $request,$type,$fun_type='')
    {     
        $role =$type;

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
 
        $user_details             = $this->BaseModel->getTable();
        $prefixed_user_details    = DB::getTablePrefix().$this->BaseModel->getTable();
        $user_role_table          = $this->UserRoleModel->getTable();
    
        $role_table               = $this->RoleModel->getTable();
        
        $student_table               = $this->StudentModel->getTable();
        $user_trans_table             = $this->UserTranslationModel->getTable();                  
        $prefixed_user_trans_table    = DB::getTablePrefix().$this->UserTranslationModel->getTable();
        $level_class          = $this->LevelClassModel->getTable();

        $level_details          = $this->LevelModel->getTable();
        $prefixed_level_table = DB::getTablePrefix().$this->LevelModel->getTable();

        $level_trans               = $this->LevelTranslationModel->getTable();

        $class_trans               = $this->ClassTranslationModel->getTable();
        $prefixed_class_trans_table      = DB::getTablePrefix().$this->ClassTranslationModel->getTable();

        $obj_user = DB::table($student_table)
                                ->select(DB::raw($student_table.".id as id,".
                                                 $student_table.".user_id as user_id,".
                                                 $prefixed_user_details.".email as email, ".
                                                 $student_table.".is_active as is_active, ".
                                                 $student_table.".student_no, ".
                                                 $student_table.".school_id, ".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
                                                          .$prefixed_user_trans_table.".last_name) as user_name,".
                                                 $level_trans.".level_name as level_name, ".
                                                 $class_trans.".class_name as class_name, ".
                                                 $prefixed_user_details.".national_id,".
                                                 $student_table.".has_left"        
                                                 ))
                                ->whereNull($student_table.'.deleted_at')
                                ->join($user_details,$student_table.'.user_id', ' = ',$user_details.'.id')
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                ->join($level_class,$student_table.'.level_class_id', ' = ',$level_class.'.id')
                                ->join($level_trans,$level_trans.'.level_id', ' = ',$level_class.'.level_id')
                                ->join($class_trans,$class_trans.'.class_id', ' = ',$level_class.'.class_id')
                                ->where($user_trans_table.'.locale','=',$locale)
                                ->where($level_trans.'.locale','=',$locale)
                                ->where($class_trans.'.locale','=',$locale)
                                ->where($student_table.'.has_left','=',0)
                                ->orderBy($student_table.'.created_at','DESC');

        if($fun_type == 'export'){
            $search_term = $request->input('search');
        }else{
            $search = $request->input('search');
            $search_term = $search['value'];
        }
    
        if($request->has('search') && $search_term!="")
        {
            $obj_user = $obj_user->WhereRaw("( (".$user_details.".email LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$student_table.".school_id LIKE '%".$search_term."%') ")
                                 ->orWhereRaw(" ( CONCAT(".$prefixed_user_trans_table.".first_name,'',".$prefixed_user_trans_table.".last_name)  LIKE  '%".$search_term."%' ) )");
        }

        if($fun_type=="export"){
            return $obj_user->get();
        }else{
            return $obj_user;
        }
    }


    public function get_records(Request $request,$type='')
    {
        
        $arr_current_user_access =[];
        $role = Sentinel::findRoleById(1);
                
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;
       
        $obj_user        = $this->get_users_details($request,$type);

        $role = 'student';

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('users.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data) 
                            {
                                return base64_encode($data->user_id);
                            });
        }                    

        $json_result     = $json_result->editColumn('user_name',function($data) 
                            {
                                if($data->user_name!=null && $data->user_name!=''){
                                    $name = explode(' ', $data->user_name);
                                    return   ucfirst($name[0]).' '.ucfirst($name[1]);
                                }else{
                                    return  '-';
                                }
                            })
                            ->editColumn('school_name',function($data)
                            {
                                if($data->school_id != null)
                                {
                                    return $this->CommonDataService->get_school_name($data->school_id);
                                }
                            })

                            ->editColumn('build_action_btn',function($data) use ($role)
                            {
                                if($role != null)
                                {       
                                     $build_status_btn =  ''; 
 
                                    $view_href =  $this->module_url_path.'/view/'.base64_encode($data->user_id);
                                    $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>';
                                    
                                    return $build_view_action.'&nbsp;';  
                                }
                            })
                             ->editColumn('build_checkbox',function($data){
                           
                                return '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    public function view($enc_id)
    {   
        $id = base64_decode($enc_id);

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
        
        $arr_user = [];
        $obj_user = $this->BaseModel->where('id','=',$id)
                                    ->first();
        if($obj_user)
        {
            $arr_user = $obj_user->toArray();

        }                            
         
        $student_details     =   $this->StudentModel
                                    ->with("get_parent","get_parent_details")
                                    ->where('user_id',$id)
                                    ->first();

        $arr_student_details = [];
        if($student_details)
        {
            $arr_student_details = $student_details->toArray();

        }

        $level               =   $this->LevelTranslationModel
                                    ->where(['level_id'=>$student_details->level_id,'locale'=>$locale])
                                    ->first();

        $arr_level = [];
        if($level)
        {
            $arr_level = $level->toArray();

        }
        
        $arr_data = []; 
        $arr_data['users']          = $arr_user;
        $arr_data['student']        = $arr_student_details;
        $arr_data['level']          = $arr_level;
        $arr_data['student_details']   = $arr_student_details;

            
        $this->arr_view_data['role']                         = 'student';
        $this->arr_view_data['page_title']                   = translation("view").' student ';
        $this->arr_view_data['module_title']                 = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['arr_data']                     = $arr_data;
        $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
        $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        
        return view($this->module_view_folder.'.view', $this->arr_view_data);
    }

    /*
    | export() : Export List
    | Auther  : Vrajesh
    | Date    : 14-12-2018
    */

    public function export(Request $request)
    {
        $file_type = config('app.project.export_file_formate');
        $obj_data = $this->get_users_details($request,'school_admin','export');

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
                        $arr_fields['name']         = translation('name');
                        $arr_fields['email']        = translation('email');
                        $arr_fields['school_name']  = translation('school_name');

                        $sheet->row(2, [ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                        $sheet->row(4, $arr_fields);
                        
                        // To set Colomn head
                        $j = 'A'; $k = '4';
                        for($i=0; $i<=4;$i++)
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
                                $school_name = '';
                                if($result->school_id != null && $result->school_id != '')
                                {
                                    $school_name = $this->CommonDataService->get_school_name($result->school_id);
                                }else{
                                    $school_name = '-';
                                }
                                $arr_tmp[$key]['sr_no']         = $count++;
                                $arr_tmp[$key]['name']          = $result->user_name;
                                $arr_tmp[$key]['email']         = $result->email;
                                $arr_tmp[$key]['school_name']   = $school_name;
                            }
                            $sheet->rows($arr_tmp);
                        }
                    });
                })->export($file_type);     
        }
        
        if($request->file_format == 'pdf')
        {
            $this->arr_view_data['arr_data'] = $obj_data;

            foreach($this->arr_view_data['arr_data'] as $key => $row)
            {
                $school_name = '';
                if($row->school_id != null && $row->school_id != '')
                {
                    $school_name = $this->CommonDataService->get_school_name($row->school_id);
                }else{
                    $school_name = '-';
                }
                $row->school_name = $school_name;
            }

            $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
            return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
        }
    }
}