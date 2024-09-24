<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;
use App\Common\Services\CommonDataService;

use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\SchoolProfileModel;
use App\Models\SchoolProfileTranslationModel;
use App\Models\SchoolAdminModel;
use App\Models\UserRoleModel;   
use App\Models\RoleModel;
use App\Models\ActivationModel;
use App\Models\SchoolTemplateModel;
use App\Common\Services\LanguageService;
/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
/*Activity Log */
use App\Common\Services\EmailService;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use PDF;
use Datatables;
    
class SchoolAdminController extends Controller
{
    use MultiActionTrait;
    public function __construct(    
                                    UserModel $user,
                                    UserTranslationModel $translation,
                                    SchoolProfileModel $profile,
                                    SchoolProfileTranslationModel $schoolProfile,
                                    SchoolAdminModel $school,
                                    UserRoleModel $user_role_model,
                                    RoleModel $role_model,
                                    ActivityLogsModel $activity_logs,
                                    LanguageService $language,
                                    EmailService $mail_service,
                                    SchoolTemplateModel $template,
                                    CommonDataService $CommonDataService

                                )
    {
        $str_school_admin = 'school_admin';

        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->SchoolProfileModel           = $profile;
        $this->SchoolProfileTranslationModel= $schoolProfile;
        $this->SchoolAdminModel             = $school;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->BaseModel                    = $this->SchoolAdminModel;
        $this->LanguageService              = $language;
        $this->ActivityLogsModel            = $activity_logs; /* Activity Model */   
        $this->EmailService                 = $mail_service;
        $this->SchoolTemplateModel          = $template;
        $this->CommonDataService            = $CommonDataService;

        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/".$str_school_admin);
        
        $this->module_title                 = translation($str_school_admin);
        $this->modyle_url_slug              = translation($str_school_admin);

        $this->module_view_folder           = "admin.".$str_school_admin;
        $this->theme_color                  = theme_color();

        $this->first_name = $this->last_name ='';

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            
        }
        /* Activity Section */



    }   

    public function index(Request $request)
    {   
        $page_title = translation("manage")." ".str_plural($this->module_title);
        $this->arr_view_data['role']            = 'school_admin';
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function create()
    {   
        
        $page_title                             = translation("create")." ".str_plural($this->module_title);
        $this->arr_view_data['role']            = 'school_admin';
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['image_path']     = $this->user_profile_public_img_path;
        return view($this->module_view_folder.'.create', $this->arr_view_data);
    }

    public function store(Request $request)
    {
        
        $arr_rules  =   $messages = [];

        $str_last_name      = 'last_name';
        $str_locale         = 'locale';
        $str_mobile_no      = 'mobile_no';
        $str_password       = 'password'; 
        $str_conf_password  = 'confirm_password'; 
        $str_national_id    = 'national_id';
        $str_telephone_no   = 'telephone_no';
        $str_birth_date     = 'birth_date';
        $str_gender         = 'gender';
        $str_address        = 'address';

        $arr_data   =   $this->LanguageService->get_all_language();
        $str_required = 'required';
        $str_email = 'email';
        $min1 = 'min:6';
        $min2 = 'min:10';
        $max  = 'max:14';

        foreach ($arr_data as $key => $lang) 
        {
            $arr_rules['first_name']   =   'required|regex:/^[a-zA-Z]+$/';
            $arr_rules[$str_last_name]    =   'required|regex:/^[a-zA-Z]+$/';   
        }
        $arr_rules[$str_email]                 =   $str_required.'|'.$str_email;
        $arr_rules[$str_conf_password]         =   $str_required.'|'.$min1;
        $arr_rules[$str_password]              =   $min1.'|required_with:confirm_password|same:confirm_password';
        $arr_rules[$str_mobile_no]             =   $str_required.'|numeric|digits_between:10,14';
        $arr_rules[$str_telephone_no]          =   'numeric|digits_between:6,14';
        $arr_rules[$str_national_id]           =    ['required','regex:/^[a-zA-Z0-9]+$/'];
        $arr_rules[$str_birth_date]            =   $str_required.'|date';
        $arr_rules[$str_gender]                =   $str_required;
        $arr_rules[$str_address]               =   $str_required;

        $messages[$str_required]            =   translation('this_field_is_required');
        $messages[$min1]                    =   translation('please_enter_at_least_6_digits');
        $messages['digits_between:10,14']   =   translation('please_enter_telephone_no_within_range_of').$min1.'-'.$max;
        $messages['digits_between:6,14']    =   translation('please_enter_mobile_no_within_range_of').$min2.'-'.$max;
        $messages[$max]                     =   translation('please_enter_not_more_than_14_digits');
        $messages['same']                   =   translation('password_and_confirm_password_must_be_same');
        $messages[$str_email]               =   translation('please_enter_valid_email_format');
        $messages['regex']                  =   translation('please_enter_valid_national_id_format');
        $messages['date']                   =   translation('please_enter_valid_date');
        
        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
         
        /***************** Image Upload starts here ******************/
        $is_new_file_uploaded = FALSE;

        if ($request->file('profile_image')) 
        {
            
            $arr_image_size = [];
            $arr_image_size = getimagesize($request->file('profile_image'));

            if(isset($arr_image_size) && $arr_image_size==false)
            {
                Flash::error(translation('please_use_valid_image'));
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
        $email =    trim($request->Input('email'));
        $admin   =    $this->UserModel->where('email',$email)->count();
        
        if($admin>0)
        {
            Flash::error(translation('this_user_is_already_exist'));
            return redirect()->back();
        }

        $role           = 'school_admin';
        $role_id        = $this->RoleModel->where('slug',$role)->value('id');
        $date           =   explode('/', $request->input($str_birth_date));
        $birth_date     =   $date[2].'-'.$date[0].'-'.$date[1];
        
        $data           =   [];
        $credentials    =   [
                                $str_email         =>  $request->Input($str_email),
                                $str_password      =>  $request->Input($str_password),
                                'telephone_no'     =>  $request->Input('telephone_no'),
                                $str_mobile_no     =>  $request->Input($str_mobile_no),
                                $str_address       =>  $request->Input($str_address),
                                'profile_image' =>  $file_name,
                                $str_national_id   =>  $request->Input($str_national_id),
                                $str_birth_date    =>  $birth_date,
                                $str_gender        =>  $request->Input($str_gender),
                                'city'          =>  $request->Input('city'),
                                'country'       =>  $request->Input('country'),
                                'is_active'     =>  '1',
                                'latitude'      =>  $request->Input('latitude'),
                                'longitude'     =>  $request->Input('longitude')
                            ];

        
        $user = Sentinel::registerAndActivate($credentials);

        $role = Sentinel::findRoleBySlug('school_admin');
        
        $role->users()->attach($user);
        
        if($user && sizeof($arr_data) > 0)
        {
            /* update record into translation table */
            
            foreach ($arr_data as $lang) 
            {            
                $arr_data = array();
                $first_name       = $request->input('first_name');
                $last_name        = $request->input($str_last_name);
                if( (isset($first_name)  && $first_name != '') && (isset($last_name) && $last_name != ''))
                { 
                    $translation = $user->translateOrNew($lang['locale']);
                    $translation->first_name    = $first_name;
                    $translation->last_name     = $last_name;
                    $translation->user_id       = $user->id;
                    $translation->locale       = $lang['locale'];
                    $translation->save();
                    Flash::success(str_singular($this->module_title).' '.translation('created_successfully'));
                }
            }
             
           
        }

        $data          = [
                            'first_name'      =>  $request->Input('first_name'),
                            'last_name'     =>  $request->Input('last_name'),
                            'password'      =>  $request->Input('password'),
                            'email'         =>  $request->Input('email')
                         ];

        $school_details               =   [];
        $school_details['user_id']    =   $user->id;
        $school_details['school_id']  =   '';
        $school_admin                 =   $this->SchoolAdminModel->create($school_details);


        $arr_mail_data = $this->built_mail_data($data); 
        $email_status  = $this->EmailService->send_mail($arr_mail_data); 
        
        if($user)
        {

            Flash::success(translation('school_admin_added_successfully'));
            return redirect()->back();
        }
        else
        {

            Flash::error(translation('problem_occured_while_adding_new_school_admin'));
            return redirect()->back();
        }
    }

    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        $count = $this->SchoolAdminModel->whereHas('get_user_details',function($q) use($email){
                                            $q->where('email',$email);
                                        })->count();
        if( $count > 0)
        {
            return response()->json(array('status'=>'error','msg'=>translation('this_school_admin_is_already_exist')));
        }
        else
        {
            return response()->json(array('status'=>'success'));
        }
    }

     public function built_mail_data($arr_data)
    {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $login_url = '<p class="email-button"><a target="_blank" href="'.\URL::to('/school_admin').'">Click Here</a></p><br/>' ;

            $arr_built_content = [
                                  'FIRST_NAME'       => $arr_data['first_name'],
                                  'LAST_NAME'        => $arr_data['last_name'],
                                  'EMAIL'            => $arr_data['email'],
                                  'PASSWORD'         => $arr_data['password'],
                                  'URL'              => $login_url,
                                  'PROJECT_NAME'     => config('app.project.name')];


            $arr_mail_data                      = [];
            $arr_mail_data['email_template_slug']   = 'school_admin_registration';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function get_records(Request $request,$type='')
    {
        
        
        $arr_current_user_access =[];
        $role = Sentinel::findRoleById(1);
                
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;
       
        $obj_user        = $this->get_users_details($request,$type);

        $role = 'school_admin';


        $current_context = $this;

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('users.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data) use ($current_context)
                            {
                                return base64_encode($data->id);
                            });
        }                    

        $json_result     = $json_result->editColumn('user_name',function($data) use ($current_context)
                            { 
                                 
                                if($data->user_name!=null && $data->user_name!=''){
                                    $name = explode(' ', $data->user_name);
                                    $name_user  =   ucfirst($name[0]).' '.ucfirst($name[1]);
                                    return  $name_user;
                                }else{
                                    return  '-';
                                }

                            }) 
                            ->editColumn('last_login',function($data) use ($current_context)
                            { 
                                 
                                if($data->last_login!=null && $data->last_login!='0000-00-00 00:00:00'){

                                    return  getDateFormat($data->last_login);
                                }else{
                                    return  '-';
                                }

                            }) 
                            ->editColumn('school_name',function($data)
                            {
                                $school_name = '';
                                if($data->school_id != null && $data->school_id != '')
                                {
                                    $school_name = $this->CommonDataService->get_school_name($data->school_id);
                                }
                                else
                                {
                                    $school_name = '-';
                                }
                                return $school_name;
                            })

                            ->editColumn('assign_school',function($data) use ($current_context)
                            { 
 
                                if(!isset($data->school_id) || $data->school_id == null || $data->school_id == '0'){
                                    
                                    $view_href =  'school/create/'.base64_encode($data->user_id);
                                    $build_view_action = '<a class="btn btn-primary" href="'.$view_href.'" title="'.translation('assign_school').'">'.translation('add_school_info').'</a>';

                                    return $build_view_action;
                                }
                                else
                                {
                                    $view_href =  'school/edit/'.base64_encode($data->user_id);
                                    $build_view_action = '<a class="btn btn-primary" href="'.$view_href.'" title="'.translation('edit_school').'">'.translation('edit_school_info').'</a>';

                                    return $build_view_action;
                                }

                            }) 
                            ->editColumn('build_action_btn',function($data) use ($current_context,$role,$arr_current_user_access)
                            {
                                if($role != null)
                                {       
                                    $build_edit_action = $build_status_btn =  ''; 
 
                                    $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                    $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>';

                                    if($data->is_active != null && $data->is_active == "0")
                                    {   
                                        $build_status_btn = '<a class="blue-color" title="'.translation('activate').'" href="'.$this->module_url_path.'/activate/'.base64_encode($data->id).'" 
                                        onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-lock"></i></a>';
                                    }
                                    elseif($data->is_active != null && $data->is_active == "1")
                                    {
                                        $build_status_btn = '<a class="light-blue-color" title="'.translation('deactivate').'" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-unlock"></i></a>';
                                    }
                                    if($role == 'school_admin' && array_key_exists('users.update',$arr_current_user_access))
                                    {
                                        

                                        $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                        $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';
                                        
                                    }
                                    
                                    
                                    return $build_status_btn.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_view_action.'&nbsp;';  
                                }
                            })
                            ->editColumn('build_checkbox',function($data){
                           
                                $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                
                            return $build_checkbox;
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    function get_users_details(Request $request,$type,$fun_type='')
    {
        $role =$type;
        $locale = '';
        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
 

        $user_details             = $this->UserModel->getTable();
        $prefixed_user_details    = DB::getTablePrefix().$this->UserModel->getTable();
        $user_role_table          = $this->UserRoleModel->getTable();
        $prefixed_user_role_table = DB::getTablePrefix().$this->UserRoleModel->getTable();

        $role_table               = $this->RoleModel->getTable();
        $prefixed_role_table      = DB::getTablePrefix().$this->RoleModel->getTable();

        $user_trans_table             = $this->UserTranslationModel->getTable();                  
        $prefixed_user_trans_table    = DB::getTablePrefix().$this->UserTranslationModel->getTable();

        $school_admin_table             = $this->SchoolAdminModel->getTable();
        $prefixed_school_admin_details  = DB::getTablePrefix().$this->SchoolAdminModel->getTable();

        $obj_user = DB::table($school_admin_table)
                                ->select(DB::raw($school_admin_table.".id as id,".
                                                 $school_admin_table.".user_id as user_id,".   
                                                 $prefixed_user_details.".email as email, ".
                                                 $school_admin_table.".is_active as is_active,".
                                                 $prefixed_user_details.".last_login as last_login,".
                                                 $prefixed_school_admin_details.".school_id,".
                                                 $role_table.".slug as role_slug,".
                                                 $user_details.".deleted_at,".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"

                                                          .$prefixed_user_trans_table.".last_name) as user_name"
                                                 ))

                                ->whereNull($school_admin_table.'.deleted_at')
                                ->join($user_details,$user_details.'.id','=',$school_admin_table.'.user_id')
                                ->join($user_role_table,$user_details.'.id','=',$user_role_table.'.user_id')
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                ->join($role_table, function ($join) use($role_table,$user_role_table,$role) {
                                    $join->on($role_table.'.id', '=',$user_role_table.'.role_id')
                                         ->where('slug','=',$role);
                                })
                                ->where($user_trans_table.'.locale','=',$locale)
                                ->orderBy($user_details.'.created_at','DESC');
      
        if($fun_type == 'export'){
            $search_term = $request->input('search');
        }else{
            $search = $request->input('search');
            $search_term = $search['value'];
        }

        if($request->has('search') && $search_term!="")
        {
            $obj_user = $obj_user->WhereRaw("( (".$user_details.".email LIKE '%".$search_term."%') ")
                                     ->orWhereRaw(" ( CONCAT(".$prefixed_user_trans_table.".first_name,'',".$prefixed_user_trans_table.".last_name)  LIKE  '%".$search_term."%' ) )");
        }

        if($fun_type=="export"){
            return $obj_user->get();
        }else{
            return $obj_user;
        }

    }


    public function view($enc_id)
    {   
        $id = base64_decode($enc_id);
        $locale = '';
        $data   = [];
        $arr_lang   = [];
        $arr_lang   = $this->LanguageService->get_all_language();  

        $school_admin   =   $this->SchoolAdminModel
                            ->where('id',$id)
                            ->first();

        $obj_user       = $this->UserModel
                               ->where('id','=',$school_admin->user_id)
                               ->first();
        

        $school = $this->SchoolProfileModel
                         ->where('school_no',$school_admin->school_id)
                         ->get();
        
        $school_details     =   [];
        foreach($arr_lang as $lang_key =>$lang )
        {
            foreach ($school as $key => $value) {
                
                $data[$key]     =   $this->SchoolProfileTranslationModel->where('school_profile_id',$value['id'])->get()->toArray();
             
            }
        }
        
        $obj_template = $this->SchoolTemplateModel
                             ->with("get_question_category")
                             ->where('is_active',1)
                             ->orderBy('position','asc')
                             ->get();
        
        $arr_data = [];                                    
        
        if($obj_user)
        {
            $arr_data['users']              = $obj_user->toArray();
            if($school != null)
            {                
                $arr_data['school']             = $school->toArray();
                if($obj_template)
                {
                    $arr_data['template']           = $obj_template->toArray() ;
                }
            }

        }  
        $this->arr_view_data['role']                         = 'school_admin';
        $this->arr_view_data['page_title']                   = translation("view").' school_admin ';
        $this->arr_view_data['module_title']                 = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['arr_data']                     = $arr_data;
       
        $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
        $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        
        return view($this->module_view_folder.'.view', $this->arr_view_data);


    }

    public function edit($enc_id)
    {
        $page_title         = translation("edit")." ".str_plural($this->module_title);
        $id                 = base64_decode($enc_id,TRUE);
        
        if(!is_numeric($id))
        {
            Flash::error(translation('something_went_wrong'));
            return redirect($this->module_url_path);
        }

        $arr_lang           = $this->LanguageService->get_all_language();
        $arr_user = $arr_user_details = [];

        $school_admin = $this->BaseModel->where('id',$id)->first();
        if(!$school_admin){
            Flash::error(translation('something_went_wrong'));
            return redirect($this->module_url_path);   
        }
        $obj_data           = $this->UserModel
                                   ->where('id',$school_admin->user_id)
                                   ->first();
        
        if(isset($obj_data) && $obj_data != null)
        {
            $arr_user = $obj_data->toArray();
        }
        else
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }
        

        $arr_data = [];
        if(isset($arr_user) && count($arr_user)>0)
        {
            $arr_data['user']      = $arr_user;
        }

        $this->arr_view_data['arr_data']                            = $arr_data;
        $this->arr_view_data['edit_mode']                           = TRUE;
        $this->arr_view_data['user_profile_public_img_path']        = $this->user_profile_public_img_path;
        $this->arr_view_data['role']                                = config('app.project.role_slug.school_admin_role_slug');
        $this->arr_view_data['page_title']                          = $page_title;
        $this->arr_view_data['module_title']                        = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']                     = $this->module_url_path;
        $this->arr_view_data['theme_color']                         = $this->theme_color;
        $this->arr_view_data['enc_id']                              = $enc_id;
        $this->arr_view_data['image_path']     = $this->user_profile_public_img_path;

        return view($this->module_view_folder.'.edit', $this->arr_view_data);
    }

    public function update(Request $request,$enc_id)
    {
        $id = base64_decode($enc_id);
        if(!is_numeric($id))
        {
            Flash::error(translation('something_went_wrong'));
            redirect($this->module_url_path);
        }

        $school_admin = $this->BaseModel->where('id',$id)->first();
        if(!$school_admin){
            Flash::error(translation('something_went_wrong'));
            return redirect($this->module_url_path);   
        }
        $arr_rules = $messages = array();
        $arr_lang   =   $this->LanguageService->get_all_language();

        $str_last_name = 'last_name';
        $str_locale = 'locale';
        $str_mobile_no = 'mobile_no';
        $str_national_id = 'national_id';
        $str_birth_date = 'birth_date';
        $str_gender = 'gender';
        $str_address ='address';
        $str_telephone_no = 'telephone_no';
        
        $arr_rules['first_name']   = "required|regex:/^[a-zA-Z ]*$/";
        $arr_rules['last_name']    = "required|regex:/^[a-zA-Z ]*$/"; 
        $arr_rules[$str_mobile_no]    = "required|numeric|digits_between:10,14";
        $arr_rules[$str_telephone_no] = "numeric|digits_between:6,14";
        $arr_rules[$str_address]      = "required";
        $arr_rules[$str_national_id]  = "required|alphanum";
        $arr_rules[$str_birth_date]   = "required|date";

        $messages['required']               =   translation('this_field_is_required');
        
        $messages['numeric']                =   translation('please_enter_digits_only');
        $messages['digits_between:10,14']   =   translation('please_enter_mobile_no_within_range_of_10_14');
        $messages['digits_between:6,14']    =   translation('please_enter_telephone_no_within_range_of_6_14');
        
        $messages['same']                   =   translation('password_and_confirm_password_must_be_same');
        $messages['regex']                  =   translation('please_enter_valid_text_format');
        $messages['date']                   =   translation('please_enter_valid_date');
        $messages['alpha']                  =   translation('please_enter_characters_only');

        
        $validator = Validator::make($request->all(),$arr_rules,$messages);
       
        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $oldImage = $request->input('oldimage');
        if($request->hasFile('image'))
        { 
            $arr_image_size = [];
            $arr_image_size = getimagesize($request->file('image'));

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
            $file_extension = strtolower($request->file('image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('image')->move($this->user_profile_base_img_path , $file_name);
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
        $date           =   explode('/', $request->input($str_birth_date));
        $birth_date     =   $date[2].'-'.$date[0].'-'.$date[1];

        $arr_data                      = [];
        $arr_data['profile_image']  = $file_name;
        $arr_data['latitude']          = $request->input('latitude');
        $arr_data['longitude']         = $request->input('longitude');
        $arr_data['telephone_no']      = $request->input('telephone_no');
        $arr_data[$str_national_id]    = $request->input($str_national_id);    
        $arr_data[$str_mobile_no]      = $request->input($str_mobile_no);
        $arr_data[$str_birth_date]     = $birth_date;
        $arr_data[$str_gender]         = $request->input($str_gender);
        $arr_data['city']              = $request->input('city');
        $arr_data['country']           = $request->input('country');
        $arr_data[$str_address]        = trim($request->input($str_address));

        $obj_data = $this->UserModel->where('id',$school_admin->user_id)->update($arr_data);

        $status = $this->UserModel->where('id',$school_admin->user_id)->first(); 
        if($status && sizeof($arr_lang) > 0)
        {
            /* update record into translation table */
            foreach ($arr_lang as $lang) 
            {            
                $arr_data = array();
                $first_name       = $request->input('first_name');
                $last_name        = $request->input('last_name');
                if( (isset($first_name)  && $first_name != '') && (isset($last_name) && $last_name != ''))
                { 
                    $translation = $status->translateOrNew($lang[$str_locale]);
                    $translation->first_name    = $first_name;
                    $translation->last_name     = $last_name;
                    $translation->save();
                    Flash::success(str_singular($this->module_title).' '.translation('updated_successfully'));
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

    /*
    | export() : Export List
    | Auther  : Vrajesh
    | Date    : 14-12-2018
    */

    public function export(Request $request)
    {
        $file_type = config('app.project.export_file_formate');
        $obj_data = $this->get_users_details($request,'school_admin','export');

        if(sizeof($obj_data)<=0){
            Flash::error(translation("no_records_found_to_export"));
            return redirect()->back();
        }
        if(sizeof($obj_data)>500 && $request->file_format == $file_type ){
            Flash::error(translation("too_many_records_to_export"));
            return redirect()->back();
        }
        if($request->file_format == $file_type){
            \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel) use($obj_data) 
                {
                    $excel->sheet(ucwords($this->module_title), function($sheet) use($obj_data) 
                    {
                        $arr_fields['sr_no']        = translation('sr_no');
                        $arr_fields['name']         = translation('name');
                        $arr_fields['email']        = translation('email');
                        $arr_fields['school_name']  = translation('school_name');

                        $sheet->row(2, [ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                        $sheet->row(4, $arr_fields);
                        
                        // To set Colomn head
                        $j = 'A'; $k = '4';
                        for($i=0; $i<=3;$i++)
                        {
                            $sheet->cell($j.$k, function($cells) {
                                $cells->setBackground('#495b79');
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                                $cells->setFontColor('#ffffff');
                            });
                            $j++;
                        }

                        if(sizeof($obj_data)>0)
                        {
                            $arr_tmp = [];
                            $count = 1;
                            foreach($obj_data as $key => $result)
                            {
                                $school_name = '';
                                if($result->school_id != null && $result->school_id != '')
                                {
                                    $school_name = $this->CommonDataService->get_school_name($result->school_id);
                                }else{
                                    $school_name = '-';
                                }
                                $arr_tmp[$key]['sr_no']         = $count++;
                                $arr_tmp[$key]['name']          = $result->user_name;
                                $arr_tmp[$key]['email']         = $result->email;
                                $arr_tmp[$key]['school_name']   = $school_name;
                            }
                            $sheet->rows($arr_tmp);
                        }
                    });
                })->export($file_type);     
        }
        
        if($request->file_format == 'pdf')
        {
            $this->arr_view_data['arr_data'] = $obj_data;

            foreach($this->arr_view_data['arr_data'] as $key => $row)
            {
                $school_name = '';
                if($row->school_id != null && $row->school_id != '')
                {
                    $school_name = $this->CommonDataService->get_school_name($row->school_id);
                }else{
                    $school_name = '-';
                }
                $row->school_name = $school_name;
            }

            $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
            return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
        }
    }
   
}