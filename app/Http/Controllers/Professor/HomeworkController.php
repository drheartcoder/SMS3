    <?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\HomeworkModel;
use App\Models\HomeworkStudentModel;
use App\Models\LevelClassModel;
use App\Models\LevelTranslationModel;
use App\Models\ClassTranslationModel;
use App\Models\CourseTranslationModel;
use App\Models\CourseModel;
use App\Models\StudentModel;
use App\Models\SchoolAdminModel;
use App\Models\NotificationModel;
use App\Common\Services\EmailService;

use App\Common\Services\CommonDataService;

use Session;
use Sentinel;
use DB;
use PDF;
use Datatables;
use Validator;
use Flash;

class HomeworkController extends Controller
{
	
    public function __construct(HomeworkModel $homework_model,
                                LevelClassModel $level_class,
                                LevelTranslationModel $level_translation,
                                ClassTranslationModel $class_translation,
                                CourseTranslationModel $course_translation,
                                CommonDataService $common_data_service,
                                HomeworkStudentModel $homework_student,
                                StudentModel $student,
                                SchoolAdminModel $SchoolAdminModel,
                                NotificationModel $NotificationModel,
                                EmailService $EmailServices,
                                CourseModel $CourseModel)
    {
    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.professor_panel_slug')).'/homework';
        $this->module_title                 = translation('homework');
 
        $this->module_view_folder           = "professor.homework";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-book';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');

    	$this->HomeworkModel = $homework_model;
        $this->LevelClassModel    = $level_class;
        $this->LevelTranslationModel = $level_translation;
        $this->ClassTranslationModel = $class_translation;
        $this->CourseTranslationModel = $course_translation;
        $this->CommonDataService      = $common_data_service;
        $this->HomeworkStudentModel   = $homework_student;
        $this->StudentModel           = $student;
        $this->SchoolAdminModel       = $SchoolAdminModel;
        $this->NotificationModel      = $NotificationModel;
        $this->EmailService           = $EmailServices;
        $this->CourseModel            = $CourseModel;

    	$this->arr_view_data['page_title']      = translation('homework');
    	$this->arr_view_data['module_url_path'] = $this->module_url_path;
    	$this->arr_view_data['theme_color']     = $this->theme_color;
    	$this->arr_view_data['module_icon']     = $this->module_icon;
    	$this->arr_view_data['edit_icon']       = $this->edit_icon;
    	$this->arr_view_data['create_icon']     = $this->create_icon;

        $this->first_name  = $this->last_name = $this->school_admin_id = $this->user_id = '';
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
                                ->with('notification_permissions')
                                ->where('school_id',$this->school_id)
                                ->first();

