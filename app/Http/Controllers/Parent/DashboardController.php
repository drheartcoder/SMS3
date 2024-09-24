<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\ClubModel;
use App\Models\NewsModel;
use App\Models\ExamModel;
use App\Models\UserModel;
use App\Models\TaskModel;
use App\Models\ClaimModel;
use App\Models\StudentModel;
use App\Models\HomeworkModel;
use App\Models\DocumentsModel;
use App\Models\LevelClassModel;
use App\Models\SuggestionModel;
use App\Models\CourseMaterialModel;
use App\Models\CanteenBookingsModel;
use App\Models\UserTranslationModel;
use App\Models\ClassTranslationModel;
use App\Models\LevelTranslationModel;
use App\Models\CourseTranslationModel;
use App\Models\ExamTypeTranslationModel;
use App\Models\CanteenBookingDetailModel;
use App\Models\ExamPeriodTranslationModel;


use App\Common\Services\CommonDataService;

use DB;
use Sentinel;
use Session;

class DashboardController extends Controller
{
    public function __construct(UserModel $user,
								CommonDataService $common,
                                StudentModel $StudentModel
								)
	{
		$this->arr_view_data          = [];
		$this->module_title           = "Dashboard";
		$this->UserModel              = $user;
		$this->CommonDataService	    =	$common;
    $this->StudentModel           = $StudentModel;
    $this->ClubModel              = new ClubModel();
    $this->NewsModel              = new NewsModel();
    $this->ExamModel              = new ExamModel();
    $this->TaskModel              = new TaskModel();
    $this->ClaimModel             = new ClaimModel();
    $this->HomeworkModel          = new HomeworkModel();
    $this->DocumentsModel         = new DocumentsModel();
    $this->LevelClassModel        = new LevelClassModel();
    $this->SuggestionModel        = new SuggestionModel();
    $this->CourseMaterialModel    = new CourseMaterialModel();
    $this->UserTranslationModel   = new UserTranslationModel();
    $this->CanteenBookingsModel   = new CanteenBookingsModel();
    $this->LevelTranslationModel  = new LevelTranslationModel();
    $this->ClassTranslationModel  = new ClassTranslationModel();
    $this->CourseTranslationModel = new CourseTranslationModel();
    $this->ExamTypeTranslationModel   = new ExamTypeTranslationModel();
    $this->CanteenBookingDetailModel  = new CanteenBookingDetailModel();
    $this->ExamPeriodTranslationModel = new ExamPeriodTranslationModel();
 
    		$this->module_view_folder     = "parent.dashboard";
    		$this->module_url_path        = url(config('app.project.parent_panel_slug'));
    		$this->theme_color            = theme_color();
        $this->role                   = config('app.project.role_slug.parent_role_slug'); 
        $this->school_id              = Session::get('school_id');
        $this->academic_year          = Session::get('academic_year');
        $this->level_class_id         = Session::get('level_class_id');
        $this->level_id               = Session::get('student_level');
        $this->class_id               = Session::get('student_class');
        $this->kid_id                 = Session::get('kid_id');

        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;
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
	}

    /*---------------------------------
    index() : Show dashboard
    Auther  : Padmashri Joshi
    Date    : 21-05-2018
    ---------------------------------*/

