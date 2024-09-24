<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;
use App\Models\BrotherhoodModel;
/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
use App\Models\AcademicYearModel;   
/*Activity Log */

use App\Common\Services\CommonDataService;


use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;

class BrotherhoodController extends Controller
{
    
    use MultiActionTrait;
    public function __construct(BrotherhoodModel $brotherhood,AcademicYearModel $academic_year,CommonDataService $CommonDataService)
    {
        $this->BrotherhoodModel             = $brotherhood;
        $this->BaseModel                    = $this->BrotherhoodModel;
        $this->AcademicYearModel            = $academic_year;
        $this->CommonDataService            = $CommonDataService;

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/brotherhood';
        
        $this->module_title                 = translation("brotherhood");
        $this->modyle_url_slug              = translation("brotherhood");

        $this->module_view_folder           = "schooladmin.brotherhood";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-user-o';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';

        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->first_name 					= $this->last_name =$this->ip_address ='';
        
        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
        }
        /* Activity Section */

        /*Local Section*/
        if(Session::has('locale'))
        {
            $this->locale = Session::get('locale');
        }
        else
        {
            $this->locale = 'en';
        }
        /*Local Section*/
    }   

    /*
    | create() : load brotherhood index page 
    | Auther   : Padmashri
    | Date 	   : 15-05-2018
    */
    public function index(Request $request)
    {   
    	$page_title = translation("manage")." ".$this->module_title;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
		return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*
    | get_details() : To get the list of brotherhood added 
    | Auther  : Padmashri
    | Date    : 15-05-2018
    */
    public function get_details(Request $request){

        /* GET THE END DATE OF ACADEMIC YEAR */
        $str_years                = $this->CommonDataService->get_academic_year_less_than($this->academic_year);
        $prefixed_room_details    = DB::getTablePrefix().$this->BaseModel->getTable();
        $prefixed_academic_year    = DB::getTablePrefix().$this->AcademicYearModel->getTable();
        

        $obj_user = DB::table($prefixed_room_details)
                                ->select(DB::raw($prefixed_room_details.".id as id,".
                                                 $prefixed_room_details.".school_id, ".
                                                 $prefixed_room_details.".kid_no, ".
                                                 $prefixed_room_details.".discount,".
                                                 $prefixed_room_details.".academic_year_id,".
                                                 $prefixed_academic_year.".academic_year"
                                             ))
                                ->whereNull($prefixed_room_details.'.deleted_at')
                                ->where($prefixed_room_details.'.school_id','=',$this->school_id)
                                ->join($prefixed_academic_year,$prefixed_room_details.'.academic_year_id','=',$prefixed_academic_year.".id")
                                ->whereRaw(" academic_year_id in (".$str_years.")")
                                ->orderBy($prefixed_room_details.'.id','desc');


            $search = $request->input('search');
            $search_term = $search['value'];
                       

        if($request->has('search') && $search_term!="")
        {
                $obj_user = $obj_user ->WhereRaw("( (".$prefixed_room_details.".kid_no LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_room_details.".discount LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_academic_year.".academic_year LIKE '%".$search_term."%') ) ");
        }

     
        return $obj_user;
    }

    /*
    | get_records() : To get the list of brotherhood added 
    | Auther  : Padmashri
    | Date    : 15-05-2018
    */
    public function get_records(Request $request){

    
        $role = Session::get('role');
        
        $arr_current_user_access =[];
    
        $arr_current_user_access = $this->CommonDataService->current_user_access();
        $obj_user        = $this->get_details($request);

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        

             $json_result     = $json_result->editColumn('build_action_btn',function($data) use ($arr_current_user_access){
                                           
                                $build_delete_action =  $build_edit_action =  ''; 
                                if(array_key_exists('brotherhood.update', $arr_current_user_access)){
	                                $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
	                                $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';
                                }
                                if(array_key_exists('brotherhood.delete', $arr_current_user_access)){ 
                                $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
		                        }
                                return $build_edit_action.'&nbsp;'.$build_delete_action;
                                     
                                });
        
        $json_result =      $json_result->editColumn('build_checkbox',function($data)use($arr_current_user_access){
                                $build_checkbox = '';

                                if(array_key_exists('brotherhood.update', $arr_current_user_access) || array_key_exists('brotherhood.delete', $arr_current_user_access))
                                {
                                    $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>';    
                                }
                                return $build_checkbox; 
                                
                                })
                                ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    /*
    | create(): Create brotherhood management
    | Auther  : Padmashri
    | Date    : 15-05-2018
    */
    public function create(){
        $this->arr_view_data['page_title']      = translation('add')." ".$this->module_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['create_icon']     = $this->create_icon;
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    /*
    | store() : Store  brotherhood management
    | Auther  : Padmashri
    | Date    : 15-05-2018
    */
    public function store(Request $request){
        
        $messages = $arr_rules = [];

        $arr_rules['kid_no']           = 'required|numeric';
        $arr_rules['discount']         = 'required|numeric';
        
        $messages['required']      =  translation('this_field_is_required');
        $messages['numeric']       = translation('please_enter_a_valid_number');
        $validator                 = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $academic_year    =   $this->academic_year;
        $kid_no           =   trim($request->input('kid_no'));
        $discount         =   trim($request->input('discount'));
      
        $arr_data = [];     
        $arr_data['school_id']       = $this->school_id;
        $arr_data['discount']        = $discount;
        $arr_data['kid_no']          = $kid_no;
        $arr_data['academic_year_id']   = $academic_year;
        $obj_exist =   BrotherhoodModel::where('school_id','=',$this->school_id)->where('kid_no','=',$kid_no)->where('academic_year_id','=',$academic_year)->first();
        if(isset($obj_exist->id))
        {
            Flash::error($this->module_title." ".translation("already_exists"));
            return redirect()->back();            
        }                
        $res = $this->BrotherhoodModel->create($arr_data);
        if($res){
            Flash::success($this->module_title." ".translation("created_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_creating ".$this->module_title);
            return redirect()->back();
        }        

    }


     /*
    | edit()  : Edit  Room Assignment
    | Auther  : Padmashri
    | Date    : 15-04-2018
    */
    public function edit($enc_id=FALSE)
    {
        $id = base64_decode($enc_id);
        $obj_data = $arr_data = [];
        $obj_data = $this->BrotherhoodModel->where('id','=',$id)->first();

        if($obj_data)
        {
           $arr_data = $obj_data->toArray();
        }
        $this->arr_view_data['page_title']      = translation('edit')." ".$this->module_title;
        $this->arr_view_data['module_title']    = $this->module_title;
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
    | edit()  : Update  Room Assignment
    | Auther  : Padmashri
    | Date    : 15-04-2018
    */
    public function update(Request $request,$enc_id=FALSE)
    {
        $id = base64_decode($enc_id);     
        
        $arr_rules = $messages = [];

        $arr_rules['kid_no']           = 'required|numeric';
        $arr_rules['discount']         = 'required|numeric';
        
        $messages['required']      =  translation('this_field_is_required');
        $messages['numeric']       = translation('please_enter_a_valid_number');

        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $academic_year    =   $this->academic_year;
        $kid_no           =   trim($request->input('kid_no'));
        $discount         =   trim($request->input('discount'));
         
       

        $arr_data = [];     
        $arr_data['academic_year_id']   = $academic_year;
        $arr_data['kid_no']             = $kid_no;
        $arr_data['discount']           = $discount;
        $arr_data['school_id']          = $this->school_id;
        
        $obj_exist = $this->BrotherhoodModel->where('school_id','=',$this->school_id)->where('kid_no','=',$kid_no)->where('academic_year_id','=',$academic_year)->where('id','<>',$id)->first();
        
        if(isset($obj_exist->id))
        {
            Flash::error($this->module_title." ".translation("already_exists"));
            return redirect()->back();            
        }
     
        $res = $this->BrotherhoodModel->where('id',$id)->update($arr_data);
        if($res){
            Flash::success($this->module_title." ".translation("updated_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_updating ".$this->module_title);
            return redirect()->back();
        }        
       
    }


}
