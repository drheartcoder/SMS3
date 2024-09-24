<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TaskModel;
use App\Models\TaskStudentModel;
use App\Common\Traits\MultiActionTrait;
use App\Models\NotificationModel;
use App\Common\Services\CommonDataService;
use App\Common\Services\EmailService;
use Session;
use Validator;
use Flash;
use Sentinel;

class TaskController extends Controller{
    
    public function __construct(CommonDataService $CommonDataService,EmailService $EmailService){

        $this->CommonDataService            = $CommonDataService;
        $this->EmailService                 = $EmailService;
        $this->TaskModel                    = new TaskModel();
        $this->TaskStudentModel             = new TaskStudentModel();
        $this->NotificationModel            = new NotificationModel();
        $this->BaseModel = $this->TaskModel;
        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/task';
        $this->module_title                 = translation('task');
        
        $this->module_view_folder           = "schooladmin.task";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-tasks';
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

    public function index(){
        $arr_tasks=[];
        $obj_tasks = $this->TaskModel
                                    ->with('get_user')
                                    ->with('get_task_users.get_user')
                                    ->with('get_supervisor')
                                    ->whereHas('get_task_users.get_user',function(){})   
                                     ->with('get_task_users.get_user')
                                    ->where('school_id',$this->school_id)
                                    ->where('academic_year_id',$this->academic_year)
                                    ->orderBy('id','DESC')
                                    ->get();
        if(isset($obj_tasks) && !empty($obj_tasks)){
            $arr_tasks =  $obj_tasks -> toArray();
        }                            
        
        $this->arr_view_data['module_title']  =  translation('manage').' '.$this->module_title;
        $this->arr_view_data['arr_tasks']  =  $arr_tasks;
        $this->arr_view_data['current_user']  =  $this->user_id;
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function create(){

        $arr_levels = $arr_class =[];
        $obj_level = $this->CommonDataService->get_levels($this->academic_year);
        if(isset($obj_level) && !empty($obj_level)){

            $arr_levels = $obj_level->toArray();
        }

        $arr_employees = $this->CommonDataService->get_employees();

        $this->arr_view_data['arr_levels']  =  $arr_levels;
        $this->arr_view_data['module_icon']  =  $this->module_icon;
        $this->arr_view_data['arr_employees']  =  $arr_employees;
        $this->arr_view_data['module_title'] =  translation('add').' '.$this->module_title;

        return view($this->module_view_folder.'.create', $this->arr_view_data);
    }

    public function get_professors(){
        
        $options = "<option value=''>".translation('select')."</option>";

        $arr_professors = $this->CommonDataService->get_professor_by_year($this->school_id,$this->academic_year);

        if(count($arr_professors)>0)
        {
            foreach($arr_professors as $professor)
            {
                $options .= "<option value='".$professor->user_id."'>".ucwords($professor->user_name)."</option>";
            }
        }
        
        return $options;

    }

    public function get_employees(){
            
        $options = "<option value=''>".translation('select')."</option>";   

        $arr_employee = $this->CommonDataService->get_employees();

        if(count($arr_employee)>0)
        {
            foreach($arr_employee as $employee)
            {
                $options .= "<option value='".$employee->user_id."'>".ucwords($employee->user_name)."</option>";
            }
        }
        
        return $options;
        
    }

    public function store(Request $request){
        
        $messages = $arr_rules = $arr_permissions = [];
        $form_data                    = $request->all();
        if($request->input('user_type')!= 'employee')
        {
            $arr_rules['individual']      = 'required';
        }
        
        $arr_rules['task_name']       = 'required|regex:/^[a-zA-Z \-]+$/';
        $arr_rules['priority']        = 'required|alpha';
        $arr_rules['description']     = 'required';
        $arr_rules['submission_date'] = 'required|date';
        $arr_rules['submission_time'] = 'required';
        $arr_rules['supervisor_type'] = 'required|alpha';
        $arr_rules['supervisor']      = 'required|numeric';
        $arr_rules['status']          = 'required|regex:/^[a-zA-Z \_]+$/';

        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'digits_between:10,14' => translation('please_enter_telephone_no_within_range_of_10_-_14'),
                            'digits_between:6,14'  => translation('please_enter_telephone_no_within_range_of_6_-_14'),
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_eRnter_valid_date'),
                            'alpha'                => translation('please_enter_letters_only')  
                        );
        
        $validator                          = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails()){

            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 
        
        $user_type = isset($request->user_type) ? $request->user_type : '';
        

        $individual      = $request->individual;
        $class           = $request->has('class') ? $request->class : 0 ;
        $task_name       = $request->task_name;
        $priority        = $request->priority;
        $description     = $request->description;
        $submission_date = $request->submission_date;
        $supervisor_type = $request->supervisor_type;
        $supervisor      = $request->supervisor;
        $status          = $request->status;

        $submission_time = $request->submission_time;
        if($submission_time!='00:00:00'){
            $submission_time = date_create($submission_time);
            $submission_time = date_format($submission_time,'H:i:s');
        }

        $arr_data = [];
        $arr_data['school_id'] = $this->school_id;
        if($individual=='red'){

            $arr_data['is_individual'] = 1;
            if($request->class!=''){
                $arr_data['level_class_id'] = $class;    
            }
            else{
                $arr_data['level_id'] = $request->level;    
            }
            

        }
        else{

            $arr_data['is_individual'] = 0;       
        }

        $arr_data['task_name'] = trim($task_name);
        $arr_data['priority'] = $priority;
        $arr_data['user_role'] = $user_type;
        $arr_data['task_submission_date'] = $submission_date;
        $arr_data['task_submission_time'] = $submission_time;
        $arr_data['task_description'] = trim($description);
        $arr_data['task_supervisor_id'] = $supervisor;
        $arr_data['task_status'] = $status;
        $arr_data['academic_year_id'] = $this->academic_year;
        $arr_data['supervisor_role'] = $supervisor_type;
        $arr_data['added_by'] = $this->user_id;

        $task = $this->TaskModel->create($arr_data);

        if($supervisor_type == config('app.project.role_slug.employee_role_slug'))
        {
            $obj_employee = $this->CommonDataService->get_user_permissions($supervisor,$supervisor_type,$this->academic_year);
            if(isset($obj_employee['notifications']['notification_permission']) && $obj_employee['notifications']['notification_permission']!=null)
            {
                $arr_permissions = json_decode($obj_employee['notifications']['notification_permission'],true);
                if(count($arr_permissions)>0 && $arr_permissions!=null)
                {
                    $result = $this->send_notifications($arr_permissions,$obj_employee['get_user_details'],$obj_employee['user_id'],trim($user_type),$task,'supervisor');    
                }
                
            }
        }
        else
        {
            $obj_professor = $this->CommonDataService->get_user_permissions($supervisor,$supervisor_type,$this->academic_year);
            if(isset($obj_professor['notifications']['notification_permission']) && $obj_professor['notifications']['notification_permission']!=null)
            {
                $arr_permissions = json_decode($obj_professor['notifications']['notification_permission'],true);
                if(count($arr_permissions)>0 && $arr_permissions!=null)
                {
                    $result = $this->send_notifications($arr_permissions,$obj_professor['get_user_details'],$obj_professor['user_id'],trim($user_type),$task,'supervisor');
                }
            }
        }

        if(config('app.project.role_slug.employee_role_slug')==trim($user_type)) {
            $arr_employee=[];

            //$arr_employee = $this->CommonDataService->get_employees();
            $arr_employee = $this->CommonDataService->get_permissions(trim($user_type),$this->academic_year,$this->school_id);
            
            if(count($arr_employee)>0){
                foreach($arr_employee as $employee){
                    $arr_data = $arr_permissions = [];
                    $arr_data['task_id'] = isset($task->id) ? $task->id : 0;
                    $arr_data['user_id'] = isset($employee['user_id'])?$employee['user_id']:0;
                    $arr_data['status'] = 'PENDING';
                    $this->TaskStudentModel->create($arr_data);

                    if(isset($employee['notifications']['notification_permission']) && $employee['notifications']['notification_permission']!=null)
                    {
                        $arr_permissions = json_decode($employee['notifications']['notification_permission'],true);
                        if(count($arr_permissions)>0 && $arr_permissions!=null)
                        {
                            $result = $this->send_notifications($arr_permissions,$employee['get_user_details'],$employee['user_id'],trim($user_type),$task,'users');
                        }
                    }
                    
                }
            }
        }
        if(config('app.project.role_slug.student_role_slug')==trim($user_type)) {
            if($individual=='red'){
                if($request->class!=''){
                                   
                    $obj_students = $this->CommonDataService->get_students($class); 
                }
                else{

                    $level = $request->level;
                    $obj_students = $this->CommonDataService->get_students(0,$level);     
                }
                    
            }
            else{
                $obj_students = $this->CommonDataService->get_students();
            }

            if(isset($obj_students) && !empty(($obj_students))){
                $arr_students = $obj_students -> toArray();

                if(count($arr_students)>0){
                    foreach($arr_students as $student){

                        $arr_data =[];
                        $arr_data['task_id'] = isset($task->id) ? $task->id : 0;
                        $arr_data['user_id'] = isset($student['user_id']) ? $student['user_id']:0;
                        $arr_data['status'] = 'PENDING';
                        $this->TaskStudentModel->create($arr_data);

                        if(isset($student['notifications']['notification_permission']) && $student['notifications']['notification_permission']!=null)
                        {
                            $arr_permissions = json_decode($student['notifications']['notification_permission'],true);
                            $result = $this->send_notifications($arr_permissions,$student['get_user_details'],$student['user_id'],trim($user_type),$task,'users');
                        }
                    }       
                }
            }
        }

        if(config('app.project.role_slug.professor_role_slug')==trim($user_type)) {
            if($individual=='red'){
                if($request->class!=''){
                    $obj_professors = $this->CommonDataService->get_professor_by_level_class($class);
                }
                else
                {
                    $level = $request->level;
                    $obj_professors = $this->CommonDataService->get_professor_by_level($level);
                }
                if(isset($obj_professors) && !empty(($obj_professors))){
                    $arr_professors = $obj_professors -> toArray();
                    
                    if(count($arr_professors)>0){
                        
                        foreach($arr_professors as $professor){
                           
                            $arr_data =[];
                            $arr_data['task_id'] = isset($task->id) ? $task->id : 0;
                            $arr_data['user_id'] = isset($professor['user_details']['id'])?$professor['user_details']['id']:0;
                            $arr_data['status'] = 'PENDING';
                            $this->TaskStudentModel->create($arr_data);

                            if(isset($professor['notifications']['notification_permission']) && $professor['notifications']['notification_permission']!=null)
                            {
                                $arr_permissions = json_decode($professor['notifications']['notification_permission'],true);
                                $result = $this->send_notifications($arr_permissions,$professor['user_details'],$professor['professor_id'],trim($user_type),$task,'users');
                            }
                        }      
                    }
                }
            }
            else{
                //$arr_professors = $this->CommonDataService->get_professor_by_year();
                $arr_professors = $this->CommonDataService->get_permissions(trim($user_type),$this->academic_year,$this->school_id);
                
                if(isset($arr_professors) && !empty(($arr_professors))){
                    
                        foreach($arr_professors as $professor){
                            $arr_data =[];
                            $arr_data['task_id'] = isset($task->id) ? $task->id : 0;
                            $arr_data['user_id'] = $professor['user_id'];
                            $arr_data['status'] = 'PENDING';
                            $this->TaskStudentModel->create($arr_data);

                            if(isset($professor['notifications']['notification_permission']) && $professor['notifications']['notification_permission']!=null)
                            {
                                $arr_permissions = json_decode($professor['notifications']['notification_permission'],true);
                                $result = $this->send_notifications($arr_permissions,$professor['get_user_details'],$professor['user_id'],trim($user_type),$task,'users');
                            }

                        }      
                    
                }
            }
            
        }

        if(config('app.project.role_slug.parent_role_slug')==trim($user_type)) {
            if($individual=='red'){
                if($request->class!=''){
                    $obj_parent = $this->CommonDataService->get_parent(0,$class);
                }
                else
                {
                    $level = $request->level;
                    $obj_parent = $this->CommonDataService->get_parent($level);                    
                }
            }
            else{
                $obj_parent = $this->CommonDataService->get_parent();
            }

                if(isset($obj_parent) && !empty(($obj_parent))){
                    $arr_parent = $obj_parent -> toArray();

                    if(count($arr_parent)>0){
                        foreach($arr_parent as $parent){
                            $arr_data =[];
                            $arr_data['task_id'] = isset($task->id) ? $task->id : 0;
                            $arr_data['user_id'] = $parent['parent_id'];
                            $arr_data['status'] = 'PENDING';
                            $this->TaskStudentModel->create($arr_data);

                            if(isset($parent['parent_notifications']['notification_permission']) && $parent['parent_notifications']['notification_permission']!=null)
                            {
                                $arr_permissions = json_decode($parent['parent_notifications']['notification_permission'],true);
                                $result = $this->send_notifications($arr_permissions,$parent['get_parent_details'],$parent['parent_id'],trim($user_type),$task,'users');
                            }
                        }      
                    }
                }
        }

        $count = $this->TaskStudentModel->where('task_id',$task->id)->count();
        if($count==0){
            $this->TaskModel->where('id',$task->id)->delete();
            Flash::error(translation('no_users_available'));    
            return redirect()->back();
        }

        Flash::success(translation('task_added_successfully'));
        return redirect()->back();
    }

    public function getClasses(Request $request){
        $level_id = $request->input('level');

        $options ='<option value="">'.translation('select_class').'</option>';

        $obj_class = $this->CommonDataService->get_class($level_id);
    
        if(!empty($obj_class))
        {
            $arr_class  = $obj_class -> toArray();
            if(count($arr_class)>0)
            {
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

    public function change_status(Request $request){

        $status = $request->input('status');
        $id = $request->input('id');
        
        $this->TaskModel->where('id',$id)->update(['task_status'=>$status]);
    }

    public function view($enc_id=FALSE){

        $id = base64_decode($enc_id);

        $obj_task = $this->TaskModel
                         ->whereHas('get_supervisor',function(){})
                         ->with('get_supervisor','level_details','get_level_class.level_details')
                         ->with('get_supervisor','get_level_class.class_details')
                         ->whereHas('get_task_users.get_user',function(){})   
                         ->with('get_task_users.get_user')
                         ->where('id',$id)
                         ->first();

        if(isset($obj_task) && !empty($obj_task)){

            $arr_data = $obj_task -> toArray();
            $task_users = $arr_data['get_task_users'] ;
            if(count($task_users)>0){
                $this->arr_view_data['added_by']  = ($arr_data['added_by'] == $this->user_id) ? 'yes':'no';        
                $this->arr_view_data['arr_data']  = $task_users;
                $this->arr_view_data['task']  = $arr_data;
                $this->arr_view_data['current_user']  = $this->user_id;
                $this->arr_view_data['module_title'] = translation('view').' '.$this->module_title;
                return view($this->module_view_folder.'.view', $this->arr_view_data);    
            }
            
        }
        Flash::error(translation('no_data_available'));
        return redirect()->back();              
    }

    public function change_user_status(Request $request){
        $status = $request->input('status');
        $id = $request->input('id');
        $task = $this->TaskStudentModel->where('id',$id)->first();
        if($task)
        {
            $this->TaskStudentModel->where('id',$id)->update(['status'=>$status]);
            $count = $this->TaskStudentModel->where('task_id',$task->task_id)->where('status','PENDING')->count();
            if($count==0)
            {   
                $this->TaskModel->where('id',$task->task_id)->update(['task_status'=>'CLOSED']);
            }    
        }
    }

    public function edit($enc_id){

        $arr_data = [];

        $id = base64_decode($enc_id);
        if(!is_numeric($id)){

            Flash::error('something_went_wrong');
            return redirect($this->module_url_path);
        }
        $obj_task = $this->TaskModel
                                    ->with('get_level_class.level_details','get_level_class.class_details','level_details')
                                    ->where('id',$id)->first();

        if(isset($obj_task) && !empty($obj_task)){

                $arr_data = $obj_task->toArray();

                
                $arr_users =[];
                
                $users = explode(',',$arr_data['user_role']);
                $arr_data['user_role'] = $users;  
                    
                $submission_date = $arr_data['task_submission_date'];

                $submission_time = date_create($arr_data['task_submission_time']);
                $submission_time = date_format($submission_time,'h:i a');

                $arr_data['submission_date'] = $submission_date ; 
                $arr_data['submission_time'] = $submission_time ; 

                if($arr_data['supervisor_role']=='employee'){
                    $arr_employees = $this->CommonDataService->get_employees();    
                }
                else{

                    $arr_employees = $this->CommonDataService->get_professor_by_year($this->school_id,$this->academic_year);
                }
                

                $this->arr_view_data['arr_data']  = $arr_data;
                $this->arr_view_data['arr_employees']  = $arr_employees;
                $this->arr_view_data['module_title'] = translation('edit').' '.$this->module_title;
                return view($this->module_view_folder.'.edit', $this->arr_view_data);    
        }

        Flash::success(translation('no_data_available'));
        return redirect()->back();
    }

    public function update(Request $request,$enc_id=FALSE){

        $id = base64_decode($enc_id);
        if(!is_numeric($id)){
            
            Flash::error('something_went_wrong');
            return redirect($this->module_url_path);
        }

        $messages = $arr_rules = [];
        $form_data = $request->all();

        $arr_rules['task_name']       = 'required|regex:/^[a-zA-Z \-]+$/';
        $arr_rules['priority']        = 'required|alpha';
        $arr_rules['description']     = 'required';
        $arr_rules['submission_date'] = 'required|date';
        $arr_rules['submission_time'] = 'required';
        $arr_rules['supervisor_type'] = 'required|alpha';
        $arr_rules['supervisor']      = 'required';
        $arr_rules['status']          = 'required|regex:/^[a-zA-Z \_]+$/';

        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'digits_between:10,14' => translation('please_enter_telephone_no_within_range_of_10_-_14'),
                            'digits_between:6,14'  => translation('please_enter_telephone_no_within_range_of_6_-_14'),
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'),
                            'alpha'                => translation('please_enter_letters_only')  
                        );
        
        $validator                          = Validator::make($request->all(),$arr_rules,$messages);
     
        if($validator->fails()){

            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 


        $task_name       = $request->task_name;
        $priority        = $request->priority;
        $description     = $request->description;
        $submission_date = $request->submission_date;
        $supervisor_type = $request->supervisor_type;
        $supervisor = $request->supervisor;
        $status = $request->status;

        $submission_time = $request->submission_time;
        if($submission_time!='00:00:00'){
            $submission_time = date_create($submission_time);
            $submission_time = date_format($submission_time,'H:i:s');
        }

        $task = $this->TaskModel->where('id',$id)->first();
        
        $arr_data = [];
        $arr_data['task_name'] = $task_name;
        $arr_data['priority'] = $priority;
        $arr_data['task_submission_date'] = $submission_date;
        $arr_data['task_submission_time'] = $submission_time;
        $arr_data['task_description'] = $description;
        $arr_data['task_supervisor_id'] = $supervisor;
        $arr_data['task_status'] = $status;
        $arr_data['academic_year_id'] = $this->academic_year;
        $arr_data['supervisor_role'] = $supervisor_type;

        $this->TaskModel->where('id',$id)->update($arr_data);
        $task2 = $this->TaskModel->where('id',$id)->first();
        $previous_date = date($task->task_submission_date);

        $nxt_date      = date($submission_date);
        $current_date  = date('Y-m-d');
        
        if(($previous_date!=$nxt_date) && ($nxt_date>$current_date))
        {
            if(($previous_date<$nxt_date) || ($nxt_date<$previous_date) || (strtotime($task->task_submission_time)!=strtotime($submission_time)) || (strtotime($task->task_submission_time)!=strtotime($submission_time)))
            {
                $result = $this->notifications($task,'update',$arr_data,'users');
                $result = $this->notifications($task,'update',$arr_data,'supervisor');
            }
        }

        if($task->task_supervisor_id != $arr_data['task_supervisor_id'])
        {
            if($arr_data['supervisor_role'] == config('app.project.role_slug.employee_role_slug'))
            {
                $obj_employee = $this->CommonDataService->get_user_permissions($arr_data['task_supervisor_id'],$arr_data['supervisor_role'],$this->academic_year);
                if(isset($obj_employee['notifications']['notification_permission']) && $obj_employee['notifications']['notification_permission']!=null)
                {
                    $arr_permissions = json_decode($obj_employee['notifications']['notification_permission'],true);
                    $result = $this->send_notifications($arr_permissions,$obj_employee['get_user_details'],$obj_employee['user_id'],$arr_data['supervisor_role'],$task2,'supervisor');
                }
            }
            else
            {
                $obj_professor = $this->CommonDataService->get_user_permissions($arr_data['task_supervisor_id'],$arr_data['supervisor_role'],$this->academic_year);
                if(isset($obj_professor['notifications']['notification_permission']) && $obj_professor['notifications']['notification_permission']!=null)
                {
                    $arr_permissions = json_decode($obj_professor['notifications']['notification_permission'],true);
                    $result = $this->send_notifications($arr_permissions,$obj_professor['get_user_details'],$obj_professor['user_id'],$arr_data['supervisor_role'],$task2,'supervisor');
                }
            }
        }

        Flash::success(translation('task_updated_successfully'));
        return redirect()->back();
    }

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
               Flash::success($this->module_title.' '.translation('deleted_succesfully')); 
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
        $this->TaskStudentModel->where('task_id',$id)->delete();
        if($delete)
        {  
            return TRUE;
        }

        return FALSE;
    }

    public function send_notifications($arr_permissions,$user_details,$user_id,$role,$task,$user,$arr_data=FALSE,$status=FALSE)
    {
        
        if(array_key_exists('task.app',$arr_permissions))
        {
         
            $arr_notification = [];
            $arr_notification['school_id']          =  $this->school_id;
            $arr_notification['from_user_id']       =  $this->user_id;
            $arr_notification['to_user_id']         =  $user_id;
            $arr_notification['user_type']          =  config('app.project.role_slug.school_admin_role_slug');

            if($status && $status=='update')
            {
                $arr_notification['notification_type']  =  'Task Edit';
                $arr_notification['title']              =  'Task Edit:Submission date of task '.ucwords($task->task_name).' assigned to you is changed from '.getDateFormat($task->task_submission_date).' '.getTimeFormat($task->task_submission_time).' to '.getDateFormat($arr_data['task_submission_date']).' '.getTimeFormat($arr_data['task_submission_time']);
                if($user == 'supervisor')
                {
                    $arr_notification['view_url']           =  url('/').'/'.$arr_data['supervisor_role'].'/task'; 
                }
                else
                {
                    $arr_notification['view_url']           =  url('/').'/'.$role.'/task'; 
                }
            }
            else
            {
                $arr_notification['notification_type']  =  'Task Added';
                if($user == 'supervisor')
                {
                    $arr_notification['title']              =  'Task Added: New Task '.ucwords($task->task_name).' is assigned to '.$task->user_role.' and it\'s submission date & time is '.getDateFormat($task->task_submission_date).' '.getTimeFormat($task->task_submission_time).' & you are it\'s supervisor';     
                    $arr_notification['view_url']           =  url('/').'/'.$task->supervisor_role.'/task';       
                }
                else
                {
                    $arr_notification['title']              =  'Task Added:New Task '.ucwords($task->task_name).' is assigned to you and it\'s submission date & time is '.getDateFormat($task->task_submission_date).' '.getTimeFormat($task->task_submission_time);   
                    $arr_notification['view_url']           =  url('/').'/'.$role.'/task';         
                }
                
            }

            $result = $this->NotificationModel->create($arr_notification);
        }

        $details          = [
                                    'first_name'  =>  isset($user_details['first_name'])?ucwords($user_details['first_name']):'',
                                    'user_role'   =>  isset($task->user_role)?ucwords($task->user_role):'',
                                    'email'       =>  isset($user_details['email'])?$user_details['email']:'',
                                    'mobile_no'   =>  isset($user_details['mobile_no'])?$user_details['mobile_no']:'',
                                    'task_name'   =>  isset($task->task_name)?ucwords($task->task_name):'',
                                    'date'        =>  isset($task->task_submission_date)?getDateFormat($task->task_submission_date):''
                            ];
        if(array_key_exists('task.sms',$arr_permissions))
        {
            if($user =='supervisor')
            {
                $arr_sms_data = $this->built_sms_data($details,$status,'supervisor',$arr_data);
            }
            else
            {
                $arr_sms_data = $this->built_sms_data($details,$status,'',$arr_data);
            }
            $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
        }
        if (array_key_exists('task.email',$arr_permissions))
        {
            if($user =='supervisor')
            {
                $arr_mail_data = $this->built_mail_data($details,$status,'supervisor',$arr_data);
            }
            else
            {
                $arr_mail_data = $this->built_mail_data($details,$status,'',$arr_data);
            }
            //$arr_mail_data = $this->built_mail_data($details,'add',$arr_data); 
            $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
        }
        return $result;
    }

    public function notifications($task,$status,$arr_data,$user)
    {
        $result = '';
        $arr_details = [];
        if($user != 'supervisor')
        {
            if($task->is_individual == 1)
            {
                if($task->user_role == config('app.project.role_slug.student_role_slug'))
                {
                    if($task->level_class_id!=0)
                    {
                        $arr_details = $this->CommonDataService->get_students($task->level_class_id);
                    }
                    elseif($task->level_id!=0)
                    {
                        $arr_details = $this->CommonDataService->get_students(0,$task->level_id);
                    }
                }
                elseif($task->user_role == config('app.project.role_slug.parent_role_slug'))
                {
                    if($task->level_class_id!=0)
                    {
                        $arr_details = $this->CommonDataService->get_parent(0,$task->level_class_id);
                    }
                    elseif($task->level_id!=0)
                    {
                        $arr_details = $this->CommonDataService->get_parent($task->level_id);
                    }

                }
                elseif($task->user_role == config('app.project.role_slug.professor_role_slug'))
                {
                    if($task->level_class_id!=0)
                    {
                        $obj_details = $this->CommonDataService->get_professor_by_level_class($task->level_class_id);
                        if(isset($obj_details) && $obj_details!=null && count($obj_details)>0)
                        {
                            $arr_details = $obj_details->toArray();
                        }
                    }
                    elseif($task->level_id!=0)
                    {
                        $obj_details = $this->CommonDataService->get_professor_by_level($task->level_id);
                        if(isset($obj_details) && $obj_details!=null && count($obj_details)>0)
                        {
                            $arr_details = $obj_details->toArray();
                        }
                    }
                }
            }
            else
            {
                $data = $this->CommonDataService->get_permissions($task->user_role,$this->academic_year,$this->school_id);
                if(isset($arr_data) && count($arr_data)>0)
                {
                    foreach ($data as $key => $value) {
                        if(isset($value['notifications']['notification_permission']) && $value['notifications']['notification_permission']!=null)
                        {   
                            $arr_permissions = json_decode($value['notifications']['notification_permission'],true);
                            $user_id = '';
                            if($task->user_role == config('app.project.role_slug.parent_role_slug'))
                            {
                                $user_id = $value['parent_id'];
                            }
                            else
                            {
                                $user_id = $value['user_id'];
                            }
                            $result = $this->send_notifications($arr_permissions,$value['get_user_details'],$user_id,$task->user_role,$task,$user,$arr_data,'update');
                        }
                    }
                }
            }

            if(isset($arr_details) && count($arr_details)>0)
            {
                
                foreach ($arr_details as $key => $value) {
                    $arr_permissions = [];
                    $user_id = '';
                    
                    if($task->user_role==config('app.project.role_slug.parent_role_slug'))
                    {
                        if(isset($value['parent_notifications']['notification_permission']) && $value['parent_notifications']['notification_permission']!=null)
                        {   
                            $arr_permissions = json_decode($value['parent_notifications']['notification_permission'],true);
                            $user_id = $value['parent_id'];
                            $result = $this->send_notifications($arr_permissions,$value['get_user_details'],$user_id,$task->user_role,$task,$user,$arr_data,'update');
                        }
                    }
                    elseif($task->user_role==config('app.project.role_slug.professor_role_slug'))
                    {
                        if(isset($value['notifications']['notification_permission']) && $value['notifications']['notification_permission']!=null)
                        {   
                            $arr_permissions = json_decode($value['notifications']['notification_permission'],true);
                            $user_id = $value['professor_id'];
                            $result = $this->send_notifications($arr_permissions,$value['get_user_details'],$user_id,$task->user_role,$task,$user,$arr_data,'update');
                        }
                    }
                    else
                    {
                        if(isset($value['notifications']['notification_permission']) && $value['notifications']['notification_permission']!=null)
                        {   
                            $arr_permissions = json_decode($value['notifications']['notification_permission'],true);
                            $user_id = $value['user_id'];
                            $result = $this->send_notifications($arr_permissions,$value['get_user_details'],$user_id,$task->user_role,$task,$user,$arr_data,'update');
                        }
                    }
                }
            }
        }
        else
        {
            if($task->supervisor_role==config('app.project.role_slug.professor_role_slug'))
            {
               $supervisor_data = $this->CommonDataService->get_user_permissions($task->task_supervisor_id,$task->supervisor_role,$this->academic_year);
               if(isset($supervisor_data['notifications']['notification_permission']) &&$supervisor_data['notifications']['notification_permission']!=null)
               {
                    $arr_permissions = json_decode($supervisor_data['notifications']['notification_permission'],true);
                    $result = $this->send_notifications($arr_permissions,$supervisor_data['get_user_details'],$supervisor_data['user_id'],$task->supervisor_role,$task,'supervisor',$arr_data,'update') ;
               }               

            }
            if($task->supervisor_role==config('app.project.role_slug.employee_role_slug'))
            {
               $supervisor_data = $this->CommonDataService->get_user_permissions($task->task_supervisor_id,$task->supervisor_role,$this->academic_year);
               if(isset($supervisor_data['notifications']['notification_permission']) &&$supervisor_data['notifications']['notification_permission']!=null)
               {
                    $arr_permissions = json_decode($supervisor_data['notifications']['notification_permission'],true);
                    $result = $this->send_notifications($arr_permissions,$supervisor_data['get_user_details'],$supervisor_data['user_id'],$task->supervisor_role,$task,'supervisor',$arr_data,'update') ;
               }
            }
            
        }
    }

    public function built_mail_data($arr_data,$status=FALSE,$supervisor=FALSE,$update_data=FALSE)
     {
        

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [];

            if($status == 'update')
            {
                $arr_built_content = [
                                      'FIRST_NAME'         => $arr_data['first_name'],
                                      'TASK_NAME'          => $arr_data['task_name'],
                                      'SCHOOL_ADMIN'       => $this->CommonDataService->get_school_name($this->school_id),
                                      'PREVIOUS_DATE'      => $arr_data['date'],
                                      'NEXT_DATE'          => getDateFormat($update_data['task_submission_date'])
                                     ];    
            }
            else
            {

                if($supervisor!='')
                {
                    $arr_built_content = [
                                      'FIRST_NAME'         => $arr_data['first_name'],
                                      'TASK_NAME'          => $arr_data['task_name'],
                                      'SCHOOL_ADMIN'       => $this->CommonDataService->get_school_name($this->school_id),
                                      'SUBMISSION_DATE'    => $arr_data['date'],
                                      'USER_GROUP'         => $arr_data['user_role']
                                     ];     
                }
                else
                {
                    $arr_built_content = [
                                      'FIRST_NAME'         => $arr_data['first_name'],
                                      'TASK_NAME'          => $arr_data['task_name'],
                                      'SCHOOL_ADMIN'       => $this->CommonDataService->get_school_name($this->school_id),
                                      'SUBMISSION_DATE'    => $arr_data['date']
                                     ];         
                }
                
            }
            
    
            $arr_mail_data                        = [];
            if($status =='update')
            {
                $arr_mail_data['email_template_slug'] = 'edit_task';                
            }
            else
            {
                if($supervisor!='')
                {
                    $arr_mail_data['email_template_slug'] = 'add_task_to_supervisor';                   
                }
                else
                {
                    $arr_mail_data['email_template_slug'] = 'add_task';                       
                }
                
            }
            
            $arr_mail_data['arr_built_content']   = $arr_built_content;
            $arr_mail_data['user']                = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_sms_data($arr_data,$status=FALSE,$supervisor=FALSE,$update_data=FALSE)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {

            $arr_built_content = [];

            if($status == 'update')
            {
                $arr_built_content = [
                                      'TASK_NAME'          => $arr_data['task_name'],
                                      'PREVIOUS_DATE'      => $arr_data['date'],
                                      'NEXT_DATE'          => getDateFormat($update_data['task_submission_date'])
                                     ];    
            }
            else
            {

                if($supervisor!='')
                {
                    $arr_built_content = [
                                      'TASK_NAME'          => $arr_data['task_name'],
                                      'USER_GROUP'         => $arr_data['user_role']
                                     ];     
                }
                else
                {
                    $arr_built_content = [
                                      'TASK_NAME'          => $arr_data['task_name'],
                                      'SUBMISSION_DATE'    => $arr_data['date']
                                     ];         
                } 
            }

            $arr_sms_data                      = [];
            if($status =='update')
            {
                $arr_sms_data['sms_template_slug'] = 'edit_task';                
            }
            else
            {
                if($supervisor!='')
                {
                    $arr_sms_data['sms_template_slug'] = 'add_task_to_supervisor';                   
                }
                else
                {
                    $arr_sms_data['sms_template_slug'] = 'add_task';                       
                }
                
            }
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }
}
