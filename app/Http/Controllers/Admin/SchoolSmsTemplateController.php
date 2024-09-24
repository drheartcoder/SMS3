<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Common\Services\LanguageService;
use App\Common\Traits\MultiActionTrait;
use App\Models\SchoolSmsTemplateModel;
use App\Models\SchoolSmsTemplateTranslationModel;
use App\Models\SmsTemplateModel;


use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\SchoolProfileModel;
use App\Models\SchoolProfileTranslationModel;
use App\Models\SchoolAdminModel;
use App\Models\UserRoleModel;   
use App\Models\RoleModel;

use DB;
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
                                    SchoolSmsTemplateTranslationModel $sms_template_translation,
                                    SmsTemplateModel $SmsTemplateModel,
                                    UserModel $user,
                                    UserTranslationModel $translation,
                                    SchoolProfileModel $profile,
                                    SchoolProfileTranslationModel $schoolProfile,
                                    SchoolAdminModel $school,
                                    UserRoleModel $user_role_model,
                                    RoleModel $role_model
                                   
                                    
        )
    {
        //$this->SmsTemplateModel              = $sms_template;
        $this->SchoolSmsTemplateTranslationModel = $sms_template_translation;
        $this->LanguageService               = $langauge;
        $this->SmsTemplateModel              = $SmsTemplateModel;
        $this->BaseModel                     = $this->SmsTemplateModel;
        $this->arr_view_data                 = [];
        $this->module_title                  = translation("sms_template");
        $this->module_icon                   = "fa fa-mobile";
        $this->module_view_folder            = "admin.sms_template";
        $this->module_url_path               = url(config('app.project.admin_panel_slug')."/sms_template");
        $this->theme_color                   = theme_color();


        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->SchoolProfileModel           = $profile;
        $this->SchoolProfileTranslationModel= $schoolProfile;
        $this->SchoolAdminModel             = $school;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        if(Session::has('locale'))
        {
            $this->locale = Session::get('locale');
        }
        else
        {
            $this->locale = 'en';
        }
    }

    
    
     /*
    | index() :  Get all email templates and redirect to listing page 
    | Auther  : Padmashri
    | Date    : 9-05-2018
    */
    public function index()
    {   
        $obj_data = $this->BaseModel->where('school_id','=',0)->get();

        $arr_slug = [];
        $obj_data1 = $this->BaseModel->select('template_slug')->groupBy('template_slug')->orderBy('template_slug','ASC')->get();
        if($obj_data1!= FALSE)
        {
            $arr_slug = $obj_data1->toArray();
        }
		if($obj_data)
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
    | index() :  Create SMS templates
    | Auther  : Padmashri
    | Date    : 9-05-2018
    */
    public function create()
    {
    	$this->arr_view_data['edit_mode']         = TRUE;
        $this->arr_view_data['page_title']        = translation("create_sms_template");
        $this->arr_view_data['arr_lang']          = $this->LanguageService->get_all_language();
        $this->arr_view_data['module_title']      = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']   = $this->module_url_path;
        $this->arr_view_data['theme_color']       = $this->theme_color;
        $this->arr_view_data['module_icon']       = $this->module_icon;

        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }


    /*
    | store() :  Store SMS templates
    | Auther  : Padmashri
    | Date    : 9-05-2018
    */
    public function store(Request $request)
    {
       
        $arr_rules['template_subject']  	=	"required";  
        $arr_rules['template_html'] 	    =	"required";        
        $arr_rules['variables'] 	   	    =	"required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
             Flash::error('Please_fill_all_the_mandatory_fields');
             return redirect()->back()->withErrors($validator)->withInput();
        }
        foreach ($request->input('variables') as  $key => $value) 
        {
        	$arr_varaible[$key] = "##".$value."##";
        }

        $template_name = trim($request->input('template_subject'));
        $template_slug = str_replace(' ','_',strtolower($template_name));

        /*iF exists */
        $isExists  =  SchoolSmsTemplateModel::where('template_slug','=',$template_slug)->get();
        if(count($isExists)>0)
        {
            Flash::error(str_singular($this->module_title).' '.translation('already_exists'));
            return redirect()->back();
        }


        $arr_data = array(
                                'template_variables' 	=>	 implode("~", $arr_varaible),
        						'school_id'      		=>   '0',
                                'template_subject'      =>  trim($request->input('template_subject')) ,
                                'template_html'         =>   trim($request->input('template_html'))
        				 );
        for($i=0;$i<3;$i++)
            $entity = $this->BaseModel->create($arr_data);    
        
        
        if($entity){
            $schools = $this->SchoolAdminModel->get();

            foreach($schools as $school){
                if($school->school_id!='' && $school->school_id!='0'){
                    for($i=0;$i<3;$i++){
                        $arr_data1 = array(
                                'template_variables'    =>   implode("~",$arr_varaible),
                                'school_id'             =>   $school->school_id,
                                'template_subject'      =>  trim($request->input('template_subject')) ,
                                'template_html'         =>   trim($request->input('template_html'))
                         );
                   
                        $res = $this->BaseModel->create($arr_data1);        
                    }
                    
                
                    Flash::success(str_singular($this->module_title).translation('created_successfully'));
                }
            }    
        }
        else
        {
            Flash::error(translation('problem_occured_while_creating').' '.str_singular($this->module_title));  
        }

        return redirect()->back();
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
        
        $obj_data = $this->SmsTemplateModel->where('id', $id)->first();
       if($obj_data)
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
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        


	    $fetched_email_template = $this->BaseModel->where('id',$id)->first();
        if($fetched_email_template)
        {

            $arr_data = [];
            $arr_data['school_id'] = 0;
            $arr_data['template_subject'] = $request->input('template_subject');
            $arr_data['template_html']    = $request->input('template_html');

            $this->SmsTemplateModel->where('id',$id)->update($arr_data);
            Flash::success(str_singular($this->module_title).' '.translation('updated_successfully'));
            return redirect()->back();
        }
		
    }
    
    /*********** If email templates are available in multiple languages then only it is useful*******/
    public function arrange_locale_wise(array $arr_data)
    {
        if(sizeof($arr_data)>0)
        {
            foreach ($arr_data as $key => $data) 
            {
                unset($arr_data[$key]);
                $arr_data[$data['locale']] = $data;                    
            }

            return $arr_data;
        }
        else
        {
            return [];
        }
    }

    public function change_enabled(Request $request)
    {

        $template_id = $request->template_id;
        $data = $this->BaseModel->where('id',$template_id)->first();
        
        $details = $this->BaseModel->where('template_slug',$data->template_slug)->get();

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