<?php
namespace App\Common\Services;

use App\Models\ModulesModel;
use App\Models\RoleModel;
use App\Models\UserRoleModel;
use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\LevelModel;

use App\Models\SiteSettingModel;
use App\Models\ModuleUserModel;
use App\Models\AcademicYearModel;
use App\Models\LevelClassModel;
use App\Models\ProfessorModel;
use App\Models\SurveyModel;   
use App\Models\NotificationSettingsModel;   
use App\Models\CourseModel;
use App\Models\CourseTranslationModel;
use App\Models\SchoolCourseModel;
use App\Models\ProfessorCoursesmodel;
use App\Models\FeesModel;
use App\Models\SchoolProfileModel;
use App\Models\SchoolParentModel;
use App\Models\SchoolTemplateModel;
use App\Models\SchoolTemplateTranslationModel;
use App\Models\SchoolSmsTemplateModel;
use App\Models\EmployeeModel;
use App\Models\StudentModel;
use App\Models\SchoolTimeTableModel;
use App\Models\SurveyQuestionsAnswerModel;
use App\Models\QuestionCategoryModel;
use App\Models\NewsModel;
use App\Models\SchoolSubjectsModel;
use App\Models\SchoolRoleModel;


use Session;
use DB;
class CommonDataService
{
    public function __construct(
                
                                    UserRoleModel         $user_roles,
                                    UserModel             $user,
                                    SiteSettingModel      $site_setting,
                                    ModuleUserModel       $module_user,
                                    AcademicYearModel     $academic_year,
                                    LevelClassModel       $level_class,
                                    UserTranslationModel  $user_translation_model,
                                    ProfessorModel        $professor_model,
                                    LevelModel            $LevelModel,
                                    SchoolCourseModel     $school_course,
                                    CourseModel           $CourseModel,
                                    ProfessorCoursesmodel $ProfessorCoursesmodel,
                                    SchoolSubjectsModel   $SchoolSubjectsModel,
                                    SchoolTemplateTranslationModel $SchoolTemplateTranslationModel,
                                    NotificationSettingsModel $NotificationSettingsModel
                                )
    {
        $this->UserModel             = $user;
        $this->UserRoleModel         = $user_roles;
        $this->SiteSettingModel      = $site_setting;
        $this->ModuleUserModel       = $module_user;
        $this->AcademicYearModel     = $academic_year;
        $this->LevelClassModel       = $level_class;
        $this->UserTranslationModel  = $user_translation_model;
        $this->ProfessorModel        = $professor_model;
        $this->LevelModel            = $LevelModel;
        $this->SchoolCourseModel     = $school_course;
        $this->CourseModel           = $CourseModel;
        $this->ProfessorCoursesmodel = $ProfessorCoursesmodel;
        $this->NotificationSettingsModel = $NotificationSettingsModel;
        $this->StudentModel          = new StudentModel();
        $this->FeesModel             = new FeesModel();
        $this->SchoolProfileModel    = new SchoolProfileModel();
        $this->SchoolTemplateModel   = new SchoolTemplateModel();
        $this->EmployeeModel         = new EmployeeModel();
        $this->SchoolTimeTableModel  = new SchoolTimeTableModel();
        $this->SurveyModel           = new SurveyModel();
        $this->SchoolSubjectsModel   = new SchoolSubjectsModel();
        $this->SchoolTemplateTranslationModel = new SchoolTemplateTranslationModel();
        $this->SchoolSmsTemplateModel= new SchoolSmsTemplateModel();
         /*Local Section*/

        $this->user_profile_base_img_path     = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path   = url('/').config('app.project.img_path.user_profile_images');

        if(Session::has('locale'))
        {
            $this->locale = Session::get('locale');
        }
        else
        {
            $this->locale = 'en';
        }

        $this->school_id            =  \Session::has('school_id') ? \Session::get('school_id') : '0';
        $this->academic_year        =  \Session::has('academic_year') ? \Session::get('academic_year') : '0'; 
        /*Local Section*/
    }
    
    /********** Get admin ID from database ********/
    public function get_admin_id()
    {
        $admin_id = 0;

        $obj_admin = $this->UserModel
                        ->select('id')
                        ->whereHas('roles',function($query){
                            $query->where('slug','admin');
                        })
                        ->first();
        if(isset($obj_admin->id) && $obj_admin->id!=0)
        {
            $admin_id = $obj_admin->id;
        }
        return $admin_id;
    }
    /************ encrypt values while sending values from one page to another ************/
    public function encrypt_value($value)
    {
        $encrypted = encrypt($value);
        return $encrypted;
    }

    public function decrypt_value($value)
    {
        $decrypted = decrypt($value);
        return $decrypted;
    }
    
