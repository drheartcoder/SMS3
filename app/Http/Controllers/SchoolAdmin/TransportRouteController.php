<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\TransportRouteModel;
use App\Models\TransportRouteStopsModel;
use App\Models\BusModel;
use App\Models\BusStudentsModel;
use App\Models\AcademicYearModel;
use App\Models\StudentModel;
/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
/*Activity Log */

use App\Common\Services\CommonDataService;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;
use Excel;
use PDF;
use APP;

class TransportRouteController extends Controller
{
    public function __construct(CommonDataService $CommonDataService)
    {
        $this->TransportRouteModel             = new TransportRouteModel();
        $this->BaseModel                       = $this->TransportRouteModel;
        $this->TransportRouteStopsModel        = new TransportRouteStopsModel();
        $this->BusModel                        = new BusModel();
        $this->BusStudentsModel                = new BusStudentsModel();
        $this->AcademicYearModel               = new AcademicYearModel();
        $this->StudentModel                    = new StudentModel();
        $this->CommonDataService               = $CommonDataService;

        $this->arr_view_data                   = [];
        $this->module_url_path                 = url(config('app.project.school_admin_panel_slug')).'/transport_route';
        
        $this->module_title                    = translation("transport_route");
        $this->modyle_url_slug                 = translation("transport_route");

        $this->module_view_folder              = "schooladmin.transport_route";
        $this->theme_color                     = theme_color();
        $this->module_icon                     = 'fa fa-road';
        $this->create_icon                     = 'fa fa-plus-circle';
        $this->edit_icon                       = 'fa fa-edit';
        $this->view_icon                       = 'fa fa-eye';

        $this->school_id                       = Session::get('school_id');
        $this->academic_year                   = Session::get('academic_year');
        $this->first_name 					   = $this->last_name =$this->ip_address ='';

        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['page_title']      = $this->module_title;
        $this->arr_view_data['create_icon']      = $this->create_icon;

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
        }
        /* Activity Section */