    public function index(Request $request)
    {
      
        $this->CommonDataService->assign_module_permission_to_admin(config('app.project.parent_panel_slug'));
        $this->CommonDataService->getNewsPublishDate();
        $user_id = 0;
    	$user = Sentinel::check();
    	if($user)
    	{
            $user_id = isset($user->id)?$user->id:0;

    	}

    	$this->arr_view_data['arr_final_tile']  = $this->built_dashboard_tiles($request);
      $this->arr_view_data['page_title']      = $this->module_title;
    	$this->arr_view_data['module_url_path'] = $this->module_url_path;
    	$this->arr_view_data['user_id']         = $user_id;
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    
    public function set_parent_kid(Request $request){
        $kidId        = $request->input('kidId');
        $levelClassId = $request->input('level_class_id');

        if($kidId!='' && $levelClassId!=''){
            $resData = $this->CommonDataService->get_level_class($levelClassId);
            
            if(!empty($resData) ){
                $student = $this->StudentModel->where('user_id',$kidId)
                                   ->where('school_id',\Session::get('school_id'))
                                   ->first();

                session()->put('student_id', isset($student->id) ? $student->id :0);
                 session()->put('student_level', $resData['level_id']);
                 session()->put('student_level_name', $resData['level_details']['level_name']);
                 session()->put('student_class', $resData['class_id']);
                 session()->put('student_class_name', $resData['class_details']['class_name']);
                 session()->put('level_class_id', $levelClassId);
                 session()->put('kid_id', $kidId);
            
            }else{
                
            }
        }else{
            
        }
        

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
        $survey_count = $countCanteenBooking = $newsCount = $clubCount = $taskCount = $homeworkCount = $toDoCount = $documentCount =$surveyCount = $suggestionCount = $claimCount = 0;        

       /*document Count*/

        $documentCount = $this->DocumentsModel
                             ->where('academic_year_id',$this->academic_year)
                             ->where('school_id',$this->school_id)
                             ->where('level_class_id',$this->level_class_id)
                             ->where('student_id',$this->kid_id)
                             ->orderBy('id','desc')
                             ->count();
       /*document Count*/

       /*homework count*/
       $homeworkCount =   $this->HomeworkModel
                                        ->with(['get_course','homework_details'=>function($q){
                                            $q->where('student_id',\Session::get('student_id'));
                                        }])
                                        ->where('school_id',$this->school_id)
                                        ->where('level_class_id',$this->level_class_id)
                                        ->where('academic_year_id',$this->academic_year)
                                        ->count();
                                        
       /*homework count*/

       /*Course Material*/
        $courseMaterialCount = $this->CourseMaterialModel
                               ->with(['get_level_class'=>function($q){
                                    $q->with('level_details');
                                    $q->with('class_details');
                                },'get_course','get_material_details'])
                               ->where('level_class_id',$this->level_class_id)
                               ->orderBy('id','DESC')
                               ->count();
       /*Course Material*/
        

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
                                            $exam_period_translation_table.".exam_name,".
                                            $exam_type_translationtable.".exam_type,".
                                            $level_table.".level_name,".
                                            $class_table.".class_name,".
                                            $course_table.".course_name,".
                                            $exam_table.".status"
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
                                        ->where($exam_table.'.school_id','=',$this->school_id)
                                        ->where($exam_table.'.status','=','APPROVED')
                                        ->where($exam_table.'.level_class_id','=',$this->level_class_id)
                                        ->orderBy($exam_table.'.created_at','DESC')->count();
       /*Exam Count*/

       /*News Count*/
       $arr_academic_year = '';
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 
        if(!empty($academic_year)){
            $arr_academic_year = explode(',',$academic_year);
        }
        $news             = $this->NewsModel->getTable();
        $newsCount      = DB::table($news)
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
       /*News Count*/

       /*Canteen Booking*/
        $booking_table                            = $this->CanteenBookingsModel->getTable();
        $prefixed_booking_table                   = DB::getTablePrefix().$this->CanteenBookingsModel->getTable();
        $canteen_booking_details_table            = $this->CanteenBookingDetailModel->getTable();
        $user_table                               = $this->UserModel->getTable();
        $prefixed_user_table                      = DB::getTablePrefix().$this->UserModel->getTable();
         $prefixed_user_trans_table               = DB::getTablePrefix().$this->UserTranslationModel->getTable();
        $user_trans_table                         = $this->UserTranslationModel->getTable();
       
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
                            ->where($booking_table.'.academic_year_id','=', $this->academic_year)
                            ->where($user_table.'.id','=', $this->user_id)
                            ->whereNull($booking_table.'.deleted_at')
                            ->leftJoin($user_table,$user_table.'.id',' = ',$booking_table.'.customer_id')
                            ->leftJoin($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_table.'.id')
                            ->groupBy($prefixed_booking_table.'.id')
                            ->orderBy($prefixed_booking_table.'.id','DESC')->count();
       /*Canteen Booking*/

       /*Task Count*/
        $level =0;
        $obj_level_class = $this->CommonDataService->get_level_class($this->level_class_id);
        
        $level = isset($obj_level_class['level_id']) ? $obj_level_class['level_id']  : 0 ;  

        $tasks =[];
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

                if(in_array( config('app.project.role_slug.parent_role_slug'),$arr_roles ) ||  in_array( config('app.project.role_slug.student_role_slug'),$arr_roles )){
                            array_push($tasks,$task);   
                        }
                }
              }
        $taskCount = count($tasks);
       /*Task Count*/

