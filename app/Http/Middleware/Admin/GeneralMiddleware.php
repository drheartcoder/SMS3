<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Session;
use Sentinel;


use App\Models\NotificationModel;
use App\Models\SchoolRoleModel;
use App\Models\RoleModel;

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
        if(!Session::has('locale')){
            Session::put('locale','en');
        }
        \App::setlocale(Session::get('locale'));
        view()->share('admin_panel_slug',config('app.project.admin_panel_slug'));
        view()->share('arr_current_user_access',$this->current_user_access($request));
        view()->share('employeeNotificationCount',$this->getNotificationEmployee());
        view()->share('schooladminNotificationCount',$this->getNotificationSchooladmin());
        view()->share('professorNotificationCount',$this->getNotificationProfessor());
        view()->share('studentNotificationCount',$this->getNotificationStudent());
        view()->share('parentNotificationCount',$this->getNotificationParent()); 
 
       return $next($request);
    }
    
    public function current_user_access()
    {
        $data =[];
        
        $user = RoleModel::select('permissions')->where('slug','admin')->first() ;
        if($user)
        {
           $data = json_decode($user->permissions);
        }
        return  $data;
    }
    function getNotificationCount()
    {   
        
         $notification_count = NotificationModel::where('user_type','=','admin')->where('is_read','=','0')->count();
         return $notification_count;
    }

    function getNotificationEmployee()
    {   
        
        $notification_count =  $this->getUsersUnReadNotification(config('app.project.role_slug.employee_role_slug'));
         return $notification_count;
    }

    function getNotificationSchooladmin()
    {   

        $notification_count =  $this->getUsersUnReadNotification(config('app.project.role_slug.school_admin_role_slug'));
        
        return $notification_count;
    }

    function getNotificationParent()
    {   
        $notification_count =  $this->getUsersUnReadNotification(config('app.project.role_slug.parent_role_slug'));
        return $notification_count;
    }

    function getNotificationStudent()
    {   
          $notification_count =  $this->getUsersUnReadNotification(config('app.project.role_slug.student_role_slug'));
         return $notification_count;
    }

    function getNotificationProfessor()
    {   
          $notification_count =  $this->getUsersUnReadNotification(config('app.project.role_slug.professor_role_slug'));
         return $notification_count;
    }

    function getAdminDetails()
    {
        $adminDetails = UserModel::select('first_name','last_name')->where('id','=','1')->first();
        $adminName = $adminDetails['first_name'].' '.$adminDetails['last_name'];
        
        return $adminName;
    }

    function getAdminEmail()
    {
        $adminDetails = UserModel::select('email')->where('id','=','1')->first();
        $adminEmail = $adminDetails['email'];
        return $adminEmail;
    }

    
    function getUsersUnReadNotification($userType)
    {   
        $count = 0;
        if($userType)
        {
            $count = NotificationModel::whereHas('user_details',function($q){})
                                    ->with('user_details')
                                    ->where('user_type','=',$userType)
                                    ->where('to_user_id','=',1)
                                    ->where('is_read','=',0)
                                    ->count();
        }
        return $count;

    }

}
