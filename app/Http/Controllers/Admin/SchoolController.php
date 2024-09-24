<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Common\Traits\MultiActionTrait;

use App\Models\QuestionCategoryModel;
use App\Models\SchoolTemplateModel;
use App\Models\SchoolTemplateTranslationModel;
use App\Models\SchoolAdminModel;

use App\Models\SchoolProfileModel;
use App\Models\SchoolProfileTranslationModel;

use App\Models\SchoolSmsTemplateModel;
use App\Models\SchoolSmsTemplateTranslationModel;
use App\Models\ClaimPermissionModel;
use App\Models\ModulesModel;
use App\Models\EmailTemplateModel;
use App\Models\SchoolEmailTemplateModel;
use App\Models\SmsTemplateModel;
use Validator;
use Flash;
use Session;
use DB;
use Datatables;
use Sentinel;
class SchoolController extends Controller
{
	use MultiActionTrait;
    public function __construct(
    								LanguageService 	  $language,
    								QuestionCategoryModel $question_category,
    								SchoolTemplateModel   $school_template,
    								SchoolTemplateTranslationModel $school_template_translation,
                                    SchoolProfileModel   $school_profile,
                                    SchoolProfileTranslationModel $school_profile_translation,
                                    SchoolAdminModel $school_admin,
                                    ClaimPermissionModel $claim,
                                    ModulesModel $module,
                                    EmailTemplateModel $EmailTemplateModel,
                                    SchoolEmailTemplateModel $SchoolEmailTemplateModel,
                                    SchoolSmsTemplateModel $SchoolSmsTemplateModel,
                                    SmsTemplateModel $SmsTemplateModel
    							)
    {
		$this->arr_view_data 	              = [];
		$this->QuestionCategoryModel          = $question_category;
		$this->SchoolTemplateModel            = $school_template;
		$this->BaseModel                      = $this->SchoolTemplateModel;
		$this->LanguageService                = $language;
    	$this->SchoolTemplateTranslationModel = $school_template_translation;
        $this->SchoolProfileModel             = $school_profile;
        $this->SchoolProfileTranslationModel  = $school_profile_translation;
        $this->SchoolAdminModel               = $school_admin;
		$this->ClaimPermissionModel           = $claim;
        $this->ModulesModel                   = $module;
        $this->EmailTemplateModel             = $EmailTemplateModel;
        $this->SchoolEmailTemplateModel       = $SchoolEmailTemplateModel;
        $this->SchoolSmsTemplateModel         = $SchoolSmsTemplateModel;
        $this->SmsTemplateModel               = $SmsTemplateModel;
 		$this->module_url_path 	              = url(config('app.project.admin_panel_slug')."/school_admin");
        $this->school_url_path                = url(config('app.project.admin_panel_slug')."/school");
        $this->user_profile_base_img_path     = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path   = url('/').config('app.project.img_path.user_profile_images');
		$this->module_view_folder             = "admin.school";
		$this->module_title                   = translation('school_admin');
        $this->school_title               = translation('school');
		$this->theme_color                    = theme_color();
		$this->module_icon                    = 'fa fa-server';
		$this->create_icon                    = 'fa fa-plus-circle';
		$this->edit_icon                      = 'fa fa-edit';
    }

    public function create($enc_id)
    {
        $arr_lang = [];
        $arr_lang = $this->LanguageService->get_all_language();

        $arr_template = [];
        $obj_template = $this->BaseModel
                             ->with("get_question_category")
                             ->with('translations')
                             ->where('is_active',1)
                             ->orderBy('position','asc')
                             ->get();   

        if($obj_template)
        {
            $arr_template = $obj_template->toArray();
           
            /*foreach ($arr_template as $key => $value) {
                $data_translation[$key]   =   $this->arrange_locale_wise($value['translations']);
            }
            $arr_template['translations'] =$data_translation;*/
        } 
        
        $this->arr_view_data['page_title']      = translation('assign_school');
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language(); 
        $this->arr_view_data['school_url_path'] = $this->school_url_path;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_template']    = $arr_template;
        
        $this->arr_view_data['enc_id']          = $enc_id;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['create_icon']     = $this->create_icon;
        $this->arr_view_data['image_path']     = $this->user_profile_public_img_path;
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }


