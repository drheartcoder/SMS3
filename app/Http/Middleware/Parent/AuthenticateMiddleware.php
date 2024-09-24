<?php

namespace App\Http\Middleware\Parent;

use App\Models\ParentModel;
use App\Models\StudentModel;
use App\Models\SchoolParentModel;
use App\Models\NotificationModel;
use Closure;
use Sentinel;
use Session;
use Flash;

use App\Models\LevelClassModel;

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

        $parent_path = config('app.project.role_slug.parent_role_slug');
        $arr_except[] =  $parent_path;
        $arr_except[] =  $parent_path.'/login';
        $arr_except[] =  $parent_path.'/process_login';
        $arr_except[] =  $parent_path.'/forgot_password';
        $arr_except[] =  $parent_path.'/process_forgot_password';
        $arr_except[] =  $parent_path.'/validate_admin_reset_password_link';
        $arr_except[] =  $parent_path.'/reset_password';
        $arr_except[] =  $parent_path.'/email_change';
        $arr_except[] =  $parent_path.'/process_change_email';
        $arr_except[] =  $parent_path.'/login_process';
        
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
            $SLUG = config('app.project.role_slug.parent_role_slug');
            if($user!=null)
            {   

                $schoolId    =  session('school_id');

               /* if(!Session::has('locale')){
                    Session::put('locale','en');
                }
                \App::setlocale(Session::get('locale'));*/
                if(isset($schoolId) && $schoolId!=''){

                    if($user->inRole(config('app.project.role_slug.parent_role_slug'))){

                        
                        /*$is_exists = SchoolParentModel::where('school_id','=',$schoolId)->where('parent_id','=',$user->id)->first();
*/                      
                        $is_exists = ParentModel::with('school_parent_details')->where('user_id','=',$user->id)->first();

                        
                        if(!empty($is_exists) && count($is_exists)>0){
                           
                                if($is_exists['school_parent_details']['is_active'] == 1){
                                    
                                    if($is_exists['parent_no']!='')
                                    {
                                        Session::put('user_no',$is_exists['parent_no']);
                                    }
                                    

                                    if($is_exists['school_parent_details']['language']!='')
                                    {
                                        Session::put('locale',$is_exists['school_parent_details']['language']);
                                    }
                                    else
                                    {
                                        Session::put('locale','en');
                                    }
                                    \App::setlocale(Session::get('locale'));
                                        
                                    /* Check studemt is in the school */
                                    $isChildExists = StudentModel::with(['get_user_details'])
                                                                   ->where('school_id','=',$schoolId)
                                                                   ->where('parent_id','=',$user->id)
                                                                   ->where('has_left','0')
                                                                   ->where('academic_year_id',\Session::get('academic_year'))
                                                                   ->get();
                                    
                                    if(count($isChildExists) > 0){
                                        $arr_children = $isChildExists->toArray();
                                        
                                        foreach($arr_children as $key=>$child){
                                            if(!(\Session::has('kid_id')))
                                            {
                                                 $resData = LevelClassModel::with('class_details')
                                                                                                    ->whereHas('level_details',function(){})
                                                                                                    ->whereHas('class_details',function(){})
                                                                                                    ->with('level_details')
                                                                                                    ->where('id',$child['level_class_id'])
                                                                                                    ->where('school_id',Session::get('school_id'))
                                                                                                    ->first();

                                                                               
                                                 if(isset($resData) && count($resData)>0){
                                                   
                                                     session()->put('student_id', $arr_children[$key]['id']);                                                   
                                                     session()->put('student_level', $resData->level_id);
                                                     session()->put('student_level_name', $resData->level_details->level_name);
                                                     session()->put('student_class', $resData->class_id);
                                                     session()->put('student_class_name', $resData->class_details->class_name);
                                                     session()->put('level_class_id', $arr_children[$key]['level_class_id']);
                                                     session()->put('kid_id', $arr_children[$key]['user_id']);     
                                                     break;
                                                 }
                                                 else{
                                                  unset($arr_children[$key]);    
                                                  continue;

                                                 }                                                   
                                                 

                                            }    
                                        }
                                        
                                        
                                        view()->share('parent_childern_arr',$arr_children);
                                        
                                        view()->share('schooladminNotificationCount',$this->getNotificationSchooladmin($user->id));

                                        view()->share('professorNotificationCount',$this->getNotificationProfessor($user->id));
                                        return $next($request);    
                                    }else{
                                        Sentinel::logout();
                                        Session::flush();
                                        Flash::error('No childern present in the selected school');
                                        return redirect(url($SLUG));    
                                    }
                                   

                                }else{
                                    Sentinel::logout();
                                    Session::flush();
                                    Flash::error('Your account is blocked by admin');
                                    return redirect(url($SLUG));
                                }

                                return $next($request);    
                        }else{

                            Sentinel::logout();
                            Session::flush();   
                            Flash::error('Parent is not registered with selected school');
                            return redirect(url($SLUG));
                        }
                    }
                    else
                    {   
                        Sentinel::logout();
                        Session::flush();   
                        Flash::error('Your Session is expired');
                        return redirect(url($SLUG));
                    } 

                }
                return redirect(url($SLUG));
               
            }else{
                return redirect(url($SLUG));
            }
            
        }else{
            return $next($request); 
        }
    }

    function getNotificationSchooladmin($userId){   

        $notification_count =  $this->getUsersUnReadNotification(config('app.project.role_slug.school_admin_role_slug'),$userId);
        
        return $notification_count;
    }
    
    function getNotificationProfessor($userId){   
          $notification_count =  $this->getUsersUnReadNotification(config('app.project.role_slug.professor_role_slug'),$userId);
         return $notification_count;
    }

    function getUsersUnReadNotification($userType,$userId)
    {   
        $count = 0;
        $toUserType = config('app.project.role_slug.parent_role_slug');

        if($userType)
        {
            $count = NotificationModel::where('user_type','=',$userType)
                                ->where('to_user_id','=',$userId)
                                ->where('school_id','=',\Session::get('school_id'))
                                ->where('is_read','=',0)
                                ->count();
        }
        return $count;

    }
}
