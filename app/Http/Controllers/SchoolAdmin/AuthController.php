<?php
                                    
namespace App\Http\Controllers\SchoolAdmin;
    
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\EmployeeModel;
use App\Models\UserRoleModel;
use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;
use App\Models\SchoolAdminModel;  
use App\Models\UserModel;  
use App\Models\StudentModel;
use App\Models\AcademicYearModel;
use App\Models\ProfessorModel;  
use App\Models\SchoolParentModel;

use Validator;
use Flash;
use Sentinel;
use Reminder;
use URL;
use Mail;
use Session;
use Cookie;

class AuthController extends Controller
{
    public function __construct(EmailService $mail_service,
                                EmployeeModel $employee,
                                SchoolAdminModel $school_admin,
                                CommonDataService $CommonDataService,
                                UserRoleModel $UserRoleModel,
                                StudentModel $StudentModel,
                                AcademicYearModel $AcademicYearModel,
                                ProfessorModel $ProfessorModel,
                                SchoolParentModel $SchoolParentModel
                                )
    {
        $this->arr_view_data      = [];
        $this->module_title       = "School Admin";
        $this->module_view_folder = "auth";
        $this->EmailService       = $mail_service;
        $this->school_admin_panel_slug   = config('app.project.role_slug.school_admin_role_slug');
        $this->module_url_path    = url($this->school_admin_panel_slug);
        $this->SchoolAdminModel   = $school_admin;
        $this->EmployeeModel      = $employee;
        $this->StudentModel       = $StudentModel;
        $this->CommonDataService  = $CommonDataService;
        $this->UserRoleModel      = $UserRoleModel;
        $this->AcademicYearModel  = $AcademicYearModel;
        $this->ProfessorModel     = $ProfessorModel;
        $this->SchoolParentModel  = $SchoolParentModel;
        $this->UserModel          = new UserModel();
        $this->locale = '';
        if(\Session::has('locale'))
        {
            $this->locale = \Session::get('locale');
        }
        else
        {
            $this->locale = 'fr';
        }

        

        /*----------------Admin Panel Theme Color Helper----------------*/
        $this->theme_color = theme_color();
        /*--------------------------------------------------------------*/      
    }

    /************ Redirect to login page view ***************/
    public function login()
    {
        
        $this->arr_view_data['module_title']     = $this->module_title." Login";
        $this->arr_view_data['theme_color']      = $this->theme_color;
        $this->arr_view_data['locale']           = $this->locale;

        return view($this->module_view_folder.'.login',$this->arr_view_data);
    }
                                                        
