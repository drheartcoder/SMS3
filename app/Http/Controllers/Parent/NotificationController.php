<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;
use App\Models\NotificationModel;  
 


use Validator;
use Session;
use Flash;
use File;
use Sentinel;
use DB;
use Datatables;

class NotificationController extends Controller
{
    use MultiActionTrait;
   
    public function __construct(NotificationModel $notification)
    {

        $this->NotificationModel		= $notification;
        $this->BaseModel                = $this->NotificationModel;

        $this->arr_view_data            = [];
        $this->module_url_path          =  url(config('app.project.role_slug.parent_role_slug').'/notification'); 
        $this->module_title             = translation("notification");
        $this->module_url_slug          = "Notification";
        $this->module_view_folder       = "parent.notification";
        $this->module_icon              = "fa fa-bell";
        $this->module_panel_slug        = config('app.project.parent_role_slug');
        /* Users Section */
        $this->obj_data          = Sentinel::getUser();
        $this->first_name        = $this->obj_data->first_name;
        $this->last_name         = $this->obj_data->last_name;
        $this->user_id           = $this->obj_data->id;

        /* Users Section */
        $this->theme_color              = theme_color();

        $this->school_id  = Session::get('school_id');
        
    }  

 /*
    | index() : load notification listing page according to role
    | Auther : Padmashri bhirud
    | Date : 23-05-2018
    */
    public function index()
    {	
        
        $arr_notification = [];
        $userType        = '';
        $page_title = "Manage ".$this->module_title;
        $toUserType = $this->module_panel_slug;
        if(\Request::has('type'))
        {   
                $user_type  =  \Request::get('type'); 
            
        }
        
        $read  = NotificationModel::
                                    whereHas('user_details',function(){})
                                    ->with('user_details');
                                    if($user_type!='all'){
                                        $read->where('user_type',$user_type);
                                    } 
                                    
                                    $read ->where('to_user_id','=',$this->user_id)
                                    ->where('school_id','=',$this->school_id)   
                                    ->update(['is_read'=>1]);

        $query  = NotificationModel::whereHas('user_details',function(){})
                                    ->with('user_details');
                                    if($user_type!='all'){
                                        $query->where('user_type',$user_type);
                                    }
                                    $query->where('to_user_id','=',$this->user_id)
                                    ->where('school_id','=',$this->school_id);
                                $query = $query ->get();

        if(!empty($query))
        {
            $arr_notification = $query->toArray();
        }
        
        $this->arr_view_data['arr_notification']         = $arr_notification;
        $this->arr_view_data['page_title']               = $page_title;
        $this->arr_view_data['module_title']             = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']          = $this->module_url_path;
        $this->arr_view_data['module_icon']              = $this->module_icon;
        $this->arr_view_data['theme_color']              = $this->theme_color;
        
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

   
  
}