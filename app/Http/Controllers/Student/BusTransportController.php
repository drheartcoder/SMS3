<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\BusStudentsModel;
use App\Common\Services\CommonDataService;
use App\Models\StudentModel;

use Session;
use Validator;
use Flash;
use Sentinel;

class BusTransportController extends Controller
{
	public function __construct(CommonDataService $CommonDataService){
		$this->BusStudentsModel = new BusStudentsModel();
        $this->CommonDataService  = $CommonDataService;
        $this->StudentModel = new StudentModel();

		$this->arr_view_data     = [];
        $this->module_url_path   = url(config('app.project.student_panel_slug')).'/transport_bus';
        $this->module_title      = translation('transport_bus');
        
        $this->module_view_folder           = "student.transport_bus";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-users';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->level_class_id               = Session::get('level_class_id');

        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->user_id           = $obj_data->id;
        }
        $this->arr_view_data['module_icon']  =  $this->module_icon;
        $this->arr_view_data['create_icon']  =  $this->create_icon;
        $this->arr_view_data['edit_icon']    =  $this->edit_icon;
        $this->arr_view_data['theme_color']  =  $this->theme_color;
        $this->arr_view_data['page_title']   =  $this->module_title;
        $this->arr_view_data['module_url_path']   =  $this->module_url_path;
	}
    public function index(){
    	$arr_bus = $pick_students = $drop_students = [] ;
    	$obj_bus = $this->BusStudentsModel
    				->with('route_details','bus_details.driver_details','fees_details')
    				->whereHas('bus_details',function($q){
    					$q->where('academic_year_id',$this->academic_year);
    					$q->where('school_id',$this->school_id);
    				})
                    ->orderBy('type','ASC')
    				->where('student_id',$this->user_id)
    				->get();

     	if(isset($obj_bus) && !empty($obj_bus)){
            $arr_bus =  $obj_bus->toArray();
            foreach($arr_bus as $bus){
                
                    $transport_type = $bus['type'];
                    $bus_id = $bus['bus_id_fk'];

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
                                $obj_student_query->whereHas('get_bus_assigned_to_student',function($query)use($transport_type,$bus_id){
                                                            $query->whereHas('bus_details',function($q)use($transport_type,$bus_id){
                                                                $q->where('school_id',\Session::get('school_id'));
                                                                $q->where('id',$bus_id);
                                                                $q->where('type',$transport_type);
                                                            });
                                                    })
                                                    ->with('get_bus_assigned_to_student');
                    
                    $obj_student_list = $obj_student_query->get();    
                    
                    if($obj_student_list && count($obj_student_list)>0){
                        if($transport_type=="pickup"){
                            $pick_students = $obj_student_list ->toArray();
                        }
                        if($transport_type=="drop"){
                            $drop_students = $obj_student_list ->toArray();   
                        }
                    }
            }
        }


        $pick_route=[];
        $drop_route=[];
        foreach($pick_students as $pick){

            $temp_arr = [];
            $pickup_location = isset($pick['pickup_location'])?json_decode($pick['pickup_location'],true):[];

            $first_name = isset($pick['get_user_details']['first_name']) ? $pick['get_user_details']['first_name']:'';
            $last_name = isset($pick['get_user_details']['last_name']) ? $pick['get_user_details']['last_name']:'';
            $temp_arr[0] = $pick['pickup_address'];
            $temp_arr[1] = isset($pickup_location['latitude']) ? $pickup_location['latitude'] :'';
            $temp_arr[2] = isset($pickup_location['longitude']) ? $pickup_location['longitude'] :'';
            $temp_arr[3] = $first_name.' '.$last_name;
            array_push($pick_route,$temp_arr);
        }
        foreach($drop_students as $drop){
            $temp_arr = [];
            $drop_location = isset($drop['drop_location'])?json_decode($drop['drop_location'],true):[];
            $first_name = isset($drop['get_user_details']['first_name']) ? $drop['get_user_details']['first_name']:'';
            $last_name = isset($drop['get_user_details']['last_name']) ? $drop['get_user_details']['last_name']:'';
            $temp_arr[0] = $drop['drop_address'];
            $temp_arr[1] = isset($drop_location['latitude']) ? $drop_location['latitude'] :'';
            $temp_arr[2] = isset($drop_location['longitude']) ? $drop_location['longitude'] :'';
            $temp_arr[3] = $first_name.' '.$last_name;
            array_push($drop_route,$temp_arr);
        }
        $school_latitude     = $this->CommonDataService->get_school_latitude();
        $school_longitude    = $this->CommonDataService->get_school_longitude();
        $school_name    = $this->CommonDataService->get_school_name();
        $school_address = $this->CommonDataService->get_school_address();        

        $this->arr_view_data['arr_bus']          = $arr_bus;
        $this->arr_view_data['module_title']     = $this->module_title;
        $this->arr_view_data['school_latitude']  = $school_latitude;
        $this->arr_view_data['school_longitude'] = $school_longitude;
        $this->arr_view_data['school_name']      = $school_name;
        $this->arr_view_data['school_address']   = $school_address;
        $this->arr_view_data['pick_route']    = $pick_route;
        $this->arr_view_data['drop_route']    = $drop_route;

        $this->arr_view_data['arr_bus']    = $arr_bus;
        $this->arr_view_data['module_title'] = $this->module_title;
    	return view($this->module_view_folder.'.index', $this->arr_view_data);	
    }
}
