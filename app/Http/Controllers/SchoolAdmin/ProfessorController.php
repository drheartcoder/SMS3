<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;

use App\Models\UserModel;
use App\Models\LevelModel;
use App\Models\ParentModel;
use App\Models\RoleModel;
use App\Models\CourseModel;
use App\Models\UserRoleModel;   
use App\Models\ProfessorModel;
use App\Models\EmployeeModel;
use App\Models\LevelClassModel;
use App\Models\ActivationModel;
use App\Models\UserTranslationModel;
use App\Models\ProfessorCoursesmodel;
use App\Models\CourseTranslationModel;

use App\Models\SchoolProfileModel;
use App\Models\SchoolTemplateTranslationModel;

/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
use App\Models\AcademicYearModel;   
/*Activity Log */
use App\Common\Services\LanguageService;
use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;
use App\Common\Services\CheckEmailExistanceService;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;
use PDF;

class ProfessorController extends Controller
{
    use MultiActionTrait;
    public function __construct(    
                                    UserModel $user,
                                    UserTranslationModel $translation,
                                    UserRoleModel $user_role_model,
                                    RoleModel $role_model,
                                    ActivityLogsModel $activity_logs,
                                    ProfessorModel $professor,
                                    LanguageService $language,
                                    CourseModel $course,
                                    CourseTranslationModel $courseTranslation,
                                    ParentModel $parent,
                                    EmailService $mail_service,
                                    SchoolProfileModel $profile,
                                    SchoolTemplateTranslationModel $template,
                                    AcademicYearModel $year,
                                    CommonDataService $CommonDataService,
                                    CheckEmailExistanceService $CheckEmailExistanceService,
                                    EmployeeModel $EmployeeModel

                                )
    {
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->CourseModel                  = $course;
        $this->CourseTranslationModel       = $courseTranslation;
        $this->AcademicYearModel            = $year;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->EmailService                 = $mail_service;
        $this->ProfessorModel               = $professor;
        $this->BaseModel                    = $this->ProfessorModel;
        $this->ActivityLogsModel            = $activity_logs; /* Activity Model */   
        $this->LanguageService              = $language;  
        $this->ParentModel                  = $parent;
        $this->SchoolProfileModel           = $profile;
        $this->SchoolTemplateTranslationModel = $template;
        $this->CommonDataService            = $CommonDataService;
        $this->EmployeeModel                = $EmployeeModel;
        $this->ProfessorCoursesmodel        = new ProfessorCoursesmodel();
        $this->CheckEmailExistanceService   = $CheckEmailExistanceService;
        $this->LevelModel                   = new LevelModel();  

        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/professor';
        
        $this->module_title                 = translation("professor");
        $this->modyle_url_slug              = translation("professor");

        $this->module_view_folder           = "schooladmin.professor";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-user';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->first_name = $this->last_name =$this->ip_address ='';
        $this->role       = 'professor';

        $this->arr_view_data['page_title']    = translation("professor");
        $this->arr_view_data['base_url']      = $this->user_profile_base_img_path;
        $this->arr_view_data['image_path']      = $this->user_profile_public_img_path;

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
            $this->user_id           = $obj_data->id;  
        }
        /* Activity Section */

