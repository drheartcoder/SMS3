<?php

namespace App\Http\Controllers\Professor;

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
        $this->module_url_path          = url(config('app.project.professor_panel_slug')."/notification");
        $this->module_title             = translation("notification");
        $this->module_url_slug          = "Notification";
        $this->module_view_folder       = "professor.notification";
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
    | Auther : pooja k
    | Date : 19 July 2018
    */
    public function index()
    {   
        
        $arr_notification = [];
        $user_type        = '';
        

        if(\Request::has('type'))
        {   if((\Request::has('type') == 'technical')  || (\Request::has('type') == 'professor')|| (\Request::has('type') == 'student') || (\Request::has('type') == 'parent') || (\Request::has('type') == 'employee'))
            {
                $user_type =  \Request::get('type');

                
            } 
            
        }

        $query  = $this->BaseModel
                                 ->whereHas('user_details',function(){})
                                 ->with('user_details');
                                 if($user_type!='all')
                                 {
                                    $query = $query->where('user_type','=',$user_type);
                                 }
                                 $query = $query->where('to_user_id','=',$this->user_id)
                                 ->where('school_id','=',$this->school_id);
        
        $obj_data =  $query->orderBy('id','desc')->get();

        $arr_id = [];
        if(!empty($obj_data))
        {
            $arr_notification = $obj_data->toArray();
            foreach ($arr_notification as $value) {
                array_push($arr_id,$value['id']);
            }
            $this->BaseModel->whereIn('id',$arr_id)->update(['is_read'=>1]);
            
        }
        
        $this->arr_view_data['arr_notification']         = $arr_notification;
        $this->arr_view_data['page_title']               = translation("manage")." ".str_plural(translation("notification"));
        $this->arr_view_data['module_title']             = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']          = $this->module_url_path;
        $this->arr_view_data['module_icon']              = $this->module_icon;
        $this->arr_view_data['theme_color']              = $this->theme_color;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }
  
}