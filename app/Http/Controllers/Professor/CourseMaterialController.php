<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Services\CommonDataService;
use App\Models\SchoolCourseModel;
use App\Models\CourseMaterialDetailsModel;
use App\Models\CourseMaterialModel;
use App\Models\StudentModel;
use App\Models\SchoolAdminModel;
use App\Models\LevelTranslationModel;
use App\Models\NotificationModel;
use App\Models\SchoolTimeTableModel;
use App\Models\LevelClassModel;
use App\Models\ClassTranslationModel;
use App\Models\CourseTranslationModel;
use App\Common\Traits\MultiActionTrait;
use App\Common\Services\EmailService;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;

class CourseMaterialController extends Controller
{
    use MultiActionTrait;
    public function __construct(CommonDataService $common_data_service,
                                SchoolCourseModel $school_course,
                                CourseMaterialDetailsModel $course_material_detail_model,
                                CourseMaterialModel $course_material_model,
                                LevelClassModel $LevelClassModel,
                                CourseTranslationModel $CourseTranslationModel,
                                LevelTranslationModel $LevelTranslationModel,
                                ClassTranslationModel $ClassTranslationModel,
                                StudentModel $StudentModel,
                                SchoolAdminModel $SchoolAdminModel,
                                NotificationModel $NotificationModel,
                                EmailService $EmailService,
                                SchoolTimeTableModel $SchoolTimeTableModel)
    {
        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.professor_panel_slug')).'/course_material';
        $this->module_title                 = translation('course_material');
 
        $this->module_view_folder           = "professor.course_material";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-book';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');

        $this->CourseMaterialModel          = $course_material_model;
        $this->BaseModel                    = $this->CourseMaterialModel;
        $this->CommonDataService            = $common_data_service;
        $this->SchoolCourseModel            = $school_course;
        $this->CourseMaterialDetailsModel   = $course_material_detail_model;
        $this->StudentModel                 = $StudentModel;
        $this->SchoolAdminModel             = $SchoolAdminModel;
        $this->NotificationModel            = $NotificationModel;
        $this->EmailService                 = $EmailService;
        $this->SchoolTimeTableModel         = $SchoolTimeTableModel;
        
        $this->CourseTranslationModel     = $CourseTranslationModel;
        $this->LevelTranslationModel      = $LevelTranslationModel;
        $this->ClassTranslationModel      = $ClassTranslationModel;
        $this->LevelClassModel            = $LevelClassModel;

