<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\SchoolRoleModel;
use App\Models\RoleModel;
use App\Models\ModuleUserModel;
use App\Common\Services\LanguageService;


use Flash;
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
    	$this->SchoolRoleModel = $school_role;	
    	$this->RoleModel 	= 	$role;
    	$this->BaseModel                  = $this->SchoolRoleModel;
		$this->module_url_path 	          = url(config('app.project.role_slug.school_admin_role_slug')."/role");
		$this->module_view_folder         = "schooladmin.role";
		$this->module_title               = translation('role');
		$this->theme_color                = theme_color();
		$this->module_icon                = 'fa fa-user';
		$this->create_icon                = 'fa fa-plus-circle';
		$this->edit_icon                  = 'fa fa-edit';
        $this->ModuleUserModel            = $module_user;
        $this->LanguageService            = $language;
        $this->school_no                  =  \Session::has('school_id') ? \Session::get('school_id') : '0' ;
    }
    public function index()
    {	
    	$obj_roles = $this->SchoolRoleModel->with('role_details')->where('school_id',$this->school_no)->get();
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
        $arr_rules['arr_permisssion'] = 'required';
        $arr_rules['role']            = 'required|regex:/^[a-zA-Z ]*$/';
        $arr_rules['role_for']        = 'required';

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

        $role       = $request->role;
        $role_for   = $request->role_for;
        $slug = strslug($role);
        $obj_role = $this->RoleModel->where('slug',$slug)->first();

        if(!isset($obj_role->id))
        {
            $arr_data = [];
            $arr_data['slug'] = $slug;
            $arr_data['name'] = $role ; 

            $role = $this->RoleModel -> create($arr_data);
            $role = isset($role->id) ? $role->id :'0'; 
        }
        else
        {
            $role = $obj_role->id;
        }
        if($role <= 8 )
        {
            Flash::error(translation('you_have_no_access_to_change_the_permission_of_this_user'));
            return redirect()->back(); 
        }

        $arr_data = [];     
        $arr_data['role_id']    = $role;
        $arr_data['school_id']  = $this->school_no;
        $arr_data['role_for']   = $role_for;
        
        $obj_exist = $this->SchoolRoleModel
                        ->where('school_id',$arr_data['school_id'])
                        ->where('role_id',$arr_data['role_id'])
                        ->first();
        if(isset($obj_exist->id))
        {
            Flash::error($this->module_title." ".translation("already_exists"));
            return redirect()->back();            
        }                
        $school_role = $this->SchoolRoleModel->create($arr_data);
        $this->SchoolRoleModel->where('id',$school_role->id)->update(array('permissions'=>$permission_arr));

        Flash::success($this->module_title." ".translation("created_successfully"));
        return redirect()->back();
    }

    public function edit($enc_id=FALSE)
    {
        $id = base64_decode($enc_id);
        $arr_data = [];
        $obj_modules = $this->ModuleUserModel
                            ->whereHas('get_role',function($q){
                                $q->where('slug','school_admin') ;
                            })
                            ->whereHas('get_modules',function($q){
                                $q->where('is_active',1);
                            })
                            ->with('get_role','get_modules')
                            ->get();

        $obj_data = $this->SchoolRoleModel->with('role_details')->where('id',$id)->first();                 
        if($obj_data)
        {
            $arr_data =$obj_data->toArray();
        }
        if($obj_modules)
        {
           $arr_current_user_access = $obj_modules->toArray();
        }
       
        $this->arr_view_data['page_title']   = translation('edit')." ".$this->module_title;
        $this->arr_view_data['module_title'] = str_plural($this->module_title);
        $this->arr_view_data['edit_mode'] = TRUE;
        
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['arr_modules']     = $arr_current_user_access;

        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['edit_icon'] = $this->edit_icon;
        $this->arr_view_data['arr_data'] = $arr_data;
        
        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }

    public function update(Request $request,$enc_id=FALSE)
    {
        $id = base64_decode($enc_id);     

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
              $permission_arr    =   json_encode($permissions['subadmin']);  
        }
      
        $this->SchoolRoleModel->where('id',$id)->update(array('permissions'=>$permission_arr));

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
