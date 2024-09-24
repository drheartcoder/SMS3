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
use App\Models\SchoolProfileModel;
use App\Models\SchoolTemplateTranslationModel;
use App\Models\AcademicYearModel;  
/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
/*Activity Log */
use App\Common\Services\LanguageService;
use App\Common\Services\EmailService;
use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;

class CanteenController extends Controller
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
                                    AcademicYearModel $year

                                )
    {
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->AcademicYearModel            = $year;
        $this->SchoolRoleModel              = $role;
        $this->EmployeeModel                = $employee;
        $this->EmailService                 = $mail_service;
        $this->BaseModel                    = $employee;
        $this->ActivityLogsModel            = $activity_logs; /* Activity Model */   
        $this->LanguageService              = $language;  
        $this->ParentModel                  = $parent;
        $this->SchoolProfileModel           = $profile;
        $this->SchoolTemplateTranslationModel = $template;
        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/canteen';
        
        $this->module_title                 = translation("canteen");
        $this->modyle_url_slug              = translation("canteen");

        $this->module_view_folder           = "schooladmin.canteen";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-user';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->first_name = $this->last_name =$this->ip_address ='';

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
        }
        /* Activity Section */
    }   

    /*
    | index() : load canteen staff listing page
    | Auther : sayali
    | Date : 03-05-2018
    */ 
    public function index(Request $request)
    {   
        
        $page_title = translation("manage_canteen_staff");
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = translation('canteen_staff');
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    function get_users_details(Request $request)
    {
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

        $user_details             = $this->UserModel->getTable();
        $prefixed_user_details    = DB::getTablePrefix().$this->UserModel->getTable();

        $user_trans_table             = $this->UserTranslationModel->getTable();                  
        $prefixed_user_trans_table    = DB::getTablePrefix().$this->UserTranslationModel->getTable();

        $employee                    = $this->EmployeeModel->getTable();       
        $prefixed_employee_table     =  DB::getTablePrefix().$this->EmployeeModel->getTable();       
  

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
                                ->whereIn($employee.'.user_role',['canteen_supervisor','canteen_manager','canteen_staff'])
                                ->where($user_trans_table.'.locale','=',$locale)
                                ->where($employee.'.school_id','=',$school_id)
                                ->whereBetween($employee.'.academic_year_id',[$start_id->id,$this->academic_year])
                                ->orderBy($employee.'.created_at','DESC');
                                
                  
        /* ---------------- Filtering Logic ----------------------------------*/                    

            $search = $request->input('search');
            $search_term = $search['value'];

            if($request->has('search') && $search_term!="")
            {
                $obj_user = $obj_user->WhereRaw("( (".$user_details.".email LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$user_details.".national_id LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$user_details.".mobile_no LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_employee_table.".user_role LIKE '%".$search_term."%') ")
                                     ->orWhereRaw(" ( CONCAT(".$prefixed_user_trans_table.".first_name,'',".$prefixed_user_trans_table.".last_name)  LIKE  '%".$search_term."%' ) )");
                                     
                                     
            }
        return $obj_user;
    }

    /*
    | get_records() : get records of canteen staff
    | Auther : sayali
    | Date : 03-05-2018
    */
    public function get_records(Request $request)
    {
        $role = Sentinel::findRoleBySlug(config('app.project.role_slug.school_admin_role_slug'));
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;
       
        $obj_user        = $this->get_users_details($request);

       // $role = $type;

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('users.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data)
                            {
                                return base64_encode($data->id);
                            });
                            
        }                    

        $json_result     = $json_result->editColumn('employee_no',function($data) 
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
                                    $name = explode(' ',$data->user_name);
                                    return  ucfirst($name[0]).' '.ucfirst($name[1]);
                                }else{
                                    return  '-';
                                }
                            })
                            ->editColumn('role',function($data) 
                            {   
                                if($data->role!=null && $data->role!=''){
                                    $name = explode('_',$data->role);
                                    return  ucfirst($name[1]);
                                }else{
                                    return  '-';
                                }
                            })
                            ->editColumn('build_action_btn',function($data) use ($arr_current_user_access,$role)
                            {
                                    
                                $build_edit_action = $build_status_btn = $build_view_action = $build_delete_action ='';
                                if($role != null)
                                {       
                                    $view_href =  $this->module_url_path.'/view_canteen_staff/'.base64_encode($data->id);
                                    $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>';


                                    if(array_key_exists('canteen.update',$arr_current_user_access))
                                    {
 
                                        $edit_href =  $this->module_url_path.'/edit_canteen_staff/'.base64_encode($data->id);
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
                                    if(array_key_exists('canteen.delete',$arr_current_user_access))
                                    {
                                        $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                        $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                    } 
                                }       
                                return $build_view_action.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_status_btn.'&nbsp;'.$build_delete_action;
                            })
                            ->editColumn('build_checkbox',function($data)use($arr_current_user_access){
                                $build_checkbox ='';
                                if(array_key_exists('canteen.update',$arr_current_user_access) || array_key_exists('canteen.delete',$arr_current_user_access))
                                {
                                    $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                }
                            return $build_checkbox;
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    /*
    | view() : load view page for canteen staff
    | Auther : sayali
    | Date : 03-05-2018
    */
    public function view($enc_id)
    {   
        $id       = base64_decode($enc_id);
        
        $employee_details =   $this->EmployeeModel
                               ->with('user_details') 
                               ->where('id',$id)
                               ->first();

        $arr_employee_details = [];
        if($employee_details)
        {
            $arr_employee_details = $employee_details->toArray();
        }
       
        $arr_data = []; 
        $arr_data    = $arr_employee_details;

        $this->arr_view_data['role']                         = 'employee';
        $this->arr_view_data['page_title']                   = translation("view").' '.translation('canteen_staff');
        $this->arr_view_data['module_title']                 = translation('canteen_staff');
        $this->arr_view_data['module_url_path']              = $this->module_url_path.'/manage_canteen_staff';
        $this->arr_view_data['arr_data']                     = $arr_data;
        $this->arr_view_data['module_icon']                  = $this->module_icon;
        $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
        $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        
        return view($this->module_view_folder.'.view', $this->arr_view_data);
    }

    /*
    | index() : load create page for canteen staff
    | Auther : sayali
    | Date : 03-05-2018
    */
    public function create()
    {
        $position   =   ['canteen_manager','canteen_staff','canteen_supervisor'];
        $role_name  =   [];
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
                    foreach ($position as $key => $value) {
                        if($obj->slug == $value)
                    {
                        $role_name[$key] = $obj->toArray();
                    }
                    }
                    
                }
            }
        }

        if(isset($role_name))
        {
            $this->arr_view_data['role']    = $role_name;   
        }
       
        $page_title = translation("add_canteen_staff");
        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['module_title']    = translation('canteen_staff');
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['create_icon']     = $this->create_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['module_path']     = $this->module_url_path.'/manage_canteen_staff';
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.create', $this->arr_view_data);        
    }

    /*
    | index() : register canteen staff
    | Auther : sayali
    | Date : 03-05-2018
    */
    public function store(Request $request)
    {
         
        $arr_rules  =   $messages = [];
        $arr_data   =   $this->LanguageService->get_all_language();
       
        $arr_rules['first_name']           = ['required','regex:/^[a-zA-Z ]*$/'];
        $arr_rules['last_name']            = ['required','regex:/^[a-zA-Z ]*$/'];
        $arr_rules['email']                = 'required|email';
        $arr_rules['mobile_no']            = 'required|numeric|digits_between:10,14';
        $arr_rules['national_id']          = ['required','regex:/^[a-zA-Z0-9]*$/'];
        $arr_rules['birth_date']           = 'required|date|before:tomorrow';
        $arr_rules['gender']               = 'required|alpha';
        $arr_rules['address']              = 'required';
        $arr_rules['year_of_experience']   = 'required|numeric|digits_between:1,2';
        $arr_rules['telephone_no']         = 'required|numeric|digits_between:6,14';
        $arr_rules['qualification_degree'] = ['required','regex:/^[a-zA-Z0-9 \,]*$/'];

        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'mobile_no.digits_between' => translation('please_enter_mobile_no_within_range_of_10_14'),
                            'telephone_no.digits_between'  => translation('please_enter_telephone_no_within_range_of_6_14'),
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'),
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
        $employee_no    =   $this->generate_employee_no($school_no);
        $data           =   [];

        $email          =   $request->input('email');
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
            
            $user = Sentinel::findById($parent_details->user_id);

            $role = Sentinel::findRoleBySlug($request->input('role'));
        
            $role->users()->attach($user);

            $employee_details = [];
            $employee_details['school_id']             =   $this->school_id;
            $employee_details['user_id']               =   $user->id;
            $employee_details['employee_no']           =   $employee_no;
            $employee_details['user_role']             =   $role->slug;
            $employee_details['marital_status']        =   $request->input('status');
            $employee_details['year_of_experience']    =   $request->input('year_of_experience');
            $employee_details['qualification_degree']  =   $request->input('qualification_degree');
            $employee_details['academic_year_id']      =   $this->academic_year;
            
            $employee  = $this->EmployeeModel->create($employee_details);

            $data          = [
                            'first_name'    =>  $request->Input('first_name'),
                            'last_name'     =>  $request->Input('last_name'),
                            'password'      =>  'admin@123',
                            'email'         =>  $request->Input('email'),
                            'role'          =>  $request->input('role')
                             ];

            $arr_mail_data = $this->built_mail_data($data); 
            $email_status  = $this->EmailService->send_mail($arr_mail_data); 
            
            if($user)
            {

                Flash::success(translation('canteen_staff_added_successfully'));
                return redirect()->back();
            }
            else
            {

                Flash::error(translation('problem_occured_while_adding_new_canteen_staff'));
                return redirect()->back();
            }
        }

        else
        {            
            $school_no      =   Session::get('school_id');
            $employee_no    =   $this->generate_employee_no($school_no);
            $data           =   [];
            $credentials    =   [
                                    'email'         =>  $request->Input('email'),
                                    'password'      =>  'admin@123',
                                    'telephone_no'  =>  $request->Input('telephone_no'),
                                    'mobile_no'     =>  $request->Input('mobile_no'),
                                    'address'       =>  $request->Input('address'),
                                    'profile_image' =>  $file_name,
                                    'national_id'   =>  $request->Input('national_id'),
                                    'birth_date'    =>  $request->input('birth_date'),
                                    'gender'        =>  $request->Input('gender'),
                                    'city'          =>  $request->Input('city'),
                                    'country'       =>  $request->Input('country'),
                                    'is_active'     =>  '1',
                                    'latitude'      =>  $request->Input('latitude'),
                                    'longitude'     =>  $request->Input('longitude')
                                ];

            
            $user = Sentinel::registerAndActivate($credentials);

            
            $role = Sentinel::findRoleBySlug($request->input('role'));
            
            $role->users()->attach($user);

            $employee_details = [];
            $employee_details['school_id']             =   $this->school_id;
            $employee_details['user_id']               =   $user->id;
            $employee_details['employee_no']           =   $employee_no;
            $employee_details['user_role']             =   $role->slug;
            $employee_details['marital_status']        =   $request->input('status');
            $employee_details['year_of_experience']    =   $request->input('year_of_experience');
            $employee_details['qualification_degree']  =   $request->input('qualification_degree');
            $employee_details['academic_year_id']      =   $this->academic_year;
            
            $employee  = $this->EmployeeModel->create($employee_details);
            
            if($user)
            {
                /* update record into translation table */
                
                if(sizeof($arr_data) > 0 )
                {
                    foreach ($arr_data as $lang) 
                    {            
                        $arr_data = array();
                        $first_name       = $request->input('first_name');
                        $last_name        = $request->input('last_name');
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

            $data          = [
                                'first_name'    =>  $request->Input('first_name'),
                                'last_name'     =>  $request->Input('last_name'),
                                'password'      =>  'admin@123',
                                'email'         =>  $request->Input('email'),
                                'role'          =>  $request->input('role')
                             ];

            $arr_mail_data = $this->built_mail_data($data); 
            $this->EmailService->send_mail($arr_mail_data); 
            
            if($user)
            {

                Flash::success(translation('canteen_staff_added_successfully'));
                return redirect()->back();
            }
            else
            {

                Flash::error(translation('problem_occured_while_adding_new_canteen_staff'));
                return redirect()->back();
            }
        }
    }

    /*
    | index() : generate employee no
    | Auther : sayali
    | Date : 03-05-2018
    */
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

    /*
    | index() : check existance of email
    | Auther : sayali
    | Date : 03-05-2018
    */
    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        $users = $employee = null;
        
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
                                        ->whereHas('employee_details',function($query) {
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

    /*
    | index() : manage translations records locale wise
    | Auther : sayali
    | Date : 03-05-2018
    */
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

    /*
    | index() : load canteen staff edit page
    | Auther : sayali
    | Date : 03-05-2018
    */
    public function edit($enc_id)
    {
        $id         =   base64_decode($enc_id);

        if(is_numeric($id)){
            $role_name  =   [];
            $position   =   ['canteen_manager','canteen_staff','canteen_supervisor'];

            $arr_user = $arr_employee_details = $arr_data = [];   

            $employee_details =   $this->EmployeeModel
                                   ->with('get_user_details') 
                                   ->where('id',$id)
                                   ->first();

            if($employee_details)
            {
                $arr_employee_details = $employee_details->toArray();
            }                 
            
            $arr_data    = $arr_employee_details;

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
                        foreach ($position as $key => $value) {
                            if($obj->slug == $value)
                        {
                            $role_name[$key] = $obj->toArray();
                        }
                        }
                        
                    }
                }
            }
            if(isset($role_name))
            {
                $this->arr_view_data['role']    = $role_name;   
            }
            $page_title = translation("edit_canteen_staff");
            $this->arr_view_data['edit_mode']       = TRUE;
            $this->arr_view_data['page_title']      = $page_title;
            $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
            $this->arr_view_data['module_title']    = translation('canteen_staff');
            $this->arr_view_data['module_icon']     = $this->module_icon;
            $this->arr_view_data['edit_icon']       = $this->edit_icon;
            $this->arr_view_data['arr_data']        = $arr_data;
            
            $this->arr_view_data['id']              = $id;
            $this->arr_view_data['module_url_path'] = $this->module_url_path;
            $this->arr_view_data['module_path']     = $this->module_url_path.'/manage_canteen_staff';
            $this->arr_view_data['theme_color']     = $this->theme_color;
            return view($this->module_view_folder.'.edit', $this->arr_view_data);
        }
        else{

            Flash::error(translation('something_went_wrong'));
            return redirect($thsi->module_url_path);
        }
        
    }

    /*
    | index() : update canteen staff details
    | Auther : sayali
    | Date : 03-05-2018
    */
    public function update(Request $request,$enc_id)
    {
        $arr_rules = array();
        $arr_lang   =   $this->LanguageService->get_all_language();
       
        $arr_rules['first_name']           = ['required','regex:/^[a-zA-Z ]*$/'];
        $arr_rules['last_name']            = ['required','regex:/^[a-zA-Z ]*$/'];
        $arr_rules['email']                = 'required|email';
        $arr_rules['mobile_no']            = 'required|numeric|digits_between:10,14';
        $arr_rules['national_id']          = ['required','regex:/^[a-zA-Z0-9]*$/'];
        $arr_rules['birth_date']           = 'required|date|before:tomorrow';
        $arr_rules['gender']               = 'required|alpha';
        $arr_rules['address']              = 'required';
        $arr_rules['year_of_experience']   = 'required|numeric|digits_between:1,2';
        $arr_rules['telephone_no']         = 'required|numeric|digits_between:6,14';
        $arr_rules['qualification_degree'] = ['required','regex:/^[a-zA-Z0-9 \,]*$/'];

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

        $id = base64_decode($enc_id);
        
        $oldImage = $request->input('old_image');
        if($request->hasFile('profile_image'))
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

        $employee_details = [];
        $employee_details['marital_status']        =   $request->input('status');
        $employee_details['user_role']             =   $request->input('role');
        $employee_details['year_of_experience']    =   $request->input('year_of_experience');
        $employee_details['qualification_degree']  =   $request->input('qualification_degree');
    
        $this->EmployeeModel->where('id',$id)->update($employee_details);

        $user = $this->EmployeeModel->select('user_id')->where('id',$id)->first();

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
        $arr_data['city']           = $request->input('city');
        $arr_data['country']        = $request->input('country');
        $arr_data['address']        = trim($request->input('address'));
       
        $obj_data = $this->UserModel->where('id',$user_id)->update($arr_data);

        $status = $this->UserModel->where('id',$user_id)->first(); 

        $role = Sentinel::findRoleBySlug($request->input('role'));
        $data['role_id']    =   $role->id;
        $this->UserRoleModel->where('user_id',$user_id)->update($data);

        if($status)
        {
            /* update record into translation table */
            if(sizeof($arr_lang) > 0 )
            {
                foreach ($arr_lang as $lang) 
                {            
                    $arr_data = array();
                    $first_name       = $request->input('first_name');
                    $last_name        = $request->input('last_name');
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
            Flash::success(translation('canteen_staff').' '.translation('updated_successfully')); 
        }
        else
        {
            Flash::error(translation('problem_occurred_while_updating').' '.translation('canteen_staff'));  
        } 
      
        return redirect()->back();
    }   

    /*
    | index() : generate mail data
    | Auther : sayali
    | Date : 03-05-2018
    */
     public function built_mail_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $template_id = $this->SchoolTemplateTranslationModel->where('title','School name')->first(['school_template_id']);
            $school_name = '';
            $school_details = $this->SchoolProfileModel->where('school_no',$this->school_id)->get();
            foreach ($school_details as  $value) {
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
   
}