        $obj_data          = Sentinel::getUser();
        $this->first_name  = $this->last_name = $this->school_admin_id = $this->school_admin_email = $this->school_admin_contact = '';
        $this->permissions = [];
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;
        }

        $obj_permissions = $this->SchoolAdminModel
                                ->with('notification_permissions','get_user_details')
                                ->where('school_id',$this->school_id)
                                ->first();

        if(isset($obj_permissions) && count($obj_permissions)>0 && !is_null($obj_permissions))
        {
            $arr_permissions       = $obj_permissions->toArray();

            $this->school_admin_id = $arr_permissions['user_id'];

            if(isset($arr_permissions['notification_permissions']['notification_permission']) && !empty($arr_permissions['notification_permissions']['notification_permission']))
            {   
                $this->permissions = json_decode($arr_permissions['notification_permissions']['notification_permission'],true);   
            }
            $this->school_admin_email   = isset($arr_permissions['get_user_details']['email'])?$arr_permissions['get_user_details']['email']:'';
            $this->school_admin_contact = isset($arr_permissions['get_user_details']['mobile_no'])?$arr_permissions['get_user_details']['mobile_no']:'';
        }

        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['create_icon'] = $this->create_icon;
        $this->arr_view_data['page_title'] = $this->module_title;
        $this->arr_view_data['theme_color'] = $this->theme_color;

        $this->course_material_public_path = url('/').config('app.project.img_path.course_material');
        $this->course_material_base_path   = base_path().config('app.project.img_path.course_material');  
    }

    public function index()
    {   

        $arr_data = $arr_levels = $arr_courses = [];

        $obj_data = $this->CourseMaterialModel
                               ->with(['get_level_class'=>function($q){
                                    $q->with('level_details');
                                    $q->with('class_details');
                                },'get_course','get_material_details'])
                               ->orderBy('id','DESC')
                               ->where('material_added_by','=',$this->user_id)->get();

        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }                        

        $obj_levels = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id);                       
        
        if(!empty($obj_levels))
        {
            $arr_levels = $obj_levels->toArray();
        }

        /*$obj_courses = $this->CommonDataService->get_professor_courses($level,$class,$this->user_id);*/                      

        if(!empty($obj_courses))
        {
            $arr_courses = $obj_courses->toArray();
        }

        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_data'] = $arr_data;
        $this->arr_view_data['arr_levels'] = $arr_levels;
        $this->arr_view_data['arr_courses'] = $arr_courses;
        $this->arr_view_data['current_user'] = $this->user_id;
        
        $this->arr_view_data['module_title']    = translation("manage")." ".$this->module_title;
        return view($this->module_view_folder.'.index', $this->arr_view_data);

    }

    public function create()
    {

        /*$address = "1600 Pennsylvania Ave NW Washington DC 20500";
        $address = str_replace(" ", "+", $address);
        $region = "USA";

        $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
        $json = json_decode($json,true);

        $lat = $json['results'][0]['geometry']['location']['lat'];
        $long = $json['results'][0]['geometry']['location']['lng'];
        
        dd($lat,$long);*/

        $arr_levels = $arr_courses = [];

        $obj_levels = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id);                       
        
        if(!empty($obj_levels))
        {
            $arr_levels = $obj_levels->toArray();
        }

        /*$obj_courses = $this->CommonDataService->get_professor_courses($level,$class,$this->user_id);*/

        if(!empty($obj_courses))
        {
            $arr_courses = $obj_courses->toArray();
        }

        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_levels'] = $arr_levels;
        $this->arr_view_data['arr_courses'] = $arr_courses;
        $this->arr_view_data['current_user'] = $this->user_id;
        
        $this->arr_view_data['module_title']    = translation("manage")." ".$this->module_title;
        return view($this->module_view_folder.'.create', $this->arr_view_data);
    }

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

    public function store(Request $request)
    {

        $messages = $arr_rules = [];
        $form_data = $request->all();
        $arr_rules['level']            = 'required';
        $arr_rules['course']          = 'required';
        $arr_rules['class']            = 'required';

        $messages['required']               = translation('this_field_is_required');
        
        $validator                          = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $arr_data =[]; 
        $arr_data['school_id'] = $this->school_id;
        $arr_data['academic_year_id'] = $this->academic_year;

        $level_id = $request->input('level');
        $class_id = $request->input('class');
        $course_id = $request->input('course');

        $arr_data['school_id']          = $this->school_id;
        $arr_data['level_class_id']     = $class_id;
        $arr_data['course_id']          = $course_id;
        $arr_data['material_added_by']  = $this->user_id;
        $arr_data['academic_year_id']   = $this->academic_year;

        $course_material = $this->CourseMaterialModel->create($arr_data);
        $course_material_id = $course_material->id;

        if(isset($form_data['arr_document']) && count($form_data['arr_document'])>0 )
        {
            foreach($form_data['arr_document'] as $key => $file) 
            {
                if($file != NULL)
                {
                    $filename = rand(1111,9999);
                    $original_file_name = $file->getClientOriginalName();
                    $fileExt  = $file->getClientOriginalExtension();
                    $fileName = $original_file_name;
                    if(in_array($fileExt,['pdf','doc','docx','PDF','DOC','DOCX','xls','xlsx']))
                    {
                        $files[] = $original_file_name;
                        $upload_success = $file->move($this->course_material_base_path, $fileName);

                        if($upload_success)
                        {
                           $arr_certificate['type']                 = "Document";
                           $arr_certificate['path']                 = $fileName;
                           $arr_certificate['course_material_id']   = $course_material_id;

                           $status = $this->CourseMaterialDetailsModel->create($arr_certificate);
                        }
                    }
                } 
            }
        }
        if(isset($form_data['matrial_url']) && count($form_data['matrial_url'])>0)
        {
            foreach($form_data['matrial_url'] as $key => $url) 
            {
               $arr_video['type']                 = "Video";
               $arr_video['path']                 = trim($url);
               $arr_video['course_material_id']   = $course_material_id;
               if($arr_video['path']!='')
               {
                     $status = $this->CourseMaterialDetailsModel->create($arr_video);
               }
              
            }
        }
        $arr_students = [];
        $obj_students   =   $this->StudentModel
                                 ->with('notifications')
                                 ->where('school_id',$this->school_id)
                                 ->where('academic_year_id',$this->academic_year)
                                 ->where('level_class_id',$class_id)
                                 ->where('has_left',0)
                                 ->where('is_active',1)
                                 ->get();

       if(isset($obj_students) && !is_null($obj_students) && count($obj_students)>0)
       {
            $arr_students = $obj_students->toArray();
       }


       if($course_material)
       {
            $result = $this->send_notifications($course_material);

            /*if (isset($arr_students) && count($arr_students)>0) {
                foreach ($arr_students as $key => $value) {
                         
                    if(isset($value['notifications']) && $value['notifications']!='')       
                    {
                        $arr_permissions = json_decode($value['notifications']['notification_permission'],true);
                        if(isset($arr_permissions) && count($arr_permissions)>0)
                        {

                            if(array_key_exists('course_material.app',$arr_permissions))
                            {
                             
                                $arr_notification = [];
                                $arr_notification['school_id']          =  $this->school_id;
                                $arr_notification['from_user_id']       =  $this->user_id;
                                $arr_notification['to_user_id']         =  $value['user_id'];
                                $arr_notification['user_type']          =  config('app.project.role_slug.professor_role_slug');
                                $arr_notification['notification_type']  =  'course_material Add';
                                $arr_notification['title']              =  'New Course Material Added:Professor added new course material';
                                $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.student_role_slug').'/course_material';
                                $this->NotificationModel->create($arr_notification);
                            }
                            elseif(array_key_exists('course_material.sms',$arr_permissions))
                            {

                            }
                            elseif (array_key_exists('course_material.email',$arr_permissions))
                            {
                                
                            }
                    }
                }
            }*/

            Flash::success(translation("course_material_added_successfully"));
            return redirect()->back();
       }
       else
       {
            Flash::success(translation("problem_occur_while_adding_course_material"));
            return redirect()->back();
       }
        
    }

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

        $arr_data = [];

        $obj_data = $this->CourseMaterialModel
                               ->with(['get_level_class'=>function($q){
                                    $q->with('level_details');
                                    $q->with('class_details');
                                },'get_course','get_material_details'])
                                ->where('id',$id)->first();

        if(!empty($obj_data))
        {
            $arr_data = $obj_data->toArray();
        }

        $this->arr_view_data['arr_data'] = $arr_data;
        $this->arr_view_data['module_title'] = translation("view")." ".$this->module_title;
 
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['page_title'] = $this->module_title;
        $this->arr_view_data['theme_color'] = $this->theme_color;

        return view($this->module_view_folder.'.view', $this->arr_view_data); 
    }

    public function delete_doc(Request $request)
    {
        $record = $this->CourseMaterialDetailsModel->where('id',$request->input('id'))->first();

        if(isset($record->type) && $record->type=='Video'){
            if(($this->CourseMaterialDetailsModel->where('course_material_id',$record->course_material_id)->where('type','Video')->count()) ==1)
            {
                Flash::error(translation("at_least_one_url_should_be_there"));
                return "error";
            }
        }
        $course_material_id = $record->course_material_id;
        $record->delete(); 
        return "success";
        $exist = $this->CourseMaterialDetailsModel->where('course_material_id',$course_material_id)->count();
        if($exist==0){
            $this->CourseMaterialModel->where('id',$course_material_id)->delete();
        }
    }

    public function download_document($enc_id)
    {
        $arr_document = [];
        if(isset($enc_id))
        {
            $document_id = base64_decode($enc_id);
            $obj_documents = $this->CourseMaterialDetailsModel
                                                    ->where('id',$document_id)
                                                    ->select('path')
                                                    ->first();
            if($obj_documents)
            {
                  $arr_document    = $obj_documents->toArray();
                  $file_name       = $arr_document['path'];
                  $pathToFile      = $this->course_material_base_path.$file_name;

                  $file_exits      = file_exists($pathToFile);
                  if($file_exits)
                  {
                     return response()->download($pathToFile, $file_name); 
                  }
                  else
                  {
                     Flash::error("Error while downloading an document.");
                  }
                  
             }
        }
        else
        {
           Flash::error("Error while downloading an document.");
        }
        return redirect()->back();
    }

    public function get_courses(Request $request)
    {
        $option = '';
        $level = $request->input('level');
        $class = $request->input('class');
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
                    $option .= '">';
                    $option .= isset($professor['professor_subjects']['course_name'])?$professor['professor_subjects']['course_name']:'';
                    $option .= '</option>';
                }
            }
        }
        return $option;
       
    }

    public function send_notifications($course_material)
    {
        $result = '';
        $data = $this->SchoolTimeTableModel
                     ->with('level_details','class_details','course_details','user_details')
                     ->where('course_id',$course_material->course_id)
                     ->where('level_class_id',$course_material->level_class_id)
                     ->where('school_id',$this->school_id)
                     ->where('academic_year_id',$this->academic_year)
                     ->first();

        $students = $this->CommonDataService->get_permissions(config('app.project.role_slug.student_role_slug'),$this->academic_year,$this->school_id,$course_material->level_class_id);

        if(isset($data) && count($data)>0)
        {
            $arr_data = $data->toArray();
        }

        $level_name  =   isset($arr_data['level_details']['level_name'])?$arr_data['level_details']['level_name']:'';
        $class_name  =   isset($arr_data['class_details']['class_name'])?$arr_data['class_details']['class_name']:'';
        $course_name =   isset($arr_data['course_details']['course_name'])?$arr_data['course_details']['course_name']:'';
        
        if(isset($this->permissions) && count($this->permissions)>0)
        {
            $result = $this->notifications($this->permissions,$this->school_admin_id,config('app.project.role_slug.school_admin_role_slug'),$level_name,$class_name,$course_name);    
        }
        

        if(isset($students) && count($students)>0)
        {
            foreach ($students as $key => $value) {
                if(isset($value['notifications']['notification_permission']) && $value['notifications']['notification_permission']!=null)
                {
                    $arr_permissions = json_decode($value['notifications']['notification_permission'],true);
                    if(isset($arr_permissions) && count($arr_permissions)>0)
                    {
                        $result = $this->notifications($arr_permissions,$value['user_id'],config('app.project.role_slug.student_role_slug'),$level_name,$class_name,$course_name,$value['get_user_details']);    
                    }
                    
                }
            }
        }
        return $result;
    }

    public function notifications($permissions,$user_id,$role,$level_name,$class_name,$course_name,$users=FALSE)
    {
        if(array_key_exists('course_material.app',$permissions))
        {
            $arr_notification = [];
            $arr_notification['school_id']          =  $this->school_id;
            $arr_notification['from_user_id']       =  $this->user_id;
            $arr_notification['user_type']          =  config('app.project.role_slug.professor_role_slug');
            $arr_notification['notification_type']  =  'course_material Add';
            $arr_notification['title']              =  'Course Material Added:School Admin added course material for '.$level_name.' '.$class_name.' '.$course_name;
            $arr_notification['to_user_id']         =  $user_id;
            $arr_notification['view_url']           =  url('/').'/'.$role.'/course_material';
            
            $this->NotificationModel->create($arr_notification);
        }
        $details          = [
                                    'first_name'  =>  isset($users['first_name'])?ucwords($users['first_name']):'',
                                    'course_name' =>  isset($course_name)?ucwords($course_name):'',
                                    'level'       =>  isset($level_name)?$level_name:'',
                                    'class'       =>  isset($class_name)?$class_name:'',
                                    'email'       =>  isset($users['email'])?$users['email']:'',
                                    'mobile_no'   =>  isset($users['mobile_no'])?$users['mobile_no']:''
                            ];
        if(array_key_exists('course_material.sms',$this->permissions))
        {
            $arr_sms_data = $this->built_sms_data($details,$role);
            $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id);
        }
        if (array_key_exists('course_material.email',$this->permissions))
        {
            $arr_mail_data = $this->built_mail_data($details,$role); 
            $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
        }
    }

    public function built_mail_data($arr_data,$role)
     {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            if($role == config('app.project.role_slug.school_admin_role_slug'))
            {
                $arr_built_content = [
                                      'FIRST_NAME'     => 'School Admin',
                                      'COURSE_NAME'    => $arr_data['course_name'],
                                      'ADDED_BY'       => 'Prof. '.ucwords($this->first_name.' '.$this->last_name),
                                      'LEVEL'          => $arr_data['level'],
                                      'SCHOOL_ADMIN'   => $this->CommonDataService->get_school_name($this->school_id),
                                      'CLASS'          => $arr_data['class']];
            }
            else
            {
                $arr_built_content = [
                                      'FIRST_NAME'     => $arr_data['first_name'],
                                      'COURSE_NAME'    => $arr_data['course_name'],
                                      'ADDED_BY'       => 'Prof. '.ucwords($this->first_name.' '.$this->last_name),
                                      'LEVEL'          => $arr_data['level'],
                                      'SCHOOL_ADMIN'   => $this->CommonDataService->get_school_name($this->school_id),
                                      'CLASS'          => $arr_data['class']];
            }
            

            $arr_mail_data                        = [];
            $arr_mail_data['email_template_slug'] = 'add_course_material';            
            $arr_mail_data['arr_built_content']   = $arr_built_content;
            $arr_mail_data['user']                = $arr_data;
            if($role == config('app.project.role_slug.school_admin_role_slug'))
            {
                $arr_mail_data['user']['email']           = $this->school_admin_email;    
            }


            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_sms_data($arr_data,$role)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                      'COURSE_NAME'    => $arr_data['course_name'],
                                      'ADDED_BY'       => 'Prof. '.ucwords($this->first_name.' '.$this->last_name),
                                      'LEVEL'          => $arr_data['level'],
                                      'CLASS'          => $arr_data['class']];   

            $arr_sms_data                      = [];
            $arr_sms_data['sms_template_slug'] = 'add_course_material';
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            if($role == config('app.project.role_slug.school_admin_role_slug'))
            {
                $arr_mail_data['mobile_no']        = $this->school_admin_contact;    
            }
            else
            {
                $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];
            }
            
            return $arr_sms_data;
        }
        return FALSE;
    }
}
