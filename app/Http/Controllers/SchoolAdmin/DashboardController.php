<?php
namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\BusModel;
use App\Models\UserModel;
use App\Models\ClubModel;

use App\Models\NewsModel;
use App\Models\ExamModel;
use App\Models\TaskModel;
use App\Models\RoleModel;
use App\Models\FeesModel;
use App\Models\ClassModel;
use App\Models\ClaimModel;
use App\Models\CourseModel;
use App\Models\ParentModel;
use App\Models\StudentModel;
use App\Models\UserRoleModel;
use App\Models\HomeworkModel;
use App\Models\EmployeeModel;
use App\Models\ProfessorModel;
use App\Models\DocumentsModel;
use App\Models\FeesSchoolModel;
use App\Models\LevelClassModel;
use App\Models\SuggestionModel;
use App\Models\BookDetailsModel;
use App\Models\SchoolAdminModel;
use App\Models\AcademicYearModel;
use App\Models\SchoolParentModel;
use App\Models\SchoolCourseModel;
use App\Models\StockReceivedModel;
use App\Models\LibraryContentModel;
use App\Models\CourseMaterialModel;
use App\Models\CanteenProductsModel;
use App\Models\FeesTranslationModel;
use App\Models\AdmissionConfigModel;
use App\Models\CanteenBookingsModel;
use App\Models\AssessmentScaleModel;
use App\Models\UserTranslationModel;
use App\Models\ClassTranslationModel;
use App\Models\LevelTranslationModel;
use App\Models\CourseTranslationModel;
use App\Models\CanteenProductTypesModel;
use App\Models\ExamTypeTranslationModel;
use App\Models\CanteenBookingDetailModel;
use App\Models\ExamPeriodTranslationModel;
use App\Common\Services\CommonDataService;

