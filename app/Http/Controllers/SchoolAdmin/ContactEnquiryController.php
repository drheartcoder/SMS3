<?php

namespace App\Http\Controllers\SchoolAdmin;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactEnquiryModel;

use App\Models\UserModel;                            
use App\Models\EmailTemplateModel;
use App\Models\EnquiryCategoryModel;
use App\Models\NotificationModel;
use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;

use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   

use Session;
use Validator;
use Flash;
use Sentinel;
                                                
class ContactEnquiryController extends Controller
{
	public function __construct(
                                UserModel $user,
                                ContactEnquiryModel $contact_enquiry,
                                ActivityLogsModel $activity_logs,
                                EmailTemplateModel $email_template,
                                EnquiryCategoryModel $EnquiryCategory,
								EmailService $mail_service,
                                CommonDataService $CommonDataService,
                                NotificationModel $NotificationModel
                                ) 
	{
        $this->arr_view_data 		= [];
		$this->ContactEnquiryModel 	= $contact_enquiry;
        $this->EnquiryCategoryModel = $EnquiryCategory;
		$this->EmailTemplateModel 	= $email_template;
		$this->EmailService      	= $mail_service;
        $this->CommonDataService    = $CommonDataService;
        $this->UserModel            = $user;
        $this->BaseModel            = $this->ContactEnquiryModel;
        $this->NotificationModel    = $NotificationModel;
        $this->ActivityLogsModel    = $activity_logs; 
        $this->module_url_path      = url(config('app.project.school_admin_panel_slug')).'/contact_support';
        $this->module_view_folder   = "schooladmin.contact_support";
        $this->module_title         = translation("contact_support");
        $this->modyle_url_slug      = translation("contact_support");
        $this->module_name          = translation("enquiry_category");
        $this->theme_color          = theme_color();

        $this->first_name = $this->last_name =$this->ip_address ='';

        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->user_id           = $obj_data->id;  
        }
        $this->school_id  = \Session::has('school_id')?\Session::get('school_id'):0;
        
	}

    public function index() 
    {   
        $arr_contact_enquiry = array();
        $obj_contact_enquiry = $this->BaseModel->where('sender_id',$this->school_id)->with(['enquiry_category' => function($ques){
            $ques->select('id','category_name');

        }])->orderBy('id','DESC')->get();
        
        if($obj_contact_enquiry != FALSE)
        {
            $arr_contact_enquiry = $obj_contact_enquiry->toArray();
        }

        $this->arr_view_data['arr_contact_enquiry'] = $arr_contact_enquiry;
        $this->arr_view_data['page_title']          = translation('manage')." ".str_singular($this->module_title);
        $this->arr_view_data['module_title']        = $this->module_title;
        $this->arr_view_data['module_url_path']     = $this->module_url_path;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

        /*
        | create() : Contact support create 
        | Auther : Gaurav 
        | Date : 08-05-2018
        */

	public function create(Request $request) 
	{
            $arr_category = [];
            $obj_category = $this->EnquiryCategoryModel->get();
            $arr_category = $obj_category->toArray();
            

            $page_title                             = translation("create")." ".$this->module_title;
            
            $this->arr_view_data['edit_mode']       = TRUE;
            $this->arr_view_data['arr_category']    = $arr_category; 
            $this->arr_view_data['page_title']      = $page_title;
            $this->arr_view_data['module_title']    = $this->module_title;
            $this->arr_view_data['module_url_path'] = $this->module_url_path;
            $this->arr_view_data['theme_color']     = $this->theme_color;
            return view($this->module_view_folder.'.create', $this->arr_view_data);
	}


    /*
        | store() : Contact support store 
        | Auther : Gaurav 
        | Date : 08-05-2018
        */
    public function store(Request $request)
    {

        $arr_rules  =   $messages  = [];
        $arr_rules['category_id']  =   'required';
        $arr_rules['subject']      =   'required';
        $arr_rules['description']  =   'required';
                        
        $messages['required']      =   'This field is required';

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {  
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        $obj_data       = Sentinel::getUser();
        $email          = isset($obj_data->email) ? $obj_data->email:'-';
        $mobile_no      = isset($obj_data->mobile_no) ? $obj_data->mobile_no:'-';
        $first_name     = isset($obj_data->first_name) ? $obj_data->first_name:'-';
        $last_name      = isset($obj_data->last_name) ? $obj_data->last_name:'-';
        
        $description    = $request->Input('description');
        $subject        = $request->Input('subject');
        $category_id    = $request->Input('category_id');
        $enquiry_no     = $this->generate_enquiry_no();

        $obj_admin      = $this->UserModel->select('id','email')->where('id',1)->first(); 
      
        if ($obj_admin) 
        {
            $to_email         = isset($obj_admin->email) ? $obj_admin->email:'';
            $first_name       = isset($obj_admin->first_name) ? $obj_admin->first_name:'';
        }

        $data           =   [];
        $data           =   [
                                'category_id'    =>  $category_id,
                                'subject'        =>  $subject,
                                'description'    =>  $description,
                                'email'          =>  $email,
                                'contact_number' =>  $mobile_no,
                                'enquiry_no'     =>  $enquiry_no,
                                'sender_id'      =>  $this->school_id
                            ];
        $status = $this->BaseModel->create($data);                   
        if($status)                                                                                                 
        {
            $arr_event                 = [];
            $arr_event['ACTION']       = 'ADD';
            $arr_event['MODULE_TITLE'] = $this->module_title;
            $this->save_activity($arr_event);

            $school_name = $this->CommonDataService->get_school_name($this->school_id);
            $enquiry     = $this->EnquiryCategoryModel->where('id',$category_id)->first();
            $user_name   = ucwords($this->first_name.' '.$this->last_name);
            
            $arr_notification = [];
            $arr_notification['from_user_id']       =  $this->user_id;
            $arr_notification['to_user_id']         =  1;
            $arr_notification['school_id']          =  $this->school_id;
            $arr_notification['user_type']          =  config('app.project.role_slug.professor_role_slug');
            $arr_notification['notification_type']  =  'Contact Enquiry';
            $arr_notification['title']              =  'Contact enquiry sent by '.$user_name.' from '.$school_name;
            $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.student_role_slug').'/homework';
            $this->NotificationModel->create($arr_notification);

            $mail = $this->built_contact_enquiry_reply_mail_data($enquiry->title,$to_email,$first_name,$school_name,$user_name);
            $this->EmailService->send_mail($mail);
                  
            Flash::success(str_plural($this->module_title).' '.translation('created_successfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_creating').' '.str_singular($this->module_title));
        }
        return redirect()->back();                    
    }
         
        /*
        | built_contact_enquiry_reply_mail_data() : Contact support built_contact_enquiry_reply_mail_data 
        | Auther : Gaurav 
        | Date : 08-05-2018
        */
	public function built_contact_enquiry_reply_mail_data($title,$to_email,$first_name,$school,$user)
    {
        $arr_built_content = [
                              'USER_NAME'                => ucwords($this->first_name.' '.$this->last_name),
                              'FIRST_NAME' 	             => ucfirst($first_name),
                              'CONTACT_ENQUIRY_CATEGORY' => ucwords($title),
                              'SCHOOL_NAME'              => $school,
                              'SCHOOL_ADMIN'             => $school];

        if($arr_built_content)
        {
            $arr_mail_data                         = [];
            $arr_mail_data['email_template_slug']  = 'contact_enquiry';
            $arr_mail_data['arr_built_content']    = $arr_built_content;
            $arr_mail_data['user']                 = array('email'=> $to_email);
            
            return $arr_mail_data;
        }
        
        return FALSE;
    }

    public function generate_enquiry_no()
    {  

        $enquiry_no  =   'SUB'.rand(1000,9999); 
        
        $exist = $this->BaseModel->where('enquiry_no',$enquiry_no)->first();
        if($exist)
        {
            $enquiry_no = $this->generate_enquiry_no();
        }
       
        return  $enquiry_no;
    }
    public function view($enc_id)
    {
        $id = base64_decode($enc_id);

        if(!is_numeric($id)) {
            Flash::error(translation("something_went_wrong"));    
            return redirect()->back();
        }

        $arr_contact_enquiry_details = array();

        $obj_contact_enquiry         = $this->BaseModel->with(['enquiry_category' => function($ques){
            $ques->select('id','category_name');

        },'get_user'])->where('id','=',$id)->first();

        if($obj_contact_enquiry != FALSE)
        {
            $arr_contact_enquiry_details = $obj_contact_enquiry->toArray();
        }

        if(isset($arr_contact_enquiry_details))
        {
          
            $this->arr_view_data['arr_contact_enquiry'] = $arr_contact_enquiry_details;
            $this->arr_view_data['page_title']          = "View ".str_singular($this->module_title);
            $this->arr_view_data['module_title']        = $this->module_title;
            $this->arr_view_data['module_url_path']     = $this->module_url_path;

            return view($this->module_view_folder.'.view',$this->arr_view_data);
            }
            else
            {
                Flash::error(translation("something_went_wrong"));    
                return redirect()->back();
            }
        }
}