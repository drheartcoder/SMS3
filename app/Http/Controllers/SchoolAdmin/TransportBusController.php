<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;
use App\Models\EmployeeModel;
use App\Models\BusModel;
use App\Models\UserTranslationModel;
use App\Models\SchoolProfileTranslationModel;
use App\Models\SchoolProfileModel;
use App\Models\SchoolTemplateModel;
use App\Models\SchoolTemplateTranslationModel;
use App\Models\AcademicYearModel;
use App\Models\BusStudentsModel;
use App\Models\StudentModel;
use App\Models\BusFeesModel;
use App\Models\NotificationModel;


/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
/*Activity Log */
use App\Common\Services\EmailService;
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

class TransportBusController extends Controller
{
    use MultiActionTrait;
    public function __construct(CommonDataService $CommonDataService,EmailService $EmailService)
    {
        $this->EmployeeModel                   = new EmployeeModel();
        $this->BusModel                        = new BusModel();
        $this->BaseModel                       = $this->BusModel;
        $this->UserTranslationModel            = new UserTranslationModel();
        $this->SchoolProfileTranslationModel   = new SchoolProfileTranslationModel();
        $this->SchoolProfileModel              = new SchoolProfileModel();
        $this->SchoolTemplateModel             = new SchoolTemplateModel();
        $this->SchoolTemplateTranslationModel  = new SchoolTemplateTranslationModel();
        $this->AcademicYearModel               = new AcademicYearModel();
        $this->BusStudentsModel                = new BusStudentsModel();
        $this->StudentModel                    = new StudentModel();
        $this->BusFeesModel                    = new BusFeesModel();
        $this->NotificationModel               = new NotificationModel();
        $this->CommonDataService               = $CommonDataService;
        $this->EmailService                    = $EmailService;

        $this->arr_view_data                   = [];
        $this->module_url_path                 = url(config('app.project.school_admin_panel_slug')).'/transport_bus';
        
        $this->module_title                    = translation("transport_bus");
        $this->modyle_url_slug                 = translation("transport_bus");

        $this->module_view_folder              = "schooladmin.transport_bus";
        $this->theme_color                     = theme_color();
        $this->module_icon                     = 'fa fa-bus';
        $this->create_icon                     = 'fa fa-plus-circle';
        $this->edit_icon                       = 'fa fa-edit';
        $this->view_icon                       = 'fa fa-eye';

        $this->school_id                       = Session::get('school_id');
        $this->academic_year                   = Session::get('academic_year');

        $this->first_name 					   = $this->last_name =$this->ip_address ='';

        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['create_icon']    =  $this->create_icon;
        
        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
            $this->user_id           = $obj_data->id;
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
    | create() : load Transport Bus index page 
    | Auther   : Akshay
    | Date 	   : 01-06-2018
    */
    public function index(Request $request)
    {   
    	$page_title = translation("manage")." ".str_plural($this->module_title);
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
		return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*
    | get_details() : To get the list of transport buses 
    | Auther  : Akshay
    | Date    : 01-06-2018
    */
    public function get_details(Request $request){
        $str_years                = $this->CommonDataService->get_academic_year_less_than($this->academic_year);

        $prefixed_academic_year                       = DB::getTablePrefix().$this->AcademicYearModel->getTable();
        $prefixed_bus_details                         = DB::getTablePrefix().$this->BaseModel->getTable();
        $prefixed_user_details                        = DB::getTablePrefix().$this->UserTranslationModel->getTable();

        $obj_user = DB::table($prefixed_bus_details)
                                ->select(DB::raw($prefixed_bus_details.".id as id,".
                                                 $prefixed_bus_details.".school_id, ".
                                                 $prefixed_bus_details.".bus_no, ".
                                                 $prefixed_bus_details.".bus_plate_no, ".
                                                 $prefixed_bus_details.".driver_id,".
                                                 $prefixed_bus_details.".bus_capacity,".
                                                 $prefixed_academic_year.".academic_year,".
                                                 "CONCAT(".$prefixed_user_details.".first_name,' ',"
                                                          .$prefixed_user_details.".last_name) as bus_driver_name"
                                             ))
                                ->whereNull($prefixed_bus_details.'.deleted_at')
                                ->join($prefixed_academic_year,$prefixed_bus_details.'.academic_year_id','=',$prefixed_academic_year.".id")
                                ->join($prefixed_user_details,$prefixed_bus_details.'.driver_id','=',$prefixed_user_details.".user_id")
                                ->where($prefixed_bus_details.'.school_id','=',$this->school_id)
                                ->where($prefixed_user_details.'.locale','=',$this->locale)
                                ->whereRaw(" academic_year_id in (".$str_years.")")
                                ->orderBy($prefixed_bus_details.'.id','DESC');

            $search = $request->input('search');
            $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
            $obj_user = $obj_user->WhereRaw("((".$prefixed_bus_details.".bus_no LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$prefixed_bus_details.".bus_plate_no LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$prefixed_bus_details.".bus_capacity LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(CONCAT(".$prefixed_user_details.".first_name,' ',".$prefixed_user_details.".last_name) LIKE '%".$search_term."%')) ");
        }
        return $obj_user;
    }

    /*
    | get_records() : To get the list of transport buses 
    | Auther  : Akshay
    | Date    : 01-06-2018
    */
    public function get_records(Request $request){
        $arr_current_user_access =[];
    
        $role = Session::get('role');
        
        $arr_current_user_access = $this->CommonDataService->current_user_access();
        $obj_user        = $this->get_details($request);

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        $json_result     = $json_result
                            ->editColumn('build_add_student',function($data) {

                               return '<a  href="'.$this->module_url_path.'/add_student/'.base64_encode($data->id).'" class="light-blue-color" style="color:white">&nbsp;'.translation('add').' '.translation('student').'&nbsp;</a>'; 
                            })
                            ->editColumn('build_action_btn',function($data) use ($arr_current_user_access){           
                            $build_delete_action =  $build_edit_action = $build_view_action =  ''; 
                            if(array_key_exists('transport_bus.list', $arr_current_user_access)){
                                $view_href         =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>';
                            }

                            if(array_key_exists('transport_bus.update', $arr_current_user_access)){
                                $edit_href         =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';
                            }

                            if(array_key_exists('transport_bus.delete', $arr_current_user_access)){ 
                            $delete_href          =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                            $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
	                        }
                            return $build_view_action.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_delete_action;
                                 
                            });
        
        $json_result =      $json_result->editColumn('build_checkbox',function($data)use($arr_current_user_access){
                                $build_checkbox = '';

                                if(array_key_exists('transport_bus.update', $arr_current_user_access) || array_key_exists('transport_bus.delete', $arr_current_user_access))
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
    | Date    : 01-06-2018
    */
    public function create(){
        $arr_data  = [];
        $str_years = $this->CommonDataService->get_academic_year_less_than($this->academic_year);
        
        $obj_data = $this->CommonDataService->get_employees(); 

        $this->arr_view_data['page_title']      = translation('add')." ".$this->module_title;
        $this->arr_view_data['arr_data']        = $obj_data;
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
    | Date    : 01-06-2018
    */
    public function store(Request $request){
        $messages = $arr_rules = [];
        $str_years = $this->CommonDataService->get_academic_year_less_than($this->academic_year);

        $arr_rules['bus_driver_id']    = 'required';
        $arr_rules['bus_plate_number'] = 'required';
        $arr_rules['bus_capacity']     = 'required|numeric';
        $arr_rules['bus_number']       = 'required';
        $arr_rules['bus_type']         = 'required';
        
        $messages['required']      =  translation('this_field_is_required');
        $messages['numeric']       = translation('please_enter_a_valid_number');
        $validator                 = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $bus_driver_id    =   trim($request->input('bus_driver_id'));
        $bus_plate_number =   trim($request->input('bus_plate_number'));
        $bus_capacity     =   trim($request->input('bus_capacity'));
        $bus_number       =   trim($request->input('bus_number'));
        $bus_type         =   trim($request->input('bus_type'));
      
        $arr_data = [];     
        $arr_data['school_id']        = $this->school_id;
        $arr_data['driver_id']        = $bus_driver_id;
        $arr_data['bus_plate_no']     = $bus_plate_number;
        $arr_data['bus_capacity']     = $bus_capacity;
        $arr_data['bus_no']           = $bus_number;
        $arr_data['bus_type']         = $bus_type;
        $arr_data['academic_year_id'] = $this->academic_year;
        
        $obj_exist =   $this->BusModel->where('school_id','=',$this->school_id)->where('academic_year_id','=',$this->academic_year)->where('bus_plate_no','=',$bus_plate_number)->first();
        if(isset($obj_exist->id))
        {
            Flash::error($this->module_title." ".translation("already_exists"));
            return redirect()->back();            
        }

        $obj_bus_no_exist =   $this->BusModel->where('school_id','=',$this->school_id)->where('academic_year_id','=',$this->academic_year)->where('bus_no','=',$bus_number)->first();
        if(isset($obj_bus_no_exist->id))
        {
            Flash::error(translation('bus_number').' '.translation("already_exists"));
            return redirect()->back();            
        }

        $res = $this->BusModel->create($arr_data);
        if($res){
            Flash::success($this->module_title." ".translation("created_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_creating ".$this->module_title);
            return redirect()->back();
        }        

    }

     /*
    | edit()  : Edit  Transport Bus
    | Auther  : Akshay
    | Date    : 01-06-2018
    */
   
    public function edit($enc_id=FALSE)
    {
        $id = base64_decode($enc_id);
        $arr_data = $arr_driver = [];

        $obj_driver = $this->CommonDataService->get_employees(); 

        $obj_data = $this->BusModel->where('id',$id)->first();
        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }

        $this->arr_view_data['page_title']      = translation('edit')." ".$this->module_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['enc_id']          = $enc_id;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['edit_icon']       = $this->edit_icon;
        $this->arr_view_data['arr_driver']      = $obj_driver;
        $this->arr_view_data['arr_data']        = $arr_data;
        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }

    /*
    | edit()  : Update  Transport Bus
    | Auther  : Akshay
    | Date    : 01-06-2018
    */
    public function update(Request $request,$enc_id=FALSE)
    {
        $id = base64_decode($enc_id);     
        
        $arr_rules = $messages = [];

        $arr_rules['bus_driver_id']    = 'required';
        $arr_rules['bus_plate_number'] = 'required';
        $arr_rules['bus_capacity']     = 'required|numeric';
        $arr_rules['bus_number']       = 'required';
        $arr_rules['bus_type']         = 'required';
        
        $messages['required']      =  translation('this_field_is_required');
        $messages['numeric']       = translation('please_enter_a_valid_number');
        $validator                 = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $bus_driver_id    =   trim($request->input('bus_driver_id'));
        $bus_plate_number =   trim($request->input('bus_plate_number'));
        $bus_capacity     =   trim($request->input('bus_capacity'));
        $bus_number       =   trim($request->input('bus_number'));
        $bus_type         =   trim($request->input('bus_type'));
      
        $arr_data = [];     
        $arr_data['school_id']        = $this->school_id;
        $arr_data['driver_id']        = $bus_driver_id;
        $arr_data['bus_plate_no']     = $bus_plate_number;
        $arr_data['bus_capacity']     = $bus_capacity;
        $arr_data['bus_no']           = $bus_number;
        $arr_data['bus_type']         = $bus_type;
        $arr_data['academic_year_id'] = $this->academic_year;

        $obj_exist =   $this->BusModel->where('school_id','=',$this->school_id)->where('bus_plate_no','=',$bus_plate_number)->where('id','<>',$id)->first();
        
        if(isset($obj_exist->id))
        {
            Flash::error($this->module_title." ".translation("already_exists"));
            return redirect()->back();            
        }

        $obj_bus_no_exist =   $this->BusModel->where('school_id','=',$this->school_id)->where('bus_no','=',$bus_number)->where('id','<>',$id)->first();
        
        if(isset($obj_bus_no_exist->id))
        {
            Flash::error(translation('bus_number').' '.translation("already_exists"));
            return redirect()->back();            
        }
        
        $res = $this->BusModel->where('id',$id)->update($arr_data);
        if($res){
            Flash::success($this->module_title." ".translation("updated_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_updating ".$this->module_title);
            return redirect()->back();
        }        
       
    }

    /*
    | view()  : View  Transport Bus
    | Auther  : Akshay
    | Date    : 02-06-2018
    */    
    public function view($enc_id=FALSE)
    {
        $id = base64_decode($enc_id);
        $arr_data = $arr_driver = [];

        $obj_data = $this->BusModel->where('id',$id)
                                   ->with(['driver_details' => function ($query) {
                                            $query->select('id')->orderBy('id','ASC');
                                        }])
                                   ->first();
        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }
        $arr_students=[];
        $obj_students = $this->BusStudentsModel 
                                ->where('bus_id_fk',$id)
                                ->whereHas('student_details.get_user_details',function(){})
                                ->whereHas('student_details.get_level_class.level_details',function(){})
                                ->with('student_details.get_user_details','student_details.get_level_class.level_details')
                                ->get();
    
        if($obj_students && count($obj_students)>0){
            $arr_students = $obj_students->toArray(); 
        }        
        $this->arr_view_data['page_title']      = translation('view')." ".$this->module_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['view_mode']       = TRUE;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['enc_id']          = $enc_id;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['view_icon']       = $this->view_icon;
        $this->arr_view_data['arr_driver']      = $arr_driver;
        $this->arr_view_data['arr_students']    = $arr_students;
        $this->arr_view_data['arr_data']        = $arr_data;
        return view($this->module_view_folder.'.view',$this->arr_view_data);
    }

    public function export(Request $request)
    {   

        $arr_data = $data = [];
        $obj_data = $this->BusModel->with(['driver_details' => function ($query) {
                                            $query->select('id')->orderBy('id','ASC');
                                        }])
                                    ->with('get_bus_transports')
                                    ->where('school_id',$this->school_id)
                                    ->where('academic_year_id',$this->academic_year)
                                    ->get();
        
        if(sizeof($obj_data)<=0){
            Flash::error(translation("no_records_found_to_export"));
            return redirect()->back();
        }
        if(sizeof($obj_data)>500 && $request->file_format == 'csv'){
            Flash::error(translation("too_many_records_to_export"));
            return redirect()->back();
        }

        $arr_data = $obj_data->toArray();

        if($request->file_format=='pdf'){
            $school_name = $this->CommonDataService->get_school_name();

            $school_address = $this->CommonDataService->get_school_address();

            $school_email = $this->CommonDataService->get_school_email();

            $school_logo = $this->CommonDataService->get_school_logo();

            $this->arr_view_data['arr_data']      = $obj_data;
            $this->arr_view_data['school_name']   = $school_name;    
            $this->arr_view_data['school_address']= $school_address;
            $this->arr_view_data['school_email']  = $school_email;
            $this->arr_view_data['school_logo']   = $school_logo;

            $data['arr_data'] = $arr_data;        
            $pdf = PDF::loadView($this->module_view_folder.'.pdf',$this->arr_view_data);
            return $pdf->download(str_plural($this->module_title).'.pdf');    
        }
        else{
            \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel) use($arr_data) 
            {
                $excel->sheet(ucwords($this->module_title), function($sheet) use($arr_data) 
                {
                    $arr_fields['id']                  = 'Sr.No';
                    $arr_fields['bus_number']          = translation('bus_number');
                    $arr_fields['bus_plate_number']    = translation('bus_plate_number');
                    $arr_fields['bus_capacity']        = translation('bus_capacity');
                    $arr_fields['bus_driver_name']     = translation('bus_driver_name');
                    $arr_fields['pickup_fees']         = translation('pickup_fees');
                    $arr_fields['drop_fees']           = translation('drop_fees');

                    
                    $sheet->row(2, ['',ucwords($this->module_title).' - '.date('c'),'','','']);
                    $sheet->row(4, $arr_fields);
                    if(sizeof($arr_data)>0)
                    {
                        $arr_tmp = [];
                        foreach($arr_data as $key => $result)
                        {
                            $status = "";
                            $arr_tmp[$key]['id']                  = intval($key+1);
                            $arr_tmp[$key]['bus_number']          = $result['bus_no'];
                            $arr_tmp[$key]['bus_plate_number']    = $result['bus_plate_no'];
                            $arr_tmp[$key]['bus_capacity']        = $result['bus_capacity'];
                            $arr_tmp[$key]['bus_driver_name']     = $result['driver_details']['first_name']." ".$result['driver_details']['last_name'];

                            $pickup_fees=0;
                            $drop_fees=0;
                            if(isset($result['get_bus_transports']) &&$result['get_bus_transports']!=null && count($result['get_bus_transports'])>0) {
                                foreach($result['get_bus_transports'] as $value){
                                    
                                    if($value['transport_type']=="pickup") {
                                        $pickup_fees =  $value['fees'];
                                    }
                                    if($value['transport_type']=="drop"){
                                        $drop_fees = $value['fees'];
                                    }
                                }
                            } 
                            $arr_tmp[$key]['pickup_fees'] = $pickup_fees;
                            $arr_tmp[$key]['drop_fees']   = $drop_fees;
                           $sheet->rows($arr_tmp);
                        }
                    }    

                });
            })->export('csv'); 
        }
        
    }

    /*
    | add_student()  : add student to Transport Bus
    | Auther        : Pooja
    | Date          : 13-08-2018
    */
    public function add_student($enc_id,Request $request){

        $id = base64_decode($enc_id);
        if(is_numeric($id)){
            if(Session::has('bus_id')){
                Session::put('bus_id',$id);    
            }
            else{
             Session::set('bus_id',$id);       
            }
            if(!Session::has('transport_type')){
                Session::set('transport_type',"pickup");    
            }
            /*else{
             Session::put('transport_type',"pickup");
            }*/
            $temp_arr = [];
            $obj_bus = $this->BaseModel->where('id',$id)->first();    
            if($obj_bus){
                $arr_students=[];
                $obj_students = $this->StudentModel
                            ->whereHas('get_user_details',function(){})
                            ->with('get_student_assigned_bus_stop_details')
                            ->with('get_user_details','get_level_class.level_details')    
                            ->where('school_id',$this->school_id)
                            ->where('academic_year_id',$this->academic_year)
                            ->where('has_left',0)
                            ->where('is_active',1)
                            ->where('bus_transport',1)
                            ->get();

                if($obj_students && count($obj_students)>0){
                    $arr_students = $obj_students->toArray();
                    foreach($arr_students as $student){

                        if($student['get_student_assigned_bus_stop_details']!=null){
                            if( ($student['get_student_assigned_bus_stop_details']['type']==\Session::get("transport_type") && $student['get_student_assigned_bus_stop_details']['bus_id_fk']==\Session::get("bus_id")) ||
                                $student['get_student_assigned_bus_stop_details']['type']!=\Session::get("transport_type")
                             )
                            {
                                array_push($temp_arr,$student);
                            }
                            
                        }
                        else{
                            array_push($temp_arr,$student);
                        }
                        
                    } 
                }
                $student_id=[]; 
                $bus_students = $this->BusStudentsModel->where('bus_id_fk',$id)->where('type',Session::get('transport_type'))->get();
                
                if($bus_students && count($bus_students)>0)
                {   
                    foreach($bus_students as $student){
                        array_push($student_id,$student->student_id);
                    }
                }
                $school_latitude = $this->CommonDataService->get_school_latitude();
                $school_longitude = $this->CommonDataService->get_school_longitude();

                $obj_bus_fees = $this->BusFeesModel
                        ->where('bus_id',Session::get('bus_id'))
                        ->where('transport_type',Session::get('transport_type'))
                        ->where('academic_year_id',$this->academic_year)
                        ->first();
                if($request->has('route_name')){

                    $this->BusFeesModel->where('bus_id',$id)->where('transport_type',\Session::get('transport_type'))->update(['route_name'=>$request->route_name]);
                }        
                $this->arr_view_data['module_icon']      = $this->module_icon;
                $this->arr_view_data['arr_students']     = $temp_arr;
                $this->arr_view_data['obj_bus']          = $obj_bus;
                $this->arr_view_data['student_id']       = $student_id;
                $this->arr_view_data['school_latitude']  = $school_latitude;
                $this->arr_view_data['school_longitude'] = $school_longitude;
                $this->arr_view_data['fees']             = isset($obj_bus_fees->fees)?$obj_bus_fees->fees:'';
                $this->arr_view_data['page_title']       = translation('add').' '.translation('student');

                return view($this->module_view_folder.'.add-student', $this->arr_view_data);
            }
        }
        Flash::error(translation('something_went_wrong'));
        return redirect($this->module_url_path);
    }
    /*
    | store_student()  : store student to Transport Bus
    | Auther        : Pooja
    | Date          : 14-08-2018
    */
    public function store_student(Request $request,$enc_id=FALSE){
        
        $id = '';
        if($enc_id)
        {
            $id = base64_decode($enc_id);
        }
        $type = $request->transport_type;
       
        $arr_students=$student_id= $students= [];
        $arr_students = $request->checked_record;

        if(count($arr_students)==0){
            $this->BusStudentsModel->where('type',Session::get('transport_type'))->where('bus_id_fk',$id)->delete();
            return redirect(url($this->module_url_path));
        }

        $records = $this->BusStudentsModel->where('type',Session::get('transport_type'))->where('bus_id_fk',$id)->get();
        if($records && count($records)>0){
            foreach($records as $value){
                if(!in_array($value->student_id,$arr_students)){
                    $this->BusStudentsModel->where('id',$value->id)->delete();
                }
            }
        }

        $bus_fees =[];
        $bus_fees['bus_id'] = $id;
        $bus_fees['transport_type'] = $type;
        $bus_fees['fees'] = $request->fees;
        $bus_fees['academic_year_id'] = $this->academic_year;

        $bus_fees_id = $this->BusFeesModel->updateOrCreate(['bus_id'=>$id,'transport_type'=>$request->transport_type,'academic_year_id'=>$this->academic_year],$bus_fees);

        if($request->transport_type=="pickup" ||$request->transport_type=="drop" )
        {
            foreach($arr_students as $key => $student){
                $arr_data                   = [];
                $arr_data['bus_id_fk']      = $id;
                $arr = explode('_',$student);
                $students[$key]             = $arr[0];
                $arr_data['student_id']     = $arr[0];
                $arr_data['pickup_distance']= $arr[1];
                $arr_data['drop_distance']  = $arr[2];
                $arr_data['type']           = $request->transport_type;
                $arr_data['bus_fees_id']  = isset($bus_fees_id->id)?$bus_fees_id->id:0;
                $arr_data['academic_year_id']  = $this->academic_year;
                $this->BusStudentsModel->updateOrCreate(["bus_id_fk"=>$id,"student_id"=>$student,"type"=>$type],$arr_data);
            }
             $bus_details  = $this->BusModel->where('id',$id)->first();
            $students_data = $this->StudentModel->with('notifications','parent_notifications','get_user_details','get_parent_details','get_level_class.level_details','get_level_class.class_details')->where('school_id',$this->school_id)->where('academic_year_id',$this->academic_year)->where('is_active',1)->where('has_left',0)->whereIn('user_id',$students)->get();
            if(isset($students_data) && $students_data!=null && count($students_data)>0)
            {
                $arr_student_data = $students_data->toArray();
                if(isset($arr_student_data) && count($arr_student_data)>0)
                {
                    foreach ($arr_student_data as $key => $value) {

                        $result = $this->send_notifications($value,$bus_details);
                    }
                }
            }
            Flash::success(translation('students_added_in_bus_successfully')); 
            return redirect($this->module_url_path.'/view_map');   
        }
        else{
            Flash::success(translation('something_went_wrong'));    
        }
        
        return redirect(url($this->module_url_path));
    }

    public function get_student(Request $request){

        $type = $request->transport_type;
        Session::put('transport_type',$type);
        $data=[];
        $str_table = '';
        $arr_students=$temp_arr=[];
                $obj_students = $this->StudentModel
                            ->whereHas('get_user_details',function(){})
                            ->with('get_student_assigned_bus_stop_details')
                            ->with('get_user_details','get_level_class.level_details')    
                            ->where('school_id',$this->school_id)
                            ->where('academic_year_id',$this->academic_year)
                            ->where('has_left',0)
                            ->where('is_active',1)
                            ->where('bus_transport',1)
                            ->get();
                    
                if($obj_students && count($obj_students)>0){
                    $arr_students = $obj_students->toArray();
                    foreach($arr_students as $student){

                        if($student['get_student_assigned_bus_stop_details']!=null){
                            if( ($student['get_student_assigned_bus_stop_details']['type']==\Session::get("transport_type") && $student['get_student_assigned_bus_stop_details']['bus_id_fk']==\Session::get("bus_id")) ||
                                $student['get_student_assigned_bus_stop_details']['type']!=\Session::get("transport_type")
                             )
                            {
                                array_push($temp_arr,$student);
                            }
                            
                        }
                        else{
                            array_push($temp_arr,$student);
                        }
                        
                    } 
                }
                   
        $student_id=[]; 
        $bus_students = $this->BusStudentsModel->where('type',Session::get('transport_type'))->where('bus_id_fk',Session::get('bus_id'))->get();
        if($bus_students && count($bus_students)>0)
        {   
            foreach($bus_students as $student){
                array_push($student_id,$student->student_id);
            }
        }            

        if($temp_arr && count($temp_arr)>0){
            
            $school_latitude = $this->CommonDataService->get_school_latitude();
            $school_longitude = $this->CommonDataService->get_school_longitude();           
            foreach($temp_arr as $student){

                $pickup_address  = isset($student['pickup_address']) ? $student['pickup_address'] : "";
                $drop_address    = isset($student['drop_address']) ? $student['drop_address'] : "";
                $level           = isset($student['get_level_class']['level_details']['level_name'])?$student['get_level_class']['level_details']['level_name'] : "";
                $pickup_location = isset($student['pickup_location']) ? $student['pickup_location'] : "";
                $pickup_latitude = $pickup_longitude = $drop_latitude = $drop_longitude="";
                
                if($pickup_location!=''){
                  $pickup_location  = json_decode($pickup_location,'true');
                  $pickup_latitude  = isset($pickup_location['latitude']) ? $pickup_location['latitude'] :'';
                  $pickup_longitude = isset($pickup_location['longitude']) ? $pickup_location['longitude'] :'';
                }

                $drop_location = isset($student['drop_location']) ? $student['drop_location'] : "";

                if($drop_location!=''){
                  $drop_location  = json_decode($drop_location,'true');
                  $drop_latitude  = isset($drop_location['latitude']) ? $drop_location['latitude'] :'';
                  $drop_longitude = isset($drop_location['longitude']) ? $drop_location['longitude'] :'';
                }
                
                $pickup_distance=0;
                $drop_distance=0;
                if($school_latitude!='' && $school_longitude!='')
                {
                  if($pickup_latitude!="" && $pickup_longitude!=""){
                    $theta           = $pickup_longitude - $school_longitude;
                    $dist            = sin(deg2rad($pickup_latitude)) * sin(deg2rad($school_latitude)) +  cos(deg2rad($pickup_latitude)) * cos(deg2rad($school_latitude)) * cos(deg2rad($theta));
                    $dist            = acos($dist);
                    $dist            = rad2deg($dist);
                    $miles           = $dist * 60 * 1.1515;
                    $pickup_distance = $miles * 1.609344;
                  }

                  if($drop_latitude!="" && $drop_longitude!=""){
                    $theta         = $drop_longitude - $school_longitude;
                    $dist          = sin(deg2rad($drop_latitude)) * sin(deg2rad($school_latitude)) +  cos(deg2rad($drop_latitude)) * cos(deg2rad($school_latitude)) * cos(deg2rad($theta));
                    $dist          = acos($dist);
                    $dist          = rad2deg($dist);
                    $miles         = $dist * 60 * 1.1515;
                    $drop_distance = $miles * 1.609344;
                  }
                   
                }

                $first_column = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.$student['user_id'].'" value="'.$student['user_id'].'_'.round($pickup_distance,2).'_'.round($drop_distance,2).'"';
                if(in_array($student['user_id'],$student_id)){
                    $first_column .= ' checked';
                }
                $first_column .= ' onclick="checkCount()"/><label for="mult_change_'.$student['user_id'].'"></label></div>';

                $first_name = isset($student['get_user_details']['first_name']) ? ucfirst($student['get_user_details']['first_name']) : "";
                $last_name = isset($student['get_user_details']['last_name']) ? ucfirst($student['get_user_details']['last_name']) : "";

                $second_column = $first_name.' '.$last_name;

                $third_column   = $level;
                $fourth_column  = $pickup_address;
                $fifth_column   = $drop_address;
                $sixth_column   = round($pickup_distance,2);
                $seventh_column = round($drop_distance,2);

                $str_table .= '<tr><td>'.$first_column.'</td><td>'.$second_column.'</td><td>'.$third_column.'</td>'.
                              '<td>'.$fourth_column.'</td><td>'.$fifth_column.'</td><td>'.$sixth_column.'</td>'.'</td><td>'.$seventh_column.'</td></tr>';
            }                       
        }
        $obj_bus_fees = $this->BusFeesModel
                        ->where('bus_id',Session::get('bus_id'))
                        ->where('transport_type',Session::get('transport_type'))
                        ->where('academic_year_id',$this->academic_year)
                        ->first();

        $data['table']=$str_table;
        $data['route_name']= isset($obj_bus_fees->route_name) ? $obj_bus_fees->route_name: '';
        $data['fees']=isset($obj_bus_fees->fees) ? $obj_bus_fees->fees: '';
        return $data;
    }  

    public function view_map(){

        if(!Session::has('bus_id') || !Session::has('transport_type')){
            return redirect($this->module_url_path);
        }
        $arr_data  = [];
        
        $school_lat     = $this->CommonDataService->get_school_latitude();
        $school_lang    = $this->CommonDataService->get_school_longitude();
        $school_name    = $this->CommonDataService->get_school_name();
        $school_address = $this->CommonDataService->get_school_address();        

        $bus = $this->BusModel
                            ->where('id',Session::get('bus_id'))
                            ->whereHas('get_fees_details',function($q){
                                $q->where('transport_type',Session::get('transport_type'));
                            })
                            ->with(['get_fees_details'=>function($q){
                                $q->where('transport_type',Session::get('transport_type'));
                            }])
                            ->first();
        if($bus){
            $arr_data = $bus->toArray();
        }

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
        return view($this->module_view_folder.'.view_map',$this->arr_view_data);
    }  
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
                        $obj_student_query->whereHas('get_bus_assigned_to_student',function($query){
                                                    $query->whereHas('bus_details',function($q){
                                                        $q->where('school_id',\Session::get('school_id'));
                                                        $q->where('id',\Session::get('bus_id'));
                                                        $q->where('type',\Session::get('transport_type'));
                                                    });
                                            })
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

    public function send_notifications($data,$details)
    {
        $result = '';
        $permissions = $parent_permissions = [];
        if(isset($data['notifications']['notification_permission']) && $data['notifications']['notification_permission']!=null) 
        {
            
            $permissions        = json_decode($data['notifications']['notification_permission'],true);
        }
        if(isset($data['parent_notifications']['notification_permission']) && $data['parent_notifications']['notification_permission']!=null)
        {
            
            $parent_permissions = json_decode($data['parent_notifications']['notification_permission'],true);
        }

        $level      = isset($data['get_level_class']['level_details']['level_name'])?$data['get_level_class']['level_details']['level_name']:'';
        $class      = isset($data['get_level_class']['class_details']['class_name'])?$data['get_level_class']['class_details']['class_name']:'';
        $bus_no     = isset($details->bus_no)?$details->bus_no:'';
        $kid_name   = ucwords((isset($data['get_user_details']['first_name'])?$data['get_user_details']['first_name']:'').' '.(isset($data['get_user_details']['last_name'])?$data['get_user_details']['last_name']:''));

        if (isset($permissions) &&  count($permissions)>0)
        {
            $result = $this->build_notification($permissions,$data['get_user_details'],'student',$level,$class,$bus_no,$kid_name);
        }
        if (isset($parent_permissions) &&  count($parent_permissions)>0)
        {
            
            $result = $this->build_notification($parent_permissions,$data['get_parent_details'],'parent',$level,$class,$bus_no,$kid_name);
        }
        return $result;
    }

    public function build_notification($permissions,$user,$user_type,$level,$class,$bus,$kid_name)
    {
        $result = '';
        if(array_key_exists('bus_transport.app',$permissions))
        {
            $arr_notification = [];
            $arr_notification['school_id']          =  $this->school_id;
            $arr_notification['from_user_id']       =  $this->user_id;
            $arr_notification['to_user_id']         =  $user['id'];
            $arr_notification['user_type']          =  config('app.project.role_slug.school_admin_role_slug');
            $arr_notification['notification_type']  =  'Bus assigned to student';
            $arr_notification['title']              =  'Bus sssigned to student: Bus '.$bus.' assigned to '.$kid_name.' of class '.$level.' '.$class;
            $arr_notification['view_url']           =  url('/').'/'.$user_type.'/transport_bus';
            $result = $this->NotificationModel->create($arr_notification);
        }
        $details          = [
                                    'first_name'  =>  isset($user['first_name'])?ucwords($user['first_name']):'',
                                    'level'       =>  isset($level)?$level:'',
                                    'class'       =>  isset($class)?$class:'',
                                    'email'       =>  isset($user['email'])?$user['email']:'',
                                    'kid_name'    =>  ($kid_name!='')?$kid_name:'',
                                    'mobile_no'   =>  isset($user['mobile_no'])?$user['mobile_no']:'',
                                    'bus_no'      =>  $bus
                            ];
        if(array_key_exists('bus_transport.sms',$permissions))
        {
            $arr_sms_data = $this->built_sms_data($details,$user_type);
            $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
        }
        if(array_key_exists('bus_transport.email',$permissions))
        {
            $arr_mail_data = $this->built_mail_data($details,$user_type); 
            $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
        }
        return $result;
    }
    public function built_mail_data($arr_data,$type)
    {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'     => $arr_data['first_name'],
                                  'KID_NAME'       => $arr_data['kid_name'],
                                  'BUS_NO'         => $arr_data['bus_no'],
                                  'LEVEL'          => $arr_data['level'],
                                  'SCHOOL_ADMIN'   => $this->CommonDataService->get_school_name($this->school_id),
                                  'CLASS'          => $arr_data['class']];
    
            $arr_mail_data                        = [];
            if($type=='parent')
            {
                $arr_mail_data['email_template_slug'] = 'bus_transport_to_parent';                
            }
            else
            {
                $arr_mail_data['email_template_slug'] = 'bus_transport_to_student';                
            }
            
            $arr_mail_data['arr_built_content']   = $arr_built_content;
            $arr_mail_data['user']                = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_sms_data($arr_data,$type)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'KID_NAME'       => $arr_data['kid_name'],
                                  'BUS_NO'         => $arr_data['bus_no'],
                                  'LEVEL'          => $arr_data['level'],
                                  'CLASS'          => $arr_data['class']];

            $arr_sms_data                      = [];
            if($type=='parent')
            {
                $arr_sms_data['sms_template_slug'] = 'bus_transport_to_parent';                
            }
            else
            {
                $arr_sms_data['sms_template_slug'] = 'bus_transport_to_student';                
            }
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }
}