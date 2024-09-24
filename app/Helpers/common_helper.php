<?php

use App\Models\CourseModel;
use App\Models\StudentModel;
use App\Models\SchoolCourseModel;
use App\Models\SchoolTimeTableModel;
use App\Models\ExamModel;
use App\Models\SchoolAdminModel;
use App\Models\SchoolTemplateTranslationModel;
use App\Models\SchoolProfileModel;


function strslug($str)
{
    $str = strtolower($str);
    $str = str_replace(' ', '_', $str);
    return $str;
}
function getDateFormat($date)
{
    
    if($date!="0000-00-00" && $date!="0000-00-00 00:00:00")
    {
        $new_date_format = $date;
        $new_date_format = date_create($new_date_format);
        $new_date_format = date_format($new_date_format,'d-m-Y');
        return ($new_date_format);    
    }
    else
    {
        return "";
    }
    
}

function getDateTimeFormat($date)
{
    if($date!="0000-00-00" && $date!="0000-00-00 00:00:00")
    {
        $new_date_format = $date;
        $new_date_format = date_create($new_date_format);
        $new_date_format = date_format($new_date_format,'d-m-Y H:i A ');

        return ($new_date_format);        
    }
    else
    {
        return "";
    }
    
}

function getTimeFormat($time){
    if($time!="00:00:00" && $time!="")
    {
        $new_time_format = $time;
        
        $new_time_format = date_create($new_time_format);
        $new_time_format = date_format($new_time_format,'H:i');

        return ($new_time_format);        
    }
    else
    {
        return "";
    }
}

function getDateFormateForEdit($date){
     if($date!="0000-00-00" && $date!="0000-00-00 00:00:00")
    {
        $new_date_format = $date;
        $new_date_format = date_create($new_date_format);
        $new_date_format = date_format($new_date_format,'m/d/Y');
        return ($new_date_format);        
    }
    else
    {
        return "";
    }
    
}

function get_course_name($id){
      $obj_course   = CourseModel::where('id',$id)->first();
      if(count($obj_course)>0){
        return $obj_course->course_name;
      }
      else{
       return ''; 
      }
}   

function get_professor_by_course($level_class_id ,$course_id){

    $name='';
    $obj_professor = SchoolTimeTableModel::where('level_class_id',$level_class_id)
                                                    ->whereHas('professor_details',function($q1){
                                                        $q1->where('has_left','=', 0);
                                                        $q1->where('school_id','=',\Session::get('school_id'));
                                                    })
                                                    ->with(['user_details' => function ($query)
                                                    {
                                                          $query->select('id','profile_image');
                                                    }])
                                                    ->where('school_id',\Session::get('school_id'))
                                                    ->where('academic_year_id',\Session::get('academic_year'))
                                                    ->where('course_id',$course_id)
                                                    ->first();

    if(count($obj_professor)>0){
     
        $first_name = $obj_professor->user_details->first_name;
        $last_name = $obj_professor->user_details->last_name;
        $name = ucfirst($first_name).' '.ucfirst($last_name);
    }                                                
    return $name;
}

function get_coefficient($course_id)
{
    $course = SchoolCourseModel::where('school_id',Session::get('school_id'))
                       ->where('course_id',$course_id)
                       ->where('academic_year_id',Session::get('academic_year'))
                       ->first();
                       
    if(count($course)>0)                   
        return $course->coefficient;
    else
        return '';    
}

