<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\HomeworkModel;
use App\Models\HomeworkStudentModel;
use App\Models\LevelClassModel;
use App\Models\LevelTranslationModel;
use App\Models\ClassTranslationModel;
use App\Models\CourseTranslationModel;
use App\Models\StudentModel;

use App\Common\Services\CommonDataService;

use Session;
use Sentinel;
use DB;
use Datatables;
use Validator;
use Flash;
use PDF;
class HomeworkController extends Controller
{
    public function __construct(HomeworkModel $homework_model,
                                LevelClassModel $level_class,
                                LevelTranslationModel $level_translation,
                                ClassTranslationModel $class_translation,
                                CourseTranslationModel $course_translation,
                                CommonDataService $common_data_service,
                                HomeworkStudentModel $homework_student,
                                StudentModel $student)
    {
    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.student_panel_slug')).'/homework';
        $this->module_title                 = translation('homework');
 
        $this->module_view_folder           = "student.homework";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-book';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->level_class_id                = Session::get('level_class_id');

    	$this->HomeworkModel       = $homework_model;
        $this->LevelClassModel    = $level_class;
        $this->LevelTranslationModel = $level_translation;
        $this->ClassTranslationModel = $class_translation;
        $this->CourseTranslationModel = $course_translation;
        $this->CommonDataService      = $common_data_service;
        $this->HomeworkStudentModel   = $homework_student;
        $this->StudentModel           = $student;

    	$this->arr_view_data['page_title']      = translation('homework');
    	$this->arr_view_data['module_url_path'] = $this->module_url_path;
    	$this->arr_view_data['theme_color']     = $this->theme_color;
    	$this->arr_view_data['module_icon']     = $this->module_icon;
    	$this->arr_view_data['edit_icon']       = $this->edit_icon;
    	$this->arr_view_data['create_icon']     = $this->create_icon;

    	$obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $student = $this->StudentModel->where('user_id',$obj_data->id)->first();

            if(empty($student))
            {
                return url(config('app.project.student_panel_slug'));
            }
            $this->student_id = $student->id ;
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;


        }
        else
        {
            return url(config('app.project.student_panel_slug'));
        }
    }
    
    /*
    | index() : redirecting to homework listing  
    | Auther        : Pooja K  
    | Date          : 6 Jun 2018
    */
    public function index()
    {
    	$this->arr_view_data['module_title']    = translation("manage")." ".$this->module_title;
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*
    | get_records() : homework listing using ajax 
    | Auther        : Pooja K  
    | Date          : 6 Jun 2018
    */ 
    public function get_records(Request $request)
    {
        $obj_custom = $this->get_homework_records($request);

        $role = Sentinel::findRoleBySlug(config('app.project.role_slug.professor_role_slug'));
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;

        $json_result  = Datatables::of($obj_custom);

        $json_result = $json_result->blacklist(['id']);                    

  
        $json_result =  $json_result->editColumn('due_date',function($data)
                        {
                            return getDateFormat($data->due_date);
                        })
                        ->editColumn('added_date',function($data)
                        {
                            return getDateFormat($data->added_date);
                        })
                        ->editColumn('build_status',function($data)
                        {
                            if($data->status == 'COMPLETED' && $data->status_changed_by=="PROFESSOR"){
                                $status =' <div style="margin: 0 !important;width: 200px" class="form-group"><input type="text" class="form-control" value="'.translation('completed').'" disabled style="width:" /></div>';
                            }
                            else{
                                $status = ' <div style="margin: 0 !important;width: 200px" class="form-group"> 
                                <select class="form-control" onchange="changeStatus('.$data->homework_student_id.')" id="status">
                                          <option value="PENDING"';
                                if($data->status=='PENDING'){
                                    $status .= 'selected';
                                }

                                $status .= '>'.translation('pending').'</option><option value="COMPLETED"';
                                if($data->status=='COMPLETED')
                                {
                                    $status .= 'selected';
                                }
                                $status .= '>'.translation('completed').'</option><option value="REJECTED"';
                                if($data->status=='REJECTED')
                                {
                                    $status .= 'selected';
                                }
                                $status .= ' disabled>'.translation('rejected').'</option></select></div>';
                            }
                                
                            return $status;
                        })
                        ->editColumn('build_reason',function($data)
                        {   
                            if($data->status=='REJECTED')
                            {
                                $status = '<a style="width:auto" id="openModel" onclick="openReason(\''.$data->homework_student_id.'\')">'.translation('reason').'</a><a style="display:none" id="reason_'.$data->homework_student_id.'" >'.$data->rejection_reason.'</a>';
                            }
                            else{
                                $status = '-';
                            }
                            return $status;
                        })           
                        ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);
    }

    /*
    | get_homework_records() : homework listing using ajax 
    | Auther        : Pooja K  
    | Date          : 6 Jun 2018
    */
    public function get_homework_records(Request $request,$fun_type='')
    {
    	$user_id = $this->user_id;
        $locale = '';

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
                      
  		$homework_table                = $this->HomeworkModel->getTable();
        $homework_student_table        = $this->HomeworkStudentModel->getTable();
  		$course_table                  = $this->CourseTranslationModel->getTable();

        $obj_custom = DB::table($homework_table)
                        ->select(DB::raw(   

                                            $homework_table.".id as homework_id,".
                                            $homework_student_table.".id as homework_student_id,".
                                            $homework_student_table.".rejection_reason as rejection_reason,".
                                            $homework_table.".description,".
                                            $homework_table.".due_date,".
                                            $homework_table.".added_date,".
                                            $homework_student_table.".status,".
                                            $course_table.".course_name,".
                                            $homework_student_table.".status_changed_by"
                                            
                                        ))
                                        ->leftJoin($homework_student_table,$homework_table.'.id',' = ',$homework_student_table.'.homework_id')
                        				->leftJoin($course_table,$homework_table.'.course_id',' = ',$course_table.'.course_id')
                                        ->where($course_table.'.locale','=',$locale)
                                        ->where($homework_table.'.school_id',$this->school_id)
                                        ->where($homework_table.'.academic_year_id',$this->academic_year)
                                        ->where($homework_table.'.level_class_id',$this->level_class_id)
                                        ->where($homework_student_table.'.student_id',$this->student_id)
                                        ->whereNull($homework_table.'.deleted_at')
                                        ->orderBy($homework_table.'.created_at','DESC');
                 
        if($fun_type=='export'){
            $search_term = $request->input('search');
        }else{
            $search = $request->input('search');
            $search_term = $search['value'];
        }
        if($request->has('search') && $search_term!="")
        {

            $obj_custom = $obj_custom
                                     ->whereRaw("((".$homework_table.".description LIKE '%".$search_term."%')")
                                     ->orWhereRaw("(".$course_table.".course_name LIKE '%".$search_term."%') )");       

        }

        if($fun_type=="export"){
            return $obj_custom->get();
        }else{

            return $obj_custom;
        }

        
    }

    /*
    | view() : view homework
    | Auther        : Pooja K  
    | Date          : 6 Jun 2018
    */ 
    public function view(Request $request,$enc_id)
    {
        $id = base64_decode($enc_id);

        $student = $this->StudentModel->where('user_id',$id)->first();

        if(empty($student))
        {
        	return redirect()->back();
        }

        $obj_homework = $this->HomeworkModel
                                        ->with(['get_course','homework_details'=>function($q)use($student){
                                            $q->where('student_id',$student->id);
                                        },'homework_added_by'])
                                        ->where('id',$id)
                                        ->first();

        if($obj_homework)
        {
            $arr_data = $obj_homework->toArray();
        }
        else
        {
        	return redirect()->back();
        }
        $this->arr_view_data['arr_data'] = $arr_data;
        
        $this->arr_view_data['module_title']    = translation("view")." ".$this->module_title;
        return view($this->module_view_folder.'.view', $this->arr_view_data);
    }

    /*
    | view() : change status of homework
    | Auther        : Pooja K  
    | Date          : 6 Jun 2018
    */ 
    public function change_status(Request $request)
    {
        $status = $request->input('status');
        $id = $request->input('id');
        
        
            if(!($request->has('reason')))
            {
                $this->HomeworkStudentModel->where('id',$id)->update(['status'=>$status,'status_changed_by'=>'STUDENT']);    
            }
            else
            {
                $this->HomeworkStudentModel->where('id',$id)->update(['status'=>$status,"rejection_reason"=>$request->reason,'status_changed_by'=>'STUDENT']);    
            }
    }   


    /*
    | export() : Export List
    | Auther  : Padmashri
    | Date    : 14-12-2018
    */
    public function export(Request $request)
    {       
            $file_type = config('app.project.export_file_formate');

            $obj_data = $this->get_homework_records($request,'export');
            if(sizeof($obj_data)<=0){
                Flash::error(translation("no_records_found_to_export"));
                return redirect()->back();
            }
            if(sizeof($obj_data)>500 && $request->file_format == $file_type){
                Flash::error(translation("too_many_records_to_export"));
                return redirect()->back();
            }
            if($request->file_format == $file_type){
                \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel) use($obj_data) 
                    {
                        $excel->sheet(ucwords($this->module_title), function($sheet) use($obj_data) 
                        {
                            $arr_fields['id']               = translation('sr_no');;
                            $arr_fields['course']           = translation('course');
                            $arr_fields['homework_details'] = translation('homework_details');
                            $arr_fields['added_date']       = translation('added_date');
                            $arr_fields['due_date']         = translation('due_date');
                            $arr_fields['status']           = translation('status');
                            $arr_fields['rejection_reason'] = translation('rejection_reason');
                            
                            
                            $sheet->row(2, ['',ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                            $sheet->row(4, $arr_fields);

                            // To set Colomn head
                            $j = 'A'; $k = '4';
                            $totalHead = 6;
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
                                foreach($obj_data as $key => $result)
                                {
                                    $status = '';

                                    if($result->status == 'COMPLETED' && $result->status_changed_by=="PROFESSOR"){
                                        $status = translation('completed');
                                    }
                                    else
                                    {

                                        if($result->status=='PENDING')
                                        {
                                            $status = translation('pending');
                                        }
                                        else  if($result->status=='COMPLETED')
                                        {
                                            $status = translation('completed');
                                        }
                                        else  if($result->status=='REJECTED')
                                        {
                                            $status = translation('rejected');
                                        }
                
                                    }

                                    $reason = '-';
                                    if($result->rejection_reason!='' && isset($result->rejection_reason))
                                    {
                                        $reason = $result->rejection_reason;
                                    }

                                    $arr_tmp[$key]['id']               = intval($key+1);
                                    $arr_tmp[$key]['course']           = $result->course_name;
                                    $arr_tmp[$key]['homework_details'] = ucwords($result->description);
                                    $arr_tmp[$key]['added_date']       = getDateFormat($result->added_date);
                                    $arr_tmp[$key]['due_date']         = getDateFormat($result->due_date);;
                                    $arr_tmp[$key]['status']           = $status;
                                    $arr_tmp[$key]['rejection_reason'] = $reason;
                                    
                                }
                                   $sheet->rows($arr_tmp);
                            }
                        });
                    })->export($file_type);     
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
