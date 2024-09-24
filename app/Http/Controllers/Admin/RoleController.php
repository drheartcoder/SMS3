<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\SchoolRoleModel;
use App\Models\RoleModel;
use App\Models\ModuleUserModel;
use App\Common\Services\LanguageService;


use Flash;	
use Sentinel;
use Validator;
class RoleController extends Controller
{
    public function __construct(
    								SchoolRoleModel $school_role,
    								RoleModel $role,
                                    ModuleUserModel $module_user,
                                    LanguageService $language
    							)
    {
        $this->str_school_id ='school_id';

    	$this->SchoolRoleModel = $school_role;	
    	$this->RoleModel 	= 	$role;
    	$this->BaseModel                  = $this->SchoolRoleModel;
		$this->module_url_path 	          = url(config('app.project.role_slug.admin_role_slug')."/role");
		$this->module_view_folder         = "admin.role";
		$this->module_title               = translation('role');
		$this->theme_color                = theme_color();
		$this->module_icon                = 'fa fa-user';
		$this->create_icon                = 'fa fa-plus-circle';
		$this->edit_icon                  = 'fa fa-edit';
        $this->ModuleUserModel            = $module_user;
        $this->LanguageService            = $language;
        $this->school_no                  =  \Session::has($this->str_school_id) ? \Session::get($this->str_school_id) : '0' ;
    }
    public function index()
    {	
    	$obj_roles = $this->RoleModel
                                    ->where('id','>',8)->get();
    	if($obj_roles)
    	{
    		$arr_roles = $obj_roles->toArray();
     	}

    	$this->arr_view_data['page_title']      = translation("manage")." ".$this->module_title;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;       
        $this->arr_view_data['arr_data']       = $arr_roles;       
        
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    public function approve($enc_id=FALSE)
    {
        $id = base64_decode($enc_id);
        
        $this->RoleModel->where('id',$id)->update(array('is_approved'=>'APPROVED'));

        Flash::success(translation('record_approved_successfully'));
        return redirect()->back();

    }

    public function reject($enc_id=FALSE)
    {
        $id = base64_decode($enc_id);
        
        $this->RoleModel->where('id',$id)->update(array('is_approved'=>'REJECTED'));

        Flash::success(translation('record_rejected_successfully'));
        return redirect()->back();

    }	
    public function create()
    {
        $obj_modules = $this->ModuleUserModel
                            ->whereHas('get_role',function($q){
                                $q->where('slug','school_admin') ;   
                            })
                            ->with('get_role','get_modules')
                            ->where('is_active',1)
                            ->get();               
        if($obj_modules)
        {
           $arr_current_user_access = $obj_modules->toArray();
          
        }

       
        $this->arr_view_data['page_title']   = translation('add')." ".$this->module_title;
        $this->arr_view_data['module_title'] = $this->module_title;
        $this->arr_view_data['edit_mode'] = TRUE;
        
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['arr_modules']     = $arr_current_user_access;

        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['create_icon'] = $this->create_icon;
        
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    public function store(Request $request)
    {
        
        $arr_rules = [];
        
        $arr_rules['role']  = 'required|regex:/^[a-zA-Z ]*$/';

          $messages = array(
                        'regex'                => translation('please_enter_valid_text_format'),
                        'required'             => translation('this_field_is_required'));

        $validator = Validator::make($request->all(),$arr_rules,$messages);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $permission_arr =[];
        
        $permissions    =   $request->input('arr_permisssion');

        if(isset($permissions) && sizeof($permissions)>0)
        {    
              $permission_arr    =   json_encode($permissions['subadmin']);  
        }

        $arr_data = []; 

        $role = $request->role;
        $slug = strslug($role);
        $obj_role = $this->RoleModel->where('slug',$slug)->first();

        if(isset($obj_role->id))
        {
            Flash::error(translation("already_exists"));
            return redirect()->back(); 
        }

        $arr_data = [];     
        $arr_data['slug'] = $slug;
        $arr_data['name'] = $role;
        $arr_data['is_approved'] = 'APPROVED';
        
        $this->RoleModel->create($arr_data);
        Flash::success($this->module_title." ".translation("created_successfully"));
        return redirect()->back();
    }

    public function edit($enc_id=FALSE)
    {
        $id = base64_decode($enc_id);
        $arr_data = [];
        

        $obj_data = $this->RoleModel->where('id',$id)->first();                 
        if($obj_data)
        {
            $arr_data =$obj_data->toArray();
        }
       
       
        $this->arr_view_data['page_title']   = translation('edit')." ".$this->module_title;
        $this->arr_view_data['module_title'] = str_plural($this->module_title);
        $this->arr_view_data['edit_mode'] = TRUE;
        
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  

        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['edit_icon'] = $this->edit_icon;
        $this->arr_view_data['arr_data'] = $arr_data;
        
        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }

    public function update(Request $request,$enc_id=FALSE)
    {
        $id = base64_decode($enc_id);     

        $arr_rules = [];
        
        $arr_rules['role']  = 'required|regex:/^[a-zA-Z ]*$/';

          $messages = array(
                        'regex'                => translation('please_enter_valid_text_format'),
                        'required'             => translation('this_field_is_required'));

        $validator = Validator::make($request->all(),$arr_rules,$messages);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $permission_arr =[];
        
        $permissions    =   $request->input('arr_permisssion');

        if(isset($permissions) && sizeof($permissions)>0)
        {    
              $permission_arr    =   json_encode($permissions['subadmin']);  
        }

        $role = $request->role;
        $slug = strslug($role);

        $obj_role = $this->RoleModel->where('id','<>',$id)->where('slug',$slug)->first();
        if(isset($obj_role->id))
        {
            Flash::error(translation("already_exists"));
            return redirect()->back(); 
        }

        $arr_data = [];     
        $arr_data['slug'] = $slug;
        $arr_data['name'] = $role;
        $arr_data['is_approved'] = 'APPROVED';
        
        $this->RoleModel->where('id',$id)->update($arr_data);
        Flash::success($this->module_title." ".translation("updated_successfully"));
        return redirect()->back();

    }
    public function get_roles(Request $request)
    {
        $arr_roles = [];
        $obj_roles = $this->RoleModel
                          ->where('name','LIKE', '%'.$request->keyword.'%') 
                          ->where('id','>',8)
                          ->get();
        if($obj_roles)
        {
            $arr_roles = $obj_roles ->toArray();    
        }   
        $str ='';               
        if(count($arr_roles)>0)
        {
            $str .= '<ul id="country-list">';
            foreach($arr_roles as $role)
            {
                $str .= '<li onClick="selectCountry(\''. $role["name"] .'\')">'.$role["name"].'</li>';
            }
        }
        return $str;
    }   
}