    public function store(Request $request)
    {
      
        $arr_rules  =   $messages = [];
        $arr_lang   =   $this->LanguageService->get_all_language();
        $user_id     =   base64_decode($request->input('enc_id'));
        
        $arr_data   =   $data   =   [];
        $obj_template = $this->BaseModel
                             ->with("get_question_category")
                             ->where('is_active',1)
                             ->orderBy('position','asc')
                             ->get();   

        if($obj_template)
        {
            $arr_template = $obj_template->toArray();
        }  
        
        $min1 = 10;
        $min2 = 6;
        $max  = 14;
       foreach ($arr_template as $key => $template) 
       {
          if($template['id'] == 4)
          {
            $arr_rules['school_name'] = ['required','regex:/^[a-zA-Z0-9 .&]+$/'];
          }
          elseif($template['id'] == 13)
          {
            $arr_rules['email'] = ['required','email'];
          }
          elseif($template['id'] == 8)
          {
            $arr_rules['address'] = ['required'];
          }
          else
          {
            if(($template['is_required'] == 1) || ($template['validations']!=''))
            {
                 $string1= [];
                 if($template['is_required']==1)
                 {
                      /*$string1 = 'required';*/
                      array_push($string1, 'required');
                 }
                 if($template['validations']!= '')
                 {
                      $arr_validations = explode(',', $template['validations']);
                      if(!in_array('mobile_no', $arr_validations) && !in_array('telephone_no', $arr_validations) && !in_array('email', $arr_validations))
                      {
                        $pattern ='/^[';
                        if(in_array('letters',$arr_validations))
                        {
                          $pattern .= 'a-zA-Z';
                        }
                        if(in_array('digits',$arr_validations))
                        {
                          $pattern .= '0-9';
                        }
                        if(in_array('white_space',$arr_validations))
                        {
                          $pattern .= ' ';
                        }
                        if(in_array('hyphen',$arr_validations))
                        {
                          $pattern .= '\-';
                        }
                        if(in_array('special_symbols',$arr_validations))
                        {
                          $pattern .= '$&+,:;=?@#|\'<>^*()%!~`\"';
                        }
                        if(in_array('dot',$arr_validations))
                        {
                          $pattern .= '\.';
                        }
                        $pattern .=']+$/';

                        $string2 = "regex:".$pattern;
                        array_push($string1, $string2);
                    	}
                    	else
                    	{
                        if(in_array('mobile_no',$arr_validations))
                        {
                          $string4 = "digits_between:10,14";
                          array_push($string1, $string4);
                        }
                        if(in_array('telephone_no',$arr_validations))
                        {
                          $string5 = "digits_between:6,14";
                          array_push($string1, $string5);
                        }
                    	}
                 }
                 
                 $arr_rules[strslug($template['title'])] = $string1;
                 
             }
           }

       }
       
       $messages['required']               = translation('this_field_is_required');
       $messages['regex']                  = translation('please_enter_valid_text_format');
       $messages['email']                  = translation('please_enter_valid_email_format');
       $messages['digits_between:10,14']   = translation('please_enter_telephone_no_within_range_of').$min1.'-'.$max;
       $messages['digits_between:6,14']    = translation('please_enter_mobile_no_within_range_of').$min2.'-'.$max;
       $validator = Validator::make($request->all(),$arr_rules,$messages);
       
       
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        
        $arr_emails = [];
        
        $is_new_file_uploaded = FALSE;
        /*foreach ($request->all() as $key => $data) {*/
           
            if($request->hasFile('image'))
            {
                /*$temp_key = $key;
                $arr = explode('_',$temp_key);

                $id = $arr[0];*/

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

                $excel_file_name = $request->file('image');
                $file_extension   = strtolower($request->file('image')->getClientOriginalExtension());

                if(in_array($file_extension,['png','jpg','jpeg']))
                {
                    $arr_files_name=[];
                    $file_name       = sha1(uniqid().$excel_file_name.uniqid()).'.'.$file_extension;
                    $request->file('image')->move($this->user_profile_base_img_path,$file_name);

                    $arr_files_name['image'] = $file_name;    
                }
                else
                {
                    Flash::error('invalid_file_type_while_creating'.str_singular($this->module_title));
                    return redirect()->back();
                }
            }
       /* }*/
        
            $school_no = '';

            $school_no  =   $this->generate_school_no($request->input('school_name'));

                
            foreach ($arr_template as $template) 
            {

                $arr_data['school_no']              =   $school_no;
                $arr_data['school_template_id']     =   $template['id'];
                $arr_data['position']               =   $template['position'];
                
                $data   =   $this->SchoolProfileModel->create($arr_data);

                foreach ($arr_lang as $lang) 
                {
                        $value = '';
                        $key = strslug($template['title']);
                        
                        if($request->hasFile('image'))
                        {
                            
                            $value = $arr_files_name['image'];
                        }
                        if($request->has($key))
                        {
                            $value  =   trim($request->input(strslug($template['title'])));  
                        }
                        if($template['id']== 8)
                        {
                            $value  =   trim($request->input('address'));   
                        }
                        if($template['id']== 4)
                        {
                            $value  =   trim($request->input('school_name'));   
                        }
                        if($template['id']== 13)
                        {
                            $value  =   trim($request->input('email'));   
                        }
                        
                        if(isset($value))
                        {
                            $translation = $data->translateOrNew($lang['locale']);
                            $translation->value                  = $value;
                            $translation->school_profile_id      = $data->id;
                            $translation->locale                 = $lang['locale'];
                            $translation->save();
                        }
                }

            }

        $obj    =   $this->SchoolAdminModel->where('user_id',$user_id)->update(['school_id'=>$school_no]);
        
        if($obj)
        {
            $module = $this->ModulesModel->where('slug','claim')->first();
            if(isset($mosule) && !empty($module))
            {
                $arr_data1 = [];
                $arr_data1['school_id'] = $school_no;
                $arr_data1['academic_year_id'] = $this->academic_year;
                $arr_data1['module_id'] = $module->id;
                $arr_data1['is_active'] = 1;

                $data = $this->ClaimPermissionModel->create($arr_data);
            }

            $result = $this->copyTemplate($school_no);

            Flash::success(translation('school_added_successfully'));
            return redirect('admin/school_admin');
        }
        else
        {
            Flash::error(translation('problem_occured_while_assigning_school'));
            return redirect()->back();
        }


    }  
    

