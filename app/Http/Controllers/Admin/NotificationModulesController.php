<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;



use App\Common\Traits\MultiActionTrait;

use App\Models\NotificationModulesModel;  

use Validator;
use Session;
use Flash;
use File;
use Sentinel;
use DB;
use Datatables;

class NotificationModulesController extends Controller
{
    use MultiActionTrait;
   
    public function __construct(NotificationModulesModel $notification)
    {

        $this->NotificationModulesModel		= $notification;
        $this->BaseModel                = $this->NotificationModulesModel;
        $this->arr_view_data            = [];
        $this->module_url_path          = url(config('app.project.admin_panel_slug')."/notification_modules");
        $this->module_title             = translation("notification_modules");
        $this->module_url_slug          = "notification_modules";
        $this->module_view_folder       = "admin.notification_modules";


        /* Activity Section */
        $this->obj_data          = Sentinel::getUser();
        $this->first_name        = $this->obj_data->first_name;
        $this->last_name         = $this->obj_data->last_name;
        $this->ip_address        = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;  
        /* Activity Section */


        $this->theme_color              = theme_color();
    }  

    public function index($role)
    {	

        $arr_modules      = array();
        if($role !='')
        {
            $modules    = $this->NotificationModulesModel->where('role',$role)->get();
        
            if(isset($modules) && count($modules)>0 && !is_null($modules))
            {
                $arr_modules = $modules->toArray();
            }
        }
        $page_title = translation($role)." ".str_plural($this->module_title);
        
        $this->arr_view_data['arr_modules']         = $arr_modules;
        $this->arr_view_data['page_title']          = $page_title;
        $this->arr_view_data['module_title']        = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['theme_color']         = $this->theme_color;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }
}