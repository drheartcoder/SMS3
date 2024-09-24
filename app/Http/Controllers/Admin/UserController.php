<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;

use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\ParentModel;
use App\Models\UserRoleModel;   
use App\Models\RoleModel;
use App\Models\ActivationModel;
/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
/*Activity Log */

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;

class UserController extends Controller
{
    use MultiActionTrait;
    public function __construct(    
                                    UserModel $user,
                                    UserTranslationModel $translation,
                                    UserRoleModel $user_role_model,
                                    RoleModel $role_model,
                                    ActivityLogsModel $activity_logs,
                                    ParentModel $parent

                                )
    {
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->ParentModel                  = $parent;
        $this->BaseModel                    = $this->UserModel;
        $this->ActivityLogsModel            = $activity_logs; /* Activity Model */   

        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/users");
        
        $this->module_title                 = translation("users");
        $this->modyle_url_slug              = translation("users");

        $this->module_view_folder           = "admin.users";
        $this->theme_color                  = theme_color();

        $this->first_name = $this->last_name =$this->ip_address ='';

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            
        }
        /* Activity Section */



    }   

    public function index(Request $request)
    {   
        $userName = '';

        $user_type= 'none';
       
        if(\Request::has('type'))
        {   if((\Request::has('type') == 'parent'))
            {
                $user_type =  \Request::get('type');
                $page_title = translation("manage").' '.$user_type; 
            } 
            
        }
        $this->arr_view_data['role']            = $user_type;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    function get_users_details(Request $request,$type)
    {     
        $role =$type;

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
 

        $user_details             = $this->BaseModel->getTable();
        $prefixed_user_details    = DB::getTablePrefix().$this->BaseModel->getTable();
        $user_role_table          = $this->UserRoleModel->getTable();

        $role_table               = $this->RoleModel->getTable();

        $user_trans_table             = $this->UserTranslationModel->getTable();                  
        $prefixed_user_trans_table    = DB::getTablePrefix().$this->UserTranslationModel->getTable();

    
        $obj_user = DB::table($user_details)
                                ->select(DB::raw($prefixed_user_details.".id as id,".
                                                 $prefixed_user_details.".email as email, ".
                                                 $prefixed_user_details.".is_active as is_active, ".
                                                 $prefixed_user_details.".last_login as last_login,".
                                                 
                                                 $role_table.".slug as role_slug,".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
                                                          .$prefixed_user_trans_table.".last_name) as user_name"
                                                 ))
                                ->join($user_role_table,$user_details.'.id','=',$user_role_table.'.user_id')
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                ->join($role_table, function ($join) use($role_table,$user_role_table,$role) {
                                    $join->on($role_table.'.id', '=',$user_role_table.'.role_id')
                                         ->where('slug','=',$role);
                                })
                                ->where($user_trans_table.'.locale','=',$locale)
                                ->orderBy($user_details.'.created_at','DESC');
    
                              
        /* ---------------- Filtering Logic ----------------------------------*/                    
        $search = $request->input('search');
        $search_term = $search['value'];
    
        if($request->has('search') && $search_term!="")
        {
            $obj_user = $obj_user->WhereRaw("( (".$user_details.".email LIKE '%".$search_term."%') ")
                                     ->orWhereRaw(" ( CONCAT(".$prefixed_user_trans_table.".first_name,'',".$prefixed_user_trans_table.".last_name)  LIKE  '%".$search_term."%' ) )");
        }


        return $obj_user;
    }


    public function get_records(Request $request,$type='')
    {
        
    
        $arr_current_user_access =[];
        $arr_current_user_access = $request->user()->permissions;
       
        $obj_user        = $this->get_users_details($request,$type);

        $role = config('app.project.role_slug.user_role_slug');

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('users.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data)
                            {
                                return base64_encode($data->id);
                            });
        }                    

        $json_result     = $json_result->editColumn('last_login',function($data) 
                            { 
                                 
                                if($data->last_login!=null && $data->last_login!='0000-00-00 00:00:00'){

                                    return  date("Y-m-d H:i A",strtotime($data->last_login));
                                }
                                else
                                {
                                    return  '-';
                                }

                            }) 
                            ->editColumn('build_action_btn',function($data) use ($role)
                            {
                                if($role != null)
                                {       
                                    $build_status_btn =  ''; 
 
                                    $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                    $build_view_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="'.$view_href.'" title="View"><i class="fa fa-eye" ></i></a>';

                                    if($data->is_active != null && $data->is_active == "0")
                                    {   
                                        $build_status_btn = '<a class="btn btn-circle btn-dangers  btn-bordered btn-fill show-tooltip " title="activate" href="'.$this->module_url_path.'/activate/'.base64_encode($data->id).'" 
                                        onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-lock"></i></a>';
                                    }
                                    elseif($data->is_active != null && $data->is_active == "1")
                                    {
                                        $build_status_btn = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip  btn-to-success" title="deactivate" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-unlock"></i></a>';
                                    }
                                    
                                    
                                    return $build_status_btn.'&nbsp;'.$build_view_action.'&nbsp;';  
                                }
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    public function view($enc_id)
    {   
        $id = base64_decode($enc_id);
        $locale = '';

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
        
        $obj_user = $this->BaseModel->where('id','=',$id)
                                    ->first();
        $user_details = $this->UserTranslationModel->where(['user_id'=>$id,'locale'=>$locale])->first();
        $parent_details =   $this->ParentModel->where('user_id',$id)->first();
        
        $arr_data = [];                                    
        
        if($obj_user && $user_details && $parent_details)
        {
            $arr_data[0] = $obj_user->toArray();
            $arr_data[1] = $user_details->toArray();
            $arr_data[2] = $parent_details->toArray();
        }  
        
        $page_title     =   '';
        if(\Request::has('type'))
        {   
            if((\Request::has('type') == 'parent'))
            {
                $user_type =  \Request::get('type');
                $page_title = translation("view").' '.$user_type; 
            } 
        }

        $this->arr_view_data['page_title']                   = $page_title;
        $this->arr_view_data['module_title']                 = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['arr_data']                     = $arr_data;
        
        $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
        $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        
        return view($this->module_view_folder.'.view', $this->arr_view_data);


    }
   
}