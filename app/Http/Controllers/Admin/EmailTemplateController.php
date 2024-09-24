<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;

use App\Models\EmailTemplateModel;
use App\Models\EmailTemplateTranslationModel;
use App\Models\SiteSettingModel;

use App\Common\Services\LanguageService;

use Validator;
use Flash;
use Sentinel;
class EmailTemplateController extends Controller
{   
	public function __construct(
                                    EmailTemplateModel $email_template, 
                                    LanguageService $langauge,
                                    EmailTemplateTranslationModel $email_template_translation,
                                    SiteSettingModel $site_setting
        )
    {
        $this->EmailTemplateModel            = $email_template;
        $this->BaseModel                     = $this->EmailTemplateModel;
        $this->EmailTemplateTranslationModel = $email_template_translation;
        $this->SiteSettingModel              = $site_setting;
        $this->LanguageService               = $langauge;
        $this->arr_view_data                 = [];
        $this->module_title                  = translation("email_template");
        $this->module_icon                  = "fa fa-envelope";
        $this->module_view_folder            = "admin.email_template";
        $this->module_url_path               = url(config('app.project.admin_panel_slug')."/email_template");
        $this->theme_color                   = theme_color();

        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
    }
    
    /************* Get all email templates and redirect to listing page ***********/
    public function index()
    {   
        $arr_slug = [];
        $obj_data = $this->BaseModel->get();
        $obj_data1 = $this->BaseModel->select('slug')->groupBy('slug')->orderBy('slug','ASC')->get();
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
        $this->arr_view_data['page_title']      = translation("manage_email_template");
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    /************* Get detailed information in email template ***********/
    public function view($enc_id,$act_lng)
    {

        $id   = base64_decode($enc_id);
        $html = ''; 
        $subject = ''; 
        $obj_email_template = $this->BaseModel->where('id',$id)
                                              ->first();

        if($obj_email_template)
        {
            $arr_email_template = $obj_email_template->toArray();

            $html = $arr_email_template['template_html'];
            $subject = $arr_email_template['template_subject'];    

            $content  = isset($html)&&sizeof($html)>0?$html:'';
            $subject  = isset($subject)&&sizeof($subject)>0?$subject:'';

            $site_url = '<a href="'.url('/').'">'.config('app.project.name').'</a>.<br/>' ;

            $content  = str_replace("##SITE_URL##",$site_url,$content);  

            return view('email.front_general',compact('content','subject'))->render();
        }
        else
        {
            return redirect()->back();
        }
    }

    /************* redirect to create view of email template ***********/
    public function create()
    {
        $this->arr_view_data['page_title']      = translation("create_email_template");

        $this->arr_view_data['arr_lang']          = $this->LanguageService->get_all_language();
        $this->arr_view_data['module_title']      = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']   = $this->module_url_path;
        $this->arr_view_data['theme_color']       = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;

        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    /***************** store new email template in database ******************/
    public function store(Request $request)
    {
        $arr_rules['template_name'] 	    =	"required";  
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

        $arr_site_settings = [];
        $site_setting = $this->SiteSettingModel->first();
        if($site_setting) 
        {
            $arr_site_settings = $site_setting->toArray();
        }

        $this->site_email_address = isset($arr_site_settings['site_email_address']) && $arr_site_settings['site_email_address'] != "" ? $arr_site_settings['site_email_address'] : 'info@printingstore.com';

        $arr_data = array(
                                'template_variables' 	=>	 implode("~", $arr_varaible),
        						'template_from_mail' 	=>	 $this->site_email_address,
        						'template_from'			=>	 'Admin',
                                'template_name'         =>   $request->input('template_name'),
                                'template_subject'      =>   $request->input('template_subject'),
                                'template_html'         =>   $request->input('template_html')
        				 );

        $entity = $this->BaseModel->create($arr_data);

        if($entity)
        {
            Flash::success(str_singular($this->module_title).translation('created_successfully'));
 		}
 		else
 		{
 			Flash::error(translation('problem_occured_while_creating').' '.str_singular($this->module_title));	
 		}

       return redirect()->back();
    }

    
    /*********** Get details of email template and redirect to edit view ******************/
    public function edit($enc_id)
    {
        $id    = base64_decode($enc_id);
        if(!is_numeric($id))
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }
        $arr_data = [];     
        $obj_data = $this->BaseModel->where('id', $id)->first();

        if($obj_data != FALSE)
        {
            $arr_data  = $obj_data->toArray(); 
        }
        else
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();   
        }

        $arr_variables = isset($arr_data['template_variables'])?
        				 explode("~",$arr_data['template_variables']):array();

        $this->arr_view_data['page_title']                   = translation("edit_email_template");
        $this->arr_view_data['edit_mode']                    = TRUE;
        $this->arr_view_data['arr_data']                     = $arr_data;
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['module_title']                 = $this->module_title;
        $this->arr_view_data['module_icon']                  = $this->module_icon;
        $this->arr_view_data['arr_variables']                = $arr_variables;
        
        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }

    /*********** update email template in database ******************/
    public function update(Request $request, $enc_id)
    {   
		$id = base64_decode($enc_id);

    	$arr_rules['template_name'] 		=	"required";
    	$arr_rules['template_from']			=	"required";
    	$arr_rules['email_template_from']	=	"required";
    	$arr_rules['template_subject']		=	"required";
    	$arr_rules['template_html']			=	"required";

        $messages['required']   =   translation('this_field_is_required');

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }        

    	$arr_data  	=   array(
                                    'template_name'         =>   $request->input('template_name'),
									'template_from'			=>	 $request->input('template_from'),
									'template_from_mail'	=>	 $request->input('email_template_from'),
                                    'template_subject'      =>   $request->input('template_subject'),
                                    'template_html'         =>   $request->input('template_html')
    							);



    	$entity = 	$this->BaseModel->where('id',$id)->update($arr_data);

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
        $data = $this->BaseModel->where('id',$template_id)->first();
        $details = $this->BaseModel->where('slug',$data->slug)->get();

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
