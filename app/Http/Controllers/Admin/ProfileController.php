<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;
use App\Models\UserTranslationModel;

use App\Common\Services\LanguageService;
use Validator;
use Flash;
use Sentinel;
use Hash;
use Image;

class ProfileController extends Controller
{
    public function __construct(
                                UserModel $user,
                                UserTranslationModel $translation,
                                LanguageService $language
                               )
    {
        $this->UserModel                     = $user;
        $this->UserTranslationModel          = $translation;
        $this->LanguageService               = $language;
        $this->arr_view_data                 = [];
        $this->module_title                  = translation("profile");
        $this->module_view_folder            = "admin.profile";
        $this->module_icon                   = "fa-user";
        $this->theme_color                   = theme_color();
        $this->admin_url_path                = url(config('app.project.admin_panel_slug'));
        $this->module_url_path               = $this->admin_url_path."/profile";
        $this->profile_image_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->profile_image_base_img_path   = base_path().config('app.project.img_path.user_profile_images');
    }
    /****** Get information of admin ******************/
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
        
        $obj_translation =  $this->UserTranslationModel
                                 ->where(['user_id'=>$obj_data->id])       
                                 ->get();    
        if($obj_data)
        {
           $arr_data['user']         = $obj_data->toArray();    
          /* $arr_data['user_details'] = $obj_translation->toArray();    */
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
        $this->arr_view_data['profile_image_public_img_path'] = $this->profile_image_public_img_path;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    /****** update information of admin ******************/
    public function update(Request $request)
    {   

        $str_email = 'email';
        $str_image = 'image';
        $arr_rules = array();
        $arr_lang   =   $this->LanguageService->get_all_language();
        
        $arr_rules['first_name']                    = "required|regex:/^[a-zA-Z]+$/";
        $arr_rules['last_name']                     = "required|regex:/^[a-zA-Z]+$/";
        $arr_rules['mobile_no']                     = "required|numeric|digits_between:10,14";
        $arr_rules['address']                       = "required";
        $arr_rules[$str_email]                      = "required|email";

        $messages['required']                       = translation('this_field_is_required');
        $messages['mobile_no.digits_between']       = translation('please_enter_telephone_no_within_range_of').'10'.'-'.'14';
        $messages['email']                          = translation('please_enter_valid_email_format');
        $messages['email.regex']                    = translation('please_enter_valid_email_format');
        $messages['numeric']                        = translation('please_enter_digits_only');


        $validator = Validator::make($request->all(),$arr_rules,$messages);
       
        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        else
        {
            $id      = $request->input('enc_id');
            $user_id = base64_decode($id);
            
            $oldImage = $request->input('oldimage');
            if($request->hasFile($str_image))
            {
                $arr_image_size = [];
                $arr_image_size = getimagesize($request->file($str_image));

                if(isset($arr_image_size) && $arr_image_size==false)
                {
                    Flash::error('Please use valid image'); 
                }
                else
                {
                    $minHeight = 250;
                    $minWidth  = 250;
                    $maxHeight = 2000;
                    $maxWidth  = 2000;

                    if(($arr_image_size[0] < $minWidth || $arr_image_size[0] > $maxWidth) && ($arr_image_size[1] < $minHeight || $arr_image_size[1] > $maxHeight))
                    {
                        
                        Flash::error(translation('please_upload_image_with_height_and_width_greater_than_or_equal_to_250_x_250_less_than_or_equal_to_2000_x_2000_for_best_result'));
                        return redirect()->back();
                    }
                    else
                    {
                        $file_name = $request->file($str_image);
                        $file_extension = strtolower($request->file($str_image)->getClientOriginalExtension());
                        if(in_array($file_extension,['png','jpg','jpeg']))
                        {
                            $file_name = time().uniqid().'.'.$file_extension;
                            $isUpload = $request->file($str_image)->move($this->profile_image_base_img_path , $file_name);
                            if($isUpload)
                            {
                                @unlink($this->profile_image_base_img_path.$oldImage);
                                @unlink($this->profile_image_base_img_path.'/thumb_50X50_'.$oldImage);
                                $this->attachmentThumb(file_get_contents($this->profile_image_base_img_path.$file_name), $file_name, 50, 50);
                            }
                        }
                        else
                        {
                            Flash::error(translation('invalid_file_type_while_updating').' '.str_singular($this->module_title));
                            return redirect()->back();
                        }
                    }
                }
            }
            else
            {
                 $file_name = $oldImage;
            }
            $arr_data   =   [];
            $arr_data['profile_image']  = $file_name;
            $arr_data[$str_email]          = trim($request->input($str_email));
            $arr_data['mobile_no']      = $request->input('mobile_no');
            $arr_data['address']        = trim($request->input('address'));
            $obj_data = $this->UserModel->where('id',$user_id)->update($arr_data);

            $status = $this->UserModel->where('id',$user_id)->first(); 
            if($status && sizeof($arr_lang) > 0 )
            {
                /* update record into translation table */
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
                        Flash::success(str_singular($this->module_title).' '.translation('updated_successfully'));
                    }
                }
                
               /*------------------------------------------------------*/
            }
            
            if($obj_data)
            {   
                Flash::success(str_singular($this->module_title).' '.translation('updated_successfully')); 
            }
            else
            {
                Flash::error(translation('problem_occurred_while_updating').' '.str_singular($this->module_title));  
            }
            /*-------------------------------------------------------
            |   Activity log Event
            --------------------------------------------------------*/
                $arr_event                 = [];
                $arr_event['ACTION']       = 'EDIT';
                $arr_event['MODULE_TITLE'] = $this->module_title;

                Controller::save_activity($arr_event);

            /*----------------------------------------------------------------------*/
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
}
