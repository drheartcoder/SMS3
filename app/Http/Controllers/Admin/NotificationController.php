<?php

namespace App\Http\Controllers\Admin;

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
        $this->module_url_path          = url(config('app.project.admin_panel_slug')."/notification");
        $this->module_title             = "Notification";
        $this->module_url_slug          = "Notification";
        $this->module_view_folder       = "admin.notification";


        /* Activity Section */
        $this->obj_data          = Sentinel::getUser();
        $this->first_name        = $this->obj_data->first_name;
        $this->last_name         = $this->obj_data->last_name;
        $this->ip_address        = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;  
        /* Activity Section */


        $this->theme_color              = theme_color();
    }  

    public function index()
    {	
         
        $arr_notification = array();
        $user_type        = '';
        
        $user_type  =  \Request::get('type'); 

        $page_title = "Manage ".str_plural($this->module_title).' - '.translation($user_type);

        /*$read  = $this->BaseModel
                                    ->whereHas('user_details',function(){})
                                    ->with('user_details');
                                    if($user_type!="all"){
                                        $read->where('user_type','=',$user_type);
                                    }
                                    $read->where('to_user_id','=',1)
                                    ->update(['is_read'=>1]);

         $query  = $this->BaseModel
                                    ->whereHas('user_details',function(){})
                                    ->with('user_details');
                                    if($user_type!="all"){
                                        $query->where('user_type','=',$user_type);
                                    }
                                    $query->where('to_user_id','=',1)
                                    ->orderBy('id','desc')
                                    ->get();*/

        $query  = $this->BaseModel
                                ->whereHas('user_details',function(){})
                                ->with('user_details');
                                if($user_type!="all"){
                                 $query   ->where('user_type','=',$user_type);
                                }
                                $query->where('to_user_id','=',1)
                                ->orderBy('id','desc');
                                $query   =  $query->get();

        $query2  = $this->BaseModel
                                ->whereHas('user_details',function(){})
                                ->with('user_details');
                                if($user_type!="all"){
                                 $query2->where('user_type','=',$user_type);
                                }
                                $query2->where('to_user_id','=',1);
                                $query2 =$query2 ->update(['is_read'=>1]);                            

        
        if(isset($query) && $query != FALSE)
        {
            $arr_notification = $query->toArray();

          
        }
        
        $this->arr_view_data['arr_notification']         = $arr_notification;
        $this->arr_view_data['page_title']               = $page_title;
        $this->arr_view_data['module_title']             = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']          = $this->module_url_path;
        $this->arr_view_data['theme_color']              = $this->theme_color;
        $this->arr_view_data['role']                     = $user_type;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    public function view(Request $request,$encId)
    {
           $id = base64_decode($encId);

               $query  = $this->BaseModel->where('id','=',$id);
               $does_exists =  $query->orderBy('id','desc')->count();

                if($does_exists>0)
                {

                   $arr_data = $obj_arr_data = []; 
                   $query = $this->BaseModel
                                    ->whereHas('user_details',function($q){})
                                    ->with('user_details')
                                    ->where('to_user_id','=',1)        
                                    ->where('id','=',$id)
                                    ->first();

                          if(isset($query))
                          {
                            $obj_arr_data = $query->toArray();
                          }

                    $this->arr_view_data['obj_arr_data']        = $obj_arr_data;
                    $this->arr_view_data['page_title']          = "View ".str_singular($this->module_title);
                    $this->arr_view_data['module_title']        = str_plural($this->module_title);
                    $this->arr_view_data['module_url_path']     = $this->module_url_path;
                    $this->arr_view_data['theme_color']              = $this->theme_color;
                    return view($this->module_view_folder.'.view',$this->arr_view_data);
               }
               else
               {
                  return redirect()->back();
               	   
               }
    }      

  
}