    /***************** Assigning module permissions to admin  ******************/
    public function assign_module_permission_to_admin($role)
    {
        $obj_modules    =   $this->ModuleUserModel
                            ->whereHas('get_modules',function($q){
                                $q->where('is_active','1');
                            })
                            ->whereHas('get_role',function($q)use($role){
                                $q->where('slug',$role);
                            })
                            ->with('get_role','get_modules')
                            ->where('is_active',1)
                            ->get();
        
        if($obj_modules != FALSE)
        {
            $arr_modules = $obj_modules->toArray();
        }
      
        $arr_permission = [];

        if (count($arr_modules) > 0)
        {
            foreach ($arr_modules as $submodule) 
            {
                $arr_permission[$submodule['get_modules']['slug'].'.list'] = true;
                $arr_permission[$submodule['get_modules']['slug'].'.create'] = true;
                $arr_permission[$submodule['get_modules']['slug'].'.update'] = true;
                $arr_permission[$submodule['get_modules']['slug'].'.delete'] = true;
            }     
        }
       
        RoleModel::where('slug',$role)->update(['permissions'=>json_encode($arr_permission)]);
   
    }
    public function get_current_academic_id()
    {

        $current_year = date('Y');
        $previous_year = $current_year - 1;
        $next_year = $current_year + 1;
        $previous_acdemic_year = $previous_year.'-'.$current_year;
        $next_acdemic_year = $current_year.'-'.$next_year;
        $academic_year_id = 0; 
        
        $obj = AcademicYearModel::
                                 where('school_id',Session::get('school_id'))
                                ->where('academic_year',$next_acdemic_year)
                                ->first();

        if(isset($obj->id) && count($obj->id)>0)
        {
            
            $academic_year_id = $obj->id;
        }
        else
        {
            $obj =  AcademicYearModel::
                                where('school_id',Session::get('school_id'))
                                ->where('academic_year',$previous_acdemic_year)
                                ->first();

            if(isset($obj->id)  && count($obj->id)>0)
            {
            
                $academic_year_id = $obj->id;
            }
           
        }   
        
        return $academic_year_id;
    }

    public function get_academic_year_less_than($academic_year_id)
    {
        $str_years = '';
        $academic_year = array();
        $endDate = AcademicYearModel::select('end_date')
                                                ->where('id','=',$academic_year_id)
                                                ->where('school_id','=',$this->school_id)
                                                ->first();
                                                 
        if(isset($endDate->end_date))
        {
            $academic_year = AcademicYearModel::select('id')->where('school_id','=',$this->school_id)
                                                            ->where('end_date','<=',$endDate->end_date)->get();    
        }
        
        if(!empty($academic_year)){
            $endDate = $academic_year->toArray();
            if(isset($endDate) && !empty($endDate))
            {
             
                $arr      = [];
                foreach($endDate as $val)
                {
                    $arr[] = $val['id'];
                }
                $str_years    = implode(',',$arr);

            }
            $str_years;
        }
        return $str_years;
    }

    public function get_levels($academic_year_id)
    {
        /* academic years less than selected academic year
           level should not bloced by admin
           school_id should be login school 
        */

        $arr_academic_year=[];   
        $obj_levels = '';
        
        $academic_year = $this->get_academic_year_less_than($academic_year_id); 

        if($academic_year)
        {
            
            $arr_academic_year = explode(',',$academic_year);    

            $obj_levels =   $this->LevelClassModel
                                        ->with('level_details')
                                        ->whereHas('get_level',function($q){
                                            $q->where('is_active','1');
                                        })
                                        ->where('school_id',Session::get('school_id'))
                                        ->whereIn('academic_year_id',$arr_academic_year)
                                        ->groupBy('level_id')
                                        ->orderBy('position')
                                        ->get();
        }
    
        return $obj_levels;
    }

    public function get_class($level_id,$professor=FALSE)
    {
        if($professor){
            $level_class=[];
            $obj_levels_for_professor = $this->get_levels_for_professor($this->academic_year,$professor,'optional');
            if(isset($obj_levels_for_professor) && !empty($obj_levels_for_professor)){
                foreach($obj_levels_for_professor as $value){
                    array_push($level_class,$value->level_class_id);    
                }
            }
        }

        $obj_classes = $this->LevelClassModel
                                        ->with('class_details')
                                        ->where('level_id',$level_id)
                                        ->where('school_id',Session::get('school_id'));
                                        if($professor){
                                            $obj_classes -> whereIn('id',$level_class);
                                        }
                                    $obj_classes = $obj_classes->get();

        return $obj_classes;                                
    }


    function get_level_class($level_class_id){
        $level_class_str = '';
        if($level_class_id){

            $obj_classes = $this->LevelClassModel
                                            ->with('class_details')
                                            ->with('level_details')
                                            ->where('id',$level_class_id)
                                            ->where('school_id',Session::get('school_id'))
                                            ->first();
            if(!empty($obj_classes)){
                $level_class_str =  $obj_classes->toArray();                 
            }
        }
        return $level_class_str;
    }

