<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;

use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\UserRoleModel;
use App\Models\RoleModel;
use App\Models\SuggestionModel;
use App\Models\SuggestionPollingModel;
use App\Models\SchoolProfileModel;
use App\Models\SchoolProfileTranslationModel;
use App\Models\SchoolTemplateModel;
use App\Models\SuggestionCategoriesModel;
use App\Models\NotificationModel;
use App\Models\SchoolAdminModel;
/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService; 
/*Activity Log */

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;

class SuggestionsController extends Controller
{
    use MultiActionTrait;
    public function __construct(    
                                    UserModel $user,
                                    UserTranslationModel $translation,
                                    UserRoleModel $user_role_model,
                                    RoleModel $role_model,
                                    ActivityLogsModel $activity_logs,
                                    SuggestionModel $suggestion,
                                    SuggestionPollingModel $polling,
                                    SchoolProfileTranslationModel $school,
                                    SchoolProfileModel $profile,
                                    SchoolTemplateModel $template,
                                    SuggestionCategoriesModel $categories,
                                    SchoolAdminModel $SchoolAdminModel,
                                    NotificationModel $NotificationModel,
                                    EmailService $EmailService,
                                    CommonDataService $CommonDataService

                                )
    {
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->ActivityLogsModel            = $activity_logs; /* Activity Model */   
        $this->SuggestionModel              = $suggestion;
        $this->BaseModel                    = $this->SuggestionModel;
        $this->SuggestionPollingModel       = $polling;
        $this->SchoolProfileTranslationModel= $school;
        $this->SchoolProfileModel           = $profile;
        $this->SchoolTemplateModel          = $template;
        $this->SuggestionCategoriesModel    = $categories;
        $this->SchoolAdminModel             = $SchoolAdminModel;
        $this->NotificationModel            = $NotificationModel;
        $this->EmailService                 = $EmailService;
        $this->CommonDataService            = $CommonDataService;

        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.parent_panel_slug')."/suggestions");
        
        $this->module_title                 = translation("suggestions");
        $this->module_url_slug              = translation("suggestions");

        $this->module_view_folder           = "parent.suggestions";
        $this->theme_color                  = theme_color();

        $this->school_id         = Session::has('school_id')?Session::get('school_id'):0;
        $this->academic_year     = Session::has('academic_year')?Session::get('academic_year'):0;
        $this->first_name = $this->last_name = $this->school_admin_email = $this->school_admin_contact =$this->school_admin_id='';
        $this->permissions = [];

        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;
        }

        $obj_permissions = $this->SchoolAdminModel
                                ->with('notification_permissions','get_user_details')
                                ->where('school_id',$this->school_id)
                                ->first();

