<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\ProfessorModel;
use App\Models\ParentModel;
use App\Models\CourseModel;
use App\Models\ProfessorCoursesmodel;
use App\Common\Services\CommonDataService;
use App\Common\Services\EmailService;
use App\Common\Services\CheckEmailExistanceService;

use Validator;
use Flash;
use Sentinel;
use Hash;
use Image;

class ProfileController extends Controller
{
	public function __construct(UserModel $user,
                                UserTranslationModel $translation,
                                LanguageService $language,
                                ProfessorModel $professor_model,
                                CourseModel $courses_model,
                                CommonDataService $service,
                                ProfessorCoursesmodel $courses,
                                EmailService $email,
                                ParentModel $ParentModel,
                                CheckEmailExistanceService $CheckEmailExistanceService)
	{
        $this->UserModel                     = $user;
        $this->UserTranslationModel          = $translation;
        $this->LanguageService               = $language;
        $this->ProfessorModel                = $professor_model;
        $this->CourseModel                   = $courses_model;
        $this->CommonDataService             = $service;
        $this->ProfessorCoursesmodel         = $courses;
        $this->EmailService                  = $email;
        $this->ParentModel                   = $ParentModel;
        $this->CheckEmailExistanceService    = $CheckEmailExistanceService;
		$this->arr_view_data                 = [];
        $this->module_title                  = "My Profile";
        $this->module_view_folder            = "professor.profile";

        $this->module_icon                   = "fa-user";
        $this->edit_icon                     = "fa-create-circle";
        $this->theme_color                   = theme_color();

        $this->admin_url_path                = url(config('app.project.role_slug.professor_role_slug'));
        $this->module_url_path               = $this->admin_url_path."/profile";
        $this->profile_image_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->profile_image_base_img_path   = base_path().config('app.project.img_path.user_profile_images');
        $this->school_id                     = \Session::has('school_id')?\Session::get('school_id'):0;
        $this->academic_year                 = \Session::has('academic_year')?\Session::get('academic_year'):0;

         $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
            $this->user_id           = $obj_data->id;  
        }
	}
    public function index()
    {
         $arr_data = array();

        if(\Session::has('locale'))
        {
            $locale = \Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
        
        $obj_data        =  Sentinel::getUser();
        
        $obj_professor = $this->ProfessorModel->where(['user_id'=>$obj_data->id])->first();
        $course     = [];
        $obj_course = $this->CommonDataService->get_courses($this->academic_year,config('app.project.role_slug.professor_role_slug'),$this->user_id);
        
        if($obj_course)
        {
            $course = $obj_course->toArray();
            
        }

        $obj_level = $arr_level = [];
        $obj_level  =   $this->CommonDataService->get_levels($this->academic_year);
        if($obj_level){
            $arr_level = $obj_level->toArray();
            
        } 
        $arrSubjects = $objSubjects = array();
        $objSubjects = ProfessorCoursesmodel::where('professor_id','=',$this->user_id)->where('school_id','=',$this->school_id)->first();
        if(!empty($objSubjects)){
            $arrSubjects = $objSubjects->toArray();
            
        }

        if($obj_data)
        {
           $arr_data['user']         = $obj_data->toArray();    
           $arr_data['professor_data'] = $obj_professor->toArray(); 
        }                                             
     


        if(sizeof($arr_data)<=0)
        {
            return redirect($this->admin_url_path.'/login');
        }
        $school_name = $this->CommonDataService->get_school_name();

        $this->arr_view_data['arrSubjects']   = $arrSubjects;
        $this->arr_view_data['course']   = $course;
        $this->arr_view_data['arr_level']   = $arr_level;
        $this->arr_view_data['edit_mode']                     = TRUE;
        $this->arr_view_data['arr_data']                      = $arr_data;
        $this->arr_view_data['page_title']                    = translation('edit_profile');
        $this->arr_view_data['module_title']                  = $this->module_title;
        $this->arr_view_data['module_url_path']               = $this->module_url_path;
        $this->arr_view_data['theme_color']                   = $this->theme_color;
        $this->arr_view_data['module_icon']                   = $this->module_icon;
        $this->arr_view_data['arr_lang']                      = $this->LanguageService->get_all_language(); 
        $this->arr_view_data['image_path'] = $this->profile_image_public_img_path;
        $this->arr_view_data['school_name'] = $school_name;

         return view($this->module_view_folder.'.edit_profile',$this->arr_view_data);
    }

    public function edit_profile()
    {

        $arr_level =[];
        $arr_data = array();

        if(\Session::has('locale'))
        {
            $locale = \Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
        
        $obj_data        =  Sentinel::getUser();
        $obj_translation =  $this->UserTranslationModel
                                 ->where(['user_id'=>$obj_data->id])       
                                 ->get(); 
        $obj_professor = $this->ProfessorModel->where(['user_id'=>$obj_data->id])->first();
        $course     = [];
        $obj_course = $this->CommonDataService->get_courses($this->academic_year,config('app.project.role_slug.professor_role_slug'),$this->user_id);
        
        if($obj_course)
        {
            $course = $obj_course->toArray();
            
        }


        $obj_level = $arr_level = [];
        $obj_level  =   $this->CommonDataService->get_levels($this->academic_year);
        if($obj_level){
            $arr_level = $obj_level->toArray();
            
        } 
        $arrSubjects = $objSubjects = array();
        $objSubjects = ProfessorCoursesmodel::where('professor_id','=',$this->user_id)->where('school_id','=',$this->school_id)->first();
        if(!empty($objSubjects)){
            $arrSubjects = $objSubjects->toArray();
            
        }

        if($obj_data)
        {
           $arr_data['user']         = $obj_data->toArray();    
           $arr_data['user_details'] = $obj_translation->toArray();
           $arr_data['professor_data'] = $obj_professor->toArray(); 
        }                                             
     


        if(sizeof($arr_data)<=0)
        {
            return redirect($this->admin_url_path.'/login');
        }
        $this->arr_view_data['course']                        = $course;
        $this->arr_view_data['arrSubjects']                   = $arrSubjects;
        $this->arr_view_data['arr_level']                     = $arr_level;
        $this->arr_view_data['edit_mode']                     = TRUE;
        $this->arr_view_data['arr_data']                      = $arr_data;
        $this->arr_view_data['page_title']                    = translation('edit_profile');
        $this->arr_view_data['module_title']                  = $this->module_title;
        $this->arr_view_data['module_url_path']               = $this->module_url_path;
        $this->arr_view_data['theme_color']                   = $this->theme_color;
        $this->arr_view_data['module_icon']                   = $this->module_icon;
        $this->arr_view_data['arr_lang']                      = $this->LanguageService->get_all_language(); 
        $this->arr_view_data['image_path'] = $this->profile_image_public_img_path;

    	 return view($this->module_view_folder.'.edit_profile',$this->arr_view_data);
    }

    public function update(Request $request)
    {
        
        $arr_rules = array();
        $arr_lang   =   $this->LanguageService->get_all_language();

        $arr_rules['first_name']           = "required|regex:/^[a-zA-Z ]+$/";
        $arr_rules['last_name']            = "required|regex:/^[a-zA-Z ]+$/";
        $arr_rules['mobile_no']            = 'required|numeric|digits_between:10,14';
        $arr_rules['national_id']          = 'required|regex:/^[A-Za-z0-9]*$/';
        $arr_rules['birth_date']           = 'required|date';
        $arr_rules['gender']               = 'required|alpha';
        $arr_rules['address']              = 'required';
        $arr_rules['year_of_experience']   = 'required|numeric|digits_between:1,2';
        $arr_rules['telephone_no']         = 'required|numeric|digits_between:6,14';
        $arr_rules['qualification_degree'] = 'required|regex:/^[a-zA-Z0-9 \,]*$/';

        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'mobile_no.digits_between' => translation('please_enter_mobile_no_within_range_of_10_14'),
                            'telephone_no.digits_between'  => translation('please_enter_telephone_no_within_range_of_6_14'),
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'),
                            'alpha'                => translation('please_enter_letters_only'),
                            'year_of_experience.digits_between' => translation('year_of_experience_can_not_be_greater_than_2_digits')  
                        );

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $id = base64_decode($request->input('enc_id'));
        
        $oldImage = $request->input('old_image');
        if($request->hasFile('image'))
        { 
            $arr_image_size = [];
            $arr_image_size = getimagesize($request->file('image'));

            if(isset($arr_image_size) && $arr_image_size==false)
            {
                Flash::error('Please use valid image');
                return redirect()->back(); 
            }

            $minHeight = 250;
            $minWidth  = 250;
            $maxHeight = 2000;
            $maxWidth  = 2000;

            if(($arr_image_size[0] < $minWidth || $arr_image_size[0] > $maxWidth) && ($arr_image_size[1] < $minHeight || $arr_image_size[1] > $maxHeight))
            {
                
                Flash::error(translation('please_upload_image_with_height_and_width_greater_than_or_equal_to_250_x_250_less_than_or_equal_to_2000_x_2000_for_best_result'));
                return redirect()->back();
            }

            $file_name = $request->file('image');
            $file_extension = strtolower($request->file('image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('image')->move($this->profile_image_base_img_path , $file_name);
                if($isUpload)
                {
                    @unlink($this->profile_image_base_img_path.$oldImage);
                    @unlink($this->profile_image_base_img_path.'/thumb_50X50_'.$oldImage);
                }
            }
            else
            {
                Flash::error(translation('invalid_file_type_while_creating').' '.str_singular($this->module_title));
                return redirect()->back();
            }
        }
        else
        {
             $file_name = $oldImage;
        }

        $professor_details = [];
        $professor_details['status']                =   $request->input('status');
        $professor_details['year_of_experience']    =   $request->input('year_of_experience');
        $professor_details['qualification_degree']  =   $request->input('qualification_degree');
        $professor_details['subject_id']            =   '0';
        
        $this->ProfessorModel->where('id',$id)->update($professor_details);

        $user  = $this->ProfessorModel->select('user_id')->where('id',$id)->first();

        $user_id = isset($user->user_id) ? $user->user_id : 0;
        
        $arr_data                   = [];
        $arr_data['profile_image']  = $file_name;
        $arr_data['latitude']       = trim($request->input('latitude'));
        $arr_data['longitude']      = trim($request->input('longitude'));
        $arr_data['telephone_no']   = trim($request->input('telephone_no'));
        $arr_data['national_id']    = trim($request->input('national_id'));    
        $arr_data['mobile_no']      = trim($request->input('mobile_no'));
        $arr_data['birth_date']     = trim($request->input('birth_date'));
        $arr_data['gender']         = trim($request->input('gender'));
        $arr_data['city']           = trim($request->input('city'));
        $arr_data['country']        = trim($request->input('country'));
        $arr_data['address']        = trim($request->input('address'));
        
        $obj_data = $this->UserModel->where('id',$user_id)->update($arr_data);

        $status = $this->UserModel->where('id',$user_id)->first(); 

        $res = '';        
        if(!empty($request->input('subject')))
        {
            $obj_sub_qualified = ProfessorCoursesmodel::where('professor_id',$user_id)
                                                    ->where('school_id',$this->school_id)
                                                    ->delete();
            
            $arrlevels = $emp_quali =  array();
            $subjects          = $request->input('subject');  
            $levels          = $request->input('levels');  
            /*Bring the classes between the orders */
            /* store level wise */
            if($subjects){
                $emp_quali['school_id']        = $this->school_id;
                $emp_quali['professor_id']     = $user_id;
                $emp_quali['course_id']        = json_encode($subjects);
                $emp_quali['levels']           = json_encode($levels);
                $emp_quali['academic_year_id'] = $this->academic_year;
                $res                           = ProfessorCoursesmodel::create($emp_quali);
            }
            /* store level wise */
        }                                                


        if($status)
        {
            /* update record into translation table */
            if(sizeof($arr_lang) > 0 )
            {
                foreach ($arr_lang as $lang) 
                {            
                    $arr_data = array();
                    $first_name       = trim($request->input('first_name'));
                    $last_name        = trim($request->input('last_name'));
                    if( (isset($first_name)  && $first_name != '') && (isset($last_name) && $last_name != ''))
                    { 
                        $translation = $status->translateOrNew($lang['locale']);
                        $translation->first_name    = $first_name;
                        $translation->last_name     = $last_name;
                        $translation->save();
                        Flash::success(str_singular($this->module_title).' '.translation('updated_successfully'));
                    }
                }
            } 
           /*------------------------------------------------------*/
        }
        
        if($obj_data || $res)
        {   
            $language           = trim($request->input('language'));
            $db_language        = $this->ProfessorModel->where('user_id',$user_id)->first();
            if($language)
            {
                if($language != $db_language->language)
                {
                    $this->ProfessorModel->where('user_id',$user_id)->update(['language'=>$language]);    
                    \Session::flush();
                    \Sentinel::logout();
                    return redirect(url($this->admin_url_path));
                }
            }
            Flash::success(str_singular($this->module_title).' '.translation('updated_successfully')); 
        }
        else
        {
            Flash::error(translation('problem_occurred_while_updating').' '.str_singular($this->module_title));  
        } 
      
        return redirect()->back();  
    }

    public function attachmentThumb($input, $name, $width, $height)
    {
        $thumb_img = Image::make($input)->resize($width,$height);
        $thumb_img->fit($width,$height, function ($constraint) {
            $constraint->upsize();
        });
        $thumb_img->save($this->profile_image_base_img_path.'/thumb_'.$width.'X'.$height.'_'.$name);         
    }

    public function change_email(Request $request)
    {

        $user          = Sentinel::check();
        if($user)
        {
            $date = date('Y-m-d H:i:s');
            $expiry_date   = date('Y-m-d H:i:s',strtotime($date. ' + 24 hours'));
                $data          = [
                                    'first_name'    =>  $request->Input('first_name'),
                                    'email'         =>  $request->Input('email'),
                                    'id'            =>  base64_decode($request->Input('id'))
                                 ];

                $arr_mail_data = $this->built_mail_data($data); 
                
                $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id); 
                if($email_status)
                {
                    $this->UserModel->where('id',$user->id)->update(['email_expired_on'=>$expiry_date]);
                    return response()->json(array('status'=>'success'));
                }
                else
                {
                    return response()->json(array('status'=>'error'));
                }
           /* }*/
        }
    }

    /*
    | index() : generate mail data
    | Auther : sayali
    | Date : 10-07-2018
    */
     public function built_mail_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $link = \URL::to(config('app.project.professor_panel_slug').'/email_change/'.base64_encode($arr_data['id']));
                

            $reminder_url = '<p class="email-button"> <a target="_blank" href="'.$link.'">Click Here </a></p><br/>';
           
            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'REMINDER_URL'     => $reminder_url,
                                  'PROJECT_NAME'     => config('app.project.name'),
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id)
                                 ];

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_slug'] = 'change_email';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function email_change($enc_id)
    {
          $this->arr_view_data['theme_color']                   = $this->theme_color;
          return view($this->module_view_folder.'.email_change',$this->arr_view_data,compact('enc_id'));
    }

    public function process_change_email(Request $request)
    {
        $date = date('Y-m-d H:i:s');
        $users = $employee = $parent_details = $school_admin = [];
        $arr_rules                      = array();
        $arr_rules['email']             = "required";
        $arr_rules['enc_id']            = "required";

        $messages['required']           = 'This field is required';

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
          return redirect()->back();
        }

        $enc_id            = $request->input('enc_id');
        $email             = $request->input('email');

        $user_id           = base64_decode($enc_id);

        $user = Sentinel::findById($user_id);

        if(!$user)
        {
          Flash::error('Invalid User Request');
          return redirect()->back();
        }


        $expiry_date       = $user->email_expired_on;
        if(!(strtotime($date)<strtotime($expiry_date)))
        {
            Flash::error('Change of email link is expired');
            return redirect()->back();
        }
        else
        {
            $existance = $this->UserModel->where('email',$email)->where('id','!=',$user_id)->first();
            if(count($existance)>0)
            {
                Flash::error('This email is already exist for this school');
                return redirect()->back();
            }
            else
            {
                $update_data    =   $this->UserModel->where('id',$user->id)->update(['email'=>$email]);

                if($update_data)
                {
                    Flash::success('Email changed successfully');
                    return redirect(url($this->admin_url_path));
                }
                else
                {
                    Flash::error('Something went wrong');
                    return redirect()->back();
                }
            }
          
        }              
    }

    public function set_language(Request $request)
    {
     
        $language = $request->input('lang');
        $db_language        = $this->ProfessorModel->where('user_id',$this->user_id)->first();
        
        if($language)
        {
            if($language != $db_language->language)
            {
                $this->ProfessorModel->where('user_id',$this->user_id)->update(['language'=>$language]);
                $lang =  \Session::get('locale');
                \Session::flush();
                Sentinel::logout();
                \Session::put('locale',$language);
                return redirect(url('/').'/login');
                //return redirect(url($this->school_admin_panel_slug));
            }
        }
    }
}
