<?php
namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
 
use App\Models\SurveyModel;
use App\Models\StudentModel;
use App\Models\SurveyImagesModel;
use App\Models\SurveyQuestionsModel;
use App\Models\QuestionCategoryModel;
use App\Common\Services\LanguageService;
use App\Models\SurveyQuestionsAnswerModel;
use App\Common\Services\CommonDataService;
use App\Models\SchoolAdminModel;
use App\Models\NotificationModel;
use App\Common\Services\EmailService;

use Session;
use Sentinel;
use DB;
use Datatables;
use Validator;
use Flash;

class SurveyController extends Controller
{
     public function __construct(
                                 	 
                                    CommonDataService $common_data_service,
                                    SurveyModel $survey,
                                    LanguageService $language,
                                    QuestionCategoryModel $question_category,
                                	StudentModel $student_model,
                                    SchoolAdminModel $SchoolAdminModel,
                                    NotificationModel $NotificationModel,
                                    EmailService $EmailService)
    {
    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.student_panel_slug')).'/survey';
        $this->module_title                 = translation('survey');
 
        $this->module_view_folder           = "student.survey";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-bar-chart';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-bar-chart';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->level_class_id               = Session::get('level_class_id');

    	$this->SurveyModel                  = $survey;
        $this->BaseModel                    = $this->SurveyModel;
        $this->StudentModel 				= $student_model;
        $this->CommonDataService            = $common_data_service;
        $this->EmailService                 = $EmailService;
        $this->SchoolAdminModel             = $SchoolAdminModel;
        $this->NotificationModel            = $NotificationModel;
        $this->role 						= config('app.project.student_panel_slug');


    	$this->arr_view_data['page_title']      = translation('survey');
    	$this->arr_view_data['module_url_path'] = $this->module_url_path;
    	$this->arr_view_data['theme_color']     = $this->theme_color;
    	$this->arr_view_data['module_icon']     = $this->module_icon;
    	$this->arr_view_data['edit_icon']       = $this->edit_icon;
    	$this->arr_view_data['create_icon']     = $this->create_icon;

        $this->first_name = $this->last_name = $this->school_admin_email = $this->school_admin_contact =$this->school_admin_id='';
        $this->permissions = [];
        
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

    	$obj_data          = Sentinel::getUser();
    	$arr_current_user_access =[];
        if($obj_data)
        {
            $student = $this->StudentModel->where('user_id',$obj_data->id)->first();

            if(empty($student))
            {
                return redirect()->back();
            }
            $this->student_id = $student->id ;
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;

	        $role = Sentinel::findRoleBySlug($this->role);
	        $this->arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [];
        }
        else
        {
            return redirect()->back();
        }
	}