    public function edit($enc_id=FALSE)
    {
        $arr_lang = [];
        $arr_data = $data = $data_translation = [];
        $arr_lang = $this->LanguageService->get_all_language();
        
        $obj_template = $this->BaseModel
                             ->with("get_question_category")
                             ->with('translations')
                             ->where('is_active',1)
                             ->orderBy('position','asc')
                             ->get();   

        if($obj_template)
        {
            $arr_template = $obj_template->toArray();
        }  
       
        $school   = $this->SchoolAdminModel->where('user_id',base64_decode($enc_id))->first();
        if(!isset($school->id)){
          Flash::error(translation('something_went_wrong'));
          return redirect()->back();
        }
        $obj_data = $this->SchoolProfileModel->where('school_no',$school->school_id)->get();
     
        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }    
        
        $this->arr_view_data['page_title']              = translation('edit_school');
        $this->arr_view_data['module_title']            = str_plural($this->module_title);
        $this->arr_view_data['edit_mode']               = TRUE;
        $this->arr_view_data['school_url_path']         = $this->school_url_path;
        $this->arr_view_data['module_url_path']         = $this->module_url_path;
        $this->arr_view_data['arr_lang']                = $arr_lang;
        $this->arr_view_data['arr_data']                = $arr_data;
          
        
        $this->arr_view_data['arr_template']            = $arr_template;

