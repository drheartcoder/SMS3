<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\GradebookModel;
use App\Common\Services\CommonDataService;
use App\Common\Services\StudentService;

use App\Common\Traits\MultiActionTrait;

use App\Models\ExamPeriodSchoolModel;
use App\Models\ExamPeriodModel;
use App\Models\AcademicYearModel;
use App\Models\SchoolSubjectsModel;
use App\Models\GradebookFieldsModel;
use App\Models\StudentPeriodAttendanceModel;
use App\Models\ExamModel;

use DB;
use Flash;
use Session;
use Sentinel;
use Validator;
use Datatables;


class GradebookController extends Controller
{
	function __construct(CommonDataService $CommonDataService,
                         StudentService $StudentService){

        $this->GradebookModel               = new GradebookModel();
        $this->BaseModel                    = new GradebookModel();
        $this->ExamPeriodSchoolModel        = new ExamPeriodSchoolModel();
        $this->AcademicYearModel            = new AcademicYearModel();
        $this->SchoolSubjectsModel          = new SchoolSubjectsModel();
        $this->GradebookFieldsModel         = new GradebookFieldsModel();
        $this->ExamPeriodModel              = new ExamPeriodModel();
        $this->StudentPeriodAttendanceModel = new StudentPeriodAttendanceModel();

        $this->CommonDataService     = $CommonDataService;

    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/gradebook';
        $this->module_title                 = translation('gradebook');
 
        $this->module_view_folder           = "schooladmin.gradebook";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-book';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->StudentService               = $StudentService;

    	$this->arr_view_data['module_title'] = translation('gradebook');
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['create_icon'] = $this->create_icon;
        $this->arr_view_data['edit_icon'] = $this->edit_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;

        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;
        }

    }
    /*
    | get_class() : redirect to list of students to genrate gradebook accordingly 
    | Auther        : Pooja K  
    | Date          : 27-07-2018
    */ 
    function index(){

        Session::forget('level_id_for_gradebook');
        Session::forget('class_id_for_gradebook');
        Session::forget('exam_period');

        $arr_levels = [];
        $levels = $this->CommonDataService->get_levels($this->academic_year);
        if(count($levels)>0){
            $arr_levels = $levels->toArray();
        }

        $arr_academic_year = '';
     
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }

    	$arr_exam_period =[] ;

        $obj_exam_period = $this->ExamPeriodSchoolModel
                                            ->whereHas('get_exam_period',function(){})
                                            ->with('get_exam_period')
                                            ->where('school_id',$this->school_id)
                                            ->whereIn('academic_year_id',$arr_academic_year)
                                            ->where('is_active',1)
                                            ->get();
        if(!empty($obj_exam_period))
        {
            $arr_exam_period = $obj_exam_period ->toArray();

        }                      

        $this->arr_view_data['page_title']      = translation("manage")." ".$this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['arr_levels']      = $arr_levels;
        $this->arr_view_data['arr_exam_period'] = $arr_exam_period;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    /*
    | get_class()   : get classes according to level
    | Auther        : Pooja K  
    | Date          : 27-07-2018
    */
    public function getClasses(Request $request)
    {
        if(!$request->has('level')){
            return;
        }
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
    | get_students(): get students according to level & class
    | Auther        : Pooja K  
    | Date          : 27-07-2018
    */
    public function get_students(Request $request)
    {

        if(!$request->has('class') || !$request->has('exam_period') ){
            Flash::error('all_fields_are_required');
            return redirect($this->module_url_path);
        }


        $id = $request->input('class');
        if(\Session::has('exam_period'))
            \Session::set('exam_period',$request->exam_period);
        else
            \Session::put('exam_period',$request->exam_period);
        
        if(\Session::has('level_id_for_gradebook'))
            \Session::set('level_id_for_gradebook',$request->level);
        else
            \Session::put('level_id_for_gradebook',$request->level);

        if(\Session::has('class_id_for_gradebook'))
            \Session::set('class_id_for_gradebook',$request->class);
        else
            \Session::put('class_id_for_gradebook',$request->class);


        $level_class = $request->class;
        $students = $this->CommonDataService->get_students($level_class);

        $arr_levels = [];
        $levels = $this->CommonDataService->get_levels($this->academic_year);
        if(count($levels)>0){
            $arr_levels = $levels->toArray();
        }

        $arr_academic_year = '';
     
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }

        $arr_exam_period =[] ;

        $obj_exam_period = $this->ExamPeriodSchoolModel
                                            ->whereHas('get_exam_period',function(){})
                                            ->with('get_exam_period')
                                            ->where('school_id',$this->school_id)
                                            ->whereIn('academic_year_id',$arr_academic_year)
                                            ->where('is_active',1)
                                            ->get();
        if(!empty($obj_exam_period))
        {
            $arr_exam_period = $obj_exam_period ->toArray();

        }                      

        $arr_class = [];
        if(\Session::has('level_id_for_gradebook')){
            $arr_class = $this->CommonDataService->get_class(\Session::get('level_id_for_gradebook'));
        }

        $this->arr_view_data['page_title']      = translation("manage")." ".$this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['arr_levels']      = $arr_levels;
        $this->arr_view_data['arr_exam_period'] = $arr_exam_period;
        $this->arr_view_data['arr_students']    = $students;
        $this->arr_view_data['arr_class']       = $arr_class;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
        
    }

   /*
    | get_students(): get students according to level & class
    | Auther        : Pooja K  
    | Date          : 27-07-2018
    */

    public function generate_gradebook($enc_id)
    {
        $id = base64_decode($enc_id); 
        if(!is_numeric($id)){

            Flash::error(translation('something_went_wrong'));
            return redirect($this->module_url_path);
        }

        $fields = $this->GradebookFieldsModel->where('school_id',$this->school_id)->where('is_active',1)->orderBy('id')->get();

        $student = $this->StudentService->get_student_details($id);

        if(count($student)==0){
            Flash::error(translation('something_went_wrong'));
            return redirect($this->module_url_path);
        }

        $first_name  = isset($student->get_user_details->first_name) ? $student->get_user_details->first_name :'';
        $last_name   = isset($student->get_user_details->last_name) ? $student->get_user_details->last_name :'';
        $name        = ucfirst($first_name).' '.ucfirst($last_name);
        $level       = isset($student->get_level_class->level_details->level_name) ? $student->get_level_class->level_details->level_name :'';
        $class       = isset($student->get_level_class->class_details->class_name) ? $student->get_level_class->class_details->class_name :'';
        $school_logo = $this->CommonDataService->get_school_logo();
        $school_name = $this->CommonDataService->get_school_name();
        $principal_name = $this->CommonDataService->get_principle();
        $school_address = $this->CommonDataService->get_school_address();
        $telephone_number = $this->CommonDataService->get_telephone_number();

        $birth_date  = isset($student->get_user_details->birth_date) && $student->get_user_details->birth_date != '0000-00-00' ? getDateFormat($student->get_user_details->birth_date) : '';
        $parent_first_name = isset($student->get_parent_details->first_name)? ucfirst($student->get_parent_details->first_name) : '';
        $parent_last_name  = isset($student->get_parent_details->last_name)? ucfirst($student->get_parent_details->last_name) : '';
        $parent_name = $parent_first_name.' '.$parent_last_name;
        $parent_address = $student->get_parent_details->address;

        $year = $this->AcademicYearModel->where('id',$this->academic_year)->first();
        $academic_year_range = $year->academic_year;

        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year);
        $arr_academic_year = explode(',',$academic_year);

        $arr_course = [];

        $courses = ExamModel::
                        whereHas('get_result',function(){})
                        ->whereHas('get_school_exam_type',function($q){
                            $q->where('gradebook','1');
                        })
                        ->whereHas('get_assessment',function($q){
                            $q->where('type','MARKS');
                        })
                        ->whereHas('get_school_course',function($q){

                        })
                        ->whereHas('get_course',function($q){

                        })
                        ->with('get_course')    
                        ->where('school_id',$this->school_id)
                        ->where('academic_year_id',$this->academic_year)
                        ->where('level_class_id',$student->level_class_id)
                        ->where('exam_period_id',Session::get('exam_period'))
                        ->groupBy('course_id')
                        ->where('status','APPROVED')
                        ->get();        

        if(count($courses)>0){
            $arr_course = $courses->toArray();
        }       

        $exam_period = $this->ExamPeriodModel->where('id',Session::get('exam_period'))->first();
        if(count($exam_period)>0){
            $level_class_id = isset($student->level_class_id) ? $student->level_class_id :0;

            $attendence = $this->StudentPeriodAttendanceModel
                                        ->where('school_id',$this->school_id)
                                        ->where('level_class_id',$level_class_id)
                                        ->where('academic_year_id',$this->academic_year)
                                        ->get();
            $absent_count = 0;
            $present_count = 0;
            $late_count = 0;

            foreach($attendence as $value){
                $result = json_decode($value->attendance,true);
                
                if(array_key_exists($student->user_id,$result)){
                    if(isset($result[$student->user_id])){
                        if($result[$student->user_id] == 'absent'){
                            $absent_count++;        
                        }
                        if($result[$student->user_id] == 'present'){
                            $present_count++;        
                        }
                        if($result[$student->user_id] == 'late'){
                            $late_count++;        
                        }
                    }
                }
            }

            $doubling="";
            if(isset($student->previous_level) && 
               isset($student->get_level_class->level_id) &&  
               $student->previous_level == $student->get_level_class->level_id)
            {
                $doubling = translation('yes');
            }
            else{

                $doubling = translation('no');
            }

            $this->arr_view_data['student_name']    = $name;
            $this->arr_view_data['school_logo']     = $school_logo;
            $this->arr_view_data['school_name']     = $school_name;
            $this->arr_view_data['school_address']  = $school_address;
            $this->arr_view_data['telephone_number']= $telephone_number;
            $this->arr_view_data['level']           = $level;
            $this->arr_view_data['class']           = $class;
            $this->arr_view_data['academic_year']   = $academic_year_range;
            $this->arr_view_data['parent_name']     = $parent_name;
            $this->arr_view_data['parent_address']  = $parent_address;
            $this->arr_view_data['principal_name']  = $principal_name;
            $this->arr_view_data['doubling']        = $doubling;
            $this->arr_view_data['courses']         = $arr_course;
            $this->arr_view_data['level_class_id']  = isset($student->level_class_id) ? $student->level_class_id :0;
            $this->arr_view_data['student_id']      = isset($student->id) ? $student->id :0;

            $this->arr_view_data['present'] = $present_count;
            $this->arr_view_data['absent']  = $absent_count;
            $this->arr_view_data['late']    = $late_count;

            $this->arr_view_data['birth_date']      = $birth_date;
            $this->arr_view_data['page_title']      = translation("view")." ".$this->module_title;
            $this->arr_view_data['module_url_path'] = $this->module_url_path;
            $this->arr_view_data['theme_color']     = $this->theme_color;
            $this->arr_view_data['module_icon']     = $this->module_icon;
            $this->arr_view_data['fields']          = $fields;
            $this->arr_view_data['exam_period']     = $exam_period->exam_name;

            return view($this->module_view_folder.'.view',$this->arr_view_data);    
        }
        else{
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }
        
    }
}
