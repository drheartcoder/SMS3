<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\EmployeeModel;
use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;
use App\Models\StudentModel;
use App\Models\AcademicYearModel;

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
                                CommonDataService $CommonDataService
                                )
    {
        $this->arr_view_data      = [];
        $this->module_title       = "Student";
        $this->module_view_folder = "student.auth";
        $this->EmailService       = $mail_service;
        $this->student_panel_slug   = config('app.project.role_slug.student_role_slug');
        $this->module_url_path    = url($this->student_panel_slug);
        $this->EmployeeModel      = $employee;
        $this->CommonDataService  = $CommonDataService;
        $this->AcademicYearModel  = new AcademicYearModel();
   
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

    public function process_login(Request $request)
    {        
        $validator = Validator::make($request->all(), [
            'email'    => 'required|max:255',
            'password' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return redirect(config('app.project.role_slug.student_role_slug').'/login')
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
                $student = StudentModel::where('user_id',$user->id)->where('has_left',0)->first();
                
                $school_id = isset($student->school_id) ? $student->school_id :0;
                \Session::put('school_id',$school_id);
                $level_class_id = isset($student->level_class_id) ? $student->level_class_id :0 ;
                \Session::put('level_class_id',$level_class_id);
                $student_id = isset($student->id) ? $student->id :0 ;
                \Session::put('student_id',$student_id);

                $academic_year_id =0;
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
                
                if($user->inRole(config('app.project.role_slug.student_role_slug')))
                {   
                    return redirect(url(config('app.project.role_slug.student_role_slug').'/dashboard'));
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

        Flash::error('Something went wrong ! Please try again !');
        return redirect()->back();
                                                    
    }

    public function change_password()
    {
        $this->arr_view_data['page_title']      = $this->module_title." Change Password";
        $this->arr_view_data['module_title']    = $this->module_title." Change Password";
        $this->arr_view_data['module_url_path'] = $this->module_url_path.'/change_password';
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.change_password',$this->arr_view_data);    
    }


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

    public function logout()
    {   
        $lang =  \Session::get('locale');
        \Session::flush();
        Sentinel::logout();
        \Session::put('locale',$lang);
        return redirect(url('/').'/login');
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

        if(!$user->inRole(config('app.project.role_slug.student_role_slug')))
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

            $reminder_url = '<p class="email-button"><a target="_blank" href=" '.URL::to($this->student_panel_slug.'/validate_admin_reset_password_link/'.base64_encode($arr_user['id']).'/'.base64_encode($reminder_code) ).'">Reset Password</a></p><br/>' ;

            $arr_built_content = ['FIRST_NAME'       => $arr_user['first_name'],
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
        $user = Sentinel::findByCredentials($credentials); /*check if user exists*/

        if($user)
        {
          return $user;
        }
        return FALSE;
    }

}
