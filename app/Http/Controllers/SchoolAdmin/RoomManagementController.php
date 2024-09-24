<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;
use App\Common\Traits\MultiActionTrait;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\SchoolRoleModel;
use App\Models\RoomManagementModel;
use App\Common\Services\CommonDataService;

use DB;
use Flash;
use Validator;
use Datatables;
use Sentinel;

class RoomManagementController extends Controller
{   
    use MultiActionTrait;
        
    public function __construct(SchoolRoleModel $school_role,RoomManagementModel $room_management, CommonDataService $CommonDataService)
    {
        $this->SchoolRoleModel            = $school_role;    
        $this->RoomManagementModel        = $room_management;
        $this->BaseModel                  = $this->RoomManagementModel;
        $this->module_url_path               = url(config('app.project.role_slug.school_admin_role_slug')."/room/management");
        $this->module_view_folder         = "schooladmin.room_management";
        $this->module_title               = translation('room');
        $this->theme_color                = theme_color();
        $this->module_icon                = 'fa fa-home';
        $this->create_icon                = 'fa fa-plus-circle';
        $this->edit_icon                  = 'fa fa-edit';
        $this->school_id                  =  \Session::has('school_id') ? \Session::get('school_id') : '0'; 

        $this->CommonDataService          = $CommonDataService;
         /* Activity Section */
        $obj_data                         = Sentinel::getUser();
        if($obj_data){

            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->id                = $obj_data->id;
            $this->ip_address        = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;  
            
        }
        /* Activity Section */

    }

    /*
    | index() : List Room Management
    | Auther  : Padmashri
    | Date    : 7-05-2018
    */
    public function index(){

        $page_title = translation("manage")." ".str_plural($this->module_title);
        
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    /*
    | get_details() : To get the List Room Management
    | Auther  : Padmashri
    | Date    : 7-05-2018
    */
    public function get_details(Request $request){

        $room_details             = $this->BaseModel->getTable();
        $prefixed_room_details    = DB::getTablePrefix().$this->BaseModel->getTable();
      
        $obj_user = DB::table($room_details)
                                ->select(DB::raw($prefixed_room_details.".id as id,".
                                                 $prefixed_room_details.".tag_name, ".
                                                 $prefixed_room_details.".floor_no, ".
                                                 $prefixed_room_details.".no_of_rooms"))
                                ->whereNull($room_details.'.deleted_at')
                                ->where($room_details.'.school_id','=',$this->school_id)
                                ->orderBy($room_details.'.id','desc');
                              
        /* ---------------- Filtering Logic ----------------------------------*/                    
        return $obj_user;
    }

    /*
    | get_records() : To get the List Room Management
    | Auther  : Padmashri
    | Date    : 7-05-2018
    */
    public function get_records(Request $request){

        $role = \Session::get('role');
        $arr_current_user_access =[];
    
        $arr_current_user_access = $this->CommonDataService->current_user_access();
        $obj_user        = $this->get_details($request);
        
        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
      
            $json_result     = $json_result->editColumn('enc_id',function($data){
                                return base64_encode($data->id);
                            });
               
             $json_result     = $json_result->editColumn('build_action_btn',function($data) use ($arr_current_user_access){
                                           
                                $build_delete_action =  $build_edit_action =  ''; 
                                if(array_key_exists('room_management.update', $arr_current_user_access))
                                {
                                    $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                    $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';    
                                }   
                                if(array_key_exists('room_management.delete', $arr_current_user_access))
                                { 
                                    $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                    $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';

                                }
                                return $build_edit_action.'&nbsp;'.$build_delete_action;
                                     
                                });
                $json_result =      $json_result->editColumn('build_checkbox',function($data){
                           
                                $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                
                                return $build_checkbox;
                                })
                                ->editColumn('tag_name',function($data){
                           
                                 
                                
                                return ucwords($data->tag_name);
                                })
                                ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    /*
    | create() : Create Room Management
    | Auther  : Padmashri
    | Date    : 7-05-2018
    */
    public function create()
    {   

        
        $this->arr_view_data['page_title']      = translation('add')." ".$this->module_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['create_icon'] = $this->create_icon;
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    /*
    | store() : Store  Room Management
    | Auther  : Padmashri
    | Date    : 7-05-2018
    */
    public function store(Request $request)
    {
        
        $arr_rules = [];
        $arr_rules['tag_name']    = 'required';
        $arr_rules['floor_no']    = 'required';
        $arr_rules['no_of_rooms'] = 'required';

        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $tag_name      =   strtolower(trim($request->input('tag_name')));
        $floor_no      =   trim($request->input('floor_no'));
        $no_of_rooms   =   trim($request->input('no_of_rooms'));
        
        $arr_data = [];     
        $arr_data['school_id']   = $this->school_id;
        $arr_data['tag_name']    = $tag_name;
        $arr_data['floor_no']    = $floor_no;
        $arr_data['no_of_rooms'] = $no_of_rooms;
        
        $obj_exist = $this->RoomManagementModel->where('tag_name','=',$arr_data['tag_name'])->where('floor_no','=',$arr_data['floor_no'])->first();

        if(isset($obj_exist->id))
        {
            Flash::error($this->module_title." ".translation("already_exists"));
            return redirect()->back();            
        }                
        
        $res = $this->RoomManagementModel->create($arr_data);
        if($res){
            Flash::success($this->module_title." ".translation("created_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_creating ".$this->module_title);
            return redirect()->back();
        }        

    }

    /*
    | edit()  : Edit  Room Management
    | Auther  : Padmashri
    | Date    : 7-05-2018
    */
    public function edit($enc_id=FALSE)
    {
        $id = base64_decode($enc_id);
        $obj_data = $arr_data = [];
        $obj_data = $this->RoomManagementModel->where('id','=',$id)->first();
        if($obj_data)
        {
           $arr_data = $obj_data->toArray();
        }
       
        $this->arr_view_data['page_title']      = translation('edit')." ".$this->module_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['enc_id']          = $enc_id;

        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['edit_icon']   = $this->edit_icon;
        $this->arr_view_data['arr_data']    = $arr_data;
        
        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }

    /*
    | edit()  : Update  Room Management
    | Auther  : Padmashri
    | Date    : 7-05-2018
    */
    public function update(Request $request,$enc_id=FALSE)
    {
        $id = base64_decode($enc_id);     

        $arr_rules =[];
        $arr_rules['tag_name']    = 'required';
        $arr_rules['floor_no']    = 'required';
        $arr_rules['no_of_rooms'] = 'required';

        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 
        


        $tag_name      =   strtolower(trim($request->input('tag_name')));
        $floor_no      =   trim($request->input('floor_no'));
        $no_of_rooms   =   trim($request->input('no_of_rooms'));
        
        $arr_data = [];     
        
        $arr_data['tag_name']    = $tag_name;
        $arr_data['floor_no']    = $floor_no;
        $arr_data['no_of_rooms'] = $no_of_rooms;
        
        $obj_exist = $this->RoomManagementModel->where('tag_name','=',$arr_data['tag_name'])->where('floor_no','=',$arr_data['floor_no'])->where('id','<>',$id)->first();
        if(isset($obj_exist))
        {   
            Flash::error($this->module_title." ".translation("already_exists"));
            return redirect()->back();            
        }                
        
        $res = $this->RoomManagementModel->where('id',$id)->update($arr_data);
        if($res){
            Flash::success($this->module_title." ".translation("updated_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_updating ".$this->module_title);
            return redirect()->back();
        }        
       
    }
}