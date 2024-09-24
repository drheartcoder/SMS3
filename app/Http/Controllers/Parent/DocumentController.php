<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\NotificationModel;
use App\Models\SchoolAdminModel;
use App\Models\DocumentsModel;

use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;

use Session;
use Sentinel;
use DB;
use Datatables;
use Validator;
use Flash;

class DocumentController extends Controller
{
     public function __construct( DocumentsModel $document,
                                  SchoolAdminModel $SchoolAdminModel,
                                  NotificationModel $NotificationModel,
                                  EmailService $EmailService,
                                  CommonDataService $CommonDataService)
    {
    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.parent_panel_slug')).'/document';
        $this->module_title                 = translation('document');
 
        $this->module_view_folder           = "parent.document";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-file';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-clock-o';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->level_class_id               = Session::get('level_class_id');
        $this->level_id                     = Session::get('student_level');
        $this->class_id                     = Session::get('student_class');
        $this->kid_id                	    = Session::get('kid_id');
        
        $this->DocumentsModel		        	= $document;
        $this->SchoolAdminModel                 = $SchoolAdminModel;
        $this->NotificationModel                = $NotificationModel;
        $this->EmailService                     = $EmailService;
        $this->CommonDataService                = $CommonDataService;
        $this->arr_view_data['page_title']      = translation('document');
    	$this->arr_view_data['module_url_path'] = $this->module_url_path;
    	$this->arr_view_data['theme_color']     = $this->theme_color;
    	$this->arr_view_data['module_icon']     = $this->module_icon;
    	$this->arr_view_data['edit_icon']       = $this->edit_icon;
    	$this->arr_view_data['create_icon']     = $this->create_icon;
        $this->student_document_base_img_path   	= public_path().config('app.project.img_path.student_documents');
        $this->student_document_public_img_path 	= url('/').config('app.project.img_path.student_documents');

        $this->first_name = $this->last_name = $this->school_admin_email = $this->school_admin_contact =$this->school_admin_id='';
        $this->permissions = [];

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
    }

