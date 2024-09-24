<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Common\Services\CommonDataService;
use App\Models\SchoolCourseModel;
use App\Models\CourseMaterialDetailsModel;
use App\Models\CourseMaterialModel;

use App\Models\LevelTranslationModel;
use App\Models\LevelClassModel;
use App\Models\ClassTranslationModel;
use App\Models\CourseTranslationModel;
use App\Common\Traits\MultiActionTrait;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;

class CourseMaterialController extends Controller
{
   
    use MultiActionTrait;
    public function __construct(CommonDataService $common_data_service,
                                SchoolCourseModel $school_course,
                                CourseMaterialDetailsModel $course_material_detail_model,
                                CourseMaterialModel $course_material_model,
                                LevelClassModel $LevelClassModel,
                                CourseTranslationModel $CourseTranslationModel,
                                LevelTranslationModel $LevelTranslationModel,
                                ClassTranslationModel $ClassTranslationModel)
    {
        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.student_panel_slug')).'/course_material';
        $this->module_title                 = translation('course_material');
 
        $this->module_view_folder           = "student.course_material";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-book';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->level_class_id                = Session::get('level_class_id');

        $this->CourseMaterialModel          = $course_material_model;
        $this->BaseModel                    = $this->CourseMaterialModel;
        $this->CommonDataService            = $common_data_service;
        $this->SchoolCourseModel            = $school_course;
        $this->CourseMaterialDetailsModel    = $course_material_detail_model;
        
        $this->CourseTranslationModel     = $CourseTranslationModel;
        $this->LevelTranslationModel      = $LevelTranslationModel;
        $this->ClassTranslationModel      = $ClassTranslationModel;
        $this->LevelClassModel            = $LevelClassModel;

        $this->arr_view_data['page_title'] = translation('course_material');

        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;
        }

