<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TaskModel;
use App\Models\TaskStudentModel;
use App\Models\SchoolAdminModel;
use App\Models\NotificationModel;
use App\Common\Traits\MultiActionTrait;
use App\Models\ProfessorModel;
use App\Common\Services\CommonDataService;
use App\Common\Services\EmailService;

use Session;
use Validator;
use Flash;
use Sentinel;

class TaskController extends Controller
{
     public function __construct(CommonDataService $CommonDataService,EmailService $EmailService){

    	$this->CommonDataService = $CommonDataService;
        $this->EmailService      = $EmailService;
    	$this->TaskModel         = new TaskModel();
    	$this->TaskStudentModel  = new TaskStudentModel();
    	$this->SchoolAdminModel  = new SchoolAdminModel();
        $this->NotificationModel = new NotificationModel();
        $this->ProfessorModel    = new ProfessorModel();
    	
        $this->BaseModel = $this->TaskModel;
    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.student_panel_slug')).'/task';
        $this->module_title                 = translation('task');
        
        $this->module_view_folder           = "student.task";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-tasks';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->level_class_id               = Session::get('level_class_id');

        $this->arr_view_data['module_icon']  =  $this->module_icon;
        $this->arr_view_data['create_icon']  =  $this->create_icon;
        $this->arr_view_data['edit_icon']    =  $this->edit_icon;
        $this->arr_view_data['theme_color']  =  $this->theme_color;
        $this->arr_view_data['page_title']   =  $this->module_title;
        $this->arr_view_data['module_url_path']   =  $this->module_url_path;

        $this->first_name = $this->last_name = $this->school_admin_email = $this->school_admin_contact =$this->school_admin_id='';
        $this->permissions = [];
        $obj_data          = Sentinel::getUser();
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

            $this->school_admin    = $arr_permissions['get_user_details'];

            if(isset($arr_permissions['notification_permissions']['notification_permission']) && !empty($arr_permissions['notification_permissions']['notification_permission']))
            {   
                $this->permissions = json_decode($arr_permissions['notification_permissions']['notification_permission'],true);   
            }
            $this->school_admin_email   = isset($arr_permissions['get_user_details']['email'])?$arr_permissions['get_user_details']['email']:'';
            $this->school_admin_contact = isset($arr_permissions['get_user_details']['mobile_no'])?$arr_permissions['get_user_details']['mobile_no']:'';
        }
    }

    /*---------------------------------
    index() : get list of tasks
    Auther  : Pooja K
    Date    : 15 June 2018
    ---------------------------------*/
    public function index(){
   	    
        $level =0;
        $obj_level_class = $this->CommonDataService->get_level_class($this->level_class_id);
        
        $level = isset($obj_level_class['level_id']) ? $obj_level_class['level_id']  : 0 ;	

      	$tasks =[];
        $obj_tasks = $this->TaskModel
        							->whereHas('get_user',function(){})
                                    ->with('get_user')
                                    ->with('get_supervisor')
                                    ->whereRaw('user_role="'.config('app.project.role_slug.student_role_slug').'" and school_id="'.$this->school_id.'" and academic_year_id="'.$this->academic_year.'" and (level_class_id="0" or level_id="0" or level_class_id="'.$this->level_class_id.'" or level_id='.$level.' )')
                                    ->orderBy('id','DESC')
                                    ->get();                            

        if(isset($obj_tasks) && !empty($obj_tasks)){
            $arr_tasks =  $obj_tasks -> toArray();

            foreach($arr_tasks as $task){
            	$arr_roles = ($task['user_role']!='') ? explode(',',$task['user_role']) : array() ;

            		if(in_array( config('app.project.role_slug.student_role_slug'),$arr_roles )){
                            array_push($tasks,$task);   
                        }
            		}
            	}	       

        $this->arr_view_data['module_title']  =  translation('manage').' '.$this->module_title;
        $this->arr_view_data['arr_tasks']  =  $tasks;
  
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*---------------------------------
    index() : view details of  tasks
    Auther  : Pooja K
    Date    : 16 June 2018
    ---------------------------------*/
    public function view($enc_id){
        $id= base64_decode($enc_id);
        $obj_tasks = $this->TaskModel->where('id',$id)->first();
        if(isset($obj_tasks) && !empty($obj_tasks) ){
            $arr_data = $obj_tasks->toArray();
            if(count($arr_data)>0){

               $this->arr_view_data['arr_data']  =  $arr_data;
               $this->arr_view_data['module_title']   = translation('view').' '.$this->module_title;
               return view($this->module_view_folder.'.view', $this->arr_view_data);     
            }
        }
        Flash::success(translation('no_data_available'));
        return redirect()->back();
    }

    public function change_user_status(Request $request){
    	
        
        $task_detail = $data = '';
        $status = $request->input('status');
        $id = $request->input('task_id');
        
        $task = $this->TaskStudentModel->where('id',$request->id)->first();
        
        $task_detail = $this->TaskModel->where('id',$id)->first();    
        
        
        $this->TaskStudentModel->where('task_id',$id)->where('id',$request->id)->update(['status'=>$status]);

        $data = $this->SchoolAdminModel->with('notification_permissions')->where('user_id',$task_detail->added_by)->where('school_id',$this->school_id)->first();    
        
        
        if(isset($data) && $data!=null)
        {
            if(isset($data['notification_permissions']['notification_permission']) && $data['notification_permissions']['notification_permission']!='')
            {
                $permissions = json_decode($data['notification_permissions']['notification_permission'],true);
                $result = $this->send_notifications($this->school_admin,$permissions,$data->user_id,$task_detail,$status,$task);
            }
        }
        else
        {
            $data = $this->ProfessorModel->with('notifications','get_user_details')->where('user_id',$task_detail->added_by)->where('school_id',$this->school_id)->first();
            if(isset($data['notifications']['notification_permission']) && $data['notifications']['notification_permission']!='')
            {
                $permissions = json_decode($data['notifications']['notification_permission'],true);
                $result = $this->send_notifications($data['get_user_details'],$permissions,$data->user_id,$task_detail,$status,$task);
            }
        }
        

        

    }

    public function send_notifications($users,$permissions,$user_id,$task,$status,$task_status)
    {
        if(array_key_exists('task.app',$permissions))
        {
         
            $arr_notification = [];
            $arr_notification['school_id']          =   $this->school_id;
            $arr_notification['from_user_id']       =   $this->user_id;
            $arr_notification['to_user_id']         =   $user_id;
            $arr_notification['user_type']          =   config('app.project.role_slug.professor_role_slug');
            $arr_notification['notification_type']  =   'Change Task Status';
            $arr_notification['title']              =   'Change Task Status :'.$task->task_name.' changed by '.ucwords($this->first_name.' '.$this->last_name).' from '.$task_status->status.' to '.$status.' which has submission date '.getDateFormat($task->task_submission_date);
            $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.student_role_slug').'/attendance';
            $this->NotificationModel->create($arr_notification);
        }
        $details    =   [
                            'first_name'         => isset($users['first_name'])?ucwords($users['first_name']):'',
                            'prev_task_status'   => $task_status->status,
                            'nxt_task_status'    => $status,
                            'student_name'       => ucwords($this->first_name.' '.$this->last_name),
                            'submission_date'    => getDateFormat($task->task_submission_date),
                            'mobile_no'          => isset($users['mobile_no'])?$users['mobile_no']:'',
                            'email'              => isset($users['email'])?$users['email']:'',
                            'task_name'          => $task->task_name
                        ];

        if(array_key_exists('task.sms',$permissions))
        {
            $arr_sms_data = $this->built_sms_data($details);
            $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
        }
        if (array_key_exists('task.email',$permissions))
        {
            $arr_mail_data = $this->built_mail_data($details); 
            $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id); 
        }

    }

    public function built_mail_data($arr_data)
     {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                  'STUDENT_NAME'     => $arr_data['student_name'],
                                  'TASK_NAME'        => $arr_data['task_name'],
                                  'PREVIOUS_TASK_STATUS'=> $arr_data['prev_task_status'],
                                  'NEW_TASK_STATUS'     => $arr_data['nxt_task_status'],
                                  'SUBMISSION_DATE'     => $arr_data['submission_date']];

            $arr_mail_data                        = [];
            $arr_mail_data['email_template_slug'] = 'task_status';
            
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
                                  'STUDENT_NAME'     => $arr_data['student_name'],
                                  'TASK_NAME'        => $arr_data['task_name'],
                                  'PREVIOUS_TASK_STATUS'=> $arr_data['prev_task_status'],
                                  'NEW_TASK_STATUS'     => $arr_data['nxt_task_status'],
                                  'SUBMISSION_DATE'     => $arr_data['submission_date']];
            

            $arr_sms_data                      = [];
            $arr_sms_data['sms_template_slug'] = 'task_status';
            
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }
}