    function get_employees()
    {
        $arr_employee = [];

        $academic_year = \Session::get('academic_year');

        $academic_year = $this->get_academic_year_less_than($academic_year); 
        $school_id = \Session::get('school_id');
        $locale = $this->locale;

        if($school_id!='' && $academic_year){

             $arr_academic_year = explode(',',$academic_year);

            $user_details             = $this->UserModel->getTable();
            $prefixed_user_details    = DB::getTablePrefix().$this->UserModel->getTable();

            $user_trans_table             = $this->UserTranslationModel->getTable();                  
            $prefixed_user_trans_table    = DB::getTablePrefix().$this->UserTranslationModel->getTable();

            $employee                    = $this->EmployeeModel->getTable();       
            $prefixed_employee_table     =  DB::getTablePrefix().$this->EmployeeModel->getTable();       
           

            $arr_employee = DB::table($employee)
                                    ->select(DB::raw($employee.".id as id,".
                                                     $employee.".user_id as user_id,".  
                                                     $prefixed_user_details.".national_id as national_id,".  
                                                     "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
                                                              .$prefixed_user_trans_table.".last_name) as user_name"
                                                     ))
                                    ->whereNull($user_details.'.deleted_at')
                                    ->join($user_details,$employee.'.user_id','=',$user_details.'.id')
                                    ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                    ->where($user_trans_table.'.locale','=',$locale)
                                    ->where($employee.'.school_id','=',$school_id)
                                    ->where($employee.'.is_active',1)
                                    ->where($employee.'.has_left',0)
                                    ->whereIn($employee.'.academic_year_id',$arr_academic_year)
                                    ->orderBy($employee.'.created_at','DESC')
                                    ->get();
                    
        }
        
        return $arr_employee;         
    }

    function get_level_order($level_id){
        /* This function returns the order of the level */
        $obj_level = LevelModel::select('level_order')->where('id',$level_id)->first();
        $order     = $obj_level->level_order;    
        return $order;
    }

    function get_class_wise_subjects($json_subjects){

        $arr_tmp_subjects = [];

         if($json_subjects!=''){
            $arr_subjects = json_decode($json_subjects,true);
            
            if(isset($arr_subjects) && $arr_subjects!='' && sizeof($arr_subjects)>0)
                    {
                        foreach ($arr_subjects as $subject_id) 
                        {
                            $obj_section    = CourseModel::where('id','=',$subject_id)->first();

                            if($obj_section)
                            {
                            
                                $arr_tmp_subjects[] = $obj_section->course_name;

                            }
                        }
                    }
                    
            $strSubjects = implode(',',$arr_tmp_subjects);
        }
        return $strSubjects;
    }

    
    /* Fill dropdown of level in professor panel*/
    function get_levels_for_professor($academic_year_id,$user_id,$optional=FALSE)
    {
        // if we set value to optional then it will return all levels and classes
                  
        $obj_professor = $this->SchoolTimeTableModel
                                                    ->whereHas('professor_details',function($q1){
                                                        $q1->where('is_active','=', 1);
                                                        $q1->where('has_left','=', 0);
                                                        $q1->where('school_id','=',$this->school_id);
                                                    })
                                                    ->where('school_id',$this->school_id)
                                                    ->where('academic_year_id',$this->academic_year)
                                                    ->where('professor_id',$user_id);
                                                    if(!$optional){
                                                      $obj_professor->groupBy('level_id');
                                                    }
                                                    
                                           $obj_professor = $obj_professor->with(['level_details'])
                                                    ->orderBy('level_order')    
                                                    ->get();

        return $obj_professor;
    }


    function get_address_from_google_maps($lat,$lng, $search_key="locality")
    {
        $current_address = '';
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
        $json = @file_get_contents($url);
        $data=json_decode($json);

        if(isset($data->results[1]->address_components) && sizeof($data->results[1]->address_components)>0)
        {
            foreach ($data->results[1]->address_components as $key => $value) 
            {
                if(isset($value->types) && sizeof($value->types)>0){
                    if(in_array($search_key,$value->types))
                    {
                        $current_address = isset($value->long_name) ? $value->long_name :'';
                    }
                }
            }
        }
        return $current_address;
    }

    function get_fees()
    {
        $obj_fees= $this->FeesModel->where('is_active',1)->get();

        return $obj_fees;
    }

    function get_school_name($school_id=FALSE)
    {
        if($school_id == FALSE)
        {
            $school_id = \Session::get('school_id');
        }
        
        $template = $this->SchoolTemplateTranslationModel->where('slug','school_name')->first();
        $template_id = isset($template->school_template_id) ? $template->school_template_id : 0;
        $result = $this->SchoolProfileModel
                                    ->where('school_template_id',$template_id)
                                    ->where('school_no',$school_id)
                                    ->first();
                                    
        if(isset($result->value))
        {

            return $result->value;
        }
        else
        {
            return '';
        }
    }

    function get_telephone_number()
    {
        $template = $this->SchoolTemplateTranslationModel->where('slug','school_telephone_number')->first();
        $template_id = isset($template->school_template_id) ? $template->school_template_id : 0;
        $result = $this->SchoolProfileModel
                                    ->where('school_template_id',$template_id)
                                    ->where('school_no',\Session::get('school_id'))
                                    ->first();
                                    
        if(isset($result->value))
        {

            return $result->value;
        }
        else
        {
            return '';
        }
    }

