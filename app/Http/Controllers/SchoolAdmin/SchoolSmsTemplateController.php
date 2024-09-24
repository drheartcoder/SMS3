<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Common\Services\LanguageService;
use App\Common\Traits\MultiActionTrait;
use App\Models\SchoolSmsTemplateModel;
use App\Models\SchoolSmsTemplateTranslationModel;

use Flash;
use Session;
use Sentinel;
use Validator;
use Datatables;

class SchoolSmsTemplateController extends Controller
{
   
	 public function __construct(
                                    LanguageService $langauge,
                                    SchoolSmsTemplateModel $sms_template, 
                                    SchoolSmsTemplateTranslationModel $sms_template_translation
                                    
        )
    {
        $this->SchoolSmsTemplateModel        = $sms_template;
        $this->BaseModel                     = $this->SchoolSmsTemplateModel;
        $this->SchoolSmsTemplateTranslationModel = $sms_template_translation;
        $this->LanguageService               = $langauge;
        $this->arr_view_data                 = [];
        $this->module_title                  = translation("sms_template");
        $this->module_icon                   = "fa fa-mobile";
        $this->edit_icon                     = 'fa fa-edit';
        $this->module_view_folder            = "schooladmin.sms_template";
        $this->module_url_path               = url(config('app.project.role_slug.school_admin_role_slug')."/sms_template");
        $this->theme_color                   = theme_color();
        $this->school_id                     =  \Session::has('school_id') ? \Session::get('school_id') : '0'; 

         /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){

            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->id                = $obj_data->id;
            $this->ip_address        = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;  
        }
        /* Activity Section */
    }
    
    
    /*
    | index() :  Get all email templates and redirect to listing page 
    | Auther  : Padmashri
    | Date    : 9-05-2018
    */
    public function index()
    {   
        $obj_data = $this->BaseModel->where("school_id",$this->school_id)->get();
        $arr_slug = [];
        $obj_data1 = $this->BaseModel->select('template_slug')->where('school_id',$this->school_id)->groupBy('template_slug')->orderBy('template_slug','ASC')->get();
        if($obj_data1!= FALSE)
        {
            $arr_slug = $obj_data1->toArray();
        }
		if($obj_data != FALSE)
        {
            $arr_data = $obj_data->toArray();
        }
    
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['arr_slug']        = $arr_slug;
        $this->arr_view_data['page_title']      = translation('sms_template');
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

     

     /*
    | edit() :  Edit SMS templates
    | Auther  : Padmashri
    | Date    : 9-05-2018
    */
    public function edit($enc_id)
    {
        $id    = base64_decode($enc_id);

        $arr_data = [];
        
        $obj_data = $this->SchoolSmsTemplateModel->where('id', $id)->first();

        if($obj_data != FALSE)
        {
            $arr_data  = $obj_data->toArray(); 
        }
        
        $arr_variables = isset($arr_data['template_variables'])?
        				 explode("~",$arr_data['template_variables']):array();

        $this->arr_view_data['page_title']                   = translation("edit_sms_template");
        $this->arr_view_data['edit_mode']                    = TRUE;
        $this->arr_view_data['arr_lang']                     = $this->LanguageService->get_all_language();
        $this->arr_view_data['arr_data']                     = $arr_data;
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['module_title']                 = $this->module_title;
        $this->arr_view_data['arr_variables']                = $arr_variables;
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        $this->arr_view_data['module_icon']                  = $this->module_icon;
        $this->arr_view_data['edit_icon']                    = $this->edit_icon;
        $this->arr_view_data['id']                           = $id;
        
        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }

     /*
    | update() :  Update SMS templates
    | Auther  : Padmashri
    | Date    : 9-05-2018
    */
    public function update(Request $request, $enc_id)
    {   
		$id = base64_decode($enc_id);

		/*Fetched all related active languages*/
        $arr_lang =  $this->LanguageService->get_all_language(); 
        /* insert record into translation table */
    	
   
        
		$arr_rules['template_subject']	 =	"required";
		$arr_rules['template_html']	     =	"required";
        

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
             Flash::error('Please_fill_all_the_mandatory_fields');
             return redirect()->back()->withErrors($validator)->withInput();
        }

        $arr_data   =   array(
                                    'template_subject'      =>   $request->input('template_subject'),
                                    'template_html'         =>   $request->input('template_html')
                             );

        $entity =   $this->BaseModel->where('id',$id)->update($arr_data);

        if($entity)
        {
            
            Flash::success(str_singular($this->module_title).' '.translation('updated_successfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_updating').' '.str_singular($this->module_title));
        }
        return redirect()->back();
    }
    
     public function change_enabled(Request $request)
    {
        $template_id = $request->template_id;
        $data        = $this->BaseModel->where('id',$template_id)->first();
        $details     = $this->BaseModel->where('template_slug',$data->template_slug)->where('school_id',$this->school_id)->get();

        if($details)
        {
            $arr_details = $details->toArray();
            foreach ($arr_details as $key => $value) {
                $this->BaseModel->where('id',$value['id'])->update(['is_enabled'=>0]);
            }
        }
        $entity = $this->BaseModel->where('id',$template_id)->update(['is_enabled'=>1]);

        if($entity)
        {
            return response()->json(array('status'=>'success','msg'=>translation('template').' '.translation('enabled_successfully')));
        }
        else
        {
            return response()->json(array('status'=>'error','msg'=>translation('something_went_wrong')));
        }
    }
}
