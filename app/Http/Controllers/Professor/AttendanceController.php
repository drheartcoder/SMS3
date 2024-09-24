<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;

use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\UserRoleModel;   
use App\Models\RoleModel;
use App\Models\AcademicYearModel;  
use App\Models\SchoolCourseModel;
use App\Common\Services\LanguageService;
use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;
use App\Models\LevelClassModel;
use App\Models\StudentModel;
use App\Models\StudentPeriodAttendanceModel;
use App\Models\SchoolPeriodsModel;
use App\Models\EmployeeAttendanceModel;
use App\Models\SchoolTimeTableModel;
use App\Models\StudentIllnessModel;
use App\Models\CourseModel;
use App\Models\NotificationModel;
use App\Models\SchoolPeriodTimingModel;
use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;
use PDF;

class AttendanceController extends Controller
{
    use MultiActionTrait;
    public function __construct(    
                                    UserModel $user,
                                    UserTranslationModel $translation,
                                    UserRoleModel $user_role_model,
                                    RoleModel $role_model,
                                    EmailService $mail_service,
                                    LanguageService $language,
                                    AcademicYearModel $year,
                                    CommonDataService $common,
                                    SchoolCourseModel $schoolCourse,
                                    LevelClassModel $levelClass,
                                    StudentModel $student,
                                    StudentPeriodAttendanceModel $attendance,
                                    SchoolPeriodsModel $periods,
                                    EmployeeAttendanceModel $employee_attendance,
                                    SchoolTimeTableModel $school_timetablemodel,
                                    StudentIllnessModel $StudentIllnessModel,
                                    CourseModel $CourseModel,
                                    NotificationModel $NotificationModel,
                                    SchoolPeriodTimingModel $SchoolPeriodTimingModel
                                )
    {
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->AcademicYearModel            = $year;
        $this->EmailService                 = $mail_service;
        $this->LanguageService              = $language;
        $this->CommonDataService            = $common;
        $this->SchoolPeriodsModel           = $periods;
        $this->EmployeeAttendanceModel      = $employee_attendance;
        $this->SchoolCourseModel            = $schoolCourse;
        $this->LevelClassModel              = $levelClass;
        $this->StudentModel                 = $student;
        $this->StudentPeriodAttendanceModel = $attendance;
        $this->SchoolTimeTableModel         = $school_timetablemodel;
        $this->StudentIllnessModel          = $StudentIllnessModel;
        $this->CourseModel                  = $CourseModel;
        $this->NotificationModel            = $NotificationModel;
        $this->SchoolPeriodTimingModel      = $SchoolPeriodTimingModel;
        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.professor_panel_slug')).'/attendance';
        
        $this->module_title                 = translation("attendance");
        $this->modyle_url_slug              = translation("attendance");

        $this->module_view_folder           = "professor.attendance";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-user';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::has('school_id')?Session::get('school_id'):0;
        $this->academic_year                = Session::has('academic_year')?Session::get('academic_year'):0;
        $this->first_name = $this->last_name =$this->ip_address ='';
        $this->weekly_days = config('app.project.week_days');

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
            $this->user_id           = $obj_data->id;
         }
         
