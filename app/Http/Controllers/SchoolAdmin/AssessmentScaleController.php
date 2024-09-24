<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Common\Traits\MultiActionTrait;
use App\Models\AssessmentScaleModel;  
use App\Models\CourseModel;  
use Session;
use Validator;
use Flash;
use Sentinel;
use DB;
use Datatables;
use App\Common\Services\CommonDataService;

class AssessmentScaleController extends Controller
{
    use MultiActionTrait;

    public function __construct(AssessmentScaleModel $scale,
                                CourseModel $course,
                                CommonDataService $CommonDataService) 
    {
        $this->arr_view_data            =   [];
        $this->AssessmentScaleModel     =   $scale;
        $this->BaseModel                =   $this->AssessmentScaleModel;
        $this->CommonDataService        =   $CommonDataService;
        $this->CourseModel              =   $course;
        $this->module_url_path          =   url(config('app.project.school_admin_panel_slug')."/assessment_scale");
        $this->module_view_folder       =   "schooladmin.assessment_scale";
        $this->module_title             =   translation("assessment_scale");
        $this->module_icon              =   'fa fa-line-chart';
        $this->create_icon              =   'fa fa-plus-circle';
        $this->edit_icon                =   'fa fa-edit';

        $this->theme_color              =   theme_color();
        $this->academic_year            =   Session::has('academic_year')?Session::get('academic_year'):0;
        $this->school_id                =   Session::has('school_id')?Session::get('school_id'):0;
        $this->first_name               =   $this->last_name =$this->ip_address ='';
        $obj_data                       =   Sentinel::getUser();
        
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->id                = $obj_data->id;  
        }
    }
    
    /*
    | index() : assessment scale listing,create,edit page 
    | Auther : sayali 
    | Date : 10-05-2018
    */
    public function index($enc_id=FALSE) 
    {
        $id = 0;
        $arr_scale = $arr_course = [];
        $obj_scale = $this->AssessmentScaleModel->with('course_name')->where('school_id',$this->school_id)->get();
        $obj_course =  $this->CommonDataService->get_courses($this->academic_year);

        if(isset($enc_id) && $enc_id!=null)
        {
            $id = base64_decode($enc_id);
        }
        if($id!=0)
        {
            $obj = $this->BaseModel->where('id',$id)->first();

            if(isset($obj) && $obj!=null)
            {
                $scale_value = $obj->toArray();
            }
            if(isset($scale_value) && !empty($scale_value))
            {
                $this->arr_view_data['scale_value']   = $scale_value;           
            }
        }


        if(isset($obj_scale) && $obj_scale != null)
        {
            $arr_scale = $obj_scale->toArray();
        }
        if(isset($arr_scale) && !empty($arr_scale))
        {
            $this->arr_view_data['arr_scale']   = $arr_scale;    
        }

        if(isset($obj_course) && $obj_course != null)
        {
            $arr_course = $obj_course->toArray();
        }
        if(isset($arr_course) && !empty($arr_course))
        {
            $this->arr_view_data['arr_course']   = $arr_course;    
        }
        $this->arr_view_data['page_title']          = translation("manage")." ".str_plural($this->module_title);
        $this->arr_view_data['module_title']        = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['theme_color']         = $this->theme_color;
        $this->arr_view_data['module_icon']         = $this->module_icon;
        $this->arr_view_data['enc_id']              = $enc_id;  

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }
        /*
        | store() : academic year store 
        | Auther : sayali    
        | Date : 10-05-2018
        */        
    public function store(Request $request)
    {

        $arr_rules['course']      =   'required';
       
        $messages['required']    =   'This field is required';

        if($request->input('scale_type')=='grade')
        {
             $arr_rules['scale']       =   'required|regex:/^[a-zA-Z,]+$/';
        }
        elseif($request->input('scale_type')== 'marks' )
        {
             $arr_rules['scale']       =   'required|regex:/^[\d]+[\-].[\d]+$/';
        }
        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
             return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $does_exists = $this->BaseModel
                            ->where('school_id',$this->school_id)
                            ->where('course_id',$request->input('course'))
                            ->where('scale',$request->input('scale'))
                            ->count();
        if($does_exists>0)
        {
            Flash::error(str_singular($this->module_title).' '.translation('already_exists'));
            return redirect()->back();
        }  
        
        $arr_data['school_id']         = $this->school_id;
        $arr_data['course_id']         = $request->input('course');
        $arr_data['scale']             = $request->input('scale');
        $arr_data['type']              = strtoupper($request->input('scale_type'));
        
        $scale     = $this->BaseModel->create($arr_data);

        
        if($scale)
        {                
            Flash::success($this->module_title .' '.translation('updated_successfully'));       
        } 
        else
        {
            Flash::success(translation('problem_occurred_while_creating').' '.$this->module_title);
        }
        
        return redirect()->back();
    }

    function update(Request $request,$enc_id)
    {
        
        $arr_rules['course']      =   'required';
        $arr_rules['scale']       =   'required|regex:/^[\d]+[\-].[\d]*$/';
        $messages['required']    =   'This field is required';

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        
        $arr_data['school_id']         = $this->school_id;
        $arr_data['course_id']         = $request->input('course');
        $arr_data['scale']             = $request->input('scale');
        $arr_data['type']              = strtoupper($request->input('scale_type'));
        
        $scale     = $this->BaseModel->where('id',base64_decode($enc_id))->update($arr_data);

        
        if($scale)
        {                
            Flash::success($this->module_title .' '.translation('updated_successfully'));       
        } 
        else
        {
            Flash::success(translation('problem_occurred_while_updating').' '.$this->module_title);
        }
        
        return redirect()->back();
    }
}
