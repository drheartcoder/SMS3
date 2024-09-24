<?php

namespace App\Http\Middleware\Student;

use Closure;
use Session;
use Sentinel;

use App\Models\RoleModel;
use App\Models\NotificationModel;
use App\Models\StudentModel;
class GeneralMiddleware
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
        
        $user = Sentinel::check();
        
       \App::setlocale(Session::get('locale'));        
        view()->share('student_panel_slug',config('app.project.student_panel_slug'));
        view()->share('footer_name',config('app.project.name'));
        view()->share('arr_current_user_access',$this->current_user_access($request));
        
         if($user)
        {
            
            view()->share('schooladminNotificationCount',$this->getNotificationSchooladmin($user->id));
            view()->share('professorNotificationCount',$this->getNotificationProfessor($user->id));
          
        }

        return $next($request);
    }

    public function current_user_access()
    {
        $data =[];
        $user = Sentinel::check();
        if($user)
        {
            if($user->inRole(config('app.project.role_slug.student_role_slug')))
            {
                $user_role = RoleModel::select('permissions')->where('slug','student')->first() ;
                if($user_role)
                {
                   $data = json_decode($user_role->permissions);
                }
            }
        }
        
        return $data;
    }

    function getNotificationSchooladmin($user_id)
    {   

        return $this->getUsersUnReadNotification(config('app.project.role_slug.school_admin_role_slug'),$user_id);
        
    }

    

    function getNotificationProfessor($user_id)
    {   
        return  $this->getUsersUnReadNotification(config('app.project.role_slug.professor_role_slug'),$user_id);
         
    }

    
    function getUsersUnReadNotification($userType,$user_id)
    {   
        $count = 0;
        $user_roles = [
                        config('app.project.role_slug.canteen_manager_role_slug'),
                        config('app.project.role_slug.canteen_staff_role_slug'),
                        config('app.project.role_slug.canteen_supervisor_role_slug'),
                        config('app.project.role_slug.driver_role_slug')
                      ];
        if($userType)
        {
           
                $count = NotificationModel::
                                    where('user_type','=',$userType)
                                    ->where('to_user_id','=',$user_id)
                                    ->where('school_id','=',\Session::get('school_id'))
                                    ->where('is_read','=',0)
                                    ->count();
            
        }
        return $count;

    }
}
