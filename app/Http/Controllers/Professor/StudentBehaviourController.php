<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\LevelClassModel;
use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\LevelTranslationModel;
use App\Models\ClassTranslationModel;
use App\Models\ProfessorModel;
use App\Models\StudentModel;
use App\Models\ParentModel;
use App\Models\ClaimModel;
use App\Models\SchoolTimeTableModel;
use App\Models\StudentBehaviourModel;
use App\Models\StudentBehaviourPeriodModel;
use App\Models\SchoolAdminModel;
use App\Models\NotificationModel;
use App\Models\CourseModel;
use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;
use App\Common\Services\StudentService;
use App\Common\Traits\MultiActionTrait;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use PDF;
use Datatables;

class StudentBehaviourController extends Controller
{
    use MultiActionTrait;
	public function __construct(CommonDataService $CommonDataService,
                                StudentService $StudentService,
                                EmailService $EmailService)
    {

    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.professor_panel_slug')).'/student_behaviour';
        $this->module_title                 = translation("student_behaviour");     
        $this->module_view_folder           = "professor.student_behaviour";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-file';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->view_icon                    = 'fa fa-eye';
        $this->school_id                    = Session::has('school_id')?Session::get('school_id'):'';
        $this->academic_year				= Session::has('academic_year')?Session::get('academic_year'):'';
        $this->SchoolAdminModel             = new SchoolAdminModel();


        $this->first_name = $this->last_name = $this->school_admin_email = $this->school_admin_contact =$this->school_admin_id='';

        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
        	$this->user_id    = $obj_data->id;
        	$this->first_name = $obj_data->first_name;
        	$this->last_name  = $obj_data->last_name;
        	$this->email      = $obj_data->email;
        }

