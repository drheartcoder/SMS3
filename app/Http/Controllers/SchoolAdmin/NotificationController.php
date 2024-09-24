<?php

namespace App\Http\Controllers\SchoolAdmin;

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

        $this->NotificationModel		= $notification;
        $this->BaseModel                = $this->NotificationModel;

        $this->ActivityLogsModel        = $activity_logs; /* Activity Model */

        $this->arr_view_data            = [];
        $this->module_url_path          = url(config('app.project.school_admin_panel_slug')."/notification");
        $this->module_title             = translation("notification");
        $this->module_url_slug          = "Notification";
        $this->module_view_folder       = "schooladmin.notification";
        $this->module_icon              = "fa fa-bell";

        $this->school_id  = Session::get('school_id');

        /* Activity Section */
        $this->obj_data          = Sentinel::getUser();
        $this->first_name        = $this->obj_data->first_name;
        $this->last_name         = $this->obj_data->last_name;
        $this->user_id           = $this->obj_data->id;



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
        


        if(\Request::has('type'))
        {   
            $user_type =  \Request::get('type');
        }
        else{

            Flash::error(translation("something_went_wrong"));
        }
        $query  = $this->BaseModel
                                ->whereHas('user_details',function(){})
                                ->with('user_details');
                                if($user_type!="all"){
                                 $query   ->where('user_type','=',$user_type);
                                }
                                $query->where('to_user_id','=',$this->user_id)
                                ->where('school_id','=',$this->school_id)
                                ->orderBy('id','desc');
                     $query   =  $query->get();

        $query2  = $this->BaseModel
                                ->whereHas('user_details',function(){})
                                ->with('user_details');
                                if($user_type!="all"){
                                 $query2->where('user_type','=',$user_type);
                                }
                                $query2->where('to_user_id','=',$this->user_id)
                                ->where('school_id','=',$this->school_id);
                             $query2 =$query2 ->update(['is_read'=>1]);

        if(!empty($query))
        {
            $arr_notification = $query->toArray();
        }
        
        $this->arr_view_data['arr_notification']         = $arr_notification;
        
        $this->arr_view_data['page_title']             = translation("manage").' '.str_plural($this->module_title);
        $this->arr_view_data['module_url_path']          = $this->module_url_path;
        $this->arr_view_data['module_icon']              = $this->module_icon;
        $this->arr_view_data['theme_color']              = $this->theme_color;
        $this->arr_view_data['module_title']             = $this->module_title;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }
  
}