        /*Local Section*/
        if(Session::has('locale')){
            $this->locale = Session::get('locale');
        }
        else
        {
            $this->locale = 'en';
        }
        /*Local Section*/
    }   

    /*
    | create() : Manage Transport Bus index page 
    | Auther   : Akshay
    | Date 	   : 07-06-2018
    */
    public function index(Request $request)
    {   
        $page_title     = translation("manage")." ".str_plural($this->module_title);
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
		return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*
    | get_details() : To get the list of transport buses 
    | Auther  : Akshay
    | Date    : 08-06-2018
    */
    public function get_details(Request $request){
        $str_years                = $this->CommonDataService->get_academic_year_less_than($this->academic_year);

        $prefixed_academic_year       = DB::getTablePrefix().$this->AcademicYearModel->getTable();
        $prefixed_route_details       = DB::getTablePrefix().$this->BaseModel->getTable();
        $prefixed_route_stop_details  = DB::getTablePrefix().$this->TransportRouteStopsModel->getTable();
        $prefixed_bus_details         = DB::getTablePrefix().$this->BusModel->getTable();

        $obj_user = DB::table($prefixed_route_details)
                                ->select(DB::raw($prefixed_route_details.".id,".
                                                 $prefixed_bus_details.".bus_no, ".
                                                 $prefixed_bus_details.".bus_plate_no, ".
                                                 $prefixed_route_details.".route_name,".
                                                 "COUNT(".$prefixed_route_stop_details.".id) as total_stops,".
                                                 $prefixed_route_details.".target_location,".
                                                 "CONCAT(UCASE(LEFT(".$prefixed_route_details.".transport_type,1)),LCASE(SUBSTRING("
                                                          .$prefixed_route_details.".transport_type,2))) as transport_type"
                                        ))
                                ->whereNull($prefixed_route_details.'.deleted_at')
                                ->join($prefixed_academic_year,$prefixed_route_details.'.academic_year_id','=',$prefixed_academic_year.".id")
                                ->join($prefixed_bus_details,$prefixed_route_details.'.bus_id_fk','=',$prefixed_bus_details.".id")
                                ->join($prefixed_route_stop_details,$prefixed_route_details.'.id','=',$prefixed_route_stop_details.".route_id_fk")
                                ->where($prefixed_route_details.'.school_id','=',$this->school_id)
                                ->whereRaw($prefixed_route_details.".academic_year_id in (".$str_years.")")
                                ->groupBy($prefixed_route_details.'.id')
                                ->where($prefixed_route_details.'.deleted_at','=',NULL)
                                ->orderBy($prefixed_route_details.'.id','DESC');

            $search = $request->input('search');
            $search_term = $search['value'];

        if($request->has('search') && $search_term!=""){
            $obj_user = $obj_user->WhereRaw("((".$prefixed_bus_details.".bus_no LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$prefixed_bus_details.".bus_plate_no LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$prefixed_route_details.".route_name LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$prefixed_route_details.".target_location LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(CONCAT(UCASE(LEFT(".$prefixed_route_details.".transport_type,1)),LCASE(SUBSTRING(".$prefixed_route_details.".transport_type,2))) LIKE '%".$search_term."%')) ");
        }
        return $obj_user;
    }

    /*
    | get_records() : To get the list of transport buses 
    | Auther  : Akshay
    | Date    : 08-06-2018
    */
    public function get_records(Request $request){
        $arr_current_user_access =[];
    
        $role = Sentinel::findRoleBySlug(config('app.project.school_admin_panel_slug'));
        
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;
        $obj_user        = $this->get_details($request);

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        $json_result     = $json_result
                            ->editColumn('build_action_btn',function($data) use ($arr_current_user_access){
                                       
                            $build_delete_action =  $build_edit_action = $build_view_action =  ''; 
                            if(array_key_exists('transport_route.list', $arr_current_user_access)){
                                $view_href         =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                $build_view_action = '<a class="green-color" href="'.$view_href.'" title="view"><i class="fa fa-eye" ></i></a>';
                            }

                            if(array_key_exists('transport_route.update', $arr_current_user_access)){
                                $edit_href         =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="edit"><i class="fa fa-edit" ></i></a>';
                            }

                            if(array_key_exists('transport_route.delete', $arr_current_user_access)){ 
                            $delete_href          =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                            $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="Delete" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
	                        }
                            return $build_view_action.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_delete_action;
                                 
                            });
        
        $json_result =      $json_result->editColumn('build_checkbox',function($data)use($arr_current_user_access){
                                $build_checkbox = '';

                                if(array_key_exists('transport_route.update', $arr_current_user_access) || array_key_exists('transport_route.delete', $arr_current_user_access))
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
    | create(): Create Trapsort Bus
    | Auther  : Akshay
    | Date    : 08-06-2018
    */
    public function create(){
        $arr_data  = [];
        $str_years = $this->CommonDataService->get_academic_year_less_than($this->academic_year);

        $obj_data  = $this->BusModel->where('school_id','=',$this->school_id)->whereRaw('academic_year_id in ('.$str_years.')')->orderBy('id','desc')->get();

        if($obj_data){
           $arr_data = $obj_data->toArray();
        }
        $school_lat     = $this->CommonDataService->get_school_latitude();
        $school_lang    = $this->CommonDataService->get_school_longitude();
        $school_name    = $this->CommonDataService->get_school_name();
        $school_address = $this->CommonDataService->get_school_address();        

        $this->arr_view_data['school_name']     = $school_name;
        $this->arr_view_data['school_address']  = $school_address;
        $this->arr_view_data['school_lat']      = $school_lat;
        $this->arr_view_data['school_lang']     = $school_lang;
        $this->arr_view_data['page_title']      = translation('add')." ".$this->module_title;
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['create_icon']     = $this->create_icon;
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    /*
    | store() : Store Transport Bus
    | Auther  : Akshay
    | Date    : 07-06-2018
    */
    public function store(Request $request){
        $messages = $arr_rules = $arr_signed_students =[];
        $str_years = $this->CommonDataService->get_academic_year_less_than($this->academic_year);

        $arr_rules['bus_id']                  = 'required';
        $arr_rules['transport_type']          = 'required';
        $arr_rules['route_name']              = 'required';
        $arr_rules['target_location']         = 'required';
        $arr_rules['latitude']                = 'required';
        $arr_rules['longitude']               = 'required';
        $arr_rules['json_pickup_drop_point']  = 'required';
        //$arr_rules['json_arr_student']        = 'required';
        
        $messages['required']      =  translation('this_field_is_required');
        $validator                 = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $bus_id                     =   trim($request->input('bus_id'));
        $transport_type             =   trim($request->input('transport_type'));
        $route_name                 =   trim($request->input('route_name'));
        $target_location            =   trim($request->input('target_location'));
        $target_location_lat        =   trim($request->input('latitude'));
        $target_location_lang       =   trim($request->input('longitude'));
        $json_pickup_drop_point     =   trim($request->input('json_pickup_drop_point'));
        $arr_pickup_drop_point      =   json_decode($json_pickup_drop_point, true);
        
        if($request->input('json_arr_student')!='')
        {
            $json_arr_student           =   trim($request->input('json_arr_student'));
            $arr_signed_students        =   json_decode($json_arr_student, true);
        }
        
        $arr_data = [];     
        $arr_data['school_id']             = $this->school_id;
        $arr_data['academic_year_id']      = $this->academic_year;
        $arr_data['bus_id_fk']             = $bus_id;
        $arr_data['transport_type']        = $transport_type;
        $arr_data['route_name']            = $route_name;
        $arr_data['target_location']       = $target_location;
        $arr_data['target_location_lat']   = $target_location_lat;
        $arr_data['target_location_lang']  = $target_location_lang;
        
        $obj_exist =   $this->TransportRouteModel->where('school_id','=',$this->school_id)->where('academic_year_id','=',$this->academic_year)->where('bus_id_fk','=',$bus_id)->where('transport_type','=',$transport_type)->first();
        
        if(isset($obj_exist->id)){
            Flash::error($this->module_title." ".translation("already_exists"));
            return redirect()->back();            
        }
        
        //Insert Bus Route 
        $res = $this->TransportRouteModel->create($arr_data);
        if($res->id){
            //Insert Bus Route Stops 
            if(count($arr_pickup_drop_point)>0){
                foreach ($arr_pickup_drop_point as $key => $value){
                    $arr_tmp = [];
                    
                    $arr_tmp['route_id_fk'] = $res->id;
                    $arr_tmp['stop_no']     = $value['stop_no'];
                    $arr_tmp['stop_name']   = $value['stop_name'];
                    $arr_tmp['landmark']    = $value['landmark'];
                    $arr_tmp['stop_radius'] = $value['stop_radius'];
                    $arr_tmp['stop_lat']    = $value['lat'];
                    $arr_tmp['stop_lang']   = $value['lng'];
                    $arr_tmp['stop_fees']   = $value['stop_fees'];
                    
                    $result = $this->TransportRouteStopsModel->create($arr_tmp);
                }

                //Assign route stop, Route and Bus to student 
                if(count($arr_signed_students)>0){
                    $result_bus_students = false;
                    
                    foreach ($arr_signed_students as $_key => $stud_val){
                        $arr_stud = [];
                        $stops =   $this->TransportRouteStopsModel->where('route_id_fk','=',$res->id)->where('stop_no','=',$stud_val['stop_no'])->first();
                        if(isset($stops->id)){
                            if($stud_val['distance'] <= $stops->stop_radius){
                                $arr_stud['bus_id_fk']      = $bus_id;
                                $arr_stud['route_id_fk']    = $res->id;
                                $arr_stud['stop_id_fk']     = $stops->id;
                                $arr_stud['student_id']     = $stud_val['student_id'];
                                
                                $result_bus_students = $this->BusStudentsModel->create($arr_stud);
                            }
                        }
                    }   
                }
                Flash::success($this->module_title." ".translation("created_successfully"));
                return redirect()->back();
            }
        }
        Flash::error(translation("something_went_wrong_while_creating ").$this->module_title);
        return redirect()->back();
    }
     
    /*
    | edit()  : Edit  Transport Bus
    | Auther  : Akshay
    | Date    : 08-06-2018
    */
    public function edit($enc_id=FALSE)
    {
        if($enc_id!='')
        {
            $arr_bus = [];
            $obj_bus = false;

            $id = base64_decode($enc_id);
            $arr_data  = [];
            
            $str_years = $this->CommonDataService->get_academic_year_less_than($this->academic_year);

            $obj_bus  = $this->BusModel->where('school_id','=',$this->school_id)->whereRaw('academic_year_id in ('.$str_years.')')->orderBy('id','desc')->get();

            $obj_data = $this->TransportRouteModel->where('id',$id)
                                   ->with(['route_stop_details','bus_details','student_assigned_to_bus'])
                                   ->first();

            if($obj_bus!=false && $obj_data!=false){
               $arr_bus        = $obj_bus->toArray();
               $arr_data       = $obj_data->toArray();
               $school_lat     = $this->CommonDataService->get_school_latitude();
               $school_lang    = $this->CommonDataService->get_school_longitude();
               $school_name    = $this->CommonDataService->get_school_name();
               $school_address = $this->CommonDataService->get_school_address();        

                $this->arr_view_data['page_title']      = translation('edit')." ".$this->module_title;
                $this->arr_view_data['module_title']    = str_plural($this->module_title);
                $this->arr_view_data['school_name']     = $school_name;
                $this->arr_view_data['school_address']  = $school_address;
                $this->arr_view_data['school_lat']      = $school_lat;
                $this->arr_view_data['school_lang']     = $school_lang;
                $this->arr_view_data['edit_mode']       = TRUE;
                $this->arr_view_data['module_url_path'] = $this->module_url_path;
                $this->arr_view_data['enc_id']          = $enc_id;
                $this->arr_view_data['theme_color']     = $this->theme_color;
                $this->arr_view_data['module_icon']     = $this->module_icon;
                $this->arr_view_data['edit_icon']       = $this->edit_icon;
                $this->arr_view_data['arr_bus']         = $arr_bus;
                $this->arr_view_data['arr_data']        = $arr_data;
                return view($this->module_view_folder.'.edit',$this->arr_view_data);
            }
        }
        return redirect()->back();
    }

    /*
    | edit()  : Update  Transport Bus
    | Auther  : Akshay
    | Date    : 08-06-2018
    */
    public function update(Request $request,$enc_id=FALSE)
    {
        $id = base64_decode($enc_id);    
        
        $messages  = $arr_rules = $arr_signed_students = [];
        $str_years = $this->CommonDataService->get_academic_year_less_than($this->academic_year);

        $arr_rules['bus_id']                  = 'required';
        $arr_rules['transport_type']          = 'required';
        $arr_rules['route_name']              = 'required';
        $arr_rules['target_location']         = 'required';
        $arr_rules['latitude']                = 'required';
        $arr_rules['longitude']               = 'required';
        $arr_rules['json_pickup_drop_point']  = 'required';
        //$arr_rules['json_arr_student']        = 'required';
        
        $messages['required']      =  translation('this_field_is_required');
        $validator                 = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $bus_id                     =   trim($request->input('bus_id'));
        $transport_type             =   trim($request->input('transport_type'));
        $route_name                 =   trim($request->input('route_name'));
        $target_location            =   trim($request->input('target_location'));
        $target_location_lat        =   trim($request->input('latitude'));
        $target_location_lang       =   trim($request->input('longitude'));
        $json_pickup_drop_point     =   trim($request->input('json_pickup_drop_point'));
        $arr_pickup_drop_point      =   json_decode($json_pickup_drop_point, true);
        
        if($request->input('json_arr_student')!='')
        {
            $json_arr_student           =   trim($request->input('json_arr_student'));
            $arr_signed_students        =   json_decode($json_arr_student, true);
        }
        
        $arr_data = [];     
        $arr_data['school_id']             = $this->school_id;
        $arr_data['academic_year_id']      = $this->academic_year;
        $arr_data['bus_id_fk']             = $bus_id;
        $arr_data['transport_type']        = $transport_type;
        $arr_data['route_name']            = $route_name;
        $arr_data['target_location']       = $target_location;
        $arr_data['target_location_lat']   = $target_location_lat;
        $arr_data['target_location_lang']  = $target_location_lang;
        
        $obj_exist =   $this->TransportRouteModel->where('school_id','=',$this->school_id)->where('academic_year_id','=',$this->academic_year)->where('bus_id_fk','=',$bus_id)->where('transport_type','=',$transport_type)->where('id','<>',$id)->first();
        
        if(isset($obj_exist->id)){
            Flash::error($this->module_title." ".translation("already_exists"));
            return redirect()->back();            
        }
        
        //update Bus Route 
        $res = $this->TransportRouteModel->where('id',$id)->update($arr_data);
        if($res){
            //Insert Bus Route Stops 
            if(count($arr_pickup_drop_point)>0){
                
                $delete_old_stops = $this->TransportRouteStopsModel->where('route_id_fk',$id)->delete();
                
                foreach ($arr_pickup_drop_point as $key => $value){
                    $arr_tmp = [];
                    
                    $arr_tmp['route_id_fk'] = $id;
                    $arr_tmp['stop_no']     = $value['stop_no'];
                    $arr_tmp['stop_name']   = $value['stop_name'];
                    $arr_tmp['landmark']    = $value['landmark'];
                    $arr_tmp['stop_radius'] = $value['stop_radius'];
                    $arr_tmp['stop_lat']    = $value['lat'];
                    $arr_tmp['stop_lang']   = $value['lng'];
                    $arr_tmp['stop_fees']   = $value['stop_fees'];

                    $result = $this->TransportRouteStopsModel->create($arr_tmp);
                }

                //Assign route stop, Route and Bus to student 
                if(count($arr_signed_students)>0){
                    $result_bus_students = false;
                    
                    $delete_old_bus_students = $this->BusStudentsModel->where('route_id_fk',$id)->delete();
                    
                    foreach ($arr_signed_students as $_key => $stud_val){
                        $arr_stud = [];
                        $stops =   $this->TransportRouteStopsModel->where('route_id_fk','=',$id)->where('stop_no','=',$stud_val['stop_no'])->first();
                        if(isset($stops->id)){
                            if($stud_val['distance'] <= $stops->stop_radius){
                                $arr_stud['bus_id_fk']      = $bus_id;
                                $arr_stud['route_id_fk']    = $id;
                                $arr_stud['stop_id_fk']     = $stops->id;
                                $arr_stud['student_id']     = $stud_val['student_id'];
                                
                                $result_bus_students     = $this->BusStudentsModel->create($arr_stud);
                            }
                        }
                    }
                }
                Flash::success($this->module_title." ".translation("updated_successfully"));
                return redirect()->back();
            }
        }
        Flash::error(translation("something_went_wrong_while_updating ").$this->module_title);
        return redirect()->back();        
       
    }

    /*
    | view()  : View  Transport Bus
    | Auther  : Akshay
    | Date    : 02-06-2018
    */    
    public function view($enc_id=FALSE)
    {
        $id = base64_decode($enc_id);
        $arr_data = $arr_bus = [];
        $str_years = $this->CommonDataService->get_academic_year_less_than($this->academic_year);
        $obj_data = $this->TransportRouteModel->where('id',$id)
                                   ->with(['route_stop_details','bus_details'])
                                   ->first();
        if($obj_data){
            $arr_data = $obj_data->toArray();
            $school_lat     = $this->CommonDataService->get_school_latitude();
            $school_lang    = $this->CommonDataService->get_school_longitude();
            $school_name    = $this->CommonDataService->get_school_name();
            $school_address = $this->CommonDataService->get_school_address();        
            $this->arr_view_data['page_title']      = translation('view')." ".$this->module_title;
            $this->arr_view_data['module_title']    = str_plural($this->module_title);
            $this->arr_view_data['school_name']     = $school_name;
            $this->arr_view_data['school_address']  = $school_address;
            $this->arr_view_data['school_lat']      = $school_lat;
            $this->arr_view_data['school_lang']     = $school_lang;
            $this->arr_view_data['view_mode']       = TRUE;
            $this->arr_view_data['module_url_path'] = $this->module_url_path;
            $this->arr_view_data['enc_id']          = $enc_id;
            $this->arr_view_data['theme_color']     = $this->theme_color;
            $this->arr_view_data['module_icon']     = $this->module_icon;
            $this->arr_view_data['view_icon']       = $this->view_icon;
            $this->arr_view_data['arr_data']        = $arr_data;
            return view($this->module_view_folder.'.view',$this->arr_view_data);
        }
        return redirect()->back();
    }

    /*
    | get_non_student_list()  : Get Non Assigned Students List
    | Auther        : Akshay
    | Date          : 08-06-2018
    */
    public function get_non_student_list(Request $request)
    {    
        $arr_data                = [];
        $arr_data['msg']         = '';
        $arr_data['status']      = 'fail';
        $route_id                = '';

        if(Session::has('bus_id')){
            Session::put('bus_id',$request->bus_id);
        }
        else{
            Session::set('bus_id',$request->bus_id);
        }

        if(Session::has('transport_type')){
            Session::put('transport_type',$request->transport_type);
        }
        else{
            Session::set('transport_type',$request->transport_type);
        }

        if($request->has('transport_type') && $request->get('transport_type')!='' && $request->has('bus_id') && $request->get('bus_id')!='' ){
            $bus_id = $request->bus_id;    
            $transport_type = $request->get('transport_type');
            if($request->has('route') && $request->get('route')!='')
            {
                $route_id = base64_decode($request->get('route'));
            }
            
            $str_years = $this->CommonDataService->get_academic_year_less_than($this->academic_year);
            $obj_student_query = $this->StudentModel->where('has_left','=',0)
                                            ->select('school_id','student_no','user_id','pickup_location','drop_location','pickup_address','drop_address')
                                            ->where('school_id','=',$this->school_id)
                                            ->where('bus_transport','=',1)
                                            ->where('academic_year_id',$this->academic_year)
                                            ->with(['get_user_details' => function ($query) {
                                                $query->select('id')->orderBy('id','ASC')->where('is_active','=',1);
                                            }]);
                                            if($transport_type=="pickup"){
                                                $obj_student_query->orderBy('pickup_address','desc');
                                            }
                                            else{
                                                $obj_student_query->orderBy('drop_address','asc');
                                            }
                        $obj_student_query->whereHas('get_bus_assigned_to_student',function($query){})
                                            ->with('get_bus_assigned_to_student');
            
            $obj_student_list = $obj_student_query->get();
            
            $school_latitude = $this->CommonDataService->get_school_latitude();
            $school_longitude = $this->CommonDataService->get_school_longitude();  
            if($obj_student_list){
               $arr_student = $obj_student_list->toArray();
               $arr_data['status']      = 'success';
               $arr_data['msg']         = 'Student List fetched successfully.';
               $arr_data['arr_student'] = $arr_student;
               $arr_data['school_latitude'] = $school_latitude;
                $arr_data['school_longitude'] = $school_longitude;
            }
        }
        return response()->json($arr_data);
    }

    /*
    | get_non_student_list()  : Get Assigned Students List
    | Auther        : Akshay
    | Date          : 08-06-2018
    */
    public function get_assigned_student_list(Request $request)
    {    
        $arr_data                = [];
        $arr_data['msg']         = '';
        $arr_data['status']      = 'fail';

        if($request->has('transport_type') && $request->get('transport_type')!=''){
            $transport_type = $request->get('transport_type');

            $str_years = $this->CommonDataService->get_academic_year_less_than($this->academic_year);
                $obj_student_query = $this->BusStudentsModel->with(['student_details' => function ($query) use($str_years) {
                                                                 $query->select('user_id','school_id','student_no','user_id','pickup_location','drop_location','pickup_address','drop_address')
                                                                  ->where('has_left','=',0)
                                                                  ->where('school_id','=',$this->school_id)
                                                                  ->where('bus_transport','=',1)
                                                                  ->where('academic_year_id',$this->academic_year)
                                                                  ->with(['get_user_details' => function ($sub_query) {
                                                                        $sub_query->select('id')->orderBy('id','ASC')->where('is_active','=',1);
                                                                    }]);
                                                                }])
                                                           ->with(['route_details' => function($query) use ($transport_type) {
                                                                  $query->select('*')->where('transport_type','=',$transport_type);  
                                                                 }]);
                
               if($request->has('route') && $request->get('route')!='')
               {
                   $route_id = base64_decode($request->get('route')); 
                   $obj_student_query->where('route_id_fk',$route_id);
               }
               $obj_student_list = $obj_student_query->get();
                
               if($obj_student_list){
                   $arr_student = $obj_student_list->toArray();
                   $arr_data['status']      = 'success';
                   $arr_data['msg']         = 'Student List fetched successfully.';
                   $arr_data['arr_student'] = $arr_student;
                   

               }
        }

        return response()->json($arr_data);
    }

    /*
    | get_non_student_list()  : Get Bus Capacity
    | Auther                  : Akshay
    | Date                    : 08-06-2018
    */
    public function get_bus_capacity(Request $request)
    { 
        $arr_data                 = [];
        $arr_data['msg']          = '';
        $arr_data['status']       = 'fail';
        $arr_data['bus_capacity'] = 0;
        $current_bus_capacity     = 0;
        
        if($request->has('transport_type') && $request->has('bus_id')!='' ){
            $transport_type       = $request->input('transport_type');
            $bus_id               = $request->input('bus_id');
            $str_years = $this->CommonDataService->get_academic_year_less_than($this->academic_year);
            
            $obj_bus_capacity = $this->BusModel->where('id','=',$bus_id)->first();
            
            if(isset($obj_bus_capacity->id)){
                $arr_capacity         = $obj_bus_capacity->toArray();
                $current_bus_capacity = $arr_capacity['bus_capacity'];

                $assigned_bus_count = $this->BusStudentsModel->with(['student_details' => function ($query) use($str_years) {
                                                                 $query->select('user_id','school_id','student_no','user_id','pickup_location','drop_location','pickup_address','drop_address')
                                                                  ->where('has_left','=',0)
                                                                  ->where('school_id','=',$this->school_id)
                                                                  ->where('bus_transport','=',1)
                                                                  ->where('academic_year_id',$this->academic_year)
                                                                  ->with(['get_user_details' => function ($sub_query) {
                                                                        $sub_query->select('id')->orderBy('id','ASC')->where('is_active','=',1);
                                                                    }]);
                                                                }])
                                                           ->with(['route_details' => function($query) use ($transport_type) {
                                                                  $query->select('*')->where('transport_type','=',$transport_type);  
                                                                 }])
                                                           ->where('bus_id_fk',$bus_id)
                                                           ->count();
                $arr_data['status']       = 'success';
                $arr_data['msg']          = 'Student List fetched successfully.';
                $arr_data['bus_capacity'] = $current_bus_capacity - $assigned_bus_count;
            }
        }

        return response()->json($arr_data);
    }    

    /*
    | check_if_route_exists()  : Check if route for this bus exists
    | Auther                   : Akshay
    | Date                     : 08-06-2018
    */
    public function check_if_route_exists(Request $request)
    { 
        $arr_data                 = [];
        $arr_data['msg']          = '';
        $arr_data['status']       = 'fail';
        
        if($request->has('transport_type') && $request->has('bus_id')!='' ){
            $transport_type       = $request->input('transport_type');
            $bus_id               = $request->input('bus_id');
            $obj_bus_capacity     = $this->BusModel->where('id','=',$bus_id)->first();
            if(isset($obj_bus_capacity->id)){

                $is_exist_obj = $this->TransportRouteModel->where('bus_id_fk',$bus_id)
                                                          ->where('transport_type',$transport_type)
                                                          ->where('academic_year_id',$this->academic_year)
                                                          ->where('school_id',$this->school_id);
                
                if($request->has('enc_route_id') && $request->input('enc_route_id')!='')
                {
                    $id = base64_decode($request->input('enc_route_id'));
                    $is_exist_obj = $is_exist_obj->where('id','<>',$id);
                }
                
                $is_exist = $is_exist_obj->count();
                if($is_exist > 0)
                {
                    $arr_data['status'] = 'success';
                    $arr_data['msg']    = translation('route_for_this_bus_is_already_exists');
                }
            }
        }

        return response()->json($arr_data);
    }

    /*
    | multiple_delete()  : Multiple Delete
    | Auther        : Akshay
    | Date          : 11-06-2018
    */    
    public function multiple_delete(Request $request)
    {
        $arr_rules = array();
        $arr_rules['multiple_delete'] = "required";
        $arr_rules['checked_record'] = "required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            Flash::error(translation('please_select_record_to_perform_multiaction'));
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $multiple_delete = $request->input('multiple_delete');
        $checked_record = $request->input('checked_record');

        /* Check if array is supplied*/
        if(is_array($checked_record) && sizeof($checked_record)<=0)
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction') );
            return redirect()->back();
        }

        foreach ($checked_record as $record_id) 
        {  
            if($multiple_delete=="delete")
            {
               $this->perform_delete(base64_decode($record_id));    
               Flash::success($this->module_title.' '.translation('deleted_succesfully')); 
            } 
        }

        return redirect()->back();
    }

    /*
    | delete()      : Delete Record
    | Auther        : Akshay
    | Date          : 11-06-2018
    */    
    public function delete($enc_id = FALSE)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_delete(base64_decode($enc_id)))
        {
            Flash::success($this->module_title.' '.translation('deleted_succesfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction'));
        }

        return redirect()->back();
    }

    /*
    | perform_delete()      : Inline Function for Deleting Records
    | Auther                : Akshay
    | Date                  : 11-06-2018
    */
    public function perform_delete($id)
    {
        $delete = $this->BaseModel->where('id',$id)->delete();
        
        if($delete)
        {
            $delete_stops = $this->TransportRouteStopsModel->where('route_id_fk',$id)->delete();  
            $delete_assigned_students = $this->BusStudentsModel->where('route_id_fk',$id)->delete();  
            return TRUE;
        }

        return FALSE;
    }  
  
}