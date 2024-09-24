<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Models\ClubModel;
use App\Models\ClubStudentsModel;
use App\Models\NotificationModel;
use App\Models\ProfessorModel;
use App\Models\StudentModel;
use App\Common\Traits\MultiActionTrait;
use App\Common\Services\EmailService;

use App\Common\Services\CommonDataService;

use Session;
use Validator;
use Flash;
use Sentinel;

class ClubController extends Controller
{
	public function __construct(CommonDataService $CommonDataService,EmailService $EmailService){

        $this->CommonDataService = $CommonDataService;
        $this->ClubModel         = new ClubModel();
        $this->ClubStudentsModel = new ClubStudentsModel();
        $this->NotificationModel = new NotificationModel();
        $this->ProfessorModel    = new ProfessorModel();
        $this->StudentModel      = new StudentModel();
        $this->BaseModel         = $this->ClubModel;
        $this->EmailService             = $EmailService;
        $this->arr_view_data     = [];
        $this->module_url_path   = url(config('app.project.school_admin_panel_slug')).'/club';
        $this->module_title      = translation('club');
        
        $this->module_view_folder           = "schooladmin.club";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-users';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');

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
    }
    /*
    | index() : listing of clubs
    | Auther : Pooja K
    | Date : 18 June 2018
    */ 
    public function index(){
        $arr_clubs =[];
        $obj_clubs = $this->ClubModel
                                    ->with('get_students')
                                    ->with('get_supervisor')
                                    ->where('school_id',$this->school_id)
                                    ->where('academic_year_id',$this->academic_year)
                                    ->orderBy('id','DESC')
                                    ->get();

        if(isset($obj_clubs) && !empty($obj_clubs)){

            $arr_clubs = $obj_clubs->toArray();
        }                            
        
        $this->arr_view_data['module_icon']  = $this->module_icon;
        $this->arr_view_data['arr_clubs']    = $arr_clubs;
        $this->arr_view_data['module_title'] = translation('manage').' '.$this->module_title;

        return view($this->module_view_folder.'.index', $this->arr_view_data);

        return redirect($this->module_url_path);
    }

    /*
    | create() : redirecting to create club page
    | Auther : Pooja K
    | Date : 18 June 2018
    */ 

    public function create(){
        $arr_professors =[];
        $arr_professors = $this->CommonDataService->get_professor_by_year();
        if(isset($arr_professors) && !empty($arr_professors)){
           
            $this->arr_view_data['module_icon']    = $this->module_icon;
            $this->arr_view_data['arr_professors'] = $arr_professors;
            $this->arr_view_data['module_title']   = translation('add').' '.$this->module_title;

            return view($this->module_view_folder.'.create', $this->arr_view_data);
        }
        Flash::error(translation("lets_add_professor_in_our_school"));
        return redirect(url(config('app.project.school_admin_panel_slug')).'/professor/create');
    }

    /*
    | store() : store club data in database
    | Auther : Pooja K
    | Date : 18 June 2018
    */ 

    public function store(Request $request){
        
        $messages = $arr_rules = [];
        $form_data                = $request->all();
        $arr_rules['club_name']   = 'required|regex:/^[a-zA-Z ]+$/';
        $arr_rules['club_id']     = 'required|regex:/^[a-zA-Z0-9 ]+$/';
        $arr_rules['supervisor']  = 'required|numeric';
        $arr_rules['club_type']   = 'required|alpha';
        $arr_rules['description'] = 'required';
        $arr_rules['club_place']  = 'required';
        if($request->club_type == 'paid')
        {
            $arr_rules['club_fees']   = 'required|numeric|min:0';    
        }
        

        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'),
                            'alpha'                => translation('please_enter_letters_only'),
                            'min'                  => translation('please_enter_a_value_greater_than_or_equal_to_0')  
                        );
        
        $validator                          = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails()){

            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $arr_data = [];

        if($request->club_type == 'paid'){     
            $arr_data['is_free'] = 'PAID';
            $arr_data['club_fee'] = $request->club_fees; 
        } 
        else{
            $arr_data['is_free'] = 'FREE'; 
        }
        $arr_data['school_id']        = $this->school_id;
        $arr_data['club_no']          = trim($request->club_id);
        $arr_data['supervisor_id']    = trim($request->supervisor);
        $arr_data['club_name']        = trim($request->club_name);
        $arr_data['place']            = $request->club_place;
        $arr_data['description']      = trim($request->description);
        $arr_data['academic_year_id'] = $this->academic_year;

        $clu_id_existence = $this->ClubModel
                                            ->where('school_id',$this->school_id)
                                            ->where('academic_year_id',$this->academic_year)
                                            ->where('club_no',$request->club_id)
                                            ->count();
        if($clu_id_existence){
            Flash::error(translation('this_club_id_is_already_in_use'));
            return redirect()->back();            
        }                                    

        $create_club = $this->ClubModel->create($arr_data);

        $settings = $this->ProfessorModel->with('notifications','get_user_details')->where('user_id',trim($request->supervisor))->where('school_id',$this->school_id)->where('is_active',1)->where('has_left',0)->first();

        $arr_settings = [];
        if(isset($settings) && count($settings)>0 && !is_null($settings))
        {
            $arr_settings = $settings->toArray();
        }
        if($create_club)
        {
            if(isset($arr_settings['notifications']['notification_permission']) && $arr_settings['notifications']['notification_permission']!=null)
            {
                $permissions = json_decode($arr_settings['notifications']['notification_permission'],true);
                    
                if(array_key_exists('club.app',$permissions))
                {
                 
                    $arr_notification = [];
                    $arr_notification['school_id']          =  $this->school_id;
                    $arr_notification['from_user_id']       =  $this->user_id;
                    $arr_notification['to_user_id']         =  $arr_settings['user_id'];
                    $arr_notification['user_type']          =  config('app.project.role_slug.school_admin_role_slug');
                    $arr_notification['notification_type']  =  'Club Supervisor Added';
                    $arr_notification['title']              =  'Club Supervisor Added: You are assigned as a supervisor to club '.ucwords($create_club->club_name);
                    $result = $this->NotificationModel->create($arr_notification);
                }

                 $details          = [
                                    'first_name'  =>  isset($arr_settings['get_user_details']['first_name'])?ucwords($arr_settings['get_user_details']['first_name']):'',                                    'email'       =>  isset($arr_settings['get_user_details']['email'])?$arr_settings['get_user_details']['email']:'',
                                    'mobile_no'   =>  isset($arr_settings['get_user_details']['mobile_no'])?$arr_settings['get_user_details']['mobile_no']:'',
                                    'club_name'   =>  isset($request->club_name)?ucwords($request->club_name):''
                            ];
                if(array_key_exists('club.sms',$permissions))
                {
                    $arr_sms_data = $this->built_sms_data($details,'supervisor');
                    $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
                }
                if (array_key_exists('club.email',$permissions))
                {
                    $arr_mail_data = $this->built_mail_data($details,'supervisor');
                    $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
                }
            }
            Flash::success(translation('club_created_successfully'));
            return redirect()->back();
        }

        
    }
    /*
    | edit() : redirecting to edit club page
    | Auther : Pooja K
    | Date : 18 June 2018
    */ 

    public function edit($enc_id){

        $id = base64_decode($enc_id);
        if(!is_numeric($id)){

            Flash::error(translation('something_went_wrong'));
            return redirect($module_url_path);

        }
        $obj_club = $this->ClubModel->with('get_students')->where('id',$id)->first();

        $arr_professors =[];
        $arr_professors = $this->CommonDataService->get_professor_by_year();
        if(isset($arr_professors) && !empty($arr_professors)){
           
            $this->arr_view_data['module_icon']    = $this->module_icon;
            $this->arr_view_data['arr_professors'] = $arr_professors;
            $this->arr_view_data['obj_club']       = $obj_club;
            $this->arr_view_data['module_title']   = translation('edit').' '.$this->module_title;

            if(isset($obj_club->get_students) && count($obj_club->get_students)>0){
                $this->arr_view_data['editable']   = 'no';    
            }
            else{
                $this->arr_view_data['editable']   = 'yes';
            }

            return view($this->module_view_folder.'.edit', $this->arr_view_data);
        }
        return redirect()->back();
    }

    /*
    | update() : update club data in database
    | Auther : Pooja K
    | Date : 18 June 2018
    */ 

    public function update(Request $request,$enc_id=FALSE){
        
      
        $id = base64_decode($enc_id); 
        if(!is_numeric($id)){

            Flash::error(translation('something_went_wrong'));
            return redirect($module_url_path);

        }
        $messages = $arr_rules = [];

        $form_data                = $request->all();
        $arr_rules['club_name']   = 'required|regex:/^[a-zA-Z ]+$/';
        $arr_rules['club_id']     = 'required|regex:/^[a-zA-Z0-9 ]+$/';
        $arr_rules['supervisor']  = 'required|numeric';
        $arr_rules['club_type']   = 'alpha';
        $arr_rules['description'] = 'required';
        $arr_rules['club_place']  = 'required';
        $arr_rules['club_fees']   = 'numeric|min:0';

        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'),
                            'alpha'                => translation('please_enter_letters_only'),
                            'min'                  => translation('please_enter_a_value_greater_than_or_equal_to_0')    
                        );
        
        $validator                          = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails()){

            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $clu_id_existence = $this->ClubModel
                                            ->where('id','<>',$id)
                                            ->where('school_id',$this->school_id)
                                            ->where('academic_year_id',$this->academic_year)
                                            ->where('club_no',$request->club_id)
                                            ->count();
        if($clu_id_existence){
            Flash::error(translation('this_club_id_is_already_in_use'));
            return redirect()->back();            
        }                                 

        $arr_data = [];

        if($request->editable=="yes"){
            if($request->club_type == 'paid'){     
            $arr_data['is_free'] = 'PAID';
            $arr_data['club_fee'] = $request->club_fees; 
            } 
            else{
                $arr_data['is_free'] = 'FREE'; 
                $arr_data['club_fee'] = 0; 
            }    
        }
        
        $arr_data['club_no']          = $request->club_id;
        $arr_data['supervisor_id']    = $request->supervisor;
        $arr_data['club_name']        = $request->club_name;
        $arr_data['place']            = $request->club_place;
        $arr_data['description']      = $request->description;

        $this->ClubModel->where('id',$id)->update($arr_data);

        Flash::success(translation('club_updated_successfully'));
        return redirect()->back();

    }

    /*
    | view() : view club details
    | Auther : Pooja K
    | Date : 18 June 2018
    */ 

    public function view($enc_id=FALSE){

        $id = base64_decode($enc_id);
        $obj_club = $this->ClubModel
                                    ->with('get_supervisor','get_students.get_user_details','get_students.get_level_class.level_details','get_students.get_level_class.class_details')
                                    ->where('id',$id)
                                    ->first();

        if(isset($obj_club) && !empty($obj_club)){

            $arr_club = $obj_club->toArray();

            $this->arr_view_data['module_icon']    = $this->module_icon;
            $this->arr_view_data['arr_data']       = $arr_club;
            $this->arr_view_data['module_title']   = translation('view').' '.$this->module_title;

            return view($this->module_view_folder.'.view', $this->arr_view_data);
        }
        Flash::success(translation('no_data_available'));
        return redirect()->back();
    }

    /*
    | multi_action() : multiple delete
    | Auther : Pooja K
    | Date : 18 June 2018
    */

    public function multi_action(Request $request)
    {
        $arr_rules = array();
        $arr_rules['multi_action'] = "required";
        $arr_rules['checked_record'] = "required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            Flash::error(translation('please_select_record_to_perform_multiaction'));
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $multi_action = $request->input('multi_action');
        $checked_record = $request->input('checked_record');

        /* Check if array is supplied*/
        if(is_array($checked_record) && sizeof($checked_record)<=0)
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction') );
            return redirect()->back();
        }

        foreach ($checked_record as $record_id) 
        {  
            if($multi_action=="delete")
            {
               $this->perform_delete(base64_decode($record_id));    
               Flash::success($this->module_title.' '.strtolower(translation('deleted_succesfully'))); 
            } 
        }

        return redirect()->back();
    }

    public function delete($enc_id = FALSE)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_delete(base64_decode($enc_id)))
        {
            Flash::success($this->module_title.' '.translation('deleted_succesfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction'));
        }

        return redirect()->back();
    }

    public function perform_delete($id)
    {
        $delete= $this->BaseModel->where('id',$id)->delete();
        $this->ClubStudentsModel->where('club_id',$id)->delete();
        if($delete)
        {  
            return TRUE;
        }

        return FALSE;
    }

    /*
    | add_student() : redirecting to add student in club page
    | Auther : Pooja K
    | Date : 18 June 2018
    */ 

    public function add_student($enc_id){

        $id = base64_decode($enc_id);

        $obj_club = $this->ClubModel->where('id',$id)->first();

        $arr_professors = $arr_levels=[];
        $arr_professors = $this->CommonDataService->get_professor_by_year();
        if(isset($arr_professors) && !empty($arr_professors)){
            
            $obj_levels = $this->CommonDataService->get_levels($this->academic_year);
            if(!empty($obj_levels))
            {
                $arr_levels = $obj_levels -> toArray();         
            }

            $this->arr_view_data['module_icon']    = $this->module_icon;
            $this->arr_view_data['arr_professors'] = $arr_professors;
            $this->arr_view_data['arr_levels']     = $arr_levels;
            $this->arr_view_data['obj_club']       = $obj_club;
            $this->arr_view_data['module_title']   = translation('add').' '.translation('student');

            return view($this->module_view_folder.'.add-student', $this->arr_view_data);
        }
        return redirect()->back();
    }

    /*
    | get_class() : get list of classes 
    | Auther        : Pooja K  
    | Date          : 18 JUne 2018
    */ 
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

    /*
    | get_student() : get list of students 
    | Auther        : Pooja K  
    | Date          : 18 JUne 2018
    */
    public function get_student(Request $request){

        $level_class_id = $request->level_class;

        $arr_students = $this->CommonDataService->get_students($level_class_id);

         $str_table = '';

        foreach($arr_students as $student){
            
            $count = $this->ClubStudentsModel
                                            ->where('club_id',$request->club_id)
                                            ->where('student_id',$student->user_id)
                                            ->count();
                                          
            if($count>0){
                $first_column = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($student->user_id).'" value="'.base64_encode($student->user_id).'" checked /><label for="mult_change_'.base64_encode($student->user_id).'"></label></div>';       
            }
            else{
                $first_column = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($student->user_id).'" value="'.base64_encode($student->user_id).'" /><label for="mult_change_'.base64_encode($student->user_id).'"></label></div>';          
            }

                
        
            $first_name =  isset($student->get_user_details->first_name) ? ucfirst($student->get_user_details->first_name) : '';
            $last_name =  isset($student->get_user_details->last_name) ? ucfirst($student->get_user_details->last_name): '';

            $second_column = $first_name.' '.$last_name;

            $third_column = isset($student->get_user_details->national_id) ? $student->get_user_details->national_id : '';

            $str_table .= '<tr><td>'.$first_column.'</td><td>'.$second_column.'</td><td>'.$third_column.'</td></tr>';

        }
    
        return $str_table;

    }

    /*
    | store_student() : store list of students 
    | Auther        : Pooja K  
    | Date          : 18 JUne 2018
    */
    public function store_student(Request $request,$id=FALSE){

        $arr_students=[];
        $arr_students = $request->checked_record;

       /* if(count($arr_students)==0){
            $this->ClubStudentsModel->where('club_id',$id)->delete();
            Flash::success(translation('students_added_in_club_successfully'));
            return redirect(url($this->module_url_path));
        }*/
        $club_name = $this->ClubModel->where('id',$id)->first();
        
        foreach($arr_students as$student){

            $arr_data = $permissions = $par_permissions = [];
            $arr_data['club_id']        = $id;
            $arr_data['student_id']     = base64_decode($student);
            $arr_data['level_class_id'] = $request->level_class_id;
            $this->ClubStudentsModel->updateOrCreate(['club_id'=>$arr_data['club_id'],'student_id'=>$arr_data['student_id']],$arr_data);

            $student = $this->StudentModel
                            ->with('notifications','parent_notifications','get_user_details','get_parent_details')
                            ->where('user_id',$arr_data['student_id'])
                            ->where('is_active',1)
                            ->where('has_left',0)
                            ->where('academic_year_id',$this->academic_year)
                            ->first();
                
            $first_name = isset($student['get_user_details']['first_name'])?ucwords($student['get_user_details']['first_name']):'';
            $last_name  = isset($student['get_user_details']['last_name'])?ucwords($student['get_user_details']['last_name']):'';
            $user_name  = $first_name.' '.$last_name;

            if(isset($student['notifications']['notification_permission']) && $student['notifications']['notification_permission']!=null )
            {
                $permissions = json_decode($student['notifications']['notification_permission'],true);
                $result = $this->send_notification($permissions,$student['get_user_details'],config('app.project.role_slug.student_role_slug'),$user_name,$club_name);
            }
            if(isset($student['parent_notifications']['notification_permission']) && $student['parent_notifications']['notification_permission']!=null)
            {
                $par_permissions = json_decode($student['parent_notifications']['notification_permission'],true);
                $result = $this->send_notification($par_permissions,$student['get_parent_details'],config('app.project.role_slug.parent_role_slug'),$user_name,$club_name);
            }


        }
        Flash::success(translation('students_added_in_club_successfully'));
        return redirect(url($this->module_url_path));
    }

    public function send_notification($permissions,$user,$role,$user_name,$club)
    {   
        $result = '';
        if(array_key_exists('club.app',$permissions))
        {
         
            $arr_notification = [];
            $arr_notification['school_id']          =  $this->school_id;
            $arr_notification['from_user_id']       =  $this->user_id;
            $arr_notification['to_user_id']         =  $user->id;
            $arr_notification['user_type']          =  config('app.project.role_slug.school_admin_role_slug');
            $arr_notification['notification_type']  =  'Club Members Added';
            $arr_notification['title']              =  'Added As Club Member:Student '.$user_name.' added in club: '.(isset($club->club_name)?ucwords($club->club_name):'').'.';
            $arr_notification['view_url']           =  url('/').'/'.$role.'/club';
            $result = $this->NotificationModel->create($arr_notification);
        }

         $details          = [
                                    'first_name'  =>  isset($user['first_name'])?ucwords($user['first_name']):'',
                                    'email'       =>  isset($user['email'])?$user['email']:'',
                                    'mobile_no'   =>  isset($user['mobile_no'])?$user['mobile_no']:'',
                                    'club_name'   =>  isset($club->club_name)?ucwords($club->club_name):''
                            ];

        if(array_key_exists('club.sms',$permissions))
        {
            $arr_sms_data = $this->built_sms_data($details,'member',$user_name);
            $email_status  = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id);
        }
        if (array_key_exists('club.email',$permissions))
        {
            $arr_mail_data = $this->built_mail_data($details,'member',$user_name);
            $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
        }
        return $result;
    }

    public function built_mail_data($arr_data,$user_type,$name=FALSE)
     {
        

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [];

            if($user_type == 'supervisor')
            {

                $arr_built_content = [
                                      'FIRST_NAME'         => $arr_data['first_name'],
                                      'CLUB_NAME'          => $arr_data['club_name'],
                                      'SCHOOL_ADMIN'       => $this->CommonDataService->get_school_name($this->school_id)
                                     ];    
            }
            elseif($user_type == 'member')
            {
                
                $arr_built_content = [
                                      'FIRST_NAME'         => $arr_data['first_name'],
                                      'CLUB_NAME'          => $arr_data['club_name'],
                                      'SCHOOL_ADMIN'       => $this->CommonDataService->get_school_name($this->school_id),
                                      'STUDENT_NAME'       => $name
                                     ];
                
            }
            
    
            $arr_mail_data                        = [];
            
            if($user_type == 'supervisor')
            {
                $arr_mail_data['email_template_slug'] = 'club_add_to_supervisor';                   
            }
            elseif($user_type == 'member')
            {   
                
                $arr_mail_data['email_template_slug'] = 'add_club';                       
            }
            
            $arr_mail_data['arr_built_content']   = $arr_built_content;
            $arr_mail_data['user']                = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_sms_data($arr_data,$user,$name=FALSE)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {

            $arr_built_content = [];

            if($user == 'supervisor')
            {
                $arr_built_content = [
                                      'CLUB_NAME'          => $arr_data['club_name']
                                     ];    
            }
            elseif($user == 'member')
            {
                $arr_built_content = [
                                      'CLUB_NAME'          => $arr_data['club_name'],
                                      'STUDENT_NAME'       => $name
                                     ];
                
            }

            $arr_sms_data                      = [];
            if($user == 'supervisor')
            {
                $arr_sms_data['sms_template_slug'] = 'club_add_to_supervisor';                   
            }
            elseif($user == 'member')
            {
                $arr_sms_data['sms_template_slug'] = 'add_club';                       
            }
                
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }
}