	 /*
    | index() : List Survey
    | Auther  : Padmashri
    | Date    : 16-06-2018
    */
    public function index(){

        $page_title = translation("manage")." ".$this->module_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    /*
    | get_details() : Get List Survey
    | Auther  : Padmashri
    | Date    : 16-06-2018
    */
    public function get_details(Request $request){
        
        $search_term = '';
        $search      = $request->input('search');
        $search_term = $search['value'];
        $obj_user    = $this->CommonDataService->get_survey($this->role,$search_term);               
        return $obj_user;
    }


    /*
    | get_records() : To get the List Survey
    | Auther  : Padmashri
    | Date    : 16-06-2018
    */
    public function get_records(Request $request){

       
        $obj_user        = $this->get_details($request);
        $current_context = $this;
        $arr_current_user_access = $this->arr_current_user_access;
        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        $json_result     = $json_result->editColumn('build_action_btn',function($data) use ($current_context,$arr_current_user_access){
                                           
                                $build_reply_action = $build_view_action =  ''; 
                                $date =date('Y-m-d');
                               if(array_key_exists('survey.update', $arr_current_user_access))
                               {
                                   
                                	$reply_href =  $this->module_url_path.'/reply_survey/'.base64_encode($data->id);
                                    $isSurveyReplied =  $this->CommonDataService->is_survey_replied($this->user_id,$data->id);

                                    $date =date('Y-m-d');
                                     
                                    if($isSurveyReplied > 0){

                                    	$build_reply_action =  '<a style="position: relative;" class="green-color" href="javascript:void(0)" title="'.translation('reply_survey').'" 
                                    	onclick="return showAlert(\''.translation('you_have_already_given_reply_to_the_survey').'\',\'warning\')"><i class="fa fa-reply"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                    }else if($data->end_date < $date ){
                                    	$build_reply_action = '<a style="position: relative;" class="green-color" href="javascript:void(0)" title="'.translation('reply_survey').'" 
                                    	onclick="return showAlert(\''.translation('you_can_not_reply_completed_survey').' \',\'warning\')"><i class="fa fa-reply"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
							   		}else{
                                    	$build_reply_action = '<a class="green-color" href="'.$reply_href.'" title="'.translation('reply_survey').'"><i class="fa fa-reply" ></i></a> ';
                                    }
                                   
                                }

                                $view_reply = $this->module_url_path.'/view_reply/'.base64_encode($data->id);
                                if($isSurveyReplied > 0){
                                    $build_view_action = '<a class="green-color" href="'.$view_reply.'" title="'.translation('view').'"><i class="fa fa-eye"></i></a>';
                                }

                                return $build_view_action.'&nbsp;'.$build_reply_action;
                                    
                             });
         
        $json_result =      $json_result->editColumn('start_date',function($data){
                                    if(isset($data->start_date) && $data->start_date!='0000-00-00'){
                                        return getDateFormat($data->start_date);
                                    }else{
                                        return '-';
                                    }
                                })
                                 ->editColumn('end_date',function($data){
                                   if(isset($data->end_date) && $data->end_date!='0000-00-00'){
                                        return getDateFormat($data->end_date);
                                    }else{
                                        return '-';
                                    }

                                })
                                ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

      /*
    | view()  : Reply Survey
    | Auther  : Padmashri
    | Date    : 13-06-2018
    */
    function reply_survey($enc_id){
        $obj_data = $arr_data = array();
        if($enc_id){
            $id =  base64_decode($enc_id);

            $obj_data = SurveyModel::with(['get_survey_images',
                                          'get_questions'=>function($q){ 
                                                $q->with(['get_question_type']);
                                                } 
                                          ])->where('id',$id)->first();
            if(!empty($obj_data)){
                $arr_data = $obj_data->toArray();
                $option_arr = $this->CommonDataService->get_question_category();

                $this->arr_view_data['page_title']      = $this->module_title;
                $this->arr_view_data['module_title']    = translation('reply_survey');
                $this->arr_view_data['module_url_path'] = $this->module_url_path;
                $this->arr_view_data['theme_color']     = $this->theme_color;
                $this->arr_view_data['module_icon']     = $this->module_icon;
                $this->arr_view_data['view_icon']       = 'fa fa-reply';
                $this->arr_view_data['option_arr']      = $option_arr;
                $this->arr_view_data['arr_data']        = $arr_data;
                $this->arr_view_data['enc_id']          = $enc_id;
                $this->arr_view_data['surveyUploadImagePath']     = public_path().config('app.project.img_path.survey_image');
                $this->arr_view_data['surveyUploadImageBasePath'] = url('/').config('app.project.img_path.survey_image');
          
                return view($this->module_view_folder.'.reply_survey',$this->arr_view_data);
            }

        }
        return redirect()->back();
    }



     /*
    | store_survey_reply()  : Reply Survey
    | Auther  : Padmashri
    | Date    : 13-06-2018
    */
    function store_survey_reply(Request $request,$enc_id){
       //dump($request->all());
        $obj_questions = $arr_question = array();
        $survey_id     = base64_decode($enc_id);
        $res           = '';
        if($survey_id){

            $answer = $request->input('answer');
            if(!empty($answer)){
                    $obj_questions = SurveyQuestionsModel::where('survey_id',$survey_id)->get();
                    if($obj_questions){
                        $arr_question = $obj_questions->toArray();
                    }
                    foreach ($arr_question as $key => $value) {
                            if(!empty($answer[$value['id']])){

                                $arr_questions                           = []; 
                                $arr_questions['survey_id']              = $survey_id;
                                $arr_questions['from_user_id']           = $this->user_id;
                                $arr_questions['survey_question_id']     = $value['id'];
                                if($value['question_category_id'] == 4 ){
                                 $arr_questions['answer']                 = implode(',',$answer[$value['id']]);
                                }else{
                                    $arr_questions['answer']                 = $answer[$value['id']];
                                }

                                $arr_questions['user_role']              = $this->role;
                                //dd($arr_questions);
                                $res = SurveyQuestionsAnswerModel::create($arr_questions);   

                            }     
                    }
                if($res){

                    if(array_key_exists('survey.app',$this->permissions))
                    {
                     
                        $arr_notification = [];
                        $arr_notification['school_id']          =   $this->school_id;
                        $arr_notification['from_user_id']       =   $this->user_id;
                        $arr_notification['to_user_id']         =   $this->school_admin_id;
                        $arr_notification['user_type']          =   config('app.project.role_slug.student_role_slug');
                        $arr_notification['notification_type']  =   'Survey Response';
                        $arr_notification['title']              =   'Survey Response: '.ucwords($this->first_name.' '.$this->last_name).' '.ucwords(config('app.project.role_slug.student_role_slug')).' responded on survey';
                        $arr_notification['view_url']           =   url('/').'/'.config('app.project.role_slug.school_admin_role_slug').'/survey';
                        $result = $this->NotificationModel->create($arr_notification);
                    }
                    $details          = [
                                                'first_name'  =>  'School Admin',
                                                'email'       =>  $this->school_admin_email,
                                                'mobile_no'   =>  $this->school_admin_contact,
                                                'student'     =>  ucwords($this->first_name.' '.$this->last_name)
                                        ];
                    if(array_key_exists('survey.sms',$this->permissions))
                    {
                        $arr_sms_data = $this->built_sms_data($details);
                        $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
                    }
                    if (array_key_exists('survey.email',$this->permissions))
                    {
                        $arr_mail_data = $this->built_mail_data($details);
                        $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
                    }

                    Flash::success( translation('survey_submitted_successfully'));
                }else{
                    Flash::error(translation('something_went_wrong_while_creating').' '.translation('survey'));
                }
                return redirect(url($this->module_url_path));   
            }
            Flash::error("you_can_not_submit_empty_survey");
        }

        return redirect()->back();
    }


    function view_reply($enc_id){

        $obj_data = $arr_data = array();
        if($enc_id){
            $id       =  base64_decode($enc_id);
            $user_id  =  $this->user_id;
            $obj_data = SurveyModel::whereHas('get_questions_answer',function($q)use($user_id){
                                                $q->where('from_user_id',$user_id);
                                            })
                                        ->with(['get_survey_images',
                                          'get_questions'=>function($q){ 
                                                $q->with(['get_question_type']);
                                                },
                                            'get_questions_answer'=>function($q)use($user_id){
                                                $q->where('from_user_id',$user_id);
                                            }])->where('id',$id)->first();
            if(!empty($obj_data)){
                $arr_data = $obj_data->toArray();
                
                $option_arr = $this->CommonDataService->get_question_category();

                $this->arr_view_data['page_title']      = translation('view').' '.$this->module_title;
                $this->arr_view_data['module_title']    = $this->module_title;
                $this->arr_view_data['module_url_path'] = $this->module_url_path;
                $this->arr_view_data['theme_color']     = $this->theme_color;
                $this->arr_view_data['module_icon']     = $this->module_icon;
                $this->arr_view_data['view_icon']       = 'fa fa-eye';
                $this->arr_view_data['option_arr']      = $option_arr;
                $this->arr_view_data['arr_data']        = $arr_data;
                $this->arr_view_data['enc_id']          = $enc_id;
                $this->arr_view_data['surveyUploadImagePath']     = public_path().config('app.project.img_path.survey_image');
                $this->arr_view_data['surveyUploadImageBasePath'] = url('/').config('app.project.img_path.survey_image');
          
                return view($this->module_view_folder.'.view_survey',$this->arr_view_data);
            }

        }
        return redirect()->back();
    }

    public function built_mail_data($arr_data)
     {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                  'USER_NAME'        => $arr_data['student'],
                                  'ROLE'             => 'Student'];

            $arr_mail_data                        = [];
            $arr_mail_data['email_template_slug'] = 'reply_survey';
            
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
                                  'USER_NAME'        => $arr_data['student'],
                                  'ROLE'             => 'Student'];
            

            $arr_sms_data                      = [];
            $arr_sms_data['sms_template_slug'] = 'reply_survey';
            
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }
}