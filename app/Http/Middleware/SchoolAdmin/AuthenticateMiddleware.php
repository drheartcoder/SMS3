<?php

namespace App\Http\Middleware\SchoolAdmin;

use Closure;
use Sentinel;
use Session;
use Flash;
use Cookie;

use App\Models\SchoolAdminModel;
use App\Models\EmployeeModel;
use App\Models\AcademicYearModel;


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
        view()->share('school_admin_panel_slug',config('app.project.school_admin_panel_slug'));
        $schooladmin_path = config('app.project.role_slug.school_admin_role_slug');
        $arr_except[] =  $schooladmin_path;
        $arr_except[] =  $schooladmin_path.'/login';
        $arr_except[] =  $schooladmin_path.'/process_login';
        $arr_except[] =  $schooladmin_path.'/login_process';
        $arr_except[] =  $schooladmin_path.'/forgot_password';
        $arr_except[] =  $schooladmin_path.'/process_forgot_password';
        $arr_except[] =  $schooladmin_path.'/validate_admin_reset_password_link';
        $arr_except[] =  $schooladmin_path.'/reset_password';
        $arr_except[] =  $schooladmin_path.'/email_change';
        $arr_except[] =  $schooladmin_path.'/process_change_email';
        $arr_except[] =  $schooladmin_path.'/role_login';

       
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
            $school_admin = '';
            if($user)
            {
                if(!$user->inRole(config('app.project.role_slug.student_role_slug'))&& !$user->inRole(config('app.project.role_slug.professor_role_slug')))
                {  
                    if($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
                    { 
                        $school_admin = SchoolAdminModel::select('is_active')->where('user_id',$user->id)->first();
                    }
                    else
                    {
                        $school_admin = EmployeeModel::select('is_active')->where('user_id',$user->id)->where('has_left',0)->first();
                    }
                    
                    if(isset($school_admin->is_active) && $school_admin->is_active == '1')
                    {
                        if($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
                        {

                            $school_admin = SchoolAdminModel::select('language')->where('user_id',$user->id)->first();
                        }
                        else
                        {
                            $school_admin = EmployeeModel::select('language','user_role')->where('user_id',$user->id)->first();    
                        }
                        
                        if($school_admin)
                        {
                            if(!Session::has('locale')){
                                            
                                if($school_admin->language != '')
                                {
                                     Session::set('locale',$school_admin->language);
                                     
                                     setcookie('locale',$school_admin->language, time()+(3600*24*2));

                                }
                                else
                                {
                                    Session::set('locale','fr');    
                                    setcookie('locale','fr', time()+(3600*24*2));
                                }
                            }


                            if(!Session::has('role'))
                            {
                                if($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
                                {
                                    Session::set('role','school_admin');
                                }
                                else
                                {
                                    Session::set('role',$school_admin->user_role);
                                }
                                
                            }

                        }
                        \App::setlocale(Session::get('locale'));
                        if(\Request::segment(2)=='academic_year' || \Request::segment(2)=='profile' || \Request::segment(2)=='change_password' || \Request::segment(2)=='logout' || \Request::segment(2)=='school' || \Request::segment(2)=='change_first_time')
                        {
                            return $next($request);    
                        }

                        if(\Request::segment(3)=='edit' || \Request::segment(3)=='delete'|| \Request::segment(2)=='payment' ||  \Request::segment(3)=='promote_students' || \Request::segment(3)== 'multi_action' || \Request::segment(3) == 'activate' || \Request::segment(3)== 'deactivate' || \Request::segment(3)=='create' || \Request::segment(4)=='store' || \Request::segment(4)=='update' || \Request::segment(4)=='store_issue_book' || \Request::segment(4)=='reissue_book')
                        {
                            if(Session::has('academic_year') && Session::has('current_academic_year') && (Session::get('academic_year') != Session::get('current_academic_year')))  
                            {
                                Flash::error('you are not allowd to edit or delete the records of previous academic year');
                                return redirect()->back();  
                              
                            }
                        }
                        
                        if(\Session::has('school_id') && \Session::get('school_id')!='0' && \Session::get('school_id')!='')
                        {
                            $url = config('app.project.role_slug.school_admin_role_slug').'/academic_year/create';
                            $current_year = date('Y');
                            $previous_year = $current_year - 1;
                            $next_year = $current_year + 1;
                            $previous_acdemic_year = $previous_year.'-'.$current_year;
                            $next_acdemic_year = $current_year.'-'.$next_year;

                            $next_exist = AcademicYearModel::where('academic_year',$next_acdemic_year)->where('school_id',\Session::get('school_id'))->count();
                            if($next_exist==0)
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
                                       Flash::error('Please set current academic year first.');
                                       return redirect(url($url));
                                    }
                                }
                                else
                                {
                                    Flash::error('Please set current academic year first.');
                                   return redirect(url(config('app.project.role_slug.school_admin_role_slug').'/academic_year/create'));
                                }
                            }
                        }
                        else
                        {
                            $url = config('app.project.role_slug.school_admin_role_slug').'/school';
                            Flash::error('Please set school profile first.');
                            return redirect(url($url));
                        }
                        return $next($request);    
                    }
                    else
                    {
                        Sentinel::logout();
                        Session::flush();
                        Flash::error('Your Account Blocked By Admin.');
                        return redirect(url(config('app.project.role_slug.school_admin_role_slug')));
                    }
                }
                else if($user->inRole(config('app.project.role_slug.technical_role_slug')))
                {   

                    $technical = EmployeeModel::where('user_id',$user->id)->where('has_left',0)->first();

                    if(isset($technical->is_active) && $technical->is_active == '1')
                    {   
                        if($technical)
                        {
                            if(!Session::has('locale')){
                                            
                                if($technical->language != '')
                                {
                                     Session::set('locale',$technical->language);
                                     setcookie('locale',$technical->language,time()+(3600*24*2));
                                }
                                else
                                {
                                    Session::set('locale','fr');    
                                    setcookie('locale','fr',time()+(3600*24*2));
                                }
                            }
                        }
                        
                        \App::setlocale(Session::get('locale'));
                        if(\Request::segment(2)=='academic_year' || \Request::segment(2)=='profile' || \Request::segment(2)=='change_password' || \Request::segment(2)=='logout' || \Request::segment(2)=='school' || \Request::segment(2)=='change_first_time')
                        {
                            return $next($request);    
                        }
                        if(\Request::segment(3)=='edit' || \Request::segment(3)=='delete'|| \Request::segment(2)=='payment' ||  \Request::segment(3)=='promote_students' || \Request::segment(3)== 'multi_action' || \Request::segment(3) == 'activate' || \Request::segment(3)== 'deactivate' || \Request::segment(3)=='create' || \Request::segment(4)=='store' || \Request::segment(4)=='update' || \Request::segment(4)=='store_issue_book' || \Request::segment(4)=='reissue_book')
                        {
                            if(Session::has('academic_year') && Session::has('current_academic_year') && (Session::get('academic_year') != Session::get('current_academic_year')))  
                            {
                                Flash::error('you are not allowd to edit or delete the records of previous academic year');
                                return redirect()->back();  
                              
                            }
                        }
                        
                        if(\Session::has('school_id') && \Session::get('school_id')!='0' && \Session::get('school_id')!='')
                        {
                            $url = config('app.project.role_slug.school_admin_role_slug').'/academic_year/create';
                            $current_year = date('Y');
                            $previous_year = $current_year - 1;
                            $next_year = $current_year + 1;
                            $previous_acdemic_year = $previous_year.'-'.$current_year;
                            $next_acdemic_year = $current_year.'-'.$next_year;

                            $next_exist = AcademicYearModel::where('academic_year',$next_acdemic_year)->where('school_id',\Session::get('school_id'))->count();
                            if($next_exist==0)
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
                                        Sentinel::logout();
                                        Session::flush();  
                                        Flash::error('Please set current academic year first.');
                                        return redirect(url(config('app.project.role_slug.school_admin_role_slug')));
                                    }
                                }
                                else
                                {
                                    Sentinel::logout();
                                    Session::flush();
                                    Flash::error('Please set current academic year first.');
                                    return redirect(url(config('app.project.role_slug.school_admin_role_slug')));
                                }
                            }
                            
                            
                        }
                        else
                        {
                            Sentinel::logout();
                            Session::flush();
                            Flash::error('Please set current academic year first.');
                            return redirect(url(config('app.project.role_slug.school_admin_role_slug')));
                        }
                        return $next($request);
                    }
                    else
                    {
                        Sentinel::logout();
                        Session::flush();
                        Flash::error('Your Account Blocked By Admin.');
                        return redirect(url(config('app.project.role_slug.school_admin_role_slug')));
                    }
                }

                else
                {   
                    Sentinel::logout();
                    Session::flush();   
                    Flash::error('Your Session is expired');
                    return redirect(url(config('app.project.role_slug.school_admin_role_slug')));
                } 

                if($user->inRole(config('app.project.role_slug.school_admin_role_slug')) || $user->inRole(config('app.project.role_slug.technical_role_slug')) || $user->inRole(config('app.project.role_slug.admin_role_slug')))
                {
                    
                    $arr_data = $user->toArray();
                    view()->share('arr_auth_user',$arr_data);
                    view()->share('profile_image_public_img_path',$profile_image_public_img_path);
                    view()->share('school_admin_panel_slug',config('app.project.role_slug.school_admin_role_slug'));

                    return $next($request);    
                }
                else
                {   view()->share('school_admin_panel_slug',config('app.project.role_slug.school_admin_role_slug'));
                    return redirect('/school_admin');
                }    
            }
            else
            {   view()->share('school_admin_panel_slug',config('app.project.role_slug.school_admin_role_slug'));
                return redirect('/school_admin');
            }
            
        }
        else
        {
            view()->share('school_admin_panel_slug',config('app.project.role_slug.school_admin_role_slug'));
            return $next($request); 
        }
    }

   
}
