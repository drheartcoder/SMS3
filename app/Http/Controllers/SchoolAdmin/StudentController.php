<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;

use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\StudentModel;
use App\Models\UserRoleModel;   
use App\Models\RoleModel;
use App\Models\ActivationModel;
use App\Models\LevelModel;
use App\Models\LevelTranslationModel;
use App\Models\LevelClassModel;
use App\Models\ClassModel;
use App\Models\ClassTranslationModel;
use App\Models\AcademicYearModel;   
use App\Models\DocumentsModel;
use App\Common\Services\CommonDataService;
use App\Common\Services\StudentService;
use App\Common\Services\LanguageService;
use App\Models\EducationalBoardModel;

use App\Models\ParentModel;
/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
/*Activity Log */

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;
use PDF;
use Image;

class StudentController extends Controller
{
   
    public function __construct(    
                                    UserModel $user,
                                    UserTranslationModel $translation,
                                    UserRoleModel $user_role_model,
                                    RoleModel $role_model,
                                    ActivityLogsModel $activity_logs,
                                    StudentModel $student,
                                    LevelModel $level,
                                    LevelTranslationModel $levelTranslation,
                                    ClassModel $class,
                                    ClassTranslationModel $classTranslation,
                                    ParentModel $parent,
                                    LevelClassModel $LevelClassModel,
                                    AcademicYearModel $year,
                                    CommonDataService $common_data_service,
                                    DocumentsModel $documents,
                                    StudentService $StudentService,
                                    LanguageService $LanguageService

                                )
    {
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->StudentModel                 = $student;
        $this->BaseModel                    = $this->StudentModel;
        $this->ActivityLogsModel            = $activity_logs; /* Activity Model */   
        $this->LevelModel                   = $level;
        $this->AcademicYearModel            = $year;
        $this->LevelTranslationModel        = $levelTranslation;
        $this->ParentModel                  = $parent;
        $this->ClassModel                   = $class;
        $this->ClassTranslationModel        = $classTranslation;
        $this->LevelClassModel              = $LevelClassModel;
        $this->CommonDataService            = $common_data_service;
        $this->DocumentsModel               = $documents;
        $this->EducationalBoardModel        = new EducationalBoardModel();
        $this->StudentService               = $StudentService;
        $this->LanguageService              = $LanguageService;

        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->student_document_base_img_path      = public_path().config('app.project.img_path.student_documents');
        $this->student_document_public_img_path     = url('/').config('app.project.img_path.student_documents');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')."/student");
        
        $this->module_title                 = translation("student");
        $this->modyle_url_slug              = translation("student");

        $this->module_view_folder           = "schooladmin.student";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = "fa fa-user";
        $this->first_name = $this->last_name =$this->ip_address ='';

        $this->arr_view_data['page_title']      = translation("student");
        $this->arr_view_data['base_url']      = $this->user_profile_base_img_path;
        $this->arr_view_data['image_path']      = $this->user_profile_public_img_path;


        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->ip_address        = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;  
        }
        /* Activity Section */
        $this->school_id             = \Session::get('school_id');
        $this->academic_year         = \Session::get('academic_year');


    }   

    public function index(Request $request)
    {   
        $this->arr_view_data['role']            = config('app.project.role_slug.student_role_slug');
        
        $this->arr_view_data['module_title']    = ucfirst(translation("manage"))." ".strtolower(str_plural($this->module_title));
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    function get_users_details(Request $request,$type,$fun_type='')
    {     

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }

        $start_id    = $this->AcademicYearModel
                              ->where(['start_date'=>($this->AcademicYearModel
                                                           ->where('school_id',$this->school_id)
                                                           ->min('start_date'))
                                      ])
                              ->first(['id']);

        $user_details             = $this->UserModel->getTable();
        $prefixed_user_details    = DB::getTablePrefix().$this->UserModel->getTable();

        $level_class          = $this->LevelClassModel->getTable();

        $level_details          = $this->LevelModel->getTable();
        $prefixed_level_table = DB::getTablePrefix().$this->LevelModel->getTable();

        $level_trans               = $this->LevelTranslationModel->getTable();

        $class_trans               = $this->ClassTranslationModel->getTable();
        $prefixed_class_trans_table      = DB::getTablePrefix().$this->ClassTranslationModel->getTable();

        $student_table               = $this->StudentModel->getTable();

        $user_trans_table             = $this->UserTranslationModel->getTable();                  
        $prefixed_user_trans_table    = DB::getTablePrefix().$this->UserTranslationModel->getTable();

        $documents                    = $this->DocumentsModel->getTable();

        $obj_user = DB::table($student_table)
                                ->select(DB::raw($student_table.".id as id,".
                                                 $prefixed_user_details.".email as email, ".
                                                 $student_table.".is_active as is_active, ".
                                                 $student_table.".academic_year_id as academic_year_id, ".
                                                 $student_table.".student_no, ".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
                                                          .$prefixed_user_trans_table.".last_name) as user_name,".
                                                 $level_trans.".level_name as level_name, ".
                                                 $class_trans.".class_name as class_name, ".
                                                 $prefixed_user_details.".national_id,".
                                                 $student_table.".has_left,".
                                                 $student_table.".user_id"        
                                                 ))
                                ->whereNull($student_table.'.deleted_at')
                                ->join($user_details,$student_table.'.user_id', ' = ',$user_details.'.id')
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                ->join($level_class,$student_table.'.level_class_id', ' = ',$level_class.'.id')
                                ->join($level_trans,$level_trans.'.level_id', ' = ',$level_class.'.level_id')
                                ->join($class_trans,$class_trans.'.class_id', ' = ',$level_class.'.class_id')
                                ->where($user_trans_table.'.locale','=',$locale)
                                ->where($level_trans.'.locale','=',$locale)
                                ->where($class_trans.'.locale','=',$locale)
                                ->where($student_table.'.school_id',$this->school_id)
                                ->where($student_table.'.academic_year_id',$this->academic_year)
                                ->orderBy($student_table.'.created_at','DESC');
                                
        /* ---------------- Filtering Logic ----------------------------------*/                    

        if($fun_type=='export'){
            $search_term = $request->input('search');
        }else{
            $search = $request->input('search');
            $search_term = $search['value'];
        }
        if($request->has('search') && $search_term!="")
        {
            $obj_user = $obj_user ->WhereRaw("( (".$user_details.".email LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$user_details.".national_id LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$level_trans.".level_name LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$class_trans.".class_name LIKE '%".$search_term."%') ")
                                 ->orWhereRaw(" ( CONCAT(".$prefixed_user_trans_table.".first_name,' ',".$prefixed_user_trans_table.".last_name)  LIKE  '%".$search_term."%' ) )");                         
        }

        if($fun_type=="export"){
            return $obj_user->get();
        }else{

            return $obj_user;
        }
    }


    public function get_records(Request $request,$type='')
    {

        $arr_current_user_access =[];
        $role = Session::get('role');
        $arr_current_user_access = $this->CommonDataService->current_user_access();
       
        $obj_user        = $this->get_users_details($request,$type);

        $role = 'student';


        $current_context = $this;

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('users.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data) use ($current_context)
                            {
                                return base64_encode($data->id);
                            });
        }                    

        $json_result     = $json_result
                            ->editColumn('has_left',function($data)use ($arr_current_user_access){
                                if($data->has_left==1){
                                    $left =  $this->module_url_path.'/not_left/'.base64_encode($data->id);
                                    return '<a href="javascript:void(0)" title="'.translation('access_denied').'" onclick="checkExistance('.$data->user_id.','.$data->id.')"><i class="fa fa-check"></i></a>';
                                }
                                else{
                                    $left =  $this->module_url_path.'/has_left/'.base64_encode($data->id);
                                    return '<a href="'.$left.'" title="'.translation('change_status').'" onclick="return confirm_action(this,event,\''.translation('is_this_user_really_left_from_the_school').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-times"></i></a>';
                                }
                            })  
                            ->editColumn('build_action_btn',function($data) use ($current_context,$role,$arr_current_user_access)
                            {
                                $build_view_action = $build_status_btn  = $build_promote_btn = $build_edit_btn = $build_delete_action =$build_payment_btn=''; 
                                if($role != null)
                                {       
                                    
 
                                    $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                    $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>';
                                    if(array_key_exists('student.update',$arr_current_user_access)){
                                        $promote_href = $this->module_url_path.'/promote_students/'.base64_encode($data->id);
                                        $build_promote_btn = '<a class="light-blue-color" href="'.$promote_href.'" title="'.translation('promote').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_promote_this_student').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-graduation-cap" ></i></a>';    
                                    }
                                    

                                    if(array_key_exists('payment.list',$arr_current_user_access))
                                    {
                                        $payment_href = url(config('app.project.school_admin_panel_slug')).'/payment/'.base64_encode($data->id);
                                        $build_payment_btn = '<a class="pink-color" href="'.$payment_href.'" title="'.translation('payment').'"><i class="fa fa-money" ></i></a>';
                                    }  
                                      
                                    if(array_key_exists('student.delete',$arr_current_user_access))
                                    {
                                        $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                        $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                    }   

                                    if(array_key_exists('student.update',$arr_current_user_access))
                                    {
                                        if($data->is_active != null && $data->is_active == "0")
                                        {   
                                            $build_status_btn = '<a class="blue-color" title="'.translation('activate').'" href="'.$this->module_url_path.'/activate/'.base64_encode($data->id).'" 
                                            onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-lock"></i></a>';
                                        }
                                        elseif($data->is_active != null && $data->is_active == "1")
                                        {
                                            $build_status_btn = '<a class="light-blue-color" title="'.translation('deactivate').'" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-unlock"></i></a>';
                                        }
                                        $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                        $build_edit_btn = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';
                                    }    
                                    
                                    return $build_status_btn.'&nbsp;'.$build_edit_btn.'&nbsp;'.$build_view_action.'&nbsp;'.$build_delete_action.'&nbsp;'.$build_promote_btn.'&nbsp;'.$build_payment_btn;  
                                }
                            })
                            ->editColumn('build_status',function($data){
                           
                                if($data->has_left==0)
                                {
                                    $build_status = "Studying";
                                } 
                                else
                                {
                                    $build_status = "Passed Out";
                                }
                                
                            return $build_status;
                            })
                             ->editColumn('build_checkbox',function($data){
                           
                                $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                
                            return $build_checkbox;
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    public function view($enc_id)
    {   
        $id = base64_decode($enc_id);
        
        if(is_numeric($id)){
            if(Session::has('locale'))
            {
                $locale = Session::get('locale');
            }
            else
            {
                $locale = 'en';
            }
            $academic_year = $this->academic_year;
            $student_details     =   $this->StudentModel
                                        ->with("get_parent","get_parent_details","get_user_details")
                                        ->with(["get_level_class"=>function($q){
                                            $q->with("get_level");
                                            $q->with("get_class");
                                        },
                                            "get_documents"=>function($q)use($academic_year){
                                                $q->select('id','student_id','document_name',
                                                    'document_title');
                                                $q->where('academic_year_id',$academic_year);
                                            }
                                        ])
                                        ->where('id',$id)
                                        ->first();

            $arr_student_details = [];
            if($student_details)
            {
                $arr_student_details = $student_details->toArray();
                $this->arr_view_data['role']                         = 'student';
                $this->arr_view_data['module_title']                 = ucfirst(translation("view")).' '.strtolower(translation('student'));
                $this->arr_view_data['module_url_path']              = $this->module_url_path;
                $this->arr_view_data['arr_data']                     = $arr_student_details;
                $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
                $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;
                $this->arr_view_data['theme_color']                  = $this->theme_color;
                $this->arr_view_data['module_icon']                  = $this->module_icon;
                $this->arr_view_data['student_document_base_img_path']  = $this->student_document_base_img_path;
                $this->arr_view_data['student_document_public_img_path']  = $this->student_document_public_img_path;

                return view($this->module_view_folder.'.view', $this->arr_view_data);
            }
            Flash::error(translation('no_data_available'));
            return redirect($this->module_url_path);
        }
        else{
            Flash::error(translation('something_went_wrong'));
            return redirect($this->module_url_path);
        }
    }

    public function edit($enc_id=FALSE){
        if($enc_id){
            $id = base64_decode($enc_id);
            if(!is_numeric($id)){
                Flash::error(translation('something_went_wrong'));
            }
            else{

                $arr_boards = [];
                $obj_boards = $this->EducationalBoardModel->where('school_id',$this->school_id)->get();
                if($obj_boards)
                {
                    $arr_boards = $obj_boards->toArray();
                }

                $obj_details = $this->StudentService->get_student_details($id);
                
                if($obj_details && count($obj_details)>0 ){

                    $arr_student_details = $obj_details->toArray();

                    $arr_levels = [];

                    $obj_levels = $this->CommonDataService->get_levels($this->academic_year);
                                           
                    if($obj_levels)
                    {
                        $arr_levels = $obj_levels->toArray();
                    }

                    $arr_class=[];

                    $obj_class = $this->CommonDataService->get_class($arr_student_details['get_level_class']['level_id']);
                                           
                    if($obj_class) 
                    {
                        $arr_class = $obj_class->toArray();
                    }
                    $pickup['latitude']='';
                    $pickup['longitude']='';
                    $drop['latitude']='';
                    $drop['longitude']='';

                    if(isset($arr_student_details['pickup_location']) && $arr_student_details['pickup_location']!='' ){
                        $pickup = json_decode($arr_student_details['pickup_location'],true);
                    }
                    if(isset($arr_student_details['drop_location']) && $arr_student_details['drop_location']!='' ){
                        $drop = json_decode($arr_student_details['drop_location'],true);
                    }

                    $this->arr_view_data['module_title']                 = ucfirst(translation("view")).' '.strtolower(translation('student'));
                    $this->arr_view_data['module_url_path']              = $this->module_url_path;
                    $this->arr_view_data['arr_data']                     = $arr_student_details;
                    $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
                    $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;
                    $this->arr_view_data['theme_color']                  = $this->theme_color;
                    $this->arr_view_data['module_icon']                  = $this->module_icon;
                    $this->arr_view_data['enc_id']                       = $enc_id;
                    $this->arr_view_data['arr_levels']                   = $arr_levels;
                    $this->arr_view_data['arr_class']                    = $arr_class;
                    $this->arr_view_data['pickup']                       = $pickup;
                    $this->arr_view_data['drop']                         = $drop;

                    return view($this->module_view_folder.'.edit', $this->arr_view_data);
                }    
                else{
                    Flash::error(translation('something_went_wrong')); 
                }
            }  
        }
        else{
            Flash::error(translation('something_went_wrong'));
        }    
        return redirect($this->module_url_path);
    }
    public function update(Request $request,$enc_id)
    {

    
        $arr_rules = array();
        $arr_lang   =   $this->LanguageService->get_all_language();
       
        $arr_rules['first_name']   = "required|alpha";
        $arr_rules['last_name']    = "required|alpha"; 
        $arr_rules['mobile_no']    = "required|numeric|digits_between:10,14";
        $arr_rules['address']      = "required";
        $arr_rules['email']        = "required|email";
        $arr_rules['national_id']  = "required|regex:/^[a-zA-Z0-9]*$/";
        
        $arr_rules['birth_date']   = "required|date";
        $arr_rules['gender']       = "required";
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

        $student_id = base64_decode($enc_id);
        $oldImage = $request->input('oldimage');

        if($request->hasFile('image'))
        {

            $arr_image_size = [];
            $arr_image_size = getimagesize($request->file('image'));

            if(!$arr_image_size)
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
                $isUpload = $request->file('image')->move($this->user_profile_base_img_path , $file_name);
                if($isUpload)
                {
                    @unlink($this->user_profile_base_img_path.$oldImage);
                    @unlink($this->user_profile_base_img_path.'/thumb_50X50_'.$oldImage);
                
                    $this->attachmentThumb(file_get_contents($this->user_profile_base_img_path.$file_name), $file_name, 50, 50);
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
     
        $pickup_address = $drop_address = $pickup_location=$drop_location="";

        if($request->has('bus_transport') && $request->input('bus_transport')=='yes')
        {
            if($request->has('pickup_latitude') && $request->has('pickup_longitude'))
            {
                $pickup_location = array("latitude"=>$request->input('pickup_latitude'),
                                      "longitude"=>$request->input('pickup_longitude'));   
                $pickup_location = json_encode($pickup_location); 

                
                $pickup_address = $request->input('pickup_address');
            }
            if($request->has('drop_latitude') && $request->has('drop_longitude'))
            {
                $drop_location = array("latitude"=>$request->input('drop_latitude'),
                                      "longitude"=>$request->input('drop_longitude'));
                $drop_location = json_encode($drop_location);        

                $drop_address = $request->input('drop_address');
            }
        }

        $student_data = [];
        $student_data['bus_transport']         =   ($request->input('bus_transport') == 'yes') ? 1 : 0;
        $student_data['pickup_location']       =   $pickup_location;
        $student_data['drop_location']         =   $drop_location;
        $student_data['pickup_address']        =   $pickup_address;
        $student_data['drop_address']          =   $drop_address;

        $this->StudentModel->where('id',$student_id)->update($student_data);

        $student = $this->StudentModel->where('id',$student_id)->first();

        if($student && count($student)>0){
            $obj_data = $this->UserModel->where('id',$student->user_id)->update($arr_data);  
            $status = $this->UserModel->where('id',$student->user_id)->first(); 

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

                        if( (isset($first_name)  && $first_name != '') && (isset($last_name) && $last_name != ''))
                        { 
                            $translation = $status->translateOrNew($lang['locale']);
                            $translation->first_name    = trim($first_name);
                            $translation->last_name     = trim($last_name);
                            $translation->special_note  = trim($special_note);
                            $translation->save();

                            /*-------------------------------------------------------
                            |   Activity log Event
                            --------------------------------------------------------*/
                                $arr_event                 = [];
                                $arr_event['ACTION']       = 'EDIT';
                                $arr_event['MODULE_TITLE'] = $this->module_title;

                                $this->save_activity($arr_event);

                            /*----------------------------------------------------------------------*/

                            Flash::success('Profile Updated successfully.');
                        }
                    }
                } 
               /*------------------------------------------------------*/
            }
            
            if($obj_data)
            {   
                $language           = trim($request->input('language'));
                $db_language        = $this->StudentModel->where('user_id',$student_id)->first();
                if($language)
                {
                    if($language != $db_language->language)
                    {
                        $this->StudentModel->where('user_id',$student_id)->update(['language'=>$language]);    
                        \Session::flush();
                        \Sentinel::logout();
                        return redirect(url($this->student_url_path));
                    }
                }
                 Flash::success('Profile Updated successfully.');
            }
            else
            {
                Flash::error(translation('problem_occured_while_updating_this_record'));  
            }  
        }
        else{
            Flash::error(translation('something_went_wrong'));  
        }
      
        return redirect()->back();
    }
    public function multi_action(Request $request)
    {
        $arr_rules = array();
        $arr_rules['multi_action'] = "required";
        $arr_rules['checked_record'] = "required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            Flash::error(translation('please_select_record_to_perform_multiaction'));
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $multi_action = $request->input('multi_action');
        $checked_record = $request->input('checked_record');

        /* Check if array is supplied*/
        if(is_array($checked_record) && sizeof($checked_record)<=0)
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction') );
            return redirect()->back();
        }

        foreach ($checked_record as $key => $record_id) 
        {  
            if($multi_action=="delete")
            {
               $this->perform_delete(base64_decode($record_id));    
               Flash::success($this->module_title.' '.translation('deleted_succesfully')); 
            } 
            elseif($multi_action=="activate")
            {
               $this->perform_activate(base64_decode($record_id)); 
               Flash::success($this->module_title.' '.translation('activated_successfully')); 
            }
            elseif($multi_action=="deactivate")
            {
               $this->perform_deactivate(base64_decode($record_id));    
               Flash::success($this->module_title.' '.translation('deactivated_successfully'));  
            }
            elseif($multi_action=="promote")
            {
                if($this->perform_promote_students(base64_decode($record_id)))
                {
                    Flash::success($this->module_title.' '.translation('promoted_successfully'));      
                }
                else
                {
                    Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction') );
                }
                
            }
        }

        return redirect()->back();
    }

    public function activate($enc_id = FALSE)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_activate(base64_decode($enc_id)))
        {
            Flash::success($this->module_title.' '.translation('activated_successfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('activation'));
        }

        return redirect()->back();
    }

    public function deactivate($enc_id = FALSE)
    {
        
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_deactivate(base64_decode($enc_id)))
        {
            Flash::success($this->module_title.' '.translation('deactivated_successfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('deactivation'));
        }

        return redirect()->back();
    }

    public function delete($enc_id = FALSE)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_delete(base64_decode($enc_id)))
        {
            Flash::success($this->module_title.' '.translation('deleted_succesfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction'));
        }

        return redirect()->back();
    }


    public function perform_activate($id)
    {
        $static_page = $this->BaseModel->where('id',$id)->first();
        
        if($static_page)
        {

            return $static_page->update(['is_active'=>1]);
        }

        return FALSE;
    }

    public function perform_deactivate($id)
    { 
        $static_page = $this->BaseModel->where('id',$id)->first();
        
        if($static_page)
        {
            return $static_page->update(['is_active'=>0]);
        }

        return FALSE;
    }

    public function perform_delete($id)
    {
        $delete= $this->BaseModel->where('id',$id)->delete();

        
        if($delete)
        {
            return TRUE;
        }

        return FALSE;
    }
    public function perform_promote_students($id)
    {
        $current_academic_year_id = $this->CommonDataService->get_current_academic_id();

        $old_student = StudentModel::find($id);

        if(isset($old_student->academic_year_id) && $old_student->academic_year_id== $current_academic_year_id)
        {
            return FALSE;
        }
        else
        {

            if(isset($old_student->level_class_id) && $old_student->level_class_id!=0)
            {
                $obj_level_class = $this->LevelClassModel->where('id',$old_student->level_class_id)
                                      ->first();

                if(isset($obj_level_class->id))
                {
                    $obj_level = $this->LevelModel->where('id',$obj_level_class->level_id)
                                                  ->first();
                    if(isset($obj_level->id))
                    {
                        $level_order = $this->LevelModel
                                                    ->where('level_order',($obj_level->level_order+1))
                                                    ->first();
                        if($level_order)
                        {

                            $obj_level_class = $this->LevelClassModel->where('level_id',$level_order->id);
                                                                     if(isset($obj_level_class->class_id) &&   $obj_level_class->class_id!=0)
                                                                     {
                                                                        $obj_level_class = $obj_level_class->orWhere('class_id',$obj_level_class->class_id);  
                                                                     }
                                                                     
                                                                     $obj_level_class = $obj_level_class->first();    

                            $new_student = $old_student->replicate();
                            $new_student->student_no = $this->generate_student_no($this->school_id);
                            $new_student->previous_level = isset($obj_level_class->level_id) ? $obj_level_class->level_id :0;
                            $new_student->save();                                

                            $old_student->has_left = 1;
                            $old_student->promoted_on= date('Y-m-d');
                            $old_student->save();

                            if($old_student->pass_fail == 'PASS')
                            {
                                $new_student->level_class_id = isset($obj_level_class->id) ? $obj_level_class->id : 0;
                            }

                            $new_student->academic_year_id = $current_academic_year_id; 
                            $new_student->save();
                            
                        }                            
                        
                    }                              
                    
                }                      
            }
            
        }
        return;
    }
    public function promote_students($enc_id = FALSE)
    {
    
        $id =  base64_decode($enc_id);
        
        $current_academic_year_id = $this->CommonDataService->get_current_academic_id();

        $old_student = StudentModel::find($id);

        if(isset($old_student->academic_year_id) && $old_student->academic_year_id== $current_academic_year_id)
        {
            Flash::error(translation('academic_year_is_not_finished_yet'));
            return redirect()->back();
        }
        else
        {

            if(isset($old_student->level_class_id) && $old_student->level_class_id!=0)
            {
                $obj_level_class = $this->LevelClassModel->where('id',$old_student->level_class_id)
                                      ->first();

                if(isset($obj_level_class->id))
                {
                    $obj_level = $this->LevelModel->where('id',$obj_level_class->level_id)
                                                  ->first();
                    if(isset($obj_level->id))
                    {
                        $level_order = $this->LevelModel
                                                    ->where('level_order',($obj_level->level_order+1))
                                                    ->first();
                        if($level_order)
                        {

                            $obj_level_class = $this->LevelClassModel->where('level_id',$level_order->id);
                                                                     if(isset($obj_level_class->class_id) &&   $obj_level_class->class_id!=0)
                                                                     {
                                                                        $obj_level_class = $obj_level_class->orWhere('class_id',$obj_level_class->class_id);  
                                                                     }
                                                                     
                                                                     $obj_level_class = $obj_level_class->first();    

                            $new_student = $old_student->replicate();
                            $new_student->student_no = $this->generate_student_no($this->school_id);
                            $new_student->previous_level = isset($obj_level_class->level_id) ? $obj_level_class->level_id :0;
                            $new_student->save();                                

                            $old_student->has_left = 1;
                            $old_student->promoted_on= date('Y-m-d');
                            $old_student->save();

                            if($old_student->pass_fail == 'PASS')
                            {
                                $new_student->level_class_id = isset($obj_level_class->id) ? $obj_level_class->id : 0;
                            }

                            $new_student->academic_year_id = $current_academic_year_id; 
                            $new_student->save();
                            
                            Flash::success(translation('student_promoted_successfully'));
                        }
                        else
                        {
                            Flash::error(translation('next_level_is_not_available'));    
                        }                            
                    }
                    else
                    {
                        Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction'));
                    }
                }
                else
                {
                    Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction'));
                }                     
            }
            else
            {
                Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction'));
            }
        }
        return redirect()->back();
    }

    /*
    | store() : Download student Document uploaded by parent
    | Auther  : Padmashri
    | Date    : 21-06-2018
    */
    public function download_document($enc_id)
    {


        $arr_document = [];
        if(isset($enc_id))
        {
            $document_id = base64_decode($enc_id);
            $obj_documents = $this->DocumentsModel->where('id',$document_id)
                                                    ->select('document_name')
                                                    ->first();
                                                
            if($obj_documents)
            {
                  $arr_document    = $obj_documents->toArray();
                  $file_name       = $arr_document['document_name'];
                  $pathToFile      = $this->student_document_base_img_path.$file_name;

                  $file_exits      = file_exists($pathToFile);
                  if($file_exits)
                  { 
                    return response()->download($pathToFile); 
                  }
                  else
                  {
                    
                     Flash::error(translation("error_while_downloading_an_document"));
                  }
                  
             }
        }
        else
        {
           Flash::error(translation("error_while_downloading_an_document"));
        }

        return redirect()->back();
    }
    public function generate_student_no($school_id)
    {   
        $new_number = rand(0,99999);
        $new_number = str_pad($new_number,5,'0',STR_PAD_LEFT);
        $student_no  =   'ST'.strtoupper(substr($school_id,2,3)).$new_number; 
        
        $exist = $this->StudentModel->where('student_no',$student_no)->first();
        if($exist)
        {
            return $this->generate_student_no($school_id);
        }
        
        return  $student_no;
    }

    /*
    | store() : Export List
    | Auther  : Pooja
    | Date    : 21-07-2018
    */
    public function export(Request $request)
    {       
            
            $obj_data = $this->get_users_details($request,'','export');
            if(sizeof($obj_data)<=0){
                Flash::error(translation("no_records_found_to_export"));
                return redirect()->back();
            }
            if(sizeof($obj_data)>500 && $request->file_format == 'csv'){
                Flash::error(translation("too_many_records_to_export"));
                return redirect()->back();
            }
            if($request->file_format == 'csv'){
                \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel) use($obj_data) 
                    {
                        $excel->sheet(ucwords($this->module_title), function($sheet) use($obj_data) 
                        {
                            $arr_fields['id']             = 'Sr.No';
                            $arr_fields['student_number']= translation('student_number');
                            $arr_fields['name']           = translation('name');
                            $arr_fields['email']          = translation('email');
                            $arr_fields['national_id']    = translation('national_id');
                            $arr_fields['level']          = translation('level');
                            $arr_fields['class']          = translation('class');
                            $arr_fields['has_left']       = translation('has_left');
                            
                            $sheet->row(2, ['',ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                            $sheet->row(4, $arr_fields);
                            if(sizeof($obj_data)>0) 
                            {
                                
                                $arr_tmp = [];
                                foreach($obj_data as $key => $result)
                                {
                                    $status = "";
                                    if($result->has_left==1)
                                    {
                                        $status = "Yes";
                                    }
                                    elseif($result->has_left==0)
                                    {
                                        $status = "No";
                                    }
                                    $arr_tmp[$key]['id']             = intval($key+1);
                                    $arr_tmp[$key]['student_number'] = $result->student_no;
                                    $arr_tmp[$key]['name']           = ucwords($result->user_name);
                                    $arr_tmp[$key]['email']          = $result->email;
                                    $arr_tmp[$key]['national_id']    = $result->national_id;
                                    $arr_tmp[$key]['level']          = $result->level_name;
                                    $arr_tmp[$key]['class']          = $result->class_name;
                                    $arr_tmp[$key]['has_left']       = $status;
                                }
                                   $sheet->rows($arr_tmp);
                            }
                        });
                    })->export('csv');     
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
    public function has_left($enc_id){
        $id = base64_decode($enc_id);
        if(is_numeric($id)){
            $this->StudentModel->where('id',$id)->update(['has_left'=>1]);
            Flash::success(translation("record_updated_successfully"));
            return redirect($this->module_url_path);
        }
        else{
            Flash::error(translation("something_went_wrong"));
            return redirect($this->module_url_path);
        }
    }

    public function not_left(Request $request){
        $id = $request->enc_id;
        
        if(is_numeric($id)){
            $this->StudentModel->where('id',$id)->update(['has_left'=>0]);
            return response()->json(array('status'=>'success','msg'=>translation('record_updated_successfully')));
        }
        else{
            return response()->json(array('status'=>'error','msg'=>translation('something_went_wrong')));
        }
    }

    public function get_classes(Request $request)
    {
        $obj = $this->CommonDataService->get_class($request->level);

        $options = '';                            
        if($obj)
        {
            $arr = $obj->toArray();
            if(count($arr)>0)
            {
                foreach($arr as $value)
                {
                    $options .= '<option value='.$value['id'].'>'.$value['class_details']['class_name'].'</option>';
                }
            }
        }
        return $options;                                  
    }
    public function attachmentThumb($input, $name, $width, $height)
    {
        $thumb_img = Image::make($input)->resize($width,$height);
        $thumb_img->fit($width,$height, function ($constraint) {
            $constraint->upsize();
        });
        $thumb_img->save($this->user_profile_base_img_path.'/thumb_'.$width.'X'.$height.'_'.$name);         
    }

    public function check_existance(Request $request)
    {
        
        $exist = '';
        $student  = $this->StudentModel
                         ->where('user_id',$request->user_id)
                         ->where('school_id','!=',$this->school_id)
                         ->where('has_left',0)
                         ->where('is_active',1)
                         ->first();
        if(isset($student) && $student!=null && count($student)>0)
        {
            $exist =  'true';
        }
        else
        {
            $exist = 'false';
        }
        return $exist;
    }
}