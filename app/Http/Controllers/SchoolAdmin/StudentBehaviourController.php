<?php

namespace App\Http\Controllers\SchoolAdmin;

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
use App\Common\Services\CommonDataService;
use App\Common\Traits\MultiActionTrait;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;

class StudentBehaviourController extends Controller
{
    use MultiActionTrait;
	public function __construct(CommonDataService $CommonDataService)
    {

    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/student_behaviour';
        $this->module_title                 = translation("student_behaviour");     
        $this->module_view_folder           = "schooladmin.student_behaviour";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-file';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->view_icon                    = 'fa fa-eye';
        $this->school_id                    = Session::has('school_id')?Session::get('school_id'):'';
        $this->academic_year				= Session::has('academic_year')?Session::get('academic_year'):'';

        $this->first_name = $this->last_name ='';

        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
        	$this->user_id    = $obj_data->id;
        	$this->first_name = $obj_data->first_name;
        	$this->last_name  = $obj_data->last_name;
        	$this->email      = $obj_data->email;
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
        $this->CommonDataService                = $CommonDataService;
        $this->BaseModel                        = $this->StudentBehaviourModel;

   		$this->arr_view_data['module_title']    = translation('student_behaviour');
   		$this->arr_view_data['module_icon']     = 'fa fa-report';
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
        $arr_levels  = $behaviour_levels = [];

        $obj_levels = $this->CommonDataService->get_levels($this->academic_year);
        $obj_levels1 = $this->StudentBehaviourModel
                            ->whereHas('get_level_class',function(){})
                            ->with('get_level_class')
                            ->where('school_id',$this->school_id)
                            ->where('academic_year_id',$this->academic_year)
                            ->groupBy('level_class_id')
                            ->get();

        if(!empty($obj_levels))
        {
            $arr_levels = $obj_levels -> toArray();    
        }
        if(!empty($obj_levels1)){
            $arr_levels1 = $obj_levels1 -> toArray();       
            foreach($arr_levels1 as $level){
                
                if(!in_array($level['get_level_class']['level_id'],$behaviour_levels)){
                    array_push($behaviour_levels,$level['get_level_class']['level_id']);
                }
            }
        }

        $behaviour_period = $this->StudentBehaviourPeriodModel
                                 ->where('school_id',$this->school_id)
                                 ->where('academic_year_id',$this->academic_year)
                                 ->first();

        
        if($behaviour_period)
        {
            $this->arr_view_data['period']            = $behaviour_period->period ;
        }

        $obj_course = $this->CommonDataService->get_courses($this->academic_year,config('app.project.school_admin_panel_slug'),$this->user_id);
        if(!empty($obj_course))
        {
            $arr_course = $obj_course ->toArray();
        }
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_levels']      = $arr_levels ;
        $this->arr_view_data['behaviour_levels']      = $behaviour_levels ;
        $this->arr_view_data['arr_course']      = $arr_course ;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*
    | store_period() : store student behaviour speriod
    | Auther         : Sayali B
    | Date           : 19-07-2018
    */
    public function store_period(Request $request)
    {
        $school_id      = $this->school_id;
        $academic_year  = $this->academic_year;
        $period         = $request->input('period');

        $behaviour_period = $this->StudentBehaviourPeriodModel->where('school_id',$school_id)->where('academic_year_id',$academic_year)->first();
        if(isset($behaviour_period) && $behaviour_period!=null)
        {
            $behaviour_data = $this->StudentBehaviourModel->where('period_id',$behaviour_period->id)->first();
            /*if($behaviour_data)
            {
                return response()->json(array('status'=>'error','msg'=>translation('you_are_unable_to_change_student_behaviour_period')));      
            }
            else
            {*/
                $arr_data['period']   =   $period;
                $period_data =  $this->StudentBehaviourPeriodModel->where('id',$behaviour_period->id)->update($arr_data);  
                if($period_data)
                {
                    return response()->json(array('status'=>'success','msg'=>translation('student_behaviour_period_updated_successfully')));
                }
                else
                {
                    return response()->json(array('status'=>'error','msg'=>translation('something_went_wrong')));   
                }  
                    
            // }
        }
        else
        {
            $arr_data['school_id']          =   $school_id;
            $arr_data['period']             =   $period;
            $arr_data['academic_year_id']   =   $academic_year;
            $period_data =  $this->StudentBehaviourPeriodModel->create($arr_data);
            if($period_data)
            {
                return response()->json(array('status'=>'success','msg'=>translation('student_behaviour_period_added_successfully')));
            }
            else
            {
                return response()->json(array('status'=>'error','msg'=>translation('something_went_wrong')));   
            }
        }
    }
    
    public function getClasses(Request $request)
    {

        $level_id = $request->input('level');
        $behaviour_levels=[];
        $obj_levels1 = $this->StudentBehaviourModel
                            ->where('school_id',$this->school_id)
                            ->where('academic_year_id',$this->academic_year)
                            ->groupBy('level_class_id')
                            ->get();

        if(!empty($obj_levels1)){
            $arr_levels1 = $obj_levels1 -> toArray();       
            foreach($arr_levels1 as $level){
                if(!in_array($level['level_class_id'],$behaviour_levels)){
                    array_push($behaviour_levels,$level['level_class_id']);
                }
            }
        }                    
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
                        if(in_array($value['id'],$behaviour_levels)){
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
        }

        return $options;
    }

    public function get_courses(Request $request)
    {

        $option = '';
        $behaviour_levels=[];
        $class = $request->input('class');
        $obj_details    = $this->LevelClassModel->where('id',$class)->first();
        $obj_levels1 = $this->StudentBehaviourModel
                            ->where('school_id',$this->school_id)
                            ->where('academic_year_id',$this->academic_year)
                            ->groupBy('course_id')
                            ->get();

        if(!empty($obj_levels1)){
            $arr_levels1 = $obj_levels1 -> toArray();       
            foreach($arr_levels1 as $level){
                if(!in_array($level['course_id'],$behaviour_levels)){
                    array_push($behaviour_levels,$level['course_id']);
                }
            }
        }
                            
        $arr_courses  = $this->CommonDataService->get_courses($this->academic_year,config('app.project.role_slug.school_admin_role_slug'),$this->user_id,$obj_details->level_id,$obj_details->class_id);

        if(isset($arr_courses) && count($arr_courses)>0)
        {   

            $option .= '<option value="">'.translation('select_course').'</option>';
            foreach ($arr_courses as $key => $course) 
            {
                if(in_array($course['id'],$behaviour_levels)){
                    $option .= '<option value="';
                    $option .= isset($course['id'])?$course['id']:'';
                    $option .= '">';
                    $option .= isset($course['course_name'])?$course['course_name']:'';
                    $option .= '</option>';
                }
            }
            return response()->json(array('status'=>'success','data'=>$option));
        }
        else
        {
            $option .= translation('course_is_not_assigned_for_selected_level_and_class');
            return response()->json(array('status'=>'error','data'=>$option));
        }
       
    }

    public function get_students(Request $request)
   {
        $data = $flag = '';
        $stud_attendance = $record = [];
        $level_id = $request->input('level');
        $class_id = $request->input('class');
        $level_class = $this->LevelClassModel->where(['level_id'=>$level_id,'class_id'=>$class_id])->first();
        
        /*if(isset($level_class) && $level_class != null)
        {*/
            $student_data = $this->StudentModel->with('get_user_details')->where(['level_class_id'=>$class_id,'school_id'=>$this->school_id])->get();
            
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
                    $data .= '<td>';
                    $data .= '<input type ="text" id="notation_'.$student['id'].'" name="notation['.$student['id'].']" class="form-control" required="true" data-rule-min="0" data-rule-max="10" data-rule-number="true" style="width:60%;">';
                    $data .= '</td>';
                    $data .= '<td>';
                    $data .= '<input type ="text" id="comment_'.$student['id'].'" name="comment['.$student['id'].']" class="form-control" pattern="^[A-Za-z0-9 ]+$">';
                    $data .= '</td>';
                    $data .= '</tr>';
                    
                }
            }
            else
            {

                $data .='<tr><td colspan="4"><div class="alert alert-danger" style="text-align:center">'.translation('no_data_available').'</div></td></tr>';
                
            }
        /*}*/
    return $data;
   }

   public function get_students_behaviour(Request $request)
   {
        
        $data = '';
        $arr_behaviour  =   $arr_details =  $record = [];
        $class_id       =   $request->input('class');
        $course_id      =   $request->input('course');
        $academic_year  =   $this->academic_year;
        $school_id      =   $this->school_id;

        $student_data = $this->StudentModel
                             ->with('get_user_details')
                             ->where(['level_class_id'=>$class_id,'school_id'=>$this->school_id,'academic_year_id'=>$this->academic_year,'has_left'=>0])
                             ->get();

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
                    $data .= '</td>';
                    $data .= '<td>';
                    $data .= $comment;
                    $data .= '</td>';
                    $data .= '<td>';
                    $data .= '<a class="green-color" href="'.$this->module_url_path.'/view/'.base64_encode($details['id']).'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>';
                    $data .= '</td>';
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
                            ->where('has_left',0)
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
}
