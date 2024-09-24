<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Services\CommonDataService;
use App\Models\CalendarModel;

use Session;
use Sentinel;
class CalendarController extends Controller
{
    public function __construct(    
                                    CalendarModel $calendar_model,
                                    CommonDataService $common_data_service
                                )
    {
        
        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.professor_panel_slug')).'/calendar';
        $this->BaseModel                    = $calendar_model;
        $this->CommonDataService            = $common_data_service;

        $this->module_title                 = translation("calendar");
        $this->modyle_url_slug              = translation("calendar");
        $this->CalendarModel                = $calendar_model;
        $this->module_view_folder           = "professor.calendar";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-calendar';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';


        $this->academic_year                = Session::get('academic_year');
        $this->school_id                    = Session::get('school_id');

        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['page_title']      = $this->module_title;
        
        
        $this->first_name = $this->last_name =$this->ip_address ='';

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id           = $obj_data->id;
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
        }
        /* Activity Section */



    }   
    public function index()
    {   

        $obj_levels = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id,'optional');
        $arr_levels = $obj_levels->toArray();    

        $arr_levels=[];
        foreach($arr_levels as $value){
          array_push($arr_levels,$value['level_class_id']);  
        }
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
                $users = explode(',',$value['user_type']);

                if(in_array(config('app.project.professor_panel_slug'),$users))
                {
                    $flag=1;
                    if($value['is_individual']==1)
                    {
                        if(in_array($value['level_class_id'],$arr_levels))
                        {
                           $flag=1;
                        }
                    }
                    else
                    {
                        $flag=1;
                    }
                    if($flag==1)
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
            }
        }
    	$page_title = translation('calendar');
    	
        $this->arr_view_data['module_title']    = $page_title;       
        
        $this->arr_view_data['arr_events']      = $arr_events;
        $this->arr_view_data['arr_holidays']    = $arr_holidays;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function get_events()
    {
        $events = '';
        $obj_levels = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id,'optional');
        $arr_levels = $obj_levels->toArray();    

        $arr_levels=[];
        foreach($arr_levels as $value){
          array_push($arr_levels,$value['level_class_id']);  
        }
        $obj_events = $this->CalendarModel
                                        ->where('school_id',$this->school_id)
                                        ->where('academic_year_id',$this->academic_year)
                                        ->get();
        if($obj_events)
        {
            $arr_events = $obj_events->toArray();
            $arr_data = [];

            foreach($arr_events as $value)
            {
                $users = explode(',',$value['user_type']);

                if(in_array(config('app.project.professor_panel_slug'),$users))
                {
                    $flag=1;
                    if($value['is_individual']==1)
                    {
                        if(in_array($value['level_class_id'],$arr_levels))
                        {
                           $flag=1;
                        }
                    }
                    else
                    {
                        $flag=1;
                    }
                    if($flag==1)
                    {
                        $temp_arr = [];
                        $temp_arr['title'] =  $value['event_title'];
                        $temp_arr['start'] =  $value['event_date_from'];
                        $temp_arr['end'] =  $value['event_date_to'];
                        $temp_arr['type'] =  $value['event_type'];
                        $temp_arr['allDay'] =  $value['all_day']=='1' ? true :false;
                        $temp_arr['id'] =  $value['id'];
                        $temp_arr['url'] = $this->module_url_path.'/view/'.base64_encode($value['id']);
                        if($temp_arr['type']=="EXAM")
                        {
                            $temp_arr['editable'] = FALSE;   
                            $temp_arr['backgroundColor'] = "#2bc3ac";
                             
                        }
                        elseif($temp_arr['type']=="HOLIDAY") 
                        {
                            $temp_arr['editable'] = FALSE;
                            $temp_arr['backgroundColor'] = "#fab63f";
                        }
                        else
                        {
                            $temp_arr['editable'] = FALSE;   
                            $temp_arr['backgroundColor'] = "#8cbf26";
                        }
                        
                        array_push($arr_data,$temp_arr);
                    }
                }
            }
            $events = json_encode($arr_data);
        }                               
        return $events;

    }
    public function view($enc_id)
    {
        $id = base64_decode($enc_id);
        $obj_calendar = $this->CalendarModel
                                        ->with(['get_level_class.level_details','get_level_class.class_details'])
                                        ->where('id',$id)
                                        ->first();

        if($obj_calendar)
        {
            $arr_data = $obj_calendar->toArray();
        }
        $this->arr_view_data['arr_data'] = $arr_data;
        
        $this->arr_view_data['module_title']    = translation("view")." ".$this->module_title;
        return view($this->module_view_folder.'.view', $this->arr_view_data);
    }
}