    /************ Verify user login credentials or check wheher he is blocked by admin***/
    public function process_login(Request $request)
    {        

        /*\Session::flush();*/
        $validator = Validator::make($request->all(), [
            'email'    => 'required|max:255',
            'password' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return redirect(config('app.project.role_slug.school_admin_role_slug').'/login')
                      ->withErrors($validator)
                      ->withInput($request->all());
        }
        $email = trim($request->input('email'));

        $credentials = [
                            'email'    => $email,
                            'password' => $request->input('password'),
                       ];

        try 
        {

            $check_authentication = Sentinel::authenticate($credentials);
           
            if($check_authentication)
            {

                $user = Sentinel::getUser();

                $role = $this->UserRoleModel->with('role_details')->where('user_id',$user->id)->get();
                
                if($role)
                {
                    $role = $role->toArray();
                }

                
                $count1 = 0;
                $user_role = [];
                $slug = '';
                foreach ($role as $key => $value) 
                {
                    if($value['role_details']['slug']==config('app.project.role_slug.parent_role_slug'))
                    {
                        $count1++;
                        array_push($user_role, config('app.project.role_slug.parent_role_slug'));
                        $slug = config('app.project.role_slug.parent_role_slug');
                    }
                    if($value['role_details']['slug']==config('app.project.role_slug.professor_role_slug'))
                    {
                        $count1++;
                        array_push($user_role, config('app.project.role_slug.professor_role_slug'));
                        $slug = config('app.project.role_slug.professor_role_slug');
                    }
                    if($value['role_details']['slug']==config('app.project.role_slug.student_role_slug'))
                    {
                        $count1++;
                        array_push($user_role, config('app.project.role_slug.student_role_slug'));
                        $slug = config('app.project.role_slug.student_role_slug');
                    }
                    if($value['role_details']['slug']==config('app.project.role_slug.admin_role_slug'))
                    {
                        $count1++;
                        array_push($user_role, config('app.project.role_slug.admin_role_slug'));
                        $slug = config('app.project.role_slug.admin_role_slug');
                    }
                    if($value['role_details']['slug']==config('app.project.role_slug.school_admin_role_slug'))
                    {
                        $count1++;
                        array_push($user_role, config('app.project.role_slug.school_admin_role_slug'));
                        $slug = config('app.project.role_slug.school_admin_role_slug');
                    }
                    if($value['role_details']['slug']!=config('app.project.role_slug.parent_role_slug') && $value['role_details']['slug']!=config('app.project.role_slug.professor_role_slug') && $value['role_details']['slug']!=config('app.project.role_slug.student_role_slug') && $value['role_details']['slug']!=config('app.project.role_slug.admin_role_slug') && $value['role_details']['slug']!=config('app.project.role_slug.school_admin_role_slug'))
                    {
                        $count1++;
                        array_push($user_role, 'employee');
                        $slug = 'employee';
                    }


                }
                
                if($count1==1)
                {
                        if(config('app.project.role_slug.parent_role_slug')== $slug)
                        {
                            $school = $this->SchoolParentModel->select('school_id')->where('parent_id',$user->id)->where('is_active',1)->get();
                                            
                            if(count($school)!=0 && count($school)>1)
                            {
                                $obj_data  = '';

                                foreach ($school as $key => $school) {
                                    $obj_data['school_id'] = $school->school_id;
                                    $obj_data['name']      = $this->CommonDataService->get_school_name($school->school_id);
                                    $data[$key]   =   $obj_data;
                                }
                                $this->arr_view_data['arr_school']   = $data;
                                $this->arr_view_data['theme_color']  = $this->theme_color;
                                $this->arr_view_data['locale']       = $this->locale;
                                return view($this->module_view_folder.'.school_list',$this->arr_view_data);
                            }
                            else
                            {      
                                $school_id = isset($school[0]['school_id']) ? $school[0]['school_id'] : '0' ;
                                \Session::set('school_id', $school_id );

                                $academic_year_id = 0 ;
                                $current_year = date('Y');
                                $previous_year = $current_year - 1;
                                $next_year = $current_year + 1;
                                $previous_acdemic_year = $previous_year.'-'.$current_year;
                                $next_acdemic_year = $current_year.'-'.$next_year;

                                $next_exist = AcademicYearModel::where('academic_year',$next_acdemic_year)->where('school_id',\Session::get('school_id'))->first();
                                if(empty($next_exist))
                                {
                                    $prvious_exist = AcademicYearModel::where('academic_year',$previous_acdemic_year)->where('school_id',\Session::get('school_id'))->first();
                                    if(!empty($prvious_exist))
                                    {

                                        $current_date = date('Y-m-d');
                                        $current_date = date_create($current_date);
                                        $to_date = date_create($prvious_exist->end_date);
                                        $date_diff = date_diff($to_date,$current_date);
                                        $url = config('app.project.role_slug.school_admin_role_slug').'/academic_year/create';
                                        if($date_diff->format('%R%a') > 0)
                                        {   
                                            
                                        }
                                        else
                                        {
                                            $academic_year_id = $prvious_exist->id;
                                        }
                                    }
                                    else
                                    {
                                        
                                    }
                                }
                                else
                                {
                                   $academic_year_id = $next_exist->id; 
                                }   
                                
                                \Session::put('academic_year',$academic_year_id);

                                return redirect(url(config('app.project.role_slug.parent_role_slug').'/dashboard'));
                            }
                        }    

                        if($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
                        {
                            $school = $this->SchoolAdminModel->where('user_id',$user->id)->first();
                            if(isset($school->is_active) && $school->is_active==0){
                                Flash::error('Your account is blocked by admin');
                                return redirect()->back();       
                            }  
                            $school_id = isset($school->school_id) ? $school->school_id : '0' ;
                            
                            \Session::set('school_id', $school_id );
                    
                            return redirect(url(config('app.project.role_slug.school_admin_role_slug').'/dashboard'));
                        }
                         
                        
                        $status = $this->CommonDataService->validate_user($slug,$user->id);
                        
                        if($status==1)
                        {
                            
                            if($slug == 'employee')
                            {
                                return redirect(url('/').'/'.config('app.project.role_slug.school_admin_role_slug').'/dashboard');
                            }
                            else
                            {
                                return redirect(url('/').'/'.$slug.'/dashboard');
                            }
                        }
                        elseif($status==0)
                        {
                            Flash::error('You have already left the school');
                            return redirect(url(config('app.project.role_slug.school_admin_role_slug')));
                        }
                }

                if($count1>1)
                {
                    $this->arr_view_data['theme_color']  = $this->theme_color;
                    $this->arr_view_data['roles']        = $user_role;
                    $this->arr_view_data['locale']       = $this->locale;

                    return view($this->module_view_folder.'.roles',$this->arr_view_data);
                }
            }
            else
            {
                Flash::error('Invalid Login Credential');
                return redirect(url('/').'/login');
            }    
        } 
        catch (\Exception $e) 
          {

            if($e instanceof \Cartalyst\Sentinel\Checkpoints\NotActivatedException)
            {
                Session::flash('error','Your account is not approved yet, Please contact to admin.');    
                Sentinel::logout();
                return redirect()->back();

            }
            else if($e instanceof \Cartalyst\Sentinel\Checkpoints\ThrottlingException)
            {  
                Flash::error('Suspicious activity has occured on your IP,try after some time.'); 
                return redirect()->back();
            }
            else if($e instanceof \Exception)
            {   
                Session::flash('error',$e->getMessage()); 
                Sentinel::logout();
                return redirect()->back();
            }
            else
            {
                Session::flash('error',$e->getMessage()); 
                Sentinel::logout();
                return redirect()->back();
            }
        }

        Flash::error('Something went wrong ! Please try again !');
        return redirect()->back();
                                                    
    }

    /************ Redirect to change password view ***************/
    public function change_password()
    {
        $this->arr_view_data['page_title']      = $this->module_title." Change Password";
        $this->arr_view_data['module_title']    = $this->module_title." Change Password";
        $this->arr_view_data['module_url_path'] = $this->module_url_path.'/change_password';
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']= "fa fa-edit";
        return view($this->module_view_folder.'.change_password',$this->arr_view_data);    
    }

    /************ Update password of particular user ***************/
    public function update_password(Request $request)
    {
        $arr_rules                     = array();
        $arr_rules['current_password'] = "required";
        $arr_rules['new_password']     = "required|confirmed";
      
        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        if($request->input('current_password')==$request->input('new_password'))
        {
            Flash::error(translation('current_password_and_new_password_should_not_be_same'));
            return redirect()->back();
        }

        $credentials = [];
        $credentials['password'] = $request->input('current_password');

        try 
        {
            $user = Sentinel::check();
            if (Sentinel::validateCredentials($user,$credentials)) 
            {  
                $new_credentials = [];
                $new_credentials['password'] = $request->input('new_password');

                if(Sentinel::update($user,$new_credentials))
                {
                    Flash::success(translation('password_changed_successfully'));
                    return redirect()->back();
                }
                else
                {
                    Flash::error('Problem Occurred, While Changing Password');
                    return redirect()->back();
                }
            } 
            else
            {
                Flash::error('Invalid Old Password');
                return redirect()->back();
            }            
        } 
        catch (\Exception $e) 
        {
            Flash::error($e->getMessage());
            return redirect()->back();
        }

        Flash::error('Something went wrong ! Please try again !');
        return redirect()->back();
    }

    /************ Forget password process step-1***************/
    public function process_forgot_password(Request $request)
    {

        $arr_rules['email']      = "required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
          Flash::error('Please enter valid email_id');
          return redirect()->back()->withErrors($validator)->withInput();
        }

        $email = trim($request->input('email'));

        $user  = Sentinel::findByCredentials(['email' => $email]);

        if($user==null)
        {
            Flash::error("Invaild Email Id");
            return redirect()->back();
        }

        /*if(!$user->inRole('school_admin') && !$user->inRole('technical') )
        {
            Flash::error('We are unable to process this Email Id');
            return redirect()->back();
        }*/

        $reminder = Reminder::create($user);

        if($reminder)
        {
            $arr_mail_data = $this->built_mail_data($email, $reminder->code); 
            $email_status  = $this->EmailService->send_mail($arr_mail_data,\Session::get('school_id'));
        
            if($email_status)
            {
                Flash::success('Password reset link sent successfully to your email id');
                return redirect()->back();
            }
            else
            {
                Flash::success('Error while sending password reset link');
                return redirect()->back();
            }
        }
        else
        {
            Flash::error('We are unable to process this Email Id');
            return redirect()->back();
        }

    }

    /********** build mail content ****************/
    public function built_mail_data($email, $reminder_code)
    {
        $user = $this->get_user_details($email);
        
        if($user)
        {
            $arr_user = $user->toArray();

            $reminder_url = '<p class="email-button"><a target="_blank" href=" '.URL::to('login/validate_admin_reset_password_link/'.base64_encode($arr_user['id']).'/'.base64_encode($reminder_code) ).'">Reset Password</a></p><br/>' ;

            $arr_built_content = ['FIRST_NAME'       => $arr_user['first_name'],
                                  'EMAIL'            => $arr_user['email'],
                                  'REMINDER_URL'      => $reminder_url,
                                  'PROJECT_NAME'     => config('app.project.name'),
                                  'SCHOOL_ADMIN'     => 'School Admin'];

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_slug'] = 'forget_password';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_user;

            return $arr_mail_data;
        }
        return FALSE;
    }

    /************* Get information of particular user ******************/
    public function get_user_details($email)
    {
        $credentials = ['email' => $email];
        $user = $this->UserModel->where('email',$email)->first(); 

        if($user)
        {
          return $user;
        }
        return FALSE;
    }

    /*************** check Reset password link is correct or not ***************/
    public function validate_reset_password_link($enc_id, $enc_reminder_code)
    {
        $user_id       = base64_decode($enc_id);

        $user = Sentinel::findById($user_id);

        if(!$user)
        {
          Flash::error('Invalid User Request');
          return redirect()->back();
        }

        if(Reminder::exists($user))
        {
          return view($this->module_view_folder.'.reset_password',compact('enc_id','enc_reminder_code'));
        }
        else
        {
          Flash::error('Reset Password Link Expired');
          return redirect()->back();
        }
    }

    /************ reset new password ***************/    
    public function reset_password(Request $request)
    {
        $arr_rules                      = array();
        $arr_rules['password']          = "required";
        $arr_rules['confirm_password']  = "required";
        $arr_rules['enc_id']            = "required";
        $arr_rules['enc_reminder_code'] = "required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
          return redirect()->back();
        }

        $enc_id            = $request->input('enc_id');
        $enc_reminder_code = $request->input('enc_reminder_code');
        $password          = $request->input('password');
        $confirm_password  = $request->input('confirm_password');

        if($password  !=  $confirm_password )
        {
          Flash::error('Passwords Do Not Match.');
          return redirect(url('/').'/login');
        }

        $user_id       = base64_decode($enc_id);
        $reminder_code = base64_decode($enc_reminder_code);

        $user = Sentinel::findById($user_id);

        if(!$user)
        {
          Flash::error('Invalid User Request');
          return redirect(url('/').'/login');
        }

        if (Reminder::complete($user, $reminder_code, $password))
        {
          Flash::success('Password reset successfully');
          return redirect(url('/').'/login');
        }
        else
        {
          Flash::error('Reset Password Link Expired');
          return redirect(url('/').'/login');
        }
    }

   