        $obj_permissions = $this->SchoolAdminModel
                                ->with('notification_permissions','get_user_details')
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
            $this->school_admin_email   = isset($arr_permissions['get_user_details']['email'])?$arr_permissions['get_user_details']['email']:'';
            $this->school_admin_contact = isset($arr_permissions['get_user_details']['mobile_no'])?$arr_permissions['get_user_details']['mobile_no']:'';
        }

		$this->LevelClassModel 		            = new LevelClassModel();
        $this->UserModel                        = new UserModel();
        $this->ClaimModel                       = new ClaimModel();
		$this->ProfessorModel 			        = new ProfessorModel();
        $this->ParentModel                      = new ParentModel();
        $this->StudentModel                     = new StudentModel();
        $this->UserTranslationModel             = new UserTranslationModel();
        $this->LevelTranslationModel            = new LevelTranslationModel();
        $this->ClassTranslationModel            = new ClassTranslationModel();
        $this->SchoolTimeTableModel             = new SchoolTimeTableModel();
        $this->StudentBehaviourModel            = new StudentBehaviourModel();
        $this->StudentBehaviourPeriodModel      = new StudentBehaviourPeriodModel();
        $this->NotificationModel                = new NotificationModel();
        $this->CourseModel                      = new CourseModel();
        $this->CommonDataService                = $CommonDataService;
        $this->BaseModel                        = $this->StudentBehaviourModel;
        $this->EmailService                     = $EmailService;

   		$this->arr_view_data['page_title']      = translation('claim');
   		$this->arr_view_data['module_title']    = translation('claim');
   		$this->arr_view_data['module_icon']     = 'fa fa-claim';
   		$this->arr_view_data['module_url_path'] = $this->module_url_path;
   		$this->arr_view_data['theme_color']     = $this->theme_color;
   		$this->arr_view_data['create_icon']     = 'fa fa-plus-circle';
   		$this->arr_view_data['edit_icon']       = 'fa fa-edit-circle';

   		/*literals*/	
    	$this->str_module_title    = 'module_title';
    	$this->str_module_url_path = 'module_url_path';

    }

      /*
    | index() 		: Redirect to claim list 
    | Auther        : Sayali B
    | Date          : 7-06-2018
    */  
    public function index()
    {	
        $arr_levels  =[];

        $obj_levels = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id);
        
        if(!empty($obj_levels))
        {
            $arr_levels = $obj_levels -> toArray();   
        }

        $behaviour_period = $this->StudentBehaviourPeriodModel
                                 ->where('school_id',$this->school_id)
                                 ->where('academic_year_id',$this->academic_year)
                                 ->first();

        if($behaviour_period)
        {
            $this->arr_view_data['period']            = $behaviour_period->period ;
        }
        else
        {
            Flash::error(translation('student_behaviour_period_is_not_set_yet_by_school_admin'));
        }

        $this->arr_view_data['module_url_path']        = $this->module_url_path;
        $this->arr_view_data[$this->str_module_title]  = translation("manage")." ".$this->module_title;
        $this->arr_view_data['arr_levels']             = $arr_levels ;
        $this->arr_view_data['module_title']           = $this->module_title;
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*
    | create() : create claim
    | Auther        : Sayali B  
    | Date          : 7-06-2018
    */
    public function create()
    {
    	$arr_levels    = [];
        $school_id     = $this->school_id;
        $academic_year = $this->academic_year;

    	$obj_levels = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id);
        
    	if(!empty($obj_levels))
    	{
			$arr_levels = $obj_levels -> toArray();   
    	}

        $behaviour_period = $this->StudentBehaviourPeriodModel
                                 ->where('school_id',$school_id)
                                 ->where('academic_year_id',$academic_year)
                                 ->first();


        if($behaviour_period)
        {
            $this->arr_view_data['period']            = $behaviour_period->period ;
        }
        else
        {
            Flash::error(translation('student_behaviour_period_is_not_set_yet_by_school_admin'));
            return redirect()->back();
        }


        $this->arr_view_data['page_title']            = translation("add")." ".$this->module_title;
    	$this->arr_view_data[$this->str_module_title] = $this->module_title;
    	$this->arr_view_data['arr_levels']            = $arr_levels ;
    
        return view($this->module_view_folder.'.create', $this->arr_view_data);

    }

    /*
    | store()       : store student_behaviour into tbl_sstudent_behaviour
    | Auther        : Sayali B 
    | Date          : 19-07-2018
    */
    public function store(Request $request)
    {
        
        $messages = $arr_rules = [];

        $arr_rules['level']            = 'required';
        $arr_rules['class']            = 'required';
        $arr_rules['course']           = 'required';
        
        $messages['required']               = translation('this_field_is_required');
        
        $validator                          = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 
        $behaviour_period = $this->StudentBehaviourPeriodModel->where('school_id',$this->school_id)->where('academic_year_id',$this->academic_year)->first();

        if($behaviour_period)
        {
            $arr_data = $arr_notation = $arr_comment = []; 
            $arr_data['school_id']              = $this->school_id;
            $arr_data['academic_year_id']       = $this->academic_year;
            $arr_data['period_id']              = $behaviour_period->id;
            $arr_data['level_class_id']         = $request->input('class');
            $arr_data['course_id']              = $request->input('course');
            $arr_data['professor_id']           = $this->user_id;

            if(!empty($request->input('notation')))
            {
                $arr_notation = json_encode($request->input('notation'));
                $arr_data['behaviour_notation'] = $arr_notation;
            }
            if(!empty($request->input('comment')))
            {
                $arr_comment = json_encode($request->input('comment'));
                $arr_data['behaviour_comments'] = $arr_comment;
            }

            if($behaviour_period->period == 'WEEKLY')
            {
                $arr_data['week_month']     =   date('W');
            }
            elseif($behaviour_period->period == 'MONTHLY')
            {
                $arr_data['week_month']     =   date('F');
            }
            else
            {
                $arr_data['week_month']     =   '';
            }
            $behaviour = $this->BaseModel->create($arr_data);

            if($behaviour)
            {
                $result = $this->send_notifications($behaviour);
                Flash::success(translation('student_behaviour_added_successfully'));
                return redirect()->back();
            }
            else
            {
                Flash::error(translation('something_went_wrong'));
                return redirect()->back();   
            }
        }
        else
        {
            Flash::error(translation('student_behaviour_period_is_not_set_yet_by_school_admin'));
            return redirect()->back();
        }
        
    }

    /*
    | update()       : update student_behaviour 
    | Auther        : Sayali B 
    | Date          : 19-07-2018
    */
    public function update(Request $request,$enc_id)
    {
        
        $id = base64_decode($enc_id);
        if(!is_numeric($id))
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }
        $messages = $arr_rules = [];

        $arr_rules['level']            = 'required';
        $arr_rules['class']            = 'required';
        $arr_rules['course']           = 'required';
        
        $messages['required']               = translation('this_field_is_required');
        
        $validator                          = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 
        $behaviour_period = $this->StudentBehaviourPeriodModel->where('school_id',$this->school_id)->where('academic_year_id',$this->academic_year)->first();

        if($behaviour_period)
        {
            $arr_data = $arr_notation = $arr_comment = []; 
            $arr_data['school_id']              = $this->school_id;
            $arr_data['academic_year_id']       = $this->academic_year;
            $arr_data['period_id']              = $behaviour_period->id;
            $arr_data['level_class_id']         = $request->input('class');
            $arr_data['course_id']              = $request->input('course');
            $arr_data['professor_id']           = $this->user_id;

            if(!empty($request->input('notation')))
            {
                $arr_notation = json_encode($request->input('notation'));
                $arr_data['behaviour_notation'] = $arr_notation;
            }
            if(!empty($request->input('comment')))
            {
                $arr_comment = json_encode($request->input('comment'));
                $arr_data['behaviour_comments'] = $arr_comment;
            }

            if($request->input('period') == 'WEEKLY')
            {
                $arr_data['week_month']     =   date('W');
            }
            elseif($request->input('period') == 'MONTHLY')
            {
                $arr_data['week_month']     =   date('F');
            }
            else
            {
                $arr_data['week_month']     =   '';
            }
            $behaviour = $this->BaseModel->where('id',$id)->update($arr_data);

            if($behaviour)
            {
                Flash::success(translation('student_behaviour_updated_successfully'));
                return redirect()->back();
            }
            else
            {
                Flash::error(translation('something_went_wrong'));
                return redirect()->back();   
            }
        }
        else
        {
            Flash::error(translation('student_behaviour_period_is_not_set_yet_by_school_admin'));
            return redirect()->back();
        }
        
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

    public function get_courses(Request $request)
    {

        $option = '';
        $level = $request->input('level');
        $class = $request->input('class');
        $arr_professor = $this->CommonDataService->get_professor_courses($class,$this->user_id);

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
        return $option;
       
    }

   public function get_students(Request $request)
   {

        $data = $flag = $period = $current = '';
        $record = [];
        $school_id     = $this->school_id;
        $academic_year = $this->academic_year;
        $level_id      = $request->input('level');
        $class_id      = $request->input('class');
        $course_id     = $request->input('course');
        
        $behaviour_period = $this->StudentBehaviourPeriodModel
                                 ->where('school_id',$school_id)
                                 ->where('academic_year_id',$academic_year)
                                 ->first();

        if($behaviour_period)
        {
            $period =  $behaviour_period->period;
        }
        if($period == 'WEEKLY')
        {
            $current = date('W');
        }
        elseif ($period == 'MONTHLY') 
        {
            $current = date('F');
        }   
        else
        {
            $current = '';
        }

        $behaviour_data   = $this->StudentBehaviourModel
                                 ->where('level_class_id',$class_id)
                                 ->where('course_id',$course_id)
                                 ->where('week_month',$current)
                                 ->first();
        
        $student_data     = $this->StudentModel
                                 ->with('get_user_details')
                                 ->where(['level_class_id'=>$class_id,'school_id'=>$this->school_id])
                                 ->where('academic_year_id',$this->academic_year)
                                 ->where('has_left',0)
                                 ->where('is_active',1)
                                 ->get();
             
            if(isset($student_data) && count($student_data)>0)
            {
                $arr_students = $student_data->toArray();

                foreach ($arr_students as $key => $student) {
                    $data .= '<tr>';
                    $data .= '<td>';
                    $data .= ($key+1);
                    $data .= '</td>';
                    $data .= '<td>';
                             $first_name = isset($student['get_user_details']['first_name'])?$student['get_user_details']['first_name']:'';
                             $last_name  = isset($student['get_user_details']['last_name'])?$student['get_user_details']['last_name']:'';
                             $user_name  = $first_name.' '.$last_name;
                    $data .= isset($user_name)?ucwords($user_name):'';
                    $data .= '</td>';
                    $data .= '<td>';
                    $data .= isset($student['get_user_details']['national_id'])?$student['get_user_details']['national_id']:'';
                    $data .= '</td>';
                    if(isset($behaviour_data) && $behaviour_data!=null)
                    {
                        $arr_notation = $arr_comment = [];
                        if(isset($behaviour_data->behaviour_notation) && !empty($behaviour_data->behaviour_notation))
                        {
                            $arr_notation = json_decode($behaviour_data->behaviour_notation,true);
                        }
                        if(isset($behaviour_data->behaviour_comments) && !empty($behaviour_data->behaviour_comments))
                        {
                            $arr_comment = json_decode($behaviour_data->behaviour_comments,true);
                        }
                        $data .= '<td>';

                        $data .= '<input type ="text" id="notation_'.$student['id'].'" name="notation['.$student['id'].']" class="form-control" required="true" data-rule-min="0" data-rule-max="10" data-rule-number="true" style="width:60%;" onBlur="getComment(this,'.$student['id'].');"';
                        if(array_key_exists($student['id'],$arr_notation))
                        {
                            $data .= 'value = "'.$arr_notation[$student['id']].'"';
                        }
                        $data .= '>';
                        
                        $data .= '</td>';
                        $data .= '<td>';
                        $data .= '<input type ="text" id="comment_'.$student['id'].'" name="comment['.$student['id'].']" class="form-control" pattern="^[A-Za-z0-9 \-.\,]+$"';
                        if(array_key_exists($student['id'],$arr_notation))
                        {
                            $data .= 'value = "'.$arr_comment[$student['id']].'"';
                        }
                        $data .= '>';
                        $data .= '</td>';
                        $record['status'] = 'update';
                        $record['enc_id'] = base64_encode($behaviour_data->id);
                    }
                    else
                    {
                        $data .= '<td>';
                        $data .= '<input type ="text" id="notation_'.$student['id'].'" name="notation['.$student['id'].']" class="form-control" required="true" data-rule-min="0" data-rule-max="10" data-rule-number="true" style="width:60%;" onBlur="getComment(this,'.$student['id'].');">';
                        $data .= '</td>';
                        $data .= '<td>';
                        $data .= '<input type ="text" id="comment_'.$student['id'].'" name="comment['.$student['id'].']" class="form-control" pattern="^[A-Za-z0-9 \-.\,]+$">';
                        $data .= '</td>';
                        $record['status'] = 'save';
                    }
                    $data .= '</tr>';
                    
                }
                $record['flag'] =   true;
                $record['data'] =   $data;
            }
            else
            {

                $data .='<div class="alert alert-danger" style="text-align:center">'.translation('no_data_available').'</div>';
                
                $record['flag'] =   false;
                $record['data'] =   $data;
            }
        /*}*/
    return $record;
   }

   public function get_students_behaviour(Request $request,$type='',$fun_type='')
   {
        $data = '';
        $arr_behaviour  =   $arr_details =  $record = [];
        $class_id       =   $request->input('class');
        $course_id      =   $request->input('course');
        $academic_year  =   $this->academic_year;
        $school_id      =   $this->school_id;
        $search_key     =   $request->has('search_key');

        //dd($request->all());    
        $student_data = $this->StudentModel
                             ->with('get_user_details')
                             ->where(['level_class_id'=>$class_id,'school_id'=>$this->school_id,'academic_year_id'=>$this->academic_year,'has_left'=>0])
                             /*->where([
                                        ['user_id', 'like', '%20%'],
                                    ])*/
                             ->get();

        //dd($student_data->toArray());

        if(isset($student_data) && count($student_data)>0)
        {
            $arr_details = $student_data->toArray();
        }
        else
        {
            $data .='<div class="alert alert-danger" style="text-align:center">'.translation('no_data_available').'</div>';
            $record['flag'] =   false;
            $record['data'] =   $data;
            return $record;
        }
        $behaviour_data =   $this->StudentBehaviourModel
                                 ->where('level_class_id',$class_id) 
                                 ->where('course_id',$course_id)
                                 ->where('academic_year_id',$academic_year)
                                 ->where('school_id',$school_id)
                                 ->where('professor_id',$this->user_id)
                                 ->get();

        
        if(isset($behaviour_data) && count($behaviour_data)>0)
        {
            $arr_behaviour = $behaviour_data->toArray();
        }
        else
        {
            $data .='<div class="alert alert-danger" style="text-align:center">'.translation('no_data_available').'</div>';
            $record['flag'] =   false;
            $record['data'] =   $data;
            return $record;
        }

        $val = 0;
        $comment = '';
        if(isset($arr_details) && count($arr_details)>0)
        {
            foreach ($arr_details as $key => $details) {
                $no = $total = 0;
                if(isset($details['get_user_details']['first_name']) && !empty($details['get_user_details']['first_name']))
                {

                    $data .='<tr>';
                    $data .='<td>'.(++$val).'</td>';
                    $data .= '<td>';
                             $first_name = isset($details['get_user_details']['first_name'])?$details['get_user_details']['first_name']:'';
                             $last_name  = isset($details['get_user_details']['last_name'])?$details['get_user_details']['last_name']:'';
                             $user_name  = $first_name.' '.$last_name;
                    $data .= isset($user_name)?ucwords($user_name):'';
                    $data .= '</td>';
                    $data .= '<td>';
                    $data .= isset($details['get_user_details']['national_id'])?$details['get_user_details']['national_id']:'';
                    $data .= '</td>';
                    if(isset($arr_behaviour) && count($arr_behaviour)>0)
                    {
                        foreach ($arr_behaviour as $key => $behaviour) 
                        {   
                            if(isset($behaviour['behaviour_notation']) && !empty($behaviour['behaviour_notation']))
                            {
                                $notation = json_decode($behaviour['behaviour_notation'],true);

                                if(array_key_exists($details['id'], $notation))
                                {
                                    $no     += $notation[$details['id']];
                                    $total  += 1;
                                }
                            }

                        }
                        
                    }
                    $data .= '<td>';
                    if($no!=0 && $total!=0)
                    {
                        $avg = $no/$total;
                        $data .= $avg;

                        if($fun_type == 'export')
                        {
                            if($avg<=10 && $avg>=0 && $avg!='')
                            {
                                if($avg>=8 && $avg<=10)
                                {
                                    $comment = '<label>'.translation('excellent_behaviour').'</label>';
                                }
                                else if($avg>=6 && $avg<8)
                                {
                                    $comment = '<label>'.translation('good_behaviour').'</label>';
                                }
                                else if($avg>=4 && $avg<6)
                                {
                                    $comment = '<label>'.translation('average_behaviour').'</label>';
                                }
                                else if($avg>=0 && $avg<4)
                                {
                                    $comment = '<label>'.translation('poor_behaviour').'</label>';
                                }
                            }
                        }else{
                            if($avg<=10 && $avg>=0 && $avg!='')
                            {
                                if($avg>=8 && $avg<=10)
                                {
                                    $comment = '<label style="color:#0fa12f">'.translation('excellent_behaviour').'</label>';
                                }
                                else if($avg>=6 && $avg<8)
                                {
                                    $comment = '<label style="color:#007ef9">'.translation('good_behaviour').'</label>';
                                }
                                else if($avg>=4 && $avg<6)
                                {
                                    $comment = '<label style="color:#ff840d">'.translation('average_behaviour').'</label>';
                                }
                                else if($avg>=0 && $avg<4)
                                {
                                    $comment = '<label style="color:#f2dede">'.translation('poor_behaviour').'</label>';
                                }
                            }
                        }
                    }
                    $data .= '</td>';
                    $data .= '<td>';
                    $data .= $comment;
                    $data .= '</td>';

                    if($fun_type != 'export')
                    {
                        $data .= '<td>';
                        $data .= '<a class="green-color" href="'.$this->module_url_path.'/view/'.base64_encode($details['id']).'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>';
                        $data .= '</td>';
                    }
                    $data .= '</tr>';

                }
            }
            $record['flag'] =   true;
            $record['data'] =   $data;

            return $record;
        }
        
   }

   public function view($enc_id)
   {
    $id = base64_decode($enc_id);
    if(!is_numeric($id))
    {
        Flash::error(translation('something_went_wrong'));
        return redirect()->back();
    }

    $student_details = $this->StudentModel
                            ->with('get_user_details','get_behaviour.get_behaviour_period','get_behaviour.get_course')
                            ->with(["get_level_class"=>function($q){
                                            $q->with("get_level");
                                            $q->with("get_class");
                                    }])
                            ->where('id',$id)
                            ->where('is_active',1)
                            ->first();

    if(isset($student_details) && count($student_details)>0)
    {
        $arr_details  = $student_details->toArray();
        $this->arr_view_data['arr_details']            = $arr_details;
    }
    else
    {
        Flash::error(translation('something_went_wrong'));
        return redirect()->back();
    }
    $this->arr_view_data['page_title']            = translation("view")." ".$this->module_title;
    $this->arr_view_data[$this->str_module_title] = $this->module_title;
    $this->arr_view_data['view_icon']             = $this->view_icon;
    $this->arr_view_data['module_icon']           = $this->module_icon;

    return view($this->module_view_folder.'.view', $this->arr_view_data);
   }

    public function send_notifications($behaviour)
    {             
        $level_class = $this->LevelClassModel->with('level_details','class_details')->where('id',$behaviour->level_class_id)->first();
        $course_name = $this->CourseModel->where('id',$behaviour->course_id)->first();
        
        if(array_key_exists('student_behaviour.app',$this->permissions))
        {
         
            $arr_notification = [];
            $arr_notification['school_id']          =  $this->school_id;
            $arr_notification['from_user_id']       =  $this->user_id;
            $arr_notification['to_user_id']         =  $this->school_admin_id;
            $arr_notification['user_type']          =  config('app.project.role_slug.professor_role_slug');
            $arr_notification['notification_type']  =  'Student Behaviour Add';
            $arr_notification['title']              =  'Student Behaviour Added:'.ucwords($this->first_name.' '.$this->last_name).' added student behaviour of class '.(isset($level_class['level_details']['level_name'])?$level_class['level_details']['level_name']:'').' '.(isset($level_class['class_details']['class_name'])?$level_class['class_details']['class_name']:'').' for course '.(isset($course_name->course_name)?$course_name->course_name:'');
            $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.student_role_slug').'/homework';
            $this->NotificationModel->create($arr_notification);
        }
        $details          = [
                                    'first_name'  =>  'School Admin',
                                    'level'       =>  isset($level_class['level_details']['level_name'])?$level_class['level_details']['level_name']:'',
                                    'class'       =>  isset($level_class['class_details']['class_name'])?$level_class['class_details']['class_name']:'',
                                    'email'       =>  $this->school_admin_email,
                                    'mobile_no'   =>  $this->school_admin_contact,
                                    'professor'   =>  ucwords($this->first_name.' '.$this->last_name),
                                    'course'      =>  isset($course_name->course_name)?$course_name->course_name:''
                            ];
        if(array_key_exists('student_behaviour.sms',$this->permissions))
        {
            $arr_sms_data = $this->built_sms_data($details);
            $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
        }
        if (array_key_exists('student_behaviour.email',$this->permissions))
        {
            $arr_mail_data = $this->built_mail_data($details);
            $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
        }
    }

     public function built_mail_data($arr_data)
     {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'LEVEL'            => $arr_data['level'],
                                  'CLASS'            => $arr_data['class'],
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                  'PROFESSOR'        => $arr_data['professor']];

            $arr_mail_data                        = [];
            $arr_mail_data['email_template_slug'] = 'add_student_behaviour';
            
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
                                  'LEVEL'            => $arr_data['level'],
                                  'CLASS'            => $arr_data['class'],
                                  'PROFESSOR'        => $arr_data['professor']];
            

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
        $obj_data = $this->get_students_behaviour($request,'','export');

        if(sizeof($obj_data)<=0){
            Flash::error(translation("no_records_found_to_export"));
            return redirect()->back();
        }
        if(sizeof($obj_data)>500 && $request->file_format == $file_type ){
            Flash::error(translation("too_many_records_to_export"));
            return redirect()->back();
        }
        if($request->file_format == $file_type)
        {
            $obj_data['sheet_head'] = ucwords($this->module_title).' - '.date('d M Y');  
            $this->arr_view_data['arr_data'] = $obj_data;

            \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel) {

                $excel->sheet(ucwords($this->module_title), function($sheet) {
                    $j = 'A'; $k = '4';
                    for($i=0; $i<=7;$i++){
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
            $this->arr_view_data['arr_data'] = $obj_data;

            $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
            return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
        }
    }

}
