<?php

namespace App\Http\Middleware\Student;

use App\Models\StudentModel;

use Closure;
use Sentinel;
use Session;
use Flash;

class AuthenticateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $arr_except = array();

        $student_path = config('app.project.role_slug.student_role_slug');
        $arr_except[] =  $student_path;
        $arr_except[] =  $student_path.'/login';
        $arr_except[] =  $student_path.'/process_login';
        $arr_except[] =  $student_path.'/forgot_password';
        $arr_except[] =  $student_path.'/process_forgot_password';
        $arr_except[] =  $student_path.'/validate_admin_reset_password_link';
        $arr_except[] =  $student_path.'/reset_password';
        $arr_except[] =  $student_path.'/email_change';
        $arr_except[] =  $student_path.'/process_change_email';

       
        $profile_image_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        /*-----------------------------------------------------------------
            Code for {enc_id} or {extra_code} in url
        ------------------------------------------------------------------*/
        $request_path = $request->route()->getCompiled()->getStaticPrefix();
        $request_path = substr($request_path,1,strlen($request_path));
        
        /*-----------------------------------------------------------------
                End
        -----------------------------------------------------------------*/        

        if(!in_array($request_path, $arr_except))
        { 
            $user = Sentinel::check();

            
            if($user!=null)
            {
                 
                $student = StudentModel::select('is_active','language','student_no')->where('user_id',$user->id)->where('academic_year_id',Session::get('academic_year'))->first();
                
                    if($student)
                    {
               
                        if($user->inRole(config('app.project.role_slug.student_role_slug')))
                        {   
                            if($student->is_active == '1')
                            {
                                if(!Session::has('user_no')){
                                    if($student->student_no != '')
                                    {
                                         Session::put('user_no',$student->student_no);
                                    }
                                }

                                if(!Session::has('locale')){
                                   
                                    if($student->language != '')
                                    {
                                         Session::put('locale',$student->language);
                                    }
                                    else
                                    {
                                        Session::put('locale','en');    
                                    }
                                }
                                \App::setlocale(Session::get('locale'));
                                return $next($request);    
                            }
                            else
                            {
                                Sentinel::logout();
                                Session::flush();
                                Flash::error('Your Account Blocked By Admin.');
                                return redirect(url(config('app.project.role_slug.student_role_slug')));
                            }
                        }
                        else
                        {   
                            Sentinel::logout();
                            Session::flush();   
                            Flash::error('Your Session is expired');
                            return redirect(url(config('app.project.role_slug.student_role_slug')));
                        } 

                        if($user->inRole(config('app.project.role_slug.student_role_slug')))
                        {
                            $arr_data = $user->toArray();
                            view()->share('arr_auth_user',$arr_data);
                            view()->share('profile_image_public_img_path',$profile_image_public_img_path);
                           

                            return $next($request);    
                        }
                        else
                        {
                            return redirect('/student');
                        }    
                    }
                    else
                    {
                        Sentinel::logout();
                        Session::flush();   
                        Flash::error('Access denied');
                        return redirect(url(config('app.project.role_slug.student_role_slug')));
                    }
                }
            
        }
        else
        {
            return $next($request); 
        }
    }
}
