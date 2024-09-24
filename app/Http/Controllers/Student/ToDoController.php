<?php


namespace App\Http\Controllers\Student;


use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\ToDoModel;
use App\Models\StudentModel;
use App\Models\LevelClassModel;
use App\Common\Services\CommonDataService;


use PDF;
use DB;
use Flash;
use Session;
use Sentinel;
use Validator;
use Datatables;

class ToDoController extends Controller
{
	public function __construct(TodoModel $todo,LevelClassModel  $level_class,CommonDataService $common_data_service,StudentModel $student){
		$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.student_panel_slug')).'/todo';
        $this->module_title                 = translation('todo');
        $this->module_view_folder           = "student.todo";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-list';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-list';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->level_class_id               = Session::get('level_class_id');

        $this->ToDoModel 	                = $todo;
        $this->BaseMdel	 	                = $this->ToDoModel;
        $this->LevelClassModel              = $level_class;
        $this->CommonDataService            = $common_data_service;
        $this->StudentModel		            = $student;


    	$this->arr_view_data['page_title']      = translation('todo');
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
                return redirect()->back();
            }
            
            $this->student_id 	  = $student->id;
            $this->level_class_id = $student->level_class_id;
            $this->user_id    	  = $obj_data->id;
            $this->first_name	  = $obj_data->first_name;
            $this->last_name  	  = $obj_data->last_name;
            $this->email      	  = $obj_data->email;


        }
        else
        {
            return redirect()->back();
        }
    }

     /*
    | index()  : index() list the todo
    | Auther  : Padmashri
    | Date    : 7-07-2018
    */
	public function index(){

		$arr_data = $obj_data = array();
		$obj_data = $this->ToDoModel->where('student_id',$this->user_id)->where('academic_year_id',$this->academic_year)->where('school_id',$this->school_id)->orderBy('id','desc')->paginate(10);
		if(!empty($obj_data) ){
			$arr_data = $obj_data->toArray();
			$page_link     = $obj_data->links();  
		}
		
        if(empty($arr_data['data'])){
            Flash::error(translation("no_record_found"));
        }
		$this->arr_view_data['arr_data']	     = $arr_data;
		$this->arr_view_data['pagination_links'] = $page_link;
		$this->arr_view_data['module_title']     = $this->module_title;
        $this->arr_view_data['page_title']    	 = $this->module_title;
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }


     /*
    | delete_todo()  : delete_todo() delete the todo
    | Auther  : Padmashri
    | Date    : 7-07-2018
    */
    public function delete_todo(Request $request){
    	
    	$id = $request->input('todoId');
    	$status = 'error';
    	if($id){
    		$isExists = $this->ToDoModel->where('id',$id)->first();
    		if(!empty($isExists)){
    			 $res = $this->ToDoModel->where('id',$id)->delete();
    			 if($res){
    			 		$statu = 'done';
    			 }
    		}else{
    			$status = 'InvalidTodo';
    		}
    	}
		return $statu;	
    }


     /*
    | mark_as_read_todo()  : mark_as_read_todo() delete the todo
    | Auther  : Padmashri
    | Date    : 7-07-2018
    */
    public function mark_as_read_todo(Request $request){
        
        $id = $request->input('todoId');
        $status = 'error####';
        if($id){
            $isExists = $this->ToDoModel->where('id',$id)->first();
            if(!empty($isExists)){
                $arr_data  =     $isExists->toArray();
                $arr_update = [];
                if($arr_data['status'] == 1){
                    $arr_update['status'] = 0;
                }else{
                    $arr_update['status'] = 1;
                }
                $res = $this->ToDoModel->where('id',$id)->update($arr_update);
                if($res){
                        $status = 'done####'.$arr_update['status'];
                 }else{
                        $status = 'oopsSomething####';
                 }
            }else{
                $status = 'InvalidTodo####';
            }
        }
        return $status;  
    }

     /*
    | add_todo()  : add_todo() add todo
    | Auther  : Padmashri
    | Date    : 7-07-2018
    */
    public function store(Request $request)
    {   
        $status = 'fail';
        $errors = $customError = '';
        $customError =translation('something_went_wrong_please_try_again_later');
        if(isset($_POST['isSubmit']))
        {    
             
            $rules = array(
                    'todo_description'           => 'required|max:1000',
                    );
            $messages = array(
                    'todo_description.required'       => translation('please_enter_todo'),
                    'add_description.max'             => translation('todo_should_not_be_more_than_1000_characters')
                   
                    );
            
            $validator = Validator::make($request->all(), $rules, $messages);

            if($validator->fails())
            {
                return json_encode(['errors'=> $validator->errors()->getMessages(),'code'=>422,'status'=>'fail']);
            }   
            else
            {   
                 $insertArr = array(
                                    'school_id'            =>  $this->school_id,
                                    'level_class_id'       =>  trim($this->level_class_id),
                                    'student_id'           =>  trim($this->user_id),
                                    'academic_year_id'     =>  trim($this->academic_year),
                                    'status'               =>  0,
                                    'todo_description'     =>  trim($request->input('todo_description')),
                                    
                                 );
                    $result = $this->ToDoModel->create($insertArr);
                    if($result)
                    {
                            $status      = 'success';
                            $customError = $this->module_title." ".translation("created_successfully");;
                     }else{
                            $customError = "something_went_wrong_while_creating ".$this->module_title;
                            $status      = "fail";
                    }
                $resp = array('status' => $status,'errors'=>$errors,'customError'=> $customError);
                return response()->json($resp);
            }

            
            
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

            $obj_data = $this->ToDoModel->where('student_id',$this->user_id)->where('academic_year_id',$this->academic_year)->where('school_id',$this->school_id)->orderBy('id','desc')->get();
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
                            $arr_fields['id']           = translation('sr_no');;
                            $arr_fields['todo']         = translation('todo');
                            $arr_fields['date']         = translation('date');
                            $arr_fields['status']       = translation('status');
                                
                            
                            
                            $sheet->row(2, ['',ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                            $sheet->row(4, $arr_fields);

                            // To set Colomn head
                            $j = 'A'; $k = '4';
                            $totalHead = 4;
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
                            $sheet->setColumnFormat([
                                'D' => "#",
                            ]);


                            
                            if(sizeof($obj_data)>0) 
                            {
                                
                                $arr_tmp = [];
                                foreach($obj_data as $key => $result)
                                {   

                                    $status = '-';
                                    if($result->status== 1)
                                    {
                                        $status = translation('completed'); 
                                    }
                                    elseif( $result->status ==  0)
                                    {
                                        $status = translation('pending'); 
                                    }

                                    $created_at = isset($result->created_at) && sizeof($result->created_at)>0?date("Y-m-d",strtotime($result->created_at)):'-';
                                    $arr_tmp[$key]['id']           = intval($key+1);
                                    $arr_tmp[$key]['todo']         = $result->todo_description;
                                    $arr_tmp[$key]['created_date'] = $created_at;
                                    $arr_tmp[$key]['status']       = $status;
                                    
                                    
                                    
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
