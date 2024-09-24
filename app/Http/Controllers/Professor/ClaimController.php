<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\LevelClassModel;
use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\LevelTranslationModel;
use App\Models\ClassTranslationModel;
use App\Models\ProfessorModel;
use App\Models\SchoolAdminModel;
use App\Models\StudentModel;
use App\Models\ParentModel;
use App\Models\ClaimModel;
use App\Models\NotificationSettingsModel;
use App\Models\NotificationModel;
use App\Common\Services\CommonDataService;
use App\Common\Services\EmailService;
use App\Common\Traits\MultiActionTrait;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;

class ClaimController extends Controller
{
    use MultiActionTrait;
	public function __construct(CommonDataService $CommonDataService,EmailService $EmailService)
    {

    	$this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.professor_panel_slug')).'/claim';
        $this->module_title                 = translation("claim");     
        $this->module_view_folder           = "professor.claim";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-file';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->view_icon                    = 'fa fa-eye';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year				= Session::get('academic_year');
        
        $this->LevelClassModel 		            = new LevelClassModel();
        $this->UserModel                        = new UserModel();
        $this->ClaimModel                       = new ClaimModel();
		$this->ProfessorModel 			        = new ProfessorModel();
        $this->ParentModel                      = new ParentModel();
        $this->StudentModel                     = new StudentModel();
        $this->UserTranslationModel             = new UserTranslationModel();
        $this->LevelTranslationModel            = new LevelTranslationModel();
        $this->ClassTranslationModel            = new ClassTranslationModel();
        $this->NotificationSettingsModel        = new NotificationSettingsModel();
        $this->SchoolAdminModel                 = new SchoolAdminModel();
        $this->NotificationModel                = new NotificationModel();
        $this->CommonDataService                = $CommonDataService;
        $this->EmailService                     = $EmailService;
        $this->BaseModel                        = $this->ClaimModel;


        $this->first_name  = $this->last_name = $this->school_admin_id = $this->school_admin_email = $this->school_admin_contact = '';
        $this->permissions = [];

        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;
        }

        $notification_permissions = $obj_permissions = $this->SchoolAdminModel->with('notification_permissions','get_user_details')->where('school_id',$this->school_id)->first();
        if(isset($obj_permissions) && count($obj_permissions)>0 && !is_null($obj_permissions))
        {
            $arr_permissions = $obj_permissions->toArray();
            $this->school_admin_id = $arr_permissions['user_id'];
            if(isset($arr_permissions['notification_permissions']['notification_permission']) && !empty($arr_permissions['notification_permissions']['notification_permission']))
            {   
                $this->permissions = json_decode($arr_permissions['notification_permissions']['notification_permission'],true);
                $this->school_admin_email   = isset($arr_permissions['get_user_details']['email'])?$arr_permissions['get_user_details']['email']:'';
                $this->school_admin_contact = isset($arr_permissions['get_user_details']['mobile_no'])?$arr_permissions['get_user_details']['mobile_no']:'';   
            }
        }

   		$this->arr_view_data['page_title']      = translation('claim');
   		$this->arr_view_data['module_title']    = translation('claim');
   		$this->arr_view_data['module_icon']     = 'fa fa-claim';
   		$this->arr_view_data['module_url_path'] = $this->module_url_path;
   		$this->arr_view_data['theme_color']     = $this->theme_color;
   		$this->arr_view_data['create_icon']     = 'fa fa-plus-circle';
   		$this->arr_view_data['edit_icon']       = 'fa fa-edit-circle';