    function get_principle()
    {
        $template = $this->SchoolTemplateTranslationModel->where('slug','principal_name')->first();
        $template_id = isset($template->school_template_id) ? $template->school_template_id : 0;
        $result = $this->SchoolProfileModel
                                    ->where('school_template_id',$template_id)
                                    ->where('school_no',\Session::get('school_id'))
                                    ->first();
                                    
        if(isset($result->value))
        {

            return $result->value;
        }
        else
        {
            return '';
        }
    }
    function get_school_address()
    {
        $template = $this->SchoolTemplateTranslationModel->where('slug','school_address')->first();
        $template_id = isset($template->school_template_id) ? $template->school_template_id : 0;
        $result = $this->SchoolProfileModel
                                    ->where('school_template_id',$template_id)
                                    ->where('school_no',\Session::get('school_id'))
                                    ->first();
                                    
        if(isset($result->value))
        {

            return $result->value;
        }
        else
        {
            return '';
        }
    }
    function get_school_email()
    {
        $template = $this->SchoolTemplateTranslationModel->where('slug','school_email')->first();
        $template_id = isset($template->school_template_id) ? $template->school_template_id : 0;
        $result = $this->SchoolProfileModel
                                    ->where('school_template_id',$template_id)
                                    ->where('school_no',\Session::get('school_id'))
                                    ->first();
                                    
        if(isset($result->value))
        {

            return $result->value;
        }
        else
        {
            return '';
        }
    }
    function get_school_logo()
    {
        $template = $this->SchoolTemplateTranslationModel->where('slug','school_logo')->first();
        $template_id = isset($template->school_template_id) ? $template->school_template_id : 0;
        $result = $this->SchoolProfileModel
                                    ->where('school_template_id',$template_id)
                                    ->where('school_no',\Session::get('school_id'))
                                    ->first();
        if(isset($result->value))
        {
            return $this->user_profile_public_img_path.'/'.$result->value;
        }
        else
        {
            return '';
        }
    }
    function get_students($level_class_id=FALSE,$level=FALSE,$professor=FALSE)
    {
       
        $arr_levels=[];
        if($level){

            $levels = $this->get_class($level,$professor);
            $arr_levels = [];
            if(isset($levels) && !empty($level)){

                $levels = $levels->toArray(); 
                foreach($levels as $level){
                    array_push($arr_levels,$level['id']);
                }
            }
        }
        if($level_class_id){
            array_push($arr_levels,$level_class_id);
        }

        $students = $this->StudentModel
                            ->with('notifications')
                            ->whereHas('get_user_details',function(){})
                            ->select('id','user_id','student_no','bus_transport','pickup_address','drop_address')
                            ->with('get_user_details')    
                            ->where('school_id',$this->school_id)
                            ->where('academic_year_id',$this->academic_year)
                            ->where('has_left',0)
                            ->where('is_active',1);
                            if(count($arr_levels)>0)
                            {
                                $students->whereIn('level_class_id',$arr_levels);
                            }
        $students = $students->get();
        
        return $students;                    
    }
    
    function get_professor_by_level_class($level_class_id)
    {
        $obj_professor = $this->SchoolTimeTableModel->where('level_class_id',$level_class_id)
                                                    ->whereHas('professor_details',function($q1){
                                                        $q1->where('is_active','=', 1);
                                                        $q1->where('has_left','=', 0);
                                                        $q1->where('school_id','=',$this->school_id);
                                                    })
                                                    ->with(['notifications','user_details'])
                                                   /* ->with(['user_details' => function ($query)
                                                    {
                                                          $query->select('id','profile_image');
                                                    },'notifications','user_details'])*/
                                                    ->where('school_id',$this->school_id)
                                                    ->where('academic_year_id',$this->academic_year)
                                                    ->groupBy('professor_id')
                                                    ->with(['professor_subjects'])
                                                    ->get();
        
        return $obj_professor;                                                    

    }

    function get_professor_by_level($level)
    {
        $levels = $this->get_class($level);

        $arr_levels = [];
        if(isset($levels) && !empty($level)){

            $levels = $levels->toArray(); 
            
            foreach($levels as $level){

                array_push($arr_levels,$level['id']);
            }
        } 

        $obj_professor = $this->SchoolTimeTableModel->whereIn('level_class_id',$arr_levels)
                                                    ->whereHas('professor_details',function($q1){
                                                        $q1->where('is_active','=', 1);
                                                        $q1->where('has_left','=', 0);
                                                        $q1->where('school_id','=',$this->school_id);
                                                    })
                                                    ->with(['user_details' => function ($query)
                                                    {
                                                          $query->select('id','profile_image');
                                                    },'notifications'])
                                                    ->where('school_id',$this->school_id)
                                                    ->where('academic_year_id',$this->academic_year)
                                                    ->groupBy('professor_id')
                                                    ->with(['professor_subjects'])
                                                    ->get();
        return $obj_professor;                                                    

    }

