<?php

namespace App\Http\Middleware\Professor;

use App\Models\ProfessorModel;

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

        $professor_path = config('app.project.role_slug.professor_role_slug');
        $arr_except[] =  $professor_path;
        $arr_except[] =  $professor_path.'/login';
        $arr_except[] =  $professor_path.'/process_login';
        $arr_except[] =  $professor_path.'/forgot_password';
        $arr_except[] =  $professor_path.'/process_forgot_password';
        $arr_except[] =  $professor_path.'/validate_admin_reset_password_link';
        $arr_except[] =  $professor_path.'/reset_password';
        $arr_except[] =  $professor_path.'/email_change';
        $arr_except[] =  $professor_path.'/process_change_email';
        view()->share('professor_panel_slug',config('app.project.professor_panel_slug'));
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
                $professor = ProfessorModel::select('is_active','language','professor_no')->where('user_id',$user->id)->where('has_left',0)->first();
                if(empty($professor)){
                        Flash::error('Invalid Login');
                        return redirect(url(config('app.project.role_slug.professor_role_slug')));
                }
                if($user->inRole(config('app.project.role_slug.professor_role_slug')) )
                {   
                    if($professor->is_active == '1')
                    {
                        if(!Session::has('user_no')){
                            
                            if($professor->professor_no != '')
                            {
                                 Session::put('user_no',$professor->professor_no);
                            }
                        }

                        if(!Session::has('locale')){
                            
                            if($professor->language != '')
                            {
                                 Session::put('locale',$professor->language);
                            }
                            else
                            {
                                Session::put('locale','en');    
                            }
                        }
                        \App::setlocale(Session::get('locale'));
                        
                        if(\Request::segment(3)=='edit' || \Request::segment(3)=='delete' || \Request::segment(3)=='create' || \Request::segment(4)=='store' || \Request::segment(4)=='update')
                        {
                            if(Session::has('academic_year') && Session::has('current_academic_year') && (Session::get('academic_year') != Session::get('current_academic_year')))  
                            {
                                Flash::error('you are not allowd to edit or delete the records of previous academic year');
                                return redirect()->back();  
                              
                            }
                        }  
                        return $next($request);  

                    }
                    else
                    {
                        Sentinel::logout();
                        Session::flush();
                        Flash::error('Your Account Blocked By Admin.');
                        return redirect(url(config('app.project.role_slug.professor_role_slug')));
                    }
                }
                else
                {   
                    Sentinel::logout();
                    Session::flush();   
                    Flash::error('Your Session is expired');
                    return redirect(url(config('app.project.role_slug.professor_role_slug')));
                } 

                if($user->inRole(config('app.project.role_slug.professor_role_slug')))
                {
                    $arr_data = $user->toArray();
                    view()->share('arr_auth_user',$arr_data);
                    view()->share('profile_image_public_img_path',$profile_image_public_img_path);
                   

                    return $next($request);    
                }
                else
                {
                    return redirect('/professor');
                }    
            }
            else
            {
                return redirect('/professor');
            }
            
        }
        else
        {
            return $next($request); 
        }
    }
}
