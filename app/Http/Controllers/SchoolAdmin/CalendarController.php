<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use PDF;


use App\Common\Services\CommonDataService;
use App\Models\CalendarModel;
use App\Models\LevelClassModel;


class CalendarController extends Controller
{
	public function __construct(    
                                    CalendarModel $calendar_model,
                                    CommonDataService $common_data_service,
                                    LevelClassModel $level_model

                                )
    {
        
        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/calendar';
        $this->BaseModel                    = $calendar_model;
        $this->module_title                 = translation("calendar");
        $this->modyle_url_slug              = translation("calendar");
        $this->CalendarModel                = $calendar_model;
        $this->LevelClassModel              = $level_model;
        $this->module_view_folder           = "schooladmin.calendar";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-calendar';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->CommonDataService            = $common_data_service;
        
        $this->first_name = $this->last_name =$this->ip_address ='';

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
        }
        /* Activity Section */}   
    /*
    | index() : load calendar
    | Auther : pooja k
    | Date : 09-05-2018
    */ 
    public function index(Request $request)
    {   
        $arr_level = [];
        $academic_year_id = $this->academic_year;

        $obj_level  =  $this->CommonDataService->get_levels($academic_year_id);  

        if(!empty($obj_level))
        {
            $arr_level = $obj_level->toArray();
        }    

        $page_title = translation("calendar");

        $obj_events = $this->CalendarModel
                                        ->where('school_id',$this->school_id)
                                        ->where('academic_year_id',$this->academic_year)
                                        ->get();
        $arr_events = [];
        $arr_holidays = [];  

        if($obj_events)
        {
            $arr = $obj_events->toArray();
            $arr_data = [];
            
            foreach($arr as $value)
            {
                $temp_arr=[];
                if($value['event_type']=='HOLIDAY')
                {
                    $temp_arr['event_title'] =$value['event_title'];
                    $temp_arr['event_date'] =$value['event_date_from'];

                    array_push($arr_holidays,$temp_arr);    
                }
                if($value['event_type']=='EVENT')
                {
                    $temp_arr['event_title'] =$value['event_title'];
                    $temp_arr['event_date'] =$value['event_date_from'];

                    array_push($arr_events,$temp_arr);
                }
            }
        }

        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = $page_title;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['arr_levels']      = $arr_level;
        $this->arr_view_data['arr_events']      = $arr_events;
        $this->arr_view_data['arr_holidays']    = $arr_holidays;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }
    /*
    | store() : store event
    | Auther : pooja k
    | Date : 16-05-2018
    */ 
    public function store(Request $request)
    {
        
        $arr_data = [];
        
        $current_academic_year = $this->CommonDataService->get_current_academic_id();
        $start_date = $request->input('start');
        $start_date = date_create($start_date);

        $end_date = $request->input('end');
        $end_date = date_create($end_date);

        $arr_data['school_id'] = $this->school_id;
        $arr_data['event_title'] = $request->has('title') ? trim($request->input('title')) : '';
        $arr_users = $request->has('user_type') ? $request->input('user_type'): '';
        if($arr_users!='')
        {
            $arr_users =  implode(',',$arr_users); 
        }
        
        $arr_data['school_id'] = $this->school_id;
        $arr_data['user_type'] = $arr_users;
        if($request->individual=='red')
        {
            $arr_data['level_class_id'] = $request->input('class');    
            $arr_data['is_individual'] = 1;    
        }
        else
        {
            $arr_data['is_individual'] = 0;    
        }
        $arr_data['event_date_from'] = $request->input('start');
        $arr_data['event_date_to'] = $request->input('end');
        $arr_data['all_day'] = $request->input('all_day')=='true' ? 1 :0 ;
        $arr_data['event_type'] = $request->input('event_type');
        $arr_data['event_description'] = $request->input('description');
        $arr_data['academic_year_id'] = $this->academic_year;

        $this->CalendarModel->create($arr_data);

        return $arr_data;
    }

    /*
    | delete() : delete event
    | Auther : pooja k
    | Date : 16-05-2018
    */ 
    public function delete(Request $request)
    {
        $id = $request->input('id');
        $this->CalendarModel->where('id',$id)->delete();
    }    

    /*
    | get_events() : get event list
    | Auther : pooja k
    | Date : 17-05-2018
    */
    public function get_events()
    {
        $events = '';
        $obj_events = $this->CalendarModel
                                        ->with('get_level_class')
                                        ->where('school_id',$this->school_id)
                                        ->where('academic_year_id',$this->academic_year)
                                        ->get();
                                       
        if($obj_events)
        {
            $arr_events = $obj_events->toArray();
            $arr_data = [];

            foreach($arr_events as $value)
            {
                $temp_arr = [];
                $temp_arr['title']             = $value['event_title'];
                $temp_arr['start']             = $value['event_date_from'];

                $temp_arr['end']               = $value['event_date_to'];
                $temp_arr['type']              = $value['event_type'];
                $temp_arr['allDay']            = $value['all_day']=='1' ? true :false;
                $temp_arr['id']                = $value['id'];
                $user_type                     = $value['user_type'];
                $temp_arr['is_individual']     = $value['is_individual'];
                $temp_arr['user_type']         = explode(',',$user_type);
                $temp_arr['level']             = isset($value['get_level_class']['level_id']) ? $value['get_level_class']['level_id'] : '0';
                $temp_arr['class']             = isset($value['level_class_id']) ? $value['level_class_id'] : '0';
                $temp_arr['event_description'] = $value['event_description'];

                if($temp_arr['type']=="EXAM")
                {
                    $temp_arr['editable'] = false;   
                    $temp_arr['backgroundColor'] = "#2bc3ac";

                     
                }
                elseif($temp_arr['type']=="HOLIDAY") 
                {
                    $temp_arr['editable'] = true;
                    $temp_arr['backgroundColor'] = "#fab63f";
                }
                else
                {
                    $temp_arr['editable'] = true;   
                    $temp_arr['backgroundColor'] = "#8cbf26";
                }
                
                array_push($arr_data,$temp_arr);
            }
            $events = json_encode($arr_data);
        }                               
        return $events;

    }

    /*
    | update() : update event
    | Auther : pooja k
    | Date : 18-05-2018
    */
    public function update(Request $request)
    { 

        $id                            = $request->input('id');
        $arr_data['event_title']       = $request->has('title') ? trim($request->input('title')) : '';
        $arr_data['event_type']        = $request->input('event_type');
        $arr_data['event_description'] = $request->input('description');
        $arr_data['is_individual']     = $request->input('individual');
        $arr_users                     = $request->has('user_type') ? $request->input('user_type'): '';
        $arr_data['event_date_from']   = $request->input('start');
        $arr_data['event_date_to']     = $request->input('end');
        $arr_data['all_day']           = $request->input('all_day')=='true' ? 1 :0 ;

        if($arr_users!='')
        {
            $arr_users =  implode(',',$arr_users); 
        }
        if($request->individual=='red')
        {
            $arr_data['level_class_id'] = $request->input('class');
            $arr_data['is_individual'] = 1;    
        }
        else
        {
            $arr_data['is_individual'] = 0;    
        }
        $arr_data['user_type'] = $arr_users;
       
        $this->CalendarModel->where('id',$id)->update($arr_data);
    
    }

    /*
    | get_class() : get list of classes 
    | Auther        : Pooja K  
    | Date          : 23-05-2018
    */ 
    public function get_class(Request $request)
    {
        $level_id = $request->input('level');

        $options ='';

        $obj_class = $this->CommonDataService->get_class($level_id);
    
        if(!empty($obj_class))
        {
            
            $arr_class  = $obj_class -> toArray();
            if(count($arr_class)>0)
            {
                $options .= '<option value="" selected>'.translation('select_class').'</option>';
                foreach($arr_class as $value)
                {
                    $options .= '<option value='.$value['id'];

                    if($request->has('class_id'))
                    {
                        if($request->class_id==$value['id'])
                            $options .= ' selected ';
                    }

                    $options .='>'.$value['class_details']['class_name'].'</option>';
                }
            }   
        }

        return $options;
    }
    /*
    | store() : Export List
    | Auther  : Pooja
    | Date    : 21-07-2018
    */
    public function export(Request $request)
    {       

            $obj_data = $this->CalendarModel
                                        ->with('get_level_class.level_details','get_level_class.class_details')
                                        ->where('school_id',$this->school_id)
                                        ->where('academic_year_id',$this->academic_year)
                                        ->get();

            if($obj_data->isEmpty()){
                Flash::error(translation("no_records_found_to_export"));
                return redirect()->back();
            }
            if(sizeof($obj_data)>500 && $request->file_format == 'csv'){
                Flash::error(translation("too_many_records_to_export"));
                return redirect()->back();
            }

            if($request->file_format == 'csv'){
                \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel) use($obj_data) 
                    {
                        $excel->sheet(ucwords($this->module_title), function($sheet) use($obj_data) 
                        {
                            $arr_fields['id']             = translation('sr_no');
                            $arr_fields['title']          = translation('title');
                            $arr_fields['description']    = translation('description');
                            $arr_fields['start_date']     = translation('start_date');
                            $arr_fields['end_date']       = translation('end_date');
                            $arr_fields['type']     = translation('type');
                            $arr_fields['users']          = translation('users');
                            $arr_fields['level']          = translation('level');
                            $arr_fields['class']          = translation('class');
                            
                            
                            
                            $sheet->row(2, ['',ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                            $sheet->row(4, $arr_fields);
                            if(sizeof($obj_data)>0) 
                            {
                                
                                $arr_tmp = [];
                                foreach($obj_data as $key => $result)
                                {
                                    $status = "";
                                    if($result->is_active==1)
                                    {
                                        $status = "Active";
                                    }
                                    elseif($result->is_active==0)
                                    {
                                        $status = "InActive";
                                    }
                                    
                                    $arr_tmp[$key]['id']             = intval($key+1);
                                    $arr_tmp[$key]['title']         = $result->event_title;
                                    $arr_tmp[$key]['description']   = $result->event_description;
                                    $arr_tmp[$key]['start_date']     = ($result->event_date_from!='') && ($result->event_date_from!='0000-00-00') && ($result->event_date_from!=null) ? $start = date('d M Y',strtotime($result->event_date_from)) :'-';
                                    $arr_tmp[$key]['end_date']     = ($result->event_date_to!='') && ($result->event_date_to!='0000-00-00') && ($result->event_date_to!=null) ? date('d M Y',strtotime($result->event_date_to)) :'-';
                                    $arr_tmp[$key]['type']     = translation(strtolower($result->event_type));
                                    $arr_tmp[$key]['users']          = $result->user_type;
                                    $arr_tmp[$key]['level']          = ($result->is_individual==1) && isset($result->get_level_class->level_details) ?$result->get_level_class->level_details->level_name:'-';
                                    $arr_tmp[$key]['class']          = ($result->is_individual==1) && isset($result->get_level_class->class_details) ? $result->get_level_class->class_details->class_name : '-';

                                }
                                   $sheet->rows($arr_tmp);
                            }
                        });
                    })->export('csv');     
            }
            
            if($request->file_format == 'pdf')
            {
                $school_name = $this->CommonDataService->get_school_name();

                $school_address = $this->CommonDataService->get_school_address();

                $school_email = $this->CommonDataService->get_school_email();

                $school_logo = $this->CommonDataService->get_school_logo();

                $this->arr_view_data['arr_data']      = $obj_data;
                $this->arr_view_data['school_name']   = $school_name;    
                $this->arr_view_data['school_address']= $school_address;
                $this->arr_view_data['school_email']  = $school_email;
                $this->arr_view_data['school_logo']   = $school_logo;

                $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
                return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
            }
         
    }
}