        /* Activity Section */

    }   

    public function index($role)
    {   
        $arr_course = $arr_academic_year = [];

        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }  
        $obj_levels = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id);
        
        if(!empty($obj_levels))
        {
            $this->arr_view_data['levels'] = $obj_levels -> toArray();         
        }

        $page_title = translation("manage")." ".str_plural(translation($role))." ".str_plural($this->module_title);
        $this->arr_view_data['role']            = $role;
        $this->arr_view_data['enc_id']          = base64_encode($this->user_id);
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        if($role == config('app.project.role_slug.student_role_slug'))
        {
            return view($this->module_view_folder.'.index', $this->arr_view_data);
        }
        if($role == config('app.project.role_slug.professor_role_slug'))
        {
            return view($this->module_view_folder.'.view_professor', $this->arr_view_data);
        }
    }

    public function create($role)
    {
        
            $arr_course = $arr_academic_year = [];

     
            $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 



            if($academic_year)
            {
                $arr_academic_year = explode(',',$academic_year);
            }  
            $level = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id);
            if(isset($level) && $level != null)
            {
                $this->arr_view_data['levels']      = $level->toArray();    
            }

            $cond_arr = [
                            'school_id'        => $this->school_id,
                            'academic_year_id' => $this->academic_year,
                        ];

            $SCHOOL_ID      = $this->school_id;          

            $obj_time_table = $this->SchoolTimeTableModel->where($cond_arr)
                                                        ->whereHas('professor_details',function($q1)use($SCHOOL_ID){
                                                            $q1->where('is_active','=', 1);
                                                            $q1->where('has_left','=', 0);
                                                            $q1->where('school_id','=',$SCHOOL_ID);
                                                        })
                                                        ->with(['user_details' => function ($query)
                                                        {
                                                              $query->select('id','profile_image');
                                                        }])
                                                        ->with(['professor_subjects','level_details','class_details'])
                                                        ->where('professor_id',$this->user_id)
                                                        ->get();

            $arr_time_table = [];
            if($obj_time_table)
            {
                $arr_time_table = $obj_time_table->toArray();
            }

            /*Bring Maximum  period for header count */
            $school_time_table = $this->SchoolTimeTableModel->getTable();
            $maxPeriod = DB::table($school_time_table)
                            ->select(DB::raw('MAX(periods_no) as max_period_no'))
                            ->where($school_time_table.'.academic_year_id','=',$this->academic_year)
                            ->where($school_time_table.'.school_id','=',$this->school_id)
                            ->where($school_time_table.'.professor_id','=',$this->user_id)
                            ->get();
            /*Bring Maximum  period for header count */
            if(empty($arr_time_table)){
                Flash::error(translation("no_record_found"));
            }
           
            $arr_holiday = isset($arr_periods['weekly_off'])&&$arr_periods['weekly_off']!=''?json_decode($arr_periods['weekly_off']):config('app.project.default_weekly_off');
             
            $this->arr_view_data['arr_time_table']  = $arr_time_table;
            $this->arr_view_data['weekly_days']     = $this->weekly_days;
            $this->arr_view_data['arr_holiday']     = $arr_holiday;
            $this->arr_view_data['period_no']       = !empty($maxPeriod)&&$maxPeriod[0]->max_period_no?$maxPeriod[0]->max_period_no:'1';
            
            $page_title = translation("create")." ".translation($role)." ".$this->module_title;
            $view_page_title = translation("view")." ".translation($role)." ".$this->module_title;
            $this->arr_view_data['page_title']      = $page_title;
            $this->arr_view_data['view_page_title'] = $view_page_title;
            $this->arr_view_data['role']            = $role;
            $this->arr_view_data['module_title']    = $this->module_title;
            $this->arr_view_data['module_icon']     = $this->module_icon;
            $this->arr_view_data['create_icon']     = $this->create_icon;
            $this->arr_view_data['module_url_path'] = $this->module_url_path;
            $this->arr_view_data['theme_color']     = $this->theme_color;
            return view($this->module_view_folder.'.create', $this->arr_view_data);        
        
    }

    public function edit($attendance_data,$role)
    {
        $obj_data = '';
        if($role!= '')
        {
            if($role == 'professor')
            {    
                $obj_data     = $this->ProfessorModel
                                     ->with(['get_user_details'=>function($q)
                                     {
                                        $q->select('id','national_id');
                                     }])
                                     ->where('school_id',$this->school_id)
                                     ->where('is_active',1)
                                     ->where('has_left',0)
                                 ->get();
            }
            elseif ($role == 'employee') {
                $obj_data     = $this->EmployeeModel
                                     ->with(['get_user_details'=>function($q)
                                     {
                                        $q->select('id','national_id');
                                     }])
                                     ->where('school_id',$this->school_id)
                                     ->where('is_active',1)
                                     ->where('has_left',0)
                                 ->get();
            }
        }
        $arr_data = [];
        if(isset($obj_data) && $obj_data != null)
        {
            $arr_data = $obj_data->toArray();
        }
        if(isset($arr_data) && !empty($arr_data))
        {
            $this->arr_view_data['arr_data']       = $arr_data;    
        }

        $this->arr_view_data['attendance']      = $attendance_data->toArray();
        $page_title = translation("edit")." ".$this->module_title;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['role']            = $role;
        $this->arr_view_data['enc_id']          = base64_encode($attendance_data->id);
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['create_icon']     = $this->create_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return;        
    }

    function store(Request $request)
    {
        $stud_details = $this->StudentModel
                             ->with('notifications')
                             ->where('level_class_id',$request->input('class'))
                             ->where('school_id',$this->school_id)
                             ->where('academic_year_id',$this->academic_year)
                             ->where('has_left',0)
                             ->get();
       
        $school_id = $this->school_id;
        $arr_rules['arr_attendance'] = 'required';
        $messages['required']        = 'This field is required';    

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $attendance = $request->input('arr_attendance');

        $attendance_arr = $arr_data = [];
        
        if(isset($attendance) && !empty($attendance))
        {
            $attendance_arr =   json_encode($attendance);
        }
        $level_id = $request->input('level');
        $class_id = $request->input('class');

        //$level_class = $this->LevelClassModel->where(['level_id'=>$level_id,'class_id'=>$class_id])->first();
        $arr_data = array(
                            'school_id'         => $school_id,
                            'level_class_id'    => $class_id,
                            'course_id'         => $request->input('subject_id'),
                            'professor_id'      => $this->user_id,
                            'period_no'         => $request->input('period_no'),
                            'attendance'        => $attendance_arr,
                            'attendance_date'   => $request->input('date'),
                            'start_time'        => '11.00AM',
                            'end_time'          => '12.00PM',
                            'academic_year_id'  => $this->academic_year
                         );

        $res = $this->StudentPeriodAttendanceModel->create($arr_data);
        $arr_illness = $arr_details = $arr =[];
        $date = date('Y-m-d');
        $illness_data = $this->StudentIllnessModel->where('level_class_id',$class_id)->where('academic_year_id',$this->academic_year)->where('start_date','<=',$date)->where('end_date','>=',$date)->get();
        
        if(isset($illness_data) && count($illness_data)>0)
        {   $arr_data = $illness_data->toArray();
            foreach ($arr_data as $key => $value) {
                array_push($arr_illness,$value['kid_id']);
            }
        }

        $arr_details = array_keys($attendance,'absent');
         
        foreach ($arr_details as $key => $value) {
            if(!in_array($value,$arr_illness))
            {
                array_push($arr,$value);
            }
        }
   
        if($res)
        {
                $data = $this->StudentModel->with('parent_notifications','get_user_details','get_level_class.level_details','get_level_class.class_details','get_parent_details')->whereIn('user_id',$arr)->where('school_id',$this->school_id)->where('academic_year_id',$this->academic_year)->get();

                $course  = $this->CourseModel->where('id',$request->input('subject_id'))->first();
                if(isset($data) && $data!=null)
                {
                    $data= $data->toArray();
                    foreach ($data as $key => $value) {

                        $student_name = ucwords((isset($value['get_user_details']['first_name'])?$value['get_user_details']['first_name']:'').' '.(isset($value['get_user_details']['last_name'])?$value['get_user_details']['last_name']:''));

                        $level        = isset($value['get_level_class']['level_details']['level_name'])?$value['get_level_class']['level_details']['level_name']:'';
                        $class        = isset($value['get_level_class']['class_details']['class_name'])?$value['get_level_class']['class_details']['class_name']:'';

                        if(isset($value['parent_notifications']['notification_permission']) && $value['parent_notifications']['notification_permission']!='')
                        {
                            $permissions = json_decode($value['parent_notifications']['notification_permission'],true);
                            if(isset($permissions) && count($permissions)>0)
                            {

                                if(array_key_exists('attendance.app',$permissions))
                                {
                                 
                                    $arr_notification = [];
                                    $arr_notification['school_id']          =   $this->school_id;
                                    $arr_notification['from_user_id']       =   $this->user_id;
                                    $arr_notification['to_user_id']         =   $value['parent_id'];
                                    $arr_notification['user_type']          =  config('app.project.role_slug.professor_role_slug');
                                    $arr_notification['notification_type']  =  'Attendance';
                                    $arr_notification['title']              =  ' today\'s Attendance added '.$student_name.' of class '.$level.' '.$class.' is absent for course '.$course->course_name;
                                    $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.student_role_slug').'/attendance';
                                    $this->NotificationModel->create($arr_notification);
                                }
                                $details    =   [
                                                    'first_name'  => isset($value['get_parent_details']['first_name'])?ucwords($value['get_parent_details']['first_name']):'',
                                                    'level'       => $level,
                                                    'class'       => $class,
                                                    'course'      => $course->course_name,
                                                    'student_name'=> $student_name,
                                                    'status'      => 'Absent',
                                                    'mobile_no'   => isset($value['get_parent_details']['mobile_no'])?$value['get_parent_details']['mobile_no']:'',
                                                    'email'       => isset($value['get_parent_details']['email'])?$value['get_parent_details']['email']:''
                                                ];

                                if(array_key_exists('attendance.sms',$permissions))
                                {
                                    $arr_sms_data = $this->built_sms_data($details,'add');
                                    $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
                                }
                                if (array_key_exists('attendance.email',$permissions))
                                {
                                    $arr_mail_data = $this->built_mail_data($details,'add'); 
                                    $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id); 
                                }
                            }

                        }
                    }
                }
           
            
            Flash::success($this->module_title." ".translation("created_successfully"));
        }
        else
        {
            Flash::error(translation("something_went_wrong_while_creating")." ".$this->module_title);
        }
        return redirect()->back();
    }

    public function update(Request $request,$enc_id)
    {
        $id = base64_decode($enc_id);
        $arr_rules['arr_attendance'] = 'required';
        $messages['required']        = 'This field is required'; 

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $attendance = $request->input('arr_attendance');
        $attendance_arr = $arr_data = [];
        if(isset($attendance) && !empty($attendance))
        {
            $attendance_arr =   json_encode($attendance);
        }
        
        $arr_data['school_id']   = $this->school_id;
        $arr_data['attendance']  = $attendance_arr;
        $attemndance_data = $this->StudentPeriodAttendanceModel->where('id',$id)->first();
        $res = $this->StudentPeriodAttendanceModel->where('id',$id)->update($arr_data);   


        $arr_illness = $arr_details = $arr =[];
        $illness_data = $this->StudentIllnessModel->where('level_class_id',$attemndance_data->level_class_id)->where('academic_year_id',$this->academic_year)->where('start_date','<=',$attemndance_data->attendance_date)->where('end_date','>=',$attemndance_data->attendance_date)->get();
        
        if(isset($illness_data) && count($illness_data)>0)
        {   $arr_data = $illness_data->toArray();
            foreach ($arr_data as $key => $value) {
                array_push($arr_illness,$value['kid_id']);
            }
        }

        $arr_details = array_keys($attendance,'absent');
         
        foreach ($arr_details as $key => $value) {
            if(!in_array($value,$arr_illness))
            {
                array_push($arr,$value);
            }
        }
        
        if($res)
        {
                $data = $this->StudentModel->with('parent_notifications','get_user_details','get_level_class.level_details','get_level_class.class_details','get_parent_details')->whereIn('user_id',$arr)->where('school_id',$this->school_id)->where('academic_year_id',$this->academic_year)->get();

                $course  = $this->CourseModel->where('id',$request->input('subject_id'))->first();
                if(isset($data) && $data!=null)
                {
                    $data= $data->toArray();
                    foreach ($data as $key => $value) {

                        $student_name = ucwords((isset($value['get_user_details']['first_name'])?$value['get_user_details']['first_name']:'').' '.(isset($value['get_user_details']['last_name'])?$value['get_user_details']['last_name']:''));

                        $level        = isset($value['get_level_class']['level_details']['level_name'])?$value['get_level_class']['level_details']['level_name']:'';
                        $class        = isset($value['get_level_class']['class_details']['class_name'])?$value['get_level_class']['class_details']['class_name']:'';

                        if(isset($value['parent_notifications']['notification_permission']) && $value['parent_notifications']['notification_permission']!='')
                        {
                            $permissions = json_decode($value['parent_notifications']['notification_permission'],true);
                            if(array_key_exists('attendance.app',$permissions))
                            {
                             
                                $arr_notification = [];
                                $arr_notification['school_id']          =   $this->school_id;
                                $arr_notification['from_user_id']       =   $this->user_id;
                                $arr_notification['to_user_id']         =   $value['parent_id'];
                                $arr_notification['user_type']          =  config('app.project.role_slug.professor_role_slug');
                                $arr_notification['notification_type']  =  'Attendance';
                                $arr_notification['title']              =  ' today\'s Attendance added '.$student_name.' of class '.$level.' '.$class.' is absent for course '.$course->course_name;
                                $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.student_role_slug').'/attendance';
                                $this->NotificationModel->create($arr_notification);
                            }
                            $details    =   [
                                                'first_name'  => isset($value['get_parent_details']['first_name'])?ucwords($value['get_parent_details']['first_name']):'',
                                                'level'       => $level,
                                                'class'       => $class,
                                                'course'      => $course->course_name,
                                                'student_name'=> $student_name,
                                                'status'      => 'Absent',
                                                'mobile_no'   => isset($value['get_parent_details']['mobile_no'])?$value['get_parent_details']['mobile_no']:'',
                                                'email'       => isset($value['get_parent_details']['email'])?$value['get_parent_details']['email']:''
                                            ];

                            if(array_key_exists('attendance.sms',$permissions))
                            {
                                $arr_sms_data = $this->built_sms_data($details,'update');
                                $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
                            }
                            if (array_key_exists('attendance.email',$permissions))
                            {
                                $arr_mail_data = $this->built_mail_data($details,'update'); 
                                $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id); 
                            }


                        }
                    }
                }
            Flash::success($this->module_title." ".translation("updated_successfully"));
        }
        else
        {
            Flash::error(translation("something_went_wrong_while_updating")." ".$this->module_title);
        }
        return redirect()->back();
    }
        
   public function getClasses(Request $request)
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

   public function get_students(Request $request)
   {
        $data = $flag = '';
        $stud_attendance = $record = [];
        $level_id = $request->input('level');
        $class_id = $request->input('cls');
        $period   = $request->input('period');
        $date     = $request->input('date');

        $arr_illness = [];
        $illness_data = $this->StudentIllnessModel->where('level_class_id',$class_id)->where('academic_year_id',$this->academic_year)->where('start_date','<=',$date)->where('end_date','>=',$date)->get();
        
        if(isset($illness_data) && count($illness_data)>0)
        {   $arr_data = $illness_data->toArray();
            foreach ($arr_data as $key => $value) {
                
                $arr_illness[$value['kid_id']]=$value['reason'];
            }
            
        }
            $student_data = $this->CommonDataService->get_students($class_id);

            if(isset($student_data) && count($student_data)>0)
            {
                $attendance_data =$this->StudentPeriodAttendanceModel->where(['attendance_date'=>$date,'level_class_id'=>$class_id,'school_id'=>$this->school_id,'period_no'=>$period])->first();

                if($attendance_data)
                {
                    if(isset($attendance_data['attendance']) && !empty($attendance_data['attendance']))
                    {
                        $stud_attendance =  json_decode($attendance_data['attendance'],true);
                    }

                    foreach ($student_data as $key => $student) {
                       $data .='<tr><td>'.($key+1).'</td>';
                       $data .='<td>'.$student['get_user_details']['first_name'].' '.$student['get_user_details']['last_name'].'</td>';
                       $data .= '<td>'.$student['get_user_details']['national_id'].'</td>';

                       $data .= '<td><div class="radio-btns">';  
                       $data .= '<div class="radio-btn">';
                       $data .= '<input type="radio" id="f-option'.$key.'" name="arr_attendance['.$student['user_id'].']" value="present" ';
                                if(array_key_exists($student['user_id'],$stud_attendance))
                                {
                                    if($stud_attendance[$student['user_id']] == 'present')
                                    {
                                        $data .= 'checked';
                                    } 
                                }
                       $data .= '>';
                       $data .= '<label for="f-option'.$key.'">'.translation('present').'</label>';
                       $data .= '<div class="check"></div></div></div> </td>';

                       $data .= '<td><div class="radio-btns">';  
                       $data .= '<div class="radio-btn">';
                       $data .= '<input type="radio" id="s-option'.$key.'" name="arr_attendance['.$student['user_id'].']" value="absent"';
                                if(array_key_exists($student['user_id'],$stud_attendance))
                                {
                                    if($stud_attendance[$student['user_id']] == 'absent')
                                    {
                                        $data .= 'checked';
                                    } 
                                }

                       $data .= '>';
                       $data .= '<label for="s-option'.$key.'">'.translation('absent').'</label>';
                       $data .= '<div class="check"></div></div></div> </td>';

                       $data .= '<td><div class="radio-btns">';  
                       $data .= '<div class="radio-btn">';
                       $data .= '<input type="radio" id="t-option'.$key.'" name="arr_attendance['.$student['user_id'].']" value="late"';
                                if(array_key_exists($student['user_id'],$stud_attendance))
                                {
                                    if($stud_attendance[$student['user_id']] == 'late')
                                    {
                                        $data .= 'checked';
                                    } 
                                }
                       $data .= '>';
                       $data .= '<label for="t-option'.$key.'">'.translation('late').'</label>';
                       $data .= '<div class="check"></div></div></div> </td>';
                       $data .= '</tr>';

                       $record = ['data'=>$data,'flag'=>'true','enc_id'=>base64_encode($attendance_data->id)];

                    }
                }
                else
                {
                    
                    foreach ($student_data as $key => $student) {
                       $data .='<tr>';
                       $data .='<td>'.$student['get_user_details']['first_name'].' '.$student['get_user_details']['last_name'].'</td>';
                       $data .= '<td>'.$student['get_user_details']['national_id'].'</td>';

                       $data .= '<td><div class="radio-btns">';  
                       $data .= '<div class="radio-btn">';
                       $data .= '<input type="radio" id="f-option'.$key.'" name="arr_attendance['.$student['user_id'].']" value="present" checked>';
                       $data .= '<label for="f-option'.$key.'">'.translation('present').'</label>';
                       $data .= '<div class="check"></div></div></div> </td>';

                       $data .= '<td ';
                       if(array_key_exists($student['get_user_details']['id'], $arr_illness))
                       {
                            $data .= 'title ="'.$arr_illness[$student['get_user_details']['id']];
                       }
                       $data .= '"><div class="radio-btns">';  
                       $data .= '<div class="radio-btn">';

                       
                       $data .= '<input type="radio" id="s-option'.$key.'" name="arr_attendance['.$student['user_id'].']" value="absent"';
                       if(array_key_exists($student['get_user_details']['id'], $arr_illness))
                       {
                            $data .= 'checked';
                       }
                       $data .= '>';
                       $data .= '<label for="s-option'.$key.'">'.translation('absent').'</label>';
                       $data .= '<div class="check"></div></div></div> </td>';

                       $data .= '<td><div class="radio-btns">';  
                       $data .= '<div class="radio-btn">';
                       $data .= '<input type="radio" id="t-option'.$key.'" name="arr_attendance['.$student['user_id'].']" value="late">';
                       $data .= '<label for="t-option'.$key.'">'.translation('late').'</label>';
                       $data .= '<div class="check"></div></div></div> </td>';
                       $data .= '</tr>';

                       $record = ['data'=>$data,'flag'=>'false'];
                    }
                }
            }
            else
            {

                $data .='<tr><td colspan="5"><div class="alert alert-danger" style="text-align:center">'.translation('no_data_available').'</div></td></tr>';
                $record = ['data'=>$data,'flag'=>'false'];
            }
       /* }*/
    return $record;
   }

   public function view($role)
   {
        $arr_course = $arr_academic_year = [];

     
            $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

            if($academic_year)
            {
                $arr_academic_year = explode(',',$academic_year);
            }  
            $level = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id);

            if(isset($level) && $level != null)
            {
                $this->arr_view_data['levels']      = $level->toArray();    
            }
            $obj_course = $this->SchoolCourseModel
                                            ->with('get_course')
                                            ->where('school_id',$this->school_id)
                                            ->whereIn('academic_year_id',$arr_academic_year)
                                            ->get();
            if(!empty($obj_course))
            {
                $arr_course = $obj_course ->toArray();
            }

            if(!empty($arr_course))
            {
                $this->arr_view_data['courses']      = $arr_course;
            }
        $view_page_title   = translation("view")." ".translation($role)." ".$this->module_title;
        $page_title        = translation("create")." ".translation($role)." ".$this->module_title;
        $this->arr_view_data['role']            = $role;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['view_page_title'] = $view_page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.view', $this->arr_view_data);
   }

   public function get_attendance($class,$date,$period=FALSE)
   {

        $data = '';
        $student = $arr_attendance = $periods = $stud_attendance = [];
        /*$level_class = $this->LevelClassModel->where(['level_id'=>$level,'class_id'=>$class])->first();
        
        if(isset($level_class) && $level_class != null)
        {*/

            $student_data = $this->CommonDataService->get_students($class);


            if(isset($student_data) && count($student_data)>0)
            {
                $student = $student_data->toArray();
                $attendance_data = $this->StudentPeriodAttendanceModel
                                        ->where('school_id',$this->school_id)
                                        ->where('academic_year_id',$this->academic_year)
                                        ->where('attendance_date',$date)
                                        ->where('professor_id',$this->user_id)
                                        ->where('level_class_id',$class);
                                        if($period!='')
                                        {
                                            $attendance_data = $attendance_data->where('period_no',$period);
                                        }
                                        $attendance_data = $attendance_data->get();

                if(isset($attendance_data) && count($attendance_data)>0)
                {
                    $arr_attendance = $attendance_data->toArray();
                    foreach ($arr_attendance as $key => $attendance) 
                    {
                        if(isset($attendance['period_no']) && $attendance['period_no']!='')
                        {
                            array_push($periods,$attendance['period_no']);
                        }
                    }
                    
                }
                else
                {
                    $data .= '<tr><td><div class="alert alert-danger" style="text-align:center">'.translation('no_data_available').'</div></td></tr>';
                    return  $data;

                }
                if (isset($student) && count($student)>0) 
                {
                    $data .= '<thead><tr><th>'.translation('sr_no').'</th>';
                    $data .= '<th>'.translation('student').' '.translation('name').'</th>';
                    $data .= '<th>'.translation('national_id').'</th>';  
                              
                    if($period)
                    {
                        $data .= '<th>'.translation('period').' '.$period.'</th>';
                    }
                    else
                    {
                        foreach ($periods as $key => $p) {
                            $data .= '<th>'.translation('period').' '.$p.'</th>';       
                        }
                    }
                    $data .= '</tr></thead><tbody>';
                    
                    foreach ($student as $key => $stud) 
                    {
                        $data .= '<tr><td>'.($key+1).'</td>';
                        $data .= '<td>'.$stud['get_user_details']['first_name'].' '.$stud['get_user_details']['last_name'].'</td>';
                        $data .= '<td>'.$stud['get_user_details']['national_id'].'</td>';
                        if(isset($arr_attendance) && count($arr_attendance)>0)
                        {
                            foreach ($arr_attendance as $key => $attendance) {
                                $stud_attendance =  json_decode($attendance['attendance'],true);
                                $data .= '<td><div ';
                                if(array_key_exists($stud['user_id'],$stud_attendance))
                                {
                                        if($stud_attendance[$stud['user_id']] == 'present')
                                        {
                                            $data .= 'class="alert alert-success">';
                                        } 

                                        if($stud_attendance[$stud['user_id']] == 'absent')
                                        {
                                            $data .= 'class="alert alert-danger">';
                                        }

                                        if($stud_attendance[$stud['user_id']] == 'late')
                                        {
                                            $data .= 'class="alert alert-warning">';
                                        }     
                                        $data .= translation($stud_attendance[$stud['user_id']]).'</div></td>';
                                }
                                
                            }
                            
                        }
                        $data .='</tr>';
                    }
                    $data .='</tbody>';
                    return $data;
                }

            }
            else
            {
                $data .='<tr><td><div class="alert alert-danger" style="text-align:center">'.translation('no_data_available').'</div></td></tr>';
                return $data;
            }
           
        //}
    
   }

   public function get_periods(Request $request)
   {
        $level_id = $request->input('level');
        $class_id = $request->input('class');

        $options  = '';
        $obj_periods = $this->SchoolPeriodsModel->where(['school_id'=>$this->school_id,'academic_year_id'=>$this->academic_year,'level_id'=>$level_id,'class_id'=>$class_id])->first();
        
        if(isset($obj_periods))
        {
            $arr_periods  = $obj_periods -> toArray();
            
            if(count($arr_periods)>0)
            {
                $options .= '<option value="">'.translation('select_period_no').'</option>';  

                for ($i=0; $i < $arr_periods['num_of_periods']; $i++) { 
                   $options .= '<option value='.($i+1).'>'.translation('period').' '.($i+1).'</option>';
                }
            } 
        }
        return $options;
   }

   public function view_professor($enc_id)
   {
        $arr_data = $temp = [];
        $data = $temp = '';
        $attendance_data =  $this->EmployeeAttendanceModel
                                 ->where('school_id',$this->school_id)  
                                 ->where('academic_year_id',$this->academic_year)  
                                 ->where('user_role','professor')
                                 ->get(['attendance']);

        if(isset($attendance_data) && !empty($attendance_data))
         {
            $arr_attendance = $attendance_data->toArray();
            foreach ($arr_attendance as $key => $prof_attendance) 
            {
                $attendance_arr = json_decode($prof_attendance['attendance'],true);

                if(isset($attendance_arr) && !empty($attendance_arr))
                {
                    if(array_key_exists($this->user_id,$attendance_arr))
                    {
                        if($attendance_arr[$this->user_id] == 'present')
                        {
                            $temp['status'] = ucfirst($attendance_arr[$this->user_id]);
                        } 

                        if($attendance_arr[$this->user_id] == 'absent')
                        {
                            $temp['status'] = ucfirst($attendance_arr[$this->user_id]);
                        }

                        if($attendance_arr[$this->user_id] == 'late')
                        {
                            $temp['status'] = ucfirst($attendance_arr[$this->user_id]);
                        }

                        array_push($arr_data,$temp);
                    }
                }
            }       
         }
        $page_title        = translation("view")." ".translation('professor')." ".$this->module_title;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['role']            = 'professor';
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.view_professor', $this->arr_view_data);
   }

   public function get_events()
   {
     $arr_data =  $temp = [];
     $data = '';
     $attendance_data = $this->EmployeeAttendanceModel
                             ->where('school_id',$this->school_id)  
                             ->where('academic_year_id',$this->academic_year)  
                             ->where('user_role','professor')
                             ->get(['attendance','date']);

     if(isset($attendance_data) && !empty($attendance_data))
     {
        $arr_attendance = $attendance_data->toArray();

        foreach ($arr_attendance as $key => $prof_attendance) 
        {
            $attendance_arr = json_decode($prof_attendance['attendance'],true);

            if(isset($attendance_arr) && !empty($attendance_arr))
            {
                if(array_key_exists($this->user_id,$attendance_arr))
                {
                    $temp['start'] = $prof_attendance['date'];
                    $temp['end']   = $prof_attendance['date'];
                    $temp['textColor'] = '#000';
                    if($attendance_arr[$this->user_id] == 'present')
                    {
                        $temp['title']          = ucfirst($attendance_arr[$this->user_id]);
                        $temp['color']          = '#dff0d8';
                        $temp['bold']           = 'bold';

                        $event_date = date_create($prof_attendance['date']);
                        $event_date = date_format($event_date,'Y-m-d');

                        $temp['event_date']= $event_date;
                        
                    } 

                    if($attendance_arr[$this->user_id] == 'absent')
                    {
                        $temp['title']          = ucfirst($attendance_arr[$this->user_id]);
                        $temp['color']          = '#f2dede';

                        $event_date = date_create($prof_attendance['date']);
                        $event_date = date_format($event_date,'Y-m-d');

                        $temp['event_date']= $event_date;
                    }

                    if($attendance_arr[$this->user_id] == 'late')
                    {
                        $temp['title']          = ucfirst($attendance_arr[$this->user_id]);
                        $temp['color']          = '#fcf8e3';

                        $event_date = date_create($prof_attendance['date']);
                        $event_date = date_format($event_date,'Y-m-d');

                        $temp['event_date']= $event_date;
                    }

                    array_push($arr_data,$temp);
                }
            }
        }       

        $data = json_encode($arr_data); 
     }
     return $data;
   }

   public function get_students_records(Request $request,$fun_type='')
   {

        $data = '';
        $arr_details = $data_attendance = $arr_academic_year = [];
        $level = $request->input('level');
        $class = $request->input('class');
        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');
        $totalColCount = 0;
        if(Session::has('attendance_start_date'))
        {
            Session::forget('attendance_start_date');
            Session::put('attendance_start_date',$start_date);
        }
        else
        {
            Session::put('attendance_start_date',$start_date);
        }

        if(Session::has('attendance_end_date'))
        {
            Session::forget('attendance_end_date');
            Session::put('attendance_end_date',$end_date);
        }
        else
        {
            Session::put('attendance_end_date',$end_date);
        }
     
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        } 
        $level_class = $this->LevelClassModel->where(['id'=>$class])->first();
        
        if(isset($level_class) && $level_class != null)
        {
            
            $details = $this->StudentModel->with('get_user_details')->where(['level_class_id'=>$level_class->id,'school_id'=>$this->school_id])->get();
            Session::put('level_class',$level_class->id);

        }

        if(isset($details) && !empty($details))
        {
            $arr_details = $details->toArray();
        }

        
        $obj_periods = $this->SchoolPeriodsModel->where(['school_id'=>$this->school_id,'academic_year_id'=>$this->academic_year,'level_class_id'=>$class])->first(['num_of_periods']);

        Session::put('num_of_periods',$obj_periods->num_of_periods);
        
        $data .= '<thead><tr><th>'.translation('sr_no').'</th>';
        $data .= '<th>'.translation('student_name').'</th>';
        $data .= '<th>'.translation('id').'</th>';
        $data .= '<th>'.translation('national_id').'</th>';

        if(isset($obj_periods->num_of_periods))
        {
            for($i=0 ; $i<$obj_periods->num_of_periods ; $i++)
            {
                $data .= '<th>'.translation('period').' '.($i+1).'</th>';
            }
        }
        if($fun_type=='')
        {
            $data .= '<th>'.translation('action').'</th>';
        }

        $data .= '</thead><tbody>';

        $no = $total = $val = 0;
        
        if(isset($arr_details) && !empty($arr_details))
        {

          foreach($arr_details as $key => $details)
          {
            if(isset($details['get_user_details']['first_name']) && !empty($details['get_user_details']['first_name']))
            {   
                $data .='<tr>';
                $data .='<td>'.(++$val).'</td>';
                $data .='<td>'.ucfirst($details['get_user_details']['first_name']).' '.ucfirst($details['get_user_details']['last_name']).'</td>';
                $data .='<td>'.(isset($details['get_user_details']['id'])?$details['get_user_details']['id']:0).'</td>';
                $data .='<td>'.(isset($details['get_user_details']['national_id'])?$details['get_user_details']['national_id']:0).'</td>';
                $totalColCount = $obj_periods->num_of_periods;
                      if(isset($obj_periods->num_of_periods))
                      {
                          for($i=0 ; $i<$obj_periods->num_of_periods ; $i++)
                          {
                              $total = $no = 0;
                             
                                $attendance_data = $this->StudentPeriodAttendanceModel;
                                                            if($start_date!='' && $end_date!='')
                                                            {
                                                                $attendance_data = $attendance_data->whereBetween('attendance_date',[$start_date,$end_date]);
                                                            }
                                                            elseif($start_date!='')
                                                            {
                                                                $attendance_data = $attendance_data->where('attendance_date','>=',$start_date);
                                                            }
                                                            elseif($end_date!='')
                                                            {
                                                                $attendance_data = $attendance_data->where('attendance_date','<=',$end_date);
                                                            }
                                                            $attendance_data = $attendance_data->where('school_id',$this->school_id)
                                                            ->where('academic_year_id',$this->academic_year)
                                                            ->where('period_no',($i+1))
                                                            ->orderBy('attendance_date','ASC')
                                                            ->get();
                                            
                                if(isset($attendance_data) && !empty($attendance_data))
                                {
                                    $data_attendance = $attendance_data->toArray();
                                }

                                if(isset($data_attendance) && !empty($data_attendance))
                                {
                                  foreach ($data_attendance as $key => $attendance) 
                                  {
                                      $total =count($data_attendance);
                                      if(isset($attendance['attendance']) && !empty($attendance['attendance']))
                                      {
                                        $attendance = json_decode($attendance['attendance'],true);
                                      }
                                    
                                      if( array_key_exists($details['user_id'],$attendance))
                                      {
                                        if($attendance[$details['user_id']] == 'present')
                                        {
                                          $no +=1;
                                        }
                                      }
                                  }
                                } 
                            if($total != 0)
                            {
                                $calculate = ($no/$total)*100;  
                                $data .='<td>'.round($calculate).' %</td>';
                            }
                            else
                            {
                                $data .='<td>-</td>';   
                            }
                          }
                      }
                      if($fun_type=='')
                      {
                      $data .= '<td>';
                        $data .='<a class="green-color" href="'.$this->module_url_path.'/view_details/student/'.base64_encode($details['user_id']).'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a></td>';
                      }
                
                
                $data .='</tr>';
                
                $no = 0;
            }
            
          }
        }
        if($fun_type == '')
        {
            return $data;
        }else{

            $arr_export  =  [];
            $arr_export['totalColCount'] = $totalColCount;
            $arr_export['data'] = $data;
            return $arr_export;
        }
   }

   public function get_timetable(Request $request)
   {

        $data  = '';
        $level              = $request->input('level');
        $class              = $request->input('class');
        $date               = $request->input('date');
        $weekday            = date('l', strtotime($date));
        
        $academic_year_id   = $this->academic_year;
        $professor_id       = $this->user_id;
        
        $current_day    =   strtoupper(substr($weekday,0,3));   
        $arr_professor = $arr_edit_professor_hours =   array();
        $school_start_time = $school_end_time  = ''; 
        
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
        
        /*Get section from class id*/
        $arr_classes    = [];
        $obj_classes    = LevelClassModel::where('school_id',$this->school_id)
                                        ->whereHas('get_class',function($q){
                                                $q->where('is_active','=',1);
                                            })
                                        ->with(['get_class' => function($q){
                                                $q->where('is_active','=',1);
                                            }])
                                        ->where('level_id','=',$level)
                                        ->get();;

        if($obj_classes)
        {
            $arr_classes = $obj_classes->toArray();
        }

        /*Get teachers from teaching hours table whose allocated hours*/
        $where_arr = [
                        'school_id'         => $this->school_id,
                        'academic_year_id'  => $this->academic_year,
                    ];



         $cond_arr = [
                        'school_id'        => $this->school_id,
                        'academic_year_id' => $this->academic_year,
                        /*'level_id'         => $level,*/
                        'level_class_id'   => $class,
                        'professor_id'     => $this->user_id
                    ];



        $SCHOOL_ID      = $this->school_id;           

        $obj_time_table = $this->SchoolTimeTableModel->where($cond_arr)
                                                    ->whereHas('professor_details',function($q1)use($SCHOOL_ID){
                                                        $q1->where('is_active','=', 1);
                                                        $q1->where('has_left','=', 0);
                                                        $q1->where('school_id','=',$SCHOOL_ID);
                                                    })
                                                    ->with(['user_details' => function ($query)
                                                    {
                                                          $query->select('id','profile_image');
                                                    }])
                                                    ->with(['professor_subjects','level_details','class_details'])
                                                    ->get();


        
        $arr_time_table = [];
        if($obj_time_table)
        {
            $arr_time_table = $obj_time_table->toArray();
        }
        
        $session_num_of_periods =$this->SchoolPeriodsModel->where(['level_class_id'=>$class,'school_id'=>$this->school_id,'academic_year_id'=>$this->academic_year])->first();

        $arr_holiday =  isset($session_num_of_periods->weekly_off)&&$session_num_of_periods->weekly_off!=''?json_decode($session_num_of_periods->weekly_off):config('app.project.default_weekly_off');
          
        $data .= '<div class="table-responsive" style="border:1!important;">';
        $data .= '<h4>'.translation('timetable').'</h4>';
        $data .= '<table class="table table-advance"  id="table_module" style="border:1!important;">';
        $data .='<thead><tr><th>&nbsp;</th>';

        if(count($this->weekly_days) >0)
        {
            foreach($this->weekly_days as $day => $day_full_name)
            {
                $data .= '<th ';
                $data .= '>'.translation(strtolower($day)).'</th>';
            }
        }
        $period = [];
        $data .= '</tr></thead><tbody>';
        if(isset($session_num_of_periods) && $session_num_of_periods!="")
        {
            for($i=1; $i<=$session_num_of_periods->num_of_periods; $i++)
            {
                $data .= '<tr> <td>'.translation('period').' '.$i.'</td>';
                    if(count($this->weekly_days) >0)
                    {
                        foreach($this->weekly_days as $day => $day_full_name)
                        {         
                           /*if(isset($arr_holiday) && in_array($day,$arr_holiday))
                           {dump($day);*/ 
                                
                                if(isset($arr_holiday) && in_array($day,$arr_holiday))
                                {
                                    if($i==1)
                                    {
                                        $data .='<td rowspan="'.$session_num_of_periods->num_of_periods.'"  style="color:#000000; font-size: 14px;text-align: center;width: 100px;" class="sunday-holiday-section">'.translation('holiday').'</td>';
                                    }
                                }
                                else
                                {
                                    $data .= '<td ';
                                    if($day==ucfirst(strtolower($current_day))){
                                        $data .=' style="background-color:#c8e9f3" ';                                           
                                    }

                                    $data .='class="droppable_td"';   
                                        if(isset($arr_time_table) && sizeof($arr_time_table)>0)
                                        { 
                                            foreach($arr_time_table as $key => $timetable)
                                            {
                                                
                                                if(isset($timetable['day']) && $timetable['day']==strtoupper($day) && isset($timetable['periods_no']) && $timetable['periods_no']==$i)
                                                {
                                                    array_push($period,$i);
                                                    if($current_day == $timetable['day'])
                                                    {
                                                        $data .='id ="td_'.$timetable['day'].'_'.$i.'" onClick="createAttendance('.$i.','.$timetable['course_id'].',\''.$timetable['day'].'\');" title="'.translation('click_here').'" >';
                                                    }
                                                    else
                                                    {
                                                        $data .='id ="td_'.$timetable['day'].'_'.$i.'" title="'.translation('access_denied').'">';
                                                    }  
                                                     $data .= '<div class="seperate_subjects">';
                                                    
                                                    if(isset($timetable['professor_subjects']['course_name']) && $timetable['professor_subjects']['course_name']!="")
                                                    {
                                                        $subject_name = $timetable['professor_subjects']['course_name'];
                                                    }
                                                    else
                                                    {
                                                        $subject_name = "NA";
                                                    }
                                                              
                                                    $data .= $subject_name;
                                                    $data .= '<br/>';
                                                    $data .= $timetable['period_start_time'] or '';
                                                    $data .= ' - ';
                                                    $data .= $timetable['period_end_time'] or '';
                                                    $data .= '</div>';
                                                    
                                                }

                                            }
                                        }
                                    $data .= '</td>';
                                }
                                                  
                           /* }*/
                        }
                        $data .= '</tr>';
                                       
                    }
            }
                   
        }
        $data .= '</tbody></table></div>';
         

        $attendance =$this->get_attendance($class,$date);
       
       return response()->json(array('data'=>$data,'attendance'=>$attendance));
   }

   public function get_data(Request $request)
   {

        $level     =    $request->input('level');
        $class     =    $request->input('cls');
        $date      =    $request->input('date');
        $period    =    $request->input('period');

        $attendance =$this->get_attendance($class,$date,$period);
        
        return $attendance;
   }

   public function built_mail_data($arr_data,$status)
     {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'STUDENT_NAME'     => $arr_data['student_name'],
                                  'LEVEL'            => $arr_data['level'],
                                  'CLASS'            => $arr_data['class'],
                                  'COURSE_NAME'      => $arr_data['course'],
                                  'STATUS'           => $arr_data['status'],
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id)];

            $arr_mail_data                        = [];
            if($status=='add')
            {
                $arr_mail_data['email_template_slug'] = 'add_attendance';    
            }
            elseif($status=='update')
            {
                $arr_mail_data['email_template_slug'] = 'edit_attendance';
            }
            
            
            $arr_mail_data['arr_built_content']   = $arr_built_content;
            $arr_mail_data['user']                = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_sms_data($arr_data,$status)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'STUDENT_NAME'     => $arr_data['student_name'],
                                  'LEVEL'            => $arr_data['level'],
                                  'CLASS'            => $arr_data['class'],
                                  'COURSE_NAME'      => $arr_data['course'],
                                  'STATUS'           => $arr_data['status']];
            

            $arr_sms_data                      = [];
            if($status=='add')
            {
                $arr_sms_data['sms_template_slug'] = 'add_attendance';    
            }
            elseif($status=='update')
            {
                $arr_sms_data['sms_template_slug'] = 'edit_attendance';
            }
            
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }

    public function view_details($role,$enc_id)
    {
        $id = base64_decode($enc_id);
        if(!is_numeric($id))
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }

        $start_date  = Session::has('attendance_start_date')?Session::get('attendance_start_date'):'';
        $end_date    = Session::has('attendance_end_date')?Session::get('attendance_end_date'):'';

        $arr_details = $period = $attendance = $dates = [];
        $details = $this->StudentModel->with('get_user_details')->where(['school_id'=>$this->school_id,'academic_year_id'=>$this->academic_year,'user_id'=>$id,'is_active'=>1,'has_left'=>0])->first();
        
        if(isset($details) && $details!=null && count($details)>0)
        {
            $arr_details = $details->toArray();
        }

        $obj_period_time = $this->SchoolPeriodTimingModel
                                ->select('period_start_time','period_end_time','period_no','is_break')
                                ->where('level_class_id',Session::get('level_class'))
                                ->where('academic_year_id',$this->academic_year)
                                ->where('school_id',$this->school_id)
                                ->get();


        if(isset($obj_period_time) && $obj_period_time!=null && count($obj_period_time)>0)
        {
            $period_time    =   $obj_period_time->toArray();
        }
        
        $this->arr_view_data['arr_details']     = $arr_details;
        $this->arr_view_data['period_time']     = $period_time;

        $page_title        = translation("view")." ".translation('student')." ".$this->module_title;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['role']            = 'student';
        $this->arr_view_data['start_date']      = $start_date;
        $this->arr_view_data['end_date']        = $end_date;
        $this->arr_view_data['id']              = $id;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.view_details_student', $this->arr_view_data);
    }

   public function build_table(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;
        $id         = $request->id;

        $data = '';
        $period = $dates = $attendance = $period_time = [];
        for ($i=1; $i <= Session::get('num_of_periods'); $i++) { 
            array_push($period, $i);
        }
        
        $obj_period_time = $this->SchoolPeriodTimingModel
                                ->select('period_start_time','period_end_time','period_no','is_break')
                                ->where('level_class_id',Session::get('level_class'))
                                ->where('academic_year_id',$this->academic_year)
                                ->where('school_id',$this->school_id)
                                ->orderBy('period_no','ASC')
                                ->get();


        if(isset($obj_period_time) && $obj_period_time!=null && count($obj_period_time)>0)
        {
            $period_time    =   $obj_period_time->toArray();
        }

        $attendance_data = $this->StudentPeriodAttendanceModel
                            ->select('attendance_date');
                            if($start_date!='' && $end_date!='')
                            {
                                $attendance_data = $attendance_data->whereBetween('attendance_date',[$start_date,$end_date]);
                            }
                            elseif($start_date!='')
                            {
                                $attendance_data = $attendance_data->where('attendance_date','>=',$start_date);
                            }
                            elseif($end_date!='')
                            {
                                $attendance_data = $attendance_data->where('attendance_date','<=',$end_date);
                            }
                            $attendance_data = $attendance_data->where('school_id',$this->school_id)
                            ->where('academic_year_id',$this->academic_year)
                            ->whereIn('period_no',$period)
                            ->where('level_class_id',Session::get('level_class'))
                            ->orderBy('attendance_date','ASC')
                            ->get();
        
        if(isset($attendance_data) && $attendance_data!=null && count($attendance_data)>0)
        {
            $attendance = $attendance_data->toArray();
        }

        foreach ($attendance as $key => $value) {
            if(!in_array($value['attendance_date'],$dates))
            {
                array_push($dates,$value['attendance_date']);
            }
        }

                   
          if(Session::has('num_of_periods'))
          {
                foreach ($dates as $k => $date) 
                {
                    $data .='<tr>';
                    $data .='<td>'.($k+1).'</td>';
                    $data .='<td>'.getDateFormat($date).'</td>';
                    
                    for($i=0 ; $i<Session::get('num_of_periods') ; $i++)
                    {
                        if($period_time[$i]['is_break']==0)
                        {
                            $data_attendance = [];
                            $attendance_data = $this->StudentPeriodAttendanceModel
                                                    ->where('attendance_date',$date)
                                                    ->where('school_id',$this->school_id)
                                                    ->where('academic_year_id',$this->academic_year)
                                                    ->where('period_no',($i+1))
                                                    ->where('level_class_id',$request->level_class)
                                                    ->orderBy('attendance_date','ASC')
                                                    ->first();
                                       
                            if(isset($attendance_data) && !empty($attendance_data))
                            {
                                $data_attendance = $attendance_data->toArray();
                            }
                            if(isset($data_attendance) && !empty($data_attendance))
                            {
                                  if(isset($data_attendance['attendance']) && !empty($data_attendance['attendance']))
                                  {
                                    $attendance = json_decode($data_attendance['attendance'],true);
                                    
                                  }
                                
                                  if(array_key_exists($id,$attendance))
                                  {
                                    $data.= '<td><div ';
                                    if($attendance[$id] == 'present')
                                    {
                                        $data .= 'class="alert alert-success">';
                                    } 

                                    if($attendance[$id] == 'absent')
                                    {
                                        $data .= 'class="alert alert-danger">';
                                    }

                                    if($attendance[$id] == 'late')
                                    {
                                        $data .= 'class="alert alert-warning">';
                                    }    
                                    $data.= translation($attendance[$id]).'</div></td>';
                                  }
                            } 
                            else
                            {
                                $data .='<td></td>';
                            }
                        }
                    }
                    $data .='</tr>';
                }
          }
          return $data;
    }



    /*
    | export() : Export List
    | Auther  : Padmashri 
    | Date    : 15-12-2018
    */
     public function export(Request $request)
    {


       $obj_data =  $class_name = $level_name= $sheetTitlePDF = $sheetTitle = '';
        $getLevelClassDetails = array();
        if(\Session::has('level_class') && \Session::get('level_class')!='')
        {

            $getLevelClassDetails = $this->CommonDataService->get_level_class(Session::get('level_class'));
            $class_name = isset($getLevelClassDetails['class_details']['class_name'])&&$getLevelClassDetails['class_details']['class_name']!=''?$getLevelClassDetails['class_details']['class_name']:'';

            $level_name = isset($getLevelClassDetails['level_details']['level_name'])&&$getLevelClassDetails['level_details']['level_name']!=''?$getLevelClassDetails['level_details']['level_name']:'';
        } 

        if(isset($getLevelClassDetails) && !empty($getLevelClassDetails))
        {
            $sheetTitle =  $this->module_title.'-'.date('d-m-Y').'-'.uniqid(). ' ( '.$level_name." ".$class_name.")";  
            $sheetTitlePDF = $this->module_title.'-'.date('d-m-Y'). ' ( '.$level_name." ".$class_name.")"; 
        }
        else
        {
            $sheetTitle =  $this->module_title.'-'.date('d-m-Y').'-'.uniqid();  
        }

        
        $file_type    = config('app.project.export_file_formate');
        $level_id     = $request->input('level');
        $class_id     = $request->input('class');
        if($level_id!='' && $level_id>0 && $class_id!='' && $class_id>0)
        {
            $obj_data     = $this->get_students_records($request,'export');
        }

        $student_name = 
        $start_date     = $request->input('start_date');
        $end_date       = $request->input('end_date');
        if(($start_date=='' || $start_date=='0000-00-00') || ($end_date=='' || $end_date=='0000-00-00'))
        {
            Flash::error(translation("please_select_date_range"));
            return redirect()->back();
        }

        if( sizeof($obj_data['data'])<=0 || $obj_data['data']==''){
            Flash::error(translation("no_records_found_to_export"));
            return redirect()->back();
        }
        if(sizeof($obj_data)>500 && $request->file_format == $file_type ){
            Flash::error(translation("too_many_records_to_export"));
            return redirect()->back();
        }
        if($request->file_format == $file_type)
        {   

            $this->arr_view_data['sheet_head'] = $sheetTitlePDF;  
            $this->arr_view_data['arr_data'] = $obj_data['data'];
            $totoalColumnCount = $obj_data['totalColCount']+3;
            \Excel::create($sheetTitle, function($excel) use($totoalColumnCount,$sheetTitlePDF){

                $excel->sheet($sheetTitlePDF,function($sheet)use($totoalColumnCount) {
                    $j = 'A'; $k = '4';

                    for($i=0; $i<=$totoalColumnCount;$i++){
                        $sheet->cell($j++.$k, function($cells) {
                            $cells->setBackground('#495b79');
                            $cells->setFontWeight('bold');
                            $cells->setAlignment('center');
                            $cells->setFontColor('#ffffff');
                        });
                    }
                    $sheet->loadView($this->module_view_folder.'.exportSheet', $this->arr_view_data);
                });

            })->export($file_type);
        }
      
        if($request->file_format == 'pdf')
        {
            $this->arr_view_data['arr_data'] = $obj_data['data'];
            $this->arr_view_data['totalColCount'] = $obj_data['totalColCount'];
            $this->arr_view_data['sheetTitle'] = $sheetTitlePDF;
            
            $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
            return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
        }
    }
}