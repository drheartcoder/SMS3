<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TaskModel;
use App\Models\TaskStudentModel;
use App\Models\SchoolAdminModel;
use App\Models\StudentModel;
use App\Models\UserModel;
use App\Models\AcademicYearModel;
use App\Models\StudentIllnessModel;
use App\Models\SchoolTimeTableModel;
use App\Models\NotificationModel;
use App\Common\Traits\MultiActionTrait;

use App\Common\Services\CommonDataService;
use App\Common\Services\EmailService;

use Session;
use Validator;
use Flash;
use Sentinel;

class StudentIllnessController extends Controller
{
     public function __construct(CommonDataService $CommonDataService,EmailService $EmailService){

    	$this->CommonDataService    = $CommonDataService;
        $this->EmailService         = $EmailService;
    	$this->TaskModel            = new TaskModel();
    	$this->TaskStudentModel     = new TaskStudentModel();
    	$this->SchoolAdminModel     = new SchoolAdminModel();
        $this->AcademicYearModel    = new AcademicYearModel();
        $this->StudentIllnessModel  = new StudentIllnessModel();
        $this->StudentModel         = new StudentModel();
        $this->SchoolTimeTableModel = new SchoolTimeTableModel();
        $this->UserModel            = new UserModel();
    	
        $this->BaseModel = $this->StudentIllnessModel;
    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.parent_panel_slug')).'/leave_application';
        $this->module_title                 = translation('leave_application');
        
        $this->module_view_folder           = "parent.student_illness";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-medkit';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';

        $this->school_id                    = Session::has('school_id')?Session::get('school_id'):0;
        $this->academic_year                = Session::has('academic_year')?Session::get('academic_year'):0;
        $this->level_class_id               = Session::has('level_class_id')?Session::get('level_class_id'):0;
        $this->kid_id                       = Session::get('kid_id')?Session::get('kid_id'):0;

        $obj_professor                = $this->SchoolTimeTableModel
                                                   ->with('notifications','user_details')
                                                   ->where('level_class_id',$this->level_class_id)
                                                   ->where('school_id',$this->school_id)
                                                   ->where('academic_year_id',$this->academic_year)
                                                   ->groupBy('professor_id')
                                                   ->get();

        $this->professors = [];
        if(isset($obj_professor) && !is_null($obj_professor) && count($obj_professor)>0)
        {
            $this->professors = $obj_professor->toArray();
        }
        
        $this->obj_user                      = $this->UserModel->where('id',$this->kid_id)->first();

        $this->arr_view_data['module_icon']  =  $this->module_icon;
        $this->arr_view_data['create_icon']  =  $this->create_icon;
        $this->arr_view_data['edit_icon']    =  $this->edit_icon;
        $this->arr_view_data['theme_color']  =  $this->theme_color;
        $this->arr_view_data['page_title']   =  $this->module_title;
        $this->arr_view_data['module_url_path']   =  $this->module_url_path;

        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->user_id           = $obj_data->id;
        }

