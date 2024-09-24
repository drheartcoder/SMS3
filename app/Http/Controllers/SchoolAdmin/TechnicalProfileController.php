<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\EmployeeModel;

use App\Common\Services\LanguageService;
use Validator;
use Flash;
use Sentinel;
use Hash;
use Image;

class TechnicalProfileController extends Controller
{
    public function __construct(
                                UserModel $user,
                                UserTranslationModel $translation,
                                LanguageService $language,
                                EmployeeModel $employee
                               )
    {
        $this->UserModel                     = $user;
        $this->UserTranslationModel          = $translation;
        $this->LanguageService               = $language;
        $this->EmployeeModel                 = $employee;
        $this->arr_view_data                 = [];
        $this->module_title                  = "Profile";
        $this->module_view_folder            = "schooladmin.technical_profile";
        $this->module_icon                   = "fa-user";
        $this->edit_icon                     = "fa-create-circle";
        $this->theme_color                   = theme_color();
        $this->admin_url_path                = url(config('app.project.role_slug.school_admin_role_slug'));
        $this->module_url_path               = $this->admin_url_path."/technical_profile";
        $this->profile_image_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->profile_image_base_img_path   = base_path().config('app.project.img_path.user_profile_images');
        $this->arr_view_data['image_path'] = $this->profile_image_public_img_path;
    }
    /****** Get information of admin ******************/
    public function index()
    {
        $arr_data = array();
        
        
        $obj_data        =  Sentinel::getUser();
        $obj_translation =  $this->UserModel
                                 ->where(['id'=>$obj_data->id])       
                                 ->first();    

        $obj_tech_details   =   $this->EmployeeModel
                                         ->where('user_id',$obj_data->id)
                                         ->where('school_id',\Session::get('school_id'))
                                         ->first();
        
        if($obj_data)
        {
           $arr_data['user']         = $obj_data->toArray();    
           $arr_data['user_details'] = $obj_translation->toArray();    
           $arr_data['technical_details'] = $obj_tech_details->toArray();    
        }

        if(sizeof($arr_data)<=0)
        {
            return redirect($this->admin_url_path.'/login');
        }
        $this->arr_view_data['enc_id']                        = base64_encode($obj_data->id);
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
        $arr_rules = array();
        $arr_lang   =   $this->LanguageService->get_all_language();
        $arr_rules['first_name']                        = "required";
        $arr_rules['last_name']                         = "required";
        $arr_rules['mobile_no']                         = "required|numeric";
        $arr_rules['address']                           = "required";
        $arr_rules['email']                             = "required";
        $arr_rules['telephone_no']                      = "required|numeric";
        $arr_rules['national_id']                       = "required";
        $arr_rules['gender']                            = "required";
        $arr_rules['birth_date']                        = "required";

        $messages['required']   =   'This field is required';
        $messages['email']      =   'Must be a valid email address';
        $messages['numeric']    =   'Must be a number';

        $validator = Validator::make($request->all(),$arr_rules,$messages);
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
            $file_extension = strtolower($request->file('image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('image')->move($this->profile_image_base_img_path , $file_name);
                if($isUpload)
                {
                    @unlink($this->profile_image_base_img_path.$oldImage);
                    @unlink($this->profile_image_base_img_path.'/thumb_50X50_'.$oldImage);
                    //generate thumb of profile image
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
        $arr_data['email']          = trim($request->input('email'));
        $arr_data['mobile_no']      = $request->input('mobile_no');
        $arr_data['address']        = trim($request->input('address'));
        $arr_data['city']           = trim($request->input('city'));
        $arr_data['country']        = trim($request->input('country'));
        $arr_data['telephone_no']   = $request->input('telephone_no');
        $arr_data['national_id']    = $request->input('national_id');
        $arr_data['gender']         = $request->input('gender');
        $arr_data['birth_date']     = $request->input('birth_date');
        $obj_data = $this->UserModel->where('id',$user_id)->update($arr_data);
        $technical_data['year_of_experience']  =  $request->input('year_of_experience');  
        $technical_data['qualification_degree']  =  $request->input('qualification_degree');

        $this->EmployeeModel->where('user_id',$user_id)->update($technical_data);

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

                        Flash::success(translation('record_updated_successfully'));
                    }
                }
            } 
           /*------------------------------------------------------*/
        }
        
        if($obj_data)
        {   
            Flash::success(translation('record_updated_successfully')); 
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
}