        /*Local Section*/
        if(Session::has('locale'))
        {
            $this->locale = Session::get('locale');
        }
        else
        {
            $this->locale = 'en';
        }
        /*Local Section*/
    }   
    /*
    | create() : load professor index page 
    | Auther : Sayali 
    | Date : 
    */
    public function index(Request $request)
    {   


        $module_title = ucfirst(translation("manage"))." ".strtolower(str_plural($this->module_title));
        $this->arr_view_data['role']            = 'professor';
        $this->arr_view_data['module_title']    = $module_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function get_professor(Request $request,$type='',$fun_type='')
    {
        $academic_year_arr = [];
        $academic_year_ids = $this->CommonDataService->get_academic_year_less_than($this->academic_year);
        if($academic_year_ids!=''){
            $academic_year_arr = explode(',',$academic_year_ids);
        }
         
        $user_details             = $this->UserModel->getTable();
        $prefixed_user_details    = DB::getTablePrefix().$this->UserModel->getTable();


        $locale = $this->locale;
        
        $user_details      = $this->UserModel->getTable();
        $user_trans_table  = $this->UserTranslationModel->getTable();                  
        $professor         = $this->ProfessorModel->getTable();       
        $obj_user = DB::table($professor)
                                ->select(DB::raw($professor.".id as id,".
                                                 $professor.".professor_no,".
                                                 $professor.".user_id,".   
                                                 $professor.".has_left,".   

                                                 $user_details.".email as email, ".
                                                 $professor.".is_active as is_active, ".
                                                 $professor.".academic_year_id as academic_year_id, ".
                                                 $prefixed_user_details.".mobile_no, ".
                                                 $prefixed_user_details.".national_id, ".
                                                 "CONCAT(".$user_trans_table.".first_name,' ',"
                                                          .$user_trans_table.".last_name) as user_name"
                                                 ))
                                ->whereNull($professor.'.deleted_at')
                                ->join($user_details,$professor.'.user_id',' = ',$user_details.'.id')
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                ->where($professor.".school_id","=",$this->school_id)
                                ->where($user_trans_table.'.locale','=',$locale)
                                ->whereIn($professor.'.academic_year_id',$academic_year_arr)
                                ->orderBy($professor.'.id','DESC');
  
                              
        
        /* ---------------- Filtering Logic ----------------------------------*/  

        if($fun_type=='export'){
            $search_term = $request->input('search');
        }else{
            $search = $request->input('search');
            $search_term = $search['value'];
        }                 

        if($request->has('search') && $search_term!="")
        {
                $obj_user = $obj_user ->WhereRaw("( (".$user_details.".email LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$user_details.".national_id LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$user_details.".mobile_no LIKE '%".$search_term."%') ")
                                     ->orWhereRaw(" ( CONCAT(".$user_trans_table.".first_name,' ',".$user_trans_table.".last_name)  LIKE  '%".$search_term."%' ) )");
        }
        
        if($fun_type=="export"){
            return $obj_user->get();
        }else{
            return $obj_user;
        }
    }

    public function get_records(Request $request,$type='')
    {
        $arr_current_user_access =[];
    
        $arr_current_user_access = $this->CommonDataService->current_user_access();
        $obj_user        = $this->get_professor($request,$type);



        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('users.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data)
                            {
                                return base64_encode($data->id);
                            });
        }                    

        $json_result     = $json_result
                            ->editColumn('has_left',function($data)use ($arr_current_user_access){
                                if($data->has_left==1){
                                    $left =  $this->module_url_path.'/not_left/'.base64_encode($data->id);
                                    return '<a href="javascript:void(0)" title="'.translation('access_denied').'" onclick="checkExistance('.$data->user_id.','.$data->id.')"><i class="fa fa-check"></i></a>';
                                }
                                else{
                                    $left =  $this->module_url_path.'/has_left/'.base64_encode($data->id);
                                    return '<a href="'.$left.'" title="'.translation('change_status').'" onclick="return confirm_action(this,event,\''.translation('is_this_user_really_left_from_the_school').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-times"></i></a>';
                                }
                            })  
                            ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                            {
                                $build_edit_action = $build_status_btn = $build_view_action = $build_delete_action ='';

                                $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                    $build_view_action = '<a href="'.$view_href.'" title="'.translation('view').'" class="green-color"><i class="fa fa-eye" ></i></a>';

                                if(array_key_exists('professor.update',$arr_current_user_access))
                                {       
                                    $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                    if($data->has_left==0)
                                    {
                                        $build_edit_action = '<a href="'.$edit_href.'" title="'.translation('edit').'" class="orange-color"><i class="fa fa-edit" ></i></a>';    
                                    }
                                    else
                                    {
                                        $build_edit_action = '<a style="position: relative;" href="javascript:void(0)" title="'.translation('access_denied').'"  class="orange-color" ><i class="fa fa-edit" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                    }
                                    

                                    if($data->has_left==0)
                                    {
                                        if($data->is_active != null && $data->is_active == "0")
                                        {   
                                            $build_status_btn = '<a class="light-red-color" href="'.$this->module_url_path.'/activate/'.base64_encode($data->id).'" 
                                            onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" title="'.translation('activate').'" ><i class="fa fa-lock"></i></a>';
                                        }
                                        elseif($data->is_active != null && $data->is_active == "1")
                                        {
                                            $build_status_btn = '<a class="light-blue-color" title="'.translation('deactivate').'" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-unlock"></i></a>';
                                        }
                                    }
                                    else
                                    {
                                        if($data->is_active != null && $data->is_active == "0")
                                        {
                                            $build_status_btn = '<a style="position: relative;" class="light-red-color" href="javascript:void(0)" title="'.translation('access_denied').'"><i class="fa fa-lock" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                        }
                                        elseif($data->is_active != null && $data->is_active == "1")
                                        {
                                            $build_status_btn = '<a style="position: relative;" class="light-blue-color" href="javascript:void(0)" title="'.translation('access_denied').'"><i class="fa fa-unloack" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                        }
                                    }

                                }
                                if(array_key_exists('professor.delete',$arr_current_user_access))
                                {
                                    $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                    $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                }


                                return $build_view_action.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_status_btn.'&nbsp;'.$build_delete_action;
                            })
                            ->editColumn('build_checkbox',function($data)use ($arr_current_user_access){
                                $build_checkbox = ""; 
                                if(array_key_exists('professor.update',$arr_current_user_access) || array_key_exists('professor.delete',$arr_current_user_access))
                                {
                                    $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>';   
                                } 
                                
                            return $build_checkbox;
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    public function view($enc_id)
    {   
        $id = base64_decode($enc_id);
        if(is_numeric($id)){
            $professor_details =   $this->ProfessorModel
                                    ->with("get_user_details")
                                   ->where('id',$id)
                                   ->first();
            $arr_data = [];                                   
            if(isset($professor_details) && count($professor_details)>0){
                $arr_data   = $professor_details->toArray();
                $arrSubjects = $objSubjects = array();
                $objSubjects = $this->ProfessorCoursesmodel->where('professor_id','=',$arr_data['user_id'])->where('school_id','=',$this->school_id)->first();
                $arr_course_name = [];
                $levels='';
                if(!empty($objSubjects)){
                    $arrSubjects = $objSubjects->toArray();

                    $subjects = $this->CommonDataService->get_class_wise_subjects($arrSubjects['course_id']);
                    $this->arr_view_data['arrSubjects'] = $subjects;
                    $this->arr_view_data['arrClass']    = $arrSubjects;

                    if($arrSubjects['levels']!=''){
                        $levels = [];
                        $arr_levels = json_decode($arrSubjects['levels'],true);
                        foreach($arr_levels as $level){
                            $obj = $this->LevelModel->where('id',$level)->first();
                            if(isset($obj->level_name))
                            {
                                array_push($levels,$obj->level_name);
                            }    
                        }
                    }
                    
                }
                if($levels){
                    $levels = implode(',',$levels);
                }

                $this->arr_view_data['module_title']                 = ucfirst(translation("view")).' '.strtolower($this->module_title);
                $this->arr_view_data['module_icon']                  = $this->module_icon;
                $this->arr_view_data['module_url_path']              = $this->module_url_path;
                $this->arr_view_data['arr_data']                     = $arr_data;
                $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
                $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;
                $this->arr_view_data['theme_color']                  = $this->theme_color;
                $this->arr_view_data['levels']                       = $levels;
                
                return view($this->module_view_folder.'.view', $this->arr_view_data);
            }
            else{
                Flash::error(translation('no_data_available'));    
            }
        }
        else{

            Flash::error(translation('something_went_wrong'));
        }
        return redirect($this->module_url_path);
    }

    public function create()
    {


        $course     = [];
        $obj_course = $this->CommonDataService->get_courses($this->academic_year,config('app.project.role_slug.school_admin_role_slug'),$this->user_id);
        
        if($obj_course)
        {
            $course = $obj_course->toArray();
           
        }

        /* GET LEVELS */
        $obj_level = $arr_level = [];
        $obj_level  =   $this->CommonDataService->get_levels($this->academic_year);
        if($obj_level){
            $arr_level = $obj_level->toArray();
        }   
        /* GET LEVELS */
        $this->arr_view_data['arr_level']       = $arr_level;
        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['role']            = 'professor';
        $this->arr_view_data['course']          = $course;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['module_title']    = ucfirst(translation("add"))." ".strtolower($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['create_icon']     = $this->create_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.create', $this->arr_view_data);        
    }

    public function store(Request $request)
    {
        $arr_rules  = [];
       
        $arr_rules['email']                 =   'required|email';
        $arr_rules['mobile_no']             =   'required|numeric|digits_between:10,14';
        $arr_rules['national_id']           =   ['required','regex:/^[a-zA-Z0-9]*$/'];
        $arr_rules['birth_date']            =   'required|date|before:tomorrow';
        $arr_rules['gender']                =   'required|alpha';
        $arr_rules['address']               =   'required';
        $arr_rules['year_of_experience']    =   'required|numeric|digits_between:1,2';
        $arr_rules['telephone_no']          =   'required|numeric|digits_between:6,14';
        $arr_rules['qualification_degree']  =   ['required','regex:/^[a-zA-Z0-9 \,]*$/'];
        $arr_rules['first_name']            =   ['required','regex:/^[a-zA-Z ]*$/'];
        $arr_rules['last_name']             =   ['required','regex:/^[a-zA-Z ]*$/'];

        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'mobile_no.digits_between' => translation('please_enter_mobile_no_within_range_of_10_14'),
                            'telephone_no.digits_between'  => translation('please_enter_telephone_no_within_range_of_6_14'),
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'),
                            'alpha'                => translation('please_enter_letters_only'),
                            'year_of_experience.digits_between' => translation('year_of_experience_can_not_be_greater_than_2_digits')  
                        );

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        
        /***************** Image Upload starts here ******************/

        if ($request->file('profile_image')) 
        {
            
            $arr_image_size = [];
            $arr_image_size = getimagesize($request->file('profile_image'));

            if(!$arr_image_size)
            {
                Flash::error('Please use valid image');
                return redirect()->back(); 
            }

            $minHeight = 250;
            $minWidth  = 250;
            $maxHeight = 2000;
            $maxWidth  = 2000;

            if(($arr_image_size[0] < $minWidth || $arr_image_size[0] > $maxWidth) && ($arr_image_size[1] < $minHeight || $arr_image_size[1] > $maxHeight))
            {
                
                Flash::error(translation('please_upload_image_with_height_and_width_greater_than_or_equal_to_250_x_250_less_than_or_equal_to_2000_x_2000_for_best_result'));
                return redirect()->back();
            }

            $excel_file_name = $request->file('profile_image');
            $file_extension   = strtolower($request->file('profile_image')->getClientOriginalExtension()); 
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name       = sha1(uniqid().$excel_file_name.uniqid()).'.'.$file_extension;
                $request->file('profile_image')->move($this->user_profile_base_img_path,$file_name);
            
                /* Unlink the Existing file from the folder */
                $obj_image = $this->UserModel->where('id',$request->input('user_id'))->first(['profile_image']);
                if($obj_image)   
                {   
                    $_arr = [];
                    $_arr = $obj_image->toArray();
                    if(isset($_arr['profile_image']) && $_arr['profile_image'] != "" )
                    {
                        $unlink_path    = $this->user_profile_base_img_path.$_arr['profile_image'];
                        @unlink($unlink_path);
                    }
                }
                $is_new_file_uploaded = TRUE;      
            }
            else
            {
                Flash::error('invalid_file_type_while_creating'.str_singular($this->module_title));
                return redirect()->back();
            }   
        }
        else
        {
            $file_name  =   '';
        }
         /************* Image Upload ends here ********************/

        $school_no      =   Session::get('school_id');
        $professor_no   =   $this->generate_professor_no($school_no);
        $data           =   [];
        $type           =   'professor';

        $email          =   $request->input('email');
        $exist = $this->CheckEmailExistanceService->check_existence_while_registration($email);

        if($exist=='exist'){
            Flash::error(translation('this_email_is_already_exist_for_this_school'));
            return redirect()->back();
        }
        if($exist=='not_exist')
        {
            $data           =   [];
            $password       = generate_password_reg('prof');
            $credentials    =   [
                                    'email'         =>  trim($request->Input('email')),
                                    'password'      =>  $password,
                                    'telephone_no'  =>  $request->Input('telephone_no'),
                                    'mobile_no'     =>  $request->Input('mobile_no'),
                                    'address'       =>  $request->Input('address'),
                                    'profile_image' =>  $file_name,
                                    'national_id'   =>  trim($request->Input('national_id')),
                                    'birth_date'    =>  $request->input('birth_date'),
                                    'gender'        =>  $request->Input('gender'),
                                    'city'          =>  trim($request->Input('city')),
                                    'country'       =>  trim($request->Input('country')),
                                    'is_active'     =>  '1',
                                    'latitude'      =>  $request->Input('latitude'),
                                    'longitude'     =>  $request->Input('longitude')
                                ];

            $user = Sentinel::registerAndActivate($credentials);


            $role = Sentinel::findRoleBySlug($type);
            
            $role->users()->attach($user);

            $professor_details = [];
            $professor_details['school_id']             =   $this->school_id;
            $professor_details['user_id']               =   $user->id;
            $professor_details['professor_no']          =   $professor_no;
            $professor_details['status']                =   $request->input('status');
            $professor_details['year_of_experience']    =   $request->input('year_of_experience');
            $professor_details['qualification_degree']  =   trim($request->input('qualification_degree'));
            $professor_details['subject_id']            =   '0';
            $professor_details['language']              =   'en';
            $professor_details['academic_year_id']      =   $this->academic_year;
            
            $professor  = $this->ProfessorModel->create($professor_details);
            $arr_data   =   $this->LanguageService->get_all_language();
            if($user)
            {
                /* update record into translation table */
                
                if(sizeof($arr_data) > 0 )
                {
                    foreach ($arr_data as $lang) 
                    {            
                        $first_name       = trim($request->input('first_name'));
                        $last_name        = trim($request->input('last_name'));
                        if( (isset($first_name)  && $first_name != '') && (isset($last_name) && $last_name != ''))
                        { 
                            $translation = $user->translateOrNew($lang['locale']);
                            $translation->first_name    = $first_name;
                            $translation->last_name     = $last_name;
                            $translation->user_id       = $user->id;
                            $translation->locale        = $lang['locale'];
                            $translation->save();
                            Flash::success(str_singular($this->module_title).' '.translation('created_successfully'));
                        }
                    }
                } 
               /*------------------------------------------------------*/
            }
            $password2       = generate_password_reg('prof');
            $data          = [
                                'first_name'    =>  trim($request->Input('first_name')),
                                'last_name'     =>  trim($request->Input('last_name')),
                                'password'      =>  $password2,
                                'email'         =>  trim($request->Input('email'))
                             ];

            $arr_mail_data = $this->built_mail_data($data); 
            $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id); 
            
            if($user)
            {   


                if(!empty($request->input('subject')))
                {
                    /*Delete Previusly added subjects  */
                    $obj_sub_qualified = ProfessorCoursesmodel::where('professor_id',$user->id)
                                                    ->where('school_id',$this->school_id)
                                                    ->delete();

                    $arrlevels = $emp_quali =  array();
                    $subjects          = $request->input('subject');
                    $levels            = $request->input('levels');
                    /* store level wise */
                    if($subjects){
                        $emp_quali['school_id']            = $this->school_id;
                        $emp_quali['professor_id']         = $user->id;
                        $emp_quali['course_id']            = json_encode($subjects); 
                        $emp_quali['levels']               = json_encode($levels); 
                        $emp_quali['academic_year_id']     = $this->academic_year;
                        
                        $res  =  ProfessorCoursesmodel::create($emp_quali);
                    }
                    /* store level wise */
                }

                Flash::success(translation('professor_added_successfully'));
                return redirect()->back();
            }
            else
            {

                Flash::error(translation('problem_occured_while_adding_new_professor'));
                return redirect()->back();
            }
        }
        else
        {
           
            $user    = Sentinel::findById($exist);
            
            $role    = Sentinel::findRoleBySlug($type);
            
            $user_details = $this->UserRoleModel->where('user_id',$exist)->where('role_id',$role->id)->first();
            if(isset($user_details) && $user_details==null && count($user_details)==0)
            {
                $role->users()->attach($user);    
            }       

            $professor_details = [];
            $professor_details['school_id']             =   $this->school_id;
            $professor_details['user_id']               =   $user->id;
            $professor_details['professor_no']          =   $professor_no;
            $professor_details['status']                =   $request->input('status');
            $professor_details['year_of_experience']    =   trim($request->input('year_of_experience'));
            $professor_details['qualification_degree']  =   trim($request->input('qualification_degree'));
            $professor_details['subject_id']            =   0;
            $professor_details['language']              =   'en';
            $professor_details['academic_year_id']      =   $this->academic_year;
            
            $this->ProfessorModel->create($professor_details);
            $password3 = generate_password_reg('prof');
            $data          = [
                                'first_name'    =>  trim($request->Input('first_name')),
                                'last_name'     =>  trim($request->Input('last_name')),
                                'password'      =>  $password3,
                                'email'         =>  trim($request->Input('email'))
                             ];

            $arr_mail_data = $this->built_mail_data($data); 
            $this->EmailService->send_mail($arr_mail_data); 
            if($user)
            {
                if(!empty($request->input('subject')))
                {
                    /*Delete Previusly added subjects  */
                    $obj_sub_qualified = ProfessorCoursesmodel::where('professor_id',$user->id)
                                                    ->where('school_id',$this->school_id)
                                                    ->delete();

                    $arrlevels = $emp_quali =  array();
                    $subjects          = $request->input('subject');
                    $levels            = $request->input('levels');
                    /* store level wise */
                    if($subjects){
                        $emp_quali['school_id']    = $this->school_id;
                        $emp_quali['professor_id'] = $user->id;
                        $emp_quali['course_id']    = json_encode($subjects); 
                        $emp_quali['levels']       = json_encode($levels); 
                        $emp_quali['academic_year_id']     = $this->academic_year;
                        
                        $res  =  ProfessorCoursesmodel::create($emp_quali);
                    }
                    /* store level wise */
                }

                Flash::success(translation('professor_added_successfully'));
                return redirect()->back();
            }
            else
            {

                Flash::error(translation('problem_occured_while_adding_new_professor'));
                return redirect()->back();
            }
        }
        
    }
    public function generate_professor_no($school_id)
    { 
        $professor_no  =   'PR'.strtoupper(substr($school_id,2,3)).rand(10000,99999); 
        
        $exist = $this->ProfessorModel->where('professor_no',$professor_no)->first();
        if($exist)
        {
            $professor_no = $this->generate_professor_no($school_id);
        }
        return  $professor_no;
    }

    public function checkEmail(Request $request)
    {
        
        $email = $request->input('email');
        $exist = $this->CheckEmailExistanceService->check_existence_while_registration($email);

        if($exist=='exist'){
            return response()->json(array('status'=>'error','msg'=>translation('this_email_is_already_exist')));
        }
        if($exist!='not_exist'){

            $parent_details = $this->UserModel->with('get_parent_details')->where('id',$exist)->first();
            $arr_temp =[];
            if(count($parent_details)>0){
                $arr_temp                       = [];
                $arr_temp['first_name']         = isset($parent_details->first_name) ?  $parent_details->first_name : '';
                $arr_temp['last_name']          = isset($parent_details->last_name) ?  $parent_details->last_name : '';
                $arr_temp['address']            = isset($parent_details->address) ?  $parent_details->address : '';
                $arr_temp['city']               = isset($parent_details->city) ?  $parent_details->city : '';
                $arr_temp['country']            = isset($parent_details->country) ?  $parent_details->country : '';
                $arr_temp['national_id']        = isset($parent_details->national_id) ?  $parent_details->national_id : '';
                $arr_temp['birth_date']         = isset($parent_details->birth_date) && $parent_details->birth_date!= '0000-00-00' ?  $parent_details->birth_date : '';
                $arr_temp['gender']             = isset($parent_details->gender) ?  $parent_details->gender : '';
                $arr_temp['mobile_no']          = isset($parent_details->mobile_no) ?  $parent_details->mobile_no : '';
                $arr_temp['telephone_no']       = isset($parent_details->telephone_no) ?  $parent_details->telephone_no : '';
                $arr_temp['status']             = isset($parent_details->get_parent_details->marital_status) ?  $parent_details->get_parent_details->marital_status : '';
                $arr_temp['qualification_degree']       = isset($parent_details->get_parent_details->qualification_degree) ?  $parent_details->get_parent_details->qualification_degree : '';
            }

            return response()->json(array('status'=>'parent','msg'=>translation('this_email_is_already_exist'),'details'=>$arr_temp));
        }
        return response()->json(array('status'=>'success'));
    }

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
    public function edit($enc_id)
    {
        $id = base64_decode($enc_id,TRUE);

        if(is_numeric($id)){
            $course     = [];
            $obj_course = $this->CommonDataService->get_courses($this->academic_year,config('app.project.role_slug.school_admin_role_slug'),$this->user_id);
            
            if($obj_course){
                $course = $obj_course->toArray();
            }

            $professor_details =   $this->ProfessorModel
                                   ->whereHas('get_user_details',function(){}) 
                                   ->with('get_user_details')
                                   ->where('id',$id)
                                   ->first();

            $arr_professor_details = [];
            $obj_level = $arr_level = [];
            $arrSubjects = $objSubjects = array();
            if(isset($professor_details) && count($professor_details)>0){
                
                $arr_professor_details = $professor_details->toArray();
                    
                $obj_level  =   $this->CommonDataService->get_levels($this->academic_year);
                if($obj_level){
                    $arr_level = $obj_level->toArray();
                }   
                
                $objSubjects = ProfessorCoursesmodel::where('professor_id','=',$arr_professor_details['user_id'])->where('school_id','=',$this->school_id)->first();
                if(!empty($objSubjects)){
                    $arrSubjects = $objSubjects->toArray();
                }

                $this->arr_view_data['arrSubjects']     = $arrSubjects;

                $this->arr_view_data['arr_level']       = $arr_level;
                $this->arr_view_data['edit_mode']       = TRUE;
                $this->arr_view_data['role']            = 'professor';
                $this->arr_view_data['enc_id']          = $enc_id;
                $this->arr_view_data['arr_data']        = $arr_professor_details;
                $this->arr_view_data['course']          = $course;

                $this->arr_view_data['module_title']    = translation("edit")." ".strtolower($this->module_title);
                $this->arr_view_data['module_icon']     = $this->module_icon;
                $this->arr_view_data['create_icon']       = $this->edit_icon;
                $this->arr_view_data['module_url_path'] = $this->module_url_path;
                $this->arr_view_data['theme_color']     = $this->theme_color;

                return view($this->module_view_folder.'.edit', $this->arr_view_data);
            }
            else{
                Flash::error(translation('no_data_available'));
            }
        }
        else{
            Flash::error(translation('something_went_wrong'));
        }
        return redirect($this->module_url_path);
    }

    public function update(Request $request,$enc_id)
    {
        $id = base64_decode($enc_id);
        if(is_numeric($id)){

            $arr_rules = array();
            $arr_lang   =   $this->LanguageService->get_all_language();
            $arr_rules['email']                 =   'required|email';
            $arr_rules['mobile_no']             =   'required|numeric|digits_between:10,14';
            $arr_rules['national_id']           =   ['required','regex:/^[a-zA-Z0-9]*$/'];
            $arr_rules['birth_date']            =   'required|date|before:tomorrow';
            $arr_rules['gender']                =   'required|alpha';
            $arr_rules['address']               =   'required';
            $arr_rules['year_of_experience']    =   ['required','regex:/^[0-9\.]+$/'];
            $arr_rules['telephone_no']          =   'required|numeric|digits_between:6,14';
            $arr_rules['qualification_degree']  =   ['required','regex:/^[a-zA-Z0-9 \,]*$/'];
            $arr_rules['first_name']            =   ['required','regex:/^[a-zA-Z ]*$/'];
            $arr_rules['last_name']             =   ['required','regex:/^[a-zA-Z ]*$/'];

              $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'mobile_no.digits_between' => translation('please_enter_mobile_no_within_range_of_10_14'),
                            'telephone_no.digits_between'  => translation('please_enter_telephone_no_within_range_of_6_14'),
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'),
                            'alpha'                => translation('please_enter_letters_only'),
                            'year_of_experience.digits_between' => translation('year_of_experience_can_not_be_greater_than_2_digits')  
                        );
            $validator = Validator::make($request->all(),$arr_rules,$messages);

            if($validator->fails())
            { 
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            
            $oldImage = $request->input('old_image');
            if($request->hasFile('profile_image'))
            { 
                $arr_image_size = [];
                $arr_image_size = getimagesize($request->file('profile_image'));

                if(isset($arr_image_size) && $arr_image_size==false)
                {
                    Flash::error('Please use valid image');
                    return redirect()->back(); 
                }

                $minHeight = 250;
                $minWidth  = 250;
                $maxHeight = 2000;
                $maxWidth  = 2000;

                if(($arr_image_size[0] < $minWidth || $arr_image_size[0] > $maxWidth) && ($arr_image_size[1] < $minHeight || $arr_image_size[1] > $maxHeight))
                {
                    
                    Flash::error(translation('please_upload_image_with_height_and_width_greater_than_or_equal_to_250_x_250_less_than_or_equal_to_2000_x_2000_for_best_result'));
                    return redirect()->back();
                }

                $file_name = $request->file('image');
                $file_extension = strtolower($request->file('profile_image')->getClientOriginalExtension());
                if(in_array($file_extension,['png','jpg','jpeg']))
                {
                    $file_name = time().uniqid().'.'.$file_extension;
                    $isUpload = $request->file('profile_image')->move($this->user_profile_base_img_path , $file_name);
                    if($isUpload)
                    {
                        @unlink($this->user_profile_base_img_path.$oldImage);
                        @unlink($this->user_profile_base_img_path.'/thumb_50X50_'.$oldImage);
                    }
                }
                else
                {
                    Flash::error(translation('invalid_file_type_while_creating').' '.str_singular($this->module_title));
                    return redirect()->back();
                }
            }
            else
            {
                $file_name = $oldImage;
            }

            $professor_details = [];
            $professor_details['status']                =   $request->input('status');
            $professor_details['year_of_experience']    =   $request->input('year_of_experience');
            $professor_details['qualification_degree']  =   trim($request->input('qualification_degree'));
            $professor_details['subject_id']            =   '0';
            $this->ProfessorModel->where('id',$id)->update($professor_details);

            $user  = $this->ProfessorModel->select('user_id')->where('id',$id)->first();

            $user_id = isset($user->user_id) ? $user->user_id : 0;

            $arr_data                   = [];
            $arr_data['profile_image']  = $file_name;
            $arr_data['latitude']       = $request->input('latitude');
            $arr_data['longitude']      = $request->input('longitude');
            $arr_data['telephone_no']   = $request->input('telephone_no');
            $arr_data['national_id']    = $request->input('national_id');    
            $arr_data['mobile_no']      = $request->input('mobile_no');
            $arr_data['birth_date']     = $request->input('birth_date');
            $arr_data['gender']         = $request->input('gender');
            $arr_data['city']           = trim($request->input('city'));
            $arr_data['country']        = trim($request->input('country'));
            $arr_data['address']        = trim($request->input('address'));
            
            $obj_data = $this->UserModel->where('id',$user_id)->update($arr_data);

            $status = $this->UserModel->where('id',$user_id)->first(); 

            $res = '';        
            if(!empty($request->input('subject')))
            {
                $obj_sub_qualified = ProfessorCoursesmodel::where('professor_id',$user_id)
                                                        ->where('school_id',$this->school_id)
                                                        ->delete();
                $arrlevels = $emp_quali =  array();
                $subjects          = $request->input('subject');  
                $levels            = $request->input('levels');   
                /*Bring the classes between the orders */
                /* store level wise */
                if($subjects){
                    $emp_quali['school_id']    = $this->school_id;
                    $emp_quali['professor_id'] = $user_id;
                    $emp_quali['course_id']    = json_encode($subjects);
                    $emp_quali['levels']       = json_encode($levels);
                    $emp_quali['academic_year_id']     = $this->academic_year;
                    $res  =  ProfessorCoursesmodel::create($emp_quali);
                }
                /* store level wise */
            }                                                

            $arr_data   =   $this->LanguageService->get_all_language();

            if($status)
            {
                /* update record into translation table */
                if(sizeof($arr_lang) > 0 )
                {
                    foreach ($arr_lang as $lang) 
                    {            
                        
                        $first_name       = trim($request->input('first_name'));
                        $last_name        = trim($request->input('last_name'));
                        if( (isset($first_name)  && $first_name != '') && (isset($last_name) && $last_name != ''))
                        { 
                            $translation = $status->translateOrNew($lang['locale']);
                            $translation->first_name    = $first_name;
                            $translation->last_name     = $last_name;
                            $translation->save();
                            Flash::success(str_singular($this->module_title).' '.translation('updated_successfully'));
                        }
                    }
                } 
               /*------------------------------------------------------*/
            }
            
            if($obj_data || $res)
            {   
                Flash::success(str_singular($this->module_title).' '.translation('updated_successfully')); 
            }
            else
            {
                Flash::error(translation('problem_occurred_while_updating').' '.str_singular($this->module_title));  
            }
            return redirect()->back();
        }
        return redirect($this->module_url_path);
    }
    public function built_mail_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            
            $login_url = '<p class="email-button"><a target="_blank" href="'.\URL::to('/professor').'">Click Here</a></p><br/>' ;

            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'LAST_NAME'        => ucfirst($arr_data['last_name']),
                                  'EMAIL'            => $arr_data['email'],
                                  'PASSWORD'         => $arr_data['password'],
                                  'URL'              => $login_url,
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                  'PROJECT_NAME'     => config('app.project.name')];


            $arr_mail_data                        = [];
            $arr_mail_data['email_template_slug'] = 'professor_registration';
            $arr_mail_data['arr_built_content']   = $arr_built_content;
            $arr_mail_data['user']                = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function export(Request $request)
    {      
        $obj_data = $this->get_professor($request,'','export');
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
                        $arr_fields['id']             = translation('sr_no');
                        $arr_fields['professor_number']= translation('professor_number');
                        $arr_fields['name']           = translation('name');
                        $arr_fields['email']          = translation('email');
                        $arr_fields['national_id']    = translation('national_id');
                        $arr_fields['mobile_no']      = translation('mobile_no');
                        $arr_fields['has_left']       = translation('has_left');
                        
                        $sheet->row(2, ['',ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                        $sheet->row(4, $arr_fields);
                        if(sizeof($obj_data)>0) 
                        {
                            
                            $arr_tmp = [];
                            foreach($obj_data as $key => $result)
                            {
                                $status = "";
                                if($result->has_left==1)
                                {
                                    $status = "Yes";
                                }
                                elseif($result->has_left==0)
                                {
                                    $status = "No";
                                }
                                $arr_tmp[$key]['id']             = intval($key+1);
                                $arr_tmp[$key]['professor_no']   = $result->professor_no;
                                $arr_tmp[$key]['name']           = ucwords($result->user_name);
                                $arr_tmp[$key]['email']          = $result->email;
                                $arr_tmp[$key]['national_id']    = $result->national_id;
                                $arr_tmp[$key]['mobile_no']      = $result->mobile_no;
                                $arr_tmp[$key]['has_left']       = $status;
                            }
                               $sheet->rows($arr_tmp);
                        }
                    });
                })->export('csv');     
        }
        
        if($request->file_format == 'pdf')
        {
            $school_name = $this->CommonDataService->get_school_name();

            $school_address = $this->CommonDataService->get_school_address();

            $school_email = $this->CommonDataService->get_school_email();

            $school_logo = $this->CommonDataService->get_school_logo();

            $this->arr_view_data['arr_data']      = $obj_data;
            $this->arr_view_data['school_name']   = $school_name;    
            $this->arr_view_data['school_address']= $school_address;
            $this->arr_view_data['school_email']  = $school_email;
            $this->arr_view_data['school_logo']   = $school_logo;

            $pdf = PDF::loadView($this->module_view_folder.'.pdf', $this->arr_view_data);
            return $pdf->stream($this->module_view_folder.'.pdf', $this->arr_view_data);
        }
    }

     /*
    | download() : Download Doc
    | Auther  : Padmashri
    | Date    :  11-05-2018 and 14-05-2018
    */
    public function download_doc($format){

        if($format=='xls')
        {
            $obj_course = $arr_course = $arr_levels =  array();

            $obj_course = $this->CommonDataService->get_courses($this->academic_year,config('app.project.role_slug.school_admin_role_slug'),$this->user_id);
            if($obj_course)
            {
                $arr_course = $obj_course->toArray();
            }

            $obj_levels = $this->CommonDataService->get_levels($this->academic_year);
            if(count($obj_levels)){
                $arr_levels = $obj_levels->toArray();   
            }
            
            \Excel::create(ucwords($this->module_title).'-DETAILS', function($excel) use($arr_course,$arr_levels)
            {
                  $excel->sheet(translation('instroction_sample'), function($sheet) use($arr_course,$arr_levels)
                  { 
                       
                        $title = translation('instroction_for_adding_records_for')." ".ucwords($this->module_title);
                        $sheet->setHeight(1, 50);
                        $sheet->setAutoSize(false);
                        $sheet->cell('A1', function($cell) use($title){         // set title at D1 cell i.e. middle of doc. 
                            $cell->setValue($title);
                            $cell->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '16',
                                'bold'       =>  true
                            ));
                            $cell->setFontColor('#660099');
                        });

                        $title = translation('please_add_valid_data_if_you_failed_to_add_valid_data_then_that_data_will_skip_automatically');
                        $sheet->setHeight(2, 50);
                        $sheet->setAutoSize(false);
                        $sheet->cell('A1', function($cell) use($title){         
                            $cell->setValue($title);
                            $cell->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '16',
                                'bold'       =>  true
                            ));
                            $cell->setFontColor('#ff3333');
                        });


                        $sheet->setWidth('A', 20);
                        $sheet->row(4,array(translation("first_name"),"Alice"));
                        $sheet->setHeight(4, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(4, function($row){     //row no 2 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });


                        $sheet->row(5,array(translation('last_name'),"Aguillon"));
                        $sheet->setHeight(5, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(5, function($row){     //row no 5 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });


                        $sheet->row(6,array(translation('email'),"demo@mail.com(".translation("unique_in_all_user_in_this_system_except_parent_section").")"));
                        $sheet->setHeight(6, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(6, function($row){     //row no 6 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });


                        $sheet->row(7,array(translation('address'),"Madame Duval 27 RUE PASTEUR 14390 CABOURG FRANCE"));
                        $sheet->setHeight(7, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(7, function($row){     //row no 7 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });


                        $sheet->row(8,array(translation('city'),"Paris"));
                        $sheet->setHeight(8, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(8, function($row){     //row no 8 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(9,array(translation('country'),"France"));
                        $sheet->setHeight(9, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(9, function($row){     //row no 9 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(10,array(translation('latitude'),"15.2321321"));
                        $sheet->setHeight(10, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(10, function($row){     //row no 9 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(11,array(translation('longitude'),"70.34324234"));
                        $sheet->setHeight(11, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(11, function($row){     //row no 9 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });


                        $sheet->row(12,array(translation('national_id'),"1503110"));
                         $sheet->setHeight(12, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(12, function($row){     //row no 10 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(13,array(translation('birth_date'),"1990-05-29"));
                        $sheet->setHeight(13, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(13, function($row){     //row no 11 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(14,array(translation('gender'),"Male OR Female"));
                         $sheet->setHeight(14, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(14, function($row){     //row no 12 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(14,array("MARITAL-STATUS"));
                        $sheet->setHeight(14, 30);
                        $sheet->row(14, function($row){     //row no 3 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(15,array('SR.NO',translation('status'),translation('abbreviations')));
                        $sheet->setHeight(15, 30);
                        $sheet->row(15, function($row) {   //row no 5 formatiing for columns names
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $arr_status    = [];
                        $arr_status[0] = [0=>"Married",1=>"MARRIED"];
                        $arr_status[1] = [0=>"Single",1=>"SINGLE"];
                        $arr_status[2] = [0=>"Engaged",1=>"ENGAGED"];
                        $arr_status[3] = [0=>"Divorced",1=>"DIVORCED"];
                        
                        $i = 16 ; 

                        if(isset($arr_status) && sizeof($arr_status)>0)
                        {
                            foreach($arr_status as $key => $status)
                            {
                                $sheet->setHeight($i, 30);
                                $sheet->row($i, function($row) {
                                $row->setFont(array(
                                        'family'     => 'Calibri',
                                        'size'       => '11'
                                      ));
                                }); 

                                $key = $key + 1;

                                $sheet->appendRow($i, array($key,$status[0],$status[1]));

                                $i++;
                            }
                        }
                        
                        $sheet->row(21,array(translation('year_of_experience'),"5"));
                        $sheet->setHeight(21, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(21, function($row){     //row no 11 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(22,array(translation('mobile_no'),"78968546987"));
                        $sheet->setHeight(22, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(22, function($row){     //row no 11 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });


                        $sheet->row(23,array(translation('telephone_no'),"9888745698"));
                        $sheet->setHeight(23, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(23, function($row){     //row no 11 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->row(24,array(translation('qualification_degree'),"B.E (E.N.T.C),M.B.A etc "));
                        $sheet->setHeight(24, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row(24, function($row){     //row no 11 formatiing for title
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });
                     

                         $i= 25;

                     
                        $sheet->row($i,array('SR.NO',translation('subject'),translation('subject_list')));
                        $sheet->setHeight(25, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row($i, function($row) {   
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $i = $i + 1;
                        $sheet->setWidth('B', 20);
                        $sheet->setWidth('C', 20);
                        if(isset($arr_course) && sizeof($arr_course)>0)
                        {
                            foreach($arr_course as $key => $course)
                            {   
                                if(isset($course) && sizeof($course)>0)
                                {
                                    $sheet->setHeight($i, 30);
                                    $sheet->setAutoSize(false);
                                    $sheet->row($i, function($row) {
                                    $row->setFont(array(
                                            'family'     => 'Calibri',
                                            'size'       => '11',
                                          ));
                                    }); 

                                    $key = $key + 1;
                                    
                                    $sheet->appendRow($i, array($key,$course['get_course']['course_name'],$course['get_course']['course_name']));

                                    $i++;
                                }    
                            }
                        }

                        $sheet->row($i,array('SR.NO',translation('level'),translation('level')));
                        $sheet->setHeight($i, 30);
                        $sheet->setAutoSize(false);
                        $sheet->row($i, function($row) {   
                            $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '12',
                                'bold'       =>  true
                            ));
                        });

                        $i = $i + 1;
                        $sheet->setWidth('B', 20);
                        $sheet->setWidth('C', 20);
                        if(isset($arr_levels) && sizeof($arr_levels)>0)
                        {

                            foreach($arr_levels as $key => $level)
                            {   
                                if(isset($level['level_details']['level_name']))
                                {
                                    $sheet->setHeight($i, 30);
                                    $sheet->setAutoSize(false);
                                    $sheet->row($i, function($row) {
                                    $row->setFont(array(
                                            'family'     => 'Calibri',
                                            'size'       => '11',
                                          ));
                                    }); 

                                    $key = $key + 1;
                                    
                                    $sheet->appendRow($i, array($key,$level['level_details']['level_name'],$level['level_details']['level_name']));

                                    $i++;
                                }    
                            }
                        }
                        
                        });

                        
                        $excel->sheet(translation('report_sample'), function($sheet)  
                        {
                            $sheet->setWidth(array(
                                'A'     =>  30,
                                'B'     =>  30,
                                'C'     =>  30,
                                'D'     =>  30,
                                'E'     =>  35,
                                'F'     =>  40,
                                'G'     =>  45,
                                'H'     =>  15,
                                'I'     =>  45,
                                'J'     =>  25,
                                'K'     =>  45,
                                'L'     =>  15,
                                'M'     =>  15,
                                'N'     =>  50,
                                'O'     =>  45,
                                'P'     =>  45,
                                'Q'     =>  45,
                                'R'     =>  45    
                            ));

                            //setting first 100 rows height to 25
                            $arr_height = array();
                            for ($i=1; $i <=100 ; $i++) 
                            { 
                              $arr_height[$i] = 25;
                            }
                            $sheet->setHeight($arr_height);
                            
                            $sheet->row(1, function($row) {
                               $row->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '10',
                                'bold'       => true
                                ));
                            });

                            $sheet->row(1,array(
                                                    'First Name',
                                                    'Last Name',
                                                    'Email Id',
                                                    'Address',
                                                    'City',
                                                    'Country',
                                                    'Latitude',
                                                    'Longitude',
                                                    'National Id',
                                                    'Birth Date',
                                                    'Gender',
                                                    'Status',
                                                    'Year Of Experience',
                                                    'Mobile No',
                                                    'Telephone No',
                                                    'Qualification Degree',
                                                    'Subjects',
                                                    'Level'
                                                    ));
                    });                             

            })->export('xls');
        }
    }
   
    /*
    | import(): Import Data
    | Auther  : Padmashri
    | Date    : 11-05-2018
    */
    public function import(Request $request){

        if($request->hasFile('upload_file'))
        {
            $file = $request->file('upload_file');
            $validator = Validator::make(['file'=>$file], ['file'=>'required']);

            if($validator->fails())
            {
                Flash::error(translation('uploaded_file_is_not_valid_file_please_upload_valid_file'));
                return redirect()->back();
            }

            $file_ext = $file->getClientOriginalExtension();


            if($file_ext!='xls')
            {
                Flash::error(translation('uploaded_file_is_not_valid_file_please_upload_valid_file'));
                return redirect()->back();
            }

            $results = '';
            $results = \Excel::load($file, function($reader) {
                                    $reader->formatDates(false);
                            })->get();

            $record_inserted = 0;
            $success_record_count = $error_record_count = $duplicate_record_count =$skipped_record_count = 0;
            $arr_data   =   $this->LanguageService->get_all_language();
            if(isset($results) && sizeof($results)>0)
            {   
                foreach($results as $result_key => $file_data)
                {
                    if($result_key>=0)
                    {
                    
                        $data = $file_data->toArray();
                        
                        if(isset($data) && sizeof($data)>0)
                        {
                          
                                /*-----------------------------------------------
                                |Check if document is valid or not
                                -----------------------------------------------*/
                                $is_vaild_document = $this->is_vaild_document($data);
                                if($is_vaild_document==true)
                                {
                                    
                                    $first_name    = isset($data['first_name'])&&$data['first_name']!=""?trim($data['first_name']):'';
                                    $last_name     = isset($data['last_name'])&&$data['last_name']!=""?trim($data['last_name']):'';
                                    $national_id   = isset($data['national_id'])&&$data['national_id']!=""?trim($data['national_id']):'';
                                    $email         = isset($data['email_id'])&&$data['email_id']!=""?trim($data['email_id']):'';
                                    $birth_date    = isset($data['birth_date'])&&$data['birth_date']!=""?trim($data['birth_date']):'';
                                    $gender        = isset($data['gender'])&&$data['gender']!=""?trim($data['gender']):'';
                                    $status        = isset($data['status'])&&$data['status']!=""?trim($data['status']):'';
                                    $telephone_no  = isset($data['telephone_no'])&&$data['telephone_no']!=""?trim($data['telephone_no']):'';
                                    $mobile_no     = isset($data['mobile_no'])&&$data['mobile_no']!=""?trim($data['mobile_no']):'';
                                    $national_id   = isset($data['national_id'])&&$data['national_id']!=""?trim($data['national_id']):'';
                                    $address       = isset($data['address'])&&$data['address']!=""?trim($data['address']):'';
                                    $city          = isset($data['city'])&&$data['city']!=""?trim($data['city']):'';
                                    $country       = isset($data['country'])&&$data['country']!=""?trim($data['country']):'';
                                    $latitude      = isset($data['latitude'])&&$data['latitude']!=""?trim($data['latitude']):'';
                                    $longitude     = isset($data['longitude'])&&$data['longitude']!=""?trim($data['longitude']):'';
                                    
                                    $year_of_experience   = isset($data['year_of_experience'])&&$data['year_of_experience']!=''?$data['year_of_experience']:'';
                                    $qualification_degree = isset($data['qualification_degree'])&&$data['qualification_degree']!=''?$data['qualification_degree']:'';
                                    $subjects             = isset($data['subjects'])&&$data['subjects']!=''?$data['subjects']:'';
                                    $subject_id           = $this->get_cources_ids($subjects);

                                    $levels             = isset($data['level'])&&$data['level']!=''?$data['level']:'';
                                    $level_id           = $this->get_level_ids($levels);


                                    $school_no      =   Session::get('school_id');
                                    $professor_no   =   $this->generate_professor_no($school_no);
                                    $data           =   [];
                                    $type           =   'professor';

                                    

                                    
                                    $flag = 0;
                                    $employee = '';
                                    
                                    $exist = $this->CheckEmailExistanceService->check_existence_while_registration($email);

                                    if($exist=='exist'){
                                        Flash::error(translation('this_email_is_already_exist_for_this_school'));
                                        return redirect()->back();
                                    }
                                     
                                    if($exist=='not_exist')
                                    {
                                        $data           =   [];
                                        $password4 = generate_password_reg('prof');
                                        $credentials    =   [
                                                                'email'         =>  $email,
                                                                'password'      =>  $password4,
                                                                'telephone_no'  =>  $telephone_no,
                                                                'mobile_no'     =>  $mobile_no,
                                                                'address'       =>  $address,
                                                                'national_id'   =>  $national_id,
                                                                'birth_date'    =>  $birth_date,
                                                                'gender'        =>  $gender,
                                                                'city'          =>  $city,
                                                                'country'       =>  $country,
                                                                'is_active'     =>  '1',
                                                                'latitude'      =>  $latitude,
                                                                'longitude'     =>  $longitude
                                        ];
                                        $user = Sentinel::registerAndActivate($credentials);

                                        $role = Sentinel::findRoleBySlug($type);
                                        
                                        $role->users()->attach($user);

                                        $professor_details = [];
                                        $professor_details['school_id']             =   $this->school_id;
                                        $professor_details['user_id']               =   $user->id;
                                        $professor_details['professor_no']          =   $professor_no;
                                        $professor_details['status']                =   $status;
                                        $professor_details['year_of_experience']    =   $year_of_experience;
                                        $professor_details['qualification_degree']  =   $qualification_degree;
                                        $professor_details['language']              =   'en';
                                        $professor_details['academic_year_id']      =   $this->academic_year;
                                        
                                        $professor  = $this->ProfessorModel->create($professor_details);
                                        $arr_data   =   $this->LanguageService->get_all_language();
                                        if($user)
                                        {
                                            /* update record into translation table */
                                            
                                            if(sizeof($arr_data) > 0 )
                                            {
                                                foreach ($arr_data as $lang) 
                                                {            
                                                     
                                                    $translation = $user->translateOrNew($lang['locale']);
                                                    $translation->first_name    = $first_name;
                                                    $translation->last_name     = $last_name;
                                                    $translation->user_id       = $user->id;
                                                    $translation->locale        = $lang['locale'];
                                                    $translation->save();
                                                    Flash::success(str_singular($this->module_title).' '.translation('created_successfully'));
                                                }
                                            } 
                                           /*------------------------------------------------------*/
                                        }
                                        $password5 = generate_password_reg('prof');
                                        $data          = [
                                                            'first_name'    =>  $first_name,
                                                            'last_name'     =>  $last_name,
                                                            'password'      =>  $password5,
                                                            'email'         =>  $email
                                                         ];

                                        $arr_mail_data = $this->built_mail_data($data); 
                                        $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id); 

                                        $success_record_count++;
                                        $record_inserted++;

                                        if($user)
                                        {   

                                            if($subject_id)
                                            {
                                                /*Delete Previusly added subjects  */
                                                $obj_sub_qualified = ProfessorCoursesmodel::where('professor_id',$user->id)
                                                                                ->where('school_id',$this->school_id)
                                                                                ->delete();
                                                /* store level wise */
                                                if($subjects){
                                                    $emp_quali['school_id']    = $this->school_id;
                                                    $emp_quali['professor_id'] = $user->id;
                                                    $emp_quali['course_id']    = json_encode($subject_id); 
                                                    $emp_quali['levels']       = json_encode($level_id);
                                                    $emp_quali['academic_year_id']     = $this->academic_year;
                                                    $res  =  ProfessorCoursesmodel::create($emp_quali);
                                                }
                                                /* store level wise */
                                            }

                                            Flash::success(translation('professor_added_successfully'));
                                            return redirect()->back();
                                        }
                                        else
                                        {

                                            Flash::error(translation('problem_occured_while_adding_new_professor'));
                                            return redirect()->back();
                                        }
                                    }
                                    else
                                    {
                                        $user    = Sentinel::findById($exist);
                                       
                                        $role    = Sentinel::findRoleBySlug($type);
                                        $role->users()->attach($user);

                                        $professor_details = [];
                                        $professor_details['school_id']             =   $this->school_id;
                                        $professor_details['user_id']               =   $user->id;
                                        $professor_details['professor_no']          =   $professor_no;
                                        $professor_details['status']                =   $status;
                                        $professor_details['year_of_experience']    =   $year_of_experience;
                                        $professor_details['qualification_degree']  =   $qualification_degree;
                                        $professor_details['subject_id']            =   0;
                                        $professor_details['language']              =   'en';
                                        $professor_details['academic_year_id']      =   $this->academic_year;
                                        
                                        $this->ProfessorModel->create($professor_details);
                                        $password6 = generate_password_reg('prof');
                                        $data          = [
                                                            'first_name'    =>  $first_name,
                                                            'last_name'     =>  $last_name,
                                                            'password'      =>  $password6,
                                                            'email'         =>  $email
                                                         ];

                                        $arr_mail_data = $this->built_mail_data($data); 
                                        $this->EmailService->send_mail($arr_mail_data,$this->school_id); 

                                        $success_record_count++;
                                        $record_inserted++;

                                        if($user)
                                        {
                                            if($subject_id)
                                            {
                                                /*Delete Previusly added subjects  */
                                                $obj_sub_qualified = ProfessorCoursesmodel::where('professor_id',$user->id)
                                                                                ->where('school_id',$this->school_id)
                                                                                ->delete();
                                                /* store level wise */
                                                if($subjects){
                                                    $emp_quali['school_id']    = $this->school_id;
                                                    $emp_quali['professor_id'] = $user->id;
                                                    $emp_quali['course_id']    = json_encode($subject_id); 
                                                    $emp_quali['levels']       = json_encode($level_id);
                                                    $emp_quali['academic_year_id']     = $this->academic_year;

                                                    $res  =  ProfessorCoursesmodel::create($emp_quali);
                                                }
                                                /* store level wise */
                                            }
                                            /* Add subjects to the other table */

                                            $success_record_count++;
                                            $record_inserted++;
                                        }
                                        else
                                        {

                                            $error_record_count++;
                                        }
                                    }    
                                }
                                else
                                {
                                    $error_record_count++;
                                }
                                /*---------------------------------------------*/
                        }
                    }
                }
                
                if($success_record_count>0)
                {
                    
                    $error_msg = '';
                    $error_msg .= translation('your_data_imported_successfully');
                    if($duplicate_record_count>0 || $error_record_count > 0 || $skipped_record_count > 0 )
                    {   
                        $error_msg .= ', '.translation('also_some_records_are_duplicate');
                        $error_msg .= ' '.translation('some_records_are_skipped');
                    }
                    
                    Flash::success($error_msg);
                }
                else 
                {
                    $error_msg = '';
                    if($error_record_count>0)
                    {
                        $error_msg .= translation('some_records_are_not_valid');
                        if($duplicate_record_count>0)
                        {
                            $error_msg .= ', '.translation('also_some_records_are_duplicate');
                        }
                        $error_msg .= ', '.translation('some_records_are_skipped');
                    }
                    else if($duplicate_record_count>0)
                    {
                        $error_msg .= translation('some_records_are_duplicate');
                        $error_msg .= ', '.translation('some_records_are_skipped');
                    }
                    else
                    {
                        $error_msg = translation('error_while_importing_data');
                    }
                    Flash::error($error_msg);
                }
            }
            else
            {
                Flash::error(translation('error_while_importing_data'));
            }    
        }
     return redirect()->back();
    }

    /*
    | get_cources(): Get course data
    | Auther  : Padmashri
    | Date    : 11-05-2018
    */
    public function get_cources_ids($name=false)
    {
        
          $array = explode(',', $name);
          $comma_list = "'" .implode("', '", $array) . "'"; 
        $arr_ids = array();
        $strIds  = '';
        if($name!=false)
        {
            $arr_course = $obj_course = array();
            $obj_course = $this->CourseModel
                           ->whereHas('get_course',function($q)use($comma_list){
                                    $q->whereRaw(" course_name IN (".$comma_list.")");
                           })
                           ->get();
            if(isset($obj_course) && sizeof($obj_course)>0)
            {  
                $arr_course = $obj_course->toArray();
                foreach ($arr_course as $res) 
                {
                    array_push($arr_ids,$res['id']."");
                }
             }
        } 
        
        if(!empty($arr_ids)){
            return $arr_ids;
        }

        
    }

    /*
    | get_cources(): Get course data
    | Auther  : Padmashri
    | Date    : 11-05-2018
    */
    public function get_level_ids($name=false)
    {    
        $array = explode(',', $name);
        $comma_list = "'" .implode("', '", $array) . "'"; 
        $arr_ids = array();
        $strIds  = '';
        if($name!=false)
        {
            $arr_levels = $obj_levels = array();
            $obj_levels = $this->LevelModel
                           ->whereHas('get_level',function($q)use($comma_list){
                                    $q->whereRaw(" level_name IN (".$comma_list.")");
                           })
                           ->get();
            if(isset($obj_levels) && sizeof($obj_levels)>0)
            {  
                $arr_levels = $obj_levels->toArray();
                foreach ($arr_levels as $res) 
                {
                    array_push($arr_ids,$res['id']."");
                }
             }
        }

        if(!empty($arr_ids)){
            return $arr_ids;
        }
    }

     /*
    | is_vaild_document(): Validate the documents details
    | Auther  : Padmashri
    | Date    : 11-05-2018
    */
    public function is_vaild_document($data)
    {

        if(isset($data['first_name']) && $data['first_name']!="" &&
           isset($data['last_name']) && $data['last_name']!="" && 
           isset($data['national_id']) && $data['national_id']!="" && 
           isset($data['email_id']) && $data['email_id']!="" &&
           filter_var($data['email_id'], FILTER_VALIDATE_EMAIL)!=false &&
           isset($data['birth_date']) && $data['birth_date']!="" && 
           isset($data['gender']) && $data['gender']!="" && 
           isset($data['status']) && $data['status']!="" && 
           isset($data['telephone_no']) && $data['telephone_no']!="" && 
           $this->check_mobile_number($data['telephone_no'])!=false &&
           isset($data['mobile_no']) && $data['mobile_no']!="" && 
           $this->check_mobile_number($data['mobile_no'])!=false &&
           isset($data['national_id']) && $data['national_id']!="" && 
           isset($data['address']) && $data['address']!="" && 
           isset($data['city']) && $data['city']!="" &&
           isset($data['country']) && $data['country']!="" &&
           isset($data['year_of_experience']) && $data['year_of_experience']!="" &&
           $this->validate_number($data['year_of_experience'])!=false &&
           isset($data['qualification_degree']) && $data['qualification_degree']!="" &&
           isset($data['subjects']) && $data['subjects']!="" &&
           isset($data['latitude']) && $data['latitude']!="" &&
           isset($data['longitude']) && $data['longitude']!="" 
          )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function check_mobile_number($mobile_no)
    {
            $mo = Intval($mobile_no);
            if(is_numeric($mo)){
                if(strlen($mo) < 16 && strlen($mo) >= 7 ){
                    return true;
                }else{
                    return false;
                }
        }
        return false;
    }

    public function validate_number($number){
        if(is_numeric($number)){

            return true;
        }
        return false;
    }

    public function has_left($enc_id){
        $id = base64_decode($enc_id);
        if(is_numeric($id)){
            $this->ProfessorModel->where('id',$id)->update(['has_left'=>1]);
            Flash::success(translation("record_updated_successfully"));
            return redirect($this->module_url_path);
        }
        else{
            Flash::error(translation("something_went_wrong"));
            return redirect($this->module_url_path);
        }
    }

    public function not_left(Request $request){
        $id = $request->enc_id;
        
        if(is_numeric($id)){
            $this->ProfessorModel->where('id',$id)->update(['has_left'=>0]);
            return response()->json(array('status'=>'success','msg'=>translation('record_updated_successfully')));
        }
        else{
            return response()->json(array('status'=>'error','msg'=>translation('something_went_wrong')));
        }
    }

    public function check_existance(Request $request)
    {
        
        $exist = '';
        $employee = $this->EmployeeModel
                         ->where('user_id',$request->user_id)
                         ->where('school_id','!=',$this->school_id)
                         ->where('has_left',0)
                         ->where('is_active',1)
                         ->first();
        if(isset($employee) && $employee!=null && count($employee)>0)
        {
            $exist =  'true';
        }
        else
        {
            $professor = $this->ProfessorModel
                              ->where('user_id',$request->user_id)
                              ->where('school_id','!=',$this->school_id)
                              ->where('has_left',0)
                              ->where('is_active',1)
                              ->first();

            if(isset($professor) && $professor!=null && count($professor)>0)
            {
                $exist =  'true';
            }
            else
            {
                $exist = 'false';
            }
        }
        return $exist;
    }
}