   		/*literals*/	
    	$this->str_module_title    = 'module_title';
    	$this->str_module_url_path = 'module_url_path';

    }

      /*
    | index() 		: Redirect to claim list 
    | Auther        : Sayali B
    | Date          : 7-06-2018
    */ 
    public function index()
    {	
        
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data[$this->str_module_title]    = translation("manage")." ".$this->module_title;
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*
    | get_records() : claim listing using ajax 
    | Auther        : Sayali B 
    | Date          : 7-06-2018
    */ 
    public function get_records(Request $request)
    {
        $obj_custom = $this->get_claim_records($request);

        $role = Sentinel::findRoleBySlug(config('app.project.role_slug.professor_role_slug'));
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;

        $json_result  = Datatables::of($obj_custom);

        $json_result = $json_result->blacklist(['id']);                    

         if(array_key_exists('claim.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data)
                            {
                                return base64_encode($data->claim_id);
                            });
                            
        } 

        $json_result =  $json_result->editColumn('level_name',function($data)
                        {
                            if(isset($data->level_name))
                            {
                                return $data->level_name;
                            }
                            else
                            {   
                                return '-';
                            }
                        })
                        ->editColumn('class_name',function($data)
                        {
                            if(isset($data->class_name))
                            {
                                return $data->class_name;
                            }
                            else
                            {   
                                return '-';
                            }
                        })
                        ->editColumn('student_name',function($data)
                        {
                            if(isset($data->user_name))
                            {   
                                return ucwords($data->user_name);
                            }
                            else
                            {
                                return '-';
                            }
                        })
                        ->editColumn('national_id',function($data)
                        {
                            if(isset($data->national_id))
                            {
                                return $data->national_id;
                            }
                            else
                            {
                                return '-';
                            }
                        })
                        ->editColumn('title',function($data)
                        {
                            if(isset($data->title))
                            {
                                return $data->title;
                            }
                            else
                            {
                                return '-';
                            }
                        })
                        ->editColumn('build_status',function($data) use ($arr_current_user_access)
                        {
                            $status = '';
                            if($data->status == 'APPROVED')
                            {
                                $status = '<label class="label label-info">&nbsp;Approved&nbsp;</label>';
                            }
                            else if($data->status == 'PENDING')
                            {
                                $status = '<label class="label label-info label-lime">&nbsp;Pending&nbsp;</label>';
                            }
                            else if($data->status == 'REJECTED')
                            {
                                $status = '<label class="label label-warning">&nbsp;Rejected&nbsp;&nbsp;&nbsp;&nbsp;</label>';
                            }

                            return $status;
                            
                        })
                        ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                        {
                            $build_edit_action = $build_delete_action = $build_view_action = '';
                            if($data->status=='PENDING')
                            {    
                                
                                if(array_key_exists('claim.delete',$arr_current_user_access))
                                {     
                                    $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->claim_id);

                                    $build_delete_action = '<a href="'.$delete_href.'" class="red-color" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';

                                }

                            }
                            else
                            {
                                $build_delete_action = '<a style="position: relative;" class="red-color" href="javascript:void(0)" title="'.translation('delete').'" ><i class="fa fa-trash" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                            }     
                           

                            $view_href =  $this->module_url_path.'/view/'.base64_encode($data->claim_id);
                            $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>';

                            return $build_view_action.'&nbsp;'.$build_delete_action;  
                        })
                        ->editColumn('build_checkbox',function($data) use ($arr_current_user_access){
                        	$build_checkbox ='';
                            if(array_key_exists('claim.update',$arr_current_user_access) || array_key_exists('claim.delete',$arr_current_user_access) )
                            {
                                    if($data->status == 'PENDING')
                                    {
                                         $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->claim_id).'" value="'.base64_encode($data->claim_id).'" /><label for="mult_change_'.base64_encode($data->claim_id).'"></label></div>';   
                                    } 
                                    else
                                    {
                                        $build_checkbox = '<div> - </div>';
                                    }   
                                
                            }
                            return $build_checkbox;
                        })
                                              
                        ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);
    }

    /*
    | get_claim_records() : claim listing using ajax 
    | Auther        : Sayali B
    | Date          : 7-06-2018
    */
    public function get_claim_records(Request $request)
    {
    	$user_id = $this->user_id;
        $locale = '';

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
                      
  		$claim_table                   = $this->BaseModel->getTable();
        $user_table                    = $this->UserTranslationModel->getTable();
  		$level_class_table             = $this->LevelClassModel->getTable();
  		$level_table                   = $this->LevelTranslationModel->getTable();	
  		$class_table                   = $this->ClassTranslationModel->getTable();

        $obj_custom = DB::table($claim_table)
                        ->select(DB::raw(   

                                            $claim_table.".id as claim_id,".
                                            $claim_table.".professor_id as professor_id,".
                                            $claim_table.".student_national_id as national_id,".
                                            $claim_table.".title as title,".
                                            $level_table.".level_name,".
                                            $class_table.".class_name,".
                                            $claim_table.".status,".
                                            "CONCAT(".$user_table.".first_name,' ',"
                                                     .$user_table.".last_name) as user_name"
                                                    ))
                        				->Join($user_table,$claim_table.'.student_id',' = ',$user_table.'.user_id')
                        				->Join($level_class_table,$claim_table.'.level_class_id',' = ',$level_class_table.'.id')
                        				->Join($level_table,$level_class_table.'.level_id',' = ',$level_table.'.level_id')
                        				->Join($class_table,$level_class_table.'.class_id',' = ',$class_table.'.class_id')
                                        ->where($user_table.'.locale','=',$locale)
                                        ->where($level_table.'.locale','=',$locale)
                                        ->where($class_table.'.locale','=',$locale)
                                        ->whereNull($claim_table.'.deleted_at')
                                        ->where($claim_table.'.professor_id','=',$user_id)
                                        ->where($claim_table.'.school_id',$this->school_id)
                                        ->where($claim_table.'.academic_year_id','=',$this->academic_year)
                                        ->orderBy($claim_table.'.created_at','DESC');
                 
        $search = $request->input('search');
        $search_term = $search['value'];
        if($request->has('search') && $search_term!="")
        {
            $obj_custom = $obj_custom->WhereRaw("( (".$claim_table.".student_national_id LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$claim_table.".title LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$claim_table.".status LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$level_table.".level_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$class_table.".class_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw(" ( CONCAT(".$user_table.".first_name,'',".$user_table.".last_name)  LIKE  '%".$search_term."%' ) )");
        }

        return $obj_custom ;
    }

    /*
    | create() : create claim
    | Auther        : Sayali B  
    | Date          : 7-06-2018
    */
    public function create()
    {
    	$arr_academic_year = '';
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }

    	$arr_levels  =[];

    	$obj_levels = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id);
        
    	if(!empty($obj_levels))
    	{
			$arr_levels = $obj_levels -> toArray();    		
    	}

        $this->arr_view_data['page_title']            = translation("add")." ".$this->module_title;
    	$this->arr_view_data[$this->str_module_title] = $this->module_title;
    	$this->arr_view_data['arr_levels']            = $arr_levels ;
    
        return view($this->module_view_folder.'.create', $this->arr_view_data);

    }


    /*
    | get_class() : get list of classes 
    | Auther        : Sayali B  
    | Date          : 7-06-2018
    */ 
    public function get_class(Request $request)
    {
    	$level_id = $request->input('level');

        $options ='';

        $obj_class = $this->CommonDataService->get_class($level_id);
    
        if(!empty($obj_class))
        {
            $arr_class  = $obj_class -> toArray();
            $options .= '<option value="">'.translation('select').' '.translation('class').'</option>';
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
            else
            {
                $options .= '<option value="">'.translation('select').' '.translation('class').'</option>';
            }  
        }

        return $options;
    }

    /*
    | get_class() : get list of students as per selected level and class 
    | Auther        : Sayali B
    | Date          : 7-06-2018
    */ 
    public function get_students(Request $request)
    {
        $level_id = $request->input('level');
        $class_id = $request->input('class');

        $arr_academic_year = $options = '';
     
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }

        $obj_students = $this->StudentModel
                             ->with('get_user_details')
                             ->where('school_id',$this->school_id)
                             ->where('academic_year_id',$this->academic_year)
                             ->where('level_class_id',$class_id)
                             ->where('is_active',1)
                             ->where('has_left',0)
                             ->get();
    
        if(!empty($obj_students))
        {
            $arr_students  = $obj_students -> toArray();
            if(count($arr_students)>0)
            {
                $options .= '<option value="">'.translation('select').' '.translation('student_name').'</option>';
                foreach($arr_students as $value)
                {
                    $options .= '<option value='.$value['user_id'];
                    $options .= '>'.$value['get_user_details']['first_name'].' '.$value['get_user_details']['last_name'].' ('.$value['student_no'].') </option>';
                }
            }  
            else
            {
                $options .= '<option value="">'.translation('select').' '.translation('student_name').'</option>';
            } 
        }

        return $options;
    }

    /*
    | store() : store claim
    | Auther        : Sayali B 
    | Date          : 7-06-2018
    */
    public function store(Request $request)
    {
        
        $messages = $arr_rules = [];

        $arr_rules['level']            = 'required';
        $arr_rules['class']            = 'required';
        $arr_rules['student_name']     = 'required';
        $arr_rules['claim_title']      = 'required';
        $arr_rules['description']      = 'required';

        $messages['required']               = translation('this_field_is_required');
        
        $validator                          = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 
        $obj_parent = $this->StudentModel->select('parent_id','user_id','level_class_id')->with('get_user_details','get_level_class.level_details','get_level_class.class_details')->where('user_id',$request->input('student_name'))->first();

        $arr_data =[]; 
        $arr_data['school_id']              = $this->school_id;
        $arr_data['academic_year_id']       = $this->academic_year;
        $arr_data['title']                  = $request->input('claim_title');
        $arr_data['level_class_id']         = $request->input('class');
        $arr_data['student_id']             = $request->input('student_name');
        $arr_data['professor_id']           = $this->user_id;
        $arr_data['student_national_id']    = $request->input('student_national_id');

        if(isset($obj_parent) && !empty($obj_parent))
        {
            $arr_data['parent_id'] = $obj_parent->parent_id;
        }
        $arr_data['description'] = $request->input('description');
        $arr_data['status'] = "PENDING";
        $claim = $this->BaseModel->create($arr_data);

        if($claim)
        {      
            $first_name = isset($obj_parent['get_user_details']['first_name'])?ucwords($obj_parent['get_user_details']['first_name']):'';
            $last_name  = isset($obj_parent['get_user_details']['last_name'])?ucwords($obj_parent['get_user_details']['last_name']):'';

            if(isset($this->permissions) && count($this->permissions)>0)
            {

                if(array_key_exists('claim.app',$this->permissions))
                {
                 
                    $arr_notification = [];
                    $arr_notification['school_id']          =   $this->school_id;
                    $arr_notification['from_user_id']       =   $this->user_id;
                    $arr_notification['to_user_id']         =   $this->school_admin_id;
                    $arr_notification['user_type']          =  config('app.project.role_slug.professor_role_slug');
                    $arr_notification['notification_type']  =  'claim Add';
                    $arr_notification['title']              =  'New claim added:  Prof. '.ucwords($this->first_name.' '.$this->last_name).' claimed on '.ucwords($first_name.' '.$last_name).' of class '.(isset($obj_parent['get_level_class']['level_details']['level_name'])?$obj_parent['get_level_class']['level_details']['level_name']:'').' '.(isset($obj_parent['get_level_class']['class_details']['class_name'])?$obj_parent['get_level_class']['class_details']['class_name']:'');
                    $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.school_admin_role_slug').'/claim';
                    $this->NotificationModel->create($arr_notification);
                }
                
                $details          = [
                                        'first_name'    =>  ucwords('School Admin'),
                                        'student_name'  =>  $first_name.' '.$last_name,
                                        'level'         =>  isset($obj_parent['get_level_class']['level_details']['level_name'])?$obj_parent['get_level_class']['level_details']['level_name']:'',
                                        'class'         =>  isset($obj_parent['get_level_class']['class_details']['class_name'])?$obj_parent['get_level_class']['class_details']['class_name']:'',
                                        'professor_name'=>  ucwords($this->first_name.' '.$this->last_name),
                                        'mobile_no'     =>  $this->school_admin_contact,
                                        'email'         =>  $this->school_admin_email
                                     ];
                if(array_key_exists('claim.sms',$this->permissions))
                {
                    $arr_sms_data = $this->built_sms_data($details);
                    $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
                }
                if (array_key_exists('claim.email',$this->permissions))
                {
                    $arr_mail_data = $this->built_mail_data($details); 
                    $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id); 
                }if(array_key_exists('claim.app',$this->permissions))
                {
                 
                    $arr_notification = [];
                    $arr_notification['school_id']          =   $this->school_id;
                    $arr_notification['from_user_id']       =   $this->user_id;
                    $arr_notification['to_user_id']         =   $this->school_admin_id;
                    $arr_notification['user_type']          =  config('app.project.role_slug.professor_role_slug');
                    $arr_notification['notification_type']  =  'claim Add';
                    $arr_notification['title']              =  'New claim added:  Prof. '.ucwords($this->first_name.' '.$this->last_name).' claimed on '.ucwords($first_name.' '.$last_name).' of class '.(isset($obj_parent['get_level_class']['level_details']['level_name'])?$obj_parent['get_level_class']['level_details']['level_name']:'').' '.(isset($obj_parent['get_level_class']['class_details']['class_name'])?$obj_parent['get_level_class']['class_details']['class_name']:'');
                    $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.school_admin_role_slug').'/claim';
                    $this->NotificationModel->create($arr_notification);
                }
                
                $details          = [
                                        'first_name'    =>  ucwords('School Admin'),
                                        'student_name'  =>  $first_name.' '.$last_name,
                                        'level'         =>  isset($obj_parent['get_level_class']['level_details']['level_name'])?$obj_parent['get_level_class']['level_details']['level_name']:'',
                                        'class'         =>  isset($obj_parent['get_level_class']['class_details']['class_name'])?$obj_parent['get_level_class']['class_details']['class_name']:'',
                                        'professor_name'=>  ucwords($this->first_name.' '.$this->last_name),
                                        'mobile_no'     =>  $this->school_admin_contact,
                                        'email'         =>  $this->school_admin_email
                                     ];
                if(array_key_exists('claim.sms',$this->permissions))
                {
                    $arr_sms_data = $this->built_sms_data($details);
                    $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
                }
                if (array_key_exists('claim.email',$this->permissions))
                {
                    $arr_mail_data = $this->built_mail_data($details); 
                    $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id); 
                }
            }

            Flash::success(translation('claim_added_successfully'));
            return redirect()->back();    
        }
        
    }

    /*
    | view() : view claim
    | Auther        : Sayali B
    | Date          : 7-06-2018
    */
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

        $arr_exam = [];

        $obj_claim = $this->BaseModel
                                ->with(['get_level_class'=>function($q){
                                    $q->with('level_details');
                                    $q->with('class_details');
                                },'get_parent_details','get_professor_details','get_student_details'])
                                ->where('id',$id)->first();
        
        if(isset($obj_claim) && !empty($obj_claim))
        {
            $arr_claim = $obj_claim->toArray();
            $this->arr_view_data['arr_data'] = $arr_claim;
        }
        else
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }

        $this->arr_view_data[$this->str_module_title] = translation("view")." ".$this->module_title;
        

        return view($this->module_view_folder.'.view', $this->arr_view_data);
    }

    public function get_student_NationalId(Request $request)
    {
        $data = '';
        $obj = $this->UserModel->select('national_id')->where('id',$request->input('student_id'))->first();
       if(isset($obj) && !empty($obj))
       {
            $data = $obj->national_id;
       }

       return $data;
    }

    public function built_mail_data($arr_data)
     {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'STUDENT_NAME'     => ucwords($arr_data['student_name']),
                                  'LEVEL'            => $arr_data['level'],
                                  'CLASS'            => $arr_data['class'],
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                  'PROFESSOR'        => $arr_data['professor_name']];

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_slug'] = 'add_claim';
            
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_sms_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'STUDENT_NAME'     => ucwords($arr_data['student_name']),
                                  'LEVEL'            => $arr_data['level'],
                                  'CLASS'            => $arr_data['class'],
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                  'PROFESSOR'        => $arr_data['professor_name']];
            

            $arr_sms_data                      = [];
            $arr_sms_data['sms_template_slug'] = 'add_claim';
            
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }
       
}
