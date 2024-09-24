<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\NewsModel;
use App\Models\TaskModel;
use App\Models\ClubModel;
use App\Models\ExamModel;
use App\Models\UserModel;
use App\Models\ToDoModel;
use App\Models\StudentModel;
use App\Models\HomeworkModel;
use App\Models\LevelClassModel;
use App\Models\SuggestionModel;
use App\Models\CourseMaterialModel;
use App\Models\UserTranslationModel;
use App\Models\CanteenBookingsModel;
use App\Models\HomeworkStudentModel;
use App\Models\LevelTranslationModel;
use App\Models\ClassTranslationModel;
use App\Models\CourseTranslationModel;
use App\Models\ExamTypeTranslationModel;
use App\Models\ExamPeriodTranslationModel;
use App\Common\Services\CommonDataService;

use DB;
use Sentinel;
use Session;

class DashboardController extends Controller
{
    public function __construct(UserModel $user,
								CommonDataService $common,
                                HomeworkModel $homework_model,
                                HomeworkStudentModel $homework_student
                                )
	{
		$this->arr_view_data          = [];
		$this->module_title           = "Dashboard";
		$this->UserModel              = $user;
        $this->HomeworkModel          = $homework_model;
        $this->HomeworkStudentModel   = $homework_student;
        $this->NewsModel                    = new NewsModel();
        $this->TaskModel                    = new TaskModel();
        $this->ClubModel                    = new ClubModel();
        $this->ExamModel                    = new ExamModel();
        $this->LevelClassModel              = new LevelClassModel();
        $this->SuggestionModel              = new SuggestionModel();
        $this->CourseMaterialModel          = new CourseMaterialModel();
        $this->CanteenBookingsModel         = new CanteenBookingsModel();
        $this->UserTranslationModel         = new UserTranslationModel();
        $this->ClassTranslationModel        = new ClassTranslationModel();
        $this->LevelTranslationModel        = new LevelTranslationModel();
        $this->CourseTranslationModel       = new CourseTranslationModel();
        $this->ExamTypeTranslationModel     = new ExamTypeTranslationModel();
        $this->ExamPeriodTranslationModel   = new ExamPeriodTranslationModel();





		$this->CommonDataService	  =	$common;
		$this->module_view_folder     = "student.dashboard";
		$this->student_url_path       = url(config('app.project.role_slug.student_role_slug'));
		$this->theme_color            = theme_color();
        $this->role                   = config('app.project.role_slug.student_role_slug');

        $obj_data                     = Sentinel::getUser();

        $this->school_id              = Session::get('school_id');
        $this->academic_year          = Session::get('academic_year');
        $this->level_class_id         = Session::get('level_class_id');

        if($obj_data)
        {
            $student = StudentModel::where('user_id',$obj_data->id)->first();

            if(empty($student))
            {
                return redirect()->back();
            }
            $this->student_id = $student->id ;
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;


        }
        else
        {
            return redirect()->back();
        }
        

        if(Session::has('locale'))
        {
            $this->locale = Session::get('locale');
        }
        else
        {
            $this->locale = 'en';
        }


        $arr_current_user_access =[];
        $role = Sentinel::findRoleBySlug($this->role);
        $this->arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [];
	}

