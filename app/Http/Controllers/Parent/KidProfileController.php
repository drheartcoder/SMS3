<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\UserModel;
use App\Models\LevelModel;
use App\Models\ClassModel;
use App\Models\CourseModel;
use App\Models\StudentModel;
use App\Models\LevelClassModel;
use App\Models\UserTranslationModel;
use App\Common\Services\LanguageService;




use Hash;
use Image;
use Flash;
use Session;
use Sentinel;
use Validator;

class KidProfileController extends Controller
{
	public function __construct()
    {
 	
 		$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.parent_panel_slug')).'/kid_profile';
        $this->module_title                 = translation('kid_profile');
 		$this->module_view_folder           = "parent.kid_profile";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-user';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-user';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->level_class_id               = Session::get('level_class_id');
        $this->level_id                     = Session::get('student_level');
        $this->class_id                     = Session::get('student_class');
        $this->kid_id                	    = Session::get('kid_id');

        $this->UserModel                    = new UserModel();
        $this->LevelModel                   = new LevelModel();
        $this->ClassModel                   = new ClassModel();
        $this->CourseModel                  = new CourseModel();
        $this->StudentModel                 = new StudentModel();
        $this->LanguageService              = new LanguageService();
        $this->UserTranslationModel         = new UserTranslationModel();
		
		$this->arr_view_data['page_title']      = translation('timetable');
    	$this->arr_view_data['module_url_path'] = $this->module_url_path;
    	$this->arr_view_data['theme_color']     = $this->theme_color;
    	$this->arr_view_data['module_icon']     = $this->module_icon;
    	$this->arr_view_data['edit_icon']       = $this->edit_icon;
    	$this->arr_view_data['create_icon']     = $this->create_icon;
        $this->weekly_days = config('app.project.week_days');

    	$obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;
        }

        if(\Session::has('locale'))
        {
            $this->locale = \Session::get('locale');
        }
        else
        {
            $this->locale = 'en';
        }
  	
  		$this->profile_image_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->profile_image_base_img_path   = base_path().config('app.project.img_path.user_profile_images');
    }


    /*
    | index()       : Edit Student Profile
    | Auther        : Padmashri Joshi
    | Date          : 13 July 2018
    */

    public function index()
    {
       
        $id 	  = $this->kid_id;
        $arr_data = array();
        $obj_data = Sentinel::getUser();
        $obj_data = $this->UserModel->with(['student_details'=>function($query)use($id){
                                             $query->where('user_id','=',$id);
                                             $query->whereHas('get_level_class',function($q){
                                             		$q->select('level_class_id','id');
                                             });
											 $query->with(['get_level_class.get_level','get_level_class.get_class']);
                                     }])
                                    ->where(['id'=>$this->kid_id])
                                    ->first();    
        if($obj_data){
            $arr_data = $obj_data->toArray();
        } 
        $this->arr_view_data['edit_mode']                     = TRUE;
        $this->arr_view_data['arr_data']                      = $arr_data;
        $this->arr_view_data['page_title']                    = $this->module_title;
        $this->arr_view_data['module_title']                  = translation('edit').' '.$this->module_title;
        $this->arr_view_data['module_url_path']               = $this->module_url_path;
        $this->arr_view_data['theme_color']                   = $this->theme_color;
        $this->arr_view_data['module_icon']                   = $this->module_icon;
        $this->arr_view_data['arr_lang']                      = $this->LanguageService->get_all_language(); 
        $this->arr_view_data['image_path'] 					  = $this->profile_image_public_img_path;
		return view($this->module_view_folder.'.edit_profile',$this->arr_view_data);
    }



    /*
    | index()       : Edit Student Profile
    | Auther        : Padmashri Joshi
    | Date          : 13 July 2018
    */
    public function update(Request $request)
    {
        $arr_rules = array();
        $arr_lang   =   $this->LanguageService->get_all_language();
       
        $arr_rules['first_name']   = "required|alpha";
        $arr_rules['last_name']    = "required|alpha"; 
        $arr_rules['mobile_no']    = "required|numeric|digits_between:10,14";
        $arr_rules['address']      = "required";
        $arr_rules['email']        = "required|email";
        $arr_rules['student_no']   = "required";
        $arr_rules['national_id']  = "required|regex:/^[a-zA-Z0-9]*$/";
        $arr_rules['birth_date']   = "required|date";
        $arr_rules['gender']       = "required";
        $arr_rules['parent_national_id'] = "required";
        $arr_rules['telephone_no'] = "required|digits_between:6,14";

        $messages = array(
                                'email'                => translation('please_enter_valid_email'),
                                'numeric'              => translation('please_enter_digits_only'),
                                'mobile_no.digits_between' => translation('please_enter_mobile_no_within_range_of_10_14'),
                                'telephone_no.digits_between'  => translation('please_enter_telephone_no_within_range_of_6_14'),
                                'required'             => translation('this_field_is_required'),
                                'alpha'                => translation('please_enter_letters_only')

                            );  

       

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $user_id = $this->kid_id;
        $oldImage = $request->input('oldimage');
        if($request->hasFile('image'))
        {
            $arr_image_size = [];
            $arr_image_size = getimagesize($request->file('image'));

            if(!$arr_image_size)
            {
                Flash::error(translation('not_valid_image_please_select_proper_image_format'));
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

        $arr_data['profile_image']          = $file_name;
        $arr_data['email']                  = trim($request->input('email'));
        $arr_data['mobile_no']              = $request->input('mobile_no');
        $arr_data['address']                = trim($request->input('address'));
        $arr_data['city']                   = trim($request->input('city'));
        $arr_data['country']                = trim($request->input('country'));
        $arr_data['national_id']            = trim($request->input('national_id'));
        $arr_data['nationality_id']         = trim($request->input('nationality_id'));
        $arr_data['birth_date']             = date('Y-m-d',strtotime($request->input('birth_date')));
        $arr_data['gender']                 = strtoupper(trim($request->input('gender')));
        $arr_data['latitude']               = trim($request->input('latitude'));
        $arr_data['longitude']              = trim($request->input('longitude'));
        $arr_data['telephone_no']           = trim($request->input('telephone_no'));
        
        $student_data['parent_national_id'] = trim($request->input('parent_national_id'));
        $student_data['student_no']         = trim($request->input('student_no'));

        $this->StudentModel->where('user_id',$user_id)->update($student_data);

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
                    $special_note     = $request->input('special_note');
                    if( (isset($first_name)  && $first_name != '') && (isset($last_name) && $last_name != '') && (isset($special_note) && $special_note != ''))
                    { 
                        $translation = $status->translateOrNew($lang['locale']);
                        $translation->first_name    = $first_name;
                        $translation->last_name     = $last_name;
                        $translation->special_note  = $special_note;
                        $translation->save();

                        /*-------------------------------------------------------
                        |   Activity log Event
                        --------------------------------------------------------*/
                            $arr_event                 = [];
                            $arr_event['ACTION']       = 'EDIT';
                            $arr_event['MODULE_TITLE'] = $this->module_title;

                            $this->save_activity($arr_event);

                        /*----------------------------------------------------------------------*/
						Flash::success(translation('kid_profile_updated_successfully'));
                    }
                }
            } 
           /*------------------------------------------------------*/
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
