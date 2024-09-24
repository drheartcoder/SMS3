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
/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
/*Activity Log */
use App\Common\Services\LanguageService;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;

class TechnicalController extends Controller
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
                                    LanguageService $language

                                )
    {
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->SchoolRoleModel              = $role;
        $this->EmployeeModel                = $employee;
        $this->BaseModel                    = $this->UserModel;
        $this->ActivityLogsModel            = $activity_logs; /* Activity Model */   
        $this->LanguageService              = $language;  

        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/technical';
        
        $this->module_title                 = translation("employee");
        $this->modyle_url_slug              = translation("employee");

        $this->module_view_folder           = "schooladmin.technical";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-user';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->school_id                    = Session::get('school_id');
        $this->first_name = $this->last_name =$this->ip_address ='';

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->ip_address        = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;  
        }
        /* Activity Section */



    }   

    public function index(Request $request)
    {   
        $page_title = translation("manage")." ".str_plural($this->module_title);
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    function get_users_details(Request $request,$type)
    {

        $school_id =$this->school_id;


        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
 

        $user_details             = $this->BaseModel->getTable();
        $prefixed_user_details    = DB::getTablePrefix().$this->BaseModel->getTable();

        $user_trans_table             = $this->UserTranslationModel->getTable();                  
        $prefixed_user_trans_table    = DB::getTablePrefix().$this->UserTranslationModel->getTable();

        $employee                    = $this->EmployeeModel->getTable();       
        $prefixed_employee_table     =  DB::getTablePrefix().$this->EmployeeModel->getTable();       
 

        $obj_user = DB::table($user_details)
                                ->select(DB::raw($prefixed_user_details.".id as id,".
                                                 $prefixed_user_details.".email as email, ".
                                                 $prefixed_user_details.".is_active as is_active, ".
                                                 $prefixed_employee_table.".user_role as role,".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
                                                          .$prefixed_user_trans_table.".last_name) as user_name"
                                                 ))
                                ->whereNull($user_details.'.deleted_at')
                                ->join($employee,$employee.'.user_id','=',$user_details.'.id')
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                ->where($user_trans_table.'.locale','=',$locale)
                                ->where($employee.'.school_id','=',$school_id)
                                ->orderBy($user_details.'.created_at','DESC');
                       
        /* ---------------- Filtering Logic ----------------------------------*/                    

        $arr_search_column = $request->input('column_filter');
        
        if(isset($arr_search_column['q_user_name']) && $arr_search_column['q_user_name']!="")
        {
            $search_term      = $arr_search_column['q_user_name'];
            $obj_user = $obj_user->having('login_username','LIKE', '%'.$search_term.'%');
        } 
        if(isset($arr_search_column['q_name']) && $arr_search_column['q_name']!="")
        {
            $search_term      = $arr_search_column['q_name'];
            $obj_user = $obj_user->having('user_name','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['q_role']) && $arr_search_column['q_role']!="")
        {
            $search_term      = $arr_search_column['q_role'];
            $obj_user = $obj_user->where($employee.'.user_role','LIKE', '%'.$search_term.'%');
        }

        return $obj_user;
    }


    public function get_records(Request $request)
    {
        
        $type = 'employee';
        $arr_current_user_access =[];
        $arr_current_user_access = $request->user()->permissions;
       
        $obj_user        = $this->get_users_details($request,$type);

        $role = 'professor';

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('technical.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data)
                            {
                                return base64_encode($data->id);
                            });
                            
        }                    

        $json_result     = $json_result->editColumn('role',function($data)
                            { 
                                 
                                if($data->role!=null && $data->role!=''){

                                    return  ucfirst($data->role);
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
                                    $build_edit_action = $build_status_btn =  ''; 
 
                                    $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                    $build_view_action = '<a class="btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$view_href.'" title="View"><i class="fa fa-eye" ></i></a>';


                                    if(array_key_exists('technical.update',$arr_current_user_access))
                                    {
                                        $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                        $build_edit_action = '<a class="btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$edit_href.'" title="edit"><i class="fa fa-edit" ></i></a>';

                                        if($data->is_active != null && $data->is_active == "0")
                                        {   
                                            $build_status_btn = '<a class="btn-dangers  btn-bordered btn-fill show-tooltip " title="Lock" href="'.$this->module_url_path.'/activate/'.base64_encode($data->id).'" 
                                            onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-lock"></i></a>';
                                        }
                                        elseif($data->is_active != null && $data->is_active == "1")
                                        {
                                            $build_status_btn = '<a class="btn-to-success btn-bordered btn-fill show-tooltip  btn-to-success" title="Unlock" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-unlock"></i></a>';
                                        }
                                    }
                                    
                                    return $build_view_action.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_status_btn.'&nbsp;';  
                                }
                            })
                            ->editColumn('build_checkbox',function($data){
                           
                                return '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                
                            
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    public function view($enc_id)
    {   
        $id = base64_decode($enc_id);
       

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
        
        $obj_user = $this->BaseModel
                         ->where('id','=',$id)
                         ->first();
        $professor_details =   $this->ProfessorModel
                               ->where('user_id',$id)
                               ->first();
        $arr_data = [];                                    
        
        if($obj_user && $professor_details)
        {
            $arr_data['users']        = $obj_user->toArray();
            $arr_data['professor']    = $professor_details->toArray();
        }  
        $this->arr_view_data['role']                         = 'professor';
        $this->arr_view_data['page_title']                   = translation("view").' professor ';
        $this->arr_view_data['module_title']                 = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['arr_data']                     = $arr_data;
        $this->arr_view_data['school_admin_panel_slug']      = config('school_admin_panel_slug');
        $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
        $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        
        return view($this->module_view_folder.'.view', $this->arr_view_data);


    }

    public function create()
    {
        
        $page_title = translation("add_new")." ".$this->module_title;
        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['role']            = 'professor';
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['module_title']    = $this->module_title;
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
        foreach ($arr_data as  $lang) 
        {
            $arr_rules['first_name_'.$lang['locale']]   =   'required';
            $arr_rules['last_name_'.$lang['locale']]    =   'required';   
        }
        $arr_rules['email']                 =   'required|email';
        $arr_rules['mobile_no']             =   'required|numeric';
        $arr_rules['national_id']           =   'required';
        $arr_rules['birth_date']            =   'required';
        $arr_rules['gender']                =   'required';
        $arr_rules['address']               =   'required';
        $arr_rules['year_of_experience']    =   'required';
        $arr_rules['telephone_no']          =   'required';
        $arr_rules['qualification_degree']  =   'required';


        foreach ($arr_data as $key => $lang) 
        {
            $messages['first_name_'.$lang['locale'].'.required']   =   'This field is required';
            $messages['last_name_'.$lang['locale'].'.required']    =   'This field is required';   
        }
        $messages['required']    =   'This field is required';
        $messages['email']       =   'Must be valid email address';

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
                $obj_image = $this->BaseModel->where('id',$request->input('user_id'))->first(['profile_image']);
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
//        /************* Image Upload ends here ********************/
        
        $school_no      =   Session::get('school_id');
        $employee_no    =   $this->generate_employee_no($school_no);
        $credentials    =   [
                                'email'         =>  $request->Input('email'),
                                'password'      =>  'admin@123',
                                'telephone_no'  =>  $request->Input('telephone_no'),
                                'mobile_no'     =>  $request->Input('mobile_no'),
                                'address'       =>  $request->Input('address'),
                                'profile_image' =>  $file_name,
                                'national_id'   =>  $request->Input('national_id'),
                                'birth_date'    =>  $request->Input('birth_date'),
                                'gender'        =>  $request->Input('gender'),
                                'city'          =>  $request->Input('city'),
                                'country'       =>  $request->Input('country'),
                                'is_active'     =>  '1',
                                'latitude'      =>  $request->Input('latitude'),
                                'longitude'     =>  $request->Input('longitude')
                            ];

        $user = Sentinel::registerAndActivate($credentials);

        $employee_details = [];
        $employee_details['school_id']             =   $this->school_id;
        $employee_details['user_id']               =   $user->id;
        $employee_details['employee_no']           =   $employee_no;
        $employee_details['year_of_experience']    =   $request->input('year_of_experience');
        $employee_details['qualification_degree']  =   $request->input('qualification_degree');
    
        $this->EmployeeModel->create($employee_details);
        
        if($user)
        {
            /* update record into translation table */
            
            if(sizeof($arr_data) > 0 )
            {
                foreach ($arr_data as $lang) 
                {            
                    $arr_data = array();
                    $first_name       = $request->input('first_name_'.$lang['locale']);
                    $last_name        = $request->input('last_name_'.$lang['locale']);
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
                            'first_name'    =>  $request->Input('first_name_en'),
                            'last_name'     =>  $request->Input('last_name_en'),
                            'password'      =>  'admin@123',
                            'email'         =>  $request->Input('email')
                         ];

        
        if($user)
        {

            Flash::success(translation('employee_added_successfully'));
            return redirect()->back();
        }
        else
        {

            Flash::error(translation('problem_occured_while_adding_new_employee'));
            return redirect()->back();
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
        if( ($this->BaseModel->where('email',$request->Input('email'))->count() )> 0)
        {
            return response()->json(array('status'=>'error','msg'=>translation('this_email_is_already_exist')));
        }
        else
        {
            return response()->json(array('status'=>'success'));
        }
    }

     public function checkNationalID(Request $request)
    {
        if( ($this->BaseModel->where('national_id',$request->Input('national_id'))->count() )> 0)
        {
            return response()->json(array('status'=>'error','msg'=>translation('this_national_id_is_already_exist')));
        }
        else
        {
            return response()->json(array('status'=>'success'));
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

        $obj_user = $this->BaseModel
                         ->with('translations')
                         ->where('id','=',$id)
                         ->first();

        $arr_user = [];
        if($obj_user)
        {
            $arr_user = $obj_user->toArray();
            $arr_user['translations'] = $this->arrange_locale_wise($arr_user['translations']);
        }
        
        $professor_details =   $this->ProfessorModel
                               ->where('user_id',$id)
                               ->first();

        $arr_professor_details = [];
        if($professor_details)
        {
            $arr_professor_details = $professor_details->toArray();
        }
        $arr_data = []; 
        $arr_data['users']        = $arr_user;
        $arr_data['professor']    = $arr_professor_details;
        
        $page_title = translation("edit")." ".$this->module_title;
        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['role']            = 'professor';
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['create_icon']       = $this->create_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.edit', $this->arr_view_data); 
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

   
}