<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\GradebookFieldsModel;

use App\Common\Traits\MultiActionTrait;

use DB;
use Flash;
use Session;
use Sentinel;
use Validator;
use Datatables;


class GradebookFieldsController extends Controller
{
    use MultiActionTrait;
    function __construct(){

    	$this->GradebookFieldsModel = new GradebookFieldsModel();
        $this->BaseModel            = new GradebookFieldsModel();

    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/gradebook_fields';
        $this->module_title                 = translation('gradebook_fields');
 
        $this->module_view_folder           = "schooladmin.gradebook_fields";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-tasks';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');


    	$this->arr_view_data['module_title'] = translation('gradebook_fields');
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
    	
    	$fields = $this->GradebookFieldsModel
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

        $this->arr_view_data['page_title']      = translation("add")." ".$this->module_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        

        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }
    function store(Request $request){

        $arr_rules['name']           = ['required','regex:/^[a-zA-Z ]*$/'];
        $arr_rules['type']           = ['required','regex:/^(complement|warning)$/'];
       
        $messages = array(
                            'regex'                             => translation('please_enter_valid_text_format'),
                            'email'                             => translation('please_enter_valid_email'),
                            'numeric'                           => translation('please_enter_digits_only'),
                            'mobile_no.digits_between'          => translation('please_enter_mobile_no_within_range_of_10_14'),
                            'telephone_no.digits_between'       => translation('please_enter_telephone_no_within_range_of_6_14'),
                            'required'                          => translation('this_field_is_required'),
                            'date'                              => translation('please_enter_valid_date'),
                            'year_of_experience.digits_between' => translation('year_of_experience_can_not_be_greater_than_2_digits')

                        );
       
        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $arr_data= [];
        $arr_data['name']          = $request->name;
        $arr_data['type']          = strtoupper($request->type);
        
        $arr_data['default_value1']  = trim($request->default_value1);
        $arr_data['default_value2']  = trim($request->default_value2);
        
        if($request->type == "complement"){
            
            $arr_data['default_value3']  = trim($request->default_value3);
        }

        $arr_data['school_id']     = $this->school_id;

        $this->GradebookFieldsModel->create($arr_data);

        Flash::success(translation("field_added_successfully"));
        return redirect()->back();
    }

    function edit($enc_id){

        $id = base64_decode($enc_id);
        if(!is_numeric($id)){
            Flash::error(translation('something_went_wrong'));
            return redirect($this->module_url_path);
        }

        $field = $this->GradebookFieldsModel->where('id',$id)->first();

        $this->arr_view_data['page_title']      = translation("edit")." ".$this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['field']           = $field;


        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }

    function update(Request $request, $enc_id){

       $id = base64_decode($enc_id); 

        $arr_rules['name']           = ['required','regex:/^[a-zA-Z ]*$/'];
        $arr_rules['type']           = ['required','regex:/^(complement|warning)$/'];
       
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

        $arr_data= [];
        $arr_data['name']          = $request->name;
        $arr_data['type']          = strtoupper($request->type);
        $arr_data['default_value1']  = trim($request->default_value1);
        $arr_data['default_value2']  = trim($request->default_value2);
        
        if($request->type == "complement"){
            
            $arr_data['default_value3']  = trim($request->default_value3);
        }

        $this->GradebookFieldsModel->where('id',$id)->update($arr_data);

        Flash::success(translation("field_updated_successfully"));
        return redirect()->back();
    }
}
