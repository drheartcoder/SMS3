<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
 

use App\Models\SchoolPeriodsModel;
use App\Models\SchoolTimeTableModel;
use App\Models\SchoolPeriodTimingModel;

use Session;
use Sentinel;
use DB;
use Datatables;
use Validator;
use Flash;

class TimetableController extends Controller
{
	
    public function __construct( 
                                SchoolTimeTableModel $school_timetablemodel,
                                SchoolPeriodsModel $school_periods_model
                                )
    {
    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.parent_panel_slug')).'/timetable';
        $this->module_title                 = translation('timetable');
 
        $this->module_view_folder           = "parent.timetable";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-clock-o';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-clock-o';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->level_class_id               = Session::get('level_class_id');
        $this->level_id                     = Session::get('student_level');
        $this->class_id                     = Session::get('student_class');
        
        $this->SchoolTimeTableModel         = $school_timetablemodel;
        $this->SchoolPeriodsModel           = $school_periods_model;
    	$this->arr_view_data['page_title']      = translation('timetable');
    	$this->arr_view_data['module_url_path'] = $this->module_url_path;
    	$this->arr_view_data['theme_color']     = $this->theme_color;
    	$this->arr_view_data['module_icon']     = $this->module_icon;
    	$this->arr_view_data['edit_icon']       = $this->edit_icon;
    	$this->arr_view_data['create_icon']     = $this->create_icon;
        $this->SchoolPeriodTimingModel          = new SchoolPeriodTimingModel();
        $this->weekly_days = config('app.project.week_days');

    	$obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;
        }
    }
    /*
    | index()       : Load the timetable for that perticular student of that parent
    | Auther        : Padmashri Joshi
    | Date          : 6 Jun 2018
    */
    public function index()
    {
        $obj_periods = $arr_periods = $arr_holiday = $arr_periods_timing  = $obj_period_timing = array();
        $obj_periods = $this->SchoolPeriodsModel
                             ->where('academic_year_id',$this->academic_year)
                             ->where('school_id',$this->school_id)
                             ->where('level_id',$this->level_id)
                             ->where('class_id',$this->class_id)
                             ->first();

        if($obj_periods)
        {
            $arr_periods = $obj_periods->toArray();


            $arr_holiday =  isset($arr_periods['weekly_off'])&&$arr_periods['weekly_off']!=''?json_decode($arr_periods['weekly_off']):config('app.project.default_weekly_off');

            /*GET the values  from period Start time end time section */
            $obj_period_timing = SchoolPeriodTimingModel::where('school_id',$this->school_id)->where('level_class_id',$arr_periods['level_class_id'])->orderBy('period_no','asc')->where('academic_year_id',$this->academic_year)->get();
            if($obj_period_timing){
                $arr_periods_timing = $obj_period_timing->toArray();
            }
            /*GET the values  from period Start time end time section */
        }
        

        $weekly_days = $this->weekly_days;

        $cond_arr = [
                        'school_id'        => $this->school_id,
                        'academic_year_id' => $this->academic_year,
                        'level_id'         => $this->level_id,
                        'class_id'         => $this->class_id,
                    ];

        $SCHOOL_ID      = $this->school_id;           
        $obj_time_table = $this->SchoolTimeTableModel->where($cond_arr)
                                                    ->whereHas('professor_details',function($q1)use($SCHOOL_ID){
                                                        $q1->where('is_active','=', 1);
                                                        $q1->where('has_left','=', 0);
                                                        $q1->where('school_id','=',$SCHOOL_ID);
                                                    })
                                                    ->with(['user_details' => function ($query)
                                                    {
                                                          $query->select('id','profile_image');
                                                    }])
                                                    ->with(['professor_subjects'])
                                                    ->get();

        $arr_time_table = [];
        if($obj_time_table)
        {
            $arr_time_table = $obj_time_table->toArray();
        }
        
        if(empty($arr_time_table)){
            Flash::error(translation("no_record_found"));
        }

        

        $this->arr_view_data['arr_periods_timing']  = $arr_periods_timing;         
        $this->arr_view_data['arr_time_table']  = $arr_time_table;
        $this->arr_view_data['weekly_days']     = $this->weekly_days;
        $this->arr_view_data['arr_holiday']     = $arr_holiday;
        $this->arr_view_data['arr_periods']     = $arr_periods;
        $this->arr_view_data['period_no']       = isset($arr_periods['num_of_periods'])?$arr_periods['num_of_periods']:'1';
        
        $this->arr_view_data['module_title']    = translation("student")." ".$this->module_title;
        return view($this->module_view_folder.'.timetable', $this->arr_view_data);
    }

     

}