function get_student_average_marks($student_id,$course_id){

    $obj_course =  ExamModel::
                        whereHas('get_result',function(){})
                        ->whereHas('get_school_exam_type',function($q){
                            $q->where('gradebook','1');
                        })
                        ->whereHas('get_assessment',function($q){
                            $q->where('type','MARKS');
                        })
                        ->whereHas('get_school_course',function($q){

                        })
                        ->with('get_result','get_exam_type','get_school_course')    
                        ->where('school_id',Session::get('school_id'))
                        ->where('academic_year_id',Session::get('academic_year'))
                        ->where('level_class_id',Session::get('class_id_for_gradebook'))
                        ->where('exam_period_id',Session::get('exam_period'))
                        ->where('course_id',$course_id)
                        ->orderBy('exam_type_id','DESC')
                        ->where('status','APPROVED')
                        ->get();                                    
            
    $avg_marks       = 0;
    $marks           = 0;

    $max             = 0;
    $exams           = [];

    $arr_exam_type   = [];

    $min             = 0;
    $max             = 0;
    $flag            = 0;
    $is_grade        = 0;

    $exam_count      = 0;
    $class_avg_marks = 0;

    $arr_students= array();

    $arr_final_exams =[];
    if($obj_course && count($obj_course)>0){
        $arr_course = $obj_course->toArray();
        foreach($obj_course as $key=>$course){

                $exam_count++;
                if(isset($course->get_result->result)){
                    $result = json_decode($course->get_result->result,true);
                    $flag=0;
                    $class_marks = 0;
                    $student_count = 0;
                    $new_max=0;
                    foreach($result as $new_key=>$value){
                            if(!isset($arr_students[$new_key]))
                            {
                                $arr_students[$new_key]=0;    
                            }
                            
                            if(is_numeric($value)){
                                $student_count++;
                                $class_marks += $value;
                                
                                $arr_students[$new_key] += $result[$new_key];

                                $is_grade=1;
                                if($flag==0){
                                    $new_min = $value;
                                    $flag=1;
                                }
                                if($new_min>$value){
                                    $new_min = $value;
                                }
                                if($new_max<$value){
                                    $new_max = $value;
                                }
                                if($new_key==$student_id){
                                    $marks += $result[$student_id];
                                }
                            }
                        }
                    $temp = round($class_marks/$student_count,2);
                    $class_avg_marks += $temp ;    
                    $min += $new_min;
                    $max += $new_max;
                   
                    if(count($arr_course)==1){
                        
                        $temp_arr =[];
                        $temp_arr['exam_name'] = ucfirst($course->get_exam_type->exam_type);
                        $temp_arr['coefficient']= ucfirst($course->get_school_course->coefficient);
                        $temp_arr['marks']     = $exam_count!=0 ? round(($marks/$exam_count),2)             :0;
                        $temp_arr['minimum']   = $exam_count!=0 ? round(($min/$exam_count),2)               :0;
                        $temp_arr['maximum']   = $exam_count!=0 ? round(($max/$exam_count),2)               :0;
                        $temp_arr['average']   = $exam_count!=0 ? round(($class_avg_marks/$exam_count),2)   :0;
                        foreach($arr_students as $new_key=>$student_marks){
                            $arr_students[$new_key] = $exam_count!=0 ? round(($student_marks/$exam_count),2) : 0;
                        }
                        $temp_arr['students'] = json_encode($arr_students);
                        array_push($exams,$temp_arr);
                        $exam_count=0;
                        $class_avg_marks = 0;
                        $min             = 0;
                        $max             = 0;
                        $marks           = 0;
                        $arr_students    = [];
                    }   
                    else{
                        if(in_array($course->exam_type_id,$arr_exam_type)){                      
                         
                        }
                        else{
                           
                            array_push($arr_exam_type,$course->exam_type_id);
                            
                            if(($key+1 < count($arr_course)) && $arr_course[$key+1]['exam_type_id'] != $arr_course[$key]['exam_type_id']) {
                              
                                $temp_arr =[];
                                $temp_arr['exam_name'] = ucfirst($course->get_exam_type->exam_type);
                                $temp_arr['coefficient']= ucfirst($course->get_school_course->coefficient);
                                $temp_arr['marks']     = $exam_count!=0 ? round(($marks/$exam_count),2)             :0;
                                $temp_arr['minimum']   = $exam_count!=0 ? round(($min/$exam_count),2)               :0;
                                $temp_arr['maximum']   = $exam_count!=0 ? round(($max/$exam_count),2)               :0;
                                $temp_arr['average']   = $exam_count!=0 ? round(($class_avg_marks/$exam_count),2)   :0;
                                foreach($arr_students as $new_key=>$student_marks){
                                    $arr_students[$new_key] = $exam_count!=0 ? round(($student_marks/$exam_count),2) : 0;
                                }
                                $temp_arr['students'] = json_encode($arr_students);
                                array_push($exams,$temp_arr);
                                $exam_count=0;
                                $class_avg_marks = 0;
                                $min             = 0;
                                $max             = 0;
                                $marks           = 0;
                                $arr_students    = [];

                            }
                            if($key>0 && ($key==count($arr_course)-1) && $arr_course[$key-1]['exam_type_id'] != $arr_course[$key]['exam_type_id']) 
                            {
                                $temp_arr =[];
                                $temp_arr['exam_name'] = ucfirst($course->get_exam_type->exam_type);
                                $temp_arr['coefficient']= ucfirst($course->get_school_course->coefficient);
                                $temp_arr['marks']     = $exam_count!=0 ? round(($marks/$exam_count),2)             :0;
                                $temp_arr['minimum']   = $exam_count!=0 ? round(($min/$exam_count),2)               :0;
                                $temp_arr['maximum']   = $exam_count!=0 ? round(($max/$exam_count),2)               :0;
                                $temp_arr['average']   = $exam_count!=0 ? round(($class_avg_marks/$exam_count),2)   :0;
                                foreach($arr_students as $new_key=>$student_marks){
                                    $arr_students[$new_key] = $exam_count!=0 ? round(($student_marks/$exam_count),2) : 0;
                                }
                                $temp_arr['students'] = json_encode($arr_students);
                                array_push($exams,$temp_arr);
                                $exam_count=0;
                                $class_avg_marks = 0;
                                $min             = 0;
                                $max             = 0;
                                $marks           = 0;
                                $arr_students    = [];
                            }
                        }

                        if($key>0 && ($key==count($arr_course)-1) && $arr_course[$key-1]['exam_type_id'] == $arr_course[$key]['exam_type_id']) 
                        {
                            $temp_arr =[];
                            $temp_arr['exam_name'] = ucfirst($course->get_exam_type->exam_type);
                            $temp_arr['coefficient']= ucfirst($course->get_school_course->coefficient);
                            $temp_arr['marks']     = $exam_count!=0 ? round(($marks/$exam_count),2)             :0;
                            $temp_arr['minimum']   = $exam_count!=0 ? round(($min/$exam_count),2)               :0;
                            $temp_arr['maximum']   = $exam_count!=0 ? round(($max/$exam_count),2)               :0;
                            $temp_arr['average']   = $exam_count!=0 ? round(($class_avg_marks/$exam_count),2)   :0;
                            foreach($arr_students as $new_key=>$student_marks){
                                $arr_students[$new_key] = $exam_count!=0 ? round(($student_marks/$exam_count),2) : 0;
                            }
                            $temp_arr['students'] = json_encode($arr_students);
                            array_push($exams,$temp_arr);
                            $exam_count=0;
                            $class_avg_marks = 0;
                            $min             = 0;
                            $max             = 0;
                            $marks           = 0;
                            $arr_students    = [];
                        }
                    }
                  
                }
        }
    }
    
    $arr_exam_details['exams'] = $exams;

    return $arr_exam_details;                 

}


