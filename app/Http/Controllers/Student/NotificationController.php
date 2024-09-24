<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;



use App\Common\Traits\MultiActionTrait;

use App\Models\NotificationModel;  
/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
/*Activity Log */


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
   
    public function __construct(NotificationModel $notification,
                                ActivityLogsModel $activity_logs)
    {

        $this->NotificationModel        = $notification;
        $this->BaseModel                = $this->NotificationModel;

        $this->ActivityLogsModel        = $activity_logs; /* Activity Model */

        $this->arr_view_data            = [];
        $this->module_url_path          = url(config('app.project.student_panel_slug')."/notification");
        $this->module_title             = translation("notification");
        $this->module_url_slug          = "Notification";
        $this->module_view_folder       = "student.notification";
        $this->module_icon              = "fa fa-bell";

        /* Activity Section */
        $this->obj_data          = Sentinel::getUser();
        $this->first_name        = $this->obj_data->first_name;
        $this->last_name         = $this->obj_data->last_name;
        $this->user_id           = $this->obj_data->id;

        $this->school_id  = Session::get('school_id');
        /* Activity Section */
        $this->theme_color              = theme_color();
    }  

 /*
    | index() : load notification listing page according to role
    | Auther : sayali bhirud
    | Date : 09-05-2018
    */
    public function index()
    {   
        
        $arr_notification = [];
        $user_type        = '';
        $page_title = "Manage ".str_plural($this->module_title);

        if(\Request::has('type'))
        {   
                $user_type =  \Request::get('type');
                
            
            
        }
        
              $update  =  $this->BaseModel
                             ->whereHas('user_details',function(){})
                             ->with('user_details');
                             if($user_type!='all')
                             {
                                $update = $update->where('user_type','=',$user_type);
                             }
                             $update = $update->where('to_user_id','=',$this->user_id)
                             ->where('school_id','=',$this->school_id)
                             ->update(['is_read'=>1]);
        

             $obj_data  =  $this->BaseModel
                             ->whereHas('user_details',function(){})
                             ->with('user_details');
                             if($user_type!='all')
                             {
                                $obj_data = $obj_data->where('user_type','=',$user_type);
                             }
                             $obj_data = $obj_data->where('to_user_id','=',$this->user_id)
                             ->where('school_id','=',$this->school_id)
                             ->orderBy('id','desc')
                             ->get();


        if(!empty($obj_data))
        {
            $arr_notification = $obj_data->toArray();
        }
        
        $this->arr_view_data['arr_notification']         = $arr_notification;
        $this->arr_view_data['page_title']               = $page_title;
        $this->arr_view_data['module_title']             = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']          = $this->module_url_path;
        $this->arr_view_data['module_icon']              = $this->module_icon;
        $this->arr_view_data['theme_color']              = $this->theme_color;
        $this->arr_view_data['theme_color']              = $this->theme_color;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }
  
}