       /*Club count*/
       $clubCount = $this->ClubModel
                      ->whereHas('get_students',function($q){
                        $q->where('level_class_id',$this->level_class_id);
                        $q->where('student_id',$this->kid_id);  
                      })  
                      ->with('get_supervisor')
                                    ->where('school_id',$this->school_id)
                                    ->where('academic_year_id',$this->academic_year)
                                    ->orderBy('id','DESC')
                                    ->count();
       /*Club count*/


       /*Suggetion*/
        $suggestionCount  = $this->SuggestionModel
                                     ->with('get_user_details','get_category')
                                     ->where('school_id',$this->school_id)
                                     ->where('academic_year_id',$this->academic_year)
                                     ->where('user_id',$this->user_id)
                                     ->whereIn('status',['REQUESTED','APPROVED'])
                                     ->count();
       /*Suggetion*/

       /*Survey Count*/
        $surveyCount    = $this->CommonDataService->get_count_survey($this->role);       
       /*Survey Count*/

       /*Claim Count*/
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
                                        ->where($claim_table.'.student_id','=',$this->kid_id)
                                        ->where($claim_table.'.status','=','APPROVED')
                                        ->where($claim_table.'.school_id',$this->school_id)
                                        ->where($claim_table.'.academic_year_id','=',$this->academic_year)
                                        ->orderBy($claim_table.'.created_at','DESC')->count();
       /*Claim Count*/

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
           
            /*if(in_array('attendance.list',$arr_current_user_access)){*/
                $arr_final_tile[] = [ 'module_url'      => 'kid_profile',
                                      'images'          => $img_url.'17kid-profle-icon-dash.png',
                                      'tile_color'      => 'border-bottm-2',
                                      'module_title'    => translation('kid_profile'),
                                      'module_sub_title'=> translation('manage_kids_profile'),
                                      'total_count'     => ''];       
            /*} */

            if(in_array('document.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'document',
                                      'images'          => $img_url.'18document-icon-dash.png',
                                      'tile_color'      => 'border-bottm-3',
                                      'module_title'    => translation('document'),
                                      'module_sub_title'=> translation('check_documents'),
                                      'total_count'     => $documentCount];       
            } 

            if(in_array('course_material.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'course_material',
                                      'images'          => $img_url.'4timetable-icon-dash.png',
                                      'tile_color'      => 'border-bottm-4',
                                      'module_title'    => translation('course_material'),
                                      'module_sub_title'=> translation('check_course_material'),
                                      'total_count'     => $courseMaterialCount];       
            } 

            if(in_array('homework.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'homework',
                                      'images'          => $img_url.'3homework-icon-dash.png',
                                      'tile_color'      => 'border-bottm-5',
                                      'module_title'    => translation('homework'),
                                      'module_sub_title'=> translation('check_homework'),
                                      'total_count'     => $homeworkCount];       
            } 

            if(in_array('timetable.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'timetable',
                                      'images'          => $img_url.'4timetable-icon-dash.png',
                                      'tile_color'      => 'border-bottm-6',
                                      'module_title'    => translation('timetable'),
                                      'module_sub_title'=> translation('check_kid_timetable'),
                                      'total_count'     => ''];       
            } 

            if(in_array('calendar.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'calendar',
                                      'images'          => $img_url.'5school-calendar-icon-dash.png',
                                      'tile_color'      => 'border-bottm-7',
                                      'module_title'    => translation('calendar'),
                                      'module_sub_title'=> translation('get_calender'),
                                      'total_count'     => ''];       
            }

            if(in_array('attendance.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'attendance',
                                      'images'          => $img_url.'6attendance-icon-dash.png',
                                      'tile_color'      => 'border-bottm-8',
                                      'module_title'    => translation('attendance'),
                                      'module_sub_title'=> translation('check_attendance'),
                                      'total_count'     => ''];       
            } 

            if(in_array('exam.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'exam',
                                      'images'          => $img_url.'7exam-icon-dash.png',
                                      'tile_color'      => 'border-bottm-9',
                                      'module_title'    => translation('exam'),
                                      'module_sub_title'=> translation('check_exam_details'),
                                      'total_count'     => $examCount];       
            } 

             
            
