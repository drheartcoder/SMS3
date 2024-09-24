<?php

namespace App\Http\Middleware\Parent;

use Closure;
use Session;
use Sentinel;

use App\Models\RoleModel;
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
        $user = Sentinel::getUser();
        view()->share('parent_panel_slug',config('app.project.role_slug.parent_role_slug'));
        view()->share('profile_image_public_img_path',url('/').config('app.project.img_path.user_profile_images'));
        view()->share('profile_image_base_img_path',base_path().config('app.project.img_path.user_profile_images'));
        view()->share('arr_current_user_access',$this->current_user_access($request));
        view()->share('claim_module_access',$this->claim_module_access($request));
        view()->share('footer_name',config('app.project.name'));
        
        if($user){
            view()->share('unread_messages',$this->get_unread_messages());    
            view()->share('unread_message_count',$this->get_unread_message_count());
        }
        return $next($request);
    }
    public function current_user_access()
    {
        $data =[];
        $user = Sentinel::check();
        
        if($user)
        {
            if($user->inRole(config('app.project.role_slug.parent_role_slug')))
            {
                $user_role = RoleModel::select('permissions')->where('slug','parent')->first() ;
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
        $school_id = \Session::get('school_id');
        $obj_permission = ClaimPermissionModel::select('is_active')->where('school_id',$school_id)->first();
        
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
