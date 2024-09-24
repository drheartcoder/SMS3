<?php
    
namespace App\Http\Controllers\Admin;
    
use Illuminate\Http\Request;
    
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;

use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\UserRoleModel;   
use App\Models\RoleModel;
use App\Models\ActivationModel;
use App\Models\MemberShipModel;
use App\Common\Services\LanguageService;

use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   

use App\Common\Services\EmailService;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;

class MembershipController extends Controller
{
     use MultiActionTrait;
     public function __construct(    
                                    UserModel $user,
                                    MemberShipModel $membership,
                                    UserRoleModel $user_role_model,
                                    RoleModel $role_model,
                                    ActivityLogsModel $activity_logs,
                                    LanguageService $language
                                )
     {
        $this->UserModel                    = $user;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->MemberShipModel              = $membership;
        $this->BaseModel                    = $this->MemberShipModel;
        $this->LanguageService              = $language;
        $this->ActivityLogsModel            = $activity_logs;  
        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/membership_plans");
        
        $this->module_title                 = translation("membership_plans");
        $this->modyle_url_slug              = translation("membership_plans");
        $this->edit_icon                    = 'fa fa-edit';
        $this->module_view_folder           = "admin.membership_plans";
        $this->theme_color                  = theme_color();

        $this->page_title = 'page_title' ;


        $this->first_name = $this->last_name =$this->ip_address ='';

        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->ip_address        = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;  
        }
        
     } 
        /*
        | index() : Membership plan listing 
        | Auther : Gaurav 
        | Date : 07-05-2018
        */

        public function index(Request $request)
	    {   
	    	$this->arr_view_data[$this->page_title]      = translation("manage")." ".$this->module_title;
	        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
	        $this->arr_view_data['module_title']    = $this->module_title;
	        $this->arr_view_data['module_url_path'] = $this->module_url_path;
	        $this->arr_view_data['theme_color']     = $this->theme_color;       
	        
	        return view($this->module_view_folder.'.index',$this->arr_view_data);
		}

        /*
        | create() : Membership plan create 
        | Auther : Gaurav 
        | Date : 07-05-2018
        */
	    public function create()
	    {
	    	$arr_id = [4,5,6,7];
	    	$arr_stackholder = $this->RoleModel->whereIn('id',$arr_id)->get()->toArray();

	        $page_title                             = translation("add")." ".str_plural($this->module_title);
	        $this->arr_view_data['edit_mode']       = TRUE;
	        $this->arr_view_data['arr_stackholder'] = $arr_stackholder;
	        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
	        $this->arr_view_data[$this->page_title]      = $page_title;
	        $this->arr_view_data['module_title']    = str_plural($this->module_title);
	        $this->arr_view_data['module_url_path'] = $this->module_url_path;
	        $this->arr_view_data['theme_color']     = $this->theme_color;
	        return view($this->module_view_folder.'.create', $this->arr_view_data);
	    }

        /*
        | create() : Membership plan store 
        | Auther : Gaurav 
        | Date : 07-05-2018
        */

	    public function store(Request $request)
	    {
	        $arr_rules  =   $messages = [];
    
            $required = 'required';
	        $arr_rules['plan_name']        =   $required;
	        $arr_rules['duration_value']  =   $required.'|numeric|min:1';
	        $arr_rules['price']           =   $required.'|numeric|min:1';
	        $arr_rules['stackholders']    =   $required;

            $messages['required']    =    translation('this_field_is_required');
	        $messages['min']         =   translation('please_enter_a_value_greater_than_or_equal_to_1');

	        $validator = Validator::make($request->all(),$arr_rules,$messages);

	        if($validator->fails())
	        { 
	            return redirect()->back()->withErrors($validator)->withInput($request->all());
	        }
	        
            $exist = $this->BaseModel->where('plan_name',$request->Input('plan_name'))->first();
            if(count($exist)>0)
            {
                Flash::error(translation('this_plan_is_already_exists'));
                return redirect()->back();
            }

	        $data           =   [];
	        $stackholders   = implode(",",$request->Input('stackholders'));
	        $data    =   [
                            'plan_name'      =>  $request->Input('plan_name'),
                            'duration_value' =>  $request->Input('duration_value'),
                            'duration_type'  =>  $request->Input('duration_type'),
                            'price'     	 =>  $request->Input('price'),
                            'stackholders'   =>  $stackholders
	                     ];
	        $status = $this->BaseModel->create($data);                   
	        if($status)
	        {
                $arr_event                 = [];
                $arr_event['ACTION']       = 'ADD';
                $arr_event['MODULE_TITLE'] = $this->module_title;

                $this->save_activity($arr_event);

	            Flash::success(str_plural($this->module_title).' '.translation('created_successfully'));
	        }
	        else
	        {
	            Flash::error(translation('problem_occured_while_creating').' '.str_singular($this->module_title));
	        }
        	
        	return redirect()->back();             	      
	    }

	    public function get_records(Request $request)
    	{

        $obj_custom = $this->get_fees_records($request);

        $are_you_sure ='are_you_sure';
        $yes = 'yes';
        $no = 'no';

        $role = Sentinel::findRoleById(1);
                
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;

        $json_result  = Datatables::of($obj_custom);

        $json_result = $json_result->blacklist(['id']);                    

        $json_result =  $json_result->editColumn('enc_id',function($data) use ($arr_current_user_access)
                        {
                            return  base64_encode(($data->id));
                        })
                        ->editColumn('build_action_btn',function($data) use ($arr_current_user_access,$are_you_sure,$yes,$no)
                        {
                            $build_status_btn = $build_edit_action ='';
                            if(array_key_exists('fees.update',$arr_current_user_access))
                            {
                                
                                if($data->is_active != null && $data->is_active == "0")
                                {   
                                    $build_status_btn = '<a class="blue-color" href="'.$this->module_url_path.'/activate/'.base64_encode($data->id).'" 
                                    onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation($are_you_sure).'\',\''.translation($yes).' \',\''.translation($no).'\')" title="'.translation('activate').'"><i class="fa fa-lock"></i></a>';
                                }
                                elseif($data->is_active != null && $data->is_active == "1")
                                {
                                    $build_status_btn = '<a class="light-blue-color" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation($are_you_sure).'\',\''.translation($yes).' \',\''.translation($no).'\')" title="'.translation('deactivate').'"><i class="fa fa-unlock"></i></a>';
                                }    
                                $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation($are_you_sure).'\',\''.translation($yes).' \',\''.translation($no).'\')"><i class="fa fa-trash" ></i></a>';
                                
                                $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';
                            }
                            
                            return $build_status_btn.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_delete_action;  
                        })
                        ->editColumn('duration_type',function($data)
                         {
                            
    						return isset($data->duration_type) && $data->duration_type!='' ? ucfirst($data->duration_type):'';                           
                         })
                        ->editColumn('duration_value',function($data)
                         {
                         	$duration_value  = '';
                            $duration_type   = isset($data->duration_type) && $data->duration_type!='' ? $data->duration_type:'';
                            if ($duration_type=='month') 
                            {
                               $duration_value = isset($data->duration_value) ? $data->duration_value.' Month':'';	          
                            }
                            if ($duration_type=='year') 
                            {
                               $duration_value = isset($data->duration_value) ? $data->duration_value.' Year':'';	          
                            }                           
    						return $duration_value;
                         })
                        ->editColumn('stackholders',function($data)
                         {
                            $stackholders = isset($data->stackholders) && $data->stackholders!='' ? $data->stackholders:'';
                            $arr_stack    = get_stackholders_name($stackholders);                           
    						
    						return implode(',', $arr_stack);
                         }) 
                         ->editColumn('build_checkbox',function($data)
                         {
                      
                                
                            return '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>';   
                         })                    
                        ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);
    }
    public function get_fees_records(Request $request)
    {

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }                     
        $custom_table = $this->BaseModel->getTable();   
        $prefixed_custom_table = DB::getTablePrefix().$this->BaseModel->getTable();                                             

        $obj_custom = DB::table($custom_table)
                        ->select(DB::raw(   
                                            $prefixed_custom_table.".id as id,".
                                            $prefixed_custom_table.".plan_name as plan_name,".
                                            $prefixed_custom_table.".duration_type as duration_type,".
                                            $prefixed_custom_table.".duration_value as duration_value,".
                                            $prefixed_custom_table.".price as price,".
                                            $prefixed_custom_table.".stackholders as stackholders,".
                                            $prefixed_custom_table.".is_active"
                                        ))
                                        ->whereNull($prefixed_custom_table.'.deleted_at')
                                        ->orderBy($prefixed_custom_table.'.id','DESC');

        $search = $request->input('search');
        $search_term = $search['value'];
        if($request->has('search') && $search_term!="")
        {
            $obj_custom = $obj_custom->WhereRaw("( (".$prefixed_custom_table.".plan_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_custom_table.".duration_type LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_custom_table.".duration_value LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_custom_table.".price LIKE '%".$search_term."%') ) ");
        }
        return $obj_custom;
    }

     /*
        | edit() : Membership plan edit 
        | Auther : Gaurav 
        | Date : 08-05-2018
        */
    public function edit($enc_id=FALSE)
    {
        $id = base64_decode($enc_id);
        
        $arr_id    = [4,5,6,7];
      
        $arr_data  = [];
        $obj_data  = $this->BaseModel
                            ->where('id',$id)
                            ->first();
                        
	    $obj_stackholder = $this->RoleModel->whereIn('id',$arr_id)->get();

        if($obj_data)
        {  		
            $arr_data     = $obj_data->toArray();
        }

        $obj_roll  = $this->RoleModel
                            ->select('id','name')
                            ->whereIn('id',$arr_id)
                            ->get();
                         
        if($obj_roll)
        {  		
            $arr_roll     = $obj_roll->toArray();
        }

        $this->arr_view_data['page_title']      = translation('edit')." ".$this->module_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['edit_mode']       = TRUE;
		     
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['edit_icon']       = $this->edit_icon;
        $this->arr_view_data['enc_id']          = $enc_id;
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['arr_roll']        = $arr_roll;        
        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }

     /*
        | update() : Membership plan update 
        | Auther : Gaurav 
        | Date : 08-05-2018
        */
    public function update(Request $request,$enc_id=FALSE)
    {
        $id = base64_decode($enc_id);     
        $required = 'required';
        $arr_rules  =   $messages = [];
        $duration_value = 'duration_value';
        $price = 'price';
        $str_stackholders = 'stackholders'; 
        $duration_type ='duration_type';

        $arr_rules['plan_name']       =   $required;
        $arr_rules[$duration_value]  =   $required.'|numeric|min:1';
        $arr_rules[$price]           =   $required.'|numeric|min:1';
        $arr_rules[$str_stackholders]    =   $required;
        $arr_rules[$duration_type]    =   $required;

        $messages['required']    =    translation('this_field_is_required');
        $messages['min']         =   translation('please_enter_a_value_greater_than_or_equal_to_1');

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

   		$data           =   [];
        $stackholders   = implode(",",$request->Input($str_stackholders));
        $data    =   [
                        'plan_name'      =>  $request->Input('plan_name'),
                        $duration_value =>  $request->Input($duration_value),
                        $duration_type  =>  $request->Input($duration_type),
                        $price     	 =>  $request->Input($price),
                        $str_stackholders   =>  $stackholders
                     ];
        $status = $this->BaseModel->where('id',$id)->update($data);                   
        if($status)
        {
            $arr_event                 = [];
            $arr_event['ACTION']       = 'EDIT';
            $arr_event['MODULE_TITLE'] = $this->module_title;

            $this->save_activity($arr_event);

            Flash::success(str_plural($this->module_title).' '.translation('updated_successfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_creating').' '.str_singular($this->module_title));
        }
    	
    	return redirect()->back(); 
    }
}