        $this->arr_view_data['theme_color']             = $this->theme_color;
        $this->arr_view_data['module_icon']             = $this->module_icon;
        $this->arr_view_data['edit_icon']               = $this->edit_icon;
        $this->arr_view_data['enc_id']                  = $enc_id;
        $this->arr_view_data['image_path']              = $this->user_profile_public_img_path ;
        $this->arr_view_data['base_path']               = $this->user_profile_base_img_path;
        
        
        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }
    public function update(Request $request,$enc_id)
    {   
        $arr_rules  =   $messages = [];
        $arr_lang   =   $this->LanguageService->get_all_language();
        $obj_template = $this->BaseModel
                             ->with("get_question_category")
                             ->where('is_active',1)
                             ->orderBy('position','asc')
                             ->get();   

        if($obj_template)
        {
            $arr_template = $obj_template->toArray();
        }  
        
        $min1 = 10;
        $min2 = 6;
        $max  = 14;
       foreach ($arr_template as $key => $template) 
       {
          if($template['id'] == 4)
          {
            $arr_rules['school_name'] = ['required','regex:/^[a-zA-Z0-9 .&]+$/'];
          }
          elseif($template['id'] == 13)
          {
            $arr_rules['email'] = ['required','email'];
          }
          elseif($template['id'] == 8)
          {
            $arr_rules['address'] = ['required'];
          }
          else
          {
            if(($template['is_required'] == 1) || ($template['validations']!=''))
            {
                 $string1= [];
                 if($template['is_required']==1)
                 {
                      /*$string1 = 'required';*/
                      array_push($string1, 'required');
                 }
                 if($template['validations']!= '')
                 {
                      $arr_validations = explode(',', $template['validations']);
                      if(!in_array('mobile_no', $arr_validations) && !in_array('telephone_no', $arr_validations) )
                      {
                        $pattern ='/^[';
                        if(in_array('letters',$arr_validations))
                        {
                          $pattern .= 'a-zA-Z';
                        }
                        if(in_array('digits',$arr_validations))
                        {
                          $pattern .= '0-9';
                        }
                        if(in_array('white_space',$arr_validations))
                        {
                          $pattern .= ' ';
                        }
                        if(in_array('hyphen',$arr_validations))
                        {
                          $pattern .= '\-';
                        }
                        if(in_array('special_symbols',$arr_validations))
                        {
                          $pattern .= '$&+,:;=?@#|\'<>^*()%!~`\"';
                        }
                        if(in_array('dot',$arr_validations))
                        {
                          $pattern .= '\.';
                        }
                        $pattern .=']+$/';

                        $string2 = "regex:".$pattern;
                        array_push($string1, $string2);
                      }
                      else
                      {
                        if(in_array('mobile_no',$arr_validations))
                        {
                          $string4 = "digits_between:10,14";
                          array_push($string1, $string4);
                        }
                        if(in_array('telephone_no',$arr_validations))
                        {
                          $string5 = "digits_between:6,14";
                          array_push($string1, $string5);
                        }
                      }
                 }
                 
                 $arr_rules[strslug($template['title'])] = $string1;
                 
             }
           }

       }
       
       $messages['required']               = translation('this_field_is_required');
       $messages['regex']                  = translation('please_enter_valid_text_format');
       $messages['email']                  = translation('please_enter_valid_email_format');
       $messages['digits_between:10,14']   = translation('please_enter_telephone_no_within_range_of').$min1.'-'.$max;
       $messages['digits_between:6,14']    = translation('please_enter_mobile_no_within_range_of').$min2.'-'.$max;
       
       $validator = Validator::make($request->all(),$arr_rules,$messages);
       
       if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $user_id     =   base64_decode($request->input('enc_id'));
        $school =   $this->SchoolAdminModel->where('user_id',$user_id)->first();         
        
        $old_image =    $request->input('old_image');
        $is_new_file_uploaded = FALSE;
        foreach ($request->all() as $key => $data) 
        {
            $temp_key = $key;
            $arr = explode('_',$temp_key);
            $id = $arr[0];
            if($request->hasFile($key))
            {

                $arr_image_size = [];
                $arr_image_size = getimagesize($data);

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

                $excel_file_name = $request->file($key);
                
                $file_extension   = strtolower($data->getClientOriginalExtension());

                if(in_array($file_extension,['png','jpg','jpeg']))
                {
                    $arr_files_name=[];
                    $file_name       = sha1(uniqid().$excel_file_name.uniqid()).'.'.$file_extension;
                    $data->move($this->user_profile_base_img_path,$file_name);
                    $old_image = $file_name;
                    $arr_files_name['file_name_'.$temp_key] = $file_name;

                    /* Unlink the Existing file from the folder */
                    $obj_profile =  $this->SchoolProfileModel
                                         ->where('school_template_id',$request->input('image_id'))
                                         ->where('school_no',$school->school_id)
                                         ->first();

                    if(isset($obj_profile->id) && ($obj_profile->id !='')) 
                    {
                        $obj_profile_translation = $this->SchoolProfileTranslationModel
                                                    ->where('school_profile_id',$obj_profile->id)
                                                    ->first();        


                            if($obj_profile_translation && $obj_profile_translation->value && $obj_profile_translation->value != "" )
                            {
                                $unlink_path    = $this->user_profile_base_img_path.$obj_profile_translation->value;
                                @unlink($unlink_path);

                                $is_new_file_uploaded = TRUE;
                            }
                                                          
                    }                    
                }
                else
                {
                    Flash::error('invalid_file_type_while_creating'.str_singular($this->module_title));
                    return redirect()->back();
                }
            }
        }

        $arr_data   =   $data   =   [];

        if($obj_template)
        {
            $arr_template = $obj_template->toArray();
        }  

        $profile_data   =   $this->SchoolProfileModel->where('school_no',$school->school_id)->get();
        
        foreach ($profile_data as $key => $data) 
        {
            $this->SchoolProfileTranslationModel->where('school_profile_id',$data->id)->delete();
            
        }
        foreach ($arr_lang as $lang) 
        {
            foreach ($profile_data as $data) 
            {
              foreach ($arr_template as $template) 
              {
                if($data->school_template_id == $request->input('image_id') && $old_image!='') 
                {
                        $translation = $data->translateOrNew($lang['locale']);
                        $translation->value                  = $old_image;
                        $translation->school_profile_id      = $data->id;
                        $translation->locale                 = $lang['locale'];
                        $translation->save();
                        continue;
                }
                $value = '';
                /*$key = $data->school_template_id;
                $temp_key = $key;
                $arr = explode('_',$temp_key);
                $temp_lang = $arr[1];*/
                if($data->school_template_id == $template['id'])
                {
                  if(!$request->hasFile($key))
                  {   
                      $value  =   trim($request->input(strslug($template['title'])));      

                  }
                  if($template['id']== 8)
                  {
                      $value  =   trim($request->input('address'));   
                  }
                  if($template['id']== 4)
                  {
                      $value  =   trim($request->input('school_name'));   
                  }
                  if($template['id']== 13)
                  {
                      $value  =   trim($request->input('email'));   
                  }
                  
                  if(isset($value))
                  {
                      $translation = $data->translateOrNew($lang['locale']);
                      $translation->value                  = $value;
                      $translation->school_profile_id      = $data->id;
                      $translation->locale                 = $lang['locale'];
                      $translation->save();
                  }
                }
              }
            }

        }
   
        if($profile_data)
        {
            Flash::success($this->school_title.' '.translation('updated_successfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_updating').' '.str_singular($this->school_title));
        }
        return redirect()->back();
    }  
   
    public function generate_school_no($school_name)
    {  
        $school_name = str_replace(' ','', $school_name);
        $school_name = str_replace('.','', $school_name);
        $school_name = str_replace('-','', $school_name);
        $school_name = str_replace('&','', $school_name);
        $school_name = trim($school_name);
        
        $school_no  =   'SC'.strtoupper(substr($school_name,0,3)).rand(10000,99999); 

        $exist      =   $this->SchoolProfileModel->where('school_no',$school_no)->count();
        if($exist>0)
        {
            return $this->generate_school_no($school_name);    
        }
        return  $school_no;
    }

    public function arrange_locale_wise(array $arr_data)
    {
        if(sizeof($arr_data)>0)
        {
            foreach ($arr_data as $key => $data) 
            {
                $arr_tmp = $data;
                unset($arr_data[$key]);

                $arr_data[$data['locale']] = $data;                    
            }

            return $arr_data;
        }
        else
        {
            return [];
        }
    } 


    public function copyTemplate($schoolNo)
    {
        
        $arr_emails = $arr_sms = [];
        $email_data = $this->EmailTemplateModel->get();
        $sms_data   = $this->SmsTemplateModel->get();
        
        if(isset($email_data) && $email_data!=null && count($email_data)>0)
        {
            $arr_emails = $email_data->toArray();
        }

        if(isset($sms_data) && $sms_data!=null && count($sms_data)>0)
        {
            $arr_sms = $sms_data->toArray();
        }

        if(count($arr_emails)>0)
        {
            foreach ($arr_emails as $key => $value) {
                    
                $arr_data = [];
                $arr_data['school_id']          = $schoolNo;
                $arr_data['slug']               = $value['slug'];
                $arr_data['is_enabled']         = $value['is_enabled'];
                $arr_data['template_name']      = $value['template_name'];
                $arr_data['template_from']      = 'School Admin';
                $arr_data['template_from_mail'] = $value['template_from_mail'];
                $arr_data['template_variables'] = $value['template_variables'];
                $arr_data['template_subject']   = $value['template_subject'];
                $arr_data['template_html']      = $value['template_html'];

                $this->SchoolEmailTemplateModel->create($arr_data);
            }    
        }

        if(count($arr_sms)>0)
        {
            foreach ($arr_sms as $key => $value) {
                $arr_data = [];
                $arr_data['school_id']           = $schoolNo;
                $arr_data['template_slug']       = $value['template_slug'];
                $arr_data['is_enabled']          = $value['is_enabled'];
                $arr_data['template_variables']  = $value['template_variables'];
                $arr_data['template_subject']    = $value['template_subject'];
                $arr_data['template_html']       = $value['template_html'];
                $this->SchoolSmsTemplateModel->create($arr_data);
            }
        }
        return true;
        
     }
    
}