        if(isset($obj_permissions) && count($obj_permissions)>0 && !is_null($obj_permissions))
        {
            $arr_permissions       = $obj_permissions->toArray();

            $this->school_admin_id = $arr_permissions['user_id'];

            if(isset($arr_permissions['notification_permissions']['notification_permission']) && !empty($arr_permissions['notification_permissions']['notification_permission']))
            {   
                $this->permissions = json_decode($arr_permissions['notification_permissions']['notification_permission'],true);   
            }
            $this->school_admin_email   = isset($arr_permissions['get_user_details']['email'])?$arr_permissions['get_user_details']['email']:'';
            $this->school_admin_contact = isset($arr_permissions['get_user_details']['mobile_no'])?$arr_permissions['get_user_details']['mobile_no']:'';
        }



    }   

    public function index(Request $request,$status)
    {   
        if($status == "manage")
        {
            $obj_suggestions  = $this->BaseModel
                                     ->with('get_user_details','get_category')
                                     ->where('school_id',$this->school_id)
                                     ->where('academic_year_id',$this->academic_year)
                                     ->where('user_id',$this->user_id)
                                     ->whereIn('status',['REQUESTED','APPROVED'])
                                     ->get();
        }
        elseif($status == 'poll_raised')
        {
            $obj_suggestions  = $this->BaseModel
                                     ->with('get_user_details','get_polling_details','get_category')
                                     ->where('school_id',$this->school_id)
                                     ->where('academic_year_id',$this->academic_year)
                                     ->where('status','POLL_RAISED')
                                     ->get();
        }

        
        if(isset($obj_suggestions) && $obj_suggestions!=null)
        {
            $this->arr_view_data['arr_suggestions']          = $obj_suggestions->toArray();
        }

        $this->arr_view_data['user_id']         = $this->user_id;
        $page_title =  translation('manage').' '.$this->module_title;    
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['status']          = $status;
        $this->arr_view_data['user_id']         = $this->user_id;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function get_records(Request $request,$status='')
    {
        $arr_current_user_access =[];
        $role = Sentinel::findRoleById(1);
                
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;
       
        $obj_user        = $this->get_suggestion_details($request,$status);

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('users.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data)
                            {
                                return base64_encode($data->id);
                            });
        }                    

        $json_result     = $json_result->editColumn('subject',function($data)
                            { 
                                 
                                if($data->subject!=null && $data->subject!='')
                                {
                                    return ucfirst($data->subject);
                                }
                                else
                                {
                                    return  '-';
                                }

                            }) 
                            ->editColumn('user_role',function($data) 
                            { 
                                 
                                if($data->user_role!=null && $data->user_role!='')
                                {
                                    return $data->user_role;
                                }
                                else
                                {
                                    return  '-';
                                }

                            })
                            ->editColumn('poll_raised',function($data) 
                            { 
                                 
                                if($data->poll_raised == 0)
                                {
                                    return 'No';
                                }
                                else
                                {
                                    return  'Yes';
                                }

                            })
                            ->editColumn('poll_count',function($data) 
                            { 
                                $count = '';
                                if($data->like_count != null || $data->dislike_count != null)
                                {
                                    $count .= '<div class="left-box-count">'.$data->like_count.'</div>';
                                    $count .= '<div class="right-box-count">'.$data->dislike_count.'</div>';  
                                }
                                return $count;
                            })
                            ->editColumn('status',function($data)
                            { 
                                $status = '';
                                if($data->status!=null && $data->status!='')
                                {
                                    if($data->status == 'REQUESTED')
                                    {
                                        $status .='<div class="form-group">';
                                        $status .= '<select id="status" onChange="updateStatus('.$data->id.');" class="form-control" name="status">';
                                        $status .= '<option value="">'.translation('select').'</option>';
                                        $status .= '<option value="APPROVED">APPROVE</option>';
                                        $status .= '<option value="REJECTED">REJECT</option>';
                                        $status .= '<option value="POLL_RAISED">RAISE POLL</option>';
                                        $status .= '</select>';
                                        $status .= '</div>';
                                    }

                                    if($data->status == 'POLL_RAISED')
                                    {
                                        $status .='<div class="form-group">';
                                        $status .= '<select id="status" onChange="updateStatus('.$data->id.');" class="form-control" name="status">';
                                        $status .= '<option value="">'.translation('select').'</option>';
                                        $status .= '<option value="APPROVED">APPROVE</option>';
                                        $status .= '<option value="REJECTED">REJECT</option>';
                                        $status .= '</select>';
                                        $status .= '</div>';
                                    }
                                }
                                return $status;

                            })

                            ->editColumn('build_action_btn',function($data) 
                            { 
                                    $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                    return '<a class="green-color" href="'.$view_href.'" title="View"><i class="fa fa-eye" ></i></a>';                           
                                     
                            })
                            ->editColumn('build_checkbox',function($data)use($arr_current_user_access){
                                $build_checkbox ='';
                                if(array_key_exists('suggestions.delete',$arr_current_user_access))
                                {
                                    $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                }
                            return $build_checkbox;
                            })
                            ->make(true);

        $build_result = $json_result->getData();

        
        return response()->json($build_result);
    }

    function get_suggestion_details(Request $request,$status)
    {     
        $str =  strtoupper($status);

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }

        $suggestion_table                  =    $this->SuggestionModel->getTable();                  
        $prefixed_suggestion_table         =    DB::getTablePrefix().$this->SuggestionModel->getTable();

        $suggestion_polling_table          =    $this->SuggestionPollingModel->getTable();
        $prefixed_suggestion_polling_table =    DB::getTablePrefix().$this->SuggestionPollingModel->getTable();

        $user_translation_table            =    $this->UserTranslationModel->getTable();
        $prefixed_user_translation_table   =    DB::getTablePrefix().$this->UserTranslationModel->getTable();

        $obj_user = DB::table($suggestion_table)
                                ->select(DB::raw($prefixed_suggestion_table.".id as id,".
                                                 $prefixed_suggestion_table.".subject as subject, ".
                                                 $prefixed_suggestion_table.".suggestion_date as suggestion_date, ".
                                                 $prefixed_suggestion_table.".user_role as user_role, ".
                                                 $prefixed_suggestion_table.".duration as duration, ".
                                                 $prefixed_suggestion_table.".poll_raised as poll_raised, ".
                                                 $prefixed_suggestion_table.".like_count as like_count, ".
                                                 $prefixed_suggestion_table.".dislike_count as dislike_count, ".
                                                 "CONCAT(".$prefixed_user_translation_table.".first_name,' ',"
                                                          .$prefixed_user_translation_table.".last_name) as user_name,".
                                                 $prefixed_suggestion_table.".status as status"))
                                ->join($user_translation_table,$user_translation_table.'.user_id', ' = ',$suggestion_table.'.user_id')
                                ->where($user_translation_table.'.locale','=',$locale)
                                ->where($suggestion_table.'.school_id','=',$this->school_id)
                                ->where($suggestion_table.'.status','=',$str)
                                ->where($suggestion_table.'.academic_year_id','=',$this->academic_year)
                                ->orderBy($prefixed_suggestion_table.'.created_at','DESC');

        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
            $obj_user = $obj_user->WhereRaw("( (".$prefixed_suggestion_table.".subject LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_suggestion_table.".school_id LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_suggestion_table.".user_role LIKE '%".$search_term."%' ) )");
        }
        return $obj_user;
    }


    public function view($status,$enc_id)
    {   
        $id = base64_decode($enc_id);
       
        $school_name =  '';
        
        $suggestions    =   $this->SuggestionModel
                                 ->with('get_polling_details.user_name')
                                 ->where('id',$id)
                                 ->first();

        $arr_data   =   [];
        if(isset($suggestions) && $suggestions != null)
        {
            $this->arr_view_data['suggestions']   =   $suggestions->toArray();
        }

        $this->arr_view_data['page_title']                   = translation("view").' '.str_singular($this->module_title);
        $this->arr_view_data['module_title']                 = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['arr_data']                     = $arr_data;
        $this->arr_view_data['school_name']                  = $school_name;
        $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
        $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        $this->arr_view_data['status']                       = $status;
        
        return view($this->module_view_folder.'.view', $this->arr_view_data);

    }

    /*
    | change_status() : change status of suggestion to approved/rejected
    | Auther : sayali
    | Date : 27-06-2018
    */
    public function change_status(Request $request)
    {
        $school_id      = $this->school_id;
        $academic_year  = $this->academic_year;

        $id             = $request->input('id');
        $status         = $request->input('status');

        $update_record  = $this->BaseModel->where('id',$id)->where('school_id',$school_id)->where('academic_year_id',$academic_year)->update(['status'=>$status]);

            if($update_record)
            {
                return response()->json(array('status'=>'success'));
            }
            else
            {
                return response()->json(array('status'=>'error'));
            }
    }

    /*
    | raise_poll() : raise poll on particular suggestion and update record with duration and stakeholders
    | Auther : sayali
    | Date : 27-06-2018
    */
    public function raise_poll(Request $request)
    {
        $school_id      = $this->school_id;
        $academic_year  = $this->academic_year;
        $users  = implode(',',$request->input('users'));

        $id             = $request->input('id');
        $duration       = $request->input('duration');
        $user_type      = $users;



        if($id)
        {
            $arr_data['duration']        =   $duration;
            $arr_data['assigned_roles']  =   $user_type;
            $arr_data['status']          =   'POLL_RAISED';
            $arr_data['poll_raised']     =   1;
            $update_record = $this->BaseModel->where('id',$id)->where('school_id',$school_id)->where('academic_year_id',$academic_year)->update($arr_data);

            if($update_record)
            {
                return response()->json(array('status'=>'success'));
            }
            else
            {
                return response()->json(array('status'=>'error'));
            }
        }

    }

    public function create()
    {
        $obj_categories = $this->SuggestionCategoriesModel
                               ->whereNull('deleted_at')
                               ->get();

        if(isset($obj_categories) && $obj_categories!=null)
        {
            $this->arr_view_data['arr_categories']      = $obj_categories->toArray();
        }

        $page_title =  translation('create').' '.$this->module_title;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.create', $this->arr_view_data);
    }

    public function store(Request $request)
    {
        $arr_rules['subject']          = "required";  
        $arr_rules['category']         = "required";  

        $messages['required']    =   'This field is required';

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
             return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        
        $arr_data['subject']           =   $request->input('subject');
        $arr_data['school_id']         =   $this->school_id;
        $arr_data['academic_year_id']  =   $this->academic_year;
        $arr_data['user_id']           =   $this->user_id;
        $arr_data['description']       =   $request->input('description');
        $arr_data['category']          =   $request->input('category');
        $arr_data['user_role']         =   config('app.project.role_slug.professor_role_slug');
        $arr_data['assigned_roles']    =   '';
        $arr_data['suggestion_date']   =   Date('Y-m-d');
        $arr_data['duration']          =   0;
        $arr_data['status']            =   'REQUESTED';
        $arr_data['poll_raised']       =   0;
        $arr_data['like_count']        =   0;
        $arr_data['dislike_count']     =   0;

        $create_data = $this->SuggestionModel->create($arr_data);

        if($create_data)
        {
            if(array_key_exists('suggestions.app',$this->permissions))
            {
             
                $arr_notification = [];
                $arr_notification['school_id']          =   $this->school_id;
                $arr_notification['from_user_id']       =   $this->user_id;
                $arr_notification['to_user_id']         =   $this->school_admin_id;
                $arr_notification['user_type']          =  config('app.project.role_slug.parent_role_slug');
                $arr_notification['notification_type']  =  'Suggestion Add';
                $arr_notification['title']              =  'New Suggestion Added: '.ucwords($this->first_name.' '.$this->last_name).' Parent added new suggestion';
                $arr_notification['view_url']           =  url('/').'/'.config('app.project.role_slug.school_admin_role_slug').'/suggestions/requested';
                $this->NotificationModel->create($arr_notification);
            }
            $details          = [
                                        'first_name'  =>  'School Admin',
                                        'email'       =>  $this->school_admin_email,
                                        'mobile_no'   =>  $this->school_admin_contact,
                                        'parent'      =>  ucwords($this->first_name.' '.$this->last_name)
                                ];
            if(array_key_exists('suggestions.sms',$this->permissions))
            {
                $arr_sms_data = $this->built_sms_data($details);
                $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id); 
            }
            if (array_key_exists('suggestions.email',$this->permissions))
            {
                $arr_mail_data = $this->built_mail_data($details);
                $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
            }

            Flash::success($this->module_title.' '.translation('created_successfully'));   
            return redirect()->back();
        }
        else
        {
            Flash::error(translation('problem_occured_while_creating').' '.$this->module_title);   
            return redirect()->back();   
        }
    }

    public function add_vote(Request $request)
    {
        $vote  = $request->input('status');
        $id    = $request->input('id');
        $obj_suggestion = $this->BaseModel
                               ->where('id',$id)
                               ->first();

        if(isset($obj_suggestion) && $obj_suggestion!= null)
        {
            if($vote == 'like')
            {
                $vote_count = $obj_suggestion->like_count + 1;
                $this->SuggestionModel
                     ->where('id',$id)
                     ->update(['like_count'=>$vote_count]);
            }

            if($vote == 'dislike')
            {
                $vote_count = $obj_suggestion->dislike_count + 1;
                $this->SuggestionModel
                     ->where('id',$id)
                     ->update(['dislike_count'=>$vote_count]);
            }

            $arr_data['suggestion_id']  =   $obj_suggestion->id;
            $arr_data['vote']           =   strtoupper($vote);
            $arr_data['from_user_id']   =   $this->user_id;
            $add_polling = $this->SuggestionPollingModel->create($arr_data);

            if($add_polling)
            {
                return response()->json(array('status'=>'success'));
            }
            else
            {
                return response()->json(array('status'=>'error'));
            }
        }
        else
        {
            return response()->json(array('status'=>'error'));
        }
    }

     public function built_mail_data($arr_data)
     {

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => ucfirst($arr_data['first_name']),
                                  'SCHOOL_ADMIN'     => $this->CommonDataService->get_school_name($this->school_id),
                                  'ROLE'             => 'Parent',
                                  'USER_NAME'        => $arr_data['parent']];

            $arr_mail_data                        = [];
            $arr_mail_data['email_template_slug'] = 'create_suggestion';
            
            $arr_mail_data['arr_built_content']   = $arr_built_content;
            $arr_mail_data['user']                = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_sms_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'ROLE'             => 'Parent',
                                  'USER_NAME'        => $arr_data['parent']];
            

            $arr_sms_data                      = [];
            $arr_sms_data['sms_template_slug'] = 'create_suggestion';
            
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }
    
}