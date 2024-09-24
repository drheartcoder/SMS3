<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\ExamModel;
use App\Models\ResultModel;
use App\Models\StudentModel;
use App\Models\ExamPeriodTranslationModel;
use App\Models\ExamTypeTranslationModel;
use App\Models\CourseTranslationModel;
use App\Models\LevelTranslationModel;
use App\Models\LevelClassModel;
use App\Models\ClassTranslationModel;
use App\Models\AssessmentScaleModel;

use App\Common\Services\CommonDataService;

use App\Common\Traits\MultiActionTrait;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;
use PDF;
class ExamController extends Controller
{
    public function __construct(
    								CommonDataService $common_data_service
    							)
    {

    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.parent_panel_slug')).'/exam';
        $this->module_title                 = translation("exam");
        $this->BaseModel                    = new ExamModel();
        
        $this->module_view_folder           = "parent.exam";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-exam';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->CommonDataService            = $common_data_service;
        $this->academic_year				= Session::get('academic_year');
        $this->level_class_id               = Session::get('level_class_id');
        $this->student_id                   = Session::get('student_id');
        $this->kid_id                       = Session::has('kid_id')?Session::get('kid_id'):0;
        
        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
        	$this->user_id    = $obj_data->id;
        	$this->first_name = $obj_data->first_name;
        	$this->last_name  = $obj_data->last_name;
        	$this->email      = $obj_data->email;
        }

		$this->ExamModel                = new ExamModel();
        $this->ExamPeriodTranslationModel = new ExamPeriodTranslationModel();
        $this->ExamTypeTranslationModel   = new ExamTypeTranslationModel();
        $this->CourseTranslationModel     = new CourseTranslationModel();
        $this->LevelTranslationModel      = new LevelTranslationModel();
        $this->ClassTranslationModel      = new ClassTranslationModel();
        $this->LevelClassModel            = new LevelClassModel();
        $this->AssessmentScaleModel       = new AssessmentScaleModel();
        $this->ResultModel                = new ResultModel();
        $this->StudentModel               = new StudentModel();

   		$this->arr_view_data['page_title']      = translation('exam');
   		$this->arr_view_data['module_title']    = translation('exam');
   		$this->arr_view_data['module_icon']     = 'fa fa-book';
   		$this->arr_view_data['module_url_path'] = $this->module_url_path;
   		$this->arr_view_data['theme_color']     = $this->theme_color;
   		$this->arr_view_data['create_icon']     = 'fa fa-plus-circle';
   		$this->arr_view_data['edit_icon']       = 'fa fa-edit-circle';

