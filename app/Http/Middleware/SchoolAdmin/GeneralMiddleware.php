<?php

namespace App\Http\Middleware\SchoolAdmin;

use App\Models\NotificationModel;
use App\Models\SchoolRoleModel;
use App\Models\RoleModel;
use App\Models\EmployeeModel;
use App\Models\AcademicYearModel;
use App\Models\SchoolAdminModel;
use App\Models\UserRoleModel;
use Closure;
use Session;
use Sentinel;

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
        
         $user = Sentinel::check();
        \App::setlocale(Session::get('locale'));
        view()->share('school_admin_panel_slug',config('app.project.role_slug.school_admin_role_slug'));
        view()->share('arr_current_user_access',$this->current_user_access($request));
        view()->share('terms_and_conditions',$this->terms_and_conditions());
        
        if($user)
        {
            if($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
            {       
                view()->share('role',config('app.project.role_slug.school_admin_role_slug'));               
            }
            else
            {
                view()->share('role',config('app.project.role_slug.employee_role_slug'));                    
            }
            view()->share('adminNotificationCount',$this->getNotificationAdmin($user->id));
            view()->share('professorNotificationCount',$this->getNotificationProfessor($user->id));
            view()->share('studentNotificationCount',$this->getNotificationStudent($user->id));
            view()->share('parentNotificationCount',$this->getNotificationParent($user->id));
            if($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
            {       
                view()->share('role',config('app.project.role_slug.school_admin_role_slug'));         
                view()->share('employeeNotificationCount',$this->getNotificationEmployee($user->id));        
            }
            else
            {
                view()->share('role',config('app.project.role_slug.employee_role_slug'));         
                view()->share('schoolAdminNotificationCount',$this->getNotificationSchoolAdmin($user->id));           
            }
            view()->share('getAcademicYear',$this->getAcademicYear()); 
            view()->share('get_current_academic_year',$this->get_current_academic_id());   
        }
 
       return $next($request);
    }
    public function terms_and_conditions()
    {

        $terms_and_condition_flag = 0;
        $user = Sentinel::check();
        if($user)
        {
            if($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
            {
                if($user->first_time_login == NULL)
                {
                    $terms_and_condition_flag = 1;    
                }
            }
            elseif($user->inRole(config('app.project.role_slug.technical_role_slug')))
            {
                $terms_and_condition_flag = 0;       
            }
        }
        return $terms_and_condition_flag;
    }

    public function current_user_access()
    {
        $data =[];
        $user = Sentinel::check();
        if($user)
        {
            $role = UserRoleModel::with('role_details')->where('user_id',$user->id)->get();
                
            if($role)
            {
                $role = $role->toArray();
            }

            $count = $count1 = 0;
            foreach ($role as $key => $value) 
            {
                if($value['role_details']['slug']==config('app.project.role_slug.parent_role_slug'))
                {
                    $count++;
                }
                if($value['role_details']['slug']==config('app.project.role_slug.professor_role_slug'))
                {
                    $count1++;
                }
                if($value['role_details']['slug']==config('app.project.role_slug.student_role_slug'))
                {
                    $count1++;
                }
            }
            if($count>0)
            {

                if($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
                {
                    $user_role = RoleModel::select('permissions')->where('slug','school_admin')->first() ;
                    if($user_role)
                    {
                       $data = json_decode($user_role->permissions);
                    }
                }
                else
                { 
                    $school = EmployeeModel::select('school_id','user_role')->where('user_id',$user->id)->first();
                    if($school)
                    {
                        $role = RoleModel::select('id')->where('slug',$school->user_role)->first();    
                        $user_role = SchoolRoleModel::select('permissions')->where('role_id',$role->id)->where('school_id',$school->school_id)->first() ;
                        if($user_role)
                        {
                           $data = json_decode($user_role->permissions);
                        }
                    }
                }
            }
            if($count==0 && $count1==0)
            {
                if($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
                {
                    $user_role = RoleModel::select('permissions')->where('slug','school_admin')->first() ;
                    if($user_role)
                    {
                       $data = json_decode($user_role->permissions);
                    }
                }
                else
                { 
                    $school = EmployeeModel::select('school_id','user_role')->where('user_id',$user->id)->first();
                    if($school)
                    {
                        $role = RoleModel::select('id')->where('slug',$school->user_role)->first();    
                        $user_role = SchoolRoleModel::select('permissions')->where('role_id',$role->id)->where('school_id',$school->school_id)->first() ;
                        if($user_role)
                        {
                           $data = json_decode($user_role->permissions);
                        }
                    }
                }
            }
            /*if(!$user->inRole(config('app.project.role_slug.student_role_slug')) && !$user->inRole(config('app.project.role_slug.parent_role_slug')) && !$user->inRole(config('app.project.role_slug.professor_role_slug')))
            {
                if($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
                {
                    $user_role = RoleModel::select('permissions')->where('slug','school_admin')->first() ;
                    if($user_role)
                    {
                       $data = json_decode($user_role->permissions);
                    }
                }
                else
                { 
                    $school = EmployeeModel::select('school_id','user_role')->where('user_id',$user->id)->first();
                    $role = RoleModel::select('id')->where('slug',$school->user_role)->first();
                    $user_role = SchoolRoleModel::select('permissions')->where('role_id',$role->id)->where('school_id',$school->school_id)->first() ;
                    if($user_role)
                    {
                       $data = json_decode($user_role->permissions);
                    }
                }
            }*/
        }
        return $data;
    }
    function getNotificationCount()
    {   
        
         return NotificationModel::where('user_type','=','admin')->where('is_read','=','0')->count();

    }


    function getNotificationAdmin($user_id)
    {   

        return $this->getUsersUnReadNotification(config('app.project.role_slug.admin_role_slug'),$user_id);
        
    }

    function getNotificationParent($user_id)
    {   
        return $this->getUsersUnReadNotification(config('app.project.role_slug.parent_role_slug'),$user_id);
        
    }

    function getNotificationStudent($user_id)
    {   
        return  $this->getUsersUnReadNotification(config('app.project.role_slug.student_role_slug'),$user_id);
         
    }

    function getNotificationProfessor($user_id)
    {   
        return  $this->getUsersUnReadNotification(config('app.project.role_slug.professor_role_slug'),$user_id);
         
    }

    function getNotificationEmployee($user_id)
    {   
          return $this->getUsersUnReadNotification(config('app.project.role_slug.employee_role_slug'),$user_id);
         
    }

    function getNotificationSchoolAdmin($user_id)
    {
         return $this->getUsersUnReadNotification(config('app.project.role_slug.school_admin_role_slug'),$user_id);
    }

    function getAdminDetails()
    {
        $adminDetails = UserModel::select('first_name','last_name')->where('id','=','1')->first();
        return $adminDetails['first_name'].' '.$adminDetails['last_name'];
    }

    function getAdminEmail()
    {
        $adminDetails = UserModel::select('email')->where('id','=','1')->first();
        return $adminDetails['email'];
    }

    function getAcademicYear()
    {
        $arr_data =[];

        $academic_year = AcademicYearModel::where('school_id',\Session::get('school_id'))->get();
        if($academic_year)
        {
            $arr_data = $academic_year->toArray();
        }
        return $arr_data;
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
            /*dd($count);*/
        }
        return $count;

    }
    public function get_current_academic_id()
    {
        $current_year = date('Y');
        $previous_year = $current_year - 1;
        $next_year = $current_year + 1;
        $previous_acdemic_year = $previous_year.'-'.$current_year;
        $next_acdemic_year = $current_year.'-'.$next_year;
        $academic_year_id = 0; 
        
        $obj = AcademicYearModel::
                                 where('school_id',Session::get('school_id'))
                                ->where('academic_year',$next_acdemic_year)
                                ->first();

        if(isset($obj->id) && count($obj->id)>0)
        {
            
            $academic_year_id = $obj->id;
        }
        else
        {
            $obj =  AcademicYearModel::
                                where('school_id',Session::get('school_id'))
                                ->where('academic_year',$previous_acdemic_year)
                                ->first();

            if(isset($obj->id)  && count($obj->id)>0)
            {
            
                $academic_year_id = $obj->id;
            }
           
        }       

        if(\Session::has('academic_year') && \Session::get('academic_year')=='0')
        {
            \Session::put('academic_year',$academic_year_id);
        }
        if(!\Session::has('academic_year'))
        {
            \Session::put('academic_year',$academic_year_id);
        }
        \Session::put('current_academic_year',$academic_year_id);

        $start_date = '';
        $end_date = '';
        if(\Session::get('academic_year')!=0){
            $obj =  AcademicYearModel::
                                where('id',\Session::get('academic_year'))
                                ->first();
            if(isset($obj->start_date) && isset($obj->end_date)){
                $start_date = $obj->start_date;
                $end_date = $obj->end_date;  
                if(\Session::has('start_date')){
                    \Session::forget('start_date');
                }                  
                if(\Session::has('end_date')){
                    \Session::forget('end_date');
                }
                \Session::put('start_date',$start_date);
                \Session::put('end_date',$end_date);    
            }
            
        }
    
        return $academic_year_id;    
    }

}