    function get_professor_by_year($school_id=FALSE,$academic_year=FALSE){

        $arr_professor = array();
        $locale = $this->locale;
        $academic_year = \Session::get('academic_year');

        $academic_year = $this->get_academic_year_less_than($academic_year); 
        $school_id = \Session::get('school_id');
            
        if($school_id!='' && $academic_year){

            $arr_academic_year = explode(',',$academic_year);    
             /*GET Professor*/
            $user_details             = $this->UserModel->getTable();
            $user_trans_table         = $this->UserTranslationModel->getTable();                  
            $professor                = $this->ProfessorModel->getTable();

            
            $arr_professor     = DB::table($professor)
                                    ->select(DB::raw($professor.".id as id,".
                                                     $professor.".user_id,".
                                                     $user_details.".email as email, ".
                                                     $user_details.".national_id as national_id, ".
                                                     $professor.".is_active as is_active, ".
                                                     $professor.".academic_year_id as academic_year_id, ".
                                                     "CONCAT(".$user_trans_table.".first_name,' ',"
                                                              .$user_trans_table.".last_name) as user_name"
                                                     ))
                                    ->join($user_details,$professor.'.user_id',' = ',$user_details.'.id')
                                    ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                    ->where($professor.".school_id","=",$school_id)
                                    ->whereNull($professor.'.deleted_at')
                                    ->where($professor.'.is_active','=',1)
                                    ->where($professor.'.has_left','=',0)
                                    ->where($user_trans_table.'.locale','=',$locale)
                                    ->whereIn($professor.'.academic_year_id',$arr_academic_year)
                                    ->orderBy($user_details.'.created_at','DESC')->get();                        
            /*GET Professor*/
        }
        
        return $arr_professor;
    }

    function get_parent($level=FALSE,$level_class_id=FALSE){
        $arr_levels=[];
        if($level){

            $levels = $this->get_class($level);
            $arr_levels = [];
            if(isset($levels) && !empty($level)){

                $levels = $levels->toArray(); 
                foreach($levels as $level){
                    array_push($arr_levels,$level['id']);
                }
            }
        }
        if($level_class_id){
            array_push($arr_levels,$level_class_id);
        }

        $parents = $this->StudentModel
                        ->whereHas('get_user_details',function(){})
                        ->with('get_user_details')
                        ->with('get_level_class.level_details','get_level_class.class_details')
                        ->whereHas('get_parent_details',function(){})
                        ->with('get_parent_details','parent_notifications');
                        if(count($arr_levels)>0)
                        {
                            $parents ->whereIn('level_class_id',$arr_levels) ;
                        }
                        
                $parents = $parents->where('has_left',0)
                        ->where('is_active',1)
                        ->where('school_id',$this->school_id)
                        ->where('academic_year_id',$this->academic_year)
                        ->whereHas('get_parent_details',function(){})
                        ->groupBy('parent_id')
                        ->get();
        return $parents;                
    }


    /* used to  get the survey */
    function get_survey($role,$search_str=''){

        $survey                   = $this->SurveyModel->getTable();
        $academic_year_less_thn   = $this->get_academic_year_less_than($this->academic_year);
        $arr_academic_year        = array();
        if($academic_year_less_thn)
        {
            $arr_academic_year = explode(',',$academic_year_less_thn); 
        }   
        $date     = date('y-m-d'); 
        $q = DB::table($survey)
                                ->select(DB::raw(
                                                 $survey.".id,".
                                                 $survey.".survey_title as  survey_title ,".
                                                 $survey.".survey_description as survey_description, ".
                                                 $survey.".user_role as role, ".
                                                 $survey.".start_date as start_date, ".
                                                 $survey.".end_date as end_date "))
                                ->whereNull($survey.'.deleted_at');
                                if($role=='student' || $role=='parent'){
                                    $q->where($survey.'.academic_year_id',$this->academic_year);
                                }else{
                                    $q->whereIn($survey.'.academic_year_id',$arr_academic_year);

                                }
    $obj_user               = $q->where($survey.'.start_date','<=',$date)
                                ->whereRaw("(".$survey.".user_role LIKE '%".$role."%') ")
                                ->orderBy($survey.'.id','desc');
        $search_term = $search_str;
        if(!empty($search_term) && $search_term!="")
        {
            $obj_user = $obj_user->WhereRaw("( (".$survey.".survey_title LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$survey.".survey_description LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$survey.".user_role LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$survey.".start_date LIKE '%".$search_term."%') ") 
                                     ->orWhereRaw("(".$survey.".end_date LIKE '%".$search_term."%')) ");
                                     
        }
        /* ---------------- Filtering Logic ----------------------------------*/                    
        return $obj_user;
    }