	public function index(Request $request)
    {
    	$this->CommonDataService->assign_module_permission_to_admin(config('app.project.role_slug.student_role_slug'));
        $this->CommonDataService->getNewsPublishDate();

        $arrToDo = $objToDo = array();
        $objToDo =  ToDoModel::where('student_id',$this->user_id)->where('academic_year_id',$this->academic_year)->where('school_id',$this->school_id)->orderBy('id','desc')->limit(20)->get();
        if($objToDo){
            $arrToDo = $objToDo->toArray();
        }
    
        $this->arr_view_data['arrToDo']         = $arrToDo;
        $this->arr_view_data['arr_final_tile']   = $this->built_dashboard_tiles($request);
        $this->arr_view_data['page_title']       = $this->module_title;
        $this->arr_view_data['student_url_path'] = $this->student_url_path;
        $this->arr_view_data['user_id']          = $this->user_id;
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    public function built_dashboard_tiles($request)
    {
        /*------------------------------------------------------------------------------
        | Note: Directly Use icon name - like, fa fa-user and use directly - 'user'
        ------------------------------------------------------------------------------*/
        $arr_current_user_access  = $this->arr_current_user_access;    
        $survey_count = $countCanteenBooking = $newsCount = $clubCount = $taskCount = $homeworkCount = $toDoCount = 0;        

        $toDoCount =  ToDoModel::where('student_id',$this->user_id)->where('academic_year_id',$this->academic_year)->where('school_id',$this->school_id)->count();

      


        $homework_table                = $this->HomeworkModel->getTable();
        $homework_student_table        = $this->HomeworkStudentModel->getTable();
        $course_table                  = $this->CourseTranslationModel->getTable();

        $homeworkCount = DB::table($homework_table)
                        ->select(DB::raw(   

                                            $homework_table.".id as homework_id,".
                                            $homework_student_table.".id as homework_student_id,".
                                            $homework_student_table.".rejection_reason as rejection_reason,".
                                            $homework_table.".description,".
                                            $homework_table.".due_date,".
                                            $homework_table.".added_date,".
                                            $homework_student_table.".status,".
                                            $course_table.".course_name"
                                            
                                        ))
                                        ->leftJoin($homework_student_table,$homework_table.'.id',' = ',$homework_student_table.'.homework_id')
                                        ->leftJoin($course_table,$homework_table.'.course_id',' = ',$course_table.'.course_id')
                                        ->where($course_table.'.locale','=',$this->locale)
                                        ->where($homework_table.'.school_id',$this->school_id)
                                        ->where($homework_table.'.academic_year_id',$this->academic_year)
                                        ->where($homework_table.'.level_class_id',$this->level_class_id)
                                        ->whereNull($homework_table.'.deleted_at')
                                        ->orderBy($homework_table.'.created_at','DESC')
                                        ->count();

        /*Exam Count*/
        $exam_table                    = $this->ExamModel->getTable();
        $exam_period_translation_table = $this->ExamPeriodTranslationModel->getTable();
        $exam_type_translationtable    = $this->ExamTypeTranslationModel->getTable();
        $level_class_table             = $this->LevelClassModel->getTable();
        $level_table                   = $this->LevelTranslationModel->getTable();  
        $class_table                   = $this->ClassTranslationModel->getTable();
        $course_table                  = $this->CourseTranslationModel->getTable();

        $examCount = DB::table($exam_table)
                        ->leftJoin($exam_period_translation_table,$exam_table.'.exam_period_id',' = ',$exam_period_translation_table.'.exam_id')
                        ->leftJoin($exam_type_translationtable,$exam_table.'.exam_type_id',' = ',$exam_type_translationtable.'.exam_type_id')
                        ->leftJoin($course_table,$exam_table.'.course_id',' = ',$course_table.'.course_id')
                        ->leftJoin($level_class_table,$exam_table.'.level_class_id',' = ',$level_class_table.'.id')
                        ->leftJoin($level_table,$level_class_table.'.level_id',' = ',$level_table.'.level_id')
                        ->leftJoin($class_table,$level_class_table.'.class_id',' = ',$class_table.'.class_id')
                        ->where($exam_period_translation_table.'.locale','=',$this->locale)
                        ->where($exam_type_translationtable.'.locale','=',$this->locale)
                        ->where($level_table.'.locale','=',$this->locale)
                        ->where($class_table.'.locale','=',$this->locale)
                        ->where($course_table.'.locale','=',$this->locale)
                        ->whereNull($exam_table.'.deleted_at')
                        ->where($exam_table.'.school_id','=',$this->school_id)
                        ->where($exam_table.'.status','=','APPROVED')
                        ->where($exam_table.'.level_class_id','=',$this->level_class_id)
                        ->orderBy($exam_table.'.created_at','DESC')->count();

        /*Exam Count*/


        /*Club Count*/
         $clubCount = $this->ClubModel
                                    ->whereHas('get_students',function($q){
                                        $q->where('level_class_id',$this->level_class_id);
                                        $q->where('student_id',$this->user_id); 
                                    })  
                                    ->with('get_supervisor')
                                    ->where('school_id',$this->school_id)
                                    ->where('academic_year_id',$this->academic_year)
                                    ->orderBy('id','DESC')
                                    ->count();

        /*Club Count*/


        /*task Count*/
        $tasks =[];
        $arr_tasks = $obj_tasks = array();
        $level =0;
        $obj_level_class = $this->CommonDataService->get_level_class($this->level_class_id);
        
        $level = isset($obj_level_class['level_id']) ? $obj_level_class['level_id']  : 0 ;  

        $obj_tasks = $this->TaskModel
                                    ->whereHas('get_user',function(){})
                                    ->with('get_user')
                                    ->with('get_supervisor')
                                    ->whereRaw('school_id="'.$this->school_id.'" and academic_year_id="'.$this->academic_year.'" and ( level_class_id="'.$this->level_class_id.'" or level_id='.$level.' )')
                                    ->orderBy('id','DESC')
                                    ->get();                            

        if(isset($obj_tasks) && !empty($obj_tasks)){
            $arr_tasks =  $obj_tasks -> toArray();

            foreach($arr_tasks as $task){
                $arr_roles = ($task['user_role']!='') ? explode(',',$task['user_role']) : array() ;

                    if(in_array($this->role,$arr_roles )){
                            array_push($tasks,$task);   
                        }
                    }
                }       
        $taskCount  = count($tasks);        
        /*task Count*/


        /*Course Material Count*/
        $courseMaterialCount = $this->CourseMaterialModel
                               ->with(['get_level_class'=>function($q){
                                    $q->with('level_details');
                                    $q->with('class_details');
                                },'get_course','get_material_details'])
                               ->where('level_class_id',$this->level_class_id)
                               ->orderBy('id','DESC')
                               ->count();

                             
        /*Course Material Count*/


        /*News Count*/
        $arr_academic_year = '';
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 
        if(!empty($academic_year)){
            $arr_academic_year = explode(',',$academic_year);
        }
        $news             = $this->NewsModel->getTable();
        $newsCount        = DB::table($news)
                                ->whereNull($news.'.deleted_at')
                                ->where($news.'.school_id','=',$this->school_id)
                                ->where($news.'.is_published',1)
                                ->whereIn($news.'.academic_year_id',$arr_academic_year)
                                ->orderBy($news.'.id','desc')
                                ->count();
        /*News Count*/

        /*Canteen Booking */
        $booking_table       = $this->CanteenBookingsModel->getTable();
        $prefixed_booking_table  = DB::getTablePrefix().$this->CanteenBookingsModel->getTable();
        $user_table              = $this->UserModel->getTable();
        $user_trans_table        = $this->UserTranslationModel->getTable();
        $prefixed_user_trans_table = DB::getTablePrefix().$this->UserTranslationModel->getTable();
        $countCanteenBooking = DB::table($booking_table)
                                 ->select(DB::raw($prefixed_booking_table.".id as id,".
                                                 $prefixed_booking_table.".created_at as created_at,".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',".$prefixed_user_trans_table.".last_name) as customer_name,".
                                                 $prefixed_booking_table.".total_price as total_price,".
                                                 $prefixed_booking_table.".delivery_status as delivery_status,".
                                                 $prefixed_booking_table.".payment_status as payment_status,".
                                                 $prefixed_booking_table.".order_no as order_no,".
                                                 $prefixed_booking_table.".order_type as order_type"
                                                 )) 
                                ->where($booking_table.'.school_id','=', $this->school_id)
                                ->where($booking_table.'.customer_id','=',$this->user_id)
                                ->where($booking_table.'.academic_year_id','=', $this->academic_year)
                                ->whereNull($booking_table.'.deleted_at')
                                ->leftJoin($user_table,$user_table.'.id',' = ',$booking_table.'.customer_id')
                                ->leftJoin($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_table.'.id')
                                ->groupBy($prefixed_booking_table.'.id')
                                ->orderBy($prefixed_booking_table.'.id','DESC')
                                ->count();
        /*Canteen Booking */


        /*Suggestion Count*/
        $suggestionsCount  = $this->SuggestionModel
                                     ->with('get_user_details','get_category')
                                     ->where('school_id',$this->school_id)
                                     ->where('academic_year_id',$this->academic_year)
                                     ->where('user_id',$this->user_id)
                                     ->whereIn('status',['REQUESTED','APPROVED'])
                                     ->count();
        
        /*Suggestion Count*/


        /*Survey Count*/
        $obj_survey    = array();
        $survey_count  = $this->CommonDataService->get_count_survey($this->role);
        /*Survey Count*/

        

        $arr_final_tile = [];
        if($arr_current_user_access)
        {



            $URL     = url('/').'/'.$this->role;
            $img_url = url('/').'/images/admin/';
            /******** Get all permissions given to logged user *******/
            if(in_array('timetable.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'/timetable',
                                      'images'          => $img_url.'4timetable-icon-dash.png',
                                      'tile_color'      => 'border-bottm-1',
                                      'module_title'    => translation('timetable'),
                                      'module_sub_title'=> translation('check_your_timetable'),
                                      'total_count'     => '']; 
            }

            if(in_array('attendance.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'/attendance',
                                      'images'          => $img_url.'6attendance-icon-dash.png',
                                      'tile_color'      => 'border-bottm-2',
                                      'module_title'    => translation('attendance'),
                                      'module_sub_title'=> translation('know_your_attendance'),
                                      'total_count'     => ''];       
            } 

            if(in_array('todo.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'/todo',
                                      'images'          => $img_url.'17to-do-list-icon-dash.png',
                                      'tile_color'      => 'border-bottm-3',
                                      'module_title'    => translation('to_do_list'),
                                      'module_sub_title'=> translation('get_your_todo'),
                                      'total_count'     => $toDoCount];       
            } 

            if(in_array('homework.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'/homework',
                                      'images'          => $img_url.'3homework-icon-dash.png',
                                      'tile_color'      => 'border-bottm-4',
                                      'module_title'    => translation('homework'),
                                      'module_sub_title'=> translation('check_your_homework'),
                                      'total_count'     => $homeworkCount];       
            } 

            if(in_array('exam.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'/exam',
                                      'images'          => $img_url.'7exam-icon-dash.png',
                                      'tile_color'      => 'border-bottm-5',
                                      'module_title'    => translation('exam'),
                                      'module_sub_title'=> translation('konw_your_exams'),
                                      'total_count'     => $examCount];       
            } 

            if(in_array('club.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'/club',
                                      'images'          => $img_url.'12club-icon-dash.png',
                                      'tile_color'      => 'border-bottm-6',
                                      'module_title'    => translation('club'),
                                      'module_sub_title'=> translation('check_out_your_clubs'),
                                      'total_count'     => $clubCount];       
            } 

            if(in_array('calendar.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'/calendar',
                                      'images'          => $img_url.'5school-calendar-icon-dash.png',
                                      'tile_color'      => 'border-bottm-7',
                                      'module_title'    => translation('calendar'),
                                      'module_sub_title'=> translation('get_calender'),
                                      'total_count'     => ''];       
            }

            if(in_array('task.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'/task',
                                      'images'          => $img_url.'10task-icon-dash.png',
                                      'tile_color'      => 'border-bottm-8',
                                      'module_title'    => translation('task'),
                                      'module_sub_title'=> translation('know_your_task'),
                                      'total_count'     => $taskCount];       
            } 

            if(in_array('course_material.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'/course_material',
                                      'images'          => $img_url.'2course-icon-dash.png',
                                      'tile_color'      => 'border-bottm-9',
                                      'module_title'    => translation('course_material'),
                                      'module_sub_title'=> translation('know_course_material'),
                                      'total_count'     => $courseMaterialCount];       
            } 

            if(in_array('news.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'/news',
                                      'images'          => $img_url.'11news-icon-dash.png',
                                      'tile_color'      => 'border-bottm-10',
                                      'module_title'    => translation('news'),
                                      'module_sub_title'=> translation('latest_news_feeds'),
                                      'total_count'     => $newsCount];       
            } 

            
             /*   $arr_final_tile[] = [ 'module_url'      => $URL.'/fees',
                                      'images'          => $img_url.'11news-icon-dash.png',
                                      'tile_color'      => 'border-bottm-11',
                                      'module_title'    => translation('school_fees'),
                                      'module_sub_title'=> translation('check_your_fees'),
                                      'total_count'     => ''];    */   
            

            if(in_array('canteen_bookings.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'/canteen_bookings',
                                      'images'          => $img_url.'9canteen-icon-dash.png',
                                      'tile_color'      => 'border-bottm-12',
                                      'module_title'    => translation('canteen_bookings'),
                                      'module_sub_title'=> translation('look_whats_new_to_eat'),
                                      'total_count'     => $countCanteenBooking];       
            }  


            if(in_array('suggestions.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'/suggestions/manage',
                                      'images'          => $img_url.'14suggestion-box-icon-dash.png',
                                      'tile_color'      => 'border-bottm-13',
                                      'module_title'    => translation('suggestions'),
                                      'module_sub_title'=> translation('drop_your_suggestion'),
                                      'total_count'     => $suggestionsCount];       
            }   

            if(in_array('survey.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'/survey',
                                      'images'          => $img_url.'15-survey-icon-dash.png',
                                      'tile_color'      => 'border-bottm-14',
                                      'module_title'    => translation('survey'),
                                      'module_sub_title'=> translation('help_to_improve'),
                                      'total_count'     => $survey_count];       
            }

            
            $arr_final_tile[] = [ 'module_url'          => 'javascript:void(0);',
                                      'images'          => $img_url.'13-tudent-behavior-icon-dash.png',
                                      'tile_color'      => 'border-bottm-15',
                                      'module_title'    => translation('gradebook'),
                                      'module_sub_title'=> translation('graph_of_your_year'),
                                      'total_count'     => ''];       
                                 
        
        }       
        return  $arr_final_tile;                          
    }

}
