<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\EducationalBoardModel;
use App\Common\Services\CommonDataService;
use App\Common\Traits\MultiActionTrait;
use App\Models\ProfessorModel;
use App\Models\EmployeeModel;

use DB;
use Flash;
use Session;
use Sentinel;
use Validator;
use Datatables;

class EducationalBoardController extends Controller
{
    use MultiActionTrait;
    function __construct(CommonDataService $CommonDataService){

    	$this->EducationalBoardModel = new EducationalBoardModel();
        $this->BaseModel             = new EducationalBoardModel();
        $this->EmployeeModel         = new EmployeeModel();
        $this->ProfessorModel        = new ProfessorModel();
        $this->CommonDataService            = $CommonDataService;

    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/educational_board';
        $this->module_title                 = translation('educational_board');
 
        $this->module_view_folder           = "schooladmin.educational_board";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-book';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');


    	$this->arr_view_data['module_title'] = translation('educational_board');
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
    function index(){
    	
    	$fields = $this->EducationalBoardModel
    									->where('school_id',$this->school_id)
                                        ->orderBy('id','DESC')
    									->get();

    	$this->arr_view_data['page_title']      = translation("manage")." ".$this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['arr_data']      = $fields;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    function create(){

        $arr_employees  = $this->CommonDataService->get_employees();
        $arr_professors = $this->CommonDataService->get_professor_by_year($this->school_id,$this->academic_year);

        $this->arr_view_data['page_title']      = translation("add")." ".$this->module_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['arr_employees']   = $arr_employees;
        $this->arr_view_data['arr_professors']  = $arr_professors;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        

        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }
    
    function store(Request $request){

        
        $arr_rules['name']           = ['required','regex:/^[a-zA-Z0-9 \-]*$/'];
        $arr_rules['professor']      = 'required';
        $arr_rules['employee']       = 'required';
       
        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'mobile_no.digits_between' => translation('please_enter_mobile_no_within_range_of_10_14'),
                            'telephone_no.digits_between'  => translation('please_enter_telephone_no_within_range_of_6_14'),
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'),
                            'year_of_experience.digits_between' => translation('year_of_experience_can_not_be_greater_than_2_digits')

                        );
       
        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        $professor = implode(',', $request->professor);
        $employee  = implode(',', $request->employee);
        $school_admin = 0;
        if($request->has('school_admin') && $request->get('school_admin')!='0'){
            $school_admin = 1;
        }

        $arr_data= [];
        $arr_data['board']          = trim($request->name);
        $arr_data['school_id']      = $this->school_id;
        $arr_data['professor']      = $professor;
        $arr_data['employee']       = $employee;
        $arr_data['school_admin']   = $school_admin;

        $this->EducationalBoardModel->create($arr_data);

        Flash::success(translation("educational_board_added_successfully"));
        return redirect()->back();
    }

    function edit($enc_id){

        $id = base64_decode($enc_id);
        if(!is_numeric($id)){
            Flash::error(translation('something_went_wrong'));
            return redirect($this->module_url_path);
        }

        $field = $this->EducationalBoardModel->where('id',$id)->first();
        $arr_employees = $this->CommonDataService->get_employees();
        $arr_professors = $this->CommonDataService->get_professor_by_year($this->school_id,$this->academic_year);

        $this->arr_view_data['page_title']      = translation("edit")." ".$this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['arr_employees']   = $arr_employees;
        $this->arr_view_data['arr_professors']  = $arr_professors;
        $this->arr_view_data['field']           = $field;


        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }

    function update(Request $request, $enc_id){

       $id = base64_decode($enc_id); 

        $arr_rules['name']           = ['required','regex:/^[a-zA-Z0-9 \-]*$/'];
        $arr_rules['professor']      = 'required';
        $arr_rules['employee']       = 'required';
       
        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'mobile_no.digits_between' => translation('please_enter_mobile_no_within_range_of_10_14'),
                            'telephone_no.digits_between'  => translation('please_enter_telephone_no_within_range_of_6_14'),
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'),
                            'year_of_experience.digits_between' => translation('year_of_experience_can_not_be_greater_than_2_digits')

                        );
       
        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        $professor = implode(',', $request->professor);
        $employee  = implode(',', $request->employee);
        $school_admin = 0;
        if($request->has('school_admin') && $request->get('school_admin')!='0'){
            $school_admin = 1;
        }
        $arr_data= [];
        $arr_data['board']          = trim($request->name);
        $arr_data['professor']      = $professor;
        $arr_data['employee']       = $employee;
        $arr_data['school_admin']   = $school_admin;

        $this->EducationalBoardModel->where('id',$id)->update($arr_data);

        Flash::success(translation("educational_board_updated_successfully"));
        return redirect()->back();
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

    public function view($enc_id)
    {
        $arr_details = $arr_prof_details = $arr_emp_details = [];
        $id  = base64_decode($enc_id);

        if(!is_numeric($id))
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }

        $details = $this->EducationalBoardModel->where('id',$id)->first();
        
        if(isset($details) && $details!=null && count($details)>0)
        {
            $arr_details = $details->toArray();
        }

        if(!empty($arr_details))
        {
            $prof = explode(',',$arr_details['professor']);
            $emp  = explode(',',$arr_details['employee']);

            foreach ($prof as $key => $value) {
                $prof_details = $this->ProfessorModel
                                     ->with('get_user_details')
                                     ->where('user_id',$value)
                                     ->first();


                if(isset($prof_details) && $prof_details!=null)
                {
                    $prof_details = $prof_details->toArray();
                    $arr_prof_details[$key] = ucwords($prof_details['get_user_details']['first_name'].' '.$prof_details['get_user_details']['last_name']);
                }
            }

            foreach ($emp as $key => $value) {
                $emp_details = $this->EmployeeModel
                                     ->with('get_user_details')
                                     ->where('user_id',$value)
                                     ->first();


                if(isset($emp_details) && $emp_details!=null)
                {
                    $emp_details = $emp_details->toArray();
                    $arr_emp_details[$key] = ucwords($emp_details['get_user_details']['first_name'].' '.$emp_details['get_user_details']['last_name']);
                }
            }
        }
        
        $this->arr_view_data['page_title']      = translation("view")." ".$this->module_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['arr_employees']   = $arr_emp_details;
        $this->arr_view_data['arr_professors']  = $arr_prof_details;
        $this->arr_view_data['arr_data']        = $arr_details;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        

        return view($this->module_view_folder.'.view',$this->arr_view_data);
    }
}
