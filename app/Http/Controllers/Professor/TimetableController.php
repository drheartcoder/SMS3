<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\StudentModel;
use App\Models\LevelClassModel;
use App\Models\SchoolPeriodsModel;
use App\Models\SchoolTimeTableModel;
use App\Common\Services\CommonDataService;
use App\Models\AssignReplacedLecturesModel;

use Session;
use Sentinel;
use DB;
use Datatables;
use Validator;
use Flash;

class TimetableController extends Controller
{
	
    public function __construct(SchoolTimeTableModel $school_timetablemodel,
                                SchoolPeriodsModel $school_periods_model,
                                LevelClassModel $level_class_model,
                                CommonDataService $common_data_service,
                                StudentModel $student_model,
                                AssignReplacedLecturesModel $replacement)
    {
    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.professor_panel_slug')).'/timetable';
        $this->module_title                 = translation('timetable');
 
        $this->module_view_folder           = "professor.timetable";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-clock-o';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-clock-o';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');

    	$this->SchoolTimeTableModel         = $school_timetablemodel;
        $this->SchoolPeriodsModel           = $school_periods_model;
        $this->LevelClassModel              = $level_class_model;
        $this->CommonDataService            = $common_data_service;
        $this->StudentModel                 = $student_model;
        $this->AssignReplacedLecturesModel    = $replacement;

    	$this->arr_view_data['page_title']      = translation('timetable');
    	$this->arr_view_data['module_url_path'] = $this->module_url_path;
    	$this->arr_view_data['theme_color']     = $this->theme_color;
    	$this->arr_view_data['module_icon']     = $this->module_icon;
    	$this->arr_view_data['edit_icon']       = $this->edit_icon;
    	$this->arr_view_data['create_icon']     = $this->create_icon;

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
    | Date          : 7 Jun 2018
    */
    public function index()
    {

        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 
        $arr_academic_year = explode(',',$academic_year);  

        $obj_level = $arr_level = [];
        $obj_level  =   $this->CommonDataService->get_levels($this->academic_year);
        
        if($obj_level){
            $arr_level = $obj_level->toArray();
        }   
        
        $obj_class = $arr_class = [];
        $obj_class = LevelClassModel::whereHas('get_class',function($q){ $q->where('is_active','=',1); })->with(['get_class'=>function($q){ $q->where('is_active','=',1); }])->where('school_id',$this->school_id)->whereIn('academic_year_id',$arr_academic_year)->groupBy('class_id')->get();
         $arr_class = $obj_class->toArray();

        $cond_arr = [
                        'school_id'        => $this->school_id,
                        'academic_year_id' => $this->academic_year,
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
                                                    ->with(['professor_subjects','level_details','class_details'])
                                                    ->where('professor_id',$this->user_id)
                                                    ->get();

        $arr_time_table = $arr_replaced_lectures = [];
        if($obj_time_table)
        {
            $arr_time_table = $obj_time_table->toArray();
        }
        $tomorrow = date("Y-m-d", strtotime("+1 day"));
        $date     = date('Y-m-d');
        $obj_replaced_lectures = $this->AssignReplacedLecturesModel
                                      ->with('level_class_details.level_details','level_class_details.class_details')
                                      ->where('replaced_professor_id',$this->user_id)->whereIn('date',[$date,$tomorrow])->get();
        if(isset($obj_replaced_lectures) && count($obj_replaced_lectures)>0)
        {
            $arr_replaced_lectures = $obj_replaced_lectures->toArray();
        }
        
        /*Bring Maximum  period for header count */
        $school_time_table = $this->SchoolTimeTableModel->getTable();
        $maxPeriod = DB::table($school_time_table)
                        ->select(DB::raw('MAX(periods_no) as max_period_no'))
                        ->where($school_time_table.'.academic_year_id','=',$this->academic_year)
                        ->where($school_time_table.'.school_id','=',$this->school_id)
                        ->where($school_time_table.'.professor_id','=',$this->user_id)
                        ->get();
        /*Bring Maximum  period for header count */
        if(empty($arr_time_table)){
            Flash::error(translation("no_record_found"));
        }
        
        
         
        $this->arr_view_data['arr_time_table']  = $arr_time_table;
        $this->arr_view_data['arr_replaced_lectures']  = $arr_replaced_lectures;
        $this->arr_view_data['weekly_days']     = $this->weekly_days;
        $this->arr_view_data['period_no']       = !empty($maxPeriod)&&$maxPeriod[0]->max_period_no?$maxPeriod[0]->max_period_no:'1';

        $this->arr_view_data['page_title']      = $this->module_title;
        $this->arr_view_data['edit_icon']       = $this->edit_icon;
        $this->arr_view_data['module_title']    = translation('timetable');
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        return view($this->module_view_folder.'.timetable', $this->arr_view_data);
    }   

}
