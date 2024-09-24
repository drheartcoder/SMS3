<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;

use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\EmployeeModel;
use App\Models\UserRoleModel;   
use App\Models\RoleModel;
use App\Models\AcademicYearModel;  
use App\Common\Services\LanguageService;
use App\Common\Services\EmailService;
use App\Models\ProfessorModel;
use App\Models\EmployeeAttendanceModel;
use App\Common\Services\CommonDataService;
use App\Models\SchoolCourseModel;
use App\Models\SchoolPeriodsModel;
use App\Models\LevelClassModel;
use App\Models\StudentModel;
use App\Models\StudentPeriodAttendanceModel;
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
                                    EmployeeModel $employee,
                                    EmailService $mail_service,
                                    LanguageService $language,
                                    AcademicYearModel $year,
                                    ProfessorModel $professor,
                                    EmployeeAttendanceModel $attendance,
                                    CommonDataService $common,
                                    SchoolCourseModel $schoolCourse,
                                    SchoolPeriodsModel $periods,
                                    LevelClassModel $levelClass,
                                    StudentModel $student,
                                    StudentPeriodAttendanceModel $stud_attendance,
                                    SchoolPeriodTimingModel $SchoolPeriodTimingModel

                                )
    {
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->AcademicYearModel            = $year;
        $this->EmployeeModel                = $employee;
        $this->EmailService                 = $mail_service;
        $this->LanguageService              = $language;
        $this->ProfessorModel               = $professor;
        $this->EmployeeAttendanceModel      = $attendance;
        $this->CommonDataService            = $common;
        $this->SchoolCourseModel            = $schoolCourse;
        $this->SchoolPeriodsModel           = $periods;
        $this->LevelClassModel              = $levelClass;
        $this->StudentModel                 = $student;
        $this->StudentPeriodAttendanceModel = $stud_attendance;
        $this->SchoolPeriodTimingModel      = $SchoolPeriodTimingModel;
        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/attendance';
        
        $this->module_title                 = translation("attendance");
        $this->modyle_url_slug              = translation("attendance");

        $this->module_view_folder           = "schooladmin.attendance";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-user';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->first_name = $this->last_name ='';

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
            $this->user_id           = $obj_data->user_id;
         }
         
        /* Activity Section */

    }   

    public function index($role)
    { 

        $arr_academic_year = '';
        $arr_details = $data_attendance = [];
        $start_date = date('Y-m-d', strtotime("-1 month"));
        $end_date   = date('Y-m-d');
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }

        $attendance_data = $this->EmployeeAttendanceModel
                                /*->whereBetween('date',[$start_date,$end_date])*/
                                ->where('school_id',$this->school_id)
                                ->where('academic_year_id',$this->academic_year)
                                ->where('user_role',$role)
                                ->orderBy('date','ASC')
                                ->get();

        if(isset($attendance_data) && !empty($attendance_data))
        {
            $data_attendance = $attendance_data->toArray();
        }

        if($role == config('app.project.role_slug.employee_role_slug'))
        {
            $details = $this->EmployeeModel
                                         ->with(['get_user_details'=>function($q)
                                         {
                                            $q->select('id','national_id');
                                         }])
                                         ->where('school_id',$this->school_id)
                                         ->whereIn('academic_year_id',$arr_academic_year)
                                         ->where('is_active',1)
                                         ->where('has_left',0)
                                         ->orderBy('created_at','ASC')
                                         ->get();

            if(isset($details) && !empty($details))
            {
                $arr_details = $details->toArray();
            }
        }

        if($role == config('app.project.role_slug.professor_role_slug'))
        {
            $details = $this->ProfessorModel
                                     ->with(['get_user_details'=>function($q)
                                     {
                                        $q->select('id','national_id');
                                     }])
                                     ->where('school_id',$this->school_id)
                                     ->whereIn('academic_year_id',$arr_academic_year)
                                     ->where('is_active',1)
                                     ->where('has_left',0)
                                     ->orderBy('created_at','ASC')
                                     ->get();

            if(isset($details) && !empty($details))
            {
                $arr_details = $details->toArray();
            }

        }
        if(isset($data_attendance) && !empty($data_attendance))
        {
            $this->arr_view_data['data_attendance']            = $data_attendance;
        }

        if(isset($arr_details) && !empty($arr_details))
        {
            $this->arr_view_data['arr_details']            = $arr_details;
        }

        $page_title = translation("manage")." ".str_plural(translation($role))." ".str_plural($this->module_title);
        $this->arr_view_data['role']            = $role;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['start_date']      = $start_date;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function student_index()
    {
        $arr_academic_year = [];
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        } 
        $details = $this->StudentModel
                                         ->with(['get_user_details'=>function($q)
                                         {
                                            $q->select('id','national_id');
                                         }])
                                         ->where('school_id',$this->school_id)
                                         ->whereIn('academic_year_id',$arr_academic_year)
                                         ->where('is_active',1)
                                         ->where('has_left',0)
                                         ->orderBy('created_at','ASC')
                                         ->get();

        if(isset($details) && !empty($details))
        {
            $arr_details = $details->toArray();
        }

        $level = $this->CommonDataService->get_levels($this->academic_year);
        if(isset($level) && $level != null)
        {
            $this->arr_view_data['levels']      = $level->toArray();    
        }
        $page_title = translation("manage")." ".str_plural(translation('student'))." ".str_plural($this->module_title);
        $this->arr_view_data['role']            = 'student';
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.student_index', $this->arr_view_data);
    }

    public function create($role)
    {        
        $date = date('Y-m-d');
        
        $attendance_data = $this->EmployeeAttendanceModel
                                ->where('date',$date)
                                ->where('school_id',$this->school_id)
                                ->where('user_role',$role)
                                ->first();

        if(isset($attendance_data) && $attendance_data != null)
        {
            $this->arr_view_data['attendance'] = $attendance_data->toArray();
            $this->arr_view_data['enc_id']     = base64_encode($attendance_data->id);
        }
        /*else
        {
            
        }*/
        $arr_academic_year = '';
     
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }
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
                                 ->whereIn('academic_year_id',$arr_academic_year)
                                 ->where('is_active',1)
                                 ->where('has_left',0)
                                 ->where('created_at','<=',$date)
                                 ->orderBy('created_at','ASC')
                                 ->get();


            }
            elseif ($role == 'employee') {
                $obj_data     = $this->EmployeeModel
                                     ->with(['get_user_details'=>function($q)
                                     {
                                        $q->select('id','national_id');
                                     }])
                                     ->where('school_id',$this->school_id)
                                     ->whereIn('academic_year_id',$arr_academic_year)
                                     ->where('is_active',1)
                                     ->where('has_left',0)
                                     ->where('created_at','<=',$date)
                                     ->orderBy('created_at','ASC')
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
        else
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }
        $view_page_title   = translation("view")." ".translation($role)." ".$this->module_title;

        $this->arr_view_data['page_title']      = translation($role)." ".$this->module_title;
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
        
        $arr_academic_year = '';
     
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }

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
                                     ->whereIn('academic_year_id',$arr_academic_year)
                                     ->where('is_active',1)
                                     ->where('has_left',0)
                                     ->orderBy('created_at','ASC')
                                     ->get();
            }
            elseif ($role == 'employee') {
                $obj_data     = $this->EmployeeModel
                                     ->with(['get_user_details'=>function($q)
                                     {
                                        $q->select('id','national_id');
                                     }])
                                     ->where('school_id',$this->school_id)
                                     ->whereIn('academic_year_id',$arr_academic_year)
                                     ->where('is_active',1)
                                     ->where('has_left',0)
                                     ->orderBy('created_at','ASC') 
                                     ->get();
            }
        }

        $arr_data = [];
        if(isset($obj_data) && $obj_data != null)
        {
            $arr_data = $obj_data->toArray();
        }
        dd($arr_data);
        if(isset($arr_data) && !empty($arr_data))
        {
            $this->arr_view_data['arr_data']       = $arr_data;    
        }
        else
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }

        $this->arr_view_data['attendance']      = $attendance_data->toArray();
        $view_page_title   = translation("view")." ".translation($role)." ".$this->module_title;
        $page_title        = translation("edit")." ".translation($role)." ".$this->module_title;
        $this->arr_view_data['view_page_title'] = $view_page_title;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['role']            = $role;
        $this->arr_view_data['enc_id']          = base64_encode($attendance_data->id);
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['create_icon']     = $this->create_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.edit', $this->arr_view_data);      
    }

    
    public function get_attendance_data(Request $request,$role)
    {
        $data = $flag = '';
        $date     = $request->input('start_date');
        $record = $attendance = [];
        $formatted_date = date("Y-m-d",strtotime($date));

        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }
        $staff_data = [];
        $attendance_obj = $this->EmployeeAttendanceModel
                                ->where('date',$formatted_date)
                                ->where('school_id',$this->school_id)
                                ->where('user_role',$role)
                                ->first();
        
        if(isset($attendance_obj) && $attendance_obj != null)
        {   
            $attendance      =  $attendance_obj->toArray();
            $attendance_data =  json_decode($attendance['attendance'],true);
        }

        if($role == 'employee')
        {
            $obj_data =      $this->EmployeeModel
                                  ->with('get_user_details')
                                  ->where('school_id',$this->school_id)
                                  ->whereIn('academic_year_id',$arr_academic_year)
                                  ->where('is_active',1)
                                  ->where('has_left',0)
                                  ->get();

            if($obj_data)
            {
                $staff_data = $obj_data->toArray();
            }         
        }
        elseif($role == 'professor')
        {
            $obj_data =      $this->ProfessorModel->with('get_user_details')
                                  ->where('school_id',$this->school_id)
                                  ->whereIn('academic_year_id',$arr_academic_year)
                                  ->where('is_active',1)
                                  ->where('has_left',0)
                                  ->get();
            if($obj_data)
            {
                $staff_data = $obj_data->toArray();
            }
        }
        
        $no = 0;
        if(isset($staff_data) && !empty($staff_data))
        {
            foreach($staff_data as $key => $staff_data)
            {
                if(isset($staff_data['get_user_details']['first_name']) && !empty($staff_data['get_user_details']['first_name']))
                {
                    $data .= '<tr>';
                    $data .= '<td>'.++$no.'</td>';
                    $data .= '<td>';
                    $first_name  = isset($staff_data['get_user_details']['first_name'])?$staff_data['get_user_details']['first_name']:'';
                    $last_name  = isset($staff_data['get_user_details']['last_name'])?$staff_data['get_user_details']['last_name']:'';
                    $data .= ucwords($first_name.' '.$last_name);
                    $data .= '</td>';

                    $data .= '<td>';
                    if(isset($staff_data['get_user_details']['national_id']) && !empty($staff_data['get_user_details']['national_id']))
                    {
                        $data .= $staff_data['get_user_details']['national_id'] or '-';
                    }
                    $data .= '</td>';
                    if(isset($attendance_data) && count($attendance_data)>0)
                    {
                        $flag = 'update';
                        $data .= '<td>';
                            $data .= '<div class="radio-btns">';  
                                $data .= '<div class="radio-btn">';
                                    $data .= '<input type="radio" id="f-option'.$key.'" name="arr_attendance['.$staff_data['user_id'].']" value="present" ';
                                    if(array_key_exists($staff_data['user_id'],$attendance_data)) 
                                    {
                                        if($attendance_data[$staff_data['user_id']] == 'present')
                                        {
                                            $data .= 'checked';
                                        }
                                        
                                    }
                                    $data .= '><label for="f-option'.$key.'">'.translation('present').'</label><div class="check"></div>';
                                $data .='</div>';
                            $data .='</div> ';
                        $data .='</td>';

                        $data .= '<td>';
                            $data .= '<div class="radio-btns">';  
                                $data .= '<div class="radio-btn">';
                                    $data .= '<input type="radio" id="s-option'.$key.'" name="arr_attendance['.$staff_data['user_id'].']" value="absent" ';
                                    if(array_key_exists($staff_data['user_id'],$attendance_data)) 
                                    {
                                        if($attendance_data[$staff_data['user_id']] == 'absent')
                                        {
                                            $data .= 'checked';
                                        }
                                        
                                    }
                                    $data .= '><label for="s-option'.$key.'">'.translation('absent').'</label><div class="check"></div>';
                                $data .='</div>';
                            $data .='</div> ';
                        $data .='</td>';

                        $data .= '<td>';
                            $data .= '<div class="radio-btns">';  
                                $data .= '<div class="radio-btn">';
                                    $data .= '<input type="radio" id="t-option'.$key.'" name="arr_attendance['.$staff_data['user_id'].']" value="late" ';
                                    if(array_key_exists($staff_data['user_id'],$attendance_data)) 
                                    {
                                        if($attendance_data[$staff_data['user_id']] == 'late')
                                        {
                                            $data .= 'checked';
                                        }
                                        
                                    }
                                    $data .= '><label for="t-option'.$key.'">'.translation('late').'</label><div class="check"></div>';
                                $data .='</div>';
                            $data .='</div> ';
                        $data .='</td>';
                        $record['enc_id'] =  base64_encode($attendance['id']);
                    }
                    else
                    {
                        $flag = 'create';
                        $data .= '<td>';
                            $data .= '<div class="radio-btns">';  
                                $data .= '<div class="radio-btn">';
                                    $data .= '<input type="radio" id="f-option'.$key.'" name="arr_attendance['.$staff_data['user_id'].']" value="present" checked><label for="f-option'.$key.'">'.translation('present').'</label><div class="check"></div>';
                                $data .='</div>';
                            $data .='</div> ';
                        $data .='</td>';

                        $data .= '<td>';
                            $data .= '<div class="radio-btns">';  
                                $data .= '<div class="radio-btn">';
                                    $data .= '<input type="radio" id="s-option'.$key.'" name="arr_attendance['.$staff_data['user_id'].']" value="absent"><label for="s-option'.$key.'">'.translation('absent').'</label><div class="check"></div>';
                                $data .='</div>';
                            $data .='</div> ';
                        $data .='</td>';

                        $data .= '<td>';
                            $data .= '<div class="radio-btns">';  
                                $data .= '<div class="radio-btn">';
                                    $data .= '<input type="radio" id="t-option'.$key.'" name="arr_attendance['.$staff_data['user_id'].']" value="late"><label for="t-option'.$key.'">'.translation('late').'</label><div class="check"></div>';
                                $data .='</div>';
                            $data .='</div> ';
                        $data .='</td>';
                    }
                   $data .= '</tr>';
                }
            }
            $record['flag'] =  $flag;
            $record['data'] =  $data;
              
              return $record;
        }
        else
        {
            $data .= '<tr><td><div class="alert alert-danger" style="text-align:center">'.translation('no_data_available').'</div></td></tr>';
            return  $record['data'] =  $data;
        }
    }

    function store(Request $request)
    {

        $arr_rules['arr_attendance'] = 'required';
        $messages['required']        = translation('this_field_is_required'); 

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

        $arr_data = array(
                            'school_id'         => $this->school_id,
                            'attendance'        => $attendance_arr,
                            'date'              => $request->input('attendance_date'),
                            'academic_year_id'  => $this->academic_year,
                            'user_role'         => $request->input('role')
                         );

        $res = $this->EmployeeAttendanceModel->create($arr_data);   

        if($res)
        {
            Flash::success($this->module_title." ".translation("created_successfully"));
        }
        else
        {
            Flash::error(translation("something_went_wrong_while_creating")." ".$this->module_title);
        }
        return redirect()->back();
    }

    function update(Request $request,$enc_id)
    {
        $id = base64_decode($enc_id);
        
        if(!is_numeric($id))
        {
            Flash::error(translation("something_went_wrong"));
            return redirect()->back();
        }
        
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
        
        $arr_data['attendance']  = $attendance_arr;

        $res = $this->EmployeeAttendanceModel->where('id',$id)->update($arr_data);

        if($res)
        {
            Flash::success($this->module_title." ".translation("updated_successfully"));
        }
        else
        {
            Flash::error(translation("something_went_wrong_while_updating")." ".$this->module_title);
        }
        return redirect()->back();
    }

    public function view($role)
   {
        $arr_course = $arr_academic_year = [];

        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }  
        $level = $this->CommonDataService->get_levels($this->academic_year);
        if(isset($level) && $level != null)
        {
            $this->arr_view_data['levels']      = $level->toArray();    
        }
        else
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }
        $obj_course =  $this->SchoolCourseModel
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
        else
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }
        $view_page_title   = translation("view")." ".translation($role)." ".$this->module_title;
        $this->arr_view_data['role']            = $role;
        $this->arr_view_data['view_page_title'] = $view_page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.view', $this->arr_view_data);
   }

   public function get_data(Request $request)
   {
    $data = '';
    $student = $arr_attendance = $periods = $stud_attendance = [];
    $level_id = $request->input('level');
    $class_id = $request->input('cls');
    $period   = $request->input('period');
    $date     = $request->input('date');
    $level_class = $this->LevelClassModel->where(['level_id'=>$level_id,'class_id'=>$class_id])->first();
    if(isset($level_class) && $level_class != null)
        {
            $student_data = $this->StudentModel->with('get_user_details')->where(['level_class_id'=>$level_class->id,'school_id'=>$this->school_id])->get();

            if(isset($student_data) && count($student_data)>0)
            {
                $student = $student_data->toArray();
                $attendance_data = $this->StudentPeriodAttendanceModel
                                        ->where('school_id',$this->school_id)
                                        ->where('academic_year_id',$this->academic_year)
                                        ->where('attendance_date',$date)
                                        ->where('level_class_id',$level_class->id);
                                        if($period!='')
                                        {
                                            $attendance_data = $attendance_data->where('period_no',$period);
                                        }
                                        $attendance_data = $attendance_data->orderBy('period_no','ASC')
                                        ->get();

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
                                        $data .= ucfirst($stud_attendance[$stud['user_id']]).'</div></td>';
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
           
        }
   }

   public function get_periods(Request $request)
   {
        $level_id = $request->input('level');
        $class_id = $request->input('class');
        $options  = '';
        $obj_periods = $this->SchoolPeriodsModel->where(['level_id'=>$level_id,'class_id'=>$class_id,'school_id'=>$this->school_id,'academic_year_id'=>$this->academic_year])->first();
        
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
         if(isset($arr_data) && !empty($arr_data))
         {
            $this->arr_view_data['arr_data']        = $arr_data;
         }
         else
         {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
         }
        $page_title        = translation("view")." ".translation('professor')." ".$this->module_title;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['role']            = 'professor';
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.view_professor', $this->arr_view_data);
   }
    
   public function getClasses(Request $request)
   {
        $level_id = $request->input('level');

        $options ='';

        $obj_class = $this->CommonDataService->get_class($level_id);
    
        if(!empty($obj_class))
        {
            $arr_class  = $obj_class -> toArray();
            
            if(count($arr_class)>0)
            {
                $options .= '<option value="">'.translation('select_class').'</option>';    
                foreach($arr_class as $value)
                {
                    $options .= '<option value='.$value['class_id'];

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

     public function view_staff($role)
   {
        $arr_academic_year = $data_attendance = $staff_data = [];
        $date = date('Y-m-d');
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }

        if($role == 'employee')
        {
            $obj_data =      $this->EmployeeModel
                                  ->with('get_user_details')
                                  ->where('school_id',$this->school_id)
                                  ->whereIn('academic_year_id',$arr_academic_year)
                                  ->where('is_active',1)
                                  ->where('has_left',0)
                                  ->get();
            if($obj_data)
            {
                $staff_data = $obj_data->toArray();
            }
            
        }
        elseif($role == 'professor')
        {
            $obj_data =      $this->ProfessorModel->with('get_user_details')
                                  ->where('school_id',$this->school_id)
                                  ->whereIn('academic_year_id',$arr_academic_year)
                                  ->where('is_active',1)
                                  ->where('has_left',0)
                                  ->get();
            if($obj_data)
            {
                $staff_data = $obj_data->toArray();
            }
        }
        if(isset($obj_data))
        {
            $attendance_data =$this->EmployeeAttendanceModel->where(['date'=>$date,'school_id'=>$this->school_id,'user_role'=>$role])->first();
        }

        if($attendance_data)
        {
            $data_attendance = $attendance_data->toArray();
        }

        if(isset($data_attendance) && !empty($data_attendance))
        {
            $this->arr_view_data['data_attendance']     = $data_attendance;    
        }

        if(isset($staff_data) && !empty($staff_data))
        {
            $this->arr_view_data['staff_data']          = $staff_data;    
        }

        $view_page_title   = translation("view")." ".translation($role)." ".$this->module_title;
        $page_title   = translation("create")." ".translation($role)." ".$this->module_title;
        $this->arr_view_data['role']            = $role;
        $this->arr_view_data['view_page_title'] = $view_page_title;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.view_staff', $this->arr_view_data);
   }

   public function get_staff_data(Request $request,$role)
   {
        $data = '';
        $obj_data = '';
        $staff_attendance = $staff_data = $record = [];
        $date     = $request->input('start_date');
        $formatted_date = date("Y-m-d",strtotime($date));
        
        $arr_academic_year = '';
     
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }

        if($role == 'employee')
        {
            $obj_data =      $this->EmployeeModel
                                  ->with('get_user_details')
                                  ->where('school_id',$this->school_id)
                                  ->whereIn('academic_year_id',$arr_academic_year)
                                  ->where('is_active',1)
                                  ->where('has_left',0)
                                  ->where('created_at','<=',$date)
                                  ->get();

            if($obj_data)
            {
                $staff_data = $obj_data->toArray();
            }
            
        }
        elseif($role == 'professor')
        {
            $obj_data =      $this->ProfessorModel->with('get_user_details')
                                  ->where('school_id',$this->school_id)
                                  ->whereIn('academic_year_id',$arr_academic_year)
                                  ->where('is_active',1)
                                  ->where('has_left',0)
                                  ->where('created_at','<=',$date)
                                  ->get();
            if($obj_data)
            {
                $staff_data = $obj_data->toArray();
            }
        }
            if(isset($obj_data))
            {
                    $attendance_data =$this->EmployeeAttendanceModel->where(['date'=>$formatted_date,'school_id'=>$this->school_id,'user_role'=>$role,'academic_year_id'=>$this->academic_year])->first();
                    
                    if($attendance_data)
                    {
                        $no = 0;
                        if(isset($attendance_data['attendance']) && !empty($attendance_data['attendance']))
                        {
                            $staff_attendance =  json_decode($attendance_data['attendance'],true);
                        }

                        foreach ($staff_data as $key => $staff) 
                        {
                            if(isset($staff['get_user_details']['first_name']) && !empty($staff['get_user_details']['first_name']))
                            {
                               $data .='<tr><td>'.(++$no).'</td>';
                               $data .='<td>'.$staff['get_user_details']['first_name'].' '.$staff['get_user_details']['last_name'].'</td>';
                               $data .= '<td>'.$staff['get_user_details']['national_id'].'</td>';
                               $data .='<td><div  style="width: 150px;text-align: center"';
                                        if(array_key_exists($staff['user_id'],$staff_attendance))
                                        {
                                            if($staff_attendance[$staff['user_id']] == 'present')
                                            {
                                                $data .= 'class="alert alert-success">';
                                            } 

                                            if($staff_attendance[$staff['user_id']] == 'absent')
                                            {
                                                $data .= 'class="alert alert-danger">';
                                            }

                                            if($staff_attendance[$staff['user_id']] == 'late')
                                            {
                                                $data .= 'class="alert alert-warning">';
                                            }                                    
                                            $data .= ucfirst($staff_attendance[$staff['user_id']]);
                                        }
                                        else
                                        {
                                            $data .='class="alert alert-info">'.translation('not_available');
                                        }
                               $data .='</div></td>';
                               $data .= '</tr>';
                            }
                        }
                        $record['data'] = $data;
                        $record['flag'] = 'true';
                    }

            }
            return $record;
   }

   public function get_records(Request $request,$role)
   {
        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');
        $data = '';

        if($start_date!='' && $end_date!='')
        {
            if(date($start_date) > date($end_date))
            {
                $data .='<tr><td colspan="5"><div class="alert alert-danger" style="text-align:center;">'.translation('end_date_must_be_greater_than_start_date').'</div></td></tr>';
            }
        }

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

        $arr_academic_year = '';
        $arr_details = $data_attendance = $attendance_data = [];

        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }

        if($role == config('app.project.role_slug.professor_role_slug') || $role == config('app.project.role_slug.employee_role_slug'))
        {
            $attendance_data = $this->EmployeeAttendanceModel;
                                    if($start_date!='' && $end_date!='')
                                    {
                                        $attendance_data = $attendance_data->whereBetween('date',[$start_date,$end_date]);
                                    }
                                    elseif($start_date!='')
                                    {
                                        $attendance_data = $attendance_data->where('date','>=',$start_date);
                                    }
                                    elseif($end_date!='')
                                    {
                                        $attendance_data = $attendance_data->where('date','<=',$end_date);
                                    }
                                    $attendance_data = $attendance_data->where('school_id',$this->school_id)
                                    ->where('academic_year_id',$this->academic_year)
                                    ->where('user_role',$role)
                                    ->orderBy('date','ASC')
                                    ->get();
        }
        if(isset($attendance_data) && !empty($attendance_data))
        {
            $data_attendance = $attendance_data->toArray();
        }
        

        if($role == config('app.project.role_slug.student_role_slug'))
        {
            $data = $this->get_students_records($arr_academic_year,$request->input('level'),$request->input('class'),$start_date,$end_date);
            return $data;

        }

        if($role == config('app.project.role_slug.employee_role_slug'))
        {
            $details = $this->EmployeeModel
                                         ->with(['get_user_details'=>function($q)
                                         {
                                            $q->select('id','national_id');
                                         }])
                                         ->where('school_id',$this->school_id)
                                         ->whereIn('academic_year_id',$arr_academic_year)
                                         ->where('is_active',1)
                                         ->where('has_left',0)
                                         ->orderBy('created_at','ASC')
                                         ->get();

            if(isset($details) && !empty($details))
            {
                $arr_details = $details->toArray();
                
            }
        }

        if($role == config('app.project.role_slug.professor_role_slug'))
        {
            $details = $this->ProfessorModel
                                     ->with(['get_user_details'=>function($q)
                                     {
                                        $q->select('id','national_id');
                                     }])
                                     ->where('school_id',$this->school_id)
                                     ->whereIn('academic_year_id',$arr_academic_year)
                                     ->where('is_active',1)
                                     ->where('has_left',0)
                                     ->orderBy('created_at','ASC')
                                     ->get();


            if(isset($details) && !empty($details))
            {
                $arr_details = $details->toArray();
            }
        }
        $total = $val = $n = $key1 = 0;

        if(isset($data_attendance) && !empty($data_attendance))
        {
            if(isset($arr_details) && !empty($arr_details))
            {
              foreach($arr_details as $key => $details)
              {
                $no = $no1 = $no2 = $calculate = $calculate1 = $calculate2 = 0;
                if(isset($details['get_user_details']['first_name']) && !empty($details['get_user_details']['first_name']))
                {
                    $data .='<tr>';
                    $data .='<td>';
                    $data .=(++$n);
                    $data .='</td>';
                    $data .='<td>';
                    
                    $first_name = $details['get_user_details']['first_name'];
                    $last_name  = $details['get_user_details']['last_name'];
                    $user_name  = $first_name.' '.$last_name;

                    $data .= ucwords($user_name);
                    $data .='</td>';
                    if(isset($data_attendance) && !empty($data_attendance))
                    {      
                          foreach ($data_attendance as $key => $attendance) 
                          {
                              if(isset($attendance['attendance']) && !empty($attendance['attendance']))
                              {
                                $attendance = json_decode($attendance['attendance'],true);
                              }
                              if(array_key_exists($details['user_id'],$attendance))
                              {
                                    if($attendance[$details['user_id']] == 'present')
                                    {
                                      $no +=1;
                                    }
                                    if($attendance[$details['user_id']] == 'absent')
                                    {
                                      $no1 +=1;
                                    }
                                    if($attendance[$details['user_id']] == 'late')
                                    {
                                      $no2 +=1;
                                    }
                              }
                              $key1++;
                          }
                    }
                    $total = $no+$no1+$no2;
                    if($total != 0)
                    {
                        $calculate  = ($no/$total)*100;
                        $calculate1 = ($no1/$total)*100;
                        $calculate2 = ($no2/$total)*100;
                     }
                        $data .='<td>'.$no.' ('.round($calculate).'%) </td>';
                        $data .='<td>'.$no1.' ('.round($calculate1).'%) </td>';
                        $data .='<td>'.$no2.' ('.round($calculate2).'%) </td>';
                        $data .='<td>'.$total.'</td>';
                    $data .= '<td>';
                    $data .='<a class="green-color" href="'.$this->module_url_path.'/view_details/'.$role.'/'.base64_encode($details['user_id']).'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a></td>';
                    
                    $data .='</tr>';
                    
                   }
                
                }
            }
        }
        else
        {
            $data .='<tr><td colspan="6"><div class="alert alert-danger" style="text-align:center;">'.translation('no_data_available').'</div></td></tr>';
        }

        return $data;
   }

   public function get_students_records($arr_academic_year,$level,$class,$start_date,$end_date)
   {

        $data = '';
        $arr_details = $data_attendance = [];
        $level_class = $this->LevelClassModel->where(['level_id'=>$level,'class_id'=>$class])->first();
        
        if(isset($level_class) && $level_class != null)
        {
            $details = $this->StudentModel->with('get_user_details')->where(['level_class_id'=>$level_class->id,'school_id'=>$this->school_id])->get();
            Session::put('level_class',$level_class->id);
        }

        if(isset($details) && !empty($details))
        {
            $arr_details = $details->toArray();
        }

        $obj_periods = $this->SchoolPeriodsModel->where(['school_id'=>$this->school_id,'academic_year_id'=>$this->academic_year,'level_id'=>$level,'class_id'=>$class])->first(['num_of_periods']);

        Session::put('num_of_periods',isset($obj_periods->num_of_periods)&&$obj_periods->num_of_periods!=''?$obj_periods->num_of_periods:'0');
        
        $data .= '<thead><tr><th>'.translation('sr_no').'</th>';
        $data .= '<th>'.translation('student_name').'</th>';

        if(isset($obj_periods->num_of_periods))
        {
            for($i=0 ; $i<$obj_periods->num_of_periods ; $i++)
            {
                $data .= '<th>'.translation('period').' '.($i+1).'</th>';
            }
            $data .= '<th>'.translation('action').'</th>';
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
                                                            ->where('level_class_id',$level_class->id)
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
                                    else
                                    {
                                        $data .='<td>-</td>';
                                    }
                                if($total != 0)
                                {
                                    $calculate = ($no/$total)*100;  
                                    $data .='<td>'.round($calculate).' %</td>';
                                }
                              }
                          }
                    $data .='<td>';
                    $data .='<a class="green-color" href="'.$this->module_url_path.'/view_details/student/'.base64_encode($details['user_id']).'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a></td>';
                    $data .='</td>';
                    
                    $data .='</tr>';
                    
                    $no = 0;
                }
                
              }
            }
        }
        else
        {
            $data .='<div class="alert alert-danger" style="text-align:center;">'.translation('no_data_available').'</div>';
        }

        return $data;
   }

   public function get_employee_record(Request $request,$role)
   {
        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');
        $user_id    = $request->input('name');
        $data = '';

        if($start_date!='' && $end_date!='')
        {
            if(date($start_date) > date($end_date))
            {
                $data .='<tr><td colspan="5"><div class="alert alert-danger" style="text-align:center;">'.translation('end_date_must_be_greater_than_start_date').'</div></td></tr>';
                return $data;
            }
        }

        if(Session::has('attendance_start_date'))
        {
            Session::forget('attendance_start_date');
        }
        else
        {
            Session::put('attendance_start_date',$start_date);
        }

        if(Session::has('attendance_end_date'))
        {
            Session::forget('attendance_end_date');
        }
        else
        {
            Session::put('attendance_end_date',$end_date);
        }

        $arr_academic_year = '';
        $arr_details = $data_attendance = $attendance_data = [];

        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }
        $attendance_data = $this->EmployeeAttendanceModel;
                                if($start_date!='' && $end_date!='')
                                {
                                    $attendance_data = $attendance_data->whereBetween('date',[$start_date,$end_date]);
                                }
                                elseif($start_date!='')
                                {
                                    $attendance_data = $attendance_data->where('date','>=',$start_date);
                                }
                                elseif($end_date!='')
                                {
                                    $attendance_data = $attendance_data->where('date','<=',$end_date);
                                }
                                $attendance_data = $attendance_data->where('school_id',$this->school_id)
                                ->where('academic_year_id',$this->academic_year)
                                ->where('user_role',$role)
                                ->orderBy('date','ASC')
                                ->get();

        if(isset($attendance_data) && !empty($attendance_data))
        {
            $data_attendance = $attendance_data->toArray();
        }

        if($role == config('app.project.role_slug.employee_role_slug'))
        {
            $details = $this->EmployeeModel
                                         ->with(['get_user_details'=>function($q)
                                         {
                                            $q->select('id','national_id');
                                         }])
                                         ->where('user_id',$user_id)
                                         ->where('school_id',$this->school_id)
                                         ->where('is_active',1)
                                         ->where('has_left',0)
                                         ->first();

            if(isset($details) && !empty($details))
            {
                $arr_details = $details->toArray();
                
            }
        }

        if($role == config('app.project.role_slug.professor_role_slug'))
        {
            $details = $this->ProfessorModel
                                         ->with(['get_user_details'=>function($q)
                                         {
                                            $q->select('id','national_id');
                                         }])
                                         ->where('user_id',$user_id)
                                         ->where('school_id',$this->school_id)
                                         ->where('is_active',1)
                                         ->where('has_left',0)
                                         ->first();

            if(isset($details) && !empty($details))
            {
                $arr_details = $details->toArray();
            }
        }

        $no = $no1= $no2 = $total = $val = $calculate = $calculate1 = $calculate2 = 0;
        
        if(isset($data_attendance) && !empty($data_attendance))
        {
            if(isset($arr_details) && !empty($arr_details))
            {
                if(isset($details['get_user_details']['first_name']) && !empty($details['get_user_details']['first_name']))
                {
                    $data .='<thead><tr><th>';
                    $data .= translation('name');
                    $data .='</th>';
                    $data .='<th>';
                    $data .= translation('present_days');
                    $data .= '</th>';
                    $data .='<th>';
                    $data .= translation('absent_days');
                    $data .= '</th>';
                    $data .='<th>';
                    $data .= translation('late_days');
                    $data .= '</th>';
                    $data .='<th>';
                    $data .= translation('total_days')  ;
                    $data .= '</th>';
                    $data .= '<th>';
                    $data .= translation('action');
                    $data .='</th></tr></thead>';
                    $data .='<tbody><tr>';
                    $data .='<td>';
                    $data .= ucfirst($details['get_user_details']['first_name']).' '.ucfirst($details['get_user_details']['last_name']);
                    $data .='</td>';
                    
                        
                          
                          foreach ($data_attendance as $key => $attendance) 
                          {
                              if(isset($attendance['attendance']) && !empty($attendance['attendance']))
                              {
                                $attendance = json_decode($attendance['attendance'],true);
                              }
                            if(array_key_exists($details['user_id'],$attendance))
                            {
                                if($attendance[$details['user_id']] == 'present')
                                {
                                  $no +=1;
                                }
                                if($attendance[$details['user_id']] == 'absent')
                                {
                                  $no1 +=1;
                                }
                                if($attendance[$details['user_id']] == 'late')
                                {
                                  $no2 +=1;
                                }
                            }
                           $key++;
                          }
                        }
                        $total = $no+$no1+$no2;
                    if($total != 0)
                    {
                        $calculate  = ($no/$total)*100;
                        $calculate1 = ($no1/$total)*100;
                        $calculate2 = ($no2/$total)*100;
                    }
                    $data .='<td>'.$no.' ('.round($calculate).'%) </td>';
                    $data .='<td>'.$no1.' ('.round($calculate1).'%) </td>';
                    $data .='<td>'.$no2.' ('.round($calculate2).'%) </td>';
                    $data .='<td>'.$total.'</td>';
                    $data .= '<td>';
                    $data .='<a class="green-color" href="'.$this->module_url_path.'/view_details/'.$role.'/'.base64_encode($details['user_id']).'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a></td>';
                    $data .='</tr></tbody>';    
                }
            }
        else
        {
             $data .='<thead></thead><tbody><tr><td colspan="5"><div class="alert alert-danger" style="text-align:center;">'.translation('no_data_available').'</div></td></tr></tbody>';
        }
        return $data;
        
   }
   public function download_doc($format){

        if($format=='xls')
        {
            $obj_course = $arr_course = array();
            $obj_course = $this->CourseModel
                               ->where('school_id',$this->school_id)
                               ->get();
            if($obj_course)
            {
                $arr_course = $obj_course->toArray();
            }

            \Excel::create(ucwords($this->module_title).'-DETAILS', function($excel) use($arr_course)
            {
                  $excel->sheet(translation('instroction_sample'), function($sheet) use($arr_course)
                  { 
                       
                        $title = translation('instroction_for_adding_records_for')." ".ucwords($this->module_title);
                        $sheet->setHeight(1, 50);
                        $sheet->setAutoSize(false);
                        $sheet->cell('A1', function($cell) use($title){         // set title at D1 cell i.e. middle of doc. 
                            $cell->setValue($title);
                            $cell->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '16',
                                'bold'       =>  true
                            ));
                            $cell->setFontColor('#660099');
                        });

                        $title = translation('please_add_valid_data_if_you_failed_to_add_valid_data_then_that_data_will_skip_automatically');
                        $sheet->setHeight(2, 50);
                        $sheet->setAutoSize(false);
                        $sheet->cell('A1', function($cell) use($title){         
                            $cell->setValue($title);
                            $cell->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '16',
                                'bold'       =>  true
                            ));
                            $cell->setFontColor('#ff3333');
                        });


                        $sheet->setWidth('A', 20);
                        $sheet->row(4,array(translation("first_name"),"Alice"));
                        $sheet->setHeight(4, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(4, function($row){     //row no 2 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });


                        $sheet->row(5,array(translation('last_name'),"Aguillon"));
                        $sheet->setHeight(5, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(5, function($row){     //row no 5 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });


                        $sheet->row(6,array(translation('email'),"demo@mail.com(".translation("unique_in_all_user_in_this_system_except_parent_section").")"));
                        $sheet->setHeight(6, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(6, function($row){     //row no 6 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });


                        $sheet->row(7,array(translation('address'),"Madame Duval 27 RUE PASTEUR 14390 CABOURG FRANCE"));
                        $sheet->setHeight(7, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(7, function($row){     //row no 7 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });


                        $sheet->row(8,array(translation('city'),"Paris"));
                        $sheet->setHeight(8, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(8, function($row){     //row no 8 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(9,array(translation('country'),"France"));
                         $sheet->setHeight(9, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(9, function($row){     //row no 9 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });



                        $sheet->row(10,array(translation('national_id'),"1503110"));
                         $sheet->setHeight(10, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(10, function($row){     //row no 10 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(11,array(translation('birth_date'),"1990-05-29"));
                        $sheet->setHeight(11, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(11, function($row){     //row no 11 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(12,array(translation('gender'),"Male OR Female"));
                         $sheet->setHeight(12, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(12, function($row){     //row no 12 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(13,array("MARITAL-STATUS"));
                        $sheet->setHeight(13, 30);
                        $sheet->row(13, function($row){     //row no 3 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(14,array('SR.NO',translation('status'),translation('abbreviations')));
                        $sheet->setHeight(14, 30);
                        $sheet->row(14, function($row) {   //row no 5 formatiing for columns names
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $arr_status    = [];
                        $arr_status[0] = [0=>"Married",1=>"married"];
                        $arr_status[1] = [0=>"Single",1=>"single"];
                        $arr_status[2] = [0=>"Engaged",1=>"engaged"];
                        $arr_status[3] = [0=>"Divorced",1=>"divorced"];
                        
                        $i = 15 ; 

                        if(isset($arr_status) && sizeof($arr_status)>0)
                        {
                            foreach($arr_status as $key => $status)
                            {
                                $sheet->setHeight($i, 30);
                                $sheet->row($i, function($row) {
                                $row->setFont(array(
                                        'family'     => 'Calibri',
                                        'size'       => '11'
                                      ));
                                }); 

                                $key = $key + 1;

                                $sheet->appendRow($i, array($key,$status[0],$status[1]));

                                $i++;
                            }
                        }
                        
                        $sheet->row(19,array(translation('year_of_experience'),"5"));
                        $sheet->setHeight(19, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(19, function($row){     //row no 11 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(20,array(translation('mobile_no'),"78968546987"));
                        $sheet->setHeight(20, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(20, function($row){     //row no 11 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });


                        $sheet->row(21,array(translation('telephone_no'),"9888745698"));
                        $sheet->setHeight(21, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(21, function($row){     //row no 11 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(22,array(translation('qualification_degree'),"B.E (E.N.T.C),M.B.A etc "));
                        $sheet->setHeight(22, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(22, function($row){     //row no 11 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });
                     

                         $i= 23;

                     
                        $sheet->row($i,array('SR.NO',translation('subject'),translation('subject_list')));
                        $sheet->setHeight($i, 20);
                        $sheet->row($i, function($row) {   
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '16',
                                'bold'       =>  true
                            ));
                        });

                        $i = $i + 1;/*$i=14*/
                        $sheet->setWidth('B', 20);
                        $sheet->setWidth('C', 20);
                        if(isset($arr_course) && sizeof($arr_course)>0)
                        {
                            foreach($arr_course as $key => $course)
                            {   
                                if(isset($course['course_name']) && sizeof($course['course_name'])>0)
                                {
                                    $sheet->setHeight($i, 20);
                                    $sheet->row($i, function($row) {
                                    $row->setFont(array(
                                            'family'     => 'Calibri',
                                            'size'       => '11',
                                          ));
                                    }); 

                                    $key = $key + 1;
                                    
                                    $sheet->appendRow($i, array($key,$course['course_name'],$course['course_name']));

                                    $i++;
                                }    
                            }
                        }

                        
                        });

                        
                        $excel->sheet(translation('report_sample'), function($sheet)  
                        {
                            $sheet->setWidth(array(
                                'A'     =>  30,
                                'B'     =>  30,
                                'C'     =>  30,
                                'D'     =>  30,
                                'E'     =>  35,
                                'F'     =>  40,
                                'G'     =>  45,
                                'H'     =>  15,
                                'I'     =>  45,
                                'J'     =>  25,
                                'K'     =>  45,
                                'L'     =>  15,
                                'M'     =>  15,
                                'N'     =>  50,
                                'O'     =>  45,
                                'P'     =>  45
                            ));

                            //setting first 100 rows height to 25
                            $arr_height = array();
                            for ($i=1; $i <=100 ; $i++) 
                            { 
                              $arr_height[$i] = 25;
                            }
                            $sheet->setHeight($arr_height);
                            
                            $sheet->row(1, function($row) {
                               $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '10',
                                'bold'       => true
                                ));
                            });

                            $sheet->row(1,array(
                                                    'First Name',
                                                    'Last Name',
                                                    'Email Id',
                                                    'Address',
                                                    'City',
                                                    'Country',
                                                    'National Id',
                                                    'Birth Date',
                                                    'Gender',
                                                    'Status',
                                                    'Year Of Experience',
                                                    'Mobile No',
                                                    'Telephone No',
                                                    'Qualification Degree',
                                                    'Subjects'
                                                    ));
                    });                             

            })->export('xls');
        }
    }
   
    public function export(Request $request)
    {      
            $role = $request->input('role');
            $start_date = $request->input('start_date');
            $end_date   = $request->input('end_date');
            $data = '';

            if($start_date!='' && $end_date!='')
            {
                if(date($start_date) > date($end_date))
                {
                    Flash::error(translation('end_date_must_be_greater_than_start_date'));
                    return redirect()->back();
                }
            }

            $arr_academic_year = '';
            $arr_details = $data_attendance = $attendance_data = [];

            $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

            if($academic_year)
            {
                $arr_academic_year = explode(',',$academic_year);
            }

            if($role == config('app.project.role_slug.professor_role_slug') || $role == config('app.project.role_slug.employee_role_slug'))
            {
                $attendance_data = $this->EmployeeAttendanceModel;
                                        if($start_date!='' && $end_date!='')
                                        {
                                            $attendance_data = $attendance_data->whereBetween('date',[$start_date,$end_date]);
                                        }
                                        elseif($start_date!='')
                                        {
                                            $attendance_data = $attendance_data->where('date','>=',$start_date);
                                        }
                                        elseif($end_date!='')
                                        {
                                            $attendance_data = $attendance_data->where('date','<=',$end_date);
                                        }
                                        $attendance_data = $attendance_data->where('school_id',$this->school_id)
                                        ->where('academic_year_id',$this->academic_year)
                                        ->where('user_role',$role)
                                        ->orderBy('date','ASC')
                                        ->get();
            }
            if(isset($attendance_data) && !empty($attendance_data))
            {
                $data_attendance = $attendance_data->toArray();
            }
            else
            {
                Flash::error(translation('no_data_available'));
                return redirect()->back();
            }
            if($role == config('app.project.role_slug.employee_role_slug'))
            {
                $details = $this->EmployeeModel
                                             ->with(['get_user_details'=>function($q)
                                             {
                                                $q->select('id','national_id');
                                             }])
                                             ->where('school_id',$this->school_id)
                                             ->whereIn('academic_year_id',$arr_academic_year)
                                             ->where('is_active',1)
                                             ->where('has_left',0)
                                             ->orderBy('created_at','ASC')
                                             ->get();

                if(isset($details) && !empty($details))
                {
                    $arr_details = $details->toArray();
                    
                }
                else
                {
                    Flash::error(translation('no_data_available'));
                    return redirect()->back();
                }
            }

            if($role == config('app.project.role_slug.professor_role_slug'))
            {
                $details = $this->ProfessorModel
                                         ->with(['get_user_details'=>function($q)
                                         {
                                            $q->select('id','national_id');
                                         }])
                                         ->where('school_id',$this->school_id)
                                         ->whereIn('academic_year_id',$arr_academic_year)
                                         ->where('is_active',1)
                                         ->where('has_left',0)
                                         ->orderBy('created_at','ASC')
                                         ->get();


                if(isset($details) && !empty($details))
                {
                    $arr_details = $details->toArray();
                }
                else
                {
                    Flash::error(translation('no_data_available'));
                    return redirect()->back();
                }
            }
            if($request->file_format == 'csv'){

                \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel)use($arr_details,$data_attendance,$role,$start_date,$end_date)
                    {
                        $excel->sheet(ucwords($this->module_title), function($sheet)use($arr_details,$data_attendance,$role,$start_date,$end_date)
                        {
                            $arr_fields['id']             = translation('sr_no');
                            $arr_fields['name']           = translation('name');
                            $arr_fields['present_days']   = translation('present_days');
                            $arr_fields['absent_days']    = translation('absent_days');
                            $arr_fields['late_days']      = translation('late_days');
                            $arr_fields['total_days']     = translation('total_days');
                            
                            if($start_date=='' && $end_date=='')
                            {
                                $sheet->row(2, ['','Overall '.ucwords($role).' '.ucwords($this->module_title).' report','','','']);
                            }
                            elseif($start_date!='' && $end_date=='')
                            {
                                $sheet->row(2, ['',ucwords($role).' '.ucwords($this->module_title).' report from '.date("d-m-Y",strtotime($start_date)).' to '.date("d-m-Y"),'','','']);   
                            }
                            elseif($start_date=='' && $end_date!='')
                            {
                                $sheet->row(2, ['',ucwords($role).' '.ucwords($this->module_title).' report from '.date("d-m-Y",strtotime($data_attendance[0]['date'])).' to '.date("d-m-Y",strtotime($end_date)),'','','']);   
                            }
                            else
                            {
                                 $sheet->row(2, ['',ucwords($role).' '.ucwords($this->module_title).' report from '.date("d-m-Y",strtotime($start_date)).' to '.date("d-m-Y",strtotime($end_date)),'','','']); 
                            }
                            $sheet->row(4, $arr_fields);
                           
                            $total = $val = $n = $key1 = 0;

                            if(isset($data_attendance) && !empty($data_attendance))
                            {
                                if(isset($arr_details) && !empty($arr_details))
                                {
                                  foreach($arr_details as $key2 => $details)
                                  {
                                    $no = $no1 = $no2 = $calculate = $calculate1 = $calculate2 = 0;
                                    if(isset($details['get_user_details']['first_name']) && !empty($details['get_user_details']['first_name']))
                                    {
                                        $arr_tmp[$key2]['sr_no']         = (++$n);
                                        
                                        $first_name = $details['get_user_details']['first_name'];
                                        $last_name  = $details['get_user_details']['last_name'];
                                        $user_name  = $first_name.' '.$last_name;

                                        $arr_tmp[$key2]['name']         = ucwords($user_name);

                                        if(isset($data_attendance) && !empty($data_attendance))
                                        {      
                                              foreach ($data_attendance as $key => $attendance) 
                                              {
                                                  if(isset($attendance['attendance']) && !empty($attendance['attendance']))
                                                  {
                                                    $attendance = json_decode($attendance['attendance'],true);
                                                  }
                                                  if(array_key_exists($details['user_id'],$attendance))
                                                  {
                                                        if($attendance[$details['user_id']] == 'present')
                                                        {
                                                          $no +=1;
                                                        }
                                                        if($attendance[$details['user_id']] == 'absent')
                                                        {
                                                          $no1 +=1;
                                                        }
                                                        if($attendance[$details['user_id']] == 'late')
                                                        {
                                                          $no2 +=1;
                                                        }
                                                  }
                                                  $key1++;
                                              }
                                        }
                                        $total = $no+$no1+$no2;
                                        if($total != 0)
                                        {
                                            $calculate  = ($no/$total)*100;
                                            $calculate1 = ($no1/$total)*100;
                                            $calculate2 = ($no2/$total)*100;
                                        }
                                            $arr_tmp[$key2]['present_days']  = $no.' ('.round($calculate).'%)';
                                            $arr_tmp[$key2]['absent_days']   = $no1.' ('.round($calculate1).'%)';
                                            $arr_tmp[$key2]['late_days']     = $no2.' ('.round($calculate2).'%)';
                                            $arr_tmp[$key2]['total_days']    = $total;
                                       }
                                    
                                    }
                                    $sheet->rows($arr_tmp);
                                }
                                 else
                                {
                                    Flash::error(translation('no_students_in_class'));
                                    return redirect()->back();
                                }
                            }
                            else
                            {
                                Flash::error(translation('no_data_available'));
                                return redirect()->back();
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

                $this->arr_view_data['arr_details']   = $details->toArray();
                $this->arr_view_data['data_attendance']= $data_attendance;
                $this->arr_view_data['role']          = $role;
                $this->arr_view_data['start_date']    = $start_date;
                $this->arr_view_data['end_date']      = $end_date;    
                $this->arr_view_data['school_name']   = $school_name;    
                $this->arr_view_data['school_address']= $school_address;
                $this->arr_view_data['school_email']  = $school_email;
                $this->arr_view_data['school_logo']   = $school_logo;

                $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
                return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
            }
         
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

        $attendance_data = $this->EmployeeAttendanceModel;
                                if($start_date!='' && $end_date!='')
                                {
                                    $attendance_data = $attendance_data->whereBetween('date',[$start_date,$end_date]);
                                }
                                elseif($start_date!='')
                                {
                                    $attendance_data = $attendance_data->where('date','>=',$start_date);
                                }
                                elseif($end_date!='')
                                {
                                    $attendance_data = $attendance_data->where('date','<=',$end_date);
                                }
                                $attendance_data = $attendance_data->where('school_id',$this->school_id)
                                ->where('academic_year_id',$this->academic_year)
                                ->where('user_role',$role)
                                ->orderBy('date','ASC')
                                ->get();

        if(isset($attendance_data) && !empty($attendance_data))
        {
            $this->arr_view_data['data_attendance'] = $attendance_data->toArray();
        }

        $arr_details = [];

        if($role == config('app.project.role_slug.employee_role_slug'))
        {
            $details = $this->EmployeeModel
                                         ->with(['get_user_details'=>function($q)
                                         {
                                            $q->select('id','national_id');
                                         }])
                                         ->where('user_id',$id)
                                         ->where('school_id',$this->school_id)
                                         ->where('is_active',1)
                                         ->where('has_left',0)
                                         ->first();

            if(isset($details) && !empty($details))
            {
                $arr_details = $details->toArray();
                
            }
        }

        if($role == config('app.project.role_slug.professor_role_slug'))
        {
            $details = $this->ProfessorModel
                                         ->with(['get_user_details'=>function($q)
                                         {
                                            $q->select('id','national_id');
                                         }])
                                         ->where('user_id',$id)
                                         ->where('school_id',$this->school_id)
                                         ->where('is_active',1)
                                         ->where('has_left',0)
                                         ->first();

            if(isset($details) && !empty($details))
            {
                $arr_details = $details->toArray();
            }
        }
        if($role == config('app.project.role_slug.student_role_slug'))
        {
            $this->view_student_details($start_date,$end_date,$id);
            return view($this->module_view_folder.'.view_details_student', $this->arr_view_data);
        }

        $this->arr_view_data['arr_details']     = $arr_details;

        $page_title        = translation("view")." ".translation($role)." ".$this->module_title;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['role']            = $role;
        $this->arr_view_data['start_date']      = $start_date;
        $this->arr_view_data['end_date']        = $end_date;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.view_details', $this->arr_view_data);

        
    }
    public function view_student_details($start_date,$end_date,$id)
    {

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

        return ;
    }

    public function build_table(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;
        $id         = $request->id;

        $data = '';
        $period = $dates = $period_time = [];
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
}