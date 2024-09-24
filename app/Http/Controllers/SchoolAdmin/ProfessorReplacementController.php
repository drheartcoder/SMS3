<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;

use App\Models\UserModel;
use App\Models\ProfessorModel;
use App\Models\LevelClassModel;
use App\Models\UserTranslationModel;
use App\Models\SchoolProfessorTeachingHours;
use App\Models\LevelModel;

use App\Models\CourseModel;
use App\Models\SchoolProfileModel;
use App\Models\SchoolPeriodsModel;
use App\Models\SchoolSubjectsModel;
use App\Models\SchoolTimeTableModel;
use App\Models\ProfessorCoursesmodel;
use App\Models\SchoolPeriodTimingModel;
use App\Models\SchoolTemplateTranslationModel;
use App\Models\AssignReplacedLecturesModel;
use App\Models\ProfessorReplacementModel;
use App\Models\NotificationModel;
use App\Models\AcademicYearModel;   

use App\Common\Services\LanguageService;
use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;

use DB;
use Flash;
use Validator;
use Sentinel;
use Session;
use Datatables;
use PDF;

class ProfessorReplacementController extends Controller
{
    use MultiActionTrait;
    public function __construct(   
                                    LanguageService $language,
                                    EmailService $mail_service,
                                    CommonDataService $CommonDataService
                                )
    {
        $this->UserModel                    = new UserModel();
        $this->UserTranslationModel         = new UserTranslationModel();
        $this->AcademicYearModel            = new AcademicYearModel();
        $this->EmailService                 = $mail_service;
        $this->ProfessorModel               = new ProfessorModel();
        $this->LanguageService              = $language;  
        $this->SchoolProfileModel           = new SchoolProfileModel();
        $this->CommonDataService            = $CommonDataService;
        $this->SchoolProfessorTeachingHours = new SchoolProfessorTeachingHours();
        $this->SchoolPeriodsModel           = new SchoolPeriodsModel();
        $this->SchoolTimeTableModel         = new SchoolTimeTableModel();
        $this->SchoolSubjectsModel          = new SchoolSubjectsModel();
        $this->ProfessorCoursesmodel        = new ProfessorCoursesmodel();
        $this->LevelModel                   = new LevelModel();
        $this->SchoolPeriodTimingModel      = new SchoolPeriodTimingModel();
        $this->LevelClassModel              = new LevelClassModel();
        $this->AssignReplacedLecturesModel  = new AssignReplacedLecturesModel();
        $this->ProfessorReplacementModel    = new ProfessorReplacementModel();
        $this->CourseModel                  = new CourseModel();
        $this->NotificationModel            = new NotificationModel();
        $this->BaseModel                    = $this->ProfessorReplacementModel;

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/professor_replacement';
        
        $this->module_title                 = translation("professor_replacement");
        $this->modyle_url_slug              = translation("professor_replacement");

        $this->module_view_folder           = "schooladmin.professor_replacement";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-clock-o';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->first_name = $this->last_name =$this->ip_address ='';
        $this->role       = config('app.project.school_admin_panel_slug');

        $this->arr_view_data['page_title']      = $this->module_title;

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
            $this->user_id           = $obj_data->id;
        }
        /* Activity Section */

        /*Local Section*/
        if(Session::has('locale'))
        {
            $this->locale = Session::get('locale');
        }
        else
        {
            $this->locale = 'en';
        }
        /*Local Section*/
     