function getProfessorTeachingSubject($professor_id,$academic_year_id,$level_class_id=false){
    $temp = $arr_subjects =  array();
    $strSubjects = '';
    $obj = SchoolTimeTableModel::where('professor_id',$professor_id)
                                ->where('academic_year_id',$academic_year_id)
                                ->where('school_id',\Session::get('school_id'))
                                ->whereHas('professor_subjects',function($q){
                                        $q->select('id');
                                });
                                if($level_class_id){
                                    $obj ->where('level_class_id',$level_class_id);
                                }
                                $obj ->with('professor_subjects')
                                ->groupBy('course_id');
                         $obj = $obj ->get();
    if($obj){
        $arr_subjects = $obj->toArray();

        foreach($arr_subjects as $arr_subjects_res){
            if(!empty($arr_subjects_res['professor_subjects']) && $arr_subjects_res['professor_subjects']['course_name']!=''){
                    $temp[] = $arr_subjects_res['professor_subjects']['course_name'];
            }
        }
       
        $strSubjects = implode($temp,',');
    }

    return $strSubjects;
}

function get_kids($parent_id){
     $str = '';
      $obj= StudentModel::whereHas('get_user_details',function(){})
                          ->with('get_user_details','get_level_class.level_details','get_level_class.class_details')
                          ->where('school_id',\Session::get('school_id'))
                          ->where('has_left',0)
                          ->where('academic_year_id',\Session::get('academic_year'))
                          ->where('parent_id',$parent_id)
                          ->get();  
      if($obj && count($obj)>0){
        $arr = $obj -> toArray();
        $temp=[];
        
        foreach($arr as $value){
            $first_name = isset($value['get_user_details']['first_name']) ? $value['get_user_details']['first_name']:'';
            $last_name  = isset($value['get_user_details']['last_name']) ? $value['get_user_details']['last_name']:'';
            $level      = isset($value['get_level_class']['level_details']['level_name'])?$value['get_level_class']['level_details']['level_name']:'';
            $class      = isset($value['get_level_class']['class_details']['class_name'])?$value['get_level_class']['class_details']['class_name']:'';
            
            $name = $first_name.' '.$last_name.' ['.$level.' '.$class.']';
            ;
            if($name){
                array_push($temp,$name);
            }
        }
      } 

     return $temp; 
}
/* IMAGE HELPER */
include_once(app_path() . '/images/images.php');
if(!defined('DIR_IMAGE')){define('DIR_IMAGE','uploads/cache/');}
function resize_images_new($DIR_IMAGE,$imageName = FALSE, $width, $height)
{

     if (!is_dir(DIR_IMAGE)) {
        @mkdir(DIR_IMAGE, 0777);
     }

    if($imageName == FALSE ){
        return "https://placeholdit.imgix.net/~text?txtsize=33&txt=No Image&w=".$width."&h=".$height;
    }
    
    $fileURL =  public_path().'/'.$DIR_IMAGE.$imageName;
    
    if(!file_exists($fileURL))
    {
        return "https://placeholdit.imgix.net/~text?txtsize=33&txt=No Image&w=".$width."&h=".$height;
    }



        
    $real_dir = base_path().'/'.$DIR_IMAGE;
    if(!image_exists($real_dir.$imageName))
    {
        return "https://placeholdit.imgix.net/~text?txtsize=33&txt=No Image&w=".$width."&h=".$height;
    }


    if(!is_valid_image($real_dir.$imageName))
    {   
        return "https://placeholdit.imgix.net/~text?txtsize=33&txt=No+Image&w=".$width."&h=".$height;
    }

    /*$imageName = explode("/",$filename);
    $imageName = end($imageName);*/

   
    /*dd(substr(str_replace('\\', '/', realpath(DIR_IMAGE . $imageName)), 0, strlen(DIR_IMAGE)));*/
    if (!is_file($real_dir . $imageName)/* || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $imageName)), 0, strlen(DIR_IMAGE)) != DIR_IMAGE*/) {
            /*return;*/
            /*dd(DIR_IMAGE . $imageName);*/
            return 'Image Not Found';
        }
        $extension = pathinfo($imageName, PATHINFO_EXTENSION);

        $image_old = $imageName;
        $image_new = $imageName . '-' . (int)$width . 'x' . (int)$height . '.' . $extension;
           
        
        if (!is_file(DIR_IMAGE . $image_new) || (filectime($DIR_IMAGE . $image_old) > filectime(DIR_IMAGE . $image_new))) {
            list($width_orig, $height_orig, $image_type) = getimagesize($DIR_IMAGE . $image_old);
                 
            if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) { 
                return $DIR_IMAGE . $image_old;
            }
           

            if ($width_orig != $width || $height_orig != $height) {
                $image = new Images($DIR_IMAGE . $image_old);
                $image->resize($width, $height);
                $image->save(DIR_IMAGE . $image_new);
            } else {
                copy($DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);
            }
        }
        
        $image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +

        return url('/').'/'.DIR_IMAGE.$image_new;
}
/* IMAGE HELPER */


    function getFileExtenion($fileName)
    {   
        $path = $fileName;
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        return $ext;
    }


    function generate_password_reg($role)
    {
        $rand = rand(00000,99999);
        return $role.'@'.$rand;
    }

    function get_school_admin(){
        $school_id = \Session::get('school_id');
        $school = SchoolAdminModel::with('get_user_details')->where('school_id',$school_id)->first();

        return $school;

    }

    function get_school_name($school_id=FALSE)
    {
        if($school_id == FALSE)
        {
            $school_id = \Session::get('school_id');
        }
        
        $template = SchoolTemplateTranslationModel::where('slug','school_name')->first();
        $template_id = isset($template->school_template_id) ? $template->school_template_id : 0;
        $result = SchoolProfileModel::where('school_template_id',$template_id)
                                    ->where('school_no',$school_id)
                                    ->first();
                                    
        if(isset($result->value))
        {

            return $result->value;
        }
        else
        {
            return '';
        }
    }
?>