    function get_count_survey($role){

        $survey                   = $this->SurveyModel->getTable();
        $academic_year_less_thn   = $this->get_academic_year_less_than($this->academic_year);
        $arr_academic_year        = array();
        if($academic_year_less_thn)
        {
            $arr_academic_year = explode(',',$academic_year_less_thn); 
        }   
        $date     = date('y-m-d'); 
        $q = DB::table($survey)
                                ->select(DB::raw(
                                                 $survey.".id,".
                                                 $survey.".survey_title as  survey_title ,".
                                                 $survey.".survey_description as survey_description, ".
                                                 $survey.".user_role as role, ".
                                                 $survey.".start_date as start_date, ".
                                                 $survey.".end_date as end_date "))
                                ->whereNull($survey.'.deleted_at');
                                if($role=='student' || $role=='parent'){
                                    $q->where($survey.'.academic_year_id',$this->academic_year);
                                }else{
                                    $q->whereIn($survey.'.academic_year_id',$arr_academic_year);

                                }
    $obj_user               = $q->where($survey.'.start_date','<=',$date)
                                ->whereRaw("(".$survey.".user_role LIKE '%".$role."%') ")
                                ->orderBy($survey.'.id','desc')->count();
        
         
        /* ---------------- Filtering Logic ----------------------------------*/                    
        return $obj_user;
    }

    /*checked survey is replied by user */
    function is_survey_replied($user_id = 0,$survey_id){
        $q = SurveyQuestionsAnswerModel::where('survey_id','=',$survey_id);
        if($user_id>0){
            $q->where('from_user_id','=',$user_id);
        }
        $isReplied = $q->count();
        return $isReplied;
        
    }

    /*It returns the question categories */
    /*
    | get_question_category()  : Get the question category data
    | Auther  : Padmashri
    | Date    : 15-05-2018
    */
    function get_question_category(){

        $obj_data = $arr_data = array();
        $obj_data = QuestionCategoryModel::where('id','<',5)->whereNull('deleted_at')->get();
        if($obj_data){
            $arr_data = $obj_data->toArray();
        }
        
        return $arr_data;
    }

    function get_school_latitude()
    {
        $result = $this->SchoolProfileModel
                                    ->whereHas('school_template',function($q)
                                    {
                                        $q->whereHas('school_latitude',function($q2){
                                        }); 
                                    })
                                    ->with(['school_template'=>function($q)
                                    {
                                        $q->with(['school_latitude'=>function($q2){
                                        }]); 
                                    }])
                                    ->where('school_no',\Session::get('school_id'))->first();
            
            
        if(isset($result->value))
        {    
            return $result->value;
        }
        else
        {
            return false;
        }
    }
    function get_school_longitude()
    {
        $result = $this->SchoolProfileModel
                                    ->whereHas('school_template',function($q)
                                    {
                                        $q->whereHas('school_longitude',function($q2){
                                        }); 
                                    })
                                    ->with(['school_template'=>function($q)
                                    {
                                        $q->with(['school_longitude'=>function($q2){
                                        }]); 
                                    }])
                                    ->where('school_no',\Session::get('school_id'))->first();
        if(isset($result->value))
        {
            return $result->value;
        }
        else
        {
            return false;
        }
    }


    function getNewsPublishDate(){
        $arr = [];
        
        $arr['is_published'] = '1';
        $res = NewsModel::where('publish_date','<=',date('Y-m-d') )->where('start_time','<=',date('h:i:s'))->update($arr);
    }

    function get_professor_courses($class_id,$professor_id)
    {
        $arr_professor = [];
        $obj_professor = $this->SchoolTimeTableModel
                              ->with('professor_subjects')
                              ->whereHas('professor_details',function($q1){
                                    $q1->where('is_active','=', 1);
                                    $q1->where('has_left','=', 0);
                                    $q1->where('school_id','=',$this->school_id);
                              })
                              ->where('school_id',$this->school_id)
                              ->where('academic_year_id',$this->academic_year)
                              ->where('level_class_id',$class_id)
                              ->where('professor_id',$professor_id)
                              ->groupBy('course_id')
                              ->get();

        return $obj_professor;
    }


    function get_courses($academic_year_id,$role=FALSE,$user_id=FALSE,$level_id=FALSE,$class_id=FALSE)
    {

        $arr_academic_year=[];   
        $obj_course = '';

        $academic_year = $this->get_academic_year_less_than($academic_year_id); 
        
        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year); 

