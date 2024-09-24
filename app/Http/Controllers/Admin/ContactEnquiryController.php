<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ContactEnquiryModel;

use App\Models\EmailTemplateModel;
use App\Models\EnquiryCategoryModel;
use App\Models\EnquiryCategoryTranslationModel;

use App\Common\Traits\MultiActionTrait;

use App\Common\Services\EmailService;
/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
/*Activity Log */

use Session;
use Validator;
use Flash;
use PDF;
use DB;
use Sentinel;

class ContactEnquiryController extends Controller
{
    use MultiActionTrait;

	public function __construct(ContactEnquiryModel $contact_enquiry,
                                ActivityLogsModel $activity_logs,
                                EmailTemplateModel $email_template,
                                EnquiryCategoryModel $EnquiryCategory,
                                EnquiryCategoryTranslationModel $EnquiryCategoryTranslationModel,
								EmailService $mail_service) 
	{
        $this->arr_view_data 		= [];
		$this->ContactEnquiryModel 	= $contact_enquiry;
        $this->EnquiryCategoryModel = $EnquiryCategory;
        $this->EnquiryCategoryTranslationModel = $EnquiryCategoryTranslationModel;
		$this->EmailTemplateModel 	= $email_template;
		$this->EmailService      	= $mail_service;

        $this->BaseModel            = $this->ContactEnquiryModel;
        $this->ActivityLogsModel    = $activity_logs; /* Activity Model */

		$this->module_url_path 		= url(config('app.project.admin_panel_slug')."/contact_enquiry");
        $this->module_url_path_category      = url(config('app.project.admin_panel_slug')."/enquiry_category");
        $this->module_view_folder   = "admin.contact_enquiry";
        $this->module_title         = translation('contact_enquiry');
        $this->module_name          = translation('enquiry_category');


          /* Activity Section */
        $this->first_name = $this->last_name =$this->ip_address ='';
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->ip_address        = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;  
        }
        /* Activity Section */
	}

	public function index() 
	{
		$arr_contact_enquiry = array();
		$obj_contact_enquiry = $this->BaseModel->with(['get_school_admin.school_admin','enquiry_category' => function($ques){
            $ques->select('id','category_name');

        }])->orderBy('id','DESC')->get();
        
		if($obj_contact_enquiry != FALSE)
		{
			$arr_contact_enquiry = $obj_contact_enquiry->toArray();
		}

		$this->arr_view_data['arr_contact_enquiry'] = $arr_contact_enquiry;
        $this->arr_view_data['page_title'] 			= translation('manage')." ".str_singular($this->module_title);
        $this->arr_view_data['module_title'] 		= $this->module_title;
        $this->arr_view_data['module_url_path'] 	= $this->module_url_path;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
	}

	public function view($enc_id)
	{
		$id = base64_decode($enc_id);

        if(!is_numeric($id)) {
            Flash::error(translation("something_went_wrong"));    
            return redirect()->back();
        }

        $arr_contact_enquiry_details = array();

        $obj_contact_enquiry         = $this->BaseModel->with(['get_school_admin.school_admin','enquiry_category' => function($ques){
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

	public function reply($enc_id)
	{
		$id = base64_decode($enc_id);

        if(!is_numeric($id)) {
            Flash::error(translation("something_went_wrong"));    
            return redirect()->back();
        }

		$arr_contact_enquiry_details = array();
		$obj_contact_enquiry 		 = $this->BaseModel->where('id','=',$id)->first();
		if($obj_contact_enquiry != FALSE)
		{
			$arr_contact_enquiry_details = $obj_contact_enquiry->toArray();
		}

		$this->arr_view_data['arr_contact_enquiry'] = $arr_contact_enquiry_details;
        $this->arr_view_data['page_title'] 			= str_singular($this->module_title)." Reply";
        $this->arr_view_data['module_title'] 		= str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] 	= $this->module_url_path;

        return view($this->module_view_folder.'.reply',$this->arr_view_data);
	}

	public function send_reply(Request $request)
	{
		 $arr_rules['answer'] = 'required';

        $validator= Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {            
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
         
        $answer = $request->input('answer');
        $id = base64_decode($request->input('q_id'));

        $result = $this->ContactEnquiryModel->where('id',$id)->update(array('comments'=>$answer));
        
        if($result)
        {
            /*-------------------------------------------------------
                |   Activity log Event
                --------------------------------------------------------*/
                    $arr_event                 = [];
                    $arr_event['ACTION']       = 'SEND REPLY';
                    $arr_event['MODULE_TITLE'] = $this->module_title;

                    $this->save_activity($arr_event);

                /*----------------------------------------------------------------------*/
            $to_email       = $request->input('email');
            $question       = $request->input('question');        
            $message        = $request->input('answer');
            $full_name      = $request->input('name');        
            if($message)
            {
                $arr_mail_data = $this->built_contact_enquiry_reply_mail_data($message,$to_email,$full_name,$question); 
                $email_status  = $this->EmailService->send_mail($arr_mail_data);
                
                if($email_status)
                {
                    Flash::success('Contact enquiry reply sent successfully');
                    return redirect('/admin/contact_enquiry');
                }
            }
            else
            {            
                Flash::error('Some error occured while sending reply.');
            }    
        }
        else
        {            
            Flash::error('Some error occured while sending reply.');
        }
        return redirect()->back();

	}

	public function built_contact_enquiry_reply_mail_data($message,$to_email,$full_name,$question)
    {

        $arr_built_content = ['QUESTION' 	 => $question,
        					  'MESSAGE' 	 => $message,
                              'NAME' 		 => ucfirst($full_name),
                              'PROJECT_NAME' => config('app.project.name')];

        if($arr_built_content)
        {
            $arr_mail_data                         = [];
            $arr_mail_data['email_template_slug']  = 'enquiery_reply';
            $arr_mail_data['arr_built_content']    = $arr_built_content;
            $arr_mail_data['user']                 = array('email'=> $to_email);
            
            return $arr_mail_data;
        }
        
        return FALSE;
    }

    /*
    | export() : Export List
    | Auther  : Vrajesh
    | Date    : 14-12-2018
    */

    public function export(Request $request)
    {
        $locale = '';

        if(Session::has('locale')){
            $locale = Session::get('locale');
        }else{
            $locale = 'en';
        }
        
        $file_type = config('app.project.export_file_formate');
        $search = 'bugs';
        $enq_details = $this->BaseModel->getTable();
        $enq_cat = $this->EnquiryCategoryModel->getTable();
        $enq_cat_trans = $this->EnquiryCategoryTranslationModel->getTable();
        $prefixed_enq_details = DB::getTablePrefix().$this->BaseModel->getTable();

        $obj_data = $this->BaseModel->with(['get_school_admin.school_admin','enquiry_category' => function($ques){
            $ques->select('id','category_name');

        }])->orderBy('id','DESC')->get();

        /*$obj_data = $this->BaseModel->whereHas('enquiry_category',function($ques)use($search){
                                                $ques->select('id','category_name');
                                                if($search!=''){
                                                        $ques->whereRaw("title like '%".$search."%'");
                                                }
                                        })
                                        ->with(['get_school_admin.school_admin',
                                        'enquiry_category' => function($ques)use($search){
                                                $ques->select('id','category_name');
                                                  if($search!=''){
                                                        $ques->whereRaw("category_name like '%".$search."%'");
                                                }
                                        }])
                                    ->orderBy('id','DESC')
                                    ->get();*/

        /*$obj_enq = DB::table($enq_details)
                                ->select($enq_details.'.subject',$enq_cat.'.*',$enq_cat_trans.'.*' )
                                ->leftjoin($enq_cat,$enq_cat.'.id','=',$enq_details.'.category_id')
                                ->leftjoin($enq_cat_trans,$enq_cat_trans.'.enquiry_category_id','=',$enq_cat.'.id')
                                ->where($enq_cat_trans.'.locale','=',$locale)
                                ->whereNull($enq_details.'.deleted_at')
                                ->groupBy($enq_details.'.id')
                                ->orderBy($enq_details.'.created_at','DESC');*/

        //dd($obj_enq->get());

        $arr_data = array();
        if($obj_data->count() > 0){
            $arr_data = $obj_data->toArray();
        }

        if(sizeof($obj_data)<=0){
            Flash::error(translation("no_records_found_to_export"));
            return redirect()->back();
        }
        if(sizeof($obj_data)>500 && $request->file_format == $file_type ){
            Flash::error(translation("too_many_records_to_export"));
            return redirect()->back();
        }
        if($request->file_format == $file_type){
            \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel) use($arr_data) 
                {
                    $excel->sheet(ucwords($this->module_title), function($sheet) use($arr_data) 
                    {
                        $arr_fields['sr_no']            = translation('sr_no');
                        $arr_fields['contact_enquiry']  = translation('contact_enquiry');
                        $arr_fields['subject']          = translation('subject');
                        $arr_fields['school_name']      = translation('school_name');
                        $arr_fields['email']            = translation('email');
                        $arr_fields['phone']            = translation('phone');
                        $arr_fields['enquiry_number']   = translation('enquiry_number');
                        $arr_fields['description']      = translation('description');

                        $sheet->row(2, [ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                        $sheet->row(4, $arr_fields);

                        // To format mobile bumber
                        $sheet->setColumnFormat([
                            'F' => "#",
                        ]);
                        
                        // To set Colomn head
                        $j = 'A'; $k = '4';
                        for($i=0; $i<=7;$i++)
                        {
                            $sheet->cell($j.$k, function($cells) {
                                $cells->setBackground('#495b79');
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                                $cells->setFontColor('#ffffff');
                            });
                            $j++;
                        }

                        if(sizeof($arr_data)>0)
                        {
                            $arr_tmp = [];
                            $count = 1;
                            foreach($arr_data as $key => $contact_enquiry)
                            {
                                $arr_tmp[$key]['sr_no']             = $count++;

                                $arr_tmp[$key]['contact_enquiry']   = isset($contact_enquiry['enquiry_category']['title'])?$contact_enquiry['enquiry_category']['title']:'';

                                $arr_tmp[$key]['subject']           = isset($contact_enquiry['subject'])?$contact_enquiry['subject']:'';
                                
                                $arr_tmp[$key]['school_name']       = isset($contact_enquiry['get_school_admin']['school_admin']['school_id'])?get_school_name($contact_enquiry['get_school_admin']['school_admin']['school_id']):'';
                                
                                $arr_tmp[$key]['email']             = isset($contact_enquiry['email'])?$contact_enquiry['email']:'';
                                
                                $arr_tmp[$key]['phone']             = isset($contact_enquiry['contact_number'])?$contact_enquiry['contact_number']:'';
                                
                                $arr_tmp[$key]['enquiry_number']    = isset($contact_enquiry['enquiry_no'])?$contact_enquiry['enquiry_no']:'';
                                
                                $arr_tmp[$key]['description']       = isset($contact_enquiry['description'])?str_limit($contact_enquiry['description'],125):'';
                            }
                            $sheet->rows($arr_tmp);
                        }
                    });
                })->export($file_type);     
        }
        
        if($request->file_format == 'pdf')
        {
            $this->arr_view_data['arr_data'] = $arr_data;

            $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
            return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
        }
    }


}