        $this->course_material_public_path = url('/').config('app.project.img_path.course_material');
        $this->course_material_base_path   = base_path().config('app.project.img_path.course_material');  
    }

    public function index()
    {   
        
        $arr_data = $arr_levels = $arr_courses = [];

        $obj_data = $this->CourseMaterialModel
                               ->with(['get_level_class'=>function($q){
                                    $q->with('level_details');
                                    $q->with('class_details');
                                },'get_course','get_material_details'])
                               ->where('level_class_id',$this->level_class_id)
                               ->orderBy('id','DESC')
                               ->get();

        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }                        

        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_data'] = $arr_data;
        $this->arr_view_data['current_user'] = $this->user_id;

        $this->arr_view_data['module_title']    = translation("manage")." ".$this->module_title;
        return view($this->module_view_folder.'.index', $this->arr_view_data);

    }

    public function get_class(Request $request)
    {
        $level_id = $request->input('level');

        $options ='';

        $obj_class = $this->CommonDataService->get_class($level_id);
    
        if(!empty($obj_class))
        {
            $arr_class  = $obj_class -> toArray();
            if(count($arr_class)>0)
            {
                foreach($arr_class as $value)
                {
                    $options .= '<option value='.$value['id'];

                    if($request->has('level_class_id'))
                    {
                       
                        if($request->input('level_class_id')==$value['id'])
                        {
                            $options .= ' selected';
                        }
                    }   

                    $options .= '>'.$value['class_details']['class_name'].'</option>';
                }
            }   
        }

        return $options;
    }

    public function store(Request $request)
    {
        $messages = $arr_rules = [];
        $form_data = $request->all();
        $arr_rules['level']            = 'required';
        $arr_rules['course']          = 'required';
        $arr_rules['class']            = 'required';

        $messages['required']               = translation('this_field_is_required');
        
        $validator                          = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $arr_data =[]; 
        $arr_data['school_id'] = $this->school_id;
        $arr_data['academic_year_id'] = $this->academic_year;

        $level_id = $request->input('level');
        $class_id = $request->input('class');
        $course_id = $request->input('course');

        $arr_data['school_id']          = $this->school_id;
        $arr_data['level_id']           = $level_id;
        $arr_data['class_id']           = $class_id;
        $arr_data['course_id']          = $course_id;
        $arr_data['material_added_by']  = $this->user_id;
        $arr_data['academic_year_id']   = $this->academic_year;

        $course_material = $this->CourseMaterialModel->create($arr_data);
        $course_material_id = $course_material->id;

        if(isset($form_data['arr_document']) && count($form_data['arr_document'])>0 )
        {
            foreach($form_data['arr_document'] as $key => $file) 
            {
                if($file != NULL)
                {
                    $filename = rand(1111,9999);
                    $original_file_name = $file->getClientOriginalName();
                    $fileExt  = $file->getClientOriginalExtension();
                    $fileName = $original_file_name;
                    if(in_array($fileExt,['pdf','doc','docx','PDF','DOC','DOCX']))
                    {
                        $files[] = $original_file_name;
                        $upload_success = $file->move($this->course_material_base_path, $fileName);

                        if($upload_success)
                        {
                           $arr_certificate['type']                 = "Document";
                           $arr_certificate['path']                 = $fileName;
                           $arr_certificate['course_material_id']   = $course_material_id;

                           $status = $this->CourseMaterialDetailsModel->create($arr_certificate);
                        }
                    }
                    else
                    {
                        Session::flash('error',translation('invalid_file_extension').' '.str_singular($this->module_title));
                        return redirect()->back();
                    }
                } 
            }
        }
        if(isset($form_data['matrial_url']) && count($form_data['matrial_url'])>0)
        {
            foreach($form_data['matrial_url'] as $key => $url) 
            {
               $arr_video['type']                 = "Video";
               $arr_video['path']                 = trim($url);
               $arr_video['course_material_id']   = $course_material_id;
               if($arr_video['path']!='')
               {
                     $status = $this->CourseMaterialDetailsModel->create($arr_video);
               }
              
            }
        }

       if($course_material)
       {
            Flash::success("Course Material Added Successfully");
            return redirect()->back();
       }
       else
       {
            Flash::success("Problem Occur While adding course material");
            return redirect()->back();
       }
        
    }

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

        $arr_data = [];

        $obj_data = $this->CourseMaterialModel
                               ->with(['get_level_class'=>function($q){
                                    $q->with('level_details');
                                    $q->with('class_details');
                                },'get_course','get_material_details'])
                                ->where('id',$id)->first();

        if(!empty($obj_data))
        {
            $arr_data = $obj_data->toArray();
        }

        $this->arr_view_data['arr_data'] = $arr_data;
        $this->arr_view_data['page_title'] = $this->module_title;
 
        $this->arr_view_data['module_icon'] = "fa fa-eye";
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['module_title'] = translation("view")." ".$this->module_title;
        $this->arr_view_data['theme_color'] = $this->theme_color;

        return view($this->module_view_folder.'.view', $this->arr_view_data); 
    }

    public function delete_doc(Request $request)
    {
        $this->CourseMaterialDetailsModel->where('id',$request->input('id'))->delete();
    }

    public function download_document($enc_id)
    {
        $arr_document = [];
        if(isset($enc_id))
        {
            $document_id = base64_decode($enc_id);
            $obj_documents = $this->CourseMaterialDetailsModel
                                                    ->where('id',$document_id)
                                                    ->select('path')
                                                    ->first();
            if($obj_documents)
            {
                  $arr_document    = $obj_documents->toArray();
                  $file_name       = $arr_document['path'];
                  $pathToFile      = $this->course_material_base_path.$file_name;

                  $file_exits      = file_exists($pathToFile);
                  if($file_exits)
                  {
                     return response()->download($pathToFile, $file_name); 
                  }
                  else
                  {
                     Flash::error("Error while downloading an document.");
                  }
                  
             }
        }
        else
        {
           Flash::error("Error while downloading an document.");
        }
        return redirect()->back();
    }
}
