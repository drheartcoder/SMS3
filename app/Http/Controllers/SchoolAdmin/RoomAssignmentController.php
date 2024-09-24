<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Common\Traits\MultiActionTrait;

use App\Http\Requests;
use App\Models\SchoolRoleModel;
use App\Models\RoomManagementModel;
use App\Models\RoomAssignmentModel;
use App\Models\LevelModel;
use App\Models\ClassModel;
Use App\Models\ClassTranslationModel;
use App\Models\LevelClassModel;
use App\Models\LevelTranslationModel;
use App\Common\Services\CommonDataService;


use DB;
use Flash;
use Sentinel;
use Session;
use Validator;
use Datatables;

class RoomAssignmentController extends Controller
{
    use MultiActionTrait;
    	
    public function __construct(SchoolRoleModel $school_role,
    							RoomAssignmentModel $room_assignment,
    							RoomManagementModel $room_management,
    							LevelClassModel $level_class,
    							LevelTranslationModel $level_translation,
    							ClassModel 		$class,
    							ClassTranslationModel $class_translation,
                                CommonDataService $commonData)
    {
    	$this->SchoolRoleModel            = $school_role;	
    	$this->RoomAssignmentModel        = $room_assignment;
    	$this->BaseModel                  = $this->RoomAssignmentModel;
    	$this->RoomManagementModel 		  = $room_management;	
		$this->LevelClassModel 			  = $level_class;	
    	$this->LevelTranslationModel      = $level_translation;	
    	$this->ClassModel 			  	  = $class;	
    	$this->ClassTranslationModel 	  = $class_translation;	
        $this->CommonDataService          = $commonData;
		$this->module_url_path 	          = url(config('app.project.role_slug.school_admin_role_slug')."/room/assignment");
		$this->module_view_folder         = "schooladmin.room_assignment";
		$this->module_title               = translation('room_assignment');
		$this->theme_color                = theme_color();
		$this->module_icon                = 'fa fa-home';
		$this->create_icon                = 'fa fa-plus-circle';
		$this->edit_icon                  = 'fa fa-edit';
        $this->school_id                  =  \Session::has('school_id') ? \Session::get('school_id') : '0'; 
        $this->academic_year              =  Session::get('academic_year');
         /* Activity Section */
        $obj_data          = Sentinel::getUser();
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
    | Date    : 8-05-2018
    */
    public function index(){

        $page_title = translation("manage")." ".translation("assigned_rooms");
        
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    /*
    | get_details() : To get the List Room Assignment
    | Auther  : Padmashri
    | Date    : 8-05-2018
    */
    public function get_details(Request $request){

    	$locale = '';

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }

        $room_details             = $this->BaseModel->getTable();
        $prefixed_room_details    = DB::getTablePrefix().$this->BaseModel->getTable();

 	    $room_management_details             = $this->RoomManagementModel->getTable();
        $prefixed_room_management_details    = DB::getTablePrefix().$this->RoomManagementModel->getTable();
        $level_class             			 = $this->LevelClassModel->getTable();
        $level_trans 						 = $this->LevelTranslationModel->getTable();
        $class_trans             			 = $this->ClassTranslationModel->getTable();
        

        
      
        $obj_user = DB::table($room_details)
                                ->select(DB::raw(
                                				$prefixed_room_details.".id  as id,".
                                				$prefixed_room_details.".room_management_id  as    room_management_id,".
                                                 $prefixed_room_details.".room_name, ".
                                                 $prefixed_room_details.".room_no, ".
                                                 $prefixed_room_management_details.".tag_name, ".
                                             	 $prefixed_room_management_details.".floor_no, ".
                                             	 $prefixed_room_management_details.".no_of_rooms, ".
                                             	 $level_trans.".level_name,".
                                             	 $class_trans.".class_name"))
                                ->join($room_management_details,$room_details.'.room_management_id','=',$room_management_details.'.id')
                                ->join($level_class,$prefixed_room_details.'.level_class_id', ' = ',$level_class.'.id')
                                ->join($level_trans,$level_trans.'.level_id', ' = ',$level_class.'.level_id')
                                ->join($class_trans,$class_trans.'.class_id', ' = ',$level_class.'.class_id')
                                ->where($level_trans.'.locale','=',$locale)
                                ->where($class_trans.'.locale','=',$locale)
                                ->whereNull($room_details.'.deleted_at')
                                ->where($room_management_details.'.school_id','=',$this->school_id)
                                ->orderBy($room_details.'.id','desc');



        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
            $obj_user = $obj_user->WhereRaw("( (".$room_management_details.".tag_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$room_management_details.".floor_no LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_room_details.".room_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_room_details.".room_no LIKE '%".$search_term."%') ") 
                                     ->orWhereRaw("(".$class_trans.".class_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$level_trans.".level_name LIKE '%".$search_term."%')) ");
        }
        /* ---------------- Filtering Logic ----------------------------------*/                    
        return $obj_user;
    }

    /*
    | get_records() : To get the List Room Assignment
    | Auther  : Padmashri
    | Date    : 8-05-2018
    */
    public function get_records(Request $request){

        $role = Session::get('role');
        $arr_current_user_access =[];
    
        $arr_current_user_access = $this->CommonDataService->current_user_access();
        $obj_user        = $this->get_details($request);
        $current_context = $this;

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
      
            $json_result     = $json_result->editColumn('enc_id',function($data) use ($current_context){
                                return base64_encode($data->id);
                            });
                          
        
            
             $json_result     = $json_result->editColumn('build_action_btn',function($data) use ($current_context,$arr_current_user_access){
                                           
                                $build_delete_action =  $build_edit_action =  ''; 
                               if(array_key_exists('room_assignment.update', $arr_current_user_access))
                               {
                                $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="edit"><i class="fa fa-edit" ></i></a>';
                               }
                                    
                               if(array_key_exists('room_assignment.delete', $arr_current_user_access))
                               { 
                                $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                $build_delete_action = '<a class="red-color"  href="'.$delete_href.'" title="Delete" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';

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
    | create() : Create Room Assignment
    | Auther  : Padmashri
    | Date    : 8-05-2018
    */
    public function create(){

    	$obj_room_data = $arr_room_data = array();

    	$obj_room_data = RoomManagementModel::select('tag_name','id','no_of_rooms')->where('school_id','=',$this->school_id)->groupBy('tag_name')->orderBy('id','desc')->get();
    	if(!empty($obj_room_data))
    	{
    		$arr_room_data = $obj_room_data->toArray();
    	}


    	$obj_level = $arr_level = [];

    	$obj_level  =   $this->CommonDataService->get_levels($this->academic_year);
    	if($obj_level){
    		$arr_level = $obj_level->toArray();
    	}	

        $this->arr_view_data['page_title']      = translation('assign_room');
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['create_icon'] = $this->create_icon;
        $this->arr_view_data['arr_room_data'] = $arr_room_data;
        $this->arr_view_data['arr_level']     = $arr_level;
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    /*
    | store() : Store  Room Assignment
    | Auther  : Padmashri
    | Date    : 8-05-2018
    */
    public function store(Request $request){
        
        $arr_rules = [];
        $arr_rules['tag']    		= 'required';
        $arr_rules['floor_no']      = 'required';
        $arr_rules['room_name'] 	= 'required';
		$arr_rules['room_number']   = 'required';
        $arr_rules['assign_level']  = 'required';
        $arr_rules['assign_class']  = 'required';

        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        
        $floor_no      =   trim($request->input('floor_no'));
        $room_name     =   trim($request->input('room_name'));
		$room_number   =   trim($request->input('room_number'));
        $assign_level  =   trim($request->input('assign_level'));
        $assign_class  =   trim($request->input('assign_class'));
        
         
        /* Bring the total no of rooms as per floor  */
		$totalRooms = RoomManagementModel::select('no_of_rooms')->where('school_id','=',$this->school_id)->where('id','=',$floor_no)->first();        
		 
		$arrtotalRooms = $totalRooms->toArray();
		if($room_number > $arrtotalRooms['no_of_rooms'] )
		{
			Flash::error($this->module_title." ".translation("room_no_can_not_be_greater_than_no_of_rooms_for_that_floor"));
            return redirect()->back();
		}



		$obj_level_class =  LevelClassModel::select('*')->where('level_id','=',$assign_level)->where('class_id','=',$assign_class)->first();

		$level_class_id   = 0;
		if($obj_level_class){
			$arr_level_class = $obj_level_class->toArray();
			$level_class_id  =  $arr_level_class['id']; 
		}


        $arr_data = [];     
        $arr_data['school_id']   		= $this->school_id;
        $arr_data['room_management_id'] = $floor_no;
        $arr_data['room_name'] 		    = $room_name;
        $arr_data['room_no'] 			= $room_number;
        $arr_data['level_class_id'] 	= $level_class_id;
        
        
        $obj_exist = $this->RoomAssignmentModel->where('room_management_id','=',$arr_data['room_management_id'])->where('room_no','=',$arr_data['room_no'])->first();
        
        if(isset($obj_exist->id))
        {
            Flash::error($this->module_title." ".translation("already_exists"));
            return redirect()->back();            
        }                
        
        $res = $this->RoomAssignmentModel->create($arr_data);
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
    | Date    : 8-05-2018
    */
    public function edit($enc_id=FALSE)
    {
        $id = base64_decode($enc_id);
        $obj_data = $arr_data = [];
        $obj_data = $this->RoomAssignmentModel
        				 ->whereHas('get_level_class',function($q){
        				 			 $q->select('id','level_id','class_id');
        				 			})
        				 ->whereHas('get_room_management',function($q){
        				 				$q->select('id','no_of_rooms','tag_name','floor_no');	
        				 			})
        				 ->with(['get_level_class'=>function($q){
        				 			 $q->select('id','level_id','class_id');
        				 			},
        				 		 'get_room_management'=>function($q){
        				 		 	$q->select('id','no_of_rooms','tag_name','floor_no');	
        				 		 }
        				 		])
        				 ->where('id','=',$id)
        				 ->first();

        if($obj_data)
        {
           $arr_data = $obj_data->toArray();
        }


        $obj_level = $arr_level = [];
		$obj_level  =   $this->CommonDataService->get_levels($this->academic_year);
    	if($obj_level){
    		$arr_level = $obj_level->toArray();
    	}	



    	$obj_class = $arr_class = [];
    	$obj_class  =   LevelClassModel::where('school_id',$this->school_id)
    									->whereHas('get_class',function($q){
    											$q->where('is_active','=',1);
    										})
    									->with(['get_class' => function($q){
    											$q->where('is_active','=',1);
    									}])
    									->groupBy('level_id')
    									->get();
    	if($obj_class){
    		$arr_class = $obj_class->toArray();
    	}	


    	$obj_tags = $resTags = array();
    	$obj_tags = RoomManagementModel::where('tag_name','=',isset($arr_data['get_room_management']['tag_name'])?$arr_data['get_room_management']['tag_name']:'')->where('school_id','=',$this->school_id)->orderBy('floor_no','desc')->get();
    	if($obj_tags){
    		$resTags = $obj_tags->toArray();
    	}


    	$obj_room_data = $arr_room_data = array();
		$obj_room_data = RoomManagementModel::select('tag_name','id','no_of_rooms')->where('school_id','=',$this->school_id)->groupBy('tag_name')->orderBy('id','desc')->get();
    	if(!empty($obj_room_data))
    	{
    		$arr_room_data = $obj_room_data->toArray();
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
        $this->arr_view_data['resTags']     = $resTags;
        $this->arr_view_data['arr_class']   = $arr_class;
        $this->arr_view_data['arr_level']    = $arr_level;
        $this->arr_view_data['arr_room_data']= $arr_room_data;
        
        
        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }

    /*
    | edit()  : Update  Room Assignment
    | Auther  : Padmashri
    | Date    : 8-05-2018
    */
    public function update(Request $request,$enc_id=FALSE)
    {
        $id = base64_decode($enc_id);     

        $arr_rules['tag']    		= 'required';
        $arr_rules['floor_no']      = 'required';
        $arr_rules['room_name'] 	= 'required';
		$arr_rules['room_number']   = 'required';
        $arr_rules['assign_level']  = 'required';
        $arr_rules['assign_class']  = 'required';

        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $floor_no      =   trim($request->input('floor_no'));
        $room_name     =   trim($request->input('room_name'));
		$room_number   =   trim($request->input('room_number'));
        $assign_level  =   trim($request->input('assign_level'));
        $assign_class  =   trim($request->input('assign_class'));
        
         
        /* Bring the total no of rooms as per floor  */
		$totalRooms = RoomManagementModel::select('no_of_rooms')->where('school_id','=',$this->school_id)->where('id','=',$floor_no)->first();        
		 
		$arrtotalRooms = $totalRooms->toArray();
		if($room_number > $arrtotalRooms['no_of_rooms'] )
		{
			Flash::error($this->module_title." ".translation("room_no_cannot_be_greater_than_no_of_rooms_for_that_floor"));
            return redirect()->back();
		}
        
        $obj_level_class =  LevelClassModel::select('*')->where('level_id','=',$assign_level)->where('class_id','=',$assign_class)->first();

		$level_class_id   = 0;
		if($obj_level_class){
			$arr_level_class = $obj_level_class->toArray();
			$level_class_id  =  $arr_level_class['id']; 
		}


        $arr_data = [];     
        $arr_data['room_management_id'] = $floor_no;
        $arr_data['room_name'] 		    = $room_name;
        $arr_data['room_no'] 			= $room_number;
        $arr_data['level_class_id'] 	= $level_class_id;
        
        
        $obj_exist = $this->RoomAssignmentModel->where('room_management_id','=',$arr_data['room_management_id'])->where('room_no','=',$arr_data['room_no'])->where('id','<>',$id)->first();
        
        if(isset($obj_exist->id))
        {
            Flash::error($this->module_title." ".translation("already_exists"));
            return redirect()->back();            
        }
 	 
 	    $res = $this->RoomAssignmentModel->where('id',$id)->update($arr_data);
        if($res){
            Flash::success($this->module_title." ".translation("updated_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_updating ".$this->module_title);
            return redirect()->back();
        }        
       
    }


    /*
    | get_floors()  : To get the floors for  Room Assignment,its ajax 
    | Auther  : Padmashri
    | Date    : 8-05-2018
    */
    public function get_floors(Request $request)
    {
    	
		$rules = array(
            'room_management'    => 'required',
        );
        $messages = array(
            'room_management.required'     => 'Please select tag.',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        
        if($validator->fails())
        {
            return json_encode([
                'errors' => $validator->errors()->getMessages(),
                'code' => 422,
                'status' => 'fail',
            ]);
        }
        else
        {	
            $dbValues = $finalValue = '';
            $resTags  = array();
            $tag_name      		   = trim($request->input('tag_name'));
			$resTags     		   = RoomManagementModel::where('tag_name','=',$tag_name)->where('school_id','=',$this->school_id)->orderBy('floor_no','desc')->get();
            if(!empty($resTags)){
                $status    = 'done';
                $resTagArr = $resTags->toArray();
            
                foreach ($resTagArr as $row) {
                    $dbValues .= '{"id":"'.$row['id'].'","name":"'.ucwords(strtolower($row['floor_no'])).'","totalRooms":"'.$row['no_of_rooms'].'"},';
                }
                $dbValues   = substr($dbValues, 0, -1);
                $userMsg  	= 'Done';
                $finalValue = "[$dbValues]";
            }
            else{
                $userMsg = 'Not Found';
            }
        }
        $resp = array('status' => $status,'message'=>$userMsg,'errors' => '','categories' => $finalValue);
        return response()->json($resp);
    }

    /*
    | get_class()  : To get the floors for  Room Assignment,its ajax 
    | Auther  : Padmashri
    | Date    : 8-05-2018
    */
    public function get_class(Request $request)
    {
    	$status = 'fail';
		$rules = array(
            'level_id'    => 'required',
        );
        $messages = array(
            'level_id.required'     => 'Please select tag.',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        
        if($validator->fails())
        {
            return json_encode([
                'errors' => $validator->errors()->getMessages(),
                'code' => 422,
                'status' => 'fail',
            ]);
        }
        else
        {	
            $dbValues = $finalValue = '';
            $arr_class = $obj_class = array();
            $level_id = trim($request->input('level_id'));
            $obj_class = $this->CommonDataService->get_class($level_id);

    	if($obj_class){
    		$arr_class = $obj_class->toArray();
    	 
	            if(!empty($arr_class)){
	                $status    = 'done';
	            	foreach ($arr_class as $row) {
	                	$dbValues .= '{"id":"'.$row['class_id'].'","name":"'.ucwords(strtolower($row['class_details']['class_name'])).'"},';
	                }
	                $dbValues   = substr($dbValues, 0, -1);
	                $userMsg  	= 'Done';
	                $finalValue = "[$dbValues]";
	            }
	            else{
	                $userMsg = 'Not Found';
	            }
        	}
    	}
        $resp = array('status' => $status,'message'=>$userMsg,'errors' => '','categories' => $finalValue);
        return response()->json($resp);
    }


     
}