            if($level_id!='' && $class_id!='')   
            {
                $arr_courses = [];
                $obj_courses  = $this->SchoolSubjectsModel->select('json_subjects')->where('school_id',$this->school_id)->whereIn('academic_year_id',$arr_academic_year)->where('level_id',$level_id)->where('class_id',$class_id)->first();
        
                if(isset($obj_courses['json_subjects']) && count($obj_courses['json_subjects'])>0)
                {
                    foreach ($obj_courses['json_subjects'] as $key => $value) 
                    {
                        $arr_courses[$key] = $this->CourseModel->where('id',$value)->first()->toArray();
                    }
                }
                return $arr_courses;
            }
            else
            {
                $obj_course =   $this->SchoolCourseModel
                                            ->whereHas('get_course',function(){})
                                            ->with('get_course')
                                            ->where('school_id',Session::get('school_id'))
                                            ->whereIn('academic_year_id',$arr_academic_year)
                                            ->where('is_active','1')
                                            ->groupBy('course_id')
                                            ->get();

                return $obj_course;
            }
        }
        
    }
    function get_permissions($role,$academic_year_id,$school_id,$level_class_id=FALSE)
    {
        
        $arr_academic_year = [];
        $obj_users = '';
        $academic_year = $this->get_academic_year_less_than($academic_year_id); 
        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);    
        }
            
        if($role == config('app.project.role_slug.student_role_slug'))
        {
            $obj_users = StudentModel::with('notifications','get_user_details')
                                     ->where('academic_year_id',$academic_year_id)
                                     ->where('has_left',0)
                                     ->where('is_active',1)
                                     ->where('school_id',$school_id);
                                     if($level_class_id)
                                     {
                                        $obj_users = $obj_users->where('level_class_id',$level_class_id);
                                     }
                                     $obj_users = $obj_users->get();
        }
        if($role == config('app.project.role_slug.professor_role_slug'))
        {
            $obj_users = ProfessorModel::with('notifications','get_user_details')
                                       ->where('school_id',$school_id)
                                       ->whereIn('academic_year_id',$arr_academic_year)
                                       ->where('is_active',1)
                                       ->where('has_left',0)
                                       ->get();
        }
        if($role == config('app.project.role_slug.parent_role_slug'))
        {
            $obj_users = SchoolParentModel::with('notifications','get_user_details')
                                     ->where('is_active',1)
                                     ->where('school_id',$school_id)
                                     ->get();
        }
        if($role == config('app.project.role_slug.employee_role_slug'))
        {
            $obj_users = EmployeeModel::with('notifications','get_user_details')
                                      ->whereIn('academic_year_id',$arr_academic_year)
                                      ->where('has_left',0)
                                      ->where('is_active',1)
                                      ->where('school_id',$school_id)
                                      ->get();
        }
        if($obj_users)
        {
            $arr_users = $obj_users->toArray();
            return $arr_users;
        }

    }

    function get_user_permissions($user_id,$role,$academic_year)
    {
        /*$arr_academic_year = [];
        $academic_year = $this->get_academic_year_less_than($academic_year); 
        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year); 
        }*/

        if($role == config('app.project.role_slug.professor_role_slug'))
        {
            $obj_user = ProfessorModel::with('notifications','get_user_details')
                                      ->where('user_id',$user_id)
                                      ->where('school_id',$this->school_id)
                                      ->where('academic_year_id',$academic_year)
                                      ->where('has_left',0)
                                      ->where('is_active',1)
                                      ->first();
        }
        elseif($role == config('app.project.role_slug.student_role_slug'))
        {
            $obj_user =   StudentModel::with('notifications','get_user_details')
                                      ->where('user_id',$user_id)
                                      ->where('school_id',$this->school_id)
                                      ->where('academic_year_id',$academic_year)
                                      ->where('has_left',0)
                                      ->where('is_active',1)
                                      ->first();
        }
        elseif($role == config('app.project.role_slug.parent_role_slug'))
        {
            $obj_user = SchoolParentModel::with('notifications','get_user_details')
                                   ->where('parent_id',$user_id)
                                   ->where('school_id',$this->school_id)
                                   ->where('is_active',1)
                                   ->first();
        }
        elseif($role == config('app.project.role_slug.employee_role_slug'))
        {
            $obj_user =   EmployeeModel::with('notifications','get_user_details')
                                      ->where('user_id',$user_id)
                                      ->where('school_id',$this->school_id)
                                      ->where('academic_year_id',$academic_year)
                                      ->where('has_left',0)
                                      ->where('is_active',1)
                                      ->first();
        }

        if(isset($obj_user) && $obj_user!=null && count($obj_user)>0)
        {
            $arr_user = $obj_user->toArray();
            return $arr_user;
        }
    }

    public function send_sms($arr_data = FALSE,$school_id=FALSE)
    {   
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_sms_template = [];
            $obj_sms_template = '';
            if(isset($school_id) && $school_id!=null)
            {
                $obj_sms_template = $this->SchoolSmsTemplateModel
                                           ->where('template_slug',$arr_data['sms_template_slug'])
                                           ->where('school_id',$school_id)
                                           ->where('is_enabled',1)
                                           ->first();

            
                if($obj_sms_template)
                {
                    $arr_sms_template = $obj_sms_template->toArray();
                    $user               = $arr_data['user'];
                    
                    if(isset($arr_sms_template['template_html']))
                    {
                        $content = $arr_sms_template['template_html'];
                                        
                        if(isset($arr_data['arr_built_content']) && sizeof($arr_data['arr_built_content'])>0)
                        {
                            foreach($arr_data['arr_built_content'] as $key => $data)
                            {
                                $content = str_replace("##".$key."##",$data,$content);
                            }
                        }
                        
                       /* $username="SMS3"; 
                        $password="8639400331";
                        $message=$content;
                        $sender="SMS3"; //ex:INVITE GOT THIS ID FROM DASHBORAD
                        $mobile_number=$arr_data['mobile_no'];
                        $url="login.bulksmsgateway.in/sendmessage.php?user=".urlencode($username)."&password=".urlencode($password)."&mobile=".urlencode($mobile_number)."&message=".urlencode($message)."&sender=".urlencode($sender)."&type=".urlencode('3');
                        
                        $ch = curl_init();
                        $headers = array(
                                'Accept: application/json',
                                'Content-Type: application/json',
                            );
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $output = curl_exec($ch); 

                        if(curl_errno($ch))
                        {
                            echo curl_error($ch);
                        }
                        else
                        {
                            
                        }
                        curl_close($ch);*/
                        return true;
                    }
                }
            }
        }
        return false; 
    }
    public function current_user_access()
    {
        $data =[];
        $user =  \Sentinel::check();
        if(Session::get('role')=='school_admin'){
            $user_role = RoleModel::select('permissions')->where('slug','school_admin')->first() ;
            if($user_role)
            {
               $data = json_decode($user_role->permissions,true);
            }
        }
        else{
            $school = EmployeeModel::select('school_id','user_role')->where('user_id',$user->id)->first();
            if($school)
            {
                $role = RoleModel::select('id')->where('slug',$school->user_role)->first();    
                $user_role = SchoolRoleModel::select('permissions')->where('role_id',$role->id)->where('school_id',$school->school_id)->first() ;
                if($user_role)
                {
                   $data = json_decode($user_role->permissions,true);
                }
            }
        }
        return $data;
    }

    public function validate_user($role,$user_id)
    {

        $status = 0;

        if(config('app.project.role_slug.admin_role_slug')== $role)
        {
            $status  = 1;
        }
        elseif(config('app.project.role_slug.student_role_slug')== $role)
        {
            $student = StudentModel::where('user_id',$user_id)->where('has_left',0)->first();
            if(isset($student))
            {
                $school_id = isset($student->school_id) ? $student->school_id :0;
                \Session::put('school_id',$school_id);
                $level_class_id = isset($student->level_class_id) ? $student->level_class_id :0 ;
                \Session::put('level_class_id',$level_class_id);
                $student_id = isset($student->id) ? $student->id :0 ;
                \Session::put('student_id',$student_id);

                $academic_year_id =0;
                $current_year = date('Y');
                $previous_year = $current_year - 1;
                $next_year = $current_year + 1;
                $previous_acdemic_year = $previous_year.'-'.$current_year;
                $next_acdemic_year = $current_year.'-'.$next_year;

                $next_exist = AcademicYearModel::where('academic_year',$next_acdemic_year)->where('school_id',\Session::get('school_id'))->first();
                if(empty($next_exist))
                {
                    $prvious_exist = AcademicYearModel::where('academic_year',$previous_acdemic_year)->where('school_id',\Session::get('school_id'))->first();
                    if(!empty($prvious_exist))
                    {
                        $current_date = date('Y-m-d');
                        $current_date = date_create($current_date);
                        $to_date = date_create($prvious_exist->end_date);
                        $date_diff = date_diff($to_date,$current_date);
                        $url = config('app.project.role_slug.school_admin_role_slug').'/academic_year/create';
                        if($date_diff->format('%R%a') > 0)
                        {   
                            
                        }
                        else
                        {
                            $academic_year_id = $prvious_exist->id;
                        }
                    }
                    else
                    {
                        
                    }
                }
                else
                {
                   $academic_year_id = $next_exist->id; 
                }   
                
                \Session::put('academic_year',$academic_year_id);

                $status = 1;
            }
            else
            {
                $status = 0;
            }
    
        }
        elseif(config('app.project.role_slug.professor_role_slug')== $role)
        {

            $professor = $this->ProfessorModel->select('school_id')->where('user_id',$user_id)->where('has_left',0)->first();
            if(isset($professor->school_id)){
                \Session::set('school_id', $professor->school_id );    
                $status = 1;
            }
            else
            {
                $status = 0;
            }
        }
        elseif($role == 'employee')
        {
            $school = $this->EmployeeModel->select('school_id')->where('user_id',$user_id)->where('has_left',0)->first();
            if(isset($school->school_id)){
                \Session::set('school_id', $school->school_id );    
                $status = 1;
            }
            else{
                $status = 0;
            }   
        }
        

        /*elseif($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
        {
            $school = $this->SchoolAdminModel->where('user_id',$user->id)->first();
            if(isset($school->is_active) && $school->is_active==0){
                Flash::error('Your account is blocked by admin');
                return redirect()->back();       
            }  
            $school_id = isset($school->school_id) ? $school->school_id : '0' ;
            
            \Session::set('school_id', $school_id );
    
            return redirect(url(config('app.project.role_slug.school_admin_role_slug').'/dashboard'));
        }*/
        return $status;
       /* else
        {
            $school = $this->EmployeeModel->select('school_id')->where('user_id',$user->id)->where('has_left',0)->first();
            if(isset($school->school_id)){
                \Session::set('school_id', $school->school_id );    
                return redirect(url(config('app.project.role_slug.school_admin_role_slug').'/dashboard'));
            }
            else{
                Flash::error('You have already left the school');
                return redirect(url(config('app.project.role_slug.school_admin_role_slug')));
            }   
        }*/
    }
}