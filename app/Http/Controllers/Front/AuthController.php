<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\EmailTemplateModel;
use App\Models\Usermodel;

use App\Common\Services\EmailService;

use Sentinel;
use Activation;
use Hash;
use Mail;	
use Session;
use Reminder;
use Socialize;
use Validator;
use Flash;

class AuthController extends Controller
{
	public function __construct(UserModel $user_model,
								EmailTemplateModel $email_template,
								EmailService $mail_service)
	{
		$this->UserModel 		  = $user_model;
		$this->BaseModel 		  = $this->UserModel;
		$this->EmailTemplateModel = $email_template;
		$this->EmailService       = $mail_service;

		/*$this->arr_view_data      = [];
        $this->module_title       = "Admin";
        $this->module_view_folder = "admin.auth";
        $this->admin_panel_slug   = config('app.project.admin_panel_slug');
        $this->module_url_path    = url($this->admin_panel_slug);*/
	}

 	public function store_user(Request $request)
 	{ 
	    $arr_response = [];

		$status = $this->validate_store_user($request);
		if(is_array($status))
		{
			return response()->json($status);
		}

	    $arr_data             	= [];

	    $arr_data['user_type']  = $request->input('user_type') ;
		$arr_data['first_name'] = $request->input('first_name') ;
		$arr_data['last_name']  = $request->input('last_name') ;
		$arr_data['email'] 	 	= $request->input('email') ;
		$arr_data['password']   = $request->input('password') ;

		if($request->input('user_type')=='photographer')
		{
			$arr_data['portfolio_link'] = $request->input('portfolio_link') ;
			$arr_data['address'] 		= $request->input('address') ;
			$arr_data['city']  			= $request->input('city') ;
			$arr_data['zipcode'] 	 	= $request->input('zipcode') ;
			$arr_data['state']   		= $request->input('state') ;		
			$arr_data['country']   		= $request->input('country') ;		
		}

	    $arr_data['via_social'] = '0' ;
	    $is_activate            = '0';
	  	$via_social 			= '0';

	  	$user_status = $this->register($arr_data,$via_social,$is_activate);  // register method

	  	if($user_status)
        {   
            $id   			 = $user_status->id;
            $user 			 = Sentinel::findById($id);
        	$activation 	 = Activation::create($user);   	/* Create avtivation */
            $activation_code = $activation->code; 				// get activation code
            $email_id        = $request->input('email');

            $data['id'] 		     = $id;
            $data['activation_code'] = $activation_code;
            $data['first_name'] 	 = $request->input('first_name');
            $data['email']           = $request->input('email');

            $arr_mail_data = $this->send_regular_registration_mail($data, $activation_code);
            $email_status  = $this->EmailService->send_mail($arr_mail_data);

	    	return ['status'=>'success','msg'=> 'Thank you. We have sent you an email with a confirmation link. Please click on the link to confirm your registration.'];
        }
        else
        {
        	return ['status'=>'error','msg'=> 'Problem occuered while registration.'];
        }

 	}

 	public function validate_store_user(Request $request)
 	{
	 	if ($this->is_email_available($request->input('email')) == false) 
	    {
	    	return ['status'=>'error','msg'=> 'This email is already registered.'];
	    }
	    else
	    {
	       if ($request->has('user_type') == false && $request->input('user_type') != 'user' && $request->input('user_type') != 'photographer')
	       {
	       		return ['status'=>'error','msg'=> 'Data is invalid. Please try again later.'];
	       }

	       if ($request->has('first_name') == false) 
	       {
	       		return ['status'=>'error','msg'=> 'Please enter first name.'];
	       }

	       if ($request->has('last_name') == false) 
	       {
	        	return ['status'=>'error','msg'=> 'Please enter last name.'];
	       }

	       if ($request->has('email') == false) 
	       {
	         	return ['status'=>'error','msg'=> 'Please enter email.'];
	       }

	       if ($request->has('password') == false) 
	       {
	        	return ['status'=>'error','msg'=> 'Please enter password.'];
	       }

	       if (strlen(trim($request->input('password'))) < 6) 
	       {
	    	    return ['status'=>'error','msg'=> 'Password length should be minimum 6 characters.'];
	       }

	       if ($request->has('confirm_password') == false) 
	       {
	        	return ['status'=>'error','msg'=> 'Please confirm password.'];
	       }
	      
	       if ($request->input('password') != $request->input('confirm_password')) 
	       {
	        	return ['status'=>'error','msg'=> 'Password and confirm password should be same.'];
	       }

	        // Check whether user is photographer or not
	        if($request->has('user_type') == true && $request->input('user_type') == 'photographer')
	        {
			  	if ($request->has('portfolio_link') == false) 
				{
				  	return ['status'=>'error','msg'=> 'Please enter portfolio link.'];
				}

				if ($request->has('address') == false) 
				{
				  	return ['status'=>'error','msg'=> 'Please enter address.'];
				}  	
				  
				if ($request->has('city') == false) 
				{
				  	return ['status'=>'error','msg'=> 'Please enter city.'];
				}

				if ($request->has('zipcode') == false) 
				{
					return ['status'=>'error','msg'=> 'Please enter zip code.'];
				}

				if ($request->has('state') == false) 
				{
				  	return ['status'=>'error','msg'=> 'Please enter state.'];
				}

				if ($request->has('country') == false) 
				{
				  	return ['status'=>'error','msg'=> 'Please enter country.'];
				}
			}
		}	   	
	    return true;
 	}

