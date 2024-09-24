<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\EmployeeModel;
use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;
use App\Models\ProfessorModel;  
use App\Models\UserModel;  

use Validator;
use Flash;
use Sentinel;
use Reminder;
use URL;
use Mail;

use Session;
class AuthController extends Controller
{
	public function __construct(EmailService $mail_service,
                                EmployeeModel $employee,
                                ProfessorModel $professor_model,
                                UserModel $user,
                                CommonDataService $CommonDataService
                                )
    {
        $this->arr_view_data      = [];
        $this->module_title       = "Professor Admin";
        $this->module_view_folder = "professor.auth";
        $this->EmailService       = $mail_service;
        $this->CommonDataService  = $CommonDataService;
        $this->professor_panel_slug   = config('app.project.role_slug.professor_role_slug');
        $this->module_url_path    = url($this->professor_panel_slug);
        $this->EmployeeModel      = $employee;
        $this->ProfessorModel     = $professor_model;
        $this->UserModel          = $user;
   
        /*----------------Admin Panel Theme Color Helper----------------*/
        $this->theme_color = theme_color();
        /*--------------------------------------------------------------*/      
    }

    public function login()
    {
        $this->arr_view_data['module_title']     = $this->module_title." Login";
        $this->arr_view_data['theme_color']      = $this->theme_color;

        return view($this->module_view_folder.'.login',$this->arr_view_data);
    }

    /*---------------------------------
    process_login() : Validate Login
    Auther : Amol 
    Date : 
    ---------------------------------*/

   public function process_login(Request $request)
    {   
        \Session::flush();     
        $validator = Validator::make($request->all(), [
            'email'    => 'required|max:255',
            'password' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return redirect(config('app.project.role_slug.professor_role_slug').'/login')
                      ->withErrors($validator)
                      ->withInput($request->all());
        }

        $credentials = [
                            'email'    => $request->input('email'),
                            'password' => $request->input('password'),
                       ];
        try 
        {
            $check_authentication = Sentinel::authenticate($credentials);
            if($check_authentication)
            {
                $user = Sentinel::check();

                
                if($user->inRole(config('app.project.role_slug.professor_role_slug')))
                {   
                    $professor = $this->ProfessorModel->select('school_id')->where('user_id',$user->id)->where('has_left',0)->first();
                    if(isset($professor->school_id)){
                        \Session::set('school_id', $professor->school_id );    
                        return redirect(url(config('app.project.role_slug.professor_role_slug').'/dashboard'));
                    }
                    else{
                        Flash::error('You have already left the school');
                        return redirect(url(config('app.project.role_slug.professor_role_slug')));
                    }
                    
                }
                else
                {
                    Flash::error('Not Sufficient Privileges');
                    return redirect()->back();
                }
            }
            else
            {
                Flash::error('Invalid Login Credential');
                return redirect()->back();
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

    }  
    public function change_password()
    {
        $this->arr_view_data['page_title']      = translation('change_password');
        $this->arr_view_data['module_title']    = translation('change_password');
        $this->arr_view_data['module_url_path'] = $this->module_url_path.'/change_password';
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.change_password',$this->arr_view_data);    
    }

    /*---------------------------------
    update_password() : Update Password
    Auther : Amol 
    Date : 
    ---------------------------------*/

    public function update_password(Request $request)
    {
        $arr_rules                     = array();
        $arr_rules['current_password'] = "required";
        $arr_rules['new_password']     = "required|confirmed";
        $arr_rules['new_password_confirmation']      = "required|same:new_password";
      
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


        $current_password  = $request->input('current_password');
        $new_password  = $request->input('new_password');
        $new_password_confirmation = $request->input('new_password_confirmation');

        $credentials = [];
        $credentials['password'] = $request->input('current_password');
        
        try 
        {
            $user = Sentinel::check();

            if (Sentinel::validateCredentials($user,$credentials)) 
            {  

                if($current_password!=$new_password)
                {
                    if($new_password == $new_password_confirmation)
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
                }
                else
                {
                     Flash::error('Sorry you can\'t use current password as new password, Please enter another password');
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

     /*---------------------------------
    process_forgot_password() :
    Auther : Amol 
    Date : 
    ---------------------------------*/

    public function process_forgot_password(Request $request)
    {
        $arr_rules['email']      = "required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
          Flash::error('Please enter valid email_id');
          return redirect()->back()->withErrors($validator)->withInput();
        }

        $email = $request->input('email');

        $user  = Sentinel::findByCredentials(['email' => $email]);

        if($user==null)
        {
            Flash::error("Invaild Email Id");
            return redirect()->back();
        }

        if(!$user->inRole(config('app.project.role_slug.professor_role_slug')))
        {
            Flash::error('We are unable to process this Email Id');
            return redirect()->back();
        }

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

    public function built_mail_data($email, $reminder_code)
    {
        $user = $this->get_user_details($email);

        if($user)
        {
            $arr_user = $user->toArray();
            $reminder_url = '<p class="email-button"><a target="_blank" href="'.URL::to($this->professor_panel_slug.'/validate_admin_reset_password_link/'.base64_encode($arr_user['id']).'/'.base64_encode($reminder_code) ).'">Reset Password</a></p><br/>' ;

            $arr_built_content = ['FIRST_NAME'       => $arr_user['user_details']['first_name'],
                                  'EMAIL'            => $arr_user['email'],
                                  'REMINDER_URL'     => $reminder_url,
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

    public function get_user_details($email)
    {
        $credentials = ['email' => $email];
        /*$user = Sentinel::findByCredentials($credentials);*/ /*check if user exists*/
        $user = $this->UserModel->with('user_details')->where('email',$email)->first();
        if($user)
        {
          return $user;
        }
        return FALSE;
    }

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
          return redirect()->back();
        }

        $user_id       = base64_decode($enc_id);
        $reminder_code = base64_decode($enc_reminder_code);

        $user = Sentinel::findById($user_id);


        if(!$user)
        {
          Flash::error('Invalid User Request');
          return redirect()->back();
        }

        if ( Reminder::complete($user, $reminder_code, $password))
        {
          Flash::success('Password reset successfully');
          return redirect($this->professor_panel_slug.'/login');
        }
        else
        {
          Flash::error('Reset Password Link Expired');
          return redirect()->back();
        }
    }

    public function change_first_time()
    {
        $user = Sentinel::check();
        $user->first_time_login = date('Y-m-d H:i:s');
        $user->save();
        return;
    }    

    public function logout()
    {   
         $lang =  \Session::get('locale');
        \Session::flush();
        Sentinel::logout();
        \Session::put('locale',$lang);
        return redirect(url('/').'/login');
    }
   
}