    /************ logout process **************/
    public function logout()
    {   
        $lang =  \Session::get('locale');
        \Session::flush();
        Sentinel::logout();
        \Session::put('locale',$lang);
        return redirect(url('/').'/login');
    }

    public function login_process(Request $request)
    {
        $school_id = $request->input('school_id');

        $school_id = isset($school_id) ? $school_id : '0' ;
        \Session::set('school_id', $school_id );

        $academic_year_id = 0 ;
        $current_year = date('Y');
        $previous_year = $current_year - 1;
        $next_year = $current_year + 1;
        $previous_acdemic_year = $previous_year.'-'.$current_year;
        $next_acdemic_year = $current_year.'-'.$next_year;

        $next_exist = AcademicYearModel::where('academic_year',$next_acdemic_year)->where('school_id',\Session::get('school_id'))->first();

        if(empty($next_exist))
        {
            $prvious_exist = AcademicYearModel::where('academic_year',$previous_acdemic_year)->where('school_id',\Session::get('school_id'))->first();
            if(!empty($prvious_exist))
            {
                $current_date = date('Y-m-d');
                $current_date = date_create($current_date);
                $to_date = date_create($prvious_exist->end_date);
                $date_diff = date_diff($to_date,$current_date);
                $url = config('app.project.role_slug.school_admin_role_slug').'/academic_year/create';
                if($date_diff->format('%R%a') > 0)
                {   
                    
                }
                else
                {
                    $academic_year_id = $prvious_exist->id;
                }
            }
            else
            {
                
            }
        }
        else
        {
           $academic_year_id = $next_exist->id; 
        }   
        
        \Session::put('academic_year',$academic_year_id);
        return redirect(url(config('app.project.role_slug.parent_role_slug').'/dashboard'));
    }

