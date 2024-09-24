<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Services\LanguageService;
use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;

use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\StudentModel;
use App\Models\UserRoleModel;   
use App\Models\RoleModel;
use App\Models\SchoolRoleModel;
use App\Models\ActivationModel;
use App\Models\ActivityLogsModel;   
use App\Models\EducationalBoardModel;
use App\Models\LevelModel;
use App\Models\LevelClassModel;
use App\Models\ClassModel;
use App\Models\ParentModel;
use App\Models\SchoolParentModel;
use App\Models\SchoolTemplateTranslationModel;
use App\Models\SchoolProfileModel;
use App\Models\BrotherhoodModel;
use App\Models\AcademicYearModel;
use App\Models\EmployeeModel;
use App\Models\ProfessorModel;
use App\Models\SchoolAdminModel;


use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;
class NewAdmissionController extends Controller
{
	
	public function __construct(
									UserModel $user,
                                    UserTranslationModel $translation,
                                    UserRoleModel $user_role_model,
                                    RoleModel $role_model,
                                    SchoolRoleModel $role,
                                    ActivityLogsModel $activity_logs,
                                    StudentModel $student,
                                    EmailService $mail_service,
                                    LanguageService $language,
                                    LevelModel $level,
                                    EducationalBoardModel $educational_board,
                                    LevelClassModel $level_class,
                                    ParentModel $parent,
                                    SchoolTemplateTranslationModel $SchoolTemplateTranslationModel,
                                    SchoolProfileModel $SchoolProfileModel,
                                    BrotherhoodModel $brotherhood,
                                    ClassModel $classModel,
                                    AcademicYearModel $AcademicYearModel,
                                    SchoolParentModel $SchoolParentModel,
                                    EmployeeModel $EmployeeModel,
                                    ProfessorModel $ProfessorModel,
                                    SchoolAdminModel $SchoolAdminModel,
                                    CommonDataService $CommonDataService
								) 
	{
        $this->arr_view_data 		= [];
		$this->EducationalBoardModel = $educational_board;
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->SchoolRoleModel              = $role;
       	$this->LevelClassModel 				= $level_class;
        $this->SchoolTemplateTranslationModel = $SchoolTemplateTranslationModel;
        $this->SchoolProfileModel           = $SchoolProfileModel;
        $this->BrotherhoodModel             = $brotherhood;
        $this->EmailService                 = $mail_service;
        $this->StudentModel 				= $student;
        $this->ActivityLogsModel            = $activity_logs; /* Activity Model */   
        $this->LanguageService              = $language;  
        $this->LevelModel                   = $level;
        $this->BaseModel   					= $student;
        $this->ParentModel                  = $parent;
        $this->ClassModel                   = $classModel;
        $this->AcademicYearModel            = $AcademicYearModel;
        $this->SchoolParentModel            = $SchoolParentModel;
        $this->EmployeeModel                = $EmployeeModel;
        $this->ProfessorModel               = $ProfessorModel;
        $this->SchoolAdminModel             = $SchoolAdminModel;
        $this->CommonDataService            = $CommonDataService;

		$this->module_url_path 		= url(config('app.project.school_admin_panel_slug')).'/admission_config';
        $this->module_view_folder   = "schooladmin.new_admission";
        $this->module_title         = translation("new_admission");
        $this->theme_color          = theme_color();
        $this->module_icon    		= 'fa fa-child';
        $this->school_id          = \Session::get('school_id');
        $this->academic_year      = \Session::get('academic_year');
        $this->first_name = $this->last_name =$this->ip_address ='';
        $obj_data          = Sentinel::getUser();

        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
        }

        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
	}

   public function create()
   {

   		$arr_levels = [];

        $obj_levels = $this->CommonDataService->get_levels($this->academic_year);
                               
        if($obj_levels)
        {
            $arr_levels = $obj_levels->toArray();
        }

        $arr_boards = [];
        $obj_boards = $this->EducationalBoardModel->where('school_id',$this->school_id)->get();
        if($obj_boards)
        {
            $arr_boards = $obj_boards->toArray();
        }    

   		$this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['page_title']      = translation("add")." ".$this->module_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['arr_levels']      = $arr_levels;
        $this->arr_view_data['arr_boards']      = $arr_boards;
        

        return view($this->module_view_folder.'.create',$this->arr_view_data);
   }

    public function checkEmail(Request $request)
    {
    	$email = $request->input('email');
    	if($request->input('user_type') == "student")
    	{
            $flag = 'not_exist';
    		$user = $this->UserModel->where('email',$email)->first();
            if(count($user)>0){
                $flag = 'exist';
                $student = $this->StudentModel->where('user_id',$user->id)->where('has_left','<>',0)->count();
                if($student>0)
                {
                    $flag = 'not_exist';    
                }
            }
            if($flag=='exist'){
                return response()->json(array('status'=>'error','msg'=>translation('this_email_is_already_exist_for_this_school')));    
            }
            else{
                return response()->json(array('status'=>'success'));
            }
            
    	}
        if($request->input('user_type') == "parent")
        {
            $flag = 'not_exist';
        	$user = $this->UserModel->where('email',$email)->first();
            if(count($user)>0){
                if($user->id==1){
                    return response()->json(array('status'=>'error','msg'=>translation('this_email_is_already_exist_for_this_school')));
                }
                $student = $this->StudentModel
                                        ->where('user_id',$user->id)
                                        ->first();
                if(count($student)>0){
                    
                    return response()->json(array('status'=>'error','msg'=>translation('this_email_is_already_exist_for_this_school')));
                }
                else{
                    $flag = 'exist';
                    $data = [];
                    $data['first_name'] = isset($user->first_name) ? $user->first_name : '';
                    $data['last_name'] = isset($user->last_name) ? $user->last_name : '';
                    $data['mobile_no'] = isset($user->mobile_no) ? $user->mobile_no : '';
                    $data['national_id'] = isset($user->national_id) ? $user->national_id : '';    
                }	
            }
            if($flag=='exist'){
                return response()->json(array('status'=>'exist','data'=>$data));    
            }
            else{
                return response()->json(array('status'=>'not_exist'));
            }
        }    
    }

    public function get_classes(Request $request)
    {
    	$obj = $this->CommonDataService->get_class($request->level);

    	$options = '';							  
  		if($obj)
  		{
  			$arr = $obj->toArray();
  			if(count($arr)>0)
  			{
  				foreach($arr as $value)
  				{
  					$options .= '<option value='.$value['id'].'>'.$value['class_details']['class_name'].'</option>';
  				}
  			}
  		}
  		return $options;  								  
    }

    public function store_admission(Request $request)
    {
    	$arr_rules  =   $messages = [];       
        $arr_data   =   $this->LanguageService->get_all_language();

        $arr_rules['email']              = 'required|email';
        $arr_rules['mobile_no']          = 'required|numeric|digits_between:10,14';
        $arr_rules['national_id']        = ['required','regex:/^[a-zA-Z0-9]*$/'];
        $arr_rules['birth_date']         = 'required|date|before:tomorrow';
        $arr_rules['admission_date']     = 'required|date';
        $arr_rules['address']            = 'required';
        $arr_rules['telephone_no']       = 'required|numeric|digits_between:6,14';
        $arr_rules['parent_national_id'] = ['required','regex:/^[a-zA-Z0-9]*$/'];
        $arr_rules['parent_email']       = 'required|email';
        $arr_rules['parent_mobile_no']   = 'required|numeric';
        $arr_rules['gender']             = 'required|alpha';
        $arr_rules['admission_number']   = ['required','regex:/^[A-Z0-9 \-]+$/'];
        $arr_rules['previous_level']     = 'numeric';
        $arr_rules['level']              = 'required|numeric';
        $arr_rules['class']              = 'required|numeric';
        /*$arr_rules['education_board']    = 'required|numeric';*/
        $arr_rules['relation']           = 'required|alpha';
        $arr_rules['first_name']         = ['required','regex:/^[a-zA-Z ]+$/'];
        $arr_rules['last_name']          = ['required','regex:/^[a-zA-Z ]+$/'];
        $arr_rules['parent_first_name']  = ['required','regex:/^[a-zA-Z ]+$/'];
        $arr_rules['parent_last_name']   = ['required','regex:/^[a-zA-Z ]+$/'];
        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'mobile_no.digits_between' => translation('please_enter_mobile_no_within_range_of_10_14'),
                            'telephone_no.digits_between'  => translation('please_enter_telephone_no_within_range_of_6_14'),
                            'required'             => translation('this_field_is_required'),
                            'digits'               => translation('please_enter_digits_only')  

                        );
        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 

            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        $email = $request->email;
        $parent_email = $request->parent_email;
        $national_id = $request->national_id;
        $parent_national_id = $request->parent_national_id;

        if($email==$parent_email)
        {
            Flash::error(translation("both_email_must_be_different"));
            return redirect()->back();
        }
        /***************** student email existence********************/

        $student_exist = false;
        $flag = 'not_exist';
        $user = $this->UserModel->where('email',$email)->first();
        if(count($user)>0){
            $flag = 'exist';
            $student_count = $this->StudentModel->where('user_id',$user->id)->where('has_left','<>',0)->count();
            if($student_count>0)
            {
                $flag = 'not_exist';    
                $student_exist = true;
            }
        }    
        if($flag=='exist'){
            Flash::error(translation('this_email_is_already_exist_for_this_school'));
            return redirect()->back();
        }

        /***************** student email existence********************/

        $discount=0;
        /***************** parent email existence ********************/
        $exist = $this->UserModel->where('email',$parent_email)->first();
        
        if(count($exist)>0){

            if($exist->id==1){
                Flash::error(translation('this_email_is_already_exist_for_this_school'));
                return redirect()->back();
            }
            else{
                // email is already in use
                $roles = Sentinel::findById($exist->id)->roles;
                
                $arr_roles=[];
                foreach($roles as $role){
                    array_push($arr_roles,$role->slug);
                }
                
                //if that already exist user is student
                if(in_array(config('app.project.role_slug.student_role_slug'), $arr_roles))
                {
                    Flash::error(translation('this_email_is_already_exist_for_this_school'));
                    return redirect()->back();
                }
            }
            $count = $request->input('count');
            $kid_count = 1;
            
            while($count>=0)
            {
                if($request->has('kid_national_id_'.$count))
                {
                    $kid_national_id = $request->input('kid_national_id_'.$count);
                    $kid_exist = $this->StudentModel
                                    ->where('parent_id',$exist->id)
                                    ->where('student_no',$kid_national_id)
                                    ->where('school_id',$this->school_id)
                                    ->where('academic_year_id',$this->academic_year)
                                    ->where('has_left',0)
                                    ->count();
                    if($kid_exist){
                        $kid_count++;    
                    }
                }
                $count--;
            }
            
            $max_kids = $this->BrotherhoodModel->where('school_id',$this->school_id)->max('kid_no');

            if($max_kids<=$kid_count && $max_kids!=0)
            {
                $discount=$this->BrotherhoodModel->where('kid_no',$max_kids)->where('school_id',$this->school_id)->first(); 
                $discount = isset($discount->id) ?  $discount->id :0;   
            }
            elseif($max_kids!=0)
            {
                $discount=$this->BrotherhoodModel->where('kid_no',$kid_count)->where('school_id',$this->school_id)->first();    
                $discount = isset($discount->id) ?  $discount->id :0;
            }

        }
        
        /***************** parent email existence ********************/           

        
        $pickup_address = $drop_address = $pickup_location=$drop_location="";

            if($request->has('bus_transport') && $request->input('bus_transport')=='yes')
            {
                if($request->has('pickup_latitude') && $request->has('pickup_longitude'))
                {
                    $pickup_location = array("latitude"=>$request->input('pickup_latitude'),
                                          "longitude"=>$request->input('pickup_longitude'));   
                    $pickup_location = json_encode($pickup_location); 

                    
                    $pickup_address = $request->input('pickup_address');
                }
                if($request->has('drop_latitude') && $request->has('drop_longitude'))
                {
                    $drop_location = array("latitude"=>$request->input('drop_latitude'),
                                          "longitude"=>$request->input('drop_longitude'));
                    $drop_location = json_encode($drop_location);        

                    $drop_address = $request->input('drop_address');
                }
            }
        $birth_date     =   $request->input('birth_date');
        $admission_date     =   $request->input('admission_date');
        $school_no      =   Session::get('school_id');    
        $student_no     =   $this->generate_student_no($school_no);    
        if(!$student_exist)
        {

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
                }
                else
                {
                    Flash::error(translation('invalid_file_type_while_creating').' '.str_singular($this->module_title));
                    return redirect()->back();
                }   
            }
            else
            {
                $file_name  =   '';
            }
            /************* Image Upload ends here ********************/
            
            
            $data           =   [];
            $password       = generate_password_reg('stud');
            $credentials    =   [
                                    'email'         =>  $request->input('email'),
                                    'password'      =>  $password,
                                    'telephone_no'  =>  $request->input('telephone_no'),
                                    'mobile_no'     =>  $request->input('mobile_no'),
                                    'address'       =>  $request->input('address'),
                                    'profile_image' =>  $file_name,
                                    'national_id'   =>  $request->input('national_id'),
                                    'birth_date'    =>  $birth_date,
                                    'gender'        =>  $request->input('gender'),
                                    'city'          =>  $request->input('city'),
                                    'country'       =>  $request->input('country'),
                                    'is_active'     =>  '1',
                                    'latitude'      =>  $request->input('latitude'),
                                    'longitude'     =>  $request->input('longitude'),
                                    'gender'     =>  $request->input('gender')

                                ];

            
                               

            $new_user = Sentinel::registerAndActivate($credentials);

            $role = Sentinel::findRoleBySlug(config('app.project.role_slug.student_role_slug'));
        
            $role->users()->attach($new_user);

            $user_id = $new_user->id; 

            $student_details = [];
            $student_details['school_id']             =   $this->school_id;
            $student_details['user_id']               =   $user_id;
            $student_details['student_no']            =   $student_no;
            $student_details['parent_national_id']    =   $request->input('parent_national_id');
            $student_details['admission_date']          =   $admission_date;
            $student_details['admission_no']          =   $request->input('admission_number');
            $student_details['educational_board']     =   $request->input('education_board');
            $student_details['bus_transport']         =   ($request->input('bus_transport') == 'yes') ? 1 : 0;
            $student_details['pickup_location']       =   $pickup_location;
            $student_details['drop_location']         =   $drop_location;
            $student_details['pickup_address']        =   $pickup_address;
            $student_details['drop_address']          =   $drop_address;
            $student_details['previous_level']        =   $request->input('previous_level');
            $student_details['level_class_id']        =   $request->input('class');
            $student_details['academic_year_id']      =   \Session::get('academic_year');
            $student_details['language']              =   'en';
            $student_details['parent_id']             =   ($request->input('parent_id')!="") ? $request->input('parent_id') :0;
            $student_details['relation']              =   $request->input('relation');
            if($discount)
            {
               $student_details['brotherhood_id']  =   isset($discount)? $discount :0;             
            }

            $this->StudentModel->create($student_details);
            
            $student_id = isset($new_user->id) ? $new_user->id : 0 ;
            if($new_user)
            {
                /* update record into translation table */
               
                if(sizeof($arr_data) > 0 )
                {
                    foreach ($arr_data as $lang) 
                    {            
                        
                        $first_name       = $request->input('first_name');
                        $last_name        = $request->input('last_name');
                        $special_note        = $request->input('special_note');
                        if( (isset($first_name)  && $first_name != '') && (isset($last_name) && $last_name != ''))
                        { 
                            $translation = $new_user->translateOrNew($lang['locale']);
                            $translation->first_name    = $first_name;
                            $translation->last_name     = $last_name;
                            $translation->special_note  = $special_note;
                            $translation->user_id       = $new_user->id;
                            $translation->locale        = $lang['locale'];
                            $translation->save();
                        }
                    }
                } 
               /*------------------------------------------------------*/
            }

            $data          = [
                                'first_name'    =>  $request->input('first_name'),
                                'last_name'     =>  $request->input('last_name'),
                                'password'      =>  'admin@123',
                                'email'         =>  $request->input('email')
                             ];

            $arr_mail_data = $this->built_mail_data($data); 
            $this->EmailService->send_mail($arr_mail_data); 
        }
        else{

            $student_details = [];
            $student_details['school_id']             =   $this->school_id;
            $student_details['user_id']               =   $user->id;
            $student_details['student_no']            =   $student_no;
            $student_details['parent_national_id']    =   $request->input('parent_national_id');
            $student_details['admission_date']        =   $admission_date;
            $student_details['admission_no']          =   $request->input('admission_number');
            $student_details['educational_board']     =   $request->input('education_board');
            $student_details['bus_transport']         =   ($request->input('bus_transport') == 'yes') ? 1 : 0;
            $student_details['pickup_location']       =   $pickup_location;
            $student_details['drop_location']         =   $drop_location;
            $student_details['pickup_address']        =   $pickup_address;
            $student_details['drop_address']          =   $drop_address;
            $student_details['level_class_id']        =   $request->input('class');
            $student_details['academic_year_id']      =   \Session::get('academic_year');
            $student_details['language']              =   'en';
            $student_details['parent_id']             =   ($request->input('parent_id')!="") ? $request->input('parent_id') :0;
            $student_details['relation']              =   $request->input('relation');
            if($discount)
            {
               $student_details['brotherhood_id']  =   isset($discount)? $discount :0;             
            }

            $this->StudentModel->create($student_details);

            $data          = [
                                'first_name'    =>  $request->input('first_name'),
                                'last_name'     =>  $request->input('last_name'),
                                'password'      =>  'admin@123',
                                'email'         =>  $request->input('email')
                             ];

            $arr_mail_data = $this->built_mail_data($data); 
            $this->EmailService->send_mail($arr_mail_data); 
        }
        /******  Parent email existence ***********************/
        $exist = $this->UserModel->where('email',$parent_email)->first();
        if(count($exist)>0){

                // email is already in use
                $roles = Sentinel::findById($exist->id)->roles;
                $arr_roles=[];
                foreach($roles as $role){
                    array_push($arr_roles,$role->slug);
                }
                
                //if that already exist user is parent
                if(in_array(config('app.project.role_slug.parent_role_slug'), $arr_roles))
                {
                    $parent = $this->SchoolParentModel->where('parent_id',$exist->id)->first();
                    if(count($parent)>0){

                        if($parent->school_id!=$this->school_id){
                            //if parent is not in the same school
                            $arr_data = [];
                            $arr_data['school_id'] = $this->school_id;
                            $arr_data['parent_id'] = $exist->id;
                            $arr_data['is_active'] = 1;
                            $school_parent['language']  = 'en';
                            $this->SchoolParentModel->create($arr_data);
                        }
                    }
                }
                else
                {   
                    $parent_no    =   $this->generate_parent_no($this->school_id);
                    $parent_details = [];
                    $parent_details['user_id']               =   $exist->id;
                    $parent_details['parent_no']             =   $parent_no;
                    
                    $this->ParentModel->create($parent_details);
                    
                    $school_parent = [];
                    $school_parent['school_id'] = $this->school_id;
                    $school_parent['parent_id'] = $exist->id;
                    $school_parent['is_active'] = '1';
                    $school_parent['language']  = 'en';

                    $this->SchoolParentModel->create($school_parent);

                    $role = Sentinel::findRoleBySlug(config('app.project.role_slug.parent_role_slug'));
                
                    $role->users()->attach($exist->id);
                }
                
                $this->StudentModel->where('user_id',$student_id)
                                ->update(array('parent_id'=>$exist->id));

                $data          = [
                                'first_name'    =>  $request->input('parent_first_name'),
                                'last_name'     =>  $request->input('parent_last_name'),
                                'password'      =>  'continue with previous password',
                                'email'         =>  $request->input('parent_email')
                             ];

                $arr_mail_data = $this->built_mail_data($data); 
                $this->EmailService->send_mail($arr_mail_data);
              
        }
        else{
            //there is no user with this email ID
            $credentials    =   [
                                        'email'         =>  $request->input('parent_email'),
                                        'password'      =>  'admin@123',    
                                        'mobile_no'     =>  $request->input('parent_mobile_no'),
                                        'national_id'     =>  $request->input('parent_national_id'),
                                    ];
        
            
                $parent = Sentinel::registerAndActivate($credentials);
                
                $role = Sentinel::findRoleBySlug(config('app.project.role_slug.parent_role_slug'));
                
                $role->users()->attach($parent);
        
                $parent_no    =   $this->generate_parent_no($this->school_id);
                $parent_details = [];
                $parent_details['user_id']               =   $parent->id;
                $parent_details['parent_no']             =   $parent_no;
                
                $this->ParentModel->create($parent_details);
                
                $school_parent = [];
                $school_parent['school_id'] = $this->school_id;
                $school_parent['parent_id'] = $parent->id;
                $school_parent['is_active'] = '1';
                $school_parent['language']  = 'en';

                $this->SchoolParentModel->create($school_parent);
                if($parent)
                {
                    /* update record into translation table */
                    
                    if(sizeof($arr_data) > 0 )
                    {
                        foreach ($arr_data as $lang) 
                        {            
                            $arr_data = array();
                            $first_name       = $request->input('parent_first_name');
                            $last_name        = $request->input('parent_last_name');
                            if( (isset($first_name)  && $first_name != '') && (isset($last_name) && $last_name != ''))
                            { 
                                $translation = $parent->translateOrNew($lang['locale']);
                                $translation->first_name    = $first_name;
                                $translation->last_name     = $last_name;
                                $translation->user_id       = $parent->id;
                                $translation->locale        = $lang['locale'];
                                $translation->save();
                                
                            }
                        }
                    } 
                   /*------------------------------------------------------*/
                }
        
                $data          = [
                                    'first_name'    =>  $request->Input('parent_first_name'),
                                    'last_name'     =>  $request->Input('parent_last_name'),
                                    'password'      =>  'admin@123',
                                    'email'         =>  $request->Input('parent_email')
                                 ];
        
                $arr_mail_data = $this->built_mail_data_parent($data); 
                $this->EmailService->send_mail($arr_mail_data); 

                $this->StudentModel->where('user_id',$student_id)
                                   ->update(array('parent_id'=>$parent->id));
        }
        /****** Parent email existence ************************/
        Flash::success(translation('student_added_successfully'));
        return redirect()->back();
    }
    public function generate_student_no($school_id)
    {   

        $new_number = rand(0,99999);
        $new_number = str_pad($new_number,5,'0',STR_PAD_LEFT);
        $student_no  =   'ST'.strtoupper(substr($school_id,2,3)).$new_number;  
        
        $exist = $this->StudentModel->where('student_no',$student_no)->first();
        if($exist)
        {
            $student_no = $this->generate_student_no($school_id);
        }
        
        return  $student_no;
    }
    public function generate_parent_no($school_id)
    {   
        $new_number = rand(0,99999);
        $new_number = str_pad($new_number,5,'0',STR_PAD_LEFT);
        $parent_no  =   'PA'.strtoupper(substr($school_id,2,3)).$new_number;
        
        $exist = $this->ParentModel->where('parent_no',$parent_no)->first();
        if($exist)
        {
            $parent_no = $this->generate_parent_no($school_id);
        }
        
        return  $parent_no;
    }

    

    public function get_parent_details(Request $request)
    {
        $data = "";
        $arr_lang       = $this->LanguageService->get_all_language(); 
        if($request->has('user_type') && $request->input('user_type')=="parent")
        {
            $parent_national_id = $request->input('national_id');
            $result = $this->UserModel
                                      ->where('national_id',$parent_national_id)
                                      ->first();

            if($result)
            {
                $flag=0;
                $result = $result->toArray();
                if(count($result)>0)
                {

                    if( ($this->StudentModel->where(['user_id'=>$result['id']])->count()) >0)
                    {
                        $flag=0;
                    }
                    if($result['id']==1)
                    {
                        $flag=0;
                    }
                    else{

                        $flag=1;
                    }
                    if($flag==1)
                    {
                        $data = array();
                        $data["first_name"] = isset($result['first_name']) ? $result['first_name'] :'' ; 
                        $data["last_name"] = isset($result['last_name']) ? $result['last_name'] : ''; 
                        $data["email"] =  $result['email'];
                        $data["mobile_no"] = $result['mobile_no'];
                        $data["parent_id"] = $result['id'];        
                    }
                    
                }
            }                                                                                                                                                             
        }
       
        if($request->has('user_type') && $request->input('user_type')=="student")
    	{
            $student_national_id = $request->input('national_id');   

                $obj_student = $this->StudentModel
                            ->with(['get_user_details'=>function($q){
                                $q->select('id','email','address','city','country','national_id','birth_date','gender','mobile_no','telephone_no');
                                
                            },'get_parent_details'=>function($q){
                                $q->select('id','email','address','city','country','national_id','birth_date','gender','mobile_no','telephone_no');
                                
                        
                            },'get_level_class'])
                            ->whereHas('get_user_details',function($q)use($student_national_id){
                                $q->where('national_id',$student_national_id);
                            })
                            ->where('school_id',$this->school_id)
                            ->first();
            
                if(!empty($obj_student))
                {    
                    $result = $obj_student->toArray();
                    
                    $data = array();  

                    $data["first_name"] = isset($result['get_user_details']['first_name']) ? $result['get_user_details']['first_name'] : ''; 
                    $data["last_name"] = isset($result['get_user_details']['last_name']) ? $result['get_user_details']['last_name'] : '';
                    $data["parent_first_name"] = isset($result['get_parent_details']['first_name']) ? $result['get_parent_details']['first_name'] : ''; 
                    $data["parent_last_name"] = isset($result['get_parent_details']['last_name']) ? $result['get_parent_details']['last_name'] : ''; 
                    $data["special_note"] = isset($result['get_user_details']['special_note']) ? $result['get_user_details']['special_note'] : '';  

                    
                    $data["student_id"] = isset($result['id']) ? $result['id'] :'' ;
                    $data["user_id"] = isset($result['user_id']) ? $result['user_id'] :'' ;
                    $data["parent_national_id"] = isset($result['parent_national_id']) ? $result['parent_national_id'] :'' ;
                    $data["image"] = isset($result['profile_image']) && $result['profile_image']!='' ? $this->user_profile_public_img_path.$result['profile_image'] :'' ;
                    $data["national_id"] = isset($result['national_id']) ? $result['national_id'] :'' ;
                    $data["student_email"] = isset($result['get_user_details']['email']) ? $result['get_user_details']['email'] :'' ;
                    $data["student_mobile_no"] = isset($result['get_user_details']['mobile_no']) ? $result['get_user_details']['mobile_no'] : '';
                    $data["address"] = isset($result['get_user_details']['address']) ? $result['get_user_details']['address'] :'' ;
                    $data["city"] = isset($result['get_user_details']['city']) ? $result['get_user_details']['city'] :'' ;
                    $data["country"] = isset($result['get_user_details']['country']) ? $result['get_user_details']['country'] :'' ;
                    $data["relation"] = isset($result['relation']) ? $result['relation'] :'' ;

                    $birth_date ='';
                    if(isset($result['get_user_details']['birth_date'])  && $result['get_user_details']['birth_date']!="0000-00-00")
                    {
                        $birth_date = $result['get_user_details']['birth_date'];
                        $birth_date = date_create($birth_date);
                        $birth_date = date_format($birth_date,'m/d/Y');
                    }   


                    $data["birth_date"] = isset($result['get_user_details']['birth_date']) ? $result['get_user_details']['birth_date'] :'' ;
                    $data["gender"] = isset($result['get_user_details']['gender']) ? $result['get_user_details']['gender'] :'' ;
                    $data["telephone_no"] = isset($result['get_user_details']['telephone_no']) ? $result['get_user_details']['telephone_no'] :'' ;
                    $data["parent_id"] = isset($result['parent_id']) ? $result['parent_id'] : '';
                    $data["level"] = isset($result['get_level_class']['level_id']) ? $result['get_level_class']['level_id'] : '';
                    $data["class"] = isset($result['get_level_class']['id']) ? $result['get_level_class']['id'] : '';
                    $data["bus_transport"] = isset($result['bus_transport']) ? $result['bus_transport'] : '';
                    $data["pickup_location"] = isset($result['pickup_location']) ? $result['pickup_location'] : '';
                    $data["drop_location"] = isset($result['drop_location']) ? $result['drop_location'] : '';
                    $data["parent_email"] = isset($result['get_parent_details']['email']) ? $result['get_parent_details']['email'] : '';
                    $data["parent_mobile_no"] = isset($result['get_parent_details']['mobile_no']) ? $result['get_parent_details']['mobile_no'] : '';
                    $data["pickup_address"] = isset($result['pickup_address']) ? $result['pickup_address'] : '';
                    $data["drop_address"] = isset($result['drop_address']) ? $result['drop_address'] : '';
                    $data["pickup_latitude"] = '';
                    $data["pickup_longitude"] = '';
                    $data["drop_latitude"] = '';
                    $data["drop_latitude"] = '';

                    if($data["pickup_location"]!='')
                    {
                        $pickup = json_decode($data["pickup_location"]);
                        
                        $data["pickup_latitude"] = $pickup->latitude;
                        $data["pickup_longitude"] = $pickup->longitude;
                    }
                    if($data["drop_location"]!='')
                    {
                        $pickup = json_decode($data["drop_location"]);
                        $data["drop_latitude"] = $pickup->latitude;
                        $data["drop_longitude"] = $pickup->longitude;
                    }   

                    $options = '';
                    if(isset($result['get_level_class']['level_id']))
                    {
                        $obj = $this->LevelClassModel
                                        ->with('class_details')
                                        ->where('school_id',$this->school_id)
                                        ->where('level_id',$result['get_level_class']['level_id'])
                                        ->get();

                                                    
                        if($obj)
                        {
                            $arr = $obj->toArray();
                            if(count($arr)>0)
                            {
                                foreach($arr as $value)
                                {
                                    $options .= '<option value='.$value['id'].'>'.$value['class_details']['class_name'].'</option>';
                                }
                            }
                        }
                    }
                    $data["options"] = $options;

                }            
            
            
        }
 		return $data;	
    }
    public function built_mail_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $login_url = '<p class="email-button"><a target="_blank" href="'.\URL::to('/school_admin').'">Click Here</a></p><br/>' ;

            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'LAST_NAME'        => $arr_data['last_name'],
                                  'EMAIL'            => $arr_data['email'],
                                  'PASSWORD'         => $arr_data['password'],
                                  'URL'              => $login_url,
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                  'PROJECT_NAME'     => config('app.project.name')];


            $arr_mail_data                      = [];
            $arr_mail_data['email_template_slug'] = 'student_registration';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_mail_data_parent($arr_data)
    {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $login_url = '<p class="email-button"><a target="_blank" href="'.\URL::to('/school_admin').'">Click Here</a></p><br/>' ;

            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'LAST_NAME'        => $arr_data['last_name'],
                                  'EMAIL'            => $arr_data['email'],
                                  'PASSWORD'         => $arr_data['password'],
                                  'URL'              => $login_url,
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                  'PROJECT_NAME'     => config('app.project.name')];


            $arr_mail_data                      = [];
            $arr_mail_data['email_template_slug'] = 'parent_registration';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }
    public function  generate_admission_number(){

        $str= "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str= str_shuffle($str);
        
        $school_name = $this->CommonDataService->get_school_name();
        $school_name = str_replace('&','',$school_name);
        $school_name = str_replace('  ',' ',$school_name);
        $school_name = explode(' ',$school_name);
        $school_abbr='';

        foreach($school_name as $value)
            $school_abbr .= isset($value[0])?$value[0]:'school';
        

        $new_number =  strtoupper($school_abbr).'-'.date('Y').'-'.substr($str,0,7);
        
        $exist = $this->StudentModel->where('admission_no',$new_number)->first();
        if($exist)
        {
            return $this->generate_admission_number();
        }
        
        return  $new_number;
    }	

    public function check_admission_no_exist($number){

        $exist = $this->StudentModel->where('admission_no',$number)->first();
        if($exist)
        {
            return 'error';
        }
        
        return  'success';
    }
    public function checkBrotherhood(Request $request){
        if(!$request->has('number')){
            return 'error';            
        }
        else{
            $national_id = $request->parent_national_id;
            $student_number = $request->number;
            $parent = $this->UserModel->where('national_id',$national_id)->first();
            if(isset($parent->user_id)){
                $kid_exist = $this->StudentModel
                                        ->where('parent_id',$parent->user_id)
                                        ->where('student_no',$student_number)
                                        ->where('school_id',$this->school_id)
                                        ->where('academic_year_id',$this->academic_year)
                                        ->where('has_left',0)
                                        ->count();
                if($kid_exist){
                    return 'success';    
                }                        
            }    
        }
        return 'error';
    } 
}
