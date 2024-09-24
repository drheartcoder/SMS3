<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;
use App\Models\ClubModel;
use App\Models\NewsModel;
use App\Models\ExamModel;
use App\Models\TaskModel;
use App\Models\ClaimModel;
use App\Models\HomeworkModel;
use App\Models\DocumentsModel;
use App\Models\LevelClassModel;
use App\Models\SuggestionModel;
use App\Models\SchoolAdminModel;
use App\Models\CourseMaterialModel;
use App\Models\CanteenBookingsModel;
use App\Models\UserTranslationModel;
use App\Models\ClassTranslationModel;
use App\Models\LevelTranslationModel;
use App\Models\CourseTranslationModel;
use App\Models\ExamTypeTranslationModel;
use App\Models\CanteenBookingDetailModel;
use App\Models\ExamPeriodTranslationModel;
use App\Models\ClaimPermissionModel;
use App\Common\Services\CommonDataService;

use DB;
use Session;
use Sentinel;

class DashboardController extends Controller
{
    public function __construct(UserModel $user,
								CommonDataService $common
								)
	{
		$this->arr_view_data          = [];
		$this->module_title           = "Dashboard";
		$this->UserModel              = $user;
		$this->CommonDataService	  =	$common;
        $this->ClubModel              = new ClubModel();
        $this->NewsModel              = new NewsModel();
        $this->ExamModel              = new ExamModel();
        $this->TaskModel              = new TaskModel();
        $this->ClaimModel             = new ClaimModel();
        $this->HomeworkModel          = new HomeworkModel();
        $this->DocumentsModel         = new DocumentsModel();
        $this->LevelClassModel        = new LevelClassModel();
        $this->SuggestionModel        = new SuggestionModel();
        $this->SchoolAdminModel       = new SchoolAdminModel();
        $this->CourseMaterialModel    = new CourseMaterialModel();
        $this->UserTranslationModel   = new UserTranslationModel();
        $this->CanteenBookingsModel   = new CanteenBookingsModel();
        $this->LevelTranslationModel  = new LevelTranslationModel();
        $this->ClassTranslationModel  = new ClassTranslationModel();
        $this->CourseTranslationModel = new CourseTranslationModel();
        $this->ExamTypeTranslationModel   = new ExamTypeTranslationModel();
        $this->CanteenBookingDetailModel  = new CanteenBookingDetailModel();
        $this->ExamPeriodTranslationModel = new ExamPeriodTranslationModel();
		$this->module_view_folder     = "professor.dashboard";
		$this->professor_url_path     = url(config('app.project.role_slug.professor_role_slug'));
        $this->role                   = config('app.project.role_slug.professor_role_slug');
		$this->theme_color            = theme_color();

        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');


        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;
        }

        $obj_school_admin = $this->SchoolAdminModel->where('school_id',$this->school_id)->first();
        $this->school_admin_id = 0;
        if(isset($obj_school_admin) && !empty($obj_school_admin))
        {
            $this->school_admin_id = $obj_school_admin->user_id;
        }

        /*Local Section*/
        if(Session::has('locale')){
            $this->locale = Session::get('locale');
        }else{
            $this->locale = 'en';
        }
        /*Local Section*/

        $arr_current_user_access =[];
        $role = Sentinel::findRoleBySlug($this->role);
        $this->arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [];

        $obj_permission = ClaimPermissionModel::select('is_active')->where('school_id',Session::get('school_id'))->first();

