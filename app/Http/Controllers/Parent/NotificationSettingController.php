<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\SchoolRoleModel;
use App\Models\ModuleUserModel;
use App\Models\NotificationSettingsModel;
use App\Models\NotificationModulesModel;
use App\Models\SchoolParentModel;

use DB;
use Flash;
use Sentinel;
use Session;
use Validator;
use Datatables;

class NotificationSettingController extends Controller
{
      public function __construct(SchoolRoleModel $school_role,
    							  NotificationSettingsModel $notification_setting,
    							  ModuleUserModel $module_user,
                                  NotificationModulesModel $modules)
    {
    	$this->SchoolRoleModel            = $school_role;	
    	$this->NotificationSettingsModel  = $notification_setting;
    	$this->BaseModel                  = $this->NotificationSettingsModel;
    	$this->ModuleUserModel			  = $module_user;
    	$this->NotificationModulesModel   = $modules;
        $this->SchoolParentModel          = new SchoolParentModel();

		$this->module_url_path 	          = url(config('app.project.role_slug.parent_role_slug')."/notification_settings");
		$this->module_view_folder         = "parent.notification_settings";
		$this->module_title               = translation('notification_settings');
		$this->theme_color                = theme_color();
		$this->module_icon                = 'fa fa-bell';
		$this->create_icon                = 'fa fa-plus-circle';
		$this->edit_icon                  = 'fa fa-edit';
        $this->school_id                  =  \Session::has('school_id') ? \Session::get('school_id') : '0'; 
         /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){

            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->id                = $obj_data->id;
            $this->user_id			 = $obj_data->id;

            $role = Sentinel::findRoleBySlug(config('app.project.role_slug.parent_role_slug'));
			$roleId  = isset($role->id) && ($role->id!='') ? $role->id : 8 ;

			$this->role_id   		= $roleId;
           
        }
        $obj_user = $this->SchoolParentModel->where('parent_id',$this->user_id)->where('school_id',$this->school_id)->where('is_active',1)->first();
        if($obj_user)
        {
            $this->id   =   $obj_user->id;
        }
        /* Activity Section */

    }

     /*
    | index() : List Of Modules for Notification Setting
    | Auther  : Padmashri
    | Date    : 8-05-2018
    */
    public function index(){

        $page_title =  str_plural($this->module_title);

        $obj_modules = $this->NotificationModulesModel
                            ->where('role',config('app.project.role_slug.parent_role_slug'))
                            ->where('is_active',1)
                            ->get();
         		
        $arr_data = array();
        if($obj_modules)
        {
           $arr_current_user_access = $obj_modules->toArray();
           
        }

        $obj_data = $this->NotificationSettingsModel->where('user_id',$this->user_id)->where('role_id',$this->role_id)->where('school_id','=',$this->school_id)->first();                 
        if($obj_data)
        {
            $arr_data =$obj_data->toArray();

        }
                
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['edit_icon']    	= $this->edit_icon;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['arr_modules']     = $arr_current_user_access;
        
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

     /*
    | store() : Store Notification Setting
    | Auther  : Padmashri
    | Date    : 8-05-2018
    */
    public function store(Request $request){
    	

    	$arr_rules =[];
        $arr_rules['arr_permisssion'] = 'required';

        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $permission_arr =[];
        $permissions    =   $request->input('arr_permisssion');
	    if(isset($permissions) && sizeof($permissions)>0)
        {    
              $permission_arr    =   json_encode($permissions['notification']);  
        }

        $user_id = trim($request->input('user_id'));
        $id 	 = trim($request->input('id'));
		if(isset($user_id) && $user_id > 0  )
		{
	        $res = $this->NotificationSettingsModel->where('id',$id)->update(array('notification_permission'=>$permission_arr));
		}else{
            
			$arr_data = [];
			$arr_data = array(
							'user_id' => $this->user_id,
							'role_id' => $this->role_id,
							'notification_permission' => $permission_arr,
							'school_id' => $this->school_id
					);
			$res = $this->NotificationSettingsModel->create($arr_data);
		}

		if($res)
		{
	        Flash::success($this->module_title." ".translation("updated_successfully"));
		}else{
			Flash::error(translation("something_went_wrong_while_updating")." ".$this->module_title);
		}
        return redirect()->back();
    }
}