        $this->permissions = [];
        $obj_school_admin = SchoolAdminModel::with('notification_permissions','get_user_details')->where('school_id',$this->school_id)->first();
        $this->school_admin_id = 0;
        if(isset($obj_school_admin) && !empty($obj_school_admin))
        {

            $this->school_admin_id = $obj_school_admin->user_id;
            $arr_permissions       = $obj_school_admin->toArray();
            $this->school_admin    = $arr_permissions['get_user_details'];
            if(isset($arr_permissions['notification_permissions']['notification_permission']) && !empty($arr_permissions['notification_permissions']['notification_permission']))
            {   
                $this->permissions = json_decode($arr_permissions['notification_permissions']['notification_permission'],true);   
            }
        }
    }

    /*---------------------------------
    index() : load create page
    Auther  : sayali B
    
    ---------------------------------*/
    public function index()
    {
        $academic_year  =   $this->AcademicYearModel->where('id',$this->academic_year)->first();
        if(isset($academic_year) && !empty($academic_year))        
        {
            $arr_academic_year = $academic_year->toArray();
            $this->arr_view_data['academic_year']  =  $arr_academic_year;
        }
        $this->arr_view_data['page_title']    =  translation('add').' '.$this->module_title;
        $this->arr_view_data['module_title']  =  $this->module_title;

        return view($this->module_view_folder.'.create', $this->arr_view_data);
    }

   public function store(Request $request)
   {
        $messages = $arr_rules = [];

        $arr_rules['category']         = 'required';
        $arr_rules['reason']           = 'required';
        $arr_rules['start_date']       = 'required';
        
        $messages = array( 'required'  => translation('this_field_is_required'));
        
        $validator = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        
        $arr_data   = [];
        $category   = $request->input('category');
        $start_date = $request->input('start_date');
        $reason     = $request->input('reason');
        if($request->has('end_date'))
        {
            $arr_data['end_date']   =$request->input('end_date');
        }
        else
        {
            $arr_data['end_date']   =$request->input('start_date');   
        }
        $arr_data['reason_category']  = $category;
        $arr_data['start_date']       = $start_date;
        $arr_data['reason']           = $reason;
        $arr_data['academic_year_id'] = $this->academic_year;
        $arr_data['school_id']        = $this->school_id;
        $arr_data['kid_id']           = $this->kid_id;
        $arr_data['parent_id']        = $this->user_id;
        $arr_data['level_class_id']   = $this->level_class_id;

        $store_data     =   $this->StudentIllnessModel->create($arr_data);


        if($store_data)
        {
            $store_data = $this->StudentIllnessModel->with('get_level_class.level_details','get_level_class.class_details')->where('id',$store_data->id)->first();
            $kid_name = ucwords((isset($this->obj_user->first_name)?$this->obj_user->first_name:'').' '.(isset($this->obj_user->first_name)?$this->obj_user->last_name:''));

            $result = $this->send_notifications($this->school_admin_id,$store_data,$this->permissions,$this->school_admin,$kid_name,'school_admin');
            
            foreach ($this->professors as $key => $value) {
                
                $arr_permissions = [];
                if(isset($value['notifications']['notification_permission']) && $value['notifications']['notification_permission']!='')
                {
                    $arr_permissions =  json_decode($value['notifications']['notification_permission'],true);
                }
                $result = $this->send_notifications($value['professor_id'],$store_data,$arr_permissions,$value['user_details'],$kid_name,'professor');         
            }

            Flash::success(translation('absence_reason_stored_successfully'));
            return redirect()->back();
        }
        else
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();   
        }
   }

   public function send_notifications($user_id,$data,$permissions,$user_details,$kid,$user)
   {
    
        if(array_key_exists('student_illness.app',$permissions))
        {
         
            $arr_notification = [];
            $arr_notification['school_id']          =   $this->school_id;
            $arr_notification['from_user_id']       =   $this->user_id;
            $arr_notification['to_user_id']         =   $user_id;
            $arr_notification['user_type']          =   config('app.project.role_slug.parent_role_slug');
            $arr_notification['notification_type']  =   'Student Absence Reason';
            $arr_notification['title']              =   'Student Absence Reason:'.$this->first_name.' '.$this->last_name.' added absence of kid '.$kid.' from '.getDateFormat($data->start_date).' - '.getDateFormat($data->end_date);

            $result = NotificationModel::create($arr_notification);
        }
        $details          = [
                                    'email'       =>  isset($user_details['email'])?$user_details['email']:'',
                                    'mobile_no'   =>  isset($user_details['mobile_no'])?$user_details['mobile_no']:'',
                                    'start_date'  =>  getDateFormat($data->start_date),
                                    'end_date'    =>  getDateFormat($data->end_date),
                                    'level'       =>  isset($data['get_level_class']['level_details']['level_name'])?$data['get_level_class']['level_details']['level_name']:'',
                                    'class'       =>  isset($data['get_level_class']['class_details']['class_name'])?$data['get_level_class']['class_details']['class_name']:'',
                                    'reason'      =>  $data->reason
                            ];
        if(array_key_exists('student_illness.sms',$permissions))
        {
            $arr_sms_data = $this->built_sms_data($details,$user);
            $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
        }
        if (array_key_exists('student_illness.email',$permissions))
        {
            $arr_mail_data = $this->built_mail_data($details,$user);
            $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
        }
   }

   public function built_mail_data($arr_data,$user)
     {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {

            if($user == 'school_admin')
            {
                $arr_built_content = [
                                      'FIRST_NAME'       => 'School Admin',
                                      'START_DATE'       => $arr_data['start_date'],
                                      'TO_DATE'          => $arr_data['end_date'],
                                      'LEVEL'            => $arr_data['level'],
                                      'CLASS'            => $arr_data['class'],
                                      'REASON'           => $arr_data['reason'],
                                      'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                      'STUDENT_NAME'     => ucwords($this->obj_user->first_name.' '.$this->obj_user->last_name)];
            }
            else
            {
                $arr_built_content = [
                                      'FIRST_NAME'       => 'Professor',
                                      'START_DATE'       => $arr_data['start_date'],
                                      'TO_DATE'          => $arr_data['end_date'],
                                      'LEVEL'            => $arr_data['level'],
                                      'CLASS'            => $arr_data['class'],
                                      'REASON'           => $arr_data['reason'],
                                      'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                      'STUDENT_NAME'     => ucwords($this->obj_user->first_name.' '.$this->obj_user->last_name)];    
            }
            

            $arr_mail_data                        = [];
            $arr_mail_data['email_template_slug'] = 'student_illness';
            
            $arr_mail_data['arr_built_content']   = $arr_built_content;
            $arr_mail_data['user']                = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_sms_data($arr_data,$user)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'START_DATE'       => $arr_data['start_date'],
                                  'TO_DATE'          => $arr_data['end_date'],
                                  'LEVEL'            => $arr_data['level'],
                                  'CLASS'            => $arr_data['class'],
                                  'REASON'           => $arr_data['reason'],
                                  'STUDENT_NAME'     => ucwords($this->obj_user->first_name.' '.$this->obj_user->last_name)];
            

            $arr_sms_data                      = [];
            $arr_sms_data['sms_template_slug'] = 'student_illness';
            
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }
}

