<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\SchoolAdminModel;
use App\Models\EmployeeModel;

use App\Common\Services\LanguageService;
use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;
use App\Common\Services\CheckEmailExistanceService;
use Validator;
use Flash;
use Sentinel;
use Hash;
use Image;
use Session;
use Cookie;

class ProfileController extends Controller
{
    public function __construct(
                                UserModel $user,
                                UserTranslationModel $translation,
                                LanguageService $language,
                                SchoolAdminModel $admin,
                                EmailService $email,
                                CheckEmailExistanceService $CheckEmail,
                                CommonDataService $CommonDataService)
    {
        $this->UserModel                     = $user;
        $this->UserTranslationModel          = $translation;
        $this->LanguageService               = $language;
        $this->SchoolAdminModel              = $admin;
        $this->EmailService                  = $email;
        $this->CheckEmailExistanceService    = $CheckEmail;
        $this->CommonDataService             = $CommonDataService;
        $this->arr_view_data                 = [];
        $this->module_title                  = "profile";
        $this->module_view_folder            = "schooladmin.profile";
        $this->module_icon                   = "fa-user";
        $this->edit_icon                     = "fa-create-circle";
        $this->theme_color                   = theme_color();
        $this->admin_url_path                = url(config('app.project.role_slug.school_admin_role_slug'));
        $this->module_url_path               = $this->admin_url_path."/profile";
        $this->profile_image_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->profile_image_base_img_path   = base_path().config('app.project.img_path.user_profile_images');
        $this->EmployeeModel                 = new EmployeeModel();
        
        $this->school_admin_panel_slug       = config('app.project.role_slug.school_admin_role_slug');
        $this->arr_view_data['base_url']     = $this->profile_image_base_img_path;


        $obj_data          = Sentinel::getUser();
        
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->email             = $obj_data->email;  
            $this->user_id           = $obj_data->id;
         }
         $this->school_id  = Session::get('school_id');

    }
    /****** Get information of admin ******************/
    public function index()
    {
        $arr_data = array();
        $obj_data        =  Sentinel::getUser();

        $obj_translation =  $this->UserTranslationModel
                                 ->where(['user_id'=>$obj_data->id])       
                                 ->get();    

        $school_admin = $this->SchoolAdminModel->where('user_id',$obj_data->id)->first();
        if($obj_data)
        {
           $arr_data['user']         = $obj_data->toArray();    
           $arr_data['user_details'] = $obj_translation->toArray();
        }

        if($school_admin)
        {
            $arr_data['school_admin'] = $school_admin->toArray();
        }


        if(sizeof($arr_data)<=0)
        {
            return redirect($this->admin_url_path.'/login');
        }
        $this->arr_view_data['arr_data']                      = $arr_data;
        $this->arr_view_data['edit_mode']                     = TRUE;
        $this->arr_view_data['arr_lang']                      = $this->LanguageService->get_all_language(); 
        $this->arr_view_data['page_title']                    = $this->module_title;
        $this->arr_view_data['module_title']                  = $this->module_title;
        $this->arr_view_data['module_url_path']               = $this->module_url_path;
        $this->arr_view_data['theme_color']                   = $this->theme_color;
        $this->arr_view_data['module_icon']                   = $this->module_icon;
        $this->arr_view_data['image_path'] = $this->profile_image_public_img_path;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    /****** update information of admin ******************/
    public function update(Request $request)
    {
        $arr_rules = array();
        $arr_lang   =   $this->LanguageService->get_all_language();
      

        $arr_rules['first_name']                    = "required|regex:/^[a-zA-Z ]*$/";
        $arr_rules['last_name']                     = "required|regex:/^[a-zA-Z ]*$/"; 
        $arr_rules['mobile_no']                     = "required|numeric|digits_between:10,14";
        $arr_rules['address']                       = "required";

        $messages['required']               =   translation('this_field_is_required');
        $messages['numeric']                =   translation('please_enter_digits_only');
        $messages['digits_between:10,14']   =   translation('please_enter_mobile_no_within_range_of_10_14');
        $messages['regex']                  =   translation('please_enter_valid_text_format');
        $messages['alpha']                  =   translation('please_enter_characters_only');

        $validator = Validator::make($request->all(),$arr_rules);
   
        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $id      = $request->input('enc_id');
        $user_id = base64_decode($id);
        
        $oldImage = $request->input('oldimage');
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
            $file_name = $request->input('image');
            $file_extension = strtolower($request->file('image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('image')->move($this->profile_image_base_img_path , $file_name);
                if($isUpload)
                {
                    @unlink($this->profile_image_base_img_path.$oldImage);
                    @unlink($this->profile_image_base_img_path.'/thumb_50X50_'.$oldImage);
                    
                    $this->attachmentThumb(file_get_contents($this->profile_image_base_img_path.$file_name), $file_name, 50, 50);
                }
            }
            else
            {
                Flash::error(translation('invalid_file_type'));
                return redirect()->back();
            }
        }
        else
        {
             $file_name = $oldImage;
        }
        $arr_data   =   [];
        $arr_data['profile_image']  = $file_name;
        $arr_data['mobile_no']      = $request->input('mobile_no');
        $arr_data['address']        = trim($request->input('address'));
   
        $obj_data = $this->UserModel->where('id',$user_id)->update($arr_data);


        $status = $this->UserModel->where('id',$user_id)->first(); 
        if($status)
        {
            /* update record into translation table */
            if(sizeof($arr_lang) > 0 )
            {
                foreach ($arr_lang as $lang) 
                {            
                    $arr_data = array();
                    $first_name       = $request->input('first_name');
                    $last_name        = $request->input('last_name');
                    if( (isset($first_name)  && $first_name != '') && (isset($last_name) && $last_name != ''))
                    { 
                        $translation = $status->translateOrNew($lang['locale']);
                        $translation->first_name    = $first_name;
                        $translation->last_name     = $last_name;
                        $translation->save();

                        /*-------------------------------------------------------
                        |   Activity log Event
                        --------------------------------------------------------*/
                            $arr_event                 = [];
                            $arr_event['ACTION']       = 'EDIT';
                            $arr_event['MODULE_TITLE'] = $this->module_title;

                            $this->save_activity($arr_event);

                        /*----------------------------------------------------------------------*/

                        Flash::success(translation($this->module_title).' '.translation('updated_successfully'));
                    }
                }
            } 
           /*------------------------------------------------------*/
        }
        
        if($obj_data)
        {   
            $language = $request->input('language');
            $db_language        = $this->SchoolAdminModel->where('user_id',$user_id)->first();
            if($language)
            {
                if($language != $db_language->language)
                {
                    $this->SchoolAdminModel->where('user_id',$user_id)->update(['language'=>$language]);
                    \Session::flush();
                    \Sentinel::logout();
                    return redirect(url($this->school_admin_panel_slug));
                }
            }
            Flash::success(translation($this->module_title).' '.translation('updated_successfully')); 
        }
        else
        {
            Flash::error(translation('problem_occured_while_updating_this_record'));  
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

        $date = date('Y-m-d H:i:s');
        $expiry_date   = date('Y-m-d H:i:s',strtotime($date. ' + 24 hours'));
        $user          = Sentinel::check();

        if($user)
        {
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

            $link = \URL::to(config('app.project.school_admin_panel_slug').'/email_change/'.base64_encode($arr_data['id']));
                
            $reminder_url = '<p class="email-button"> <a target="_blank" href="'.$link.'">Click Here</a></p><br/>';
           
            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'EMAIL'            => $arr_data['email'],
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
        setcookie('locale',$language,time()+(3600*24*2));
          //  dump($language,123);
        $user = Sentinel::check();        
        if($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
        {
            $db_language        = $this->SchoolAdminModel->where('user_id',$this->user_id)->first();       
        }
        else{
            $db_language        = $this->EmployeeModel
                                                    ->where('user_id',$this->user_id)
                                                    ->where('school_id',$this->school_id)
                                                    ->where('has_left',0)
                                                    ->first();          
        }
        if($language)
        {
           // dump($db_language->language,$language); 
            /*if(isset($db_language->language) && $language != $db_language->language)
            {*/
             
                if($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
                {
                 
                  $res =   $this->SchoolAdminModel->where('user_id',$this->user_id)->update(['language'=>$language]);
                 // dd($res);
                }
                else{
                    $db_language        = $this->EmployeeModel
                                                    ->where('user_id',$this->user_id)
                                                    ->where('school_id',$this->school_id)
                                                    ->where('has_left',0)
                                                    ->update(['language'=>$language]);

                     
                } 

                $lang =  \Session::get('locale');
                \Session::flush();
                Sentinel::logout();
                \Session::put('locale',$language);
                return redirect(url('/').'/login');
                //return redirect(url($this->school_admin_panel_slug));
            //}
        }
    }
}