        $this->weekly_days = config('app.project.week_days');
 


        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

         
    }   

    public function index() 
    {

        $this->arr_view_data['page_title']      = translation('manage').' '.$this->module_title;
        $this->arr_view_data['create_icon']     = 'fa fa-list';
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function create() 
    {
        $arr_professors = [];
        $obj_professors = $this->UserModel->whereHas('professor_details',function($q)
                                            {
                                                $q->where('school_id',$this->school_id)
                                                  ->where('academic_year_id',$this->academic_year)
                                                  ->where('has_left',0);
                                            }) 
                                          ->get();
        if(isset($obj_professors) && count($obj_professors)>0)
        {
            $arr_professors = $obj_professors->toArray();
        }

        $this->arr_view_data['professors']      = $arr_professors;
        $this->arr_view_data['page_title']      = $this->module_title;
        $this->arr_view_data['create_icon']     = $this->create_icon;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.create', $this->arr_view_data);
    }

    public function add(Request $request)
    {
        $date                   =  $request->input('start_date');
        $end_date               =  $request->input('end_date');
        $absent_professor_id    =  $request->input('id');
        $absent_professor_no    =  $request->input('professor_no');


        $obj_data = $this->ProfessorReplacementModel
                         ->where('professor_id',$absent_professor_id)
                         ->where('school_id',$this->school_id)
                         ->where('academic_year_id',$this->academic_year)
                         ->whereRaw(" ((DATE('".$request->input('start_date')."') BETWEEN DATE(from_date) AND DATE(to_date)) OR (DATE('".$request->input('end_date')."') BETWEEN DATE(from_date) AND DATE(to_date)) OR (DATE(from_date) BETWEEN DATE('".$request->input('start_date')."') AND DATE('".$request->input('end_date')."')) OR
(DATE(to_date) BETWEEN DATE('".$request->input('start_date')."') AND DATE('".$request->input('end_date')."'))) ")
                         ->first();
        if(isset($obj_data) && count($obj_data)>0)
        {
            if($obj_data->from_date == $obj_data->to_date)
            {
                $data = '<div class="alert alert-danger" style="text-align:center">'.translation('professor_replacement_scheduled_for').' '.getDateFormat($obj_data->from_date).'</div>' ;     
            }
            else
            {
                $data = '<div class="alert alert-danger" style="text-align:center">'.translation('professor_replacement_scheduled_for').' '.getDateFormat($obj_data->from_date).' - '.getDateFormat($obj_data->to_date).'</div>' ;        
            }
           
           return $data;
        }
        else
        {            
            $status                 =  $request->input('status');
            $data = $this->get_professor_timetable($date,$end_date,$absent_professor_id,$absent_professor_no,$status);
            return $data;
        }
    }

    public function get_professor_timetable($date,$end_date=FALSE,$id,$no,$status)
    {

        $data  = $date_to = '';
        $weekdays = $dates  = $arr_replaced_lectures = $days = [];
        $date_from          = strtotime($date);
        if($end_date!='')
        {
            $date_to            = strtotime($end_date);

            for ($i=$date_from; $i<=$date_to; $i+=86400) {  
                $value = date('Y-m-d',$i);
                $weekday            = date('l', strtotime($value));
                $current_day        = strtoupper(substr($weekday,0,3)); 
                $dates[$current_day] = date('Y-m-d',$i);
                array_push($weekdays,$current_day);
            }
        }
        else
        {
            $value = date('Y-m-d',$date_from);
            $weekday            = date('l', strtotime($value));
            $current_day        = strtoupper(substr($weekday,0,3)); 
            $dates[$current_day] = date('Y-m-d',$date_from);
            array_push($weekdays,$current_day);
        }

        $obj_replaced_lectures = $this->AssignReplacedLecturesModel
                                      ->with('level_class_details.level_details','level_class_details.class_details','course_details')
                                      ->with('professor_details')
                                      ->whereBetween('date',[$date,$end_date])
                                      ->where('absent_professor_id',$id)
                                      ->where('school_id',$this->school_id)
                                      ->where('academic_year_id',$this->academic_year)
                                      ->get();

        if(isset($obj_replaced_lectures) && count($obj_replaced_lectures)>0)
        {
            $arr_replaced_lectures = $obj_replaced_lectures->toArray();
        }
        /*dd($arr_replaced_lectures);*/

        $academic_year_id   = $this->academic_year;  
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
                                        //->where('level_id','=',$level)
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
                        'professor_id'     => $id
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
        
        if(isset($obj_time_table) && count($obj_time_table)>0)
        {
            $arr_time_table = $obj_time_table->toArray();

            if($status =='create')
            {
                $arr_data['professor_id'] = $id;
                $arr_data['professor_no'] = $no;
                $arr_data['from_date']    = $date;
                if($end_date != '')
                {
                    $arr_data['to_date']      = $end_date;    
                }
                else
                {
                    $arr_data['to_date']      = $date;       
                }
                
                $arr_data['school_id']    = $this->school_id;
                $arr_data['academic_year_id']    = $this->academic_year;

                $replace_professor  =   $this->ProfessorReplacementModel->create($arr_data);
                if($replace_professor)
                {
                    $data .= '<input type="hidden" id="assignment_id" name="assignment_id" value="'.$replace_professor->id.'">';
                }
                else
                { 
                    $data.= '<div class="alert alert-danger" style="text-align:center">'.translation('something_went_wrong').'</div>';
                    return $data;
                }
            }
            elseif($status =='edit')
            {

                $obj_data = $this->ProfessorReplacementModel->where('professor_id',$id)
                                 ->where('school_id',$this->school_id)
                                 ->where('academic_year_id',$this->academic_year)
                                 ->where('from_date',$date);
                                 if($end_date!='')
                                 {
                                 $obj_data = $obj_data->where('to_date',$end_date);
                                 }
                                 else
                                 {
                                 $obj_data = $obj_data->where('to_date',$date)  ;
                                 }
                                 $obj_data = $obj_data->first();

                if(isset($obj_data) && count($obj_data)>0)
                {
                    $data .= '<input type="hidden" id="assignment_id" name="assignment_id" value="'.$obj_data->id.'">';
                }
            }
        }
        else
        { 
            $data.= '<div class="alert alert-danger" style="text-align:center">'.translation('no_lectures_assigned').'</div>';
            return $data;
        }

        $session_num_of_periods =$this->SchoolPeriodsModel->where(['school_id'=>$this->school_id,'academic_year_id'=>$this->academic_year])->first();

        $arr_holiday =  isset($session_num_of_periods->weekly_off)&&$session_num_of_periods->weekly_off!=''?json_decode($session_num_of_periods->weekly_off):config('app.project.default_weekly_off');          

        
        
        if(isset($session_num_of_periods) && $session_num_of_periods!="")
        {
            $data .= '<table class="table table-advance"  id="table_module" style="border:1!important;">';
            $data .='<thead><tr><th>&nbsp;</th>';
            if(count($this->weekly_days) >0)
            {
                foreach($this->weekly_days as $day => $day_full_name)
                {
                    $data .= '<th>'.translation(strtolower($day)).'</th>';
                }
            }
            $period = $timing = [];
            $data .= '</tr></thead><tbody>';
            for($i=1; $i<=$session_num_of_periods->num_of_periods; $i++)
            {   
                $data .= '<tr> <td>'.translation('period').' '.$i.'</td>';
                    if(count($this->weekly_days) >0)
                    {
                        foreach($this->weekly_days as $day => $day_full_name)
                        {  
                            $data .= '<td class="droppable_td">';  

                                if(isset($arr_replaced_lectures) && count($arr_replaced_lectures)>0)
                                {
                                    $no = 1;
                                    foreach($arr_replaced_lectures as $l => $lecture)
                                    {
                                        if(isset($lecture['day']) && $lecture['day']==strtoupper($day) && isset($lecture['period_no']) && $lecture['period_no']==$i)
                                        {
                                            if(!in_array(strtoupper($day), $days))
                                            {
                                                array_push($days,strtoupper($day));
                                            }
                                            $timing[$day][$lecture['level_class_id']]['start_time'] = $lecture['start_time'] ;
                                            $timing[$day][$lecture['level_class_id']]['end_time']   = $lecture['end_time'] ;

                                          $data .= '<div class="seperate_subjects" style="color: #1275ed"';

                                          if(in_array($lecture['day'],$weekdays))
                                            {
                                                $data .='id ="td_'.$lecture['day'].'_'.$l.'" onClick="get_details(this,'.$lecture['course_id'].',\''.$lecture['start_time'].'\',\''.$lecture['end_time'].'\','.$lecture['level_class_id'].',\''.$lecture['day'].'\',\''.$dates[$lecture['day']].'\','.$lecture['period_no'].',\''.base64_encode($lecture['id']).'\');" style="cursor:pointer;" data-toggle="modal" data-target="#myModal" >';
                                            }
                                            else
                                            {
                                                $data .='id ="td_'.$lecture['day'].'_'.$l.'" title="'.translation('access_denied').'">';
                                            }

                                          $data .= ucwords($lecture['professor_details']['first_name']) or '';
                                          $data .= ' '.ucwords($lecture['professor_details']['last_name']) or '';
                                          $data .= '<br/>'.$lecture['level_class_details']['level_details']['level_name'] or '';
                                          $data .=' '.$lecture['level_class_details']['class_details']['class_name'] or '';
                                          $data .= '<br/>';
                                          $data .=' '.$lecture['course_details']['course_name'] or '';
                                          $data .= '<br/>';
                                          $data .= getTimeFormat($lecture['start_time']) or '';
                                          $data .= ' - ';
                                          $data .= getTimeFormat($lecture['end_time']) or '';
                                          $data .= '</div>';
                                        }
                                        $no++;
                                    }
                                }
                                
                                if(isset($arr_time_table) && sizeof($arr_time_table)>0)
                                { 
                                     $no = 1;
                                    foreach($arr_time_table as $key => $timetable)
                                    {
                                        
                                        if(isset($timetable['day']) && $timetable['day']==strtoupper($day) && isset($timetable['periods_no']) && $timetable['periods_no']==$i)
                                        {
                                            array_push($period,$i);

                                            if(!empty($timetable['class_details'])  && isset($timetable['class_details']['class_name']) && $timetable['class_details']['class_name']!='')
                                            {
                                              $class_name = $timetable['class_details']['class_name'];
                                            }

                                            if(!empty($timetable['level_details'])  && isset($timetable['level_details']['level_name']) && $timetable['level_details']['level_name']!='')
                                            {
                                              $level_name = $timetable['level_details']['level_name'];
                                            }

                                            if(isset($timetable['professor_subjects']['course_name']) && $timetable['professor_subjects']['course_name']!="")
                                            {
                                                $subject_name = $timetable['professor_subjects']['course_name'];
                                            }
                                            else
                                            {
                                                $subject_name = "NA";
                                            }
                                            if(isset($timing[$day]) && count($timing[$day])>0)
                                            {
                                                if(!in_array($timetable['period_start_time'],$timing[$day][$timetable['level_class_id']]) && !in_array($timetable['period_end_time'],$timing[$day][$timetable['level_class_id']]))
                                                {
                                                    $data .= '<div class="seperate_subjects" '; 
                                                    if(in_array($timetable['day'],$weekdays))
                                                    {
                                                        $data .='id ="td_'.$timetable['day'].'_'.$key.'" onClick="get_details(this,'.$timetable['course_id'].',\''.$timetable['period_start_time'].'\',\''.$timetable['period_end_time'].'\','.$timetable['level_class_id'].',\''.$timetable['day'].'\',\''.$dates[$timetable['day']].'\','.$timetable['periods_no'].');" style="cursor:pointer;" data-toggle="modal" data-target="#myModal" >';
                                                    }
                                                    else
                                                    {
                                                        $data .='id ="td_'.$timetable['day'].'_'.(++$key).'" title="'.translation('access_denied').'">';
                                                    }  
                                                    
                                                    $data .= $level_name.' '.$class_name.'<br/>'  ;
                                                    $data .= $subject_name;
                                                    $data .= '<br/>';
                                                    $data .= getTimeFormat($timetable['period_start_time']) or '';
                                                    $data .= ' - ';
                                                    $data .= getTimeFormat($timetable['period_end_time']) or '';
                                                    $data .= '</div>';
                                                }
                                            }
                                            else
                                            {
                                                $data .= '<div class="seperate_subjects" '; 
                                                    if(in_array($timetable['day'],$weekdays))
                                                    {
                                                        $data .='id ="td_'.$timetable['day'].'_'.$key.'" onClick="get_details(this,'.$timetable['course_id'].',\''.$timetable['period_start_time'].'\',\''.$timetable['period_end_time'].'\','.$timetable['level_class_id'].',\''.$timetable['day'].'\',\''.$dates[$timetable['day']].'\','.$timetable['periods_no'].');" style="cursor:pointer;" data-toggle="modal" data-target="#myModal" >';
                                                    }
                                                    else
                                                    {
                                                        $data .='id ="td_'.$timetable['day'].'_'.(++$key).'" title="'.translation('access_denied').'">';
                                                    }  
                                                    
                                                    $data .= $level_name.' '.$class_name.'<br/>'  ;
                                                    $data .= $subject_name;
                                                    $data .= '<br/>';
                                                    $data .= getTimeFormat($timetable['period_start_time']) or '';
                                                    $data .= ' - ';
                                                    $data .= getTimeFormat($timetable['period_end_time']) or '';
                                                    $data .= '</div>';
                                            }  
                                            
                                        }

                                    }
                                }
                            $data .= '</td>';
                        }
                        $data .= '</tr>';
                                       
                    }
            }
            $data .= '</tbody></table>';
            return $data;      
        }
        

    }

    public function get_professor_no(Request $request)
    {
        $data = '';
        $id = $request->input('professor_id');
        
        $users_data = $this->ProfessorModel->where('user_id',$id)->first();
        
        if(isset($users_data) && count($users_data)>0)
        {
           $data = $users_data->professor_no;
        }
        
        return $data;
    }

    public function get_free_professors(Request $request)
    {
        $data = '';
        $professor_ids = [];
        $professors_alloted = $this->SchoolTimeTableModel
                                   ->with('professor_details')
                                   ->whereHas('professor_details',function($q){
                                        $q->where('has_left',0);
                                   })
                                   ->select('professor_id')
                                   ->where('school_id',$this->school_id)
                                   ->where('academic_year_id',$this->academic_year)
                                   ->whereRaw(" ((TIME('".$request->input('start_time')."') BETWEEN TIME(period_start_time) AND TIME(period_end_time)) OR
(TIME('".$request->input('end_time')."') BETWEEN TIME(period_start_time) AND TIME(period_end_time)) OR
(TIME(period_start_time) BETWEEN TIME('".$request->input('start_time')."') AND TIME('".$request->input('end_time')."')) OR
(TIME(period_end_time) BETWEEN TIME('".$request->input('start_time')."') AND TIME('".$request->input('end_time')."'))) ")
                                   ->where('day',$request->input('day'))
                                   ->where('course_id',$request->input('course_id'))
                                   ->get();

        
        if(isset($professors_alloted) && count($professors_alloted)>0)
        {
            $arr_alloted = $professors_alloted->toArray();
            foreach ($arr_alloted as $key => $value) {
                array_push($professor_ids, $value['professor_id']);
            }
        }

        $obj_data = $this->AssignReplacedLecturesModel
                         ->select('replaced_professor_id')
                         ->where('date',$request->input('date'))
                         ->where('school_id',$this->school_id)
                         ->where('academic_year_id',$this->academic_year)
                         ->where('course_id',$request->input('course_id'))
                         ->whereRaw(" ((TIME('".$request->input('start_time')."') BETWEEN TIME(start_time) AND TIME(end_time)) OR (TIME('".$request->input('end_time')."') BETWEEN TIME(start_time) AND TIME(end_time)) OR (TIME(start_time) BETWEEN TIME('".$request->input('start_time')."') AND TIME('".$request->input('end_time')."')) OR
(TIME(end_time) BETWEEN TIME('".$request->input('start_time')."') AND TIME('".$request->input('end_time')."'))) ")
                         ->get();

        if(isset($obj_data) && count($obj_data)>0)
        {
            $arr_data = $obj_data->toArray();
            foreach ($arr_data as $key => $value) {
                array_push($professor_ids, $value['replaced_professor_id']);
            }
        }
        
        $arr_professors = [];
        /*$obj_professors = $this->UserModel->whereHas('professor_details',function($q)use($professor_ids)
                                            {
                                                $q->where('school_id',$this->school_id)
                                                  ->where('academic_year_id',$this->academic_year)
                                                  ->whereNotIn('user_id',$professor_ids);
                                            }) 
                                          ->get();*/

        $obj_professors = $this->ProfessorCoursesmodel
                               ->select('professor_id','course_id')
                               ->with('get_user_details')
                               ->where('school_id',$this->school_id)
                               ->where('academic_year_id',$this->academic_year)
                               ->whereNotIn('professor_id',$professor_ids)
                               ->get();

                               
        if(isset($obj_professors) && count($obj_professors)>0)
        {
            $professors = $obj_professors->toArray();
        }
        if(isset($professors) && count($professors)>0)
        {
            foreach ($professors as $key => $value) {

                if(in_array($request->input('course_id'),json_decode($value['course_id'],true)))
                {
                    array_push($arr_professors,$value);
                }
            }
        }

         if(isset($arr_professors) && count($arr_professors)>0)
         {
            $data .= '<option value="">'.translation('select_professor_to_replace').'</option>';
            foreach($arr_professors as $key => $value)
            {
                $data .= '<option value="';
                $data .= isset($value['get_user_details']['id'])?$value['get_user_details']['id']:'';
                $data .= '">';

                $first_name =  isset($value['get_user_details']['first_name'])?ucwords($value['get_user_details']['first_name']):'';
                $last_name  =  isset($value['get_user_details']['last_name'])?ucwords($value['get_user_details']['last_name']):''; 
                $data .= $first_name.' '.$last_name;
                $data .= '</option>';
            }
        }
        return $data;
    }

    public function store(Request $request)
    {
        
        $record = [];
        
        $absent_professor_id    =  $request->input('professor_id');
        $absent_professor_no    =  $request->input('professor_no');
        $replaced_professor_id  =  $request->input('professor');
        $replaced_professor_no  =  $request->input('user_id');
        $day                    =  $request->input('day');
        $date                   =  $request->input('date');
        $level_class_id         =  $request->input('level_class_id');
        $start_time             =  $request->input('start_time');
        $end_time               =  $request->input('end_time');
        $period_no              =  $request->input('period');
        $from_date              =  $request->input('from_date');
        $to_date                =  $request->input('to_date');
        $assignment_id          =  $request->input('assignment_id');
        $course_id              =  $request->input('course_id');

        $course_name = $this->CourseModel->where('id',$course_id)->first();
        $level_class = $this->CommonDataService->get_level_class($level_class_id);
        
        $arr_data1 = [];

        $arr_data1['school_id']              = $this->school_id;
        $arr_data1['level_class_id']         = $level_class_id;
        $arr_data1['absent_professor_id']    = $absent_professor_id;
        $arr_data1['absent_professor_no']    = $absent_professor_no;
        $arr_data1['replaced_professor_id']  = $replaced_professor_id;
        $arr_data1['replaced_professor_no']  = $replaced_professor_no;
        $arr_data1['start_time']             = $start_time;
        $arr_data1['end_time']               = $end_time;
        $arr_data1['period_no']              = $period_no;
        $arr_data1['date']                   = $date;
        $arr_data1['day']                    = $day;
        $arr_data1['assignment_id']          = $assignment_id;
        $arr_data1['course_id']              = $course_id;
        $arr_data1['academic_year_id']       = $this->academic_year;

        $store_data =   $this->AssignReplacedLecturesModel->create($arr_data1);

        if($store_data)
        {

            $professor  = $this->UserModel->where('id',$replaced_professor_id)->first();
            $first_name = isset($professor->first_name)?ucwords($professor->first_name):'';
            $last_name  = isset($professor->last_name)?ucwords($professor->last_name):'';
            $user_name  = $first_name.' '.$last_name;

            $data['start_time'] = getTimeFormat($start_time);
            $data['end_time']   = getTimeFormat($end_time);
            $data['user_name']  = $user_name;
            $data['id']         = base64_encode($store_data->id);
            $data['level_name'] = isset($level_class['level_details']['level_name'])?$level_class['level_details']['level_name']:'';
            $data['class_name'] = isset($level_class['class_details']['class_name'])?$level_class['class_details']['class_name']:'';
            if(isset($course_name) && !is_null($course_name) && count($course_name))
            {
                $course_name = $course_name->toArray();
                $data['course_name'] = $course_name['course_name'];
            }

            $result = $this->send_notification($replaced_professor_id,$store_data,$data);
            $record['msg'] = translation('professor_replaced_successfully');
            $record['data']= $data;
            $record['type']= 'success';
            
        }
        else
        {

            $record['msg'] = translation('something_went_wrong');
            $record['type']= 'error';
        }
        return $record;

    }

    /*
    | get_exam_type_details() : Course details using ajax 
    | Auther                  : Gaurav 
    | Date                    : 09-05-2018
    */
    function get_replacement_details(Request $request)
    {     
        $school_id     = $this->school_id;
        $academic_year_id = $this->academic_year;  

        /*$str                =  $this->CommonDataService->get_academic_year_less_than($academic_year_id);
        $arr_academic_year  = explode(',', $str);   */

        $replacement_table                  = $this->ProfessorReplacementModel->getTable();
        $prefixed_replacement_table         = DB::getTablePrefix().$this->ProfessorReplacementModel->getTable();

        $user_details                       = $this->UserModel->getTable();
        $prefixed_user_details              = DB::getTablePrefix().$this->UserModel->getTable();

        $user_trans_table                   = $this->UserTranslationModel->getTable();                  
        $prefixed_user_trans_table          = DB::getTablePrefix().$this->UserTranslationModel->getTable();

        $obj_user = DB::table($replacement_table)
                                ->select(DB::raw($replacement_table.".id as id,".
                                                 $replacement_table.".professor_no, ".
                                                 $replacement_table.".from_date, ".
                                                 $replacement_table.".to_date, ".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
                                                          .$prefixed_user_trans_table.".last_name) as user_name"
                                                 ))
                                ->whereNull($replacement_table.'.deleted_at')
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$replacement_table.'.professor_id')
                                ->where($replacement_table.'.school_id','=',$school_id)
                                ->where($replacement_table.'.academic_year_id','=',$academic_year_id)
                                ->where($user_trans_table.'.locale','=',Session::get('locale'))
                                ->orderBy($replacement_table.'.created_at','DESC');
                                                                                     
        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
            $obj_user = $obj_user ->WhereRaw("( (".$replacement_table.".professor_no LIKE '%".$search_term."%') ")
                                  ->orWhereRaw("(".$replacement_table.".from_date LIKE '%".$search_term."%') ")
                                  ->orWhereRaw("(".$replacement_table.".to_date LIKE '%".$search_term."%') ")
                                  ->orWhereRaw(" ( CONCAT(".$prefixed_user_trans_table.".first_name,'',".$prefixed_user_trans_table.".last_name)  LIKE  '%".$search_term."%' ) )");
        }
        return $obj_user;   
    }

        /*
        | get_records() : Replacement get_records 
        | Auther        : Sayali B 
        | Date          : 30-07-2018
        */
    public function get_records(Request $request)
    {
               
        $role = Session::get('role');
        
        $arr_current_user_access = $this->CommonDataService->current_user_access();
        $obj_exam_type  = $this->get_replacement_details($request);

        $json_result     = Datatables::of($obj_exam_type);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('professor_replacement.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data) 
                            {
                                return base64_encode($data->id);
                            });
        }                    

        $json_result     = $json_result->editColumn('name',function($data)
                            { 
                                 
                                if($data->user_name!=null && $data->user_name!=''){
                                    return  ucfirst($data->user_name);
                                }else{
                                    return  '-';
                                }

                            }) 
                            ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                            {
                                $build_edit_action = '';
                                if(array_key_exists('professor_replacement.update',$arr_current_user_access)){
                                    $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                    $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';    
                                }
                                $build_delete_action ='';
                                
                                if(array_key_exists('professor_replacement.delete',$arr_current_user_access))
                                {
                                    $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                    $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                }

                               return $build_edit_action.'&nbsp;'.$build_delete_action.'&nbsp;';
                            })
                            ->editColumn('build_checkbox',function($data) use($arr_current_user_access){
                                $build_checkbox='';
                                if(array_key_exists('professor_replacement.update',$arr_current_user_access) || array_key_exists('professor_replacement.delete',$arr_current_user_access))
                                {
                                    $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 

                                    
                                }
                                return  $build_checkbox;
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }


        /*
        | get_records() : edit Replacement  
        | Auther        : Sayali B 
        | Date          : 31-07-2018
        */
    public function edit($enc_id)
    {
        $arr_data = [];
        $id = base64_decode($enc_id);
        if(!is_numeric($id))
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }

        $obj_data = $this->ProfessorReplacementModel->with('professor_details')->where('id',$id)->first();
        if(isset($obj_data) && count($obj_data)>0)
        {
            $arr_data = $obj_data->toArray();
        }
        $enc_id = isset($arr_data['id'])?base64_encode($arr_data['id']):0;

        $this->arr_view_data['enc_id']          = $enc_id;
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['page_title']      = translation('edit').' '.$this->module_title;
        $this->arr_view_data['edit_icon']       = 'fa fa-edit';
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.edit', $this->arr_view_data);

    }

    public function get_details(Request $request)
    {
        $from_date = $request->input('start_date');
        $to_date   = $request->input('end_date');
        $id        = $request->input('id');
        $no        = $request->input('no');
        $status    = $request->input('status');

        $data      = $this->get_professor_timetable($from_date,$to_date,$id,$no,$status);
        return $data;
    }

    public function update(Request $request,$enc_id)
    {
        $id = base64_decode($enc_id);
        if(!is_numeric($id))
        {
            $record['msg'] = translation('something_went_wrong');
            $record['type']= 'error';
            return $record;
        }

        $record = [];

        $replaced_professor_id  =  $request->input('professor');
        $replaced_professor_no  =  $request->input('user_id');
        $day                    =  $request->input('day');
        $date                   =  $request->input('date');
        $level_class_id         =  $request->input('level_class_id');
        $start_time             =  $request->input('start_time');
        $end_time               =  $request->input('end_time');
        $period_no              =  $request->input('period');
        $course_id              =  $request->input('course_id');

        $obj_details = $this->AssignReplacedLecturesModel->where('id',$id)->first();


        $course_name = $this->CourseModel->where('id',$course_id)->first();
        $level_class = $this->CommonDataService->get_level_class($level_class_id);

        if($obj_details)
        {
            $arr_data1 = [];

            $arr_data1['school_id']              = $this->school_id;
            $arr_data1['level_class_id']         = $level_class_id;
            $arr_data1['replaced_professor_id']  = $replaced_professor_id;
            $arr_data1['replaced_professor_no']  = $replaced_professor_no;
            $arr_data1['start_time']             = $start_time;
            $arr_data1['end_time']               = $end_time;
            $arr_data1['period_no']              = $period_no;
            $arr_data1['date']                   = $date;
            $arr_data1['day']                    = $day;
            $arr_data1['academic_year_id']       = $this->academic_year;
            
            $update_data =   $this->AssignReplacedLecturesModel->where('id',$id)->update($arr_data1);
            $data1 =  $this->AssignReplacedLecturesModel->where('id',$id)->first();
            if($update_data)
            {
                $professor  = $this->UserModel->where('id',$replaced_professor_id)->first();
                $first_name = isset($professor->first_name)?ucwords($professor->first_name):'';
                $last_name  = isset($professor->last_name)?ucwords($professor->last_name):'';
                $user_name  = $first_name.' '.$last_name;

                $data['start_time'] = getTimeFormat($start_time);
                $data['end_time']   = getTimeFormat($end_time);
                $data['user_name']  = $user_name;
                $data['level_name'] = isset($level_class['level_details']['level_name'])?$level_class['level_details']['level_name']:'';
                $data['class_name'] = isset($level_class['class_details']['class_name'])?$level_class['class_details']['class_name']:'';
                if(isset($course_name) && !is_null($course_name) && count($course_name))
                {
                    $course_name = $course_name->toArray();
                    $data['course_name'] = $course_name['course_name'];
                }
                $result = $this->send_notification($replaced_professor_id,$data1,$data);

                $record['msg'] = translation('professor_replacement_updated_successfully');
                $record['data']= $data;
                $record['type']= 'success';
                
            }
            else
            {
                $record['msg'] = translation('something_went_wrong');
                $record['type']= 'error';
            }
            return $record;
        }
        else
        {
            $record['msg'] = translation('something_went_wrong');
            $record['type']= 'error';
            return $record;
        }
        
    }
    public function send_notification($user_id,$data,$detail_data)
    {
        $details = $this->CommonDataService->get_user_permissions($user_id,config('app.project.role_slug.professor_role_slug'),$this->academic_year);
        $prof_data = $this->UserModel->where('id',$user_id)->first();
        if(isset($details['notifications']['notification_permission']) && $details['notifications']['notification_permission']!=null)
        {
            $permissions = json_decode($details['notifications']['notification_permission'],true);

            if(array_key_exists('professor_replacement.app',$permissions))
            {
             
                $arr_notification = [];
                $arr_notification['school_id']          =  $this->school_id;
                $arr_notification['from_user_id']       =  $this->user_id;
                $arr_notification['to_user_id']         =  $user_id;
                $arr_notification['user_type']          =  config('app.project.role_slug.school_admin_role_slug');
                $arr_notification['notification_type']  =  'Professor Replacement';
                $arr_notification['title']              =  'Professor Replacement: Your lecture is scheduled on '.getDateFormat($data->date).' on time '.getTimeFormat($data->start_time).' - '.getTimeFormat($data->end_time).' on '.$detail_data['level_name'].' '.$detail_data['class_name'].' for course '.$detail_data['course_name'];   
                $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.professor_role_slug').'/timetable';         

                $result = $this->NotificationModel->create($arr_notification);
            }
            $details          = [
                                    'first_name'  =>  isset($prof_data->first_name)?ucwords($prof_data->first_name):'',
                                    'date'        =>  isset($data->date)?getDateFormat($data->date):'',
                                    'start_time'  =>  isset($data->start_time)?getTimeFormat($data->start_time):'',
                                    'end_time'    =>  isset($data->end_time)?getTimeFormat($data->end_time):'',
                                    'email'       =>  isset($prof_data->email)?$prof_data->email:''
                                ];

            if(array_key_exists('professor_replacement.sms',$permissions))
            {
                
                $arr_sms_data = $this->built_sms_data($details,$prof_data->mobile_no);
                $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
            }
            if (array_key_exists('professor_replacement.email',$permissions))
            {
                $arr_mail_data = $this->built_mail_data($details); 
                $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
            }
        }
    }

    public function built_mail_data($arr_data)
     {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'     => $arr_data['first_name'],
                                  'DATE'           => $arr_data['date'],
                                  'START_TIME'     => $arr_data['start_time'],
                                  'END_TIME'       => $arr_data['end_time'],
                                  'SCHOOL_ADMIN'   => $this->CommonDataService->get_school_name($this->school_id)];
    
            $arr_mail_data                        = [];
            $arr_mail_data['email_template_slug'] = 'professor_replacement';            
            $arr_mail_data['arr_built_content']   = $arr_built_content;
            $arr_mail_data['user']                = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_sms_data($arr_data,$user,$mobile_no)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                              'DATE'           => $arr_data['date'],
                              'START_TIME'     => $arr_data['start_time'],
                              'END_TIME'       => $arr_data['end_time']];

            $arr_sms_data                      = [];
            $arr_sms_data['sms_template_slug'] = 'professor_replacement';
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $mobile_no;

            return $arr_sms_data;
        }
        return FALSE;
    }
}