use DB;
use Session;
use Sentinel;
use Lava;

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
		$this->module_view_folder     = "schooladmin.dashboard";
		$this->school_admin_url_path  = url(config('app.project.role_slug.school_admin_role_slug'));
		$this->theme_color            = theme_color();

		$this->BusModel               = new BusModel();
		$this->ClubModel              = new ClubModel();
        $this->NewsModel              = new NewsModel();
        $this->FeesModel              = new FeesModel();
        $this->ExamModel              = new ExamModel();
        $this->TaskModel              = new TaskModel();
        $this->RoleModel              = new RoleModel();
        $this->ClaimModel             = new ClaimModel();
        $this->ClassModel             = new ClassModel();
        $this->ParentModel            = new ParentModel();
        $this->CourseModel            = new CourseModel();
        $this->StudentModel           = new StudentModel();
        $this->UserRoleModel          = new UserRoleModel();
        $this->EmployeeModel          = new EmployeeModel();
        $this->HomeworkModel          = new HomeworkModel();
        $this->ProfessorModel         = new ProfessorModel();
        $this->DocumentsModel         = new DocumentsModel();
        $this->FeesSchoolModel        = new FeesSchoolModel();
        $this->LevelClassModel        = new LevelClassModel();
        $this->SuggestionModel        = new SuggestionModel();
        $this->BookDetailsModel       = new BookDetailsModel();
        $this->SchoolAdminModel       = new SchoolAdminModel();
        $this->SchoolCourseModel      = new SchoolCourseModel();
	    $this->SchoolParentModel	  = new SchoolParentModel();
        $this->AcademicYearModel      = new AcademicYearModel();
        $this->StockReceivedModel     = new StockReceivedModel();
        $this->LibraryContentModel    = new LibraryContentModel();
        $this->CourseMaterialModel    = new CourseMaterialModel();
        $this->CanteenProductsModel   = new CanteenProductsModel();
        $this->FeesTranslationModel   = new FeesTranslationModel();
        $this->UserTranslationModel   = new UserTranslationModel();
        $this->AdmissionConfigModel   = new AdmissionConfigModel();
        $this->AssessmentScaleModel   = new AssessmentScaleModel();
        $this->CanteenBookingsModel   = new CanteenBookingsModel();
        $this->LevelTranslationModel  = new LevelTranslationModel();
        $this->ClassTranslationModel  = new ClassTranslationModel();
        $this->CourseTranslationModel = new CourseTranslationModel();
        $this->CanteenProductTypesModel   = new CanteenProductTypesModel();
        $this->ExamTypeTranslationModel   = new ExamTypeTranslationModel();
        $this->CanteenBookingDetailModel  = new CanteenBookingDetailModel();
        $this->ExamPeriodTranslationModel = new ExamPeriodTranslationModel();

        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->role 						= config('app.project.role_slug.school_admin_role_slug');

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
        	$this->user_id           = $obj_data->id;  
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
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

        $arr_current_user_access =[];
        $role = Sentinel::findRoleBySlug($this->role);
        $this->arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [];
     
	}
	   
    public function index(Request $request)
    {
         $reasons = \Lava::DataTable();

        $reasons->addStringColumn('Reasons')
                ->addNumberColumn('Percent')
                ->addRow(array('Check Reviews', 5))
                ->addRow(array('Watch Trailers', 2))
                ->addRow(array('See Actors Other Work', 4))
                ->addRow(array('Settle Argument', 89));


        $donutchart = \Lava::DonutChart('IMDB', $reasons, [
                        'title' => 'Reasons I visit IMDB'
                    ]);

        https://dev.virtualearth.net/REST/V1/Imagery/Map/Road/42.6564%2C-73.7638/13?mapSize=600,300&format=png&key=YOUR-API-KEY-HERE

        
    	$this->CommonDataService->assign_module_permission_to_admin(config('app.project.role_slug.school_admin_role_slug'));
    	$this->CommonDataService->getNewsPublishDate();
    	$user_id = 0;
    	
    	$user = Sentinel::check();
    	if($user){
    		$user_id = isset($user->id)?$user->id:0;
    	}

        $isAcademicYear =  $isSchoolAdminCreated = $isClasses = $isLevel = $isCourse = $completeProfile = 0;
        $isAcademicYear = $this->AcademicYearModel
                              ->where('school_id',$this->school_id)
                              ->count();
        $isSchoolAdminCreated = $this->SchoolAdminModel->where('user_id',$this->user_id)->where('school_id','<>','0')->count();
        	
        $isLevel        =  $this->LevelClassModel->whereHas('level_details',function($q){ $q->select('level_id','id');})->where('school_id',$this->school_id)->groupBy('level_id')->count();
        
       $isClasses        =  $this->LevelClassModel->whereHas('class_details',function($q){ $q->select('class_id','id'); })->where('school_id',$this->school_id)->count();                  

        $str                =  $this->CommonDataService->get_academic_year_less_than($this->academic_year);
        $arr_academic_year  = explode(',', $str);   
        $isCourse           = $this->get_course_count($arr_academic_year);

        $completeProfile += isset($isSchoolAdminCreated)&&$isSchoolAdminCreated>0?20:0;
        $completeProfile += isset($isLevel)&&$isLevel>0?20:0;
        $completeProfile += isset($isClasses)&&$isClasses>0?20:0;
        $completeProfile += isset($isCourse)&&$isCourse>0?20:0;
        $completeProfile += isset($isAcademicYear)&&$isAcademicYear>0?20:0;

        /*$lava = new Lavacharts;*/

        $reasons = \Lava::DataTable();

        $reasons->addStringColumn('Reasons')
                ->addNumberColumn('Percent')
                ->addRow(array('Check Reviews', 5))
                ->addRow(array('Watch Trailers', 2))
                ->addRow(array('See Actors Other Work', 4))
                ->addRow(array('Settle Argument', 89));


        $donutchart = \Lava::BarChart('IMDB', $reasons, [
                        'title' => 'Reasons I visit IMDB'
                    ]);
        
                                
        $this->arr_view_data['isCourse']              = $isCourse;
        $this->arr_view_data['isLevel']               = $isLevel;
        $this->arr_view_data['isClasses']             = $isClasses;
        $this->arr_view_data['isAcademicYear']        = $isAcademicYear;
        $this->arr_view_data['completeProfile']       = $completeProfile;
        $this->arr_view_data['isSchoolAdminCreated']  = $isSchoolAdminCreated;
        $this->arr_view_data['school_admin_url_path'] = $this->school_admin_url_path;
        $this->arr_view_data['page_title']            = $this->module_title;
     	$this->arr_view_data['arr_final_tile'] = $this->built_dashboard_tiles($request);
    	$this->arr_view_data['user_id']        = $user_id;


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
        | Note: Directly Use icon name - like, <i class="fa fa-users"></i> and use directly - 'user'
        ------------------------------------------------------------------------------*/
        $arr_current_user_access  = $this->arr_current_user_access;    
        $survey_count = $countCanteenBooking = $newsCount = $clubCount = $taskCount = $homeworkCount = $professorCount =$surveyCount = $parentCount = $studentCount = $levelClassCount = $employeeCount = $driverCount = $suggestionCount = $claimCount = $admissionCount = $countCanteenProducts = $examCount = $assessmentScaleCount = $feesStructureCount =  $bookCount = 0;        

      

        $arr_final_tile = [];
        if($arr_current_user_access)
        {



            $URL     = url('/').'/'.$this->role.'/';
            $img_url = url('/').'/images/admin/';
            /******** Get all permissions given to logged user *******/
            /*$admissionCount*/
            $admissionCount = $this->AdmissionConfigModel
	                                            ->with('get_academic_year','get_level','get_education_board')
	                                            ->where('school_id',$this->school_id)
	                                            ->count();
	        /*$admissionCount*/

	         $start_id    = $this->AcademicYearModel
                              ->where(['start_date'=>($this->AcademicYearModel
                                                           ->where('school_id',$this->school_id)
                                                           ->min('start_date'))
                                      ])
                              ->first(['id']);

	        /* GET PROFESSOR */
	        $user_details      = $this->UserModel->getTable();
	        $prefixed_user_details    = DB::getTablePrefix().$this->UserModel->getTable();
	        $user_trans_table  = $this->UserTranslationModel->getTable();                  
	        $professor         = $this->ProfessorModel->getTable();       
	        $professorCount    = DB::table($professor)
	                                ->select(DB::raw($professor.".id as id,".
	                                                 $user_details.".email as email, ".
	                                                 $professor.".is_active as is_active, ".
	                                                 $professor.".academic_year_id as academic_year_id, ".
	                                                 $prefixed_user_details.".mobile_no, ".
	                                                 $prefixed_user_details.".national_id, ".
	                                                 "CONCAT(".$user_trans_table.".first_name,' ',"
	                                                          .$user_trans_table.".last_name) as user_name"
	                                                 ))
	                                ->whereNull($professor.'.deleted_at')
	                                ->join($user_details,$professor.'.user_id',' = ',$user_details.'.id')
	                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
	                                ->where($professor.".school_id","=",$this->school_id)
	                                ->where($user_trans_table.'.locale','=',$this->locale)
	                                ->whereBetween($professor.'.academic_year_id',[$start_id->id,$this->academic_year])
	                                ->orderBy($user_details.'.created_at','DESC')->count();
	        /* GET PROFESSOR */


	        /*GTE STUDENT*/
	        $user_details             	 = $this->UserModel->getTable();
	        $prefixed_user_details    	 = DB::getTablePrefix().$this->UserModel->getTable();
			$level_class          		 = $this->LevelClassModel->getTable();
			$level_trans               	 = $this->LevelTranslationModel->getTable();
			$class_trans               	 = $this->ClassTranslationModel->getTable();
	        $student_table               = $this->StudentModel->getTable();
			$prefixed_user_trans_table   = DB::getTablePrefix().$this->UserTranslationModel->getTable();
		    $studentCount = DB::table($student_table)
                                ->select(DB::raw($student_table.".id as id,".
                                                 $prefixed_user_details.".email as email, ".
                                                 $student_table.".is_active as is_active, ".
                                                 $student_table.".academic_year_id as academic_year_id, ".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
                                                          .$prefixed_user_trans_table.".last_name) as user_name,".
                                                 $level_trans.".level_name as level_name, ".
                                                 $class_trans.".class_name as class_name, ".
                                                 $prefixed_user_details.".national_id,".
                                                 $student_table.".has_left"        
                                                 ))
                                ->whereNull($student_table.'.deleted_at')
                                ->join($user_details,$student_table.'.user_id', ' = ',$user_details.'.id')
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                ->join($level_class,$student_table.'.level_class_id', ' = ',$level_class.'.id')
                                ->join($level_trans,$level_trans.'.level_id', ' = ',$level_class.'.level_id')
                                ->join($class_trans,$class_trans.'.class_id', ' = ',$level_class.'.class_id')
                                ->where($user_trans_table.'.locale','=',$this->locale)
                                ->where($level_trans.'.locale','=',$this->locale)
                                ->where($class_trans.'.locale','=',$this->locale)
                                ->where($student_table.'.school_id',$this->school_id)
                                ->where($student_table.'.academic_year_id',$this->academic_year)
                                ->orderBy($student_table.'.created_at','DESC')->count();
	        /*GTE STUDENT*/

	        /*GET PARENT*/
	      	$prefixed_user_details        = DB::getTablePrefix().$this->UserModel->getTable();
			$prefixed_user_trans_table    = DB::getTablePrefix().$this->UserTranslationModel->getTable();
			$parent_details               = $this->ParentModel->getTable();                  
	        $school_parent_details    	  = $this->SchoolParentModel->getTable();                  
	        $parentCount = DB::table($school_parent_details)
	                                ->select(DB::raw($school_parent_details.".id as id,".
	                                                 $prefixed_user_details.".email as email, ".
	                                                 $school_parent_details.".is_active as is_active, ".
	                                                 $user_details.".national_id as national_id,".
	                                                 $user_details.".mobile_no as mobile_no,".
	                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
	                                                          .$prefixed_user_trans_table.".last_name) as user_name"
	                                                 ))
	                                ->join($parent_details,$school_parent_details.'.parent_id','=',$parent_details.'.user_id')
	                                ->join($user_details,$parent_details.'.user_id','=',$user_details.'.id')
	                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
	                                ->where($user_trans_table.'.locale','=',$this->locale)
	                                ->where($school_parent_details.'.school_id',$this->school_id)
	                                ->whereNull($school_parent_details.'.deleted_at')
	                                ->orderBy($school_parent_details.'.id','DESC')->count();
	       
	        
	        /*GET PARENT*/

	        /*Employee Count*/

           

         

            $employee                     = $this->EmployeeModel->getTable();       
            $prefixed_employee_table      =  DB::getTablePrefix().$this->EmployeeModel->getTable();        
           
	        
	         $employeeCount = DB::table($employee)
                                ->select(DB::raw($employee.".id as id,".
                                                 $prefixed_user_details.".email as email, ".
                                                 $employee.".is_active as is_active, ".
                                                 $employee.".academic_year_id as academic_year_id, ".
                                                 $prefixed_employee_table.".user_role as role,".
                                                 $prefixed_user_details.".national_id as national_id,".
                                                 $prefixed_user_details.".mobile_no as mobile_no,".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
                                                          .$prefixed_user_trans_table.".last_name) as user_name"
                                                 ))
                                ->whereNull($user_details.'.deleted_at')
                                ->whereNotIn($employee.'.user_role',['driver','canteen_manager','canteen_supervisor','canteen_staff'])
                                ->join($user_details,$employee.'.user_id','=',$user_details.'.id')
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                ->where($user_trans_table.'.locale','=',$this->locale)
                                ->where($employee.'.school_id','=',$this->school_id)
                                ->whereBetween($employee.'.academic_year_id',[$start_id->id,$this->academic_year])
                                ->orderBy($employee.'.created_at','DESC')->count();
	        /*Employee Count*/

	        /* Driver Count */
 		       
	       /* $driverCount = DB::table($employee)
	                                ->select(DB::raw($employee.".id as id,".
	                                                 $prefixed_user_details.".email as email, ".
	                                                 $employee.".is_active as is_active, ".
	                                                 $prefixed_user_details.".national_id as national_id, ".
	                                                 $prefixed_user_details.".gender as gender, ".
	                                                 $prefixed_employee_table.".user_role as role,".
	                                                 $prefixed_employee_table.".license_no as license_no,".
	                                                 $prefixed_employee_table.".employee_no as employee_no,".
	                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
	                                                          .$prefixed_user_trans_table.".last_name) as user_name"
	                                                 ))
	                                ->whereNull($employee.'.deleted_at')
	                                ->join($user_details,$employee.'.user_id','=',$user_details.'.id')
	                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
	                                ->where($employee.'.user_role','=','driver')
	                                ->where($user_trans_table.'.locale','=',$this->locale)
	                                ->where($employee.'.school_id','=',$this->school_id)
	                                ->whereBetween($employee.'.academic_year_id',[$start_id->id,$this->academic_year])
	                                ->orderBy($employee.'.created_at','DESC')->count();*/


	        /* Driver Count */
	        /* Levele Class */
            $levelClass = array();
	         $levelClass = $this->LevelClassModel
                          ->with('level_details')
                          ->where('school_id',$this->school_id)
                          ->whereBetween('academic_year_id',[$start_id->id,$this->academic_year])
                          ->groupBy('level_id')
                          ->get();
            if(!empty($levelClass)){
                $levelClassCount = count($levelClass);
            }                            

 			 /* Levele Class */

 			 /*Course Count */
 			 
 			$str                =  $this->CommonDataService->get_academic_year_less_than($this->academic_year);
	        $arr_academic_year  = explode(',', $str);   
	        $coursesCount       = $this->get_course_count($arr_academic_year);
 			 /*Course Count */


 			 /*Exam*/
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
	                                        ->where($exam_table.'.school_id','=',$this->school_id)
	                                        ->where($exam_table.'.academic_year_id','=',$this->academic_year)
	                                        ->orderBy($exam_table.'.created_at','DESC')->count();

 			 /*Exam*/

 			 /*Assessment Scale */
 			  $assessmentScaleCount = $this->AssessmentScaleModel->with('course_name')->where('school_id',$this->school_id)->count();
 			 /*Assessment Scale */

 			 /* Fee structure*/
            $feesStructure = array(); 			
	        $feesStructure 		= $this->FeesSchoolModel
                                          ->with('get_level')  
                                          ->where('school_id',$this->school_id)
                                          ->where('academic_year_id',$this->academic_year)
                                          ->groupBy('level_id')
                                          ->get();;
            if(!empty($feesStructure)){
                $feesStructureCount = count($feesStructure);
            }
 			 /* Fee structure*/

 			 /*Task Count*/
 			 $taskCount = $this->TaskModel
                                    ->with('get_supervisor')
                                    ->where('school_id',$this->school_id)
                                    ->where('academic_year_id',$this->academic_year)
                                    ->orderBy('id','DESC')
                                    ->count();
 			 /*Task Count*/

 			 /*Stock Count */
 			$stock_table                 = $this->StockReceivedModel->getTable();
	        $prefixed_stock_table        = DB::getTablePrefix().$this->StockReceivedModel->getTable();

	        $stockCount = DB::table($stock_table)
	                                ->select(DB::raw($prefixed_stock_table.".id as id,".
	                                                 $prefixed_stock_table.".product_id as product_id,".
	                                                 $prefixed_stock_table.".product_name as product_name,".
	                                                 $prefixed_stock_table.".total_price as total_price,".
	                                                 $prefixed_stock_table.".date_created as date_created,".
	                                                 $prefixed_stock_table.".price as price,".
	                                                 $prefixed_stock_table.".quantity as quantity,".
	                                                 $prefixed_stock_table.".available_stock as available_stock"
	                                                 )) 
	                                ->where($stock_table.'.school_id','=', $this->school_id)
	                                ->whereIn($stock_table.'.academic_year_id',$arr_academic_year)
	                                ->whereNull($stock_table.'.deleted_at')
	                                ->orderBy('id','DESC')->count();
	 		/*Stock Count */

	 		/*News */
 			$news             = $this->NewsModel->getTable();
	        $newsCount		  = DB::table($news)
	                                ->whereNull($news.'.deleted_at')
	                                ->where($news.'.school_id','=',$this->school_id)
	                                ->whereIn($news.'.academic_year_id',$arr_academic_year)
	                                ->orderBy($news.'.id','desc')->count();
	 		/*News */

	 		/*Club*/
	 		$clubCount = $this->ClubModel
                                    ->with('get_students')
                                    ->with('get_supervisor')
                                    ->where('school_id',$this->school_id)
                                    ->where('academic_year_id',$this->academic_year)
                                    ->orderBy('id','DESC')
                                    ->count();
	 		/*Club*/

	 		/*Canteen*/
	 		$canteen_item_table          = $this->CanteenProductsModel->getTable();
	        $prefixed_canteen_item_table = DB::getTablePrefix().$this->CanteenProductsModel->getTable();
	        $product_type_table          = $this->CanteenProductTypesModel->getTable();
	        $prefixed_product_type_table = DB::getTablePrefix().$this->CanteenProductTypesModel->getTable();

	        $countCanteenProducts = DB::table($canteen_item_table)
	                                ->join($product_type_table,$canteen_item_table.'.product_type','=',$product_type_table.'.id')
	                                ->where($canteen_item_table.'.school_id','=', $this->school_id)
	                                ->whereNull($canteen_item_table.'.deleted_at')
	                                ->count();
	 		/*Canteen*/

	 		/*Book Count*/

	        $library_content          = $this->LibraryContentModel->getTable();
	        $book_details             = $this->BookDetailsModel->getTable();
	        $bookCount = DB::table($book_details)
	                                ->join($library_content,$book_details.'.library_content_id','=',$library_content.'.id')
	                                ->where($library_content.'.school_id','=',$this->school_id)
	                                ->orderBy($book_details.'.created_at','DESC')->count();
	 		/*Book Count*/

	 		/*BUS Count*/
            
	 		$str_years                = $this->CommonDataService->get_academic_year_less_than($this->academic_year);

	 		$prefixed_academic_year                       = DB::getTablePrefix().$this->AcademicYearModel->getTable();
	        $prefixed_bus_details                         = DB::getTablePrefix().$this->BusModel->getTable();
	        $prefixed_user_details                        = DB::getTablePrefix().$this->UserTranslationModel->getTable();
	        $busCount = DB::table($prefixed_bus_details)
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
	                                ->orderBy($prefixed_bus_details.'.id','DESC')->count();
	 		/*BUS Count*/


	 		/*Suggestion Count*/
	 		 $suggestionCount  = $this->SuggestionModel
                                     ->with('get_user_details','get_category')
                                     ->where('school_id',$this->school_id)
                                     ->where('academic_year_id',$this->academic_year)
                                     ->where('user_id',$this->user_id)
                                     ->whereIn('status',['REQUESTED','APPROVED'])
                                     ->count();
	 		/*Suggestion Count*/

            if(in_array('admission_config.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'admission_config',
                                      'fa_icons'        => '<i class="fa fa-file-text"></i>',
                                      'tile_color'      => 'border-bottm-1',
                                      'module_title'    => translation('admission'),
                                      'module_sub_title'=> translation('look_at_new_joinings'),
                                      'total_count'     => $admissionCount]; 
            }


            if(in_array('professor.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'professor',
                                      'fa_icons'        => '<i class="fa fa-users"></i>',
                                      'tile_color'      => 'border-bottm-2',
                                      'module_title'    => translation('professor'),
                                      'module_sub_title'=> translation('get_all_professor'),
                                      'total_count'     => $professorCount]; 
            }

            
            if(in_array('employee.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'employee',
                                      'fa_icons'        => '<i class="fa fa-users"></i>',
                                      'tile_color'      => 'border-bottm-3',
                                      'module_title'    => translation('employee_staff'),
                                      'module_sub_title'=> translation('get_all_employees'),
                                      'total_count'     => $employeeCount]; 
            }

            if(in_array('parent.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'parent',
                                      'fa_icons'        => '<i class="fa fa-users"></i>',
                                      'tile_color'      => 'border-bottm-4',
                                      'module_title'    => translation('parent'),
                                      'module_sub_title'=> translation('get_all_parents'),
                                      'total_count'     => $parentCount];       
            } 

            if(in_array('student.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'student',
                                      'fa_icons'        => '<i class="fa fa-users"></i>',
                                      'tile_color'      => 'border-bottm-5',
                                      'module_title'    => translation('student'),
                                      'module_sub_title'=> translation('get_all_students'),
                                      'total_count'     => $studentCount];       
            } 


            /*if(in_array('driver.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'driver',
                                      'fa_icons'        => '<i class="fa fa-users"></i>',
                                      'tile_color'      => 'border-bottm-6',
                                      'module_title'    => translation('driver'),
                                      'module_sub_title'=> translation('get_all_drivers'),
                                      'total_count'     => $driverCount];       
            }*/

            if(in_array('calender.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'calendar',
                                      'fa_icons'        => '<i class="fa fa-calendar-check-o"></i>',
                                      'tile_color'      => 'border-bottm-7',
                                      'module_title'    => translation('calendar'),
                                      'module_sub_title'=> translation('get_calender'),
                                      'total_count'     => ''];       
            }

            if(in_array('timetable.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'timetable/summary',
                                      'fa_icons'        => '<i class="fa fa-calendar"></i>',
                                      'tile_color'      => 'border-bottm-8',
                                      'module_title'    => translation('timetable'),
                                      'module_sub_title'=> translation('check_timetable'),
                                      'total_count'     => ''];       
            }

            if(in_array('level_class.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'level_class',
                                      'fa_icons'        => '<i class="fa fa-calendar-check-o"></i>',
                                      'tile_color'      => 'border-bottm-9',
                                      'module_title'    => translation('level_and_classes'),
                                      'module_sub_title'=> translation('get_all_level_classes'),
                                      'total_count'     => $levelClassCount];       
            } 
 			
 			if(in_array('course.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'course',
                                      'fa_icons'        => '<i class="fa fa-file-text"></i>',
                                      'tile_color'      => 'border-bottm-10',
                                      'module_title'    => translation('course'),
                                      'module_sub_title'=> translation('get_all_courses'),
                                      'total_count'     => $coursesCount];       
            } 

            if(in_array('attendance.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'attendance/student',
                                      'fa_icons'        => '<img src="'.url('/').'/images/admin/6attendance-icon-dash.png" alt="" />',
                                      'tile_color'      => 'border-bottm-11',
                                      'module_title'    => translation('attendance'),
                                      'module_sub_title'=> translation('get_attendance'),
                                      'total_count'     => ''];       
            } 

            if(in_array('exam.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'exam',
                                      'fa_icons'          =>  '<img src="'.url('/').'/images/admin/10task-icon-dash.png" alt="" />',
                                      'tile_color'      => 'border-bottm-12',
                                      'module_title'    => translation('exam'),
                                      'module_sub_title'=> translation('check_exam_details'),
                                      'total_count'     => $examCount];       
            } 

            if(in_array('assessment_scale.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'assessment_scale',
                                      'fa_icons'          =>  '<img src="'.url('/').'/images/admin/11news-icon-dash.png" alt="" />',
                                      'tile_color'      => 'border-bottm-13',
                                      'module_title'    => translation('assessment_scale'),
                                      'module_sub_title'=> translation('check_assessment_scale_details'),
                                      'total_count'     => $assessmentScaleCount];       
            } 


            if(in_array('fees_structure.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'fees_structure',
                                      'fa_icons'          =>  '<img src="'.url('/').'/images/admin/12club-icon-dash.png" alt="" />',
                                      'tile_color'      => 'border-bottm-14',
                                      'module_title'    => translation('fees'),
                                      'module_sub_title'=> translation('manage_fees'),
                                      'total_count'     => $feesStructureCount];       
            } 

            if(in_array('task.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'task',
                                      'fa_icons'        => '<i class="fa fa-tasks"></i>',
                                      'tile_color'      => 'border-bottm-15',
                                      'module_title'    => translation('task'),
                                      'module_sub_title'=> translation('check_task'),
                                      'total_count'     => $taskCount];       
            } 

            if(in_array('stocks.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'stock',
                                      'fa_icons'        => '<i class="fa fa-database"></i>',
                                      'tile_color'      => 'border-bottm-16',
                                      'module_title'    => translation('stock'),
                                      'module_sub_title'=> translation('check_how_much_left'),
                                      'total_count'     => $stockCount];       
            } 

             if(in_array('news.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'news',
                                      'fa_icons'        => '<i class="fa fa-newspaper-o"></i>',
                                      'tile_color'      => 'border-bottm-17',
                                      'module_title'    => translation('news'),
                                      'module_sub_title'=> translation('latest_news_feeds'),
                                      'total_count'     => $newsCount];       
            }

            if(in_array('club.list',$arr_current_user_access)){
              $arr_final_tile[] =   [   'module_url'      =>  $URL.'club',
                                        'fa_icons'        => '<i class="fa fa-cc-diners-club"></i>',
                                        'tile_color'      => 'border-bottm-18',
                                        'module_title'    => translation('club'),
                                        'module_sub_title'=> translation('check_out_clubs'),
                                        'total_count'     => $clubCount];       
                                   
            }
    		
    		if(in_array('canteen.list',$arr_current_user_access)){ 
                $arr_final_tile[] = [ 'module_url'      => $URL.'canteen_products',
                                      'fa_icons'        => '<i class="fa fa-cutlery"></i>',
                                      'tile_color'      => 'border-bottm-19',
                                      'module_title'    => translation('canteen_products'),
                                      'module_sub_title'=> translation('look_whats_there_in_canteen'),
                                      'total_count'     => $countCanteenProducts];       
            }  

            if(in_array('library.list',$arr_current_user_access)){ 
                $arr_final_tile[] = [ 'module_url'      => $URL.'library/manage_library_contents',
                                      'fa_icons'        => '<i class="fa fa-book"></i>',
                                      'tile_color'      => 'border-bottm-1',
                                      'module_title'    => translation('library'),
                                      'module_sub_title'=> translation('check_library'),
                                      'total_count'     => $bookCount];       
            } 

 
            if(in_array('transport_bus.list',$arr_current_user_access)){ 
                $arr_final_tile[] = [ 'module_url'      => $URL.'transport_bus',
                                      'fa_icons'        => '<i class="fa fa-truck"></i>',
                                      'tile_color'      => 'border-bottm-2',
                                      'module_title'    => translation('transport_bus'),
                                      'module_sub_title'=> translation('check_bus_for_transportation'),
                                      'total_count'     => $busCount];       
            }  
 			
 			if(in_array('suggestions.list',$arr_current_user_access)){
              $arr_final_tile[] = [     'module_url'      => $URL.'suggestions/employee_suggestions/manage',
                                        'fa_icons'        => '<i class="fa fa-dropbox"></i>',
                                        'tile_color'      => 'border-bottm-3',
                                        'module_title'    => translation('suggestions'),
                                        'module_sub_title'=> translation('manage_suggestion'),
                                        'total_count'     => $suggestionCount];       
                                   
            }  
            
             
    
        }       
        return  $arr_final_tile;                          
    }

    function get_course_count($arr_academic_year){
            $coursesCount  =0;
            $exam_type_details                  = $this->CourseModel->getTable();
            $prefixed_exam_type_details         = DB::getTablePrefix().$this->CourseModel->getTable();

            $school_exam_type_trans_details            = $this->SchoolCourseModel->getTable();
            $prefixed_school_exam_type_details   = DB::getTablePrefix().$this->SchoolCourseModel->getTable();

            $exam_type_trans_details            = $this->CourseTranslationModel->getTable();
            $prefixed_exam_type_trans_details   = DB::getTablePrefix().$this->CourseTranslationModel->getTable();

            $coursesCount = DB::table($school_exam_type_trans_details)
                                    ->select(DB::raw($prefixed_school_exam_type_details.".id as id,".
                                                     $prefixed_school_exam_type_details.".is_active as status,".
                                                     $prefixed_school_exam_type_details.".school_id,".
                                                     $prefixed_school_exam_type_details.".course_id,".
                                                     $prefixed_school_exam_type_details.".school_level_id,".
                                                     $prefixed_school_exam_type_details.".coefficient,".
                                                     $prefixed_exam_type_trans_details.".course_name"))
                                    ->join($exam_type_trans_details,$school_exam_type_trans_details.'.course_id','=',$exam_type_trans_details.'.course_id')
                                    ->where($exam_type_trans_details.'.locale','=',Session::get('locale'))
                                    ->whereNull($prefixed_school_exam_type_details.'.deleted_at')
                                    ->where($prefixed_school_exam_type_details.'.school_id','=',$this->school_id)
                                    ->whereIn($prefixed_school_exam_type_details.'.academic_year_id',$arr_academic_year)        
                                    ->orderBy($prefixed_school_exam_type_details.'.created_at','DESC')->count();
        return $coursesCount;
    }
    
}