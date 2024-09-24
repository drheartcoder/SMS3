<?php

namespace App\Http\Middleware\Professor;

use Closure;
use Session;
use Sentinel;
use App\Models\NotificationModel;
use App\Models\AcademicYearModel;
use App\Models\SchoolRoleModel;
use App\Models\RoleModel;
use App\Models\EmployeeModel;
use App\Models\ClaimPermissionModel;
use App\Models\MessageModel;

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
        view()->share('professor_panel_slug',config('app.project.role_slug.professor_role_slug'));
        view()->share('getAcademicYear',$this->getAcademicYear()); 
        view()->share('arr_current_user_access',$this->current_user_access($request));
        view()->share('claim_module_access',$this->claim_module_access($request));
        view()->share('get_current_academic_year',$this->get_current_academic_id());   
        view()->share('footer_name',config('app.project.name'));
        if($user)
        {
            
            view()->share('schooladminNotificationCount',$this->getNotificationSchooladmin($user->id));
            view()->share('studentNotificationCount',$this->getNotificationStudent($user->id));
            view()->share('parentNotificationCount',$this->getNotificationParent($user->id));
            view()->share('employeeNotificationCount',$this->getNotificationEmployee($user->id));
            view()->share('unread_messages',$this->get_unread_messages());    
            view()->share('unread_message_count',$this->get_unread_message_count());
        }

        return $next($request);
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

        if(isset($obj->id))
        {
            
            $academic_year_id = $obj->id;
        }
        else
        {
            $obj =  AcademicYearModel::
                                where('school_id',Session::get('school_id'))
                                ->where('academic_year',$previous_acdemic_year)
                                ->first();

            if(isset($obj->id))
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

        return $academic_year_id;    
    }

    public function current_user_access()
    {
        $data =[];
        $user = Sentinel::check();
        if($user)
        {
            if($user->inRole(config('app.project.role_slug.professor_role_slug')))
            {
                $user_role = RoleModel::select('permissions')->where('slug',config('app.project.role_slug.professor_role_slug'))->first() ;
                if($user_role)
                {
                   $data = json_decode($user_role->permissions);
                }
            }
        }
        
        return $data;
    }

    public function claim_module_access()
    {

        $data ='';
        $obj_permission = ClaimPermissionModel::select('is_active')->where('school_id',\Session::get('school_id'))->first();
        
        if(isset($obj_permission) && !empty($obj_permission))
        {
            $data = $obj_permission->is_active;
        }
        else
        {
            $data = 0;
        }
        return $data;
    }

    function getNotificationSchooladmin($user_id)
    {   

        return $this->getUsersUnReadNotification(config('app.project.role_slug.school_admin_role_slug'),$user_id);
        
    }

    function getNotificationParent($user_id)
    {   
        return $this->getUsersUnReadNotification(config('app.project.role_slug.parent_role_slug'),$user_id);
        
    }

    function getNotificationStudent($user_id)
    {   
        return  $this->getUsersUnReadNotification(config('app.project.role_slug.student_role_slug'),$user_id);
         
    }

    function getNotificationEmployee($user_id)
    {   
        return  $this->getUsersUnReadNotification(config('app.project.role_slug.employee_role_slug'),$user_id);
         
    }

    function getUsersUnReadNotification($userType,$user_id)
    {   
        $count = 0;
        
        if($userType)
        {
            
                $count = NotificationModel::whereHas('user_details',function($q){})
                                            ->with('user_details')
                                            ->where('user_type',$userType)
                                            ->where('to_user_id','=',$user_id)
                                            ->where('school_id','=',\Session::get('school_id'))
                                            ->where('is_read','=',0)
                                            ->count();
            
        }

        return $count;

    }

    public function get_unread_message_count(){
        $user = Sentinel::getUser();
        $user_id = isset($user->id)?$user->id:0;
        $chat = MessageModel::where('to_user_id',$user_id)
                                ->where('school_id',Session::get('school_id'))
                                ->where('is_read',0)
                                ->count();

        return $chat;                        
    }

    public function get_unread_messages(){
        $user = Sentinel::getUser();
        $user_id = isset($user->id)?$user->id:0;

        $chat = MessageModel::where('to_user_id',$user_id)
                                ->whereHas('get_form_user_details',function(){})
                                ->with('get_form_user_details')
                                ->where('school_id',Session::get('school_id'))
                                ->where('is_read',0)
                                ->orderBy('id','DESC')
                                ->limit(5)
                                ->get();
        
        return $chat;                        
    }
}