    public function role_login(Request $request)
    {
        $user = Sentinel::getUser();

        if(config('app.project.role_slug.parent_role_slug')== $request->role)
        {
            $school = $this->SchoolParentModel->select('school_id')->where('parent_id',$user->id)->where('is_active',1)->get();
            
            if(count($school)!=0 && count($school)>1)
            {
                $obj_data = $data = [];
                $school = $school->toArray();
                
                foreach ($school as $key => $sch) {
                    
                    $obj_data['school_id'] = $sch['school_id'];
                    $obj_data['name']      = $this->CommonDataService-> get_school_name($sch['school_id']);
                    $data[$key]            = $obj_data;
                }

                $this->arr_view_data['arr_school']   = $data;
                $this->arr_view_data['theme_color']  = $this->theme_color;
                $this->arr_view_data['locale']       = $request->locale;
                
                return view($this->module_view_folder.'.school_list',$this->arr_view_data);
            }
            else
            {      
                $school_id = isset($school[0]['school_id']) ? $school[0]['school_id'] : '0' ;
                \Session::set('school_id', $school_id );

                $academic_year_id = 0 ;
                $current_year = date('Y');
                $previous_year = $current_year - 1;
                $next_year = $current_year + 1;
                $previous_acdemic_year = $previous_year.'-'.$current_year;
                $next_acdemic_year = $current_year.'-'.$next_year;

                $next_exist = AcademicYearModel::where('academic_year',$next_acdemic_year)->where('school_id',\Session::get('school_id'))->first();
                if(empty($next_exist))
                {
                    $prvious_exist = AcademicYearModel::where('academic_year',$previous_acdemic_year)->where('school_id',\Session::get('school_id'))->first();
                    if(!empty($prvious_exist))
                    {

                        $current_date = date('Y-m-d');
                        $current_date = date_create($current_date);
                        $to_date = date_create($prvious_exist->end_date);
                        $date_diff = date_diff($to_date,$current_date);
                        $url = config('app.project.role_slug.school_admin_role_slug').'/academic_year/create';
                        if($date_diff->format('%R%a') > 0)
                        {   
                            
                        }
                        else
                        {
                            $academic_year_id = $prvious_exist->id;
                        }
                    }
                    else
                    {
                        
                    }
                }
                else
                {
                   $academic_year_id = $next_exist->id; 
                }   
                
                \Session::put('academic_year',$academic_year_id);

                return redirect(url(config('app.project.role_slug.parent_role_slug').'/dashboard'));
            }
        }    

        $status = $this->CommonDataService->validate_user($request->role,$user->id);
        
        if($status==1)
        {
            if($request->role == 'employee')
            {
                return redirect(url('/').'/'.config('app.project.role_slug.school_admin_role_slug').'/dashboard');    
            }
            return redirect(url('/').'/'.$request->role.'/dashboard');
        }
        elseif($status==0)
        {
            Flash::error('You have already left the school');
            return redirect(url(config('app.project.role_slug.school_admin_role_slug')));
        }
        
        if($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
        {
            $school = $this->SchoolAdminModel->where('user_id',$user->id)->first();
            if(isset($school->is_active) && $school->is_active==0){
                Flash::error('Your account is blocked by admin');
                return redirect()->back();       
            }  
            $school_id = isset($school->school_id) ? $school->school_id : '0' ;
            
            \Session::set('school_id', $school_id );
    
            return redirect(url(config('app.project.role_slug.school_admin_role_slug').'/dashboard'));
        }  
    }
}