        if(isset($obj_permission) && !empty($obj_permission))
        {
            $this->claim_module_access = $obj_permission->is_active;
        }
        else
        {
            $this->claim_module_access = 0;
        }


	}

    /*---------------------------------
    index() : Show dashboard
    Auther : Amol 
    Date : 
    ---------------------------------*/

    public function index(Request $request)
    {
        
    	$this->CommonDataService->assign_module_permission_to_admin(config('app.project.role_slug.professor_role_slug'));
        $this->CommonDataService->getNewsPublishDate();

    	$user_id = 0;
    	
    	$user = Sentinel::check();
    	if($user)
    	{
    		$user_id = isset($user->id)?$user->id:0;
    	}
    
    	
        $this->arr_view_data['arr_final_tile']  = $this->built_dashboard_tiles($request);
    	$this->arr_view_data['page_title']         = $this->module_title;
    	$this->arr_view_data['professor_url_path'] = $this->professor_url_path;
    	$this->arr_view_data['user_id']            = $user_id;
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    /*---------------------------------
    index() : Show dashboard Tiles
    Auther  : Padmashri 
    Date    : 9th July 2018 
    ---------------------------------*/
    public function built_dashboard_tiles($request)
    {
        /*------------------------------------------------------------------------------
        | Note: Directly Use icon name - like, fa fa-user and use directly - 'user'
        ------------------------------------------------------------------------------*/
        $arr_current_user_access  = $this->arr_current_user_access;    
        $survey_count = $countCanteenBooking = $newsCount = $clubCount = $taskCount = $homeworkCount = $toDoCount =$surveyCount = $suggestionCount = $claimCount = 0;        

        

       /*homework count*/
        $homework_table                = $this->HomeworkModel->getTable();
        $level_class_table             = $this->LevelClassModel->getTable();
        $level_table                   = $this->LevelTranslationModel->getTable();  
        $class_table                   = $this->ClassTranslationModel->getTable();
        $course_table                  = $this->CourseTranslationModel->getTable();
        $homeworkCount =   DB::table($homework_table)
                        ->select(DB::raw(   

                                            $homework_table.".id as homework_id,".
                                            $homework_table.".description,".
                                            $homework_table.".due_date,".
                                            $homework_table.".added_date,".
                                            $level_table.".level_name,".
                                            $class_table.".class_name,".
                                            $course_table.".course_name"
                                            
                                        ))
                                        ->leftJoin($course_table,$homework_table.'.course_id',' = ',$course_table.'.course_id')
                                        ->leftJoin($level_class_table,$homework_table.'.level_class_id',' = ',$level_class_table.'.id')
                                        ->leftJoin($level_table,$level_class_table.'.level_id',' = ',$level_table.'.level_id')
                                        ->leftJoin($class_table,$level_class_table.'.class_id',' = ',$class_table.'.class_id')
                                        ->where($level_table.'.locale','=',$this->locale)
                                        ->where($class_table.'.locale','=',$this->locale)
                                        ->where($course_table.'.locale','=',$this->locale)
                                        ->where($homework_table.'.school_id',$this->school_id)
                                        ->where($homework_table.'.academic_year_id',$this->academic_year)
                                        ->whereNull($homework_table.'.deleted_at')
                                        ->where($homework_table.'.homework_added_by','=',$this->user_id)
                                        ->orderBy($homework_table.'.created_at','DESC')
                                        ->count();
                                        
       /*homework count*/

        /*Exam Count*/
        $exam_table                    = $this->ExamModel->getTable();
        $exam_period_translation_table = $this->ExamPeriodTranslationModel->getTable();
        $exam_type_translationtable    = $this->ExamTypeTranslationModel->getTable();
        $level_class_table             = $this->LevelClassModel->getTable();
        $level_table                   = $this->LevelTranslationModel->getTable();  
        $class_table                   = $this->ClassTranslationModel->getTable();
        $course_table                  = $this->CourseTranslationModel->getTable();

        $examCount = DB::table($exam_table)
                        ->select(DB::raw(   

                                            $exam_table.".id as exam_id,".
                                            $exam_table.".exam_start_time,".
                                            $exam_table.".exam_end_time,".
                                            $exam_table.".exam_name,".
                                            $exam_type_translationtable.".exam_type,".
                                            $level_table.".level_name,".
                                            $class_table.".class_name,".
                                            $course_table.".course_name,".
                                            $exam_table.".status,".
                                            $exam_table.".exam_added_by"
                                        ))
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
                                        ->where($exam_table.'.exam_added_by','=',$this->user_id)
                                        ->where($exam_table.'.school_id',$this->school_id)
                                        ->where($exam_table.'.academic_year_id','=',$this->academic_year)
                                        ->orderBy($exam_table.'.created_at','DESC')->count();
        /*Exam Count*/

        /*Canteen Booking*/
        $booking_table                            = $this->CanteenBookingsModel->getTable();
        $prefixed_booking_table                   = DB::getTablePrefix().$this->CanteenBookingsModel->getTable();
        $canteen_booking_details_table            = $this->CanteenBookingDetailModel->getTable();
        $user_table                               = $this->UserModel->getTable();
        $user_trans_table                         = $this->UserTranslationModel->getTable();
        $prefixed_user_trans_table                = DB::getTablePrefix().$this->UserTranslationModel->getTable();
        
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
                            ->groupBy($booking_table.'.id')
                            ->orderBy($booking_table.'.id','DESC')->count();
        /*Canteen Booking*/

        /*Task*/
        $tasks=[];
        $levels =[];
        $level_class=[];
        $obj_levels_for_professor = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id,'optional');
        if(isset($obj_levels_for_professor) && !empty($obj_levels_for_professor)){
            foreach($obj_levels_for_professor as $value){
                array_push($levels,$value->level_id);
                array_push($level_class,$value->level_class_id);    
            }
        }
      
        $obj_tasks = $this->TaskModel
                                    ->whereHas('get_user',function(){})
                                    ->with('get_user')
                                    ->with('get_supervisor')
                                    ->where('school_id',$this->school_id)
                                    ->where('academic_year_id',$this->academic_year)
                                    ->orderBy('id','DESC')
                                    ->whereIn('added_by',[$this->school_admin_id,$this->user_id])
                                    ->get();                            

        if(isset($obj_tasks) && !empty($obj_tasks)){
            $arr_tasks =  $obj_tasks -> toArray();

            foreach($arr_tasks as $task){
                $arr_roles = ($task['user_role']!='') ? explode(',',$task['user_role']) : array() ;

                if($task['added_by']==$this->school_admin_id){

                    if(in_array( config('app.project.role_slug.professor_role_slug'),$arr_roles )){
                        if($task['level_id']==0){

                            if(in_array($task['level_class_id'] , $level_class )){
                                array_push($tasks,$task);   
                            }
                        }
                        else{
                            
                            if(in_array($task['level_id'] , $levels )){
                                array_push($tasks,$task);   
                            }   

                        }
                    }
                }
                else{   

                    array_push($tasks,$task);
                }
            }   

        }  
        $taskCount = count($tasks);
        /*Task*/

        /*News */
        $arr_academic_year = '';
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 
        if(!empty($academic_year)){
            $arr_academic_year = explode(',',$academic_year);
        }
        $news             = $this->NewsModel->getTable();
        $newsCount        = DB::table($news)
                                ->select(DB::raw(
                                                 $news.".id  as id,".
                                                 $news.".news_title  as    news_title,".
                                                 $news.".publish_date, ".
                                                 $news.".end_date, ".
                                                 $news.".added_date_time, ".
                                                 $news.".start_time, ".
                                                 $news.".end_time, ".
                                                 $news.".is_published "
                                                 ))
                                ->whereNull($news.'.deleted_at')
                                ->where($news.'.school_id','=',$this->school_id)
                                ->where($news.'.is_published',1)
                                ->whereIn($news.'.academic_year_id',$arr_academic_year)
                                ->orderBy($news.'.id','desc')->count();
        /*News */

        /*Club Count*/
        $clubCount = $this->ClubModel->with('get_supervisor')
                                    ->where('school_id',$this->school_id)
                                    ->where('academic_year_id',$this->academic_year)
                                    ->where('supervisor_id',$this->user_id)
                                    ->orderBy('id','DESC')
                                    ->count();
        /*Club Count*/

        /*Course Material*/
        $courseMaterialCount = $this->CourseMaterialModel
                               ->with(['get_level_class'=>function($q){
                                    $q->with('level_details');
                                    $q->with('class_details');
                                },'get_course','get_material_details'])
                               ->orderBy('id','DESC')
                               ->where('material_added_by','=',$this->user_id)->count();
         /*Course Material*/

        /*Suggestion Count*/
        $suggestionCount  = $this->SuggestionModel
                                     ->with('get_user_details','get_category')
                                     ->where('school_id',$this->school_id)
                                     ->where('academic_year_id',$this->academic_year)
                                     ->where('user_id',$this->user_id)
                                     ->whereIn('status',['REQUESTED','APPROVED'])
                                     ->count();
        /*Suggestion Count*/

        /*Survey Count*/
        $surveyCount = $this->CommonDataService->get_count_survey($this->role);   
        /*Survey Count*/

        /**/
        $claim_table                   = $this->ClaimModel->getTable();
        $user_table                    = $this->UserTranslationModel->getTable();
        $level_class_table             = $this->LevelClassModel->getTable();
        $level_table                   = $this->LevelTranslationModel->getTable();  
        $class_table                   = $this->ClassTranslationModel->getTable();

        $claimCount = DB::table($claim_table)
                        ->select(DB::raw(   

                                            $claim_table.".id as claim_id,".
                                            $claim_table.".professor_id as professor_id,".
                                            $claim_table.".student_national_id as national_id,".
                                            $claim_table.".title as title,".
                                            $level_table.".level_name,".
                                            $class_table.".class_name,".
                                            $claim_table.".status,".
                                            "CONCAT(".$user_table.".first_name,' ',"
                                                     .$user_table.".last_name) as user_name"
                                                    ))
                                        ->Join($user_table,$claim_table.'.student_id',' = ',$user_table.'.user_id')
                                        ->Join($level_class_table,$claim_table.'.level_class_id',' = ',$level_class_table.'.id')
                                        ->Join($level_table,$level_class_table.'.level_id',' = ',$level_table.'.level_id')
                                        ->Join($class_table,$level_class_table.'.class_id',' = ',$class_table.'.class_id')
                                        ->where($user_table.'.locale','=',$this->locale)
                                        ->where($level_table.'.locale','=',$this->locale)
                                        ->where($class_table.'.locale','=',$this->locale)
                                        ->whereNull($claim_table.'.deleted_at')
                                        ->where($claim_table.'.professor_id','=',$this->user_id)
                                        ->where($claim_table.'.school_id',$this->school_id)
                                        ->where($claim_table.'.academic_year_id','=',$this->academic_year)
                                        ->orderBy($claim_table.'.created_at','DESC')->count();
                 
        /**/

        $arr_final_tile = [];
        if($arr_current_user_access)
        {



            $URL     = url('/').'/'.$this->role.'/';
            $img_url = url('/').'/images/admin/';
            /******** Get all permissions given to logged user *******/
            if(in_array('message.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'message',
                                      'images'          => $img_url.'1message-icon-dash.png',
                                      'tile_color'      => 'border-bottm-1',
                                      'module_title'    => translation('message'),
                                      'module_sub_title'=> translation('connect_with_people'),
                                      'total_count'     => '']; 
            }

            if(in_array('homework.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'homework',
                                      'images'          => $img_url.'3homework-icon-dash.png',
                                      'tile_color'      => 'border-bottm-3',
                                      'module_title'    => translation('homework'),
                                      'module_sub_title'=> translation('check_assigned_homework'),
                                      'total_count'     => $homeworkCount];       
            } 

            if(in_array('timetable.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'timetable',
                                      'images'          => $img_url.'4timetable-icon-dash.png',
                                      'tile_color'      => 'border-bottm-4',
                                      'module_title'    => translation('timetable'),
                                      'module_sub_title'=> translation('check_your_timetable'),
                                      'total_count'     => ''];       
            } 


            if(in_array('calendar.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'calendar',
                                      'images'          => $img_url.'5school-calendar-icon-dash.png',
                                      'tile_color'      => 'border-bottm-5',
                                      'module_title'    => translation('calendar'),
                                      'module_sub_title'=> translation('get_calender'),
                                      'total_count'     => ''];       
            }

            if(in_array('attendance.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'attendance/professor',
                                      'images'          => $img_url.'6attendance-icon-dash.png',
                                      'tile_color'      => 'border-bottm-6',
                                      'module_title'    => translation('attendance'),
                                      'module_sub_title'=> translation('check_attendance'),
                                      'total_count'     => ''];       
            } 


             if(in_array('exam.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'exam',
                                      'images'          => $img_url.'7exam-icon-dash.png',
                                      'tile_color'      => 'border-bottm-7',
                                      'module_title'    => translation('exam'),
                                      'module_sub_title'=> translation('check_exam_details'),
                                      'total_count'     => $examCount];       
            } 
            
             if(in_array('canteen_bookings.list',$arr_current_user_access)){ 
                $arr_final_tile[] = [ 'module_url'      => $URL.'canteen_bookings',
                                      'images'          => $img_url.'9canteen-icon-dash.png',
                                      'tile_color'      => 'border-bottm-9',
                                      'module_title'    => translation('canteen'),
                                      'module_sub_title'=> translation('look_whats_new_to_eat'),
                                      'total_count'     => $countCanteenBooking];       
            }  


           if(in_array('task.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'task',
                                      'images'          => $img_url.'10task-icon-dash.png',
                                      'tile_color'      => 'border-bottm-10',
                                      'module_title'    => translation('task'),
                                      'module_sub_title'=> translation('check_task'),
                                      'total_count'     => $taskCount];       
            } 

            if(in_array('news.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'news',
                                      'images'          => $img_url.'11news-icon-dash.png',
                                      'tile_color'      => 'border-bottm-11',
                                      'module_title'    => translation('news'),
                                      'module_sub_title'=> translation('latest_news_feeds'),
                                      'total_count'     => $newsCount];       
            }
    
            if(in_array('club.list',$arr_current_user_access)){
              $arr_final_tile[] =   [   'module_url'      =>  $URL.'club',
                                        'images'          => $img_url.'12club-icon-dash.png',
                                        'tile_color'      => 'border-bottm-12',
                                        'module_title'    => translation('club'),
                                        'module_sub_title'=> translation('check_out_clubs'),
                                        'total_count'     => $clubCount];       
                                   
            }


             if(in_array('club.list',$arr_current_user_access)){
            $arr_final_tile[] = [     'module_url'      =>  $URL.'student_behaviour',
                                      'images'          => $img_url.'13-tudent-behavior-icon-dash.png',
                                      'tile_color'      => 'border-bottm-13',
                                      'module_title'    => translation('student_behaviour'),
                                      'module_sub_title'=> translation('manage_students_behaviour'),
                                      'total_count'     => ''];       
            }  


            if(in_array('course_material.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'course_material',
                                      'images'          => $img_url.'4timetable-icon-dash.png',
                                      'tile_color'      => 'border-bottm-4',
                                      'module_title'    => translation('course_material'),
                                      'module_sub_title'=> translation('check_course_material'),
                                      'total_count'     => $courseMaterialCount];       
            } 

            if(in_array('suggestions.list',$arr_current_user_access)){
              $arr_final_tile[] = [     'module_url'      => $URL.'suggestions/manage',
                                        'images'          => $img_url.'14suggestion-box-icon-dash.png',
                                        'tile_color'      => 'border-bottm-14',
                                        'module_title'    => translation('suggestions'),
                                        'module_sub_title'=> translation('drop_your_suggestion'),
                                        'total_count'     => $suggestionCount];       
                                   
            }  
            
            if(in_array('survey.list',$arr_current_user_access)){
              $arr_final_tile[] = [     'module_url'      => $URL.'survey',
                                        'images'          => $img_url.'15-survey-icon-dash.png',
                                        'tile_color'      => 'border-bottm-15',
                                        'module_title'    => translation('survey'),
                                        'module_sub_title'=> translation('help_to_improve'),
                                        'total_count'     => $surveyCount];       
                                   
            } 

            if($this->claim_module_access == '1')
            {
                if(in_array('claim.list',$arr_current_user_access)){
                $arr_final_tile[] = [     'module_url'      => $URL.'claim',
                                          'images'          => $img_url.'16-claim-icon-dash.png',
                                          'tile_color'      => 'border-bottm-18',
                                          'module_title'    => translation('claim'),
                                          'module_sub_title'=> translation('check_claim'),
                                          'total_count'     => $claimCount];       
                                     
                }
            }    
            
    
        }       
        return  $arr_final_tile;                          
    }
}
