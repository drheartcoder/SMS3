<?php

namespace App\Http\Controllers\Parent;

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
use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use PDF;
use Datatables;

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
                                    EmployeeAttendanceModel $employee_attendance

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
        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.parent_panel_slug')).'/attendance';
        
        $this->module_title                 = translation("attendance");
        $this->modyle_url_slug              = translation("attendance");

        $this->module_view_folder           = "parent.attendance";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-user';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::has('school_id')?Session::get('school_id'):0;
        $this->academic_year                = Session::has('academic_year')?Session::get('academic_year'):0;
        $this->kid_id                       = Session::has('kid_id')?Session::get('kid_id'):0;
        $this->first_name = $this->last_name ='';

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

    public function index()
    {   
        /*$data = '';
        $data_attendance = [];
        
                                              
        $attendance_data =$this->StudentPeriodAttendanceModel
                                       ->where('school_id',$this->school_id)
                                       ->where('attendance_date',date('Y-m-d'))
                                       ->get();

        $level_class = $this->LevelClassModel
                                    ->select('level_id','class_id')
                                    ->where('id',$student->level_class_id)
                                    ->where('school_id',$this->school_id)
                                    ->first();

        $period     =  $this->SchoolPeriodsModel
                                    ->select('num_of_periods')
                                    ->where('level_id',$level_class->level_id)
                                    ->where('class_id',$level_class->class_id)
                                    ->where('school_id',$this->school_id)
                                    ->first();
                
        if(isset($period) && !empty($period))
        {
            $this->arr_view_data['period']          = $period;   
        }
        if($attendance_data)
        {
            $data_attendance = $attendance_data->toArray();
        }
        if($data_attendance && !empty($data_attendance))
        {
            $this->arr_view_data['data_attendance']          = $data_attendance;
        }
*/
        $student         = $this->StudentModel->where('school_id',$this->school_id)
                                              ->with('get_user_details')
                                              ->where('is_active',1)
                                              ->where('has_left',0)
                                              ->where('user_id',$this->kid_id)
                                              ->first();
        if(!empty($student))
        {
            $page_title = translation('view')." ".translation('kid')." ".$this->module_title. ' ('.ucfirst($student->get_user_details->first_name)." ".ucfirst($student->get_user_details->last_name).")";  
            $this->arr_view_data['student']          = $student->toArray();  
        }
        else
        {
            $page_title = translation('view')." ".translation('kid')." ".$this->module_title;    
        }
        
        $this->arr_view_data['enc_id']          = base64_encode($this->user_id);
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.view_student', $this->arr_view_data);
    }

    

   public function get_student_data(Request $request,$fun_type='')
   {
        $arr_return =  [];
        $student         = $this->StudentModel->where('school_id',$this->school_id)
                                              ->with('get_user_details')
                                              ->where('is_active',1)
                                              ->where('has_left',0)
                                              ->where('user_id',$this->kid_id)
                                              ->where('academic_year_id',$this->academic_year)
                                              ->first();


        
        $data = $flag = '';
        $stud_attendance = $record = [];
        $start_date     = $request->input('start_date');
        $end_date       = $request->input('end_date');
        $totalPeriodCount = 0;
        if(isset($student))
        {
                $level_class = $this->LevelClassModel
                                    ->select('level_id','class_id')
                                    ->where('id',$student->level_class_id)
                                    ->where('school_id',$this->school_id)
                                    ->first();

                $period     =  $this->SchoolPeriodsModel
                                    ->select('num_of_periods')
                                    ->where('level_id',$level_class->level_id)
                                    ->where('class_id',$level_class->class_id)
                                    ->where('school_id',$this->school_id)
                                    ->first();

                
                
                $attendance_data =$this->StudentPeriodAttendanceModel;
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
                                       ->where('level_class_id',$student->level_class_id)
                                       ->orderBy('attendance_date','ASC')
                                       ->get();

                if($attendance_data && sizeof($attendance_data)>0)
                {
                    $data_attendance = $attendance_data->toArray();

                    $arr_dates = [];
                    foreach ($data_attendance as $key => $value) 
                    {
                        if(!(in_array($value['attendance_date'], $arr_dates)))
                        {
                            array_push($arr_dates,$value['attendance_date']); 
                        }
                        
                    }
                    
                    $data .='<thead><tr><th>'.translation('sr_no').'</th>';
                    $data .='<th>'.translation('date').'</th>';

                    if(!empty($period))
                    {

                        $totalPeriodCount = $period->num_of_periods;
                        for($i=0;$i<$period->num_of_periods;$i++)
                        {
                            $data .='<th style="text-align="center">'.translation('period').' '.($i+1).'</th>';
                        }
                    }
                    $data .='</tr></thead><tbody>';
                    foreach ($arr_dates as $key => $date) {
                       $data .='<tr><td>'.($key+1).'</td>'; 
                       $data .= '<td>'.getDateFormat($date).'</td>';

                        if(isset($period->num_of_periods))
                        {
                            for($i=0 ; $i<$period->num_of_periods ; $i++)
                            {
                                $attendance_data = $this->StudentPeriodAttendanceModel
                                                        ->where('school_id',$this->school_id)
                                                        ->where('level_class_id',$student->level_class_id)
                                                        ->where('academic_year_id',$this->academic_year)
                                                        ->where('period_no',($i+1))
                                                        ->where('attendance_date',$date)
                                                        ->first();
                                
                                if(isset($attendance_data) && !empty($attendance_data))
                                {
                                    $data_attendance = $attendance_data->toArray();
                                

                                    if(isset($data_attendance) && !empty($data_attendance))
                                    {
                                       /* foreach ($data_attendance as $key => $attendance) 
                                        {*/
                                            
                                                if(isset($data_attendance['attendance']) && !empty($data_attendance['attendance']))
                                                {
                                                    $stud_attendance =  json_decode($data_attendance['attendance'],true);

                                                }

                                                if($fun_type == 'export')
                                                {

                                                    $data .='<td>';
                                                                if(array_key_exists($student['user_id'],$stud_attendance))
                                                                {
                                                                    $data .= ucfirst($stud_attendance[$student['user_id']]);
                                                                }
                                                        $data .='</td>';
                                                }
                                                else
                                                {

                                                    $data .='<td><div  style="width: 150px"';
                                                                if(array_key_exists($student['user_id'],$stud_attendance))
                                                                {
                                                                    if($stud_attendance[$student['user_id']] == 'present')
                                                                    {
                                                                        $data .= 'class="alert alert-success">';
                                                                    } 

                                                                    if($stud_attendance[$student['user_id']] == 'absent')
                                                                    {
                                                                        $data .= 'class="alert alert-danger">';
                                                                    }

                                                                    if($stud_attendance[$student['user_id']] == 'late')
                                                                    {
                                                                        $data .= 'class="alert alert-warning">';
                                                                    }                                    
                                                                    $data .= ucfirst($stud_attendance[$student['user_id']]);
                                                                }
                                                        $data .='</div></td>';
                                                }
                                            
                                           
                                       // }
                                    }
                                }
                                else
                                {   
                                    if($fun_type == 'export'){
                                        $data .='<td>-</td>';
                                    }else{
                                        $data .='<td> <div  style="width: 150px;"> - </div></td>';
                                    }
                                }
                            }

                        }
                         $data .= '</tr>';
                    }
                    $data .='</tbody>';
                    if($fun_type=='export')
                    {
                        
                        $arr_return['totoalCount'] =  $totalPeriodCount;
                        $arr_return['data']       =  $data;
                        return $arr_return;
                    }
                    else
                    {
                        return $data;
                    }
                }
                else
                {
                    $data ='<div class="alert alert-danger" style="text-align:center">'.translation('no_data_available').'</div>';
                    if($fun_type == 'export')
                    {
                        $arr_return['totoalCount'] =  $totalPeriodCount;
                        $arr_return['data']       =  $data;
                        return $arr_return;
                    }
                    else
                    {
                        return $data;
                    }
                }
        }
   }

    /*
    | export() : Export List
    | Auther  : Padmashri 
    | Date    : 15-12-2018
    */
     public function export(Request $request)
    {
        $sheetTitlePDF = $sheetTitle = '';

        $student         = $this->StudentModel->where('school_id',$this->school_id)
                                              ->with('get_user_details')
                                              ->where('is_active',1)
                                              ->where('has_left',0)
                                              ->where('user_id',$this->kid_id)
                                              ->first();
                             
        if(!empty($student))
        {
            $sheetTitle =$this->module_title.'-'.date('d-m-Y').'-'.uniqid(). ' ( '.ucfirst($student->get_user_details->first_name)." ".ucfirst($student->get_user_details->last_name).")";  
            $sheetTitlePDF = $this->module_title.'-'.date('d-m-Y').' ( '.ucfirst($student->get_user_details->first_name)." ".ucfirst($student->get_user_details->last_name).")";  
        }
        else
        {
            $sheetTitlePDF = $sheetTitle = ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid();    
        }

        $file_type    = config('app.project.export_file_formate');
        $obj_data     = $this->get_student_data($request,'export');
        $student_name = 
        $start_date     = $request->input('start_date');
        $end_date       = $request->input('end_date');
        if(($start_date=='' || $start_date=='0000-00-00') || ($end_date=='' || $end_date=='0000-00-00'))
        {
            Flash::error(translation("please_select_date_range"));
            return redirect()->back();
        }

        if(sizeof($obj_data['data'])<=0 || $obj_data['data']==''){
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
            $totoalColumnCount = $obj_data['totoalCount']+1;

            \Excel::create($sheetTitle, function($excel) use($totoalColumnCount){

                $excel->sheet(ucwords($this->module_title), function($sheet)use($totoalColumnCount) {
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
            $this->arr_view_data['totoalCount'] = $obj_data['totoalCount'];
            $this->arr_view_data['sheetTitle'] = $sheetTitlePDF;

            $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
            return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
        }
    }

}