  	public function is_email_available($email = false/*, $role = false*/)
  	{
	    $response =  false;
	    if ($email != false)
	    {
	        $is_email_avail = Sentinel::createModel()
	                           ->where('email', $email)
	                           /*->whereHas('roles', function ($query) use($role) {
	                                                 // $query->where('slug', '=', $role);
	                                             })*/
	                           ->count();
	        if ($is_email_avail<=0)
	        {
	            $response = true;
	        }
	    }
	    return $response;
  	}

  	public function register($arr_data , $via_social = FALSE , $is_activate=FALSE)  // common register method
  	{	
    	if($is_activate == FALSE)
    	{	
            $arr_data['via_social'] = 0;
    		$user_status = Sentinel::register($arr_data);
    	} 
    	else 
    	{
    		$arr_data['is_active']  = 1;
    		$arr_data['via_social'] = 1;
     		$user_status = Sentinel::registerAndActivate($arr_data);
    	}

    	$user = Sentinel::findById($user_status->id);

        /* Attaching both Roles to user */
    	if(isset($arr_data['user_type']) && isset($arr_data['user_type'])=='user')
    	{
    		$role = Sentinel::findRoleBySlug('user');
			$user->roles()->attach($role);
    	}
   	 	else if(isset($arr_data['user_type']) && isset($arr_data['user_type'])=='photographer')
    	{
    		$role = Sentinel::findRoleBySlug('photographer');
			$user->roles()->attach($role);		
    	}
		return $user_status; 
  	}

  	public function send_regular_registration_mail($arr_data,$reminder_code)
    {
        // Retrieve Email Template 
        $obj_email_template = $this->EmailTemplateModel->where('id','4')->first();
        if($obj_email_template)
        {

        	$activation_url = '<p class="email-button"><a target="_blank" href=" '.url('/verify/'.base64_encode($arr_data['id']).'/'.$reminder_code ).'">Verify your account</a></p><br/>' ;

	        $arr_built_content = ['NAME'       		 => $arr_data['first_name'],
	                              'EMAIL'            => $arr_data['email'],
	                              'ACTIVATION_URL'   => $activation_url,
	                              'PROJECT_NAME'     => config('app.project.name')];


	        $arr_mail_data                      = [];
	        $arr_mail_data['email_template_id'] = '4';
	        $arr_mail_data['arr_built_content'] = $arr_built_content;
	        $arr_mail_data['user']              = ['email' => $arr_data['email'], 'first_name' => $arr_data['first_name']];

	        return $arr_mail_data;
        }  
    }

    public function verify($user_id, $activation_code1)
    {
        $id =base64_decode($user_id);
        $activation_code = $activation_code1;

        $user = Sentinel::findById($id);


        $activation = Activation::exists($user); // check if activation aleady done ...
        if($activation) // if activation is done
        {	 
            if (Activation::complete($user, $activation_code)) // complete an activation process
            {
                $tmp_user = $this->BaseModel->where('id',$id)->first();
                if($tmp_user)
                {
                    $tmp_user->is_active = 1;
                    $tmp_user->save();    
                }

                return response()->json(['status'=>'success','msg'=> 'Activation successful. Please login to your account']);
            }
            else
            {
                return response()->json(['status'=>'error','msg'=> 'Activation not found or not complete.']);
            }    
        }
        else // if user is trying activation first time ...
        {
                return response()->json(['status'=>'error','msg'=> 'Your account is already activated.']);
        }
    }

    public function process_forgot_password(Request $request)
    {

	    if ($request->has('email') == false) 
		{
		    return ['status'=>'error','msg'=> 'Please enter email.'];
		}

	    $email = $request->input('email');

	    $user  = Sentinel::findByCredentials(['email' => $email]);

	    if($user==null)
	    {
	      return ['status'=>'error','msg'=> 'Invalid Email Id.'];
	    }

	    if($user->inRole('admin')==true)
	    {
	      return ['status'=>'error','msg'=> 'We are unable to process this Email Id.'];
	    }

	    $reminder = Reminder::create($user);

	    $arr_mail_data = $this->built_forget_password_mail_data($email, $reminder->code); 
	    $email_status  = $this->EmailService->send_mail($arr_mail_data);
	    
	    return ['status'=>'success','msg'=> 'Password reset link sent successfully to your email id.'];
   }

