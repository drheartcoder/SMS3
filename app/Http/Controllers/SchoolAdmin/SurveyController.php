<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;
use App\Models\SurveyModel;
use App\Models\SurveyImagesModel;
use App\Models\SurveyQuestionsModel;
use App\Models\UserTranslationModel;
use App\Models\QuestionCategoryModel;
use App\Models\SurveyQuestionsAnswerModel;
use App\Models\ProfessorModel;
use App\Models\ParentModel;
use App\Models\StudentModel;
use App\Models\EmployeeModel;
use App\Models\NotificationModel;
use App\Models\SchoolAdminModel;

 
/*Activity Log */
use App\Models\AcademicYearModel;   
/*Activity Log */
use App\Common\Services\LanguageService;
use App\Common\Services\CommonDataService;
use App\Common\Services\EmailService;

use DB;
use Flash;
use Session;
use Sentinel;
use Validator;
use Datatables;

class SurveyController extends Controller
{
    
    public function __construct(    
                                    UserModel $user,
                                    CommonDataService $CommonDataService,
                                    SurveyModel $survey,
                                    LanguageService $language,
                                    QuestionCategoryModel $question_category,
                                    SurveyQuestionsAnswerModel $survey_question_answer,
                                    UserTranslationModel $user_translation_model,
                                    EmailService $EmailService
                                )
    {
        $this->SurveyModel                  = $survey;
        $this->BaseModel                    = $this->SurveyModel;
        $this->LanguageService              = $language;  
        $this->CommonDataService            = $CommonDataService;
        $this->QuestionCategoryModel        = $question_category;
        $this->SurveyQuestionsAnswerModel   = $survey_question_answer;
        $this->UserTranslationModel         = $user_translation_model;
        $this->SchoolAdminModel             = new SchoolAdminModel();
        $this->NotificationModel            = new NotificationModel();
        $this->EmailService                 = $EmailService;
        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/survey';
        
        $this->module_title                 = translation("survey");
        $this->modyle_url_slug              = translation("survey");

        $this->module_view_folder           = "schooladmin.survey";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-bar-chart';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->first_name = $this->last_name = $this->ip_address ='';
        $this->role                         = Session::get('role');
        $this->permissions = $this->CommonDataService->current_user_access();

        $arr_current_user_access =[];
        $role = Session::get('role');
        $this->arr_current_user_access = $this->permissions ;

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
            $this->user_id           = $obj_data->id;
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
        /* Activity Section */

        /*Local Section*/
        if(Session::has('locale')){
            $this->locale = Session::get('locale');
        }else{
            $this->locale = 'en';
        }
        /*Local Section*/

        $this->stake_holder_for_survey = config('app.project.stake_holder_for_survey');

        $this->surveyUploadImagePath        = public_path().config('app.project.img_path.survey_image');
        $this->surveyUploadImageBasePath    = url('/').config('app.project.img_path.survey_image');
    }   