        if(isset($obj_permissions) && count($obj_permissions)>0 && !is_null($obj_permissions))
        {
            $arr_permissions       = $obj_permissions->toArray();
            $this->school_admin_id = $arr_permissions['user_id'];

            if(isset($arr_permissions['notification_permissions']['notification_permission']) && !empty($arr_permissions['notification_permissions']['notification_permission']))
            {   
                $this->permissions = json_decode($arr_permissions['notification_permissions']['notification_permission'],true);   
            }
        }
    }
    /*
    | index() : redirecting to homework listing  
    | Auther        : Pooja K  
    | Date          : 4 Jun 2018
    */
    public function index()
    {
    	$obj_homework = $this->HomeworkModel->where('homework_added_by',$this->user_id)->get();

    	if($obj_homework)
    	{
    		$arr_data = $obj_homework->toArray();
    	}
        $this->arr_view_data['arr_data'] = $arr_data;
        
        $this->arr_view_data['module_title']    = translation("manage")." ".$this->module_title;
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*
    | get_records() : homework listing using ajax 
    | Auther        : Pooja K  
    | Date          : 4 Jun 2018
    */ 
    public function get_records(Request $request)
    {
        $obj_custom = $this->get_homework_records($request);

        $role = Sentinel::findRoleBySlug(config('app.project.role_slug.professor_role_slug'));
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;

        $json_result  = Datatables::of($obj_custom);

        $json_result = $json_result->blacklist(['id']);                    

  
        $json_result =  $json_result->editColumn('due_date',function($data)
                        {
                            return getDateFormat($data->due_date);
                        })
                        ->editColumn('added_date',function($data)
                        {
                            return getDateFormat($data->added_date);
                        })
                        ->editColumn('build_status',function($data)
                        {
                            $status = '';

                            $count = $this->HomeworkStudentModel->where('homework_id',$data->homework_id)
                                                                ->where("status",'<>','COMPLETED')
                                                                ->count();

                            if($count==0)
                            {
                                $status = '<a class="light-blue-color" style="color:white">&nbsp;'.translation('completed').'&nbsp;</a>';
                            }
                            else
                            {
                                $status = '<a class="light-red-color" style="color:white">&nbsp;&nbsp;&nbsp;&nbsp;'.translation('pending').'&nbsp;&nbsp;&nbsp;</a>';
                            }

                            return $status;
                        })
                        ->editColumn('build_action',function($data)
                        {
                            $view_href =  $this->module_url_path.'/view/'.base64_encode($data->homework_id);
                            $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>';

                            return $build_view_action.'&nbsp;';
                        })              
                        ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);
    }

    /*
    | get_homework_records() : homework listing using ajax 
    | Auther        : Pooja K  
    | Date          : 4 Jun 2018
    */
    public function get_homework_records(Request $request, $type='',$fun_type='')
    {
    	$user_id = $this->user_id;
        $locale = '';

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
                      
  		$homework_table                = $this->HomeworkModel->getTable();
  		$level_class_table             = $this->LevelClassModel->getTable();
  		$level_table                   = $this->LevelTranslationModel->getTable();	
  		$class_table                   = $this->ClassTranslationModel->getTable();
  		$course_table                  = $this->CourseTranslationModel->getTable();

        $obj_custom = DB::table($homework_table)
                        ->select(DB::raw(   

                                            $homework_table.".id as homework_id,".
                                            $homework_table.".description,".
                                            $homework_table.".due_date,".
                                            $homework_table.".added_date,".
                                            $level_table.".level_name,".
                                            $class_table.".class_name,".
                                            $course_table.".course_name"
                                            
                                        ))
                        				->leftJoin($course_table,$homework_table.'.course_id',' = ',$course_table.'.course_id')
                        				->leftJoin($level_class_table,$homework_table.'.level_class_id',' = ',$level_class_table.'.id')
                        				->leftJoin($level_table,$level_class_table.'.level_id',' = ',$level_table.'.level_id')
                        				->leftJoin($class_table,$level_class_table.'.class_id',' = ',$class_table.'.class_id')
                                        ->where($level_table.'.locale','=',$locale)
                                        ->where($class_table.'.locale','=',$locale)
                                        ->where($course_table.'.locale','=',$locale)
                                        ->where($homework_table.'.school_id',$this->school_id)
                                        ->where($homework_table.'.academic_year_id',$this->academic_year)
                                        ->whereNull($homework_table.'.deleted_at')
                                        ->where($homework_table.'.homework_added_by','=',$user_id)
                                        ->orderBy($homework_table.'.created_at','DESC');


        if($fun_type == 'export'){
            $search_term = $request->input('search');
        }else{
            $search = $request->input('search');
            $search_term = $search['value'];
        }

        if($request->has('search') && $search_term!="")
        {

            $obj_custom = $obj_custom->WhereRaw("( (".$level_table.".level_name LIKE '%".$search_term."%')")
                                     ->orWhereRaw("(".$class_table.".class_name LIKE '%".$search_term."%')")
                                     ->orWhereRaw("(".$homework_table.".description LIKE '%".$search_term."%')")
                                     ->orWhereRaw("(".$course_table.".course_name LIKE '%".$search_term."%') )");       

        }

        if($fun_type=="export"){
            return $obj_custom->get();
        }else{
            return $obj_custom;
        }
    }

    /*
    | create() : redirecting to create homework
    | Auther        : Pooja K  
    | Date          : 5 Jun 2018
    */
    public function create()
    {
        $arr_academic_year = '';
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }

        $arr_levels = $arr_course =[];

        $obj_levels = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id);
        
        if(!empty($obj_levels))
        {
            $arr_levels = $obj_levels -> toArray();         
        }

       /* $obj_course = $this->CommonDataService->get_professor_courses($level,$class,$this->user_id);
        if(!empty($obj_course))
        {
            $arr_course = $obj_course ->toArray();
        }*/

        $this->arr_view_data['module_title']          = translation("add")." ".$this->module_title;
        $this->arr_view_data['arr_levels']            = $arr_levels ;
        $this->arr_view_data['arr_course']            = $arr_course ;
    
        return view($this->module_view_folder.'.create', $this->arr_view_data);

    }

    /*
    | get_class() : get list of classes 
    | Auther        : Pooja K  
    | Date          : 5 Jun 2018
    */ 
    public function get_class(Request $request)
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

    /*
    | store() : store homework in database 
    | Auther        : Pooja K  
    | Date          : 5 Jun 2018
    */ 
    public function store(Request $request)
    {
        $messages = $arr_rules = [];

        $arr_rules['level']            = 'required';
        $arr_rules['course']           = 'required';
        $arr_rules['class']            = 'required';
        $arr_rules['homework_details'] = 'required';

        $messages['required']               = translation('this_field_is_required');
        
        $validator                          = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $arr_data =[];

        $arr_data['school_id'] = $this->school_id;
        $arr_data['level_class_id'] = $request->input('class');
        $arr_data['course_id'] = $request->input('course'); 
        $arr_data['homework_added_by'] = $this->user_id;
        $arr_data['description'] = $request->input('homework_details');
        $arr_data['added_date'] = date('Y-m-d');

        $due_date = date_create($request->input('due_date'));
        $due_date = date_format($due_date,'Y-m-d');

        $arr_data['due_date'] = $due_date;
        $arr_data['academic_year_id'] = $this->academic_year;

        $students = $this->StudentModel
                            ->select('id','user_id','level_class_id') 
                            ->with('notifications','get_user_details','get_level_class.level_details','get_level_class.class_details')   
                            ->where('school_id',$this->school_id)
                            ->where('academic_year_id',$this->academic_year)
                            ->where('has_left',0)
                            ->where('is_active',1)
                            ->where('level_class_id',$request->input('class'))
                            ->get();

        $course = $this->CourseModel->where('id',$request->input('course'))->first();

        $arr_students = [];
        
        if(isset($students)  && !empty($students) )
        {
            $arr_students = $students->toArray();
            
            if(count($arr_students)==0)
            {
                Flash::error(translation('no_students_available_in_this_class'));
                return redirect()->back();        
            }
            $result = $this->HomeworkModel->create($arr_data);
            foreach($arr_students as $value)
            {
                $arr_student = [];
                $arr_student['homework_id'] = $result->id;
                $arr_student['student_id'] = $value['id'];
                $this->HomeworkStudentModel->create($arr_student);

                if(isset($value['notifications']) && $value['notifications']!='')       
                {
                    
                    $arr_permissions = json_decode($value['notifications']['notification_permission'],true);
                    if(isset($arr_permissions) && count($arr_permissions)>0)
                    {

                        if(array_key_exists('homework.app',$arr_permissions))
                        {
                         
                            $arr_notification = [];
                            $arr_notification['school_id']          =  $this->school_id;
                            $arr_notification['from_user_id']       =  $this->user_id;
                            $arr_notification['to_user_id']         =  $value['user_id'];
                            $arr_notification['user_type']          =  config('app.project.role_slug.professor_role_slug');
                            $arr_notification['notification_type']  =  'homework Add';
                            $arr_notification['title']              =  'Homework Added: '.ucwords($this->first_name.' '.$this->last_name).' of class '.(isset($value['get_level_class']['level_details']['level_name'])?$value['get_level_class']['level_details']['level_name']:'').' '.(isset($value['get_level_class']['class_details']['class_name'])?$value['get_level_class']['class_details']['class_name']:'').' added homework for course '.$course->course_name.' & it\'s due date is '.$due_date;
                            $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.student_role_slug').'/homework';
                            $this->NotificationModel->create($arr_notification);
                        }
                        $details   = [
                                        'first_name'    =>  isset($value['get_user_details']['first_name'])?ucwords($value['get_user_details']['first_name']):'',
                                        'due_date'      =>  $due_date,
                                        'level'         =>  isset($value['get_level_class']['level_details']['level_name'])?$value['get_level_class']['level_details']['level_name']:'',
                                        'class'         =>  isset($value['get_level_class']['class_details']['class_name'])?$value['get_level_class']['class_details']['class_name']:'',
                                        'professor_name'=>  ucwords($this->first_name.' '.$this->last_name),
                                        'mobile_no'     =>  isset($value['get_user_details']['mobile_no'])?$value['get_user_details']['mobile_no']:'',
                                        'email'         =>  isset($value['get_user_details']['email'])?$value['get_user_details']['email']:'',
                                        'course'        =>  $course->course_name
                                     ];
                        if(array_key_exists('homework.sms',$arr_permissions))
                        {
                            $arr_sms_data = $this->built_sms_data($details);
                            $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
                        }
                        if (array_key_exists('homework.email',$arr_permissions))
                        {
                            $arr_mail_data = $this->built_mail_data($details); 
                            $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id); 
                        }
                    }
                }
            }    
            
        }                    
        else
        {
            Flash::error(translation('no_students_available_in_this_class'));
            return redirect()->back();
        }

        /*$this->send_notification($arr_students);*/
        Flash::success(translation('homework_added_successfully'));
        return redirect()->back();
    }

    /*
    | view() : view homework in database 
    | Auther        : Pooja K  
    | Date          : 5 Jun 2018
    */ 
    public function view(Request $request,$enc_id)
    {
        $id = base64_decode($enc_id);

        $obj_homework = $this->HomeworkModel
                                ->with('get_homework_students.get_student_details.get_user_details','get_level_class.level_details','get_level_class.class_details','get_course')
                                ->where('id',$id)->first();

        if(isset($obj_homework) && !empty($obj_homework)){

            $arr_data = $obj_homework->toArray();
            if(count($arr_data)>0){
                $arr_students =  $arr_data['get_homework_students'];
                if(count($arr_students)>0){
                    $this->arr_view_data['arr_students'] = $arr_students;
                    $this->arr_view_data['arr_data'] = $arr_data;
                    $this->arr_view_data['module_title']    = translation("view")." ".$this->module_title;
                    return view($this->module_view_folder.'.view', $this->arr_view_data);    
                }
            }
        } 
    }

    /*
    | view() : change status of homework
    | Auther        : Pooja K  
    | Date          : 5 Jun 2018
    */ 
    public function change_status(Request $request)
    {
        $status = $request->input('status');
        $id = $request->input('id');

        if(!($request->has('reason')))
        {
            $this->HomeworkStudentModel->where('id',$id)->update(['status'=>$status,'status_changed_by'=>'PROFESSOR']);    
        }
        else
        {
            $this->HomeworkStudentModel->where('id',$id)->update(['status'=>$status,"rejection_reason"=>$request->reason]);    
        }
         
    }   

     public function get_courses(Request $request)
    {
        $option = '';
        $level = $request->input('level');
        $class = $request->input('class');
        $professor = $this->CommonDataService->get_professor_courses($class,$this->user_id);
        
        if(isset($professor) && $professor!=null)
        {
            $arr_professor = $professor->toArray(); 
            if(isset($arr_professor) && count($arr_professor)>0)
            {                                                   
                $option .= '<option value="">'.translation('select_course').'</option>';
                foreach ($arr_professor as $key => $professor) 
                {
                    $option .= '<option value="';
                    $option .= isset($professor['course_id'])?$professor['course_id']:'';
                    $option .= '">';
                    $option .= isset($professor['professor_subjects']['course_name'])?$professor['professor_subjects']['course_name']:'';
                    $option .= '</option>';
                }
            }
        }
        return $option;
       
    }

    /*public function send_notification()
    {
        

            if (isset($arr_students) && count($arr_students)>0) {
                foreach ($arr_students as $key => $value) {
                         
                    if(isset($value['notification_permissions']) && $value['notification_permissions']!='')       
                    {
                        $arr_permissions = json_decode($value['notification_permissions']['notification_permission'],true);
                        
                        if(array_key_exists('homework.app',$arr_permissions))
                        {
                         
                            $arr_notification = [];
                            $arr_notification['school_id']          =  $this->school_id;
                            $arr_notification['from_user_id']       =  $this->user_id;
                            $arr_notification['to_user_id']         =  $value['user_id'];
                            $arr_notification['user_type']          =  config('app.project.role_slug.professor_role_slug');
                            $arr_notification['notification_type']  =  'homework Add';
                            $arr_notification['title']              =  'New Course Material Added:Professor added new course material';
                            $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.student_role_slug').'/homework';
                            $this->NotificationModel->create($arr_notification);
                        }
                        elseif(array_key_exists('homework.sms',$arr_permissions))
                        {

                        }
                        elseif (array_key_exists('homework.email',$arr_permissions))
                        {
                            
                        }
                    }
                }
            }
    }*/

     public function built_mail_data($arr_data)
     {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'DUE_DATE'         => $arr_data['due_date'],
                                  'LEVEL'            => $arr_data['level'],
                                  'CLASS'            => $arr_data['class'],
                                  'COURSE_NAME'      => $arr_data['course'],
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                  'PROFESSOR_NAME'   => $arr_data['professor_name']];

            $arr_mail_data                        = [];
            $arr_mail_data['email_template_slug'] = 'add_homework';
            
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
                                  'DUE_DATE'         => $arr_data['due_date'],
                                  'LEVEL'            => $arr_data['level'],
                                  'CLASS'            => $arr_data['class'],
                                  'COURSE_NAME'      => $arr_data['course'],
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                  'PROFESSOR_NAME'   => $arr_data['professor_name']];
            

            $arr_sms_data                      = [];
            $arr_sms_data['sms_template_slug'] = 'add_homework';
            
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }

    /*
    | export() : Export List
    | Auther  : Vrajesh
    | Date    : 14-12-2018
    */

    public function export(Request $request)
    {
        $file_type = config('app.project.export_file_formate');
        $obj_data = $this->get_homework_records($request,'','export');

        if(sizeof($obj_data)<=0){
            Flash::error(translation("no_records_found_to_export"));
            return redirect()->back();
        }
        if(sizeof($obj_data)>500 && $request->file_format == $file_type ){
            Flash::error(translation("too_many_records_to_export"));
            return redirect()->back();
        }
        if($request->file_format == $file_type){
            \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel) use($obj_data) 
                {
                    $excel->sheet(ucwords($this->module_title), function($sheet) use($obj_data) 
                    {
                        $arr_fields['sr_no']            = translation('sr_no');
                        $arr_fields['level']            = translation('level');
                        $arr_fields['class']            = translation('class');
                        $arr_fields['course']           = translation('course');
                        $arr_fields['homework_details'] = translation('homework_details');
                        $arr_fields['added_date']       = translation('added_date');
                        $arr_fields['due_date']         = translation('due_date');
                        $arr_fields['status']           = translation('status');

                        $sheet->row(2, [ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                        $sheet->row(4, $arr_fields);
                        
                        // To set Colomn head
                        $j = 'A'; $k = '4';
                        for($i=0; $i<=7;$i++)
                        {
                            $sheet->cell($j.$k, function($cells) {
                                $cells->setBackground('#495b79');
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                                $cells->setFontColor('#ffffff');
                            });
                            $j++;
                        }

                        if(sizeof($obj_data)>0)
                        {
                            $arr_tmp = [];
                            $count = 1;
                            foreach($obj_data as $key => $result)
                            {
                                $status = '';
                                $count = $this->HomeworkStudentModel->where('homework_id',$result->homework_id)
                                                                    ->where("status",'<>','COMPLETED')
                                                                    ->count();
                                if($count==0){
                                    $status = translation('completed');
                                }else{
                                    $status = translation('pending');
                                }

                                $arr_tmp[$key]['sr_no']             = $count++;
                                $arr_tmp[$key]['level']             = $result->level_name;
                                $arr_tmp[$key]['class']             = $result->class_name;
                                $arr_tmp[$key]['course']            = $result->course_name;
                                $arr_tmp[$key]['homework_details']  = $result->description;
                                $arr_tmp[$key]['added_date']        = $result->added_date;
                                $arr_tmp[$key]['due_date']          = $result->due_date;
                                $arr_tmp[$key]['status']            = $status;
                            }
                            $sheet->rows($arr_tmp);
                        }
                    });
                })->export($file_type);     
        }
        
        if($request->file_format == 'pdf')
        {
            foreach($obj_data as $key => $row)
            {
                $status = '';

                $count = $this->HomeworkStudentModel->where('homework_id',$row->homework_id)
                                                    ->where("status",'<>','COMPLETED')
                                                    ->count();
                if($count==0)
                {
                    $status = translation('completed');
                }
                else
                {
                    $status = translation('pending');
                }
                $row->status = $status;
            }

            $this->arr_view_data['arr_data'] = $obj_data;

            $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
            return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
        }
    }

}