    public function built_forget_password_mail_data($email, $reminder_code)
    {
	    $user = $this->get_user_details($email);

	    if($user)
	    {
	        $arr_user = $user->toArray();

	        $reminder_url = '<p class="email-button"><a target="_blank" href=" '.url('/validate_reset_password_link/'.base64_encode($arr_user['id']).'/'.base64_encode($reminder_code) ).'">Reset Password</a></p><br/>' ;

	        $arr_built_content = ['FIRST_NAME'       => $arr_user['first_name'],
	                              'EMAIL'            => $arr_user['email'],
	                              'REMINDER_URL'     => $reminder_url,
	                              'PROJECT_NAME'     => config('app.project.name')];


	        $arr_mail_data                      = [];
	        $arr_mail_data['email_template_id'] = '5';
	        $arr_mail_data['arr_built_content'] = $arr_built_content;
	        $arr_mail_data['user']              = $arr_user;

	        return $arr_mail_data;
	    }
	    return FALSE;
    }

    public function get_user_details($email)
    {
	    $credentials = ['email' => $email];
	    $user = Sentinel::findByCredentials($credentials); // check if user exists

	    if($user)
	    {
	      return $user;
	    }
	    return FALSE;
    }

    public function validate_reset_password_link($enc_id, $enc_reminder_code)
    {
	    $user_id       = base64_decode($enc_id);
	    $reminder_code = base64_decode($enc_reminder_code);

	    $user = Sentinel::findById($user_id);

	    if(!$user)
	    {
	    	return ['status'=>'error','msg'=> 'Invalid User Request.'];
	    }

	    if($reminder = Reminder::exists($user))
	    {
	    	return ['status'=>'success','msg'=> 'Success.'];

	        /*return view($this->module_view_folder.'.reset_password',compact('enc_id','enc_reminder_code'));*/
	    }
	    else
	    {
	    	return ['status'=>'error','msg'=> 'Reset Password Link Expired.'];
	    }
    }

    public function reset_password(Request $request)
    {
	  	if ($request->has('password') == false) 
	    {
	    	return ['status'=>'error','msg'=> 'Please enter password.'];
	    }

	    if (strlen(trim($request->input('password'))) < 6) 
	    {
	    	return ['status'=>'error','msg'=> 'Password length should be minimum 6 characters.'];
	    }

	    if ($request->has('confirm_password') == false) 
	    {
	    	return ['status'=>'error','msg'=> 'Please confirm password.'];
	    }
	  
	    if ($request->input('password') != $request->input('confirm_password')) 
	    {
	    	return ['status'=>'error','msg'=> 'Password and confirm password should be same.'];
	    }

	    if ($request->has('enc_id'))
	    {
	    	return ['status'=>'error','msg'=> 'Error while retreiving information.'];
	    }

	    if ($request->has('enc_reminder_code')) 
	    {
	    	return ['status'=>'error','msg'=> 'Error while retreiving information'];
	    }

		$enc_id            = $request->input('enc_id');
		$enc_reminder_code = $request->input('enc_reminder_code');
		$password          = $request->input('password');
		$confirm_password  = $request->input('confirm_password');

		$user_id       = base64_decode($enc_id);
		$reminder_code = base64_decode($enc_reminder_code);

		$user = Sentinel::findById($user_id);

		if(!$user)
		{
	    	return ['status'=>'error','msg'=> 'Invalid User Request'];
		}

		if ($reminder = Reminder::complete($user, $reminder_code, $password))
		{
	    	return ['status'=>'error','msg'=> 'Password reset successfully.'];
		    /*return redirect('/login');*/
		}
		else
		{
	    	return ['status'=>'error','msg'=> 'Reset Password Link Expired'];
		}
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
	      
	    $validator = Validator::make($request->all(),$arr_rules);
	    if($validator->fails())
	    {
	          return redirect()->back()->withErrors($validator)->withInput($request->all());
	    }

	    $user = Sentinel::check();

	    $credentials = [];
	    $credentials['password'] = $request->input('current_password');

	    if (Sentinel::validateCredentials($user,$credentials)) 
	    { 
	      $new_credentials = [];
	      $new_credentials['password'] = $request->input('new_password');

	      if(Sentinel::update($user,$new_credentials))
	      {
	        Flash::success('Password Change Successfully');
	      }
	      else
	      {
	        Flash::error('Problem Occurred, While Changing Password');
	      }
	    } 
	    else
	    {
	        Flash::error('Invalid Old Password');
	    }       
	      
	    return redirect()->back(); 
    }
}