    /*
    | index() : List Survey
    | Auther  : Padmashri
    | Date    : 12-06-2018
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
    | Date    : 12-06-2018
    */
    public function get_details(Request $request){
 		
 		$survey                   = $this->BaseModel->getTable();
       	$academic_year_less_thn   = $this->CommonDataService->get_academic_year_less_than($this->academic_year);
        $arr_academic_year 		  = array();
        if($academic_year_less_thn)
        {
            $arr_academic_year    = explode(',',$academic_year_less_thn); 
        }   

      
        $obj_user = DB::table($survey)
                                ->select(DB::raw(
                                				 $survey.".id,".
                                				 $survey.".survey_title as  survey_title ,".
                                                 $survey.".survey_description as survey_description, ".
                                                 $survey.".user_role as role, ".
                                                 $survey.".start_date as start_date, ".
                                             	 $survey.".end_date as end_date "))
                                ->whereNull($survey.'.deleted_at')
                                ->whereIn($survey.'.academic_year_id',$arr_academic_year)
                                ->orderBy($survey.'.id','desc');



        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
            $obj_user = $obj_user->WhereRaw("( (".$survey.".survey_title LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$survey.".survey_description LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$survey.".user_role LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$survey.".start_date LIKE '%".$search_term."%') ") 
                                     ->orWhereRaw("(".$survey.".end_date LIKE '%".$search_term."%')) ");
                                     
        }
        /* ---------------- Filtering Logic ----------------------------------*/                    
        return $obj_user;
    }


    /*
    | get_records() : To get the List Survey
    | Auther  : Padmashri
    | Date    : 13-06-2018
    */
    public function get_records(Request $request){

       
        $obj_user        = $this->get_details($request);
        $current_context = $this;
        $arr_current_user_access = $this->CommonDataService->current_user_access();

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        $json_result     = $json_result->editColumn('build_action_btn',function($data) use ($current_context,$arr_current_user_access){
                                           
                                $build_delete_action =  $build_view_action = $build_edit_action =  ''; 
                                $date =date('Y-m-d');

                                $checIfReply = $this->CommonDataService->is_survey_replied(0,$data->id);
                               if(array_key_exists('survey.update', $arr_current_user_access))
                               {
                                    $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);


                                    $date =date('Y-m-d');
                                    if($data->start_date <= $date && $date <= $data->end_date){
                                    $build_edit_action = '<a style="position: relative;" class="orange-color" href="javascript:void(0)" title="'.translation('edit').'" 

                                    onclick="return showAlert(\''.translation('you_can_not_edit_on_going_survey').'\',\'warning\')"><i class="fa fa-edit"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';

                                    }else{

                                    $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';
                                        }

                               }
                                    
                               if(array_key_exists('survey.delete', $arr_current_user_access))
                               { 
                                    $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);

                                    if($data->start_date <= $date && $date <= $data->end_date){
                                        $build_delete_action = '<a style="position: relative;" class="red-color" title="'.translation('access_denied').'" href="javascript:void(0)" ><i class="fa fa-trash"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                    }else{

                                        $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                    }
                                }

                                $build_reply_action = '';
                                if($checIfReply > 0 ){
                                    $view_reply_href =  $this->module_url_path.'/view_response/'.base64_encode($data->id);
                                    $build_reply_action  = '<a class="green-color" href="'.$view_reply_href.'" title="'.translation('view_response').'"><i class="fa fa-reply"></i></a>';
                                }
                                else{
                                    $build_reply_action  = '<a style="position: relative;" title="'.translation('access_denied').'" class="green-color" href="javascript:void(0)" ><i class="fa fa-reply"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                }

                                $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                $build_view_action  = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye"></i></a>';
                                return $build_view_action.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_delete_action.'&nbsp;'.$build_reply_action;
 
                                     
                                });
         
        $json_result =      $json_result->editColumn('build_checkbox',function($data){
                                
                                $date =date('Y-m-d');
                                if($data->start_date <= $date && $date <= $data->end_date){
                                    $build_checkbox = '-';
                                }else{

                                $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                }

                                return $build_checkbox;
                                })
                                ->editColumn('role',function($data){
                                    $strUserRole  = '';
                                    if($data->role!='' && !empty($data->role)){

                                    $strUserRole = '<span class="subjects-teaching-hrs">'.ucfirst(implode(',',json_decode($data->role))).'</span>';
                                    }      
                                    return $strUserRole;
                                })
                                 ->editColumn('start_date',function($data){
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
    | create() : Create Survey
    | Auther  : Padmashri
    | Date    : 13-06-2018
    */
    public function create()
    {   
         
        
        $option_arr = $this->get_question_category();
        $this->arr_view_data['page_title']      = translation('add')." ".$this->module_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['create_icon'] = $this->create_icon;
        $this->arr_view_data['role_user']   = $this->stake_holder_for_survey;
        $this->arr_view_data['option_arr']  = $option_arr;
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    /*
    | store() : Store  Survey
    | Auther  : Padmashri
    | Date    : 13-06-2018
    */
    public function store(Request $request)
    {
        
        $arr_rules = [];
        $arr_rules['user_role']          = 'required';
        $arr_rules['survey_title']       = 'required';
        $arr_rules['survey_description'] = 'required';
        $arr_rules['start_date']         = 'required|date|before:end_date';
        $arr_rules['end_date']           = 'required|date';
        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'),
                            'alpha'                => translation('please_enter_letters_only')  
                        );
        $validator = Validator::make($request->all(),$arr_rules,$messages);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $user_role            = $request->input('user_role');
        $survey_title         = trim($request->input('survey_title'));
        $survey_description   = trim($request->input('survey_description'));
        $start_date           = trim($request->input('start_date'));
        $end_date             = trim($request->input('end_date'));
        $survey_question      = $request->input('survey_question');
        $question_category_id = $request->input('question_category_id');
        $option               = $request->input('option');
        
        $arr_data = [];     
        $arr_data['school_id']          = $this->school_id;
        $arr_data['academic_year_id']   = $this->academic_year;
        $arr_data['survey_title']       =  $survey_title;
        $arr_data['survey_description'] = $survey_description;
        $arr_data['start_date']         = isset($start_date) ? date('Y-m-d',strtotime($start_date)):'0000-00-00';
        $arr_data['end_date']           = isset($end_date) ? date('Y-m-d',strtotime($end_date)):'0000-00-00';
        $arr_data['user_role']          = json_encode($user_role);
        
        $obj_exist = $this->SurveyModel->where('survey_title','=',$survey_title)->where('school_id','=',$this->school_id)->where('academic_year_id','=',$this->academic_year)
            ->where('start_date','=',$start_date)->where('end_date','=',$end_date)->first();

        if(isset($obj_exist->id))
        {
            Flash::error($this->module_title." ".translation("already_exists"));
            return redirect()->back();            
        }                
        
        $res = $this->SurveyModel->create($arr_data);
        if($res){
            /* ADD IMAGES */
            $imagesArray     =  $request->file('survey_images') ;

            if(!empty($request->file('survey_images')) && count($request->file('survey_images')) > 0){
                for($i=0;$i<count($request->file('survey_images'));$i++){
                     if($request->hasFile('survey_images.'.$i)){
                          $image_validation = Validator::make(array('file'=>$request->file('survey_images.'.$i)),
                                                                    array('file'=>'mimes:jpg,jpeg,png'));
                    
                            if($request->file('survey_images.'.$i)->isValid() && $image_validation->passes())
                            {

                                $survey_images = array();
                                $file_name       = '';
                                $excel_file_name = $request->file('survey_images.'.$i);
                              
                                $fileExtension   = strtolower($request->file('survey_images.'.$i)->getClientOriginalExtension()); 
                                $file_name       = sha1(uniqid().$excel_file_name.uniqid()).'.'.$fileExtension;
                                $request->file('survey_images.'.$i)->move($this->surveyUploadImagePath,$file_name); 
                                
                                $survey_images['survey_id']       = $res->id;
                                $survey_images['survey_image']    = $file_name;
                                
                                $resImage  = SurveyImagesModel::create($survey_images);
                            }
                      }
                }
            }
            /* ADD IMAGES */
            /* Add the questions */
            for($i=0;$i<count($survey_question);$i++){

                if(!empty($survey_question[$i]) && !empty($question_category_id[$i])   ){ /*!empty($option[$i])*/
                    $arr_questions = [];
                    $surveyOption = '';
                    if($question_category_id[$i]==1 || $question_category_id[$i] == 5){
                        $surveyOption = '';
                    }else if(!empty($option[$i]) && count($option[$i]) > 0){
                            $temp = [];
                            $newTemp = [];
                            for($j=0;$j<count($option[$i]);$j++){
                                if(!empty($option[$i]))
                                {
                                    $temp[] = $option[$i][$j];
                                }
                                
                            }
                            $surveyOption = json_encode($temp,true);
                    }

                    $arr_questions['survey_id']          = $res->id;
                    $arr_questions['question_category_id']   = $question_category_id[$i];
                    $arr_questions['survey_question']       =  trim($survey_question[$i]);
                    $arr_questions['question_options'] = $surveyOption;
                    $res2 = SurveyQuestionsModel::create($arr_questions);

                }

            }
            /* Add the questions */

            /* send notification*/
            $obj_users = '';
            $arr_academic_year = [];
            $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 
            if($academic_year)
            {
                $arr_academic_year = explode(',',$academic_year);    
            }
            foreach ($user_role as $key => $value) 
            {
                $obj_data = [];
                $obj_data = $this->CommonDataService->get_permissions($value,$this->academic_year,$this->school_id);
                if(isset($obj_data) && count($obj_data)>0)
                {
                    foreach ($obj_data as $key => $data1) 
                    {
                        
                        if(isset($data1['notifications']['notification_permission']) && $data1['notifications']['notification_permission']!=null)
                        {
                            $permissions = [];
                            $permissions = json_decode($data1['notifications']['notification_permission'],true);

                            if($value == config('app.project.role_slug.parent_role_slug'))
                            {
                                $user_id         =  $data1['parent_id'];
                            }
                            else
                            {
                                $user_id         =  $data1['user_id'];    
                            }  
                            $result = $this->send_notifications($permissions,$data1,$user_id,$value,$res);
                        }
                    }
                }


            }
            
            /* send notification*/
            Flash::success($this->module_title." ".translation("created_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_creating ".$this->module_title);
            return redirect()->back();
        }        

    }

    /*
    | view()  : View Survey
    | Auther  : Padmashri
    | Date    : 13-06-2018
    */
    function view($enc_id){
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
                $option_arr = $this->get_question_category();

                $this->arr_view_data['page_title']      = translation('view')." ".$this->module_title;
                $this->arr_view_data['module_title']    = $this->module_title;
                $this->arr_view_data['module_url_path'] = $this->module_url_path;
                $this->arr_view_data['theme_color']     = $this->theme_color;
                $this->arr_view_data['module_icon']     = $this->module_icon;
                $this->arr_view_data['view_icon']       = 'fa fa-eye';
                $this->arr_view_data['role_user']       = $this->stake_holder_for_survey;
                $this->arr_view_data['option_arr']      = $option_arr;
                $this->arr_view_data['arr_data']        = $arr_data;
                $this->arr_view_data['surveyUploadImagePath']     = public_path().config('app.project.img_path.survey_image');
                $this->arr_view_data['surveyUploadImageBasePath'] = url('/').config('app.project.img_path.survey_image');
          
                return view($this->module_view_folder.'.view',$this->arr_view_data);
            }

        }
        return redirect()->back();
    }


     /*
    | edit()  : Edit  Survey
    | Auther  : Padmashri
    | Date    : 14-05-2018
    */
    public function edit($enc_id=FALSE)
    {
        $id = base64_decode($enc_id);
        if(is_numeric($id)){
            $obj_data = $arr_data =  $arr_selected_user = [];
            $obj_data = SurveyModel::with(['get_survey_images','get_questions'=>function($q){ 
                                                        $q->with(['get_question_type']);} 
                                              ])->where('id',$id)->first();
            if($obj_data){
               $arr_survey_data = $obj_data->toArray();
            }
            /*role user array */
            if(!empty($arr_survey_data['user_role'])){
                $arr_selected_user = json_decode($arr_survey_data['user_role'],true);  
            }
            /*role user array */
            $option_arr = $this->get_question_category();

            $addedQuestionCount = isset($arr_survey_data['get_questions'])&&!empty($arr_survey_data['get_questions'])?count($arr_survey_data['get_questions']):0;
            $totalAddNew = 10;
            $totalAddNew = 10 - $addedQuestionCount;

            $this->arr_view_data['role_user']         = $this->stake_holder_for_survey;
            $this->arr_view_data['arr_selected_user'] = $arr_selected_user;
            $this->arr_view_data['option_arr']      = $option_arr;
            $this->arr_view_data['page_title']      = translation('edit')." ".$this->module_title;
            $this->arr_view_data['module_title']    = str_plural($this->module_title);
            $this->arr_view_data['edit_mode']       = TRUE;
            $this->arr_view_data['module_url_path'] = $this->module_url_path;
            $this->arr_view_data['enc_id']          = $enc_id;
            $this->arr_view_data['theme_color']     = $this->theme_color;
            $this->arr_view_data['module_icon']     = $this->module_icon;
            $this->arr_view_data['edit_icon']       = $this->edit_icon;
            $this->arr_view_data['totalAddNew']     = $totalAddNew-1; /*as our js count is starting from zero*/
            $this->arr_view_data['arr_survey_data']        = $arr_survey_data;
             $this->arr_view_data['surveyUploadImagePath']            = public_path().config('app.project.img_path.survey_image');
                    $this->arr_view_data['surveyUploadImageBasePath'] = url('/').config('app.project.img_path.survey_image');
            
            return view($this->module_view_folder.'.edit',$this->arr_view_data);
        }else{
             Flash::error(translation('something_went_wrong'));
             return redirect($this->module_url_path);
        }
    }

    /*
    | edit()  : Update  Survey
    | Auther  : Padmashri
    | Date    : 14-05-2018
    */
    public function update(Request $request,$enc_id=FALSE)
    {
        $id = base64_decode($enc_id);     

        $arr_rules = [];
        $arr_rules['user_role']          = 'required';
        $arr_rules['survey_title']       = 'required';
        $arr_rules['survey_description'] = 'required';
        $arr_rules['start_date']         = 'required|date|before:end_date';
        $arr_rules['end_date']           = 'required|date';
        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'),
                            'alpha'                => translation('please_enter_letters_only')  
                        );

        $validator = Validator::make($request->all(),$arr_rules,$messages);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $user_role            = $request->input('user_role');
        $survey_title         = trim($request->input('survey_title'));
        $survey_description   = trim($request->input('survey_description'));
        $start_date           = trim($request->input('start_date'));
        $end_date             = trim($request->input('end_date'));
        
        
        $arr_data = [];     
        $arr_data['survey_title']       =  $survey_title;
        $arr_data['survey_description'] = $survey_description;
        $arr_data['start_date']         = isset($start_date) ? date('Y-m-d',strtotime($start_date)):'0000-00-00';
        $arr_data['end_date']           = isset($end_date) ? date('Y-m-d',strtotime($end_date)):'0000-00-00';
        $arr_data['user_role']          = json_encode($user_role);
        
        $obj_exist = $this->SurveyModel->where('survey_title','=',$survey_title)->where('school_id','=',$this->school_id)->where('academic_year_id','=',$this->academic_year)
            ->where('start_date','=',$start_date)->where('end_date','=',$end_date)->where('id','<>',$id)->first();
        if(isset($obj_exist->id))
        {
            Flash::error($this->module_title." ".translation("already_exists"));
            return redirect()->back();            
        }  

        $imagesArray     =  $request->file('survey_images') ;
        if(!empty($request->file('survey_images')) && count($request->file('survey_images')) > 0){
            for($i=0;$i<count($request->file('survey_images'));$i++){
                 if($request->hasFile('survey_images.'.$i)){
                      $image_validation = Validator::make(array('file'=>$request->file('survey_images.'.$i)),
                                                                array('file'=>'mimes:jpg,jpeg,png'));
                
                        if($request->file('survey_images.'.$i)->isValid() && $image_validation->passes())
                        {

                            $survey_images = array();
                            $file_name       = '';
                            $excel_file_name = $request->file('survey_images.'.$i);
                          
                            $fileExtension   = strtolower($request->file('survey_images.'.$i)->getClientOriginalExtension()); 
                            $file_name       = sha1(uniqid().$excel_file_name.uniqid()).'.'.$fileExtension;
                            $request->file('survey_images.'.$i)->move($this->surveyUploadImagePath,$file_name); 
                            
                            $survey_images['survey_id']       = $id;
                            $survey_images['survey_image']    = $file_name;
                            
                            $resImage  = SurveyImagesModel::create($survey_images);
                        }
                  }
            }

        }
        
        $res = $this->SurveyModel->where('id',$id)->update($arr_data);
        if($res || $resImage){
            Flash::success($this->module_title." ".translation("updated_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_updating ".$this->module_title);
            return redirect()->back();
        }        
       
    }

    /*
    | edit()  : Update  Survey Question
    | Auther  : Padmashri
    | Date    : 14-05-2018
    */
    public function update_question(Request $request,$enc_id,$enc_survey_id)
    {
        $id = base64_decode($enc_id);     
        $survey_id = base64_decode($enc_survey_id);     

        $arr_rules = [];
        $arr_rules['survey_question']        = 'required';
        $arr_rules['question_category_id']   = 'required';
        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

       
        $survey_question        = trim($request->input('survey_question'));
        $question_category_id   = trim($request->input('question_category_id'));
        $survey_option          = $request->input('option');
        
        $temp = [];
        $newTemp = [];
        for($i=0;$i<count($survey_option);$i++){
            if(!empty($survey_option[$i]) && !empty($question_category_id)   ){ /*!empty($option[$i])*/
                    $arr_questions = [];
                    $surveyOption = '';
                    if($question_category_id==1 || $question_category_id == 5){
                        $surveyOption = '';
                    }else if(!empty($survey_option[$i]) ){
                        if(!empty($survey_option[$i])){
                            $temp[] = $survey_option[$i];
                        }
                     }
             }
        }
    $res2 = $surveyOption = '';
    if(!empty($temp)){

        $surveyOption = json_encode($temp,true);
        $arr_questions['question_options'] = $surveyOption;
        $res2 = SurveyQuestionsModel::where('survey_id',$survey_id)->where('id',$id)->update($arr_questions);
    }


        $arr_questions = [];
        $arr_questions['question_category_id']   = trim($question_category_id);
        $arr_questions['survey_question']        = trim($survey_question);

        $res = SurveyQuestionsModel::where('survey_id',$survey_id)->where('id',$id)->update($arr_questions);

        if($res || $res2){
            Flash::success($this->module_title." ".translation("updated_successfully"));
            return redirect(url($this->module_url_path).'/edit/'.$enc_survey_id);
        }else{
            Flash::error("something_went_wrong_while_updating ".$this->module_title);
            return redirect(url($this->module_url_path).'/edit/'.$enc_survey_id);
        }        
       
    }

    
    /*
    | store_questions_in_survey()  : Add new questions in edit survey section 
    | Auther  : Padmashri
    | Date    : 14-05-2018
    */
    function store_questions_in_survey(Request $request,$enc_survey_id){
        $survey_id = base64_decode($enc_survey_id);
        if($survey_id){
            $survey_question = $request->input('survey_question');
            $question_category_id = $request->input('question_category_id');
            $option               = $request->input('option');
               
            for($i=0;$i<count($survey_question);$i++){

                if(!empty($survey_question[$i]) && !empty($question_category_id[$i])   ){ /*!empty($option[$i])*/
                    $arr_questions = [];
                    $surveyOption = '';
                    if($question_category_id[$i]==1 || $question_category_id[$i] == 5){
                        $surveyOption = '';
                    }else if(!empty($option[$i]) && count($option[$i]) > 0){
                            $temp = [];
                            $newTemp = [];
                            for($j=0;$j<count($option[$i]);$j++){
                                if(!empty($option[$i]))
                                {
                                    $temp[] = $option[$i][$j];
                                }
                                
                            }
                            $surveyOption = json_encode($temp,true);
                    }

                    $arr_questions['survey_id']              = $survey_id;
                    $arr_questions['question_category_id']   = $question_category_id[$i];
                    $arr_questions['survey_question']        = trim($survey_question[$i]);
                    $arr_questions['question_options']       = $surveyOption;
                    
                    $res2 = SurveyQuestionsModel::create($arr_questions);

                }

            }
            if($res2){
                Flash::success($this->module_title." ".translation('survey_questions'));
                return redirect(url($this->module_url_path).'/edit/'.$enc_survey_id);
            }else{
                 Flash::error("something_went_wrong_while_updating ".translation('survey_questions'));
                 return redirect(url($this->module_url_path).'/edit/'.$enc_survey_id);
            }
        }
        Flash::error("something_went_wrong_while_updating ".translation('survey_questions'));
                 
        return redirect()->back();
    }

    
    /*
    | get_question_category()  : Get the question category data
    | Auther  : Padmashri
    | Date    : 15-05-2018
    */
    function get_question_category(){
       /*$obj_data = $arr_data = array();
        $obj_data = QuestionCategoryModel::where('id','<',5)->whereNull('deleted_at')->get();
        if($obj_data){
            $arr_data = $obj_data->toArray();
        }
        return $arr_data;*/
        return $this->CommonDataService->get_question_category();
    }

    /*
    | delete()  : delete() delete the survey 
    | Auther  : Padmashri
    | Date    : 15-05-2018
    */
    function delete($enc_id){
         $id = base64_decode($enc_id);
         $res = $this->delete_survey($id);
        if($res){
            Flash::success($this->module_title." ".translation("deleted_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_deleting".$this->module_title);
            return redirect()->back();
        }        
       
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
               $this->delete_survey(base64_decode($record_id));    
               Flash::success($this->module_title.' '.translation('deleted_succesfully')); 
            } 
            elseif($multi_action=="activate")
            {
               $this->perform_activate(base64_decode($record_id)); 
               Flash::success($this->module_title.' '.translation('activated_successfully')); 
            }
            elseif($multi_action=="deactivate")
            {
               $this->perform_deactivate(base64_decode($record_id));    
               Flash::success($this->module_title.' '.translation('deactivated_successfully'));  
            }
            elseif($multi_action=="promote")
            {
                $this->promote_students(base64_decode($record_id));
                Flash::success($this->module_title.' '.translation('promoted_successfully'));  
            }
        }
        return redirect()->back();
    }


    /*
    | delete()  : delete() delete the survey
    | Auther  : Padmashri
    | Date    : 15-05-2018
    */
    function delete_survey($id){
        $flag = 0;
        $getData = SurveyModel::with(['get_survey_images','get_questions'])->where('id',$id)->first();
        if(!empty($getData)){
            $arr_data = $getData->toArray();
            
            if(!empty($arr_data['get_questions'])){
                $delQues = SurveyQuestionsModel::where('survey_id',$id)->delete();
            }

            if(!empty($arr_data['get_survey_images'])){
                foreach ($arr_data['get_survey_images'] as $key => $value) {
                    if($value['survey_image']!='' && !empty($value['survey_image']))   
                    {     
                            $unlink_path    = $this->surveyUploadImagePath.'/'.$value['survey_image'];
                            @unlink($unlink_path);
                    }

                $delImg = SurveyImagesModel::where('survey_id',$id)->where('id',$value['id'])->delete();
                    
                }
            }

            $flag =    SurveyModel::where('id',$id)->delete();
        }
        return $flag;

    }

     /*
    | delete_survey_image()  : delete_survey_image() delete the survey images
    | Auther  : Padmashri
    | Date    : 15-05-2018
    */
    public function delete_survey_image(Request $request){
        $flag = 'error';
        $imageId = $request->input('surveyId');
        $value = SurveyImagesModel::where('id',$imageId)->first();
        if(!empty($value)){

            if($value['survey_image']!='' && !empty($value['survey_image']))   
            {     
                    $unlink_path    = $this->surveyUploadImagePath.'/'.$value['survey_image'];
                    @unlink($unlink_path);
            }

            $delImg = SurveyImagesModel::where('id',$imageId)->delete();
            if($delImg){
                $flag = 'done';
            }
            
            
        }
        return $flag;
    }

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
                $this->arr_view_data['view_icon']       = 'fa fa-bar-chart';
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

    function store_survey_reply(Request $request,$enc_id){
       
        $obj_questions = $arr_question = array();
        $survey_id     = base64_decode($enc_id);
        $res           = '';
        if($survey_id){

            $answer = $request->input('answer');
            if(!empty($answer))
            {
                $obj_questions = SurveyQuestionsModel::where('survey_id',$survey_id)->get();
                if($obj_questions){
                    $arr_question = $obj_questions->toArray();
                }
                foreach ($arr_question as $key => $value) 
                {
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
                        $arr_notification['user_type']          =   'employee';
                        $arr_notification['notification_type']  =   'Survey Response';
                        $arr_notification['title']              =   'Survey Response: '.ucwords($this->first_name.' '.$this->last_name).' '.'employee'.' responded on survey';
                        $arr_notification['view_url']           =   url('/').'/'.config('app.project.role_slug.school_admin_role_slug').'/survey';
                        $result = $this->NotificationModel->create($arr_notification);
                    }

                    $survey = $this->SurveyModel->where('id',$survey_id)->first();

                    $details          = [
                                                'first_name'  =>  'School Admin',
                                                'email'       =>  $this->school_admin_email,
                                                'mobile_no'   =>  $this->school_admin_contact,
                                                'employee'   =>  ucwords($this->first_name.' '.$this->last_name),
                                                'survey_title'       =>  isset($survey->survey_title)?$survey->survey_title:''
                                        ];
                    if(array_key_exists('survey.sms',$this->permissions))
                    {
                        $arr_sms_data = $this->built_sms_data_for_reply($details);
                        $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
                    }
                    if (array_key_exists('survey.email',$this->permissions))
                    {
                        $arr_mail_data = $this->built_email_data_for_reply($details);
                        $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
                    }
                    Flash::success( translation('survey_submitted_successfully'));
                }
                else
                {
                    Flash::error(translation('something_went_wrong_while_creating').' '.translation('survey'));
                }
                return redirect(url($this->module_url_path));   
            }
            Flash::error("you_can_not_submit_empty_survey");
        }

        return redirect()->back();
    }

     /*
    | delete_question()  : delete_question() delete the survey  question from perticular survey
    | Auther  : Padmashri
    | Date    : 15-05-2018
    */
    public function delete_question(Request $request,$enc_id,$enc_survey_id){

        $id        = base64_decode($enc_id);     
        $survey_id = base64_decode($enc_survey_id);     
        $res       = '';
        $isExists  = SurveyQuestionsModel::where('survey_id',$survey_id)->where('id',$id)->first();
        if($isExists){
                $res = SurveyQuestionsModel::where('survey_id',$survey_id)->where('id',$id)->delete();
        }
        
        if($res){

            Flash::success(translation('survey_questions').' '.translation('deleted_succesfully')); 
        }else{

            
            Flash::error(translation('something_went_wrong_while_deleting').' '.translation('survey_questions')); 
        }

        return redirect(url($this->module_url_path).'/edit/'.$enc_survey_id);
    }


    /*
    | view_response()  : View Response Survey
    | Auther  : Padmashri
    | Date    : 13-06-2018
    */
    function view_response($enc_id){

        $obj_data = $arr_data = array();
        if($enc_id){
            $id =  base64_decode($enc_id);

            $obj_data = SurveyModel::where('id',$id)->first();

            if(!empty($obj_data)){
                $arr_data = $obj_data->toArray();
                $option_arr = $this->get_question_category();
                $flag=0;
                $role = 'school_admin';
                if(\Session::get('role')!='school_admin')
                {
                    $role = 'employee';
                }
                $roles = json_decode($obj_data->user_role);
                
                foreach ($roles as $key => $value) {
                    if($value==$role){
                        $count = $this->SurveyQuestionsAnswerModel->where('from_user_id',$this->user_id)->where('survey_id',$id)->count();
                        $flag = 1;
                        if($count>0){
                            $flag = 0;
                        }        
                    }
                }
            
                
                $this->arr_view_data['page_title']      = translation('view_survey_response');
                $this->arr_view_data['survey_title']    = isset($arr_data['survey_title'])&&$arr_data['survey_title']!=''?$arr_data['survey_title']:'';

                $this->arr_view_data['start_date']    = isset($arr_data['start_date'])&&$arr_data['start_date']!=''?getDateFormat($arr_data['start_date']):'';
                $this->arr_view_data['end_date']    = isset($arr_data['end_date'])&&$arr_data['end_date']!=''?getDateFormat($arr_data['end_date']):'';

                $this->arr_view_data['module_title']    = $this->module_title; 
                $this->arr_view_data['module_url_path'] = $this->module_url_path;
                $this->arr_view_data['theme_color']     = $this->theme_color;
                $this->arr_view_data['module_icon']     = $this->module_icon;
                $this->arr_view_data['view_icon']       = 'fa fa-eye';
                $this->arr_view_data['arr_data']        = $arr_data;
                $this->arr_view_data['enc_id']          = $enc_id;
                $this->arr_view_data['give_reply']      = $flag==0?0:1;

                return view($this->module_view_folder.'.response',$this->arr_view_data);
            }

        }
        return redirect()->back();
    }

    /*
    | get_response_records() : To get response data
    | Auther  : Padmashri
    | Date    : 13-06-2018
    */
    public function get_response_records(Request $request,$enc_id){

        $survey_id= base64_decode($enc_id);
        
        $answer   = $this->SurveyQuestionsAnswerModel->getTable();
        $user     = $this->UserTranslationModel->getTable();
        $obj_user = DB::table($answer)
                                ->select(DB::raw(
                                                 $answer.".id as id,".
                                                 $answer.".survey_id as survey_id,".
                                                 $answer.".from_user_id as  from_user_id ,".
                                                 $answer.".user_role as user_role, ".
                                                 $answer.".created_at as created_at,".
                                                 "CONCAT(".$user.".first_name,' ',"
                                                          .$user.".last_name) as user_name"))
                                ->join($user,$user.".user_id",'=',$answer.".from_user_id")
                                ->where($user.".locale",'=',$this->locale)
                                ->whereNull($answer.'.deleted_at')
                                ->where($answer.'.survey_id',$survey_id)
                                ->groupBy($answer.'.from_user_id')
                                ->orderBy($answer.'.id','desc');

        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
            $obj_user = $obj_user->WhereRaw("(( CONCAT(".$user.".first_name,' ',".$user.".last_name)   LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$answer.".user_role LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$answer.".created_at LIKE '%".$search_term."%')) ");
                                     
        }
        /* ---------------- Filtering Logic ----------------------------------*/                    
        $current_context = $this;
        $arr_current_user_access = $this->arr_current_user_access;

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        $json_result     = $json_result->editColumn('build_action_btn',function($data) use ($current_context,$arr_current_user_access){
                                           
                                $build_view_action = ''; 
                                
                                $view_href =  $this->module_url_path.'/view_response_details/'.base64_encode($data->survey_id).'/'.base64_encode($data->from_user_id);
                                $build_view_action  = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye"></i></a>';
                                return $build_view_action;
 
                                     
                                });
         
        $json_result =      $json_result->editColumn('role',function($data){
                                    $strUserRole  = '';
                                    if($data->user_role!='' && !empty($data->user_role)){
                                        $strUserRole = '<span class="subjects-teaching-hrs">'.ucfirst($data->user_role).'</span>';
                                    }      
                                    return $strUserRole;
                                })
                                 ->editColumn('created_at',function($data){
                                    if(isset($data->created_at) && $data->created_at!='0000-00-00 00:00:00'){
                                        return getDateFormat($data->created_at);
                                    }else{
                                        return '-';
                                    }
                                })
                                ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    public function view_response_details(Request $request,$enc_survey_id,$enc_user_id){
        if($enc_survey_id!='' && $enc_user_id!=''){
            $survey_id = base64_decode($enc_survey_id);
            $user_id   = base64_decode($enc_user_id);
            $obj_data  = SurveyModel::whereHas('get_questions_answer',function($q)use($user_id){
                                                $q->where('from_user_id',$user_id);
                                            })
                                        ->with(['get_survey_images',
                                          'get_questions'=>function($q){ 
                                                $q->with(['get_question_type']);
                                                },
                                            'get_questions_answer'=>function($q)use($user_id){
                                                $q->where('from_user_id',$user_id);
                                            }])->where('id',$survey_id)->first();
            if(!empty($obj_data)){
                $arr_data = $obj_data->toArray();
                
                $option_arr = $this->CommonDataService->get_question_category();

                $this->arr_view_data['page_title']      = $this->module_title;
                $this->arr_view_data['module_title']    = translation('view')." ".$this->module_title;
                $this->arr_view_data['module_url_path'] = $this->module_url_path;
                $this->arr_view_data['theme_color']     = $this->theme_color;
                $this->arr_view_data['module_icon']     = $this->module_icon;
                $this->arr_view_data['view_icon']       = 'fa fa-bar-chart';
                $this->arr_view_data['option_arr']      = $option_arr;
                $this->arr_view_data['arr_data']        = $arr_data;
                $this->arr_view_data['enc_survey_id']   = $enc_survey_id;
                
                $this->arr_view_data['surveyUploadImagePath']     = public_path().config('app.project.img_path.survey_image');
                $this->arr_view_data['surveyUploadImageBasePath'] = url('/').config('app.project.img_path.survey_image');
          
                return view($this->module_view_folder.'.view_survey_detail',$this->arr_view_data);
            }

        }
        return redirect()->back();
    }

    public function send_notifications($permissions,$users,$user_id,$role,$survey)
    {
        $result='';
        if(array_key_exists('survey.app',$permissions))
        {
         
            $arr_notification = [];
            $arr_notification['school_id']          =  $this->school_id;
            $arr_notification['from_user_id']       =  $this->user_id;
            if($role == config('app.project.role_slug.parent_role_slug'))
            {
                $arr_notification['to_user_id']         =  $user_id;
            }
            else
            {
                $arr_notification['to_user_id']         =  $user_id;    
            }   
            
            $arr_notification['user_type']          =  config('app.project.role_slug.school_admin_role_slug');
            $arr_notification['notification_type']  =  'Survey Created';
            $arr_notification['title']              =  'New Survey Created: New survey is created We need your feedback on '.(isset($survey->survey_title)?ucwords($survey->survey_title):'').' survey.';
            $arr_notification['view_url']           =  url('/').'/'.$role.'/survey';
            $result = NotificationModel::create($arr_notification);
        }
        $details          = [
                                    'first_name'  =>  isset($users['get_user_details']['first_name'])?ucwords($users['get_user_details']['first_name']):'',
                                    'email'       =>  isset($users['get_user_details']['email'])?$users['get_user_details']['email']:'',
                                    'mobile_no'   =>  isset($users['get_user_details']['mobile_no'])?$users['get_user_details']['mobile_no']:'',
                                    'survey_title'=>  isset($survey->survey_title)?ucwords($survey->survey_title):''
                            ];
        if(array_key_exists('survey.sms',$permissions))
        {
            $arr_sms_data = $this->built_sms_data($details);
            $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
        }
        if(array_key_exists('survey.email',$permissions))
        {
            $arr_mail_data = $this->built_mail_data($details);
            $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
        }
        return $result;
    }

    public function built_mail_data($arr_data)
     {
        

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [];

            $arr_built_content = [
                                  'FIRST_NAME'         => $arr_data['first_name'],
                                  'SURVEY_TITLE'       => $arr_data['survey_title'],
                                  'SCHOOL_ADMIN'       => $this->CommonDataService->get_school_name($this->school_id)
                                 ];    
            
            $arr_mail_data                        = [];
            $arr_mail_data['email_template_slug'] = 'add_survey';                   
        
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

            $arr_built_content = [];

            $arr_built_content = [
                                      'SURVEY_TITLE'          => $arr_data['survey_title']
                                 ];    
           
            $arr_sms_data                      = [];
            $arr_sms_data['sms_template_slug'] = 'add_survey';                   
                
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }

    public function export(Request $request){

        $id = base64_decode($request->survey_id);
        if(!is_numeric($id)){
            Flash::error(translation("no_records_found_to_export"));
            return redirect()->back();
        }

        $obj_data = $this->SurveyModel
                                    ->whereHas('get_questions_answer',function(){})
                                    ->with(['get_questions_answer'=>function($q){
                                        $q->orderBy('survey_question_id','ASC');
                                    },'get_questions_answer.get_survey_question','get_questions_answer.get_form_user'])
                                    ->where('id',$id)
                                    ->where('school_id',$this->school_id)
                                    ->first();

        if(sizeof($obj_data)<=0){
            Flash::error(translation("no_records_found_to_export"));
            return redirect()->back();
        }
        if(sizeof($obj_data)>500 && $request->file_format == 'csv'){
            Flash::error(translation("too_many_records_to_export"));
            return redirect()->back();
        }
        if($request->file_format == 'csv'){
            \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel) use($obj_data) 
                {
                    $excel->sheet(ucwords($this->module_title), function($sheet) use($obj_data) 
                    {  
                        $arr_data = $obj_data->toArray();
                        
                        $survey_title = isset($arr_data['survey_title']) ? $arr_data['survey_title']:'';

                        $arr_fields['name']   = translation('name');
                        $arr_fields['answer'] = translation('answer');

                        $sheet->row(2, [ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                        $sheet->row(3, [$survey_title]);
                        
                        $sheet->setColumnFormat(array(
                            'B' => '@'
                        ));
                        $question_arr =[];
                        if(sizeof($arr_data['get_questions_answer'])>0) 
                        {   
                            $arr_tmp = [];
                            foreach($arr_data['get_questions_answer'] as $key => $result)
                            {
                                if(in_array($result['survey_question_id'],$question_arr)){
                                    
                                }
                                else{
                                    array_push($question_arr,$result['survey_question_id']);
                                    $tmp=[];
                                    $tmp[]=$result['get_survey_question']['survey_question'];
                                    array_push($arr_tmp,$tmp);
                                    array_push($arr_tmp,$arr_fields);
                                }
                                if(count($arr_data['get_questions_answer'])==1){
                                    if(!in_array($result['survey_question_id'],$question_arr)){
                                        $tmp=[];
                                        $tmp[]=$result['get_survey_question']['survey_question'];
                                        array_push($arr_tmp,$tmp);
                                        array_push($arr_tmp,$arr_fields);    
                                    }
                                }
                                if($key==count($arr_data['get_questions_answer'])-1  && $key!=0 &&
                                   $arr_data['get_questions_answer'][$key]['survey_question_id']== $arr_data['get_questions_answer'][$key-1]['survey_question_id']){
                                    if(!in_array($result['survey_question_id'],$question_arr)){    
                                        $tmp=[];
                                        $tmp[]=$result['get_survey_question']['survey_question'];
                                        array_push($arr_tmp,$tmp);
                                        array_push($arr_tmp,$arr_fields);
                                    }    
                                }

                                $first_name  = isset($result['get_form_user']['first_name']) ? $result['get_form_user']['first_name'] :'';
                                $last_name  = isset($result['get_form_user']['last_name']) ? $result['get_form_user']['last_name'] :'';
                                $name = $first_name." ".$last_name;
                                $tmp=[];
                                $tmp['name']           = $name;
                                $tmp['answer']         = isset($result['answer']) ? $result['answer']:'';
                                array_push($arr_tmp,$tmp);
                                
                            }
                            
                            $sheet->rows($arr_tmp);
                        }
                    });
                })
                ->export('csv');     
        }
    }
     public function built_email_data_for_reply($arr_data)
     {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                  'USER_NAME'        => $arr_data['employee'],
                                  'ROLE'             => 'Employee'];

            $arr_mail_data                        = [];
            $arr_mail_data['email_template_slug'] = 'reply_survey';
            
            $arr_mail_data['arr_built_content']   = $arr_built_content;
            $arr_mail_data['user']                = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_sms_data_for_reply($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'USER_NAME'        => $arr_data['employee'],
                                  'ROLE'             => 'Employee'];
            

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