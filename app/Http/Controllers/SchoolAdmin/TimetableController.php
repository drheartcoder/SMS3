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

class TimetableController extends Controller
{
    use MultiActionTrait;
    public function __construct(    
                                    UserModel $user,
                                    UserTranslationModel $translation,
                                    ProfessorModel $professor,
                                    LanguageService $language,
                                    EmailService $mail_service,
                                    SchoolProfileModel $profile,
                                    AcademicYearModel $year,
                                    CommonDataService $CommonDataService,
                                    SchoolProfessorTeachingHours $school_professor_teaching_hours,
                                    SchoolPeriodsModel $school_period_model,
                                    SchoolTimeTableModel $school_time_table,
                                    SchoolSubjectsModel $school_subjects_model,
                                    ProfessorCoursesmodel $professor_courses_model

                                )
    {
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->AcademicYearModel            = $year;
        $this->EmailService                 = $mail_service;
        $this->ProfessorModel               = $professor;
        $this->BaseModel                    = $this->ProfessorModel;
        $this->LanguageService              = $language;  
        $this->SchoolProfileModel           = $profile;
        $this->CommonDataService            = $CommonDataService;
        $this->SchoolProfessorTeachingHours = $school_professor_teaching_hours;
        $this->SchoolPeriodsModel           = $school_period_model;
        $this->SchoolTimeTableModel         = $school_time_table;
        $this->SchoolSubjectsModel          = $school_subjects_model;
        $this->ProfessorCoursesmodel        = $professor_courses_model;
        $this->LevelModel                   = new LevelModel();
        $this->SchoolPeriodTimingModel      = new SchoolPeriodTimingModel();
        $this->LevelClassModel              = new LevelClassModel();

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/timetable';
        
        $this->module_title                 = translation("timetable");
        $this->modyle_url_slug              = translation("timetable");

        $this->module_view_folder           = "schooladmin.timetable";
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

    /*
    | create() : Teaching hours
    | Auther   : Padmashri Joshi 
    | Date     : 28th May 20108
    */
    public function teaching_hours(Request $request,$enc_id='')
    {   

        $arr_professor = $arr_edit_professor_hours =   array();
        $locale = $this->locale;

        if($enc_id!=''){
            
            $page_title = translation('edit').' '.translation('periods');
            $id = base64_decode($enc_id);
            $arr_edit = SchoolProfessorTeachingHours::where('id',$id)->first();
            $arr_edit_professor_hours = $arr_edit->toArray();
            $this->arr_view_data['enc_id']     = $enc_id;
        }else{
            $page_title = translation('professor').' '.translation('periods');
        }


        /*GET Professor*/
        $user_trans_table  = $this->UserTranslationModel->getTable();                  
        $professor         = $this->ProfessorModel->getTable();       
        $user_details      = $this->UserModel->getTable();
       
        $arr_professor  = $this->CommonDataService->get_professor_by_year($this->school_id,$this->academic_year);

        /*GET Professor*/

        /*GET TEACHERS TIME  TABLE */
        $school_professor_hours = $this->SchoolProfessorTeachingHours->getTable();
        $arr_teacher = DB::table($school_professor_hours)
                                ->select(DB::raw($school_professor_hours.".id as id,".
                                                 $school_professor_hours.".professor_id as professor_id,".
                                                 $school_professor_hours.".total_periods,".
                                                 $school_professor_hours.".assigned_periods,".
                                                 "CONCAT(".$user_trans_table.".first_name,' ',"
                                                          .$user_trans_table.".last_name) as user_name"
                                                 ))
                                ->join($professor,$school_professor_hours.'.professor_id','=',$professor.".user_id")
                                ->join($user_details,$professor.'.user_id',' = ',$user_details.'.id')
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                ->whereNull($professor.'.deleted_at')
                                ->where($professor.'.is_active','=',1)
                                ->where($professor.'.has_left','=',0)
                                ->where($school_professor_hours.".school_id","=",$this->school_id)
                                ->where($user_trans_table.'.locale','=',$locale)
                                ->where($school_professor_hours.'.academic_year_id',$this->academic_year)
                                ->orderBy($user_details.'.created_at','DESC')->get();

                              

        $arr_teacher_timetable    = $this->get_teacher_lecture_info($arr_teacher);

          
        $this->arr_view_data['arr_teacher_timetable']    = $arr_teacher_timetable;
        $this->arr_view_data['arr_edit_professor_hours'] = $arr_edit_professor_hours;
        $this->arr_view_data['arr_teacher']     = $arr_teacher;
        $this->arr_view_data['arr_professor']   = $arr_professor;

        $this->arr_view_data['academic_year']   = $this->academic_year;
        $this->arr_view_data['edit_icon']       = $this->edit_icon;
        $this->arr_view_data['module_title']    = translation('teaching_hours');
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.teaching_hours', $this->arr_view_data);
    }

     /*
    | store_teaching_hours() : Store Teaching Hours Of Professor
    | Auther  : Padmashri
    | Date    : 28-05-2018
    */
    public function store_teaching_hours(Request $request)
    {
        $section_title =  translation('periods');
        $arr_rules = [];
        
        $arr_rules['total_periods']   = 'required|numeric|min:1';
        $arr_rules['professor_id']     = 'required';
        
        $message['required']           = translation('this_field_is_required');
        $message['numeric']            = translation('please_enter_digits_only');
        $message['min']                = translation('please_enter_a_value_greater_than_or_equal_to_1');
        

        $validator = Validator::make($request->all(),$arr_rules,$message);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        
        $professor_id    =   trim($request->input('professor_id'));
        $total_periods   =   trim($request->input('total_periods'));
        
        $arr_data = [];     
        $arr_data['school_id']        = $this->school_id;
        $arr_data['professor_id']     = $professor_id;
        $arr_data['total_periods']    = $total_periods;
        $arr_data['academic_year_id'] = $this->academic_year;
        
        $obj_exist = SchoolProfessorTeachingHours::where('school_id','=',$this->school_id)->where('professor_id','=',$professor_id)->where('academic_year_id','=',$this->academic_year)->first();

        if(isset($obj_exist->id))
        {
            Flash::error($section_title." ".translation("already_exists"));
            return redirect()->back();            
        }                
        
        $res = SchoolProfessorTeachingHours::create($arr_data);
        if($res){
            Flash::success($section_title." ".translation("created_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_creating ".$section_title);
            return redirect()->back();
        }        

    }

    /*
    | update_teaching_hours() : Update Teaching Hours Of Professor
    | Auther  : Padmashri
    | Date    : 28-05-2018
    */
    public function update_teaching_hours(Request $request,$enc_teaching_hours_id)
    {
        $section_title             =  translation('periods');
        $teaching_hours_id         = base64_decode($enc_teaching_hours_id);
        $old_teaching_periods      = !empty($request->input('old_total_periods'))&&$request->input('old_total_periods')!=''?$request->input('old_total_periods'):'1';

        $arr_rules['total_periods']    = 'required|numeric|min:'.$old_teaching_periods;
        $arr_rules['professor_id']     = 'required';
        
        $message['required']           = translation('this_field_is_required');
        $message['numeric']            = translation('please_enter_digits_only');
        $message['min']                = translation('please_enter_a_value_greater_than_or_equal_to_'.$old_teaching_periods);
        

        $validator = Validator::make($request->all(),$arr_rules,$message);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $professor_id  =   trim($request->input('professor_id'));
        $total_periods =   trim($request->input('total_periods')).":00:00";
        
        $arr_data = [];     
        $arr_data['school_id']      = $this->school_id;
        $arr_data['professor_id']   = $professor_id;
        $arr_data['total_periods']    = $total_periods;
        $arr_data['academic_year_id'] = $this->academic_year;
        

        $obj_exist = SchoolProfessorTeachingHours::where('school_id','=',$this->school_id)->where('professor_id','=',$professor_id)->where('academic_year_id','=',$this->academic_year)->where('id','<>',$teaching_hours_id)->first();

        if(isset($obj_exist->id))
        {
            Flash::error($section_title." ".translation("already_exists"));
            return redirect()->back();            
        }                

       
        $status = SchoolProfessorTeachingHours::where('id', $teaching_hours_id )->update($arr_data);
        if($status)
        {
            Flash::success($section_title." ".translation("updated_successfully"));
        }
        else
        {
            Flash::error("something_went_wrong_while_updating ".$section_title);
            
        }
        return redirect($this->module_url_path.'/teaching_hours');
    }

     /*
    | timetable() : View Timetable
    | Auther  : Padmashri
    | Date    : 29-05-2018
    */
   public function new_timetable(){
        if(Session::get('page_type')=='edit'){
            Session::forget('class_id');
            Session::forget('level_id');
            Session::forget('num_of_periods');
            Session::forget('weekly_off');
            Session::forget('school_start_time');
            Session::forget('school_end_time');
            Session::forget('level_class_id'); 
        }
        if(Session::has('page_type')){
            Session::put('page_type','new');      
        }
        else{
            Session::set('page_type','new'); 
        }
        
        $arr_professor = $arr_edit_professor_hours =   array();
        $locale = $this->locale;
        $school_start_time = $school_end_time  = ''; 
        
        $obj_level = $arr_level = [];
      
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        $arr_academic_year = explode(',',$academic_year);    
        $obj_levels =   $this->LevelClassModel
                                    ->whereDoesntHave('get_periods',function($q){})
                                    ->with(['get_periods'=>function($q){}])
                                    ->with('level_details')
                                    ->whereHas('get_level',function($q){
                                        $q->where('is_active','1');
                                    })
                                    ->where('school_id',Session::get('school_id'))
                                    ->whereIn('academic_year_id',$arr_academic_year)
                                    ->groupBy('level_id')
                                    ->orderBy('position')
                                    ->get();                          
        if($obj_levels){
            $arr_level = $obj_levels->toArray();
        }   

        $class_id           = Session::get('class_id');
        $level_id           = Session::get('level_id');
        $num_of_periods     = Session::get('num_of_periods');
        $weekly_off         = json_decode(Session::get('weekly_off'));
        $school_start_time  = Session::get('school_start_time');
        $school_end_time    = Session::get('school_end_time');
        $level_class_id     = Session::get('level_class_id');
        
        $arr_classes        = [];

        $obj_classes = $this->LevelClassModel
                            ->whereDoesntHave('get_periods',function($q){})
                            ->with(['get_periods'=>function($q){}])
                            ->with('class_details')
                            ->where('level_id',$level_id)
                            ->where('school_id',Session::get('school_id'))
                            ->get();
        if($obj_classes)
        {
            $arr_classes = $obj_classes->toArray();
        }

        /*Get teachers from teaching hours table whose allocated hours*/
        $where_arr = [
                        'school_id'         => $this->school_id,
                        'academic_year_id'  => $this->academic_year,
                    ];


        $arr_teachers = [];
        $arr_teachers = $this->get_teaching_hours($class_id,$level_id);

        /*Get Teacher time table information*/


         $cond_arr = [
                        'school_id'        => $this->school_id,
                        'academic_year_id' => $this->academic_year,
                        'level_id'         => $level_id,
                        'class_id'         => $class_id,

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
                                                    ->with(['professor_subjects'])
                                                    ->get();

        $arr_time_table = [];
        if($obj_time_table)
        {
            $arr_time_table = $obj_time_table->toArray();

        }
            //dd($arr_time_table,Session::all());
        $arr_holiday =  isset($weekly_off)&&$weekly_off!=''?$weekly_off:config('app.project.default_weekly_off');
        

        /*GET the values  from period Start time end time section */
        $obj_period_timing = $arr_periods_timing = array();
        $obj_period_timing = SchoolPeriodTimingModel::where('school_id',$this->school_id)->where('level_class_id',$level_class_id)->orderBy('period_no','asc')->where('academic_year_id',$this->academic_year)->get();
        if($obj_period_timing){
            $arr_periods_timing = $obj_period_timing->toArray();
        }
        /*GET the values  from period Start time end time section */

        $this->arr_view_data['session_school_id']       = $this->school_id;
        $this->arr_view_data['session_class_id']        = $class_id;
        $this->arr_view_data['session_level_id']        = $level_id;
        $this->arr_view_data['session_level_class_id']  = $level_class_id;
        $this->arr_view_data['arr_periods_timing']      = $arr_periods_timing;
        $this->arr_view_data['session_num_of_periods']  = $num_of_periods;
        $this->arr_view_data['session_weekly_off']      = $weekly_off;
        $this->arr_view_data['session_school_start_time']  = $school_start_time;
        $this->arr_view_data['session_school_end_time']    = $school_end_time;
      
        /*$this->arr_view_data['session_period_duration'] = $period_duration;*/
        $this->arr_view_data['active_academic_year_id'] = $this->academic_year;
        $this->arr_view_data['curr_academic_year_id']   = $this->academic_year;
        $this->arr_view_data['arr_time_table']          = $arr_time_table;
        $this->arr_view_data['arr_classes']     = $arr_classes;
        $this->arr_view_data['arr_level']       = $arr_level;
        $this->arr_view_data['arr_teachers']    = $arr_teachers;
        $this->arr_view_data['weekly_days']     = $this->weekly_days;
        $this->arr_view_data['arr_holiday']     = $arr_holiday;
        $this->arr_view_data['page_title']      = $this->module_title;
        $this->arr_view_data['edit_icon']       = $this->edit_icon;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
    
        return view($this->module_view_folder.'.timetable', $this->arr_view_data);
   }

   public function edit_timetable(){
        if(Session::get('page_type')=='new'){
            Session::forget('class_id');
            Session::forget('level_id');
            Session::forget('num_of_periods');
            Session::forget('weekly_off');
            Session::forget('school_start_time');
            Session::forget('school_end_time');
            Session::forget('level_class_id'); 
        }
        if(Session::has('page_type')){
            Session::put('page_type','edit');      
        }
        else{
            Session::set('page_type','edit'); 
        }

        $arr_professor = $arr_edit_professor_hours =   array();
        $locale = $this->locale;
        $school_start_time = $school_end_time  = ''; 
        
        $obj_level = $arr_level = [];
      
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        $arr_academic_year = explode(',',$academic_year);    
        $obj_levels =   $this->LevelClassModel
                                    ->whereHas('get_periods',function($q){})
                                    ->with(['get_periods'=>function($q){}])
                                    ->with('level_details')
                                    ->whereHas('get_level',function($q){
                                        $q->where('is_active','1');
                                    })
                                    ->where('school_id',Session::get('school_id'))
                                    ->whereIn('academic_year_id',$arr_academic_year)
                                    ->groupBy('level_id')
                                    ->orderBy('position')
                                    ->get();                          
        if($obj_levels){
            $arr_level = $obj_levels->toArray();
        }     

        $class_id           = Session::get('class_id');
        $level_id           = Session::get('level_id');
        $num_of_periods     = Session::get('num_of_periods');
        $weekly_off         = json_decode(Session::get('weekly_off'));
        $school_start_time  = Session::get('school_start_time');
        $school_end_time    = Session::get('school_end_time');
        $level_class_id     = Session::get('level_class_id');
        
        $arr_classes        = [];
        $obj_classes = $this->LevelClassModel
                            ->whereHas('get_periods',function($q){})
                            ->with(['get_periods'=>function($q){}])
                            ->with('class_details')
                            ->where('level_id',$level_id)
                            ->where('school_id',Session::get('school_id'))
                            ->get();
        if($obj_classes)
        {
            $arr_classes = $obj_classes->toArray();
        }

        /*Get teachers from teaching hours table whose allocated hours*/
        $where_arr = [
                        'school_id'         => $this->school_id,
                        'academic_year_id'  => $this->academic_year,
                    ];


        $arr_teachers = [];
        $arr_teachers = $this->get_teaching_hours($class_id,$level_id);
        /*Get Teacher time table information*/


         $cond_arr = [
                        'school_id'        => $this->school_id,
                        'academic_year_id' => $this->academic_year,
                        'level_id'         => $level_id,
                        'class_id'         => $class_id,

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
                                                    ->with(['professor_subjects'])
                                                    ->get();

        $arr_time_table = [];
        if($obj_time_table)
        {
            $arr_time_table = $obj_time_table->toArray();

        }
        
        $arr_holiday =  isset($weekly_off)&&$weekly_off!=''?$weekly_off:config('app.project.default_weekly_off');
        

        /*GET the values  from period Start time end time section */
        $obj_period_timing = $arr_periods_timing = array();
        $obj_period_timing = SchoolPeriodTimingModel::where('school_id',$this->school_id)->where('level_class_id',$level_class_id)->orderBy('period_no','asc')->where('academic_year_id',$this->academic_year)->get();
        if($obj_period_timing){
            $arr_periods_timing = $obj_period_timing->toArray();
        }
        /*GET the values  from period Start time end time section */

        $this->arr_view_data['session_school_id']       = $this->school_id;
        $this->arr_view_data['session_class_id']        = $class_id;
        $this->arr_view_data['session_level_id']        = $level_id;
        $this->arr_view_data['session_level_class_id']  = $level_class_id;
        $this->arr_view_data['arr_periods_timing']      = $arr_periods_timing;
        $this->arr_view_data['session_num_of_periods']  = $num_of_periods;
        $this->arr_view_data['session_weekly_off']      = $weekly_off;
        $this->arr_view_data['session_school_start_time']  = $school_start_time;
        $this->arr_view_data['session_school_end_time']    = $school_end_time;
      
        /*$this->arr_view_data['session_period_duration'] = $period_duration;*/
        $this->arr_view_data['active_academic_year_id'] = $this->academic_year;
        $this->arr_view_data['curr_academic_year_id']   = $this->academic_year;
        $this->arr_view_data['arr_time_table']          = $arr_time_table;
        $this->arr_view_data['arr_classes']     = $arr_classes;
        $this->arr_view_data['arr_level']       = $arr_level;
        $this->arr_view_data['arr_teachers']    = $arr_teachers;
        $this->arr_view_data['weekly_days']     = $this->weekly_days;
        $this->arr_view_data['arr_holiday']     = $arr_holiday;
        $this->arr_view_data['page_title']      = $this->module_title;
        $this->arr_view_data['edit_icon']       = $this->edit_icon;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        
        return view($this->module_view_folder.'.timetable', $this->arr_view_data);
   }
    /*
    | update_period_mapping() : Used to update the periods as per level class 
    | Auther  : Padmashri
    | Date    : 29-05-2018 again updated at 30 May 2018 as per discuss with shankar Sir
    */
    public function update_period_mapping(Request $request)
    {
     
        $arr_rules['num_of_periods']        = "required";
        $arr_rules['school_start_time']     = "required";
        $arr_rules['school_end_time']       = "required";
      
        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        if($request->input('class_id') && $request->input('level_id')) 
        {
            $level_id             = $request->input('level_id');
            $class_id             = $request->input('class_id');
            $school_start_time    = $request->input('school_start_time');
            $school_end_time      = $request->input('school_end_time');
            $weekly_holiday       = json_encode($request->input('weekly_off'),true);

        }else if($request->input('school_start_time') && $request->input('school_end_time') && $request->input('weekly_off') && $request->input('num_of_periods')){

            $level_id             = Session::get('level_id');
            $class_id             = Session::get('class_id');
            $level_class_id       = Session::get('level_class_id');
            $school_start_time    = $request->input('school_start_time');
            $school_end_time      = $request->input('school_end_time');
            $weekly_holiday       = json_encode($request->input('weekly_off'),true);

        }else{
            /*While edit class section */
            $level_id  = Session::get('level_id'); /* Level Id */
            $class_id  = Session::get('class_id'); /* A,B,C */
            $level_class_id       = Session::get('level_class_id');
            $school_start_time    = Session::get('school_start_time');
            $school_end_time      = Session::get('school_end_time');
            $weekly_holiday       = Session::get('weekly_off');
        }

        $num_of_periods         = $request->input('num_of_periods');
        $period_duration        = 0;


        $level_class_id     =  $this->get_level_class_id($level_id,$class_id);

        $arr_periods = [
                            'school_id'        => $this->school_id,
                            'academic_year_id' => $this->academic_year,
                            'level_class_id'   => isset($level_class_id)&&$level_class_id->id!=''?$level_class_id->id:'0',
                            'class_id'          => $class_id,
                            'level_id'          => $level_id,
                            'num_of_periods'    => $num_of_periods,
                            'school_start_time' => $school_start_time,
                            'school_end_time'   => $school_end_time,
                            'weekly_off'        => $weekly_holiday
                        ];

        $cond_arr = [
                        'school_id'         => $this->school_id,
                        'academic_year_id'  => $this->academic_year,
                        'class_id'          => $class_id,
                        'level_id'          => $level_id,
                    ];

        $obj_class_period = $this->SchoolPeriodsModel->where($cond_arr)->first();

        $arr_class_period = [];

        if($obj_class_period)
        {
            $arr_class_period = $obj_class_period->toArray();
        }

        if(isset($arr_class_period) && sizeof($arr_class_period)>0)
        {
                
                /* Need to write the code if he descreses the period time than previous */
                if($num_of_periods < Session::get('num_of_periods'))
                {
                  /* DELETE THE PERIODS GREATER THAN NEWLY SELECTED PERIODS */   
                    $obj_excess_periods = $arr_excess_periods = array();
                    $obj_excess_periods = SchoolTimeTableModel::where($cond_arr)
                                                               ->where('periods_no','>',$num_of_periods)
                                                               ->get();
                    if(isset($obj_excess_periods) && !empty($obj_excess_periods)){
                        $arr_excess_periods = $obj_excess_periods->toArray();
                       
                        foreach($arr_excess_periods as $key => $res_arr_excess_periods){
                                 

                            $professor = $this->SchoolProfessorTeachingHours->where('school_id',$this->school_id)->where('academic_year_id',$this->academic_year)->where('professor_id',$res_arr_excess_periods['professor_id'])->first();
                            if($professor)
                            {
                                $res     =  $professor->decrement('assigned_periods',1);
                                if($res){
                                    $del = SchoolTimeTableModel::where('id',$res_arr_excess_periods['id'])->delete();
                                }
                                

                            }


                        }
                    }
                  /* DELETE THE PERIODS GREATER THAN NEWLY SELECTED PERIODS */   
                }

                $status = $this->SchoolPeriodsModel->where($cond_arr)->update($arr_periods);
                $arr_defined_periods = [];
                $arr_periods_session = [
                                    'class_id'         => $class_id,
                                    'level_id'         => $level_id,
                                    'num_of_periods'   => $num_of_periods,
                                    'school_start_time'=> $school_start_time,
                                    'school_end_time'  => $school_end_time,
                                    'weekly_off'       => $weekly_holiday,
                                    'level_class_id'   => isset($level_class_id)&&$level_class_id->id!=''?$level_class_id->id:'0'
                                ];

                Session::put($arr_periods_session);
        }
        else
        {
            $status = $this->SchoolPeriodsModel->create($arr_periods);

             $arr_periods_session = [
                                    'class_id'         => $class_id,
                                    'level_id'         => $level_id,
                                    'num_of_periods'   => $num_of_periods,
                                    'school_start_time'=> $school_start_time,
                                    'school_end_time'  => $school_end_time,
                                    'weekly_off'       => $weekly_holiday,
                                     'level_class_id'  => isset($level_class_id)&&$level_class_id->id!=''?$level_class_id->id:'0'
                                ];

            Session::put($arr_periods_session);
        }
        
        if(isset($status))
        {

            Flash::success($this->module_title." ".translation("updated_successfully"));
        }
        else
        {
            Flash::error( translation("something_went_wrong_while_updating")." ".$this->module_title);   
        }

        return redirect()->back();
    } 
    

    /*
    | get_teaching_hours() : Used to get the teachers list 
    | Auther  : Padmashri
    | Date    : 29-05-2018 to  30-05-2018
    */
     public function get_teaching_hours($class_id, $level_id)
    {

        /*Q.Why we have not added the academic year upto condition for this?
        Ans: As we are bring only those teacheres who get asigned hours for that perticular academic year,so its not required here. If any teacher is not assigned hours for that academic years then it wont get displayed in teacher list. And the teachers hours are as per week as time table is working as per week.*/


        $arr_teachers_hours = [];
        $arr_teachers = [];
        $SCHOOL_ID    = $this->school_id;
        $obj_teachers_hours = $this->SchoolProfessorTeachingHours->where('school_id',$this->school_id)
                                                        ->whereHas('professor_details',function($q1)use($SCHOOL_ID){
                                                                $q1->where('is_active','=', 1);
                                                                $q1->where('has_left','=', 0);
                                                                $q1->where('school_id','=',$SCHOOL_ID);
                                                        })
                                                        ->with(['user_details' => function ($query)
                                                        {
                                                            $query->select('id','profile_image');
                                                        }])
                                                        ->where('academic_year_id',$this->academic_year)
                                                        ->with(['professor_subjects'=>function($q){
                                                                $q->select('professor_id','school_id','course_id','levels');
                                                        }])
                                                        ->get();

        if($obj_teachers_hours)
        {
            $arr_teachers_hours = $obj_teachers_hours->toArray();
        }
        
        $temp_arr = [];
        foreach ($arr_teachers_hours as $key => $value) {

            $teacher_levelsArr = isset($value['professor_subjects']['levels'])&&$value['professor_subjects']['levels']!=''?json_decode($value['professor_subjects']['levels']):'';
      
            if(!empty($teacher_levelsArr)){
                    if(in_array($level_id, $teacher_levelsArr)){
                        $temp_arr[] = $value;
                    }

            }
        
        }
     


        if(isset($temp_arr) && sizeof($temp_arr)>0)
        {
            foreach ($temp_arr as $teacher_key => $teacher) 
            {   
                 

                if(isset($teacher['professor_subjects']['course_id']) && count($teacher['professor_subjects']['course_id'])>0)
                {
                    /*Get subjects from school subjects*/

                    $cond_arr = [
                                    'school_id'        => $this->school_id,
                                    'academic_year_id' => $this->academic_year,
                                    'class_id'         => $class_id,
                                    'level_id'         => $level_id,
                                ];
                        
                    $obj_class_subjects = $this->SchoolSubjectsModel->where($cond_arr)->get();

                    $arr_class_subject = [];
                    
                    $arr_available_subject = [];

                    if($obj_class_subjects)
                    {
                        $arr_class_subject = $obj_class_subjects->toArray();
                  
                        if(isset($arr_class_subject) && count($arr_class_subject)>0)
                       {    
                            $arr_professor_subjects  = json_decode($teacher['professor_subjects']['course_id'],true); 
                  
                            foreach ($arr_class_subject as $key => $value) 
                            {
                                 $arr_available_subject = array_intersect($arr_professor_subjects,$value['json_subjects']);     
                            }
                        }

                    }

                     $obj_subjects =   CourseModel::whereIn('id',$arr_available_subject)
                                                    ->get();



                     if($obj_subjects)
                     {
                        $arr_teacher_subjects = $obj_subjects->toArray();
                      
                       if(isset($arr_teacher_subjects) && count($arr_teacher_subjects)>0)
                       {
                            foreach ($arr_teacher_subjects as $key => $value) 
                            {
                                 
                                $arr_teacher_subjects[$key]['professor_id']   = isset($teacher['professor_id'])?$teacher['professor_id']:""; 

                                $first_name = $last_name = '';
                                if(isset($teacher['user_details']['first_name']))
                                {
                                    $first_name = ucfirst($teacher['user_details']['first_name']);
                                }
                                
                                if(isset($teacher['user_details']['last_name']))
                                {
                                    $last_name = ucfirst($teacher['user_details']['last_name']);
                                }

                                $arr_teacher_subjects[$key]['teacher_name'] = $first_name.' '.$last_name;
                                $arr_teacher_subjects[$key]['total_periods'] = isset($teacher['total_periods'])?$teacher['total_periods']:"";

                                /*Get remaining hours from total hours - assigned hours*/

                                if(isset($teacher['total_periods']) && $teacher['total_periods']!="")
                                {
                                    $total_periods = $teacher['total_periods'];
                                }

                                if(isset($teacher['assigned_periods']) && $teacher['assigned_periods']!="")
                                {
                                    $assigned_periods = $teacher['assigned_periods'];
                                }
                                else
                                {
                                    $assigned_periods = 0;
                                }

                                if($total_periods>$assigned_periods)
                                {
                                    $remaingin_periods = $total_periods - $assigned_periods;
                                }
                                else
                                {
                                    $remaingin_periods = 0;
                                }
                                

                                $arr_teacher_subjects[$key]['remaingin_periods'] = isset($remaingin_periods)?$remaingin_periods:"0";

                                 if(isset($teacher['user_details']['profile_image']) && $teacher['user_details']['profile_image']!="" && file_exists($this->user_profile_base_img_path.'/'.$teacher['user_details']['profile_image']))
                                {
                                   $teacher_image = $this->user_profile_public_img_path.$teacher['user_details']['profile_image'];
                                }
                                else
                                {
                                    $teacher_image = url('/images/default-profile.png');
                                }

                                $arr_teacher_subjects[$key]['teacher_image'] = $teacher_image;
                                $arr_teacher_subjects[$key]['level_id'] = $level_id;
                                $arr_teacher_subjects[$key]['class_id'] = $class_id;
                            } // end of foreach arr_teacher_subjects
                        }

                        $arr_teachers[] = $arr_teacher_subjects;
                        $arr_teachers = array_filter($arr_teachers); // Remove the key of empty array.

                     }      
                }
            }
        }
        return $arr_teachers;
    }

    /*
    | create_timetable () : Used to assign the time to the teacher. 
    | Auther  : Padmashri
    | Date    : 30-05-2018
    */
    public function create_timetable(Request $request)
    {
        $request_data = $request;
        $response =  $this->create_timetable_record($request_data, $reqest_type = "HTTP_REQUEST");
        return $response;
        /*Get period duration from class id and section id*/
    }


    public function create_timetable_record($request_data,$request_type)
    {
        $obj_update_timetable = '';
         
        
        $class_id   = $request_data['class_id'];
        $level_id = $request_data['level_id'];
        $professor_id = $request_data['professor_id'];
        $subject_id = $request_data['subject_id'];
        $period_num = $request_data['period_num'];
        $period_day = $request_data['period_day'];
        $period_start_time = $request_data['period_start_time'];
        $period_end_time   = $request_data['period_end_time'];

         if(isset($class_id)   && $class_id!=""   &&
           isset($level_id) && $level_id!="" &&
           isset($professor_id) && $professor_id!="" && 
           isset($subject_id) && $subject_id!="" && 
           isset($period_num) && $period_num!="" && 
           isset($period_day) && $period_day!="" &&
           isset($period_start_time) && $period_start_time!='' &&
           isset($period_end_time) && $period_end_time!=''  )
        {
            $cond_arr = [
                            'school_id'         => $this->school_id,
                            'academic_year_id'  => $this->academic_year,
                            'class_id'          => $class_id,
                            'level_id'          => $level_id,
                        ]; 
           

            /*period_already_defined_for_that_professor*/
            $arr_json = [];

            $cond_arr4 = [
                            'school_id'         =>  $this->school_id,
                            'academic_year_id'  =>  $this->academic_year,
                          /*  'class_id'          =>  $class_id,
                            'level_id'          =>  $level_id,*/
                            'day'               =>  strtoupper($period_day),
                            /*'periods_no'        =>  $period_num,*/
                        ];

               
               
            $already_defined_period = $this->SchoolTimeTableModel
                                                ->whereHas('user_details',function($q){ $q->select('id');})
                                                ->with('professor_details','class_details','level_details','user_details')
                                                ->where($cond_arr4)
                 ->whereRaw(" ((TIME('".$period_start_time."') BETWEEN TIME(period_start_time) AND TIME(period_end_time)) OR
 (TIME('".$period_end_time."') BETWEEN TIME(period_start_time) AND TIME(period_end_time)) OR
 (TIME(period_start_time) BETWEEN TIME('".$period_start_time."') AND TIME('".$period_end_time."')) OR
 (TIME(period_end_time) BETWEEN TIME('".$period_start_time."') AND TIME('".$period_end_time."'))) ")
                 // OR  ('".$period_end_time."' BETWEEN `period_start_time` AND  `period_end_time` ) )
                 ->where('professor_id',$professor_id)->orderBy('id','ASC')
                 ->first();
            



          
            if(sizeof($already_defined_period)>0)
            {   

                $arr_already_defined_period = array();
                /*$msg = translation('period_already_defined_for_that_professor');*/
                $status = 'Error';
                $arr_already_defined_period =  $already_defined_period->toArray();
               // dd($arr_already_defined_period);
                if(isset($already_defined_period->user_details) && isset($already_defined_period->class_details) && isset($already_defined_period->level_details))
                {
                    
                

                  
                    $obj_timetable        = $this->SchoolTimeTableModel->where($cond_arr)
                                                                       ->where('class_id','=',$class_id)
                                                                       ->where('level_id','=',$level_id)
                                                                       ->where('professor_id',$professor_id)
                                                                       ->whereRaw("( (TIME('".$period_start_time."') >= TIME(`period_start_time`)   OR  TIME('".$period_end_time."') <= TIME(`period_end_time`) ) AND 
 (TIME('".$period_start_time."') >= TIME(`period_start_time`)   OR  TIME('".$period_end_time."') <= TIME(`period_end_time`) ) )")->where('professor_id',$professor_id)
                                                                       ->first();
                    
                    $first_name   = isset($already_defined_period->user_details->first_name)?$already_defined_period->user_details->first_name:'';
                    $last_name    = isset($already_defined_period->user_details->last_name)?$already_defined_period->user_details->last_name:'';
                    $full_name    = $first_name.' '.$last_name;
                    $full_name    = ($full_name!=' ')?$full_name:'-';
                    $class_name   = isset($already_defined_period->class_details->class_name)?$already_defined_period->class_details->class_name:'-';
                    $section_name = isset($already_defined_period->level_details->level_name)?$already_defined_period->level_details->level_name:'-';
                    $period_no = isset($already_defined_period->periods_no)?$already_defined_period->periods_no:'0';
                    $msg = ucwords($full_name).'  '.translation('period').' '.$period_no.' '.translation('is_overlapping_with').' '.$section_name.' - '.$class_name.' '.translation('period');
                    $status = 'Error';
                }
                $arr_json['status'] = $status;
                $arr_json['msg']    = $msg; 
                // 'teaching staff period <no> is overlapping with <class name> <section name> period ';
                return json_encode($arr_json);
            }   


            $cond_arr2 = [
                            'school_id'         =>  $this->school_id,
                            'academic_year_id'  =>  $this->academic_year,
                            'class_id'          =>  $class_id,
                            'level_id'          =>  $level_id,
                            'day'               =>  strtoupper($period_day),
                            'periods_no'        =>  $period_num,
                        ];


            $cond_arr5 = [
                            'school_id'         =>  $this->school_id,
                            'academic_year_id'  =>  $this->academic_year,
                            'class_id'          =>  $class_id,
                            'level_id'          =>  $level_id,
                            'day'               =>  strtoupper($period_day),
                            'periods_no'        =>  $period_num,
                        ];

            /*Check Period Is already added for any other teacher */
             $already_defined = $this->SchoolTimeTableModel
                                                ->whereHas('user_details',function($q){ $q->select('id');})
                                                ->with('professor_details','class_details','level_details','user_details')
                                                ->where($cond_arr5)
                                                ->orderBy('id','ASC')
                                                ->first();
                
            if(sizeof($already_defined)>0)
            {   
                /*$msg = translation('period_already_defined_for_that_class');*/
                $status = 'Error';
                $arr_already_defined = $already_defined->toArray();
                if(isset($already_defined->user_details) && isset($already_defined->class_details) && isset($already_defined->level_details))
                {
                    $period_no = isset($already_defined->periods_no)?$already_defined->periods_no:'0';
                    $msg = translation('period').'  '.$period_no.' '.translation('is_already_assigned_to_other_professor');
                    $status = 'Error';
                }
                $arr_json['status'] = $status;
                $arr_json['msg']    = $msg; 
                return json_encode($arr_json);
            } 
            /*Check Period Is already added for any other teacher */





            
            $cond_arr = [
                                'school_id'         => $this->school_id,
                                'academic_year_id'  => $this->academic_year,
                                'professor_id'        => $professor_id,
                        ];

            
            $obj_assign_periods = $this->SchoolProfessorTeachingHours->where($cond_arr)->first();
             /*dump($obj_assign_periods->toArray());*/
            if(!empty($obj_assign_periods))
            {
                $assigned_periods  =  $obj_assign_periods->assigned_periods;

                $total_periods     =  $obj_assign_periods->total_periods;

                /* dd($total_periods,$assigned_periods,1);*/
                 if($total_periods>$assigned_periods)
                {
                    $remain_minutes = 0;

                    $remain_minutes = $total_periods - $assigned_periods;
                   
                    /*While last remainig hours not match with period duration then teacher will not define period*/

                    if(isset($remain_minutes)  && $remain_minutes >= 0)
                    {
                       

                        $level_class_id     =  $this->get_level_class_id($level_id,$class_id);
                        
                        $level = $this->LevelModel->where('id',$level_id)->first();

                        $arr_create = [
                                    'school_id'         =>  $this->school_id,
                                    'academic_year_id'  =>  $this->academic_year,
                                    'level_class_id'    =>  isset($level_class_id)&&$level_class_id->id!=''?$level_class_id->id:'0',
                                    'class_id'          =>  $class_id,
                                    'level_id'          =>  $level_id,
                                    'professor_id'      =>  $professor_id,
                                    'course_id'         =>  $subject_id,
                                    'day'               =>  strtoupper($period_day),
                                    'periods_no'        =>  $period_num,
                                    'level_order'       => $level->level_order,
                                    'period_start_time' => $period_start_time,
                                    'period_end_time'   => $period_end_time
                                ];            
                           
                        $status = $this->SchoolTimeTableModel->create($arr_create);

                        if($status)
                        {
                            /*Update the users assigned periods count  */

                            

                            $assigned_periods  = $assigned_periods + 1;
                            if($assigned_periods<=$total_periods)
                            {
                                $this->SchoolProfessorTeachingHours->where($cond_arr)
                                                            ->update(['assigned_periods'=>$assigned_periods]);
                            }                        

                            
                            $remaining_periods = $total_periods - $assigned_periods;
                            $arr_json['status']             = 'Success';
                            $arr_json['remaining_periods']  = $remaining_periods;
                            $arr_json['msg']                = translation('selected_professor_has_period_assign_successfully'); 
                            
                        }
                    }
                    else
                    {
                        $arr_json['status']      = 'Error';
                        $arr_json['msg']         = 
                        translation('selected_teacher_has_not_required_remaining_hours_to_assign');
                    }
                } 
                else
                {
                    $arr_json['status']      = 'Error';
                    $arr_json['msg']         = translation('selected_teacher_has_not_required_remaining_hours_to_assign');
                }
            }
           
            if($request_type == "HTTP_REQUEST")
            {
                return json_encode($arr_json);
            }
            return $arr_json;
        }
    }

    /*
    | delete_period_teacher () : Used to delete the teachers period time. 
    | Auther  : Padmashri
    | Date    : 30-05-2018
    */
    public function delete_period_teacher(Request $request)
    {
        $class_id           = Session::get('class_id');
        $level_id         = Session::get('level_id');

        $professor_id = $request->input('professor_id');
        $period_num = $request->input('period_num');
        $day        = $request->input('day');

        if(isset($professor_id) && $professor_id!='' && 
           isset($period_num) && $period_num!='' &&
           isset($day)        && $day!='')
        {
            $cond_arr = [
                            'school_id'         => $this->school_id,
                            'academic_year_id'  => $this->academic_year,
                            'class_id'          => $class_id,
                            'level_id'          => $level_id,
                            'professor_id'      => $professor_id,
                            'periods_no'        => $period_num,
                            'day'               => $day
                        ];
            $obj_time_table = $this->SchoolTimeTableModel->where($cond_arr)
                                                        ->select('id','professor_id','class_id','level_id')
                                                        ->with('teaching_hours')
                                                        ->first();
                                                        

            $arr_time_table = $arr_json_data = [];

            if($obj_time_table)
            {
                $arr_time_table =  $obj_time_table->toArray();
                 
                if(isset($arr_time_table) && sizeof($arr_time_table)>0)
                {
                    

                    $total_assigned_periods = isset($arr_time_table['teaching_hours']['assigned_periods'])?$arr_time_table['teaching_hours']['assigned_periods']:"";

                     

                  /*$final_remaining_hours = $this->set_previous_remaining_hours($total_time_in_seconds,$total_assigned_periods); */

                    
                        $deleting_status = $this->SchoolTimeTableModel->where($cond_arr)->delete();
                        
                        if($deleting_status)
                        {
                            $where_arr = [
                                            'school_id'         => $this->school_id,
                                            'academic_year_id'  => $this->academic_year,
                                            'professor_id'     => $professor_id
                                         ];
                            $final_assigned_period = $total_assigned_periods-1;
                            $status = $this->SchoolProfessorTeachingHours->where($where_arr)
                                                            ->update([
                                                                        'assigned_periods'=>$final_assigned_period
                                                                    ]);
                           

                            if($status)
                            {
                                $arr_json_data['status'] = "Success";
                                $arr_json_data['msg']    = translation('assigned_teacher_period_deleted_successfully');
                            }
                            else
                            {
                                $arr_json_data['status'] = "Error";
                                $arr_json_data['msg']    = translation('error_while_assigned_teacher_period_deleting');
                            }
                        }
                        else
                        {
                            $arr_json_data['status'] = "Error";
                            $arr_json_data['msg']    = translation('problem_occured_while_assigned_teacher_period_deleting');
                        }                        
                    
                }
            }
        }

        return response()->json($arr_json_data);
    }
    
     /*
    | get_period_details () : Used to get the no of period if its already added 
    | Auther  : Padmashri
    | Date    : 31-05-2018
    */
    public function get_period_details(Request $request)
    {   
        $class_id = $request->input('class_id');

        $level_id = $request->input('level_id');

        if($class_id !="" && $class_id != null && $level_id != "" && $level_id !=null)
        {

            $obj_school_period = $this->SchoolPeriodsModel
                                                ->select('level_id','num_of_periods','weekly_off','school_start_time','school_end_time','level_class_id')
                                                ->where([
                                                            'academic_year_id' => $this->academic_year,
                                                            'class_id'         => $class_id,
                                                            'level_id'         => $level_id,
                                                            'school_id'        => $this->school_id
                                                        ])
                                                ->first();
            $arr_school_period = [];

            if($obj_school_period)
            {
               $arr_school_period =  $obj_school_period->toArray();
               
               if(isset($arr_school_period) && count($arr_school_period)>0)
               {

                    $arr_periods_session = [
                                            'class_id'         => $class_id,
                                            'level_id'         => $level_id,
                                            'num_of_periods'   => $arr_school_period['num_of_periods'],
                                            'weekly_off'       => $arr_school_period['weekly_off'],
                                            'school_start_time'=> $arr_school_period['school_start_time'],
                                            'school_end_time'  => $arr_school_period['school_end_time'],
                                            'level_class_id'   => $arr_school_period['level_class_id']
                                          
                                        ];

                     Session::put($arr_periods_session);
               }
            }
        }

        return response()->json($arr_school_period);
    }

     /*
    | get_teacher_lecture_info () : Used to get the professor lecture information
    | Auther  : Padmashri
    | Date    : 31-05-2018
    */
    public function get_teacher_lecture_info($arr_teachers)
    {
        $obj_subject_wise_timetable  = $obj_timetable = '';
        $arr_teacher_info = $arr_teacher_timetable = [];
        if(isset($arr_teachers) && sizeof($arr_teachers)>0)
        {   
            
            foreach ($arr_teachers as $key => $value) 
            {
                
               $arr_teacher_info[$key]['subjects']        =  $this->get_teacher_subjects(base64_encode(
                $value->professor_id));
               if(isset($arr_teacher_info[$key]['subjects']) && sizeof($arr_teacher_info[$key]['subjects'])>0)
               {
                    foreach ($arr_teacher_info[$key]['subjects'] as $i => $subject)
                    {
                        if(isset($subject['id']) && $subject['id']!='')
                        {
  
                          $obj_subject_wise_timetable   = $this->SchoolTimeTableModel->where('school_id',$this->school_id)
                                                                                     ->where('academic_year_id',$this->academic_year)
                                                                                     ->where('professor_id',$value->professor_id)->where('course_id','=',$subject['id'])
                                                                                     ->with(['professor_subjects','class_details','level_details'])
                                                                                   ->get();
                          if($obj_subject_wise_timetable) 
                          {
                             $arr_teacher_timetable[$subject['id']] = $obj_subject_wise_timetable->toArray(); 
                          }     
                        }
                        
                    }
               }
                   
               $arr_teacher_info[$key]['professor_id']       = isset($value->professor_id)?$value->professor_id:'';
               $arr_teacher_info[$key]['teacher_timetable']  = $arr_teacher_timetable;
               $arr_teacher_info[$key]['teacher_name']       = $value->user_name;
               $arr_teacher_info[$key]['total_periods']      = $value->total_periods;
               $arr_teacher_info[$key]['assigned_periods']   = $value->assigned_periods;

            }
        }
        
        return $arr_teacher_info;
       
    }
    /*
    | get_teacher_subjects () : Used to get the professor lecture information
    | Auther  : Padmashri
    | Date    : 31-05-2018
    */
    public function get_teacher_subjects($enc_teacher_id, $is_ajax = FALSE)
    {
        $teacher_id                 = base64_decode($enc_teacher_id);
        $obj_techer_subjects        =  $this->ProfessorCoursesmodel->where('professor_id',$teacher_id)->where('school_id',$this->school_id)->first();
        $arr_teacher_subjects       = [];

        if($obj_techer_subjects)
        {
            $arr_techer_subjects = $obj_techer_subjects->toArray();
            if(count($arr_techer_subjects) > 0)
            {
                $arr_subjects = [];

            
                $arr_json_subjects = $arr_techer_subjects['course_id'];
                if($arr_json_subjects != "" && !is_array($arr_json_subjects))
                {
                    $arr_subjects = json_decode($arr_json_subjects);
                }
                else
                {
                    $arr_subjects = $arr_json_subjects;
                }
                
                if(is_array($arr_subjects) && count($arr_subjects) > 0)
                {
                    $obj_subject = CourseModel::whereIn('id',$arr_subjects)->get();
                    if($obj_subject)
                    {
                        $arr_teacher_subjects = $obj_subject->toArray();

                    }
                }
            }
        }
         
        if(isset($is_ajax) && $is_ajax == "AJAX")
        {
            $data['techer_subjects'] = $arr_teacher_subjects;
            return response()->json($data);
        }
        return $arr_teacher_subjects;
    }

    /*
    | index()       : Load the timetable summary 
    | Auther        : Padmashri Joshi
    | Date          : 6 Jun 2018
    */
    public function timetable_summery(Request $request){

        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 
        $arr_academic_year = explode(',',$academic_year);  

        $obj_level = $arr_level = [];
        $obj_level  =   $this->CommonDataService->get_levels($this->academic_year);
        
        if($obj_level){
            $arr_level = $obj_level->toArray();
        }   
        
        $obj_class = $arr_class = [];
        $obj_class = LevelClassModel::whereHas('get_class',function($q){ $q->where('is_active','=',1); })->with(['get_class'=>function($q){ $q->where('is_active','=',1); }])->where('school_id',$this->school_id)->whereIn('academic_year_id',$arr_academic_year)->groupBy('class_id')->get();
         $arr_class = $obj_class->toArray();

         $summery_data = array();
         foreach($arr_level as $arr_levels){
            
            foreach($arr_class as $arr_classes){
                $isAddedTimeTable = SchoolPeriodsModel::where('level_id',$arr_levels['level_id'])
                                                          ->where('class_id',$arr_classes['class_id'])
                                                          ->where('academic_year_id',$this->academic_year)
                                                          ->count();
                $temp=[];
                $temp['level_id'] = $arr_levels['level_id'];
                $temp['class_id'] = $arr_classes['class_id'];
                $temp['is_created'] = $isAddedTimeTable;
                $temp['level_name'] = !empty($arr_levels['level_details'])&&isset($arr_levels['level_details']['level_name'])?$arr_levels['level_details']['level_name']:'';
                 
                array_push($summery_data,$temp);  
            }
         }
      
         /* BRING TEACHERES THOSE WERE HAVING PERIODS ASSIGNED */
         $obj_teachers = $arr_teachers = array();
         $obj_teachers = $this->SchoolProfessorTeachingHours->whereHas('user_details',function($q){ $q->select('id');})->whereHas('get_professor_timetable',function($q){ $q->select('professor_id'); })->with(['user_details'=>function($q){ $q->select('id');}])->where('school_id',$this->school_id)->where('academic_year_id',$this->academic_year)->groupBy('professor_id')->get();
         if(!empty($obj_teachers))
         {
            $arr_teachers = $obj_teachers->toArray();
         }
       
         
        $this->arr_view_data['arr_level']      = $arr_level;
        $this->arr_view_data['arr_class']      = $arr_class;
        $this->arr_view_data['summery_data']   = $summery_data;
        $this->arr_view_data['arr_teachers']   = $arr_teachers;
        

        
        $this->arr_view_data['edit_icon']       = $this->edit_icon;
        $this->arr_view_data['module_title']    = translation('summary');
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.summery', $this->arr_view_data);
    }


    public function get_level_class_id($level_id,$class_id){
        $objdata = LevelClassModel::select('id','level_id','class_id')->where('level_id',$level_id)->where('class_id',$class_id)->where('school_id',$this->school_id)->first(); 
        return $objdata;
    }

     /*
    | index()       : Add timetable periods 
    | Auther        : Padmashri Joshi
    | Date          : 18 July 2018
    */
    public function store_period_timimg(Request $request)
    {
        $class_id         = Session::get('class_id');
        $level_id         = Session::get('level_id');
        $level_class_id   = Session::get('level_class_id');

        $school_start_time   = Session::get('school_start_time');
        $school_end_time     = Session::get('school_end_time');

        $period_end_time  = $request->input('period_end_time');
        $period_start_time = $request->input('period_start_time');
        if(!empty($period_start_time) && !empty($period_end_time) ){
            
            $countForBreak = 1;
            $cond_arr = [
                        'school_id'         => $this->school_id,
                        'academic_year_id'  => $this->academic_year,
                        'class_id'          => $class_id,
                        'level_id'          => $level_id,
                        'level_class_id'    => $level_class_id
                    ];

            $obj_class_period = $this->SchoolPeriodsModel->where($cond_arr)->first();
            $arr_class_period = [];
            if($obj_class_period)
            {
                $arr_class_period = $obj_class_period->toArray();
            }

            /* Validate the school period timings */
            $con_school_start_time = isset($school_start_time)&&$school_start_time!='00:00'?strtotime($school_start_time):'';
            $con_school_end_time = isset($school_end_time)&&$school_end_time!='00:00'?strtotime($school_end_time):'';
            for($i=0;$i<count($period_start_time);$i++){
                if((isset($period_start_time[$i]) && $period_start_time[$i]!='' && $period_start_time[$i]!='00:00') && (isset($period_end_time[$i]) && $period_end_time[$i]!='' && $period_end_time[$i]!='00:00') ){
                    
                    $flag2 = $flag = 0; /* no error */
                    $conv_period_start_time = strtotime($period_start_time[$i]);
                    $conv_period_end_time = strtotime($period_end_time[$i]);
                    /*$msg = '';*/
                    /* For Start Time*/
                    if($conv_period_start_time < $con_school_start_time){
                        $flag = 1;
                        /*$msg  = '11';*/
                    }elseif($conv_period_start_time > $con_school_end_time){
                        $flag = 1;
                        /*$msg  = '12';*/
                    }elseif($conv_period_start_time > $conv_period_end_time){
                        $flag = 1;
                        /*$msg  = '13';*/
                    }else{
                        /// Check start timimgs
                        for($p=0;$p<count($period_start_time);$p++){
                                if($p!=$i && ($conv_period_start_time == strtotime($period_start_time[$p]))){
                                    $flag = 1;
                                    /*$msg  = '14';*/
                                }    
                        }
                    }
                    
                    if($flag == 1){
                        Flash::error(translation('invalid_period_timimgs'));
                        return redirect()->back();
                    }

                    /* For Start Time*/

                    /* For End Time*/
                    if($conv_period_end_time < $con_school_start_time){
                        $flag2 = 1;
                        /*$msg = '21';*/
                    }elseif($conv_period_end_time > $con_school_end_time){
                        $flag2 = 1;
                        /*$msg = '22';*/
                    }elseif($conv_period_end_time < $conv_period_start_time){
                        $flag2 = 1;
                        /*$msg = '23';*/
                    }else{
                        for($p2=0;$p2<count($period_end_time);$p2++){
                                if($p2!=$i && ($conv_period_end_time == strtotime($period_end_time[$p2]))){
                                    $flag2 = 1;
                                    /*$msg = '24';*/
                                }    
                        }
                    }
                    if($flag2 == 1){
                        Flash::error(translation('invalid_period_timimgs'));
                        return redirect()->back();
                    }
                    /* For End Time*/


                }else{ 
                    Flash::error(translation('period_timings_can_not_be_null'));
                    return redirect()->back();
                }
            }

            /* Validate the school period timings */


            for($i=0;$i<count($period_start_time);$i++){
            
                $is_break = !empty($request->input('is_break_'.$countForBreak))&&$request->input('is_break_'.$countForBreak)!=''?$request->input('is_break_'.$countForBreak):0;
                  $arr_periods_timimg = [
                            'school_period_id' => isset($arr_class_period['id'])?$arr_class_period['id']:'0',
                            'school_id'        => $this->school_id,
                            'academic_year_id' => $this->academic_year,
                            'level_class_id'   => isset($level_class_id)&&$level_class_id!=''?$level_class_id:'0',

                            'period_no'         => $countForBreak,
                            'period_start_time' => $period_start_time[$i],
                            'period_end_time'   => $period_end_time[$i],
                            'is_break'          => $is_break
                        ];

                 $countForBreak++;            
                 
                 $res = SchoolPeriodTimingModel::create($arr_periods_timimg);


            }

             if($res){
                    Flash::success(translation('periods').' '.translation('created_successfully'));
                    return redirect()->back();           
                 }else{
                    Flash::error(translation('please_select_period_start_time_and_end_time'));
                    return redirect()->back();   
                 }

        }else{
            Flash::error(translation('please_select_period_start_time_and_end_time'));
            return redirect()->back();   
        }
    }

      /*
    | index()       : Delete timetable periods 
    | Auther        : Padmashri Joshi
    | Date          : 18 July 2018
    */
    public function delete_time_table(){
        if(Session::get('level_class_id')!='' &&  Session::get('level_id')!='' ){

            $class_id           = Session::get('class_id');
            $level_id           = Session::get('level_id');
            $num_of_periods     = Session::get('num_of_periods');
            $school_start_time  = Session::get('school_start_time');
            $school_end_time    = Session::get('school_end_time');
            $level_class_id     = Session::get('level_class_id');

            $cond_arr = [
                            'school_id'         => $this->school_id,
                            'academic_year_id'  => $this->academic_year,
                            'level_class_id'    => $level_class_id,
                        ]; 
            
            /*delete from tbl_school_periods */
            $obj_empty_data = $obj_data = $arr_data = array();
            $obj_data = SchoolTimeTableModel::where($cond_arr)->get();
            if(!empty($obj_data)){
                $arr_data = $obj_data->toArray();
            }

            foreach ($arr_data as $key => $value) {
                
                /*delete from tbl_school_periods */
                $professor_id = $value['professor_id'];
                $deleteTT = SchoolTimeTableModel::where('id',$value['id'])->delete();
                if($deleteTT){
                   $res =   SchoolProfessorTeachingHours::where('professor_id',$professor_id)
                                                        ->where('academic_year_id',$this->academic_year)
                                                        ->where('school_id',$this->school_id)
                                                        ->decrement('assigned_periods',1);
                }
                /*delete from tbl_school_periods */
            }

             
            /* delete the school period timimg */
            $delTimimg = SchoolPeriodTimingModel::where($cond_arr)->delete();
            /* delete the school period timimg */

            /*delete from tbl_school_time_table*/
            $delTimeTable = SchoolPeriodsModel::where($cond_arr)->delete();
            /*delete from tbl_school_time_table*/
                

            Session::forget('class_id');                    
            Session::forget('level_id');                    
            Session::forget('num_of_periods');                    
            Session::forget('school_start_time');                    
            Session::forget('school_end_time');                    
            Session::forget('level_class_id');                    



            Flash::success(translation('timetable_deleted_successfully'));
             return redirect()->back();   
        }else{
            Flash::error(translation('oopssomething_went_wrong'));
              return redirect()->back();   
            
        }
    }

    public function export(Request $request){
            $class_id           = Session::get('class_id');
            $level_id           = Session::get('level_id');
            $num_of_periods     = Session::get('num_of_periods');
            $weekly_off         = json_decode(Session::get('weekly_off'));
            $school_start_time  = Session::get('school_start_time');
            $school_end_time    = Session::get('school_end_time');
            $level_class_id     = Session::get('level_class_id');
            
            $arr_classes        = [];
            $obj_classes = $this->CommonDataService->get_class($level_id);
            if($obj_classes)
            {
                $arr_classes = $obj_classes->toArray();
            }

            /*Get teachers from teaching hours table whose allocated hours*/
            $where_arr = [
                            'school_id'         => $this->school_id,
                            'academic_year_id'  => $this->academic_year,
                        ];


            $arr_teachers = [];
            $arr_teachers = $this->get_teaching_hours($class_id,$level_id);
            /*Get Teacher time table information*/


             $cond_arr = [
                            'school_id'        => $this->school_id,
                            'academic_year_id' => $this->academic_year,
                            'level_id'         => $level_id,
                            'class_id'         => $class_id,

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
                                                        ->with(['professor_subjects'])
                                                        ->get();

            $arr_time_table = [];
            if($obj_time_table)
            {
                $arr_time_table = $obj_time_table->toArray();

            }
            
            $arr_holiday =  isset($weekly_off)&&$weekly_off!=''?$weekly_off:config('app.project.default_weekly_off');
            

            /*GET the values  from period Start time end time section */
            $obj_period_timing = $arr_periods_timing = array();
            $obj_period_timing = SchoolPeriodTimingModel::where('school_id',$this->school_id)->where('level_class_id',$level_class_id)->orderBy('period_no','asc')->where('academic_year_id',$this->academic_year)->get();
            if($obj_period_timing){
                $arr_periods_timing = $obj_period_timing->toArray();
            }
            /*GET the values  from period Start time end time section */
            if($request->file_format == 'csv'){
                \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel) use($num_of_periods,$weekly_off,$arr_periods_timing,$arr_time_table,$arr_holiday)
                    {
                        $excel->sheet(ucwords($this->module_title), function($sheet) use($num_of_periods,$weekly_off,$arr_periods_timing,$arr_time_table,$arr_holiday)
                        {

                            if(count($this->weekly_days) >0){
                                $arr_fields['period']=translation("period");
                                
                                foreach($this->weekly_days as $day => $day_full_name){

                                    $arr_fields[$day] = translation(strtolower($day));
                                }
                            }

                            $sheet->row(2, ['',ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                            $sheet->row(4, $arr_fields);
                            $sheet->setHeight(4,50);
                    
                            $countPeriodTimimgArray  = 0;
                            $period_start_time = $period_end_time = '00:00:00'; 
                            $record_count=0;
                            if(isset($num_of_periods) && $num_of_periods!=""){
                                for($i=1; $i<=$num_of_periods; $i++){
                                    if($arr_periods_timing[$countPeriodTimimgArray]['period_no'] == $i){
                                        $period_start_time = $arr_periods_timing[$countPeriodTimimgArray]['period_start_time'];
                                        $period_end_time = $arr_periods_timing[$countPeriodTimimgArray]['period_end_time'];
                                    }
                                    $isBreak = $arr_periods_timing[$countPeriodTimimgArray]['is_break'];
                                    ++$countPeriodTimimgArray;
                                    $arr_tmp[$record_count]['period'] =translation("period")." ".$i." \n ".getTimeFormat($period_start_time)." - ".getTimeFormat($period_end_time);
                                    
                                    if(count($this->weekly_days) >0){
                                        foreach($this->weekly_days as $day => $day_full_name){
                                            $arr_tmp[$record_count][$day] = '-';

                                            if(isset($arr_holiday) && in_array($day,$arr_holiday)){
                                                if($i==1){
                                                    $arr_tmp[$record_count][$day] = translation("holiday");
                                                }     
                                            }
                                            elseif($isBreak == 1){
                                                $arr_tmp[$record_count][$day] = translation("break");
                                            }
                                            else{
                                                if(isset($arr_time_table) && sizeof($arr_time_table)>0) {
                                                    foreach($arr_time_table as $key => $timetable){
                                                        if(isset($timetable['day']) && $timetable['day']==strtoupper($day) && isset($timetable['periods_no']) && $timetable['periods_no']==$i){
                                                            $middle_name = $first_name  = $last_name  ='';
                                                            if(isset($timetable['user_details']['first_name']) && $timetable['user_details']['first_name']!="")
                                                            {
                                                              $first_name = ucfirst($timetable['user_details']['first_name']);
                                                            
                                                            }
                                                            
                                                            if(isset($timetable['user_details']['last_name']) && $timetable['user_details']['last_name']!="")
                                                            {
                                                              $last_name = ucfirst($timetable['user_details']['last_name']);
                                                            
                                                            }
                                                            if(isset($timetable['professor_subjects']['course_name']) && $timetable['professor_subjects']['course_name']!="")
                                                            {
                                                              $subject_name = $timetable['professor_subjects']['course_name'];
                                                            }
                                                            else
                                                            {
                                                              $subject_name = "NA";
                                                            }
                                                            $arr_tmp[$record_count][$day] = $first_name.' '.$last_name." \n  ( ".$subject_name." )";
                                                        }       
                                                    }
                                                }    
                                            }
                                        }   
                                    }

                                    $record_count++;
                                }
                                $sheet->rows($arr_tmp);
                            }
                        });
                    })->export('csv');     
            }
            
            if($request->file_format == 'pdf')
            {

                $this->arr_view_data['session_school_id']       = $this->school_id;
                $this->arr_view_data['session_class_id']        = $class_id;
                $this->arr_view_data['session_level_id']        = $level_id;
                $this->arr_view_data['session_level_class_id']  = $level_class_id;
                $this->arr_view_data['arr_periods_timing']      = $arr_periods_timing;
                $this->arr_view_data['session_num_of_periods']  = $num_of_periods;
                $this->arr_view_data['session_weekly_off']      = $weekly_off;
                $this->arr_view_data['arr_time_table']          = $arr_time_table;
                $this->arr_view_data['arr_classes']     = $arr_classes;
                $this->arr_view_data['arr_teachers']    = $arr_teachers;
                $this->arr_view_data['weekly_days']     = $this->weekly_days;
                $this->arr_view_data['arr_holiday']     = $arr_holiday;

                $school_name = $this->CommonDataService->get_school_name();

                $school_address = $this->CommonDataService->get_school_address();

                $school_email = $this->CommonDataService->get_school_email();

                $school_logo = $this->CommonDataService->get_school_logo();

                
                $this->arr_view_data['school_name']   = $school_name;    
                $this->arr_view_data['school_address']= $school_address;
                $this->arr_view_data['school_email']  = $school_email;
                $this->arr_view_data['school_logo']   = $school_logo;

                $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
                return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
            }
    }

    public function get_class(Request $request)
    {

        $status = 'fail';
        $rules = array(
            'level_id'    => 'required',
        );
        $messages = array(
            'level_id.required'     => 'Please select tag.',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        
        if($validator->fails())
        {
            return json_encode([
                'errors' => $validator->errors()->getMessages(),
                'code' => 422,
                'status' => 'fail',
            ]);
        }
        else
        {   
            $dbValues = $finalValue = '';
            $arr_class = $obj_class = array();
            $level_id = trim($request->input('level_id'));
            $obj_class = $this->LevelClassModel
                            ->with('class_details')
                            ->where('level_id',$level_id)
                            ->where('school_id',Session::get('school_id'));

                            if($request->type=='edit'){
                               $obj_class ->whereHas('get_periods',function($q){});
                               $obj_class ->with(['get_periods'=>function($q){}]);    
                            }
                            else{
                                $obj_class ->whereDoesntHave('get_periods',function($q){});
                                $obj_class ->with(['get_periods'=>function($q){}]);
                            }
            $obj_class = $obj_class ->get();

            if($obj_class){
                $arr_class = $obj_class->toArray();
                
                    if(!empty($arr_class)){
                        $status    = 'done';
                        foreach ($arr_class as $row) {
                            $dbValues .= '{"id":"'.$row['class_id'].'","name":"'.ucwords(strtolower($row['class_details']['class_name'])).'"},';
                        }
                        $dbValues   = substr($dbValues, 0, -1);
                        $userMsg    = 'Done';
                        $finalValue = "[$dbValues]";
                    }
                    else{
                        $userMsg = 'Not Found';
                    }
                }
            }
            $resp = array('status' => $status,'message'=>$userMsg,'errors' => '','categories' => $finalValue);
            return response()->json($resp);
        }
}