   		/*literals*/	
    	$this->str_module_title    = 'module_title';
    	$this->str_module_url_path = 'module_url_path';

    }

    /*
    | index() 		: Redirect to exam list 
    | Auther        : Pooja K 
    | Date          : 29-05-2018
    */ 
    public function index()
    {	

        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['page_title']      = str_plural(translation("manage")." ".$this->module_title);

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*
    | get_records() : exam listing using ajax 
    | Auther        : Pooja K  
    | Date          : 29-05-2018
    */ 
    public function get_records(Request $request)
    {
        $date = Date('Y-m-d');
        $obj_custom = $this->get_exam_records($request);

        $role = Sentinel::findRoleBySlug(config('app.project.role_slug.parent_role_slug'));
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;

        $json_result  = Datatables::of($obj_custom);

        $json_result = $json_result->blacklist(['id']);                    

  
        $json_result =  $json_result
                        ->editColumn('result',function($data)use($date)
                        {
                            $marks = '-';
                            if($data->result!=''){
                                $arr_result= json_decode($data->result,true);

                                if(array_key_exists($this->student_id, $arr_result) && $data->exam_date<$date)  {
                                    $marks = $arr_result[$this->student_id];
                                }    
                            }
                            
                            return $marks;
                        })
                        ->editColumn('exam_date',function($data)
                        {
                            return getDateFormat($data->exam_date) ;
                        })
                        ->editColumn('exam_time',function($data)
                        {
                            return $data->exam_start_time.' - '.$data->exam_end_time ;
                        })
                        ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                        {
                            $build_view_action = '';
                         
                            $view_href =  $this->module_url_path.'/view/'.base64_encode($data->exam_id);
                            $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>'; 

                            return $build_view_action.'&nbsp;';  
                        })
                        ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);
    }

    /*
    | get_exam_records() : Exam listing using ajax 
    | Auther        : Pooja K  
    | Date          : 29-05-2018
    */
    public function get_exam_records(Request $request,$fun_type='')
    {
        $school_id     = $this->school_id;
        $locale = '';

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
                      
  		$exam_table                    = $this->ExamModel->getTable();
        $exam_period_translation_table = $this->ExamPeriodTranslationModel->getTable();
        $exam_type_translationtable    = $this->ExamTypeTranslationModel->getTable();
        $level_table                   = $this->LevelTranslationModel->getTable();  
        $class_table                   = $this->ClassTranslationModel->getTable();
        $course_table                  = $this->CourseTranslationModel->getTable();
        $assessment_scale              = $this->AssessmentScaleModel->getTable();   
        $result                        = $this->ResultModel->getTable();   

        $obj_custom = DB::table($exam_table)
                        ->select(DB::raw(   

                                            $exam_table.".id as exam_id,".
                                            $exam_table.".exam_no,".
                                            $exam_table.".exam_start_time,".
                                            $exam_table.".exam_end_time,".
                                            $exam_table.".exam_date,".
                                            $assessment_scale.".scale,".
                                            $exam_period_translation_table.".exam_name,".
                                            $exam_type_translationtable.".exam_type,".
                                            $course_table.".course_name,".
                                            $result.".result,".
                                            $exam_table.".status"
                                        ))
                                        ->leftJoin($exam_period_translation_table,$exam_table.'.exam_period_id',' = ',$exam_period_translation_table.'.exam_id')
                                        ->leftJoin($exam_type_translationtable,$exam_table.'.exam_type_id',' = ',$exam_type_translationtable.'.exam_type_id')
                                        ->leftJoin($course_table,$exam_table.'.course_id',' = ',$course_table.'.course_id')
                                        ->leftJoin($assessment_scale,$exam_table.'.assessment_scale_id',' = ',$assessment_scale.'.id')
                                        ->leftJoin($result,$exam_table.'.id',' = ',$result.'.exam_id')
                                        ->where($exam_period_translation_table.'.locale','=',$locale)
                                        ->where($exam_type_translationtable.'.locale','=',$locale)
                                        ->where($course_table.'.locale','=',$locale)
                                        ->whereNull($exam_table.'.deleted_at')
                                        ->where($exam_table.'.school_id','=',$school_id)
                                        ->where($exam_table.'.status','=','APPROVED')
                                        ->where($exam_table.'.level_class_id','=',$this->level_class_id)
                                        ->orderBy($exam_table.'.created_at','DESC');

        if($fun_type=='export'){
            $search_term = $request->input('search');
        }else{
            $search = $request->input('search');
            $search_term = $search['value'];
        }

        if($request->has('search') && $search_term!="")
        {
            $obj_custom = $obj_custom
                                     ->whereRaw("( (".$exam_period_translation_table.".exam_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$exam_type_translationtable.".exam_type LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$exam_table.".exam_no LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$assessment_scale.".scale LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$course_table.".course_name LIKE '%".$search_term."%') )");
                                     
        }
        if($fun_type=="export"){
            return $obj_custom->get();
        }else{

            return $obj_custom;
        }
    }
    /*
    | view() : view exam
    | Auther        : Pooja K  
    | Date          : 29-05-2018
    */
    public function view($enc_id=FALSE)
    {
        if($enc_id)
        {
            $id = base64_decode($enc_id);    
        }
        else
        {
            return redirect()->back();
        }

        $arr_exam = [];
        $marks='-';
        $obj_exam = $this->ExamModel
                                ->with('get_result')   
                                ->with(['get_level_class'=>function($q){
                                    $q->with('level_details');
                                    $q->with('class_details');
                                },'get_exam_period','get_exam_type','get_assessment','get_supervisor','get_course','exam_added_by','get_academic_year',
                                  'room_assignment'=>function($q){
                                    $q->with('get_room_management');
                                  }])
                                ->where('id',$id)->first();

        if(!empty($obj_exam))
        {
            $arr_exam = $obj_exam->toArray();
            $arr_result= json_decode($arr_exam['get_result']['result'],true);

            if($arr_result && array_key_exists($this->student_id, $arr_result) )  {
                $marks = $arr_result[$this->student_id];

            } 
        }

        $this->arr_view_data[$this->str_module_title] = translation("view")." ".$this->module_title;
        $this->arr_view_data['arr_data'] = $arr_exam;
        $this->arr_view_data['marks'] = $marks;

        return view($this->module_view_folder.'.view', $this->arr_view_data);
    }

     /*
    | export() : Export List
    | Auther  : Padmashri
    | Date    : 15-12-2018
    */
    public function export(Request $request)
    {       
            $file_type = config('app.project.export_file_formate');


            $sheetTitlePDF = $sheetTitle = '';
             
            $student         = StudentModel::where('school_id',$this->school_id)
                                                  ->with('get_user_details')
                                                  ->where('is_active',1)
                                                  ->where('has_left',0)
                                                  ->where('user_id',$this->kid_id)
                                                  ->first();
            if(!empty($student))
            {
                $sheetTitle =$this->module_title.'-'.date('d-m-Y').'-'.uniqid(). ' ( '.ucfirst($student->get_user_details->first_name)." ".ucfirst($student->get_user_details->last_name).")";  
                $sheetTitlePDF = $this->module_title.'-'.date('d-m-Y').' ( '.ucfirst($student->get_user_details->first_name)." ".ucfirst($student->get_user_details->last_name).")";  
            }
            else
            {
                $sheetTitlePDF = $sheetTitle = ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid();    
            }

            $obj_data = $this->get_exam_records($request,'export');
            if(sizeof($obj_data)<=0){
                Flash::error(translation("no_records_found_to_export"));
                return redirect()->back();
            }
            if(sizeof($obj_data)>500 && $request->file_format == $file_type){
                Flash::error(translation("too_many_records_to_export"));
                return redirect()->back();
            }
            if($request->file_format == $file_type){
                \Excel::create($sheetTitle, function($excel) use($obj_data,$sheetTitlePDF,$sheetTitle) 
                    {
                        $excel->sheet($sheetTitle, function($sheet) use($obj_data,$sheetTitlePDF) 
                        {
                            $arr_fields['id']               = translation('sr_no');;
                            $arr_fields['exam_number']      = translation('exam_number');
                            $arr_fields['exam_period']      = translation('exam_period');
                            $arr_fields['exam_type']        = translation('exam_type');
                            $arr_fields['course']           = translation('course');
                            $arr_fields['exam_date']        = translation('exam_date');
                            $arr_fields['exam_time']        = translation('exam_time');
                            $arr_fields['assessment_scale'] = translation('assessment_scale');
                            $arr_fields['result']           = translation('result');
                            
                            
                            $sheet->row(2, ['',$sheetTitlePDF,'','','']);
                            $sheet->row(4, $arr_fields);

                            // To set Colomn head
                            $j = 'A'; $k = '4';
                            $totalHead = 9;
                            for($i=0; $i<=$totalHead;$i++)
                            {
                                $sheet->cell($j.$k, function($cells) {
                                    $cells->setBackground('#495b79');
                                    $cells->setFontWeight('bold');
                                    $cells->setAlignment('center');
                                    $cells->setFontColor('#ffffff');
                                });
                                $j++;
                            }


                            if(sizeof($obj_data)>0) 
                            {
                                
                                $arr_tmp = [];
                                $date = Date('Y-m-d');
                                foreach($obj_data as $key => $result)
                                {
                                   
                                    $marks = '-';
                                    if($result->result!=''){
                                        $arr_result= json_decode($result->result,true);

                                        if(array_key_exists($this->student_id, $arr_result) && $result->exam_date<$date)  {
                                            $marks = $arr_result[$this->student_id];
                                        }    
                                    }
                            
                            

                                    $arr_tmp[$key]['id']               = intval($key+1);
                                    $arr_tmp[$key]['exam_number']      = $result->exam_no;
                                    $arr_tmp[$key]['exam_period']      = $result->exam_name;
                                    $arr_tmp[$key]['exam_type']        = $result->exam_type;
                                    $arr_tmp[$key]['course']           = $result->course_name;
                                    $arr_tmp[$key]['exam_date']        = getDateFormat($result->exam_date) ;
                                    $arr_tmp[$key]['exam_time']        = $result->exam_start_time.' - '.$result->exam_end_time ;
                                    $arr_tmp[$key]['assessment_scale'] = $result->scale;
                                    $arr_tmp[$key]['result']           = $marks;
                                }
                                   $sheet->rows($arr_tmp);
                            }
                        });
                    })->export($file_type);     
            }
            
            if($request->file_format == 'pdf')
            {
     
                $this->arr_view_data['arr_data']      = $obj_data;
                $this->arr_view_data['sheetTitle']      = $sheetTitlePDF;
                $this->arr_view_data['student_id']      = $this->kid_id;
                $this->arr_view_data['date']      = date('Y-m-d');
                $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
                return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
            }
    }
}
