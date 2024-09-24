<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;

use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\EmployeeModel;
use App\Models\UserRoleModel;   
use App\Models\RoleModel;
use App\Models\SchoolRoleModel;
use App\Models\ActivationModel;
use App\Models\ParentModel;
use App\Models\AcademicYearModel;  
/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
/*Activity Log */
use App\Common\Services\LanguageService;
use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;

use App\Models\SchoolProfileModel;
use App\Models\SchoolTemplateTranslationModel;
use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;
use PDF;

class DriverController extends Controller
{
    use MultiActionTrait;
    public function __construct(    
                                    UserModel $user,
                                    UserTranslationModel $translation,
                                    UserRoleModel $user_role_model,
                                    RoleModel $role_model,
                                    SchoolRoleModel $role,
                                    ActivityLogsModel $activity_logs,
                                    EmployeeModel $employee,
                                    EmailService $mail_service,
                                    LanguageService $language,
                                    ParentModel $parent,
                                    SchoolProfileModel $profile,
                                    SchoolTemplateTranslationModel $template,
                                    AcademicYearModel $year,
                                    CommonDataService $CommonDataService

                                )
    {
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->SchoolRoleModel              = $role;
        $this->EmployeeModel                = $employee;
        $this->AcademicYearModel            = $year;
        $this->EmailService                 = $mail_service;
        $this->BaseModel                    = $employee;
        $this->ActivityLogsModel            = $activity_logs; /* Activity Model */   
        $this->LanguageService              = $language;  
        $this->ParentModel                  = $parent;
        $this->CommonDataService            = $CommonDataService;

        $this->SchoolProfileModel           = $profile;
        $this->SchoolTemplateTranslationModel = $template;
        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/driver';
        
        $this->module_title                 = translation("driver");
        $this->modyle_url_slug              = translation("driver");

        $this->module_view_folder           = "schooladmin.driver";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-user';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->first_name = $this->last_name =$this->ip_address ='';

        $this->arr_view_data['page_title']  = translation("driver");

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
        }
        /* Activity Section */

    } 

    public function index(Request $request)
    {   
        
        $this->arr_view_data['role']            = 'driver';
        $this->arr_view_data['module_title']    = ucfirst(translation("manage"))." ".strtolower(str_plural($this->module_title));
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    function get_users_details(Request $request,$type,$fun_type='')
    {
        $role ='driver';
        $school_id =$this->school_id;
       
        $locale = '';

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }

        $start_id    = $this->AcademicYearModel
                              ->where(['start_date'=>($this->AcademicYearModel
                                                           ->where('school_id',$this->school_id)
                                                           ->min('start_date'))
                                      ])
                              ->first(['id']); 

        $user_details                 = $this->UserModel->getTable();
        $prefixed_user_details        = DB::getTablePrefix().$this->UserModel->getTable();

        $user_trans_table             = $this->UserTranslationModel->getTable();                  
        $prefixed_user_trans_table    = DB::getTablePrefix().$this->UserTranslationModel->getTable();

        $employee                     = $this->EmployeeModel->getTable();       
        $prefixed_employee_table      =  DB::getTablePrefix().$this->EmployeeModel->getTable();       
       
        $obj_user = DB::table($employee)
                                ->select(DB::raw($employee.".id as id,".
                                                 $prefixed_user_details.".email as email, ".
                                                 $employee.".is_active as is_active, ".
                                                 $prefixed_user_details.".national_id as national_id, ".
                                                 $prefixed_user_details.".gender as gender, ".
                                                 $prefixed_employee_table.".user_role as role,".
                                                 $prefixed_employee_table.".license_no as license_no,".
                                                 $prefixed_employee_table.".employee_no as employee_no,".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
                                                          .$prefixed_user_trans_table.".last_name) as user_name"
                                                 ))
                                ->whereNull($employee.'.deleted_at')
                                ->join($user_details,$employee.'.user_id','=',$user_details.'.id')
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                ->where($employee.'.user_role','=',$role)
                                ->where($user_trans_table.'.locale','=',$locale)
                                ->where($employee.'.school_id','=',$school_id)
                                ->whereBetween($employee.'.academic_year_id',[$start_id->id,$this->academic_year])
                                ->orderBy($employee.'.created_at','DESC');

       
        

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
                                 ->orWhereRaw("(".$prefixed_user_details.".national_id LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$prefixed_employee_table.".license_no LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$prefixed_employee_table.".employee_no LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$prefixed_user_details.".gender LIKE '%".$search_term."%') ")
                                 ->orWhereRaw(" ( CONCAT(".$prefixed_user_trans_table.".first_name,'',".$prefixed_user_trans_table.".last_name)  LIKE  '%".$search_term."%' ) )");
                                 
        }
        if($fun_type=="export"){

            return $obj_user->get();
        }else{
            return $obj_user;
        }
        
    }

    public function get_records(Request $request,$type)
    {
       
        $role = Sentinel::findRoleBySlug(config('app.project.role_slug.school_admin_role_slug'));
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;
       
        $obj_user        = $this->get_users_details($request,$type);

        $role = $type;

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('users.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data) 
                            {
                                return base64_encode($data->id);
                            });
                            
        }                    

        $json_result     = $json_result->editColumn('license_no',function($data)
                            { 
                                 
                                if($data->license_no!=null && $data->license_no!=''){

                                    return  $data->license_no;
                                }else{
                                    return  '-';
                                }

                            })
                            ->editColumn('employee_no',function($data)
                            { 
                                 
                                if($data->employee_no!=null && $data->employee_no!=''){

                                    return  $data->employee_no;
                                }else{
                                    return  '-';
                                }

                            })
                            ->editColumn('national_id',function($data)
                            { 
                                 
                                if($data->national_id!=null && $data->national_id!=''){

                                    return  $data->national_id;
                                }else{
                                    return  '-';
                                }

                            })
                            ->editColumn('user_name',function($data)
                            {   
                                if($data->user_name!=null && $data->user_name!=''){

                                    return  ucfirst($data->user_name);
                                }else{
                                    return  '-';
                                }
                            })
                            ->editColumn('build_action_btn',function($data) use ($role,$arr_current_user_access)
                            {
                                if($role != null)
                                {  
                                    if(array_key_exists('employee.update',$arr_current_user_access))
                                    {     
                                        $build_edit_action = $build_status_btn = $build_view_action = $build_delete_action ='';
     
                                        $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                        $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>';

                                        $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                        $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';

                                        if($data->is_active != null && $data->is_active == "0")
                                        {   
                                            $build_status_btn = '<a class="blue-color" title="'.translation('activate').'" href="'.$this->module_url_path.'/activate/'.base64_encode($data->id).'" 
                                            onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-lock"></i></a>';
                                        }
                                        elseif($data->is_active != null && $data->is_active == "1")
                                        {
                                            $build_status_btn = '<a class="light-blue-color" title="'.translation('deactivate').'" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-unlock"></i></a>';
                                        }
                                    }
                                    if(array_key_exists('employee.delete',$arr_current_user_access))
                                    {
                                        $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                        $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                    }
                                }
                                return $build_view_action.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_status_btn.'&nbsp;'.$build_delete_action;
                            })
                            ->editColumn('build_checkbox',function($data)use($arr_current_user_access){
                                $build_checkbox ='';
                                if(array_key_exists('employee.update',$arr_current_user_access) || array_key_exists('employee.delete',$arr_current_user_access))
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
        $id       = base64_decode($enc_id);
        if(is_numeric($id)){
            $employee_details =   $this->EmployeeModel
                               ->with('user_details') 
                               ->where('id',$id)
                               ->first();

            $arr_employee_details = [];
            if(isset($employee_details) && count($employee_details)>0)
            {
                $arr_employee_details = $employee_details->toArray();
                $arr_data = [];  
                $arr_data    = $arr_employee_details;

                $this->arr_view_data['role']                         = 'driver';
                $this->arr_view_data['module_title']                 = translation("view")." ".strtolower($this->module_title);
                $this->arr_view_data['module_url_path']              = $this->module_url_path;
                $this->arr_view_data['arr_data']                     = $arr_data;
                $this->arr_view_data['module_icon']                  = $this->module_icon;
                $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
                $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;
                $this->arr_view_data['theme_color']                  = $this->theme_color;
                
                return view($this->module_view_folder.'.view', $this->arr_view_data);
            }
            else
            {
                Flash::error(translation('no_data_available'));
                return redirect()->back();
            }
           
            
        }
        else{
            Flash::error(translation("something_went_wrong")); 
            return redirect($this->module_url_path);
        }
    }

    public function create()
    {
        
        $this->arr_view_data['role']            = 'professor';
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['module_title']    = translation("add")." ".strtolower($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['create_icon']     = $this->create_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.create', $this->arr_view_data);        
    }

    public function store(Request $request)
    {
        $arr_rules  =   $messages = [];
        $arr_data   =   $this->LanguageService->get_all_language();
        
        $arr_rules['email']                = 'required|email';
        $arr_rules['mobile_no']            = 'required|numeric|digits_between:10,14';
        $arr_rules['national_id']          = ['required','regex:/^[a-zA-Z0-9]*$/'];
        $arr_rules['birth_date']           = 'required';
        $arr_rules['gender']               = 'required';
        $arr_rules['address']              = 'required';
        $arr_rules['year_of_experience']   = 'required|numeric|digits_between:1,2';
        $arr_rules['telephone_no']         = 'required|numeric|digits_between:6,14';
        $arr_rules['qualification_degree'] = ['required','regex:/^[a-zA-Z0-9 \,]*$/'];
        $arr_rules['first_name']           = ['required','regex:/^[a-zA-Z ]*$/'];
        $arr_rules['last_name']            = ['required','regex:/^[a-zA-Z ]*$/'];
        $arr_rules['license_no']           = ['required','regex:/^[a-zA-Z0-9 ]*$/'];

        $messages = array(
                        'regex'                => translation('please_enter_valid_text_format'),
                        'email'                => translation('please_enter_valid_email'),
                        'numeric'              => translation('please_enter_digits_only'),
                        'mobile_no.digits_between' => translation('please_enter_mobile_no_within_range_of_10_14'),
                        'telephone_no.digits_between'  => translation('please_enter_telephone_no_within_range_of_6_14'),
                        'required'             => translation('this_field_is_required'),
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
        $employee_no    =   $this->generate_employee_no($school_no);
        $data           =   [];

        $email  =   $request->input('email');
        
        if($email != $this->email)
        {
            $users =    $this->UserModel->where('email',$email)
                                    ->whereHas('professor_details',function($query) {
                                        $query->where('school_id',$this->school_id)
                                              ->where('has_left',0);
                        })
                        ->first();

            if(!$users)
            {
                $employee   =    $this->UserModel->where('email',$email)
                                        ->whereHas('employee_details',function($query) {
                                            $query->where('school_id',$this->school_id)
                                                  ->where('has_left',0);
                                })
                                ->first();
            }
            if($users || $employee)
            {
                Flash::error(translation('this_email_is_already_exist_for_this_school'));
                return redirect()->back();
            }
        }
        else
        {
            Flash::error(translation('this_email_is_already_exist_for_this_school'));
            return redirect()->back();
        }

        $parent_details =   $this->ParentModel->whereHas('user_details',function($query) use($email){
                                                   $query->where('email',$email);
                                })->first();

        if($parent_details)
        {
            $type = 'driver';
            $user = Sentinel::findById($parent_details->user_id);
            
            $role = Sentinel::findRoleBySlug($type);
            $role->users()->attach($user);

            $employee_details = [];
            $employee_details['school_id']             =   $this->school_id;
            $employee_details['user_id']               =   $user->id;
            $employee_details['employee_no']           =   $employee_no;
            $employee_details['user_role']             =   $type;
            $employee_details['marital_status']        =   $request->input('status');
            $employee_details['year_of_experience']    =   $request->input('year_of_experience');
            $employee_details['qualification_degree']  =   $request->input('qualification_degree');
            $employee_details['license_no']            =   $request->input('license_no');
            $employee_details['academic_year_id']      =   $this->academic_year;
            
            $employee  = $this->EmployeeModel->create($employee_details);
            $data          = [
                    'first_name'    =>  trim($request->Input('first_name')),
                    'last_name'     =>  trim($request->Input('last_name')),
                    'password'      =>  'admin@123',
                    'email'         =>  $request->Input('email'),
                    'role'          =>  $type
                 ];
            $arr_mail_data = $this->built_mail_data($data); 
            $this->EmailService->send_mail($arr_mail_data); 

            if($user)
            {
                Flash::success(translation('driver_added_successfully'));
                return redirect()->back();
            }
            else
            {
                Flash::error(translation('problem_occured_while_adding_new_driver'));
                return redirect()->back();
            }
                
        }
           
        else
        {
            $credentials    =   [
                                    'email'         =>  trim($request->Input('email')),
                                    'password'      =>  'admin@123',
                                    'telephone_no'  =>  trim($request->Input('telephone_no')),
                                    'mobile_no'     =>  trim($request->Input('mobile_no')),
                                    'address'       =>  trim($request->Input('address')),
                                    'profile_image' =>  $file_name,
                                    'national_id'   =>  $request->Input('national_id'),
                                    'birth_date'    =>  $request->input('birth_date'),
                                    'gender'        =>  $request->Input('gender'),
                                    'city'          =>  trim($request->Input('city')),
                                    'country'       =>  trim($request->Input('country')),
                                    'is_active'     =>  '1',
                                    'latitude'      =>  $request->Input('latitude'),
                                    'longitude'     =>  $request->Input('longitude')
                                ];

            $user = Sentinel::registerAndActivate($credentials);

            $type = 'driver';
            $role = Sentinel::findRoleBySlug($type);
            
            $role->users()->attach($user);

            $employee_details = [];
            $employee_details['school_id']             =   $this->school_id;
            $employee_details['user_id']               =   $user->id;
            $employee_details['employee_no']           =   trim($employee_no);
            $employee_details['user_role']             =   $type;
            $employee_details['marital_status']        =   trim($request->input('status'));
            $employee_details['year_of_experience']    =   trim($request->input('year_of_experience'));
            $employee_details['qualification_degree']  =   trim($request->input('qualification_degree'));
            $employee_details['license_no']            =   trim($request->input('license_no'));
            $employee_details['academic_year_id']      =   $this->academic_year;
            
            
            $employee  = $this->EmployeeModel->create($employee_details);

             $data          = [
                    'first_name'    =>  trim($request->Input('first_name')),
                    'last_name'     =>  trim($request->Input('last_name')),
                    'password'      =>  'admin@123',
                    'email'         =>  $request->Input('email'),
                    'role'          =>  $type
                 ];
            $arr_mail_data = $this->built_mail_data($data); 
            $email_status  = $this->EmailService->send_mail($arr_mail_data); 
            
            if($user)
            {
                /* update record into translation table */
                
                if(sizeof($arr_data) > 0 )
                {
                    foreach ($arr_data as $lang) 
                    {            
                        $arr_data = array();
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

            if($user)
            {

                Flash::success(translation('driver_added_successfully'));
                return redirect()->back();
            }
            else
            {

                Flash::error(translation('problem_occured_while_adding_new_driver'));
                return redirect()->back();
            }
        }
        
    }
    public function generate_employee_no($school_id)
    {   

        $employee_no  =   'EM'.strtoupper(substr($school_id,2,3)).rand(10000,99999); 
        
        $exist = $this->EmployeeModel->where('employee_no',$employee_no)->first();
        if($exist)
        {
            $employee_no = $this->generate_employee_no($school_id);
        }
        
        return  $employee_no;
    }

    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        
        if($email != $this->email)
        {
            $users =    $this->UserModel->where('email',$email)
                                    ->whereHas('professor_details',function($query){
                                        $query->where('school_id',$this->school_id)
                                              ->where('has_left',0);
                        })
                        ->first();

            if(!$users)
            {
                $employee   =    $this->UserModel->where('email',$email)
                                        ->whereHas('employee_details',function($query){
                                            $query->where('school_id',$this->school_id)
                                                  ->where('has_left',0);
                                })
                                ->first();
            }
            if($users || $employee)
            {
                return response()->json(array('status'=>'error','msg'=>translation('this_email_is_already_exist')));
            }
            else
            {
                return response()->json(array('status'=>'success'));
            }
        }
        else
        {
            return response()->json(array('status'=>'error','msg'=>translation('this_email_is_already_exist')));
        }
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
        $id       = base64_decode($enc_id);
        if(is_numeric($id)){
            $employee_details =   $this->EmployeeModel
                               ->with('get_user_details') 
                               ->where('id',$id)
                               ->first();

            $arr_employee_details = [];
            if($employee_details)
            {
                $arr_employee_details = $employee_details->toArray();
                $arr_data = []; 
                $arr_data    = $arr_employee_details;
                
                $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
                $this->arr_view_data['module_title']    = translation("edit")." ".strtolower($this->module_title);
                $this->arr_view_data['module_icon']     = $this->module_icon;
                $this->arr_view_data['edit_icon']       = $this->edit_icon;
                $this->arr_view_data['arr_data']        = $arr_data;
                $this->arr_view_data['id']              = $id;
                $this->arr_view_data['module_url_path'] = $this->module_url_path;
                $this->arr_view_data['theme_color']     = $this->theme_color;
                return view($this->module_view_folder.'.edit', $this->arr_view_data);
            }
            else{
                Flash::error(translation('no_data_available'));
                return redirect()->back();
            }    
        }
        else{

            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }
    }

    public function assign_role()
    {
        $locale = '';
        $employee = $role_name = [];

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }

        $obj_employee = $this->EmployeeModel
                             ->where('school_id',$this->school_id)
                             ->where('user_role','')
                             ->get()
                             ->toArray();

        $obj_role     = $this->SchoolRoleModel
                             ->where('school_id',$this->school_id)
                             ->get();
        
        if ($obj_role) {
            $arr_role = $obj_role->toArray();
            foreach ($arr_role as $key => $role) {
                $obj    =  $this->RoleModel
                                ->where('id',$role['role_id'])
                                ->where('is_approved','APPROVED')
                                ->first(['name','slug']);

                if ($obj) {
                    $role_name[$key] = $obj->toArray();
                }
            }
        }
       
        if($obj_employee)
        {
            foreach ($obj_employee as $key => $value) {
                $emp                = $this->UserTranslationModel
                                           ->where('user_id',$value['user_id'])
                                           ->where('locale',$locale)
                                           ->first(['first_name','last_name','user_id']);

                if($emp)
                {
                    $employee[$key] = $emp->toArray();
                }
            }
        }

        if($employee)
        {
            $this->arr_view_data['employee']    = $employee;
        }
        
        if($role_name)
        {
            $this->arr_view_data['role']    = $role_name;   
        }
        $page_title = translation("assign_role");
        $this->arr_view_data['page_title']      = $page_title;   
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['create_icon']     = 'fa fa-wrench';
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.assign_role', $this->arr_view_data);        
    }
    public function role_store(Request $request)
    {
        $arr_rules['emp_id']          =   'required';
        $arr_rules['role']            =   'required';

        $messages['required']    =   'This field is required';

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $role = Sentinel::findRoleBySlug($request->input('role'));
        
        $user = Sentinel::findById($request->input('emp_id'));
        
        $role->users()->attach($user);
        
        $data = [];
        $data['user_role']  =   $role->name;
        $employee = $this->EmployeeModel->where('user_id',$request->input('emp_id'))->update($data);

        if($employee)
        {

            Flash::success(translation('role_assigned_to_employee_successfully'));
            return redirect()->back();
        }
        else
        {

            Flash::error(translation('problem_occured_while_assigning_role_to_employee'));
            return redirect()->back();
        }

    }

    public function update(Request $request,$enc_id)
    {
        $arr_rules = array();
        $arr_lang   =   $this->LanguageService->get_all_language();
        
        $arr_rules['email']                = 'required|email';
        $arr_rules['mobile_no']            = 'required|numeric|digits_between:10,14';
        $arr_rules['national_id']          = ['required','regex:/^[a-zA-Z0-9]*$/'];
        $arr_rules['birth_date']           = 'required';
        $arr_rules['gender']               = 'required';
        $arr_rules['address']              = 'required';
        $arr_rules['year_of_experience']   = 'required|numeric|digits_between:1,2';
        $arr_rules['telephone_no']         = 'required|numeric|digits_between:6,14';
        $arr_rules['qualification_degree'] = ['required','regex:/^[a-zA-Z0-9 \,]*$/'];
        $arr_rules['first_name']           = ['required','regex:/^[a-zA-Z ]*$/'];
        $arr_rules['last_name']            = ['required','regex:/^[a-zA-Z ]*$/'];
        $arr_rules['license_no']           = ['required','regex:/^[a-zA-Z0-9 ]*$/'];

        $messages = array(
                        'regex'                => translation('please_enter_valid_text_format'),
                        'email'                => translation('please_enter_valid_email'),
                        'numeric'              => translation('please_enter_digits_only'),
                        'mobile_no.digits_between' => translation('please_enter_mobile_no_within_range_of_10_14'),
                        'telephone_no.digits_between'  => translation('please_enter_telephone_no_within_range_of_6_14'),
                        'required'             => translation('this_field_is_required'),
                        'year_of_experience.digits_between' => translation('year_of_experience_can_not_be_greater_than_2_digits')  

                    );

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $user_id = base64_decode($enc_id);
        
        
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
        $user_details = $this->EmployeeModel->where('id',$user_id)->first();

        $arr_data                   = [];
        $arr_data['profile_image']  = trim($file_name);
        $arr_data['latitude']       = $request->input('latitude');
        $arr_data['longitude']      = $request->input('longitude');
        $arr_data['telephone_no']   = trim($request->input('telephone_no'));
        $arr_data['national_id']    = trim($request->input('national_id'));    
        $arr_data['mobile_no']      = trim($request->input('mobile_no'));
        $arr_data['birth_date']     = trim($request->input('birth_date'));
        $arr_data['gender']         = trim($request->input('gender'));
        $arr_data['city']           = trim($request->input('city'));
        $arr_data['country']        = trim($request->input('country'));
        $arr_data['address']        = trim($request->input('address'));
        
        $obj_data = $this->UserModel->where('id',$user_details->user_id)->update($arr_data);
        
        $status = $this->UserModel->where('id',$user_details->user_id)->first(); 

        $employee_details = [];
        $employee_details['marital_status']        =   trim($request->input('status'));
        $employee_details['year_of_experience']    =   trim($request->input('year_of_experience'));
        $employee_details['qualification_degree']  =   trim($request->input('qualification_degree'));
        $employee_details['license_no']            =   trim($request->input('license_no'));

        //dd($employee_details);
        $this->EmployeeModel->where('id',$user_id)->update($employee_details);

        if($status)
        {
            /* update record into translation table */
            if(sizeof($arr_lang) > 0 )
            {
                foreach ($arr_lang as $lang) 
                {            
                    $arr_data = array();
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
        
        if($obj_data)
        {   
            Flash::success(str_singular($this->module_title).' '.translation('updated_successfully')); 
        }
        else
        {
            Flash::error(translation('problem_occurred_while_updating').' '.str_singular($this->module_title));  
        } 
      
        return redirect()->back();
    }   

     public function built_mail_data($arr_data)
    {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $template_id = $this->SchoolTemplateTranslationModel->where('title','School name')->first(['school_template_id']);
            $school_name = '';
            $school_details = $this->SchoolProfileModel->where('school_no',$this->school_id)->get();
            foreach ($school_details as $value) {
                if($value['school_template_id'] == $template_id->school_template_id)
                {
                    $school_name =  $value['value'];
                }
            }

            $login_url = '<p class="email-button"><a target="_blank" href="'.\URL::to('/school_admin').'">Click Here</a></p><br/>' ;

            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'LAST_NAME'        => ucfirst($arr_data['last_name']),
                                  'EMAIL'            => $arr_data['email'],
                                  'PASSWORD'         => $arr_data['password'],
                                  'ROLE'             => $arr_data['role'],  
                                  'URL'              => $login_url,
                                  'SCHOOL_NAME'      => $school_name,
                                  'PROJECT_NAME'     => config('app.project.name')];


            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '8';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }
    /*
    | store() : Export List
    | Auther  : Pooja
    | Date    : 21-07-2018
    */
    public function export(Request $request)
    {       
            $obj_data = $this->get_users_details($request,'','export');
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
                            $arr_fields['employee_number']= translation('employee_number');
                            $arr_fields['name']           = translation('name');
                            $arr_fields['email']          = translation('email');
                            $arr_fields['national_id']    = translation('national_id');
                            $arr_fields['license_number'] = translation('license_number');
                            $arr_fields['active']         = translation('active_status');
                            
                            $sheet->row(2, ['',ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                            $sheet->row(4, $arr_fields);
                            if(sizeof($obj_data)>0) 
                            {
                                
                                $arr_tmp = [];
                                foreach($obj_data as $key => $result)
                                {
                                    $status = "";
                                    if($result->is_active==1)
                                    {
                                        $status = "Active";
                                    }
                                    elseif($result->is_active==0)
                                    {
                                        $status = "InActive";
                                    }
                                    $arr_tmp[$key]['id']             = intval($key+1);
                                    $arr_tmp[$key]['employee_number']= $result->employee_no;
                                    $arr_tmp[$key]['name']           = ucwords($result->user_name);
                                    $arr_tmp[$key]['email']          = $result->email;
                                    $arr_tmp[$key]['national_id']    = $result->national_id;
                                    $arr_tmp[$key]['license_number'] = $result->license_no;
                                    $arr_tmp[$key]['active']         = $status;
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

                $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
                return $pdf->download($this->module_view_folder.'.export', $this->arr_view_data);
            }
    }
}