            $arr_final_tile[] = [ 'module_url'      => 'javascript:void(0);',
                                  'images'          => $img_url.'8result-icon-dash.png',
                                  'tile_color'      => 'border-bottm-10',
                                  'module_title'    => translation('result'),
                                  'module_sub_title'=> translation('check_result'),
                                  'total_count'     => ''];       
             

            if(in_array('canteen_bookings.list',$arr_current_user_access)){ 
                $arr_final_tile[] = [ 'module_url'      => $URL.'canteen_bookings',
                                      'images'          => $img_url.'9canteen-icon-dash.png',
                                      'tile_color'      => 'border-bottm-11',
                                      'module_title'    => translation('canteen'),
                                      'module_sub_title'=> translation('look_whats_new_to_eat'),
                                      'total_count'     => $countCanteenBooking];       
            } 
        
           if(in_array('task.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'task',
                                      'images'          => $img_url.'10task-icon-dash.png',
                                      'tile_color'      => 'border-bottm-12',
                                      'module_title'    => translation('task'),
                                      'module_sub_title'=> translation('check_task'),
                                      'total_count'     => $taskCount];       
            }   

            if(in_array('news.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'news',
                                      'images'          => $img_url.'11news-icon-dash.png',
                                      'tile_color'      => 'border-bottm-13',
                                      'module_title'    => translation('news'),
                                      'module_sub_title'=> translation('latest_news_feeds'),
                                      'total_count'     => $newsCount];       
            }

            if(in_array('club.list',$arr_current_user_access)){
              $arr_final_tile[] = [ 'module_url'          =>  $URL.'club',
                                        'images'          => $img_url.'12club-icon-dash.png',
                                        'tile_color'      => 'border-bottm-14',
                                        'module_title'    => translation('club'),
                                        'module_sub_title'=> translation('check_out_clubs'),
                                        'total_count'     => $clubCount];       
                                   
            }

            if(in_array('payment.list',$arr_current_user_access)){
              $arr_final_tile[] = [ 'module_url'          => 'payment',
                                        'images'          => $img_url.'21payment-icon-dash.png',
                                        'tile_color'      => 'border-bottm-15',
                                        'module_title'    => translation('payment'),
                                        'module_sub_title'=> translation('check_the_payments'),
                                        'total_count'     => ''];       
                                   
            }  

            if(in_array('suggestions.list',$arr_current_user_access)){
              $arr_final_tile[] = [ 'module_url'          => $URL.'suggestions/manage',
                                        'images'          => $img_url.'14suggestion-box-icon-dash.png',
                                        'tile_color'      => 'border-bottm-16',
                                        'module_title'    => translation('suggestions'),
                                        'module_sub_title'=> translation('drop_your_suggestion'),
                                        'total_count'     => $suggestionCount];       
                                   
            }  

            if(in_array('survey.list',$arr_current_user_access)){
              $arr_final_tile[] = [ 'module_url'          => $URL.'survey',
                                        'images'          => $img_url.'15-survey-icon-dash.png',
                                        'tile_color'      => 'border-bottm-17',
                                        'module_title'    => translation('survey'),
                                        'module_sub_title'=> translation('help_to_improve'),
                                        'total_count'     => $surveyCount];       
                                   
            }  
        
            if(in_array('claim.list',$arr_current_user_access)){
            $arr_final_tile[] = [ 'module_url'          => $URL.'claim',
                                      'images'          => $img_url.'16-claim-icon-dash.png',
                                      'tile_color'      => 'border-bottm-18',
                                      'module_title'    => translation('claim'),
                                      'module_sub_title'=> translation('check_claim'),
                                      'total_count'     => $claimCount];       
                                 
          }  

          $arr_final_tile[] = [ 'module_url'           => 'javascript:void(0);',
                                      'images'          => $img_url.'20gradebook-icon-dash.png',
                                      'tile_color'      => 'border-bottm-19',
                                      'module_title'    => translation('gradebook'),
                                      'module_sub_title'=> translation('graph_of_year'),
                                      'total_count'     => ''];       
            
        }       
        return  $arr_final_tile;                          
    }
}
