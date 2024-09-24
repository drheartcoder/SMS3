<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Services\CommonDataService;
use App\Common\Services\EmailService;
use App\Models\SchoolCourseModel;
use App\Models\CourseMaterialDetailsModel;
use App\Models\CourseMaterialModel;
use App\Models\SchoolSubjectsModel;
use App\Models\LevelTranslationModel;
use App\Models\LevelClassModel;
use App\Models\ClassTranslationModel;
use App\Models\CourseTranslationModel;
use App\Models\CourseModel;
use App\Models\SchoolTimeTableModel;
use App\Models\NotificationModel;
use App\Common\Traits\MultiActionTrait;

use DB;
use Flash;
use Session;
use Sentinel;
use Validator;
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
                                SchoolSubjectsModel $SchoolSubjectsModel,
                                CourseModel $CourseModel,
                                SchoolTimeTableModel $SchoolTimeTableModel,
                                NotificationModel $NotificationModel,
                                EmailService $EmailService)
    {

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/course_material';
        $this->module_title                 = translation('course_material');
 
        $this->module_view_folder           = "schooladmin.course_material";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-book';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->SchoolSubjectsModel          = $SchoolSubjectsModel;
        $this->CourseMaterialModel          = $course_material_model;
        $this->BaseModel                    = $this->CourseMaterialModel;
        $this->CommonDataService            = $common_data_service;
        $this->SchoolCourseModel            = $school_course;
        $this->CourseMaterialDetailsModel   = $course_material_detail_model;
        $this->CourseModel                  = $CourseModel;
        $this->SchoolTimeTableModel         = $SchoolTimeTableModel;
        
        $this->CourseTranslationModel       = $CourseTranslationModel;
        $this->LevelTranslationModel        = $LevelTranslationModel;
        $this->ClassTranslationModel        = $ClassTranslationModel;
        $this->LevelClassModel              = $LevelClassModel;
        $this->NotificationModel            = $NotificationModel;
        $this->EmailService                 = $EmailService;

        $this->arr_view_data['page_title'] = translation('course_material');
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['create_icon'] = $this->create_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color'] = $this->theme_color;

        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;
        }

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
                                },'get_course','get_material_details','user_details'])
                               ->orderBy('id','DESC')
                               ->get();

        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }                        

        $this->arr_view_data['arr_data'] = $arr_data;
        $this->arr_view_data['current_user'] = $this->user_id;

        $this->arr_view_data['module_title']    = translation("manage")." ".$this->module_title;
        return view($this->module_view_folder.'.index', $this->arr_view_data);

    }
    public function create(){
        $obj_levels = $this->CommonDataService->get_levels($this->academic_year,$this->user_id);                       
        
        if(!empty($obj_levels))
        {
            $arr_levels = $obj_levels->toArray();
        }

      /*  $obj_courses = $this->CommonDataService->get_courses($this->academic_year,config('app.project.school_admin_panel_slug'),$this->user_id);                       

        if(!empty($obj_courses))
        {
            $arr_courses = $obj_courses->toArray();
        }*/

        $this->arr_view_data['arr_levels'] = $arr_levels;
        // $this->arr_view_data['arr_courses'] = $arr_courses;
        $this->arr_view_data['module_title'] = translation("create")." ".$this->module_title;

        return view($this->module_view_folder.'.create', $this->arr_view_data);
    }
    public function get_class(Request $request)
    {
        $level_id = $request->input('level');

        $options ='';

        $obj_class = $this->CommonDataService->get_class($level_id);
    
        if(!empty($obj_class))
        {
            $arr_class  = $obj_class -> toArray();
            if(count($arr_class)>0)
            {
                $options .= '<option value="" >'.translation('select_class').'</option>';
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
        $arr_rules['course']           = 'required';
        $arr_rules['class']            = 'required';
        $arr_rules['matrial_url']      = 'required';
        $messages['required']               = translation('this_field_is_required');
        
        $validator                          = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $arr = $request->input('matrial_url');

        if($arr[0]=='')
        {
            Session::flash('error',translation('video url field is required'));
            return redirect()->back();
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
                    if(in_array($fileExt,['pdf','doc','docx','PDF','DOC','DOCX','xlsx','xls']))
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

       if($course_material)
       {
            $data = $this->send_notifications($class_id,$course_id);
            if($data)
            {
                Flash::success(translation("course_material_added_successfully"));
                return redirect()->back();    
            }
            
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
        $this->arr_view_data['page_title'] = $this->module_title;
 
        $this->arr_view_data['module_icon'] = "fa fa-eye";
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['module_title'] = translation("view")." ".$this->module_title;
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
                     Flash::error(translation("error_while_downloading_an_document"));
                  }
                  
             }
        }
        else
        {
           Flash::error(translation("error_while_downloading_an_document"));
        }
        return redirect()->back();
    }

    public function get_courses(Request $request)
    {
        $data ='';
        $level_class_id = $request->input('class');
       
        $obj_details    = $this->LevelClassModel->where('id',$level_class_id)->first();
        
        $arr_courses  = $this->CommonDataService->get_courses($this->academic_year, config('app.project.school_admin_panel_slug'),$this->user_id, $obj_details->level_id, $obj_details->class_id);
        
        if(isset($arr_courses) && count($arr_courses)>0)
        {
            $data .= '<option value="">'.translation('select_course').'</option>';
            foreach ($arr_courses as $key => $value) {
                $data .= '<option value="';
                $data .= isset($value['id'])?$value['id']:0;
                $data .= '">';
                $data .= isset($value['course_name'])?$value['course_name']:'';
                $data .= '</option>';
            }
            return response()->json(array('status'=>'success','data'=>$data));
        }
        else
        {
            $data .= translation('course_is_not_assigned_to_this_level_class');
            return response()->json(array('status'=>'error','data'=>$data));
        }

    }

    public function send_notifications($level_class_id,$course_id)
    {
        $result = '';
        $data = $this->SchoolTimeTableModel
                     ->with('notifications','level_details','class_details','course_details','user_details')
                     ->where('course_id',$course_id)
                     ->where('level_class_id',$level_class_id)
                     ->where('school_id',$this->school_id)
                     ->where('academic_year_id',$this->academic_year)
                     ->first();

        $students = $this->CommonDataService->get_permissions(config('app.project.role_slug.student_role_slug'),$this->academic_year,$this->school_id,$level_class_id);

        if(isset($data) && count($data)>0)
        {
            $arr_data = $data->toArray();
        }

        $level_name  =   isset($arr_data['level_details']['level_name'])?$arr_data['level_details']['level_name']:'';
        $class_name  =   isset($arr_data['class_details']['class_name'])?$arr_data['class_details']['class_name']:'';
        $course_name =   isset($arr_data['course_details']['course_name'])?$arr_data['course_details']['course_name']:'';
        
        if(isset($arr_data['notifications']['notification_permission']) && $arr_data['notifications']['notification_permission']!=null)
        {
            $arr_permissions = json_decode($arr_data['notifications']['notification_permission'],true);
            $result = $this->notifications($arr_permissions,$arr_data['notification_permissions']['user_id'],config('app.project.role_slug.professor_role_slug'),$level_name,$class_name,$course_name,$arr_data['user_details']);
        }
        if(isset($students) && count($students)>0)
        {
            foreach ($students as $key => $value) {
                if(isset($value['notifications']['notification_permission']) && $value['notifications']['notification_permission']!=null)
                {
                    $arr_permissions = json_decode($value['notifications']['notification_permission'],true);
                    $result = $this->notifications($arr_permissions,$value['user_id'],config('app.project.role_slug.student_role_slug'),$level_name,$class_name,$course_name,$value['get_user_details']);
                }
            }
        }
        return $result;
    }

    public function notifications($arr_permissions,$user_id,$role,$level_name,$class_name,$course_name,$user_details)
    {
        
        if(array_key_exists('course_material.app',$arr_permissions))
        {
         
            $arr_notification = [];
            $arr_notification['school_id']          =  $this->school_id;
            $arr_notification['from_user_id']       =  $this->user_id;
            $arr_notification['to_user_id']         =  $user_id;
            $arr_notification['user_type']          =  config('app.project.role_slug.school_admin_role_slug');
            $arr_notification['notification_type']  =  'Course Material Added';
            $arr_notification['title']              =  'Course Material Added:School Admin added course material for '.$level_name.' '.$class_name.' '.$course_name;
            $arr_notification['view_url']           =  url('/').'/'.$role.'/course_material';
            $result = $this->NotificationModel->create($arr_notification);
        }
        $details          = [
                                    'first_name'  =>  isset($user_details['first_name'])?ucwords($user_details['first_name']):'',
                                    'course_name' =>  isset($course_name)?ucwords($course_name):'',
                                    'level'       =>  isset($level_name)?$level_name:'',
                                    'class'       =>  isset($class_name)?$class_name:'',
                                    'email'       =>  isset($user_details['email'])?$user_details['email']:'',
                                    'mobile_no'   =>  isset($user_details['mobile_no'])?$user_details['mobile_no']:''
                            ];

        if(array_key_exists('course_material.sms',$arr_permissions))
        {
            $arr_sms_data = $this->built_sms_data($details);
            $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
        }
        if (array_key_exists('course_material.email',$arr_permissions))
        {
            $arr_mail_data = $this->built_mail_data($details); 
            $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
        }
        return $result;
    }

    public function built_mail_data($arr_data)
     {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'     => $arr_data['first_name'],
                                  'COURSE_NAME'    => $arr_data['course_name'],
                                  'ADDED_BY'       => 'School Admin',
                                  'LEVEL'          => $arr_data['level'],
                                  'SCHOOL_ADMIN'   => $this->CommonDataService->get_school_name($this->school_id),
                                  'CLASS'          => $arr_data['class']];
    
            $arr_mail_data                        = [];
            $arr_mail_data['email_template_slug'] = 'add_course_material';            
            $arr_mail_data['arr_built_content']   = $arr_built_content;
            $arr_mail_data['user']                = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_sms_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'COURSE_NAME'    => $arr_data['course_name'],
                                  'ADDED_BY'       => 'School Admin',
                                  'LEVEL'          => $arr_data['level'],
                                  'CLASS'          => $arr_data['class']];

            $arr_sms_data                      = [];
            $arr_sms_data['sms_template_slug'] = 'add_course_material';
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }
}