    /*
    | index()       : Load the document data
    | Auther        : Padmashri Joshi
    | Date          : 21st Jun 2018
    */
    public function index()
    {
        $obj_document = $arr_document = array();
        $obj_document = $this->DocumentsModel
                             ->where('academic_year_id',$this->academic_year)
                             ->where('school_id',$this->school_id)
                             ->where('level_class_id',$this->level_class_id)
                             ->where('student_id',$this->kid_id)
                             ->orderBy('id','desc')
                             ->get();

        if($obj_document)
        {
            $arr_document = $obj_document->toArray();
        }
        

        $this->arr_view_data['arr_document']  = $arr_document;
        $this->arr_view_data['module_title']  = $this->module_title;
        $this->arr_view_data['module_icons']  = $this->module_icon;
        $this->arr_view_data['student_document_base_img_path']  = $this->student_document_base_img_path;
        $this->arr_view_data['student_document_public_img_path']  = $this->student_document_public_img_path;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*
    | store() : Store  Document
    | Auther  : Padmashri
    | Date    : 21-06-2018
    */
    public function store(Request $request)
    {
        
        $arr_rules = [];
        $arr_rules['document_title']    = 'required|regex:/^[a-zA-Z0-9 ]*$/';
        $arr_rules['document']    		= 'required';
        $messages = array(  'required'             => translation('this_field_is_required'),
                            'regex'            => translation('letters_and_numbers_only'));

        $validator = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $file_name		= '';
        $document_title = trim($request->input('document_title'));
        if($request->hasFile('document'))
        {
            $file_name = $request->input('document');
 			$file_extension = strtolower($request->file('document')->getClientOriginalExtension());
            if(in_array($file_extension,['jpg','jpeg','pdf']))
            {




                $fileName = $request->file('document')->getClientOriginalName();
                $fileExtension   = strtolower($request->file('document')->getClientOriginalExtension()); 


                $newFileName = '';
                $pos = strrpos($fileName,'.'.$fileExtension);

                if($pos !== false)
                {
                    $newFileName = substr_replace($fileName,'',$pos,strlen('.'.$fileExtension));
                }





                $file_name = $newFileName.'_'.time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('document')->move($this->student_document_base_img_path , $file_name);
                 
            }else{
                Flash::error(translation('invalid_file_type').' '.str_singular($this->module_title));
                return redirect()->back();
            }
        }


        
        $arr_data = [];     
        $arr_data['school_id']   	   = $this->school_id;
        $arr_data['level_class_id']    = $this->level_class_id;
        $arr_data['academic_year_id']  = $this->academic_year;
        $arr_data['student_id']  	   = $this->kid_id;
        $arr_data['parent_id']  	   = $this->user_id;
        $arr_data['document_title']    = $document_title;
        $arr_data['document_name']     = $file_name;
        
        $res = $this->DocumentsModel->create($arr_data);
        if($res){
            $details = $this->DocumentsModel->with('get_student_details','get_level_class_details.level_details','get_level_class_details.class_details')->where('id',$res->id)->first();
            
            if(array_key_exists('document.app',$this->permissions))
            {
             
                $arr_notification = [];
                $arr_notification['school_id']          =   $this->school_id;
                $arr_notification['from_user_id']       =   $this->user_id;
                $arr_notification['to_user_id']         =   $this->school_admin_id;
                $arr_notification['user_type']          =  config('app.project.role_slug.parent_role_slug');
                $arr_notification['notification_type']  =  'Document Add';
                $arr_notification['title']              =  'New Document Added: '.ucwords($this->first_name.' '.$this->last_name).' added new document for kid ';
                $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.school_admin_role_slug').'/document';
                $this->NotificationModel->create($arr_notification);
            }

            $details          = [
                                        'first_name'  =>  'School Admin',
                                        'email'       =>  $this->school_admin_email,
                                        'mobile_no'   =>  $this->school_admin_contact,
                                        'parent'      =>  ucwords($this->first_name.' '.$this->last_name),
                                        'document'    =>  $document_title,
                                        'kid'         =>  (isset($details['get_student_details']['first_name'])?ucwords($details['get_student_details']['first_name']):'').' '.(isset($details['get_student_details']['last_name'])?ucwords($details['get_student_details']['last_name']):''),
                                        'level'       =>  isset($details['get_level_class_details']['level_details']['level_name'])?$details['get_level_class_details']['level_details']['level_name']:'',
                                        'class'       =>  isset($details['get_level_class_details']['class_details']['class_name'])?$details['get_level_class_details']['class_details']['class_name']:''

                                ];
            if(array_key_exists('document.sms',$this->permissions))
            {
                $arr_sms_data = $this->built_sms_data($details);
                $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
            }
            if (array_key_exists('document.email',$this->permissions))
            {
                $arr_mail_data = $this->built_mail_data($details);
                $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
            }
            Flash::success($this->module_title." ".translation("created_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_creating ".$this->module_title);
            return redirect()->back();
        }        

    }

     /*
    | store() : Download student Document uploaded by parent
    | Auther  : Padmashri
    | Date    : 21-06-2018
    */
    public function download_document($enc_id)
    {
        $arr_document = [];
        if(isset($enc_id))
        {
            $document_id = base64_decode($enc_id);
            $obj_documents = $this->DocumentsModel->where('id',$document_id)
                                                    ->select('document_name')
                                                    ->first();
            if($obj_documents)
            {
                  $arr_document    = $obj_documents->toArray();
                  $file_name       = $arr_document['document_name'];
                  $pathToFile      = $this->student_document_base_img_path.$file_name;

                  $file_exits      = file_exists($pathToFile);
                  if($file_exits)
                  {	

                  	   return response()->download($pathToFile, $file_name); 
                  }
                  else
                  {
                     Flash::error(translation("error_while_downloading_an_document"));
                  }
                  
             }
        }
        else
        {
           Flash::error(translation("error_while_downloading_an_document"));
        }
        return redirect()->back();
    }


    public function delete($enc_id){
    	if($enc_id){
    		$id = base64_decode($enc_id);
    		$res = $this->delete_document($id);
    		if($res){
    			 Flash::success($this->module_title.' '.translation('deleted_succesfully'));
    		}else{
    			 Flash::error(translation('problem_occured_while_doing').' '.translation('deleting_records') );
    		}

    	}
    	return redirect()->back();
    }


     /*
    | delete()  : multi_action() multiaction for  the survey 
    | Auther  : Padmashri
    | Date    : 15-05-2018
    */
    public function multi_action(Request $request)
    {
        $arr_rules = array();
        $arr_rules['multi_action'] = "required";
        $arr_rules['checked_record'] = "required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            Flash::error(translation('please_select_record_to_perform_multiaction'));
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $multi_action = $request->input('multi_action');
        $checked_record = $request->input('checked_record');

        /* Check if array is supplied*/
        if(is_array($checked_record) && sizeof($checked_record)<=0)
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction') );
            return redirect()->back();
        }

        foreach ($checked_record as $record_id) 
        {  
            if($multi_action=="delete")
            {
               $this->delete_document(base64_decode($record_id));    
               Flash::success($this->module_title.' '.translation('deleted_succesfully')); 
            } 
            
        }

        return redirect()->back();
    }

    public function delete_document($id){
    	$isExists = DocumentsModel::where('id',$id)->first();
    	if(!empty($isExists)){
    		$data_rs = $isExists->toArray();
    	  if(isset($data_rs['document_name']) && ($data_rs['document_name'])!=''){
              $fileURL = $this->student_document_base_img_path.'/'.$data_rs['document_name'];
			  if(file_exists($fileURL))
              {
              	
              		@unlink($this->student_document_base_img_path.$data_rs['document_name']);
              }
          } 
          $res = $this->DocumentsModel->where('id',$id)->delete();
          return $res;
    	}else{
    		return false;
    	}
	}

    public function built_mail_data($arr_data)
     {
        
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                  'PARENT_NAME'      => $arr_data['parent'],
                                  'DOCUMENT'         => $arr_data['document'],
                                  'KID_NAME'         => $arr_data['kid'],
                                  'LEVEL'            => $arr_data['level'],
                                  'CLASS'            => $arr_data['class']];

            $arr_mail_data                        = [];
            $arr_mail_data['email_template_slug'] = 'add_document';
            
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
                                  'PARENT_NAME'      => $arr_data['parent'],
                                  'DOCUMENT'         => $arr_data['document'],
                                  'KID_NAME'         => $arr_data['kid'],
                                  'LEVEL'            => $arr_data['level'],
                                  'CLASS'            => $arr_data['class']];
            

            $arr_sms_data                      = [];
            $arr_sms_data['sms_template_slug'] = 'add_document';
            
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }


}
