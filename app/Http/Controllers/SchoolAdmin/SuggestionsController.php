<?php

namespace App\Http\Controllers\SchoolAdmin;

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
use App\Models\ProfessorModel;
use App\Models\ParentModel;
use App\Models\StudentModel;
use App\Models\EmployeeModel;
use App\Models\NotificationModel;
/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
use App\Common\Services\CommonDataService;
use App\Common\Services\EmailService;
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
                                    CommonDataService $CommonDataService,
                                    EmailService $EmailService

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
        $this->CommonDataService            = $CommonDataService;
        $this->EmailService                 = $EmailService;

        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')."/suggestions");
        
        $this->module_title                 = translation("suggestions");
        $this->module_url_slug              = translation("suggestions");

        $this->module_view_folder           = "schooladmin.suggestions";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-dropbox';

        $this->school_id         = Session::has('school_id')?Session::get('school_id'):0;
        $this->academic_year     = Session::has('academic_year')?Session::get('academic_year'):0;
        $this->first_name = $this->last_name =$this->ip_address ='';

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->user_id           = $obj_data->id;
            
        }
        /* Activity Section */



    }   

    public function index(Request $request,$status='')
    {   
        if($status == 'requested')
        {
            $page_title = $this->module_title.' '. translation($status);    
        }
        else
        {
            $page_title =  translation($status).' '.$this->module_title;    
        }
        
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['status']          = $status;
        $this->arr_view_data['module_icon']     = $this->module_icon;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function get_records(Request $request,$status='')
    {
        $status =$status;
        $arr_current_user_access =[];
         $arr_current_user_access =[];
    
        $arr_current_user_access = $this->CommonDataService->current_user_access();
        $role = Session::get('role');
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
                                 
                                if(isset($data->poll_raised) && $data->poll_raised == 0)
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
                                if(isset($data->like_count) && isset($data->dislike_count))
                                {
                                    $count .= '<div class="like-ic-sgn">'.$data->like_count.'</div>';
                                    $count .= '<div class="dilike-ic-sgn">'.$data->dislike_count.'</div>';  
                                }
                                return $count;
                            })
                            ->editColumn('status',function($data) use($status)
                            { 
                                $data1 = '';
                                
                             if($status == 'created' || $status == 'polled_raised')
                             {
                                if($data->status == 'POLL_RAISED')
                                {
                                   if(isset($data->vote) && $data->vote!=null && $data->vote == 'LIKE')
                                    {
                                        $data1 = '<label class="label label-success" ><i class="fa fa-thumbs-up"></i></label>'; 
                                    }
                                    elseif(isset($data->vote) && $data->vote!=null && $data->vote == 'DISLIKE')
                                    {
                                        $data1 = '<label class="label label-warning"><i class="fa fa-thumbs-down"></i></label>'; 
                                    }
                                    else
                                    {
                                        $valid =    '';
                                        $date = $data->suggestion_date;
                                        $duration = $data->duration;
                                        $date = date_create($date);
                                        date_add($date, date_interval_create_from_date_string($duration.' days'));
                                        $date1= date_format($date,'Y-m-d');
                                        
                                        $to_date = date_create();
                                       
                                        $date_diff = date_diff($to_date,$date);
                                        if($date_diff->format('%R%d')>0)
                                        {
                                            $data1 .= '<div class="school-like-dislike">';
                                            $data1 .= ' <a href="javascript:void(0)" onClick="addVote('.$data->id.',\'like\')" class="like-ic-sgn"><i class="fa fa-thumbs-up"></i></a>' ;
                                            $data1 .= '<a href="javascript:void(0)" onClick="addVote('.$data->id.',\'dislike\')" class="dilike-ic-sgn"><i class="fa fa-thumbs-down"></i></a>';
                                            $data1 .= '</div>';
                                        }
                                        else
                                        {
                                            $data1 = '-';
                                        }
                                    }
                                }
                                elseif($data->status == 'APPROVED')
                                {
                                   $data1 .= '<label class="label label-success">APPROVED</label>';
                                }
                                elseif($data->status == 'REQUESTED')
                                {
                                   $data1 .= '<label class="label label-info">REQUESTED</label>';
                                }
                               
                             }
                             else
                             {
                                if($data->status == 'REQUESTED')
                                {
                                  /*  $data1 .='<div class="form-group">';
                                    $data1 .= '<select id="status_'.$data->id.'" onChange="updateStatus('.$data->id.');" class="form-control" name="status">';
                                    $data1 .= '<option value="">'.translation('select').'</option>';
                                    $data1 .= '<option value="APPROVED">APPROVE</option>';
                                    $data1 .= '<option value="REJECTED">REJECT</option>';
                                    $data1 .= '<option value="POLL_RAISED">RAISE POLL</option>';
                                    $data1 .= '</select>';
                                    $data1 .= '</div>';*/

                                    $val = "APPROVED";
                                    $data1 .= '<a class="green-color status-block-section" href="javascript:void(0);"
                                        data-id="'.$data->id.'"
                                        onClick="updateStatus('.$data->id.',\''.$val.'\')"
                                        title="'.translation('approve').'">
                                            <i class="fa fa-check" ></i>
                                        </a>&nbsp;';
                                    $val = "REJECTED";
                                    $data1 .= '<a class="light-blue-color status-block-section" href="javascript:void(0);" onClick="updateStatus('.$data->id.',\''.$val.'\')"  title="'.translation('reject').'" ><i class="fa fa-times" ></i></a>&nbsp;';
                                    $val = "POLL_RAISED";
                                    $data1 .= '<a class="pink-color status-block-section" href="javascript:void(0);" onClick="updateStatus('.$data->id.',\''.$val.'\')" title="RAISE POLL"  data-val="'.ucfirst(strtolower(translation('raise_poll'))).'" data-toggle="modal" data-target="#myModal" ><i class="fa fa-bar-chart" ></i></a>&nbsp;';


                                     
                                }

                                if($data->status == 'POLL_RAISED')
                                {
                                    /*$data1 .='<div class="form-group">';
                                    $data1 .= '<select id="status_'.$data->id.'" onChange="updateStatus('.$data->id.');" class="form-control" name="status">';
                                    $data1 .= '<option value="">'.translation('select').'</option>';
                                    $data1 .= '<option value="APPROVED">APPROVE</option>';
                                    $data1 .= '<option value="REJECTED">REJECT</option>';
                                    $data1 .= '</select>';
                                    $data1 .= '</div>';*/

                                    $val = "APPROVED";
                                    $data1 .= '<a class="green-color status-block-section" href="javascript:void(0);"
                                        data-id="'.$data->id.'"
                                        onClick="updateStatus('.$data->id.',\''.$val.'\')"
                                        title="'.translation('approve').'">
                                            <i class="fa fa-check" ></i>
                                        </a>&nbsp;';
                                    $val = "REJECTED";
                                    $data1 .= '<a class="light-blue-color status-block-section" href="javascript:void(0);" onClick="updateStatus('.$data->id.',\''.$val.'\')"  title="'.translation('reject').'" ><i class="fa fa-times" ></i></a>&nbsp;';

                                    
                                }

                             }

                             return $data1;

                            })
                            ->editColumn('build_action_btn',function($data) use($status)
                            { 
                                if($data->status == "APPROVED" || $data->status =="REQUESTED" || $status == 'polled_raised')
                                {
                                    $view_href =  $this->module_url_path.'/view/'.$status.'/'.base64_encode($data->id);
                                    return '<a class="green-color" href="'.$view_href.'" title="View"><i class="fa fa-eye" ></i></a>';                           
                                }
                                     
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

        $suggestion_category_table         =    $this->SuggestionCategoriesModel->getTable();                  
        $prefixed_suggestion_category_table=    DB::getTablePrefix().$this->SuggestionCategoriesModel->getTable();

        if($status == 'created')
        {
            $obj_user = DB::table($suggestion_table)
                                ->select(DB::raw($prefixed_suggestion_table.".id as id,".
                                                 $prefixed_suggestion_table.".subject as subject, ".
                                                 $prefixed_suggestion_table.".suggestion_date as suggestion_date, ".
                                                 $prefixed_suggestion_table.".user_role as user_role, ".
                                                 $prefixed_suggestion_table.".duration as duration, ".
                                                 $prefixed_suggestion_table.".dislike_count as dislike_count, ".
                                                 $prefixed_suggestion_category_table.".category as category, ".
                                                 "CONCAT(".$prefixed_user_translation_table.".first_name,' ',"
                                                          .$prefixed_user_translation_table.".last_name) as user_name,".
                                                 $prefixed_suggestion_table.".status as status"))
                                ->join($user_translation_table,$user_translation_table.'.user_id', ' = ',$suggestion_table.'.user_id')
                               /* ->join($suggestion_polling_table,$suggestion_polling_table.'.suggestion_id', ' = ',$suggestion_table.'.id')*/
                                ->join($suggestion_category_table,$suggestion_category_table.'.id', ' = ',$suggestion_table.'.category')
                                ->where($user_translation_table.'.locale','=',$locale)
                                ->where($suggestion_table.'.school_id','=',$this->school_id)
                                ->where($suggestion_table.'.user_role','=','employee')
                               /* ->whereNotIn($prefixed_suggestion_table.'.id',function($q)use($suggestion_polling_table){
                                     
                                     $q->select('id')  
                                        ->from($suggestion_polling_table)
                                      ->where('from_user_id',$this->user_id);

                                       
                                })
                                ->where($suggestion_polling_table.'.from_user_id',$this->user_id)
                                ->groupBy($suggestion_polling_table.'.suggestion_id')*/
                                /*->whereIn($suggestion_table.'.status',['REQUESTED','APPROVED'])*/
                                ->where($suggestion_table.'.user_id','=',$this->user_id)
                                ->where($suggestion_table.'.academic_year_id','=',$this->academic_year)
                                ->orderBy($prefixed_suggestion_table.'.created_at','DESC');

        }
        elseif($status == 'polled_requests')
        {
            $obj_user = DB::table($suggestion_table)
                                ->select(DB::raw($prefixed_suggestion_table.".id as id,".
                                                 $prefixed_suggestion_table.".subject as subject, ".
                                                 $prefixed_suggestion_table.".suggestion_date as suggestion_date, ".
                                                 $prefixed_suggestion_table.".user_role as user_role, ".
                                                 $prefixed_suggestion_table.".duration as duration, ".
                                                 $prefixed_suggestion_table.".dislike_count as dislike_count, ".
                                                 $prefixed_suggestion_table.".like_count as like_count, ".
                                                 $prefixed_suggestion_polling_table.".vote as vote, ".
                                                 $prefixed_suggestion_category_table.".category as category, ".
                                                 "CONCAT(".$prefixed_user_translation_table.".first_name,' ',"
                                                          .$prefixed_user_translation_table.".last_name) as user_name,".
                                                 $prefixed_suggestion_table.".status as status"))
                                ->join($user_translation_table,$user_translation_table.'.user_id', ' = ',$suggestion_table.'.user_id')
                                ->join($suggestion_polling_table,$suggestion_polling_table.'.suggestion_id', ' = ',$suggestion_table.'.id')
                                ->join($suggestion_category_table,$suggestion_category_table.'.id', ' = ',$suggestion_table.'.category')
                                ->where($user_translation_table.'.locale','=',$locale)
                                ->where($suggestion_table.'.school_id','=',$this->school_id)
                                /*->where($suggestion_table.'.status','=','POLL_RAISED')*/
                                ->where($suggestion_polling_table.'.from_user_id','=',$this->user_id)
                                ->where($suggestion_table.'.academic_year_id','=',$this->academic_year)
                                ->orderBy($prefixed_suggestion_table.'.created_at','DESC');

        }
        else
        {
            $obj_user = DB::table($suggestion_table)
                                    ->select(DB::raw($prefixed_suggestion_table.".id as id,".
                                                     $prefixed_suggestion_table.".subject as subject, ".
                                                     $prefixed_suggestion_table.".suggestion_date as suggestion_date, ".
                                                     $prefixed_suggestion_table.".user_role as user_role, ".
                                                     $prefixed_suggestion_table.".duration as duration, ".
                                                     $prefixed_suggestion_table.".poll_raised as poll_raised, ".
                                                     $prefixed_suggestion_table.".like_count as like_count, ".
                                                     $prefixed_suggestion_table.".dislike_count as dislike_count, ".
                                                     $prefixed_suggestion_category_table.".category as category, ".
                                                     "CONCAT(".$prefixed_user_translation_table.".first_name,' ',"
                                                              .$prefixed_user_translation_table.".last_name) as user_name,".
                                                     $prefixed_suggestion_table.".status as status"))
                                    ->join($user_translation_table,$user_translation_table.'.user_id', ' = ',$suggestion_table.'.user_id')
                                    ->join($suggestion_category_table,$suggestion_category_table.'.id', ' = ',$suggestion_table.'.category')
                                    ->where($user_translation_table.'.locale','=',$locale)
                                    ->where($suggestion_table.'.school_id','=',$this->school_id)
                                    ->where($suggestion_table.'.status','=',$str)
                                    ->where($suggestion_table.'.academic_year_id','=',$this->academic_year)
                                    ->orderBy($prefixed_suggestion_table.'.created_at','DESC');
        }



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

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }


        $suggestions    =   $this->SuggestionModel
                                 ->with('get_polling_details.user_name','get_user_details')
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
        $update_record = '';
        $school_id      = $this->school_id;
        $academic_year  = $this->academic_year;

        $id             = $request->input('id');
        $status         = $request->input('status');
        $data           = $this->BaseModel->where('id',$id)->first();
        if($status == 'APPROVED')
        {
            $update_record  = $this->BaseModel->where('id',$id)->where('school_id',$school_id)->where('academic_year_id',$academic_year)->update(['status'=>$status]);
            $result = $this->send_notifications('approved',$data);
        }
        if($status == 'REJECTED')
        {
            $update_record  = $this->BaseModel->where('id',$id)->delete();
            $result = $this->send_notifications('rejected',$data);
        }

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
        $users          = implode(',',$request->input('users'));
        $date           = date('Y-m-d');
        $id             = $request->input('id');
        $duration       = $request->input('duration');
        $user_type      = $users;
        $data           = $this->BaseModel->where('id',$id)->first();

        if($id)
        {
            $arr_data['duration']        =   $duration;
            $arr_data['assigned_roles']  =   $user_type;
            $arr_data['status']          =   'POLL_RAISED';
            $arr_data['poll_raised_date']=   $date;
            $arr_data['poll_raised']     =   1;
            $update_record = $this->BaseModel->where('id',$id)->where('school_id',$school_id)->where('academic_year_id',$academic_year)->update($arr_data);

            if($update_record)
            {
                $result = $this->send_notifications('poll_raised',$data,$user_type);
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
        $this->arr_view_data['module_icon']     = $this->module_icon;

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
        $arr_data['user_role']         =   config('app.project.role_slug.employee_role_slug');
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
            Flash::success($this->module_title.' '.translation('created_successfully'));   
            return redirect()->back();
        }
        else
        {
            Flash::error(translation('problem_occured_while_creating').' '.$this->module_title);   
            return redirect()->back();   
        }
    }

    public function manage_employee_suggestions($status)
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
        $this->arr_view_data['module_icon']     = $this->module_icon;

        return view($this->module_view_folder.'.manage_index', $this->arr_view_data);
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

    public function send_notifications($status,$data,$user_role=FALSE)
    {

        $result = '';
        if($status == 'poll_raised')
        {   
            $arr_roles = explode(',',$user_role);

            if(isset($arr_roles) && count($arr_roles)>0)
            {
                foreach ($arr_roles as $key => $value) {
                    $obj_data = [];
                    $obj_data = $this->CommonDataService->get_permissions($value,$this->academic_year,$this->school_id);
                    if(isset($obj_data) && count($obj_data)>0)
                    {
                        foreach ($obj_data as $key1 => $data1) {
                            if(isset($data1['notifications']['notification_permission']) && $data1['notifications']['notification_permission']!=null)
                            {
                                $user_id = '';
                                $permissions = [];
                                $permissions = json_decode($data1['notifications']['notification_permission'],true);

                                if($value == config('app.project.role_slug.parent_role_slug'))
                                {
                                    $user_id         =  $data1['parent_id'];
                                }
                                else
                                {
                                    $user_id         =  $data1['user_id'];    
                                }  
                                $result = $this->build_notifications($permissions,$data1,$user_id,$data,$status,$value);
                            }
                        }
                    }
                }
            }
        }
        else
        {
            $obj_data = $this->CommonDataService->get_user_permissions($data->user_id,$data->user_role,$this->academic_year);
            
            if(isset($obj_data['notifications']['notification_permission']) && $obj_data['notifications']['notification_permission']!=null)
            {
                $user_id = '';
                $permissions = [];
                $permissions = json_decode($obj_data['notifications']['notification_permission'],true);
                if($data->user_role == config('app.project.role_slug.parent_role_slug'))
                {
                    $user_id         =  $obj_data['parent_id'];
                }
                else
                {
                    $user_id         =  $obj_data['user_id'];    
                }  
                $result = $this->build_notifications($permissions,$obj_data,$user_id,$data,$status,$data->user_role);
               
            }
        }
        return $result;
    }

    public function build_notifications($permissions,$users,$user_id,$data,$status,$role)
    {
        
        if(array_key_exists('suggestions.app',$permissions))
        {
         
            $arr_notification = [];
            $arr_notification['school_id']          =  $this->school_id;
            $arr_notification['from_user_id']       =  $this->user_id;
            $arr_notification['to_user_id']         =  $user_id;     
            
            $arr_notification['user_type']          =  config('app.project.role_slug.school_admin_role_slug');
            if($status == 'approved')
            { 
                $arr_notification['notification_type']  =  'Suggestion Approved';
                $arr_notification['title']              =  'Suggestion Approved: Your suggestion subject : '.$data->subject.' is approved by school admin.'; 
                $arr_notification['view_url']           =  url('/').'/'.$data->user_role.'/suggestions';
            }
            elseif($status == 'rejected')
            {
               
                $arr_notification['notification_type']  =  'Suggestion Rejected';
                $arr_notification['title']              =  'Suggestion Rejected: Your suggestion subject : '.$data->subject.' is rejected by school admin.';    
            }
            else
            {
                $arr_notification['notification_type']  =  'Poll Raised Suggestion';
                $arr_notification['title']              =  'Poll Raised Suggestion: School Admin Raised Poll on suggestion subject : '.$data->subject.'.'; 
                $arr_notification['view_url']           =  url('/').'/'.$role.'/suggestions/poll_raised';
            }
            
            $result = NotificationModel::create($arr_notification);
        }
        $user_details = $this->UserModel->where('id',$data->user_id)->first();

        $details          = [
                                    'first_name'  =>  isset($users['get_user_details']['first_name'])?ucwords($users['get_user_details']['first_name']):'',
                                    'email'       =>  isset($users['get_user_details']['email'])?$users['get_user_details']['email']:'',
                                    'mobile_no'   =>  isset($users['get_user_details']['mobile_no'])?$users['get_user_details']['mobile_no']:'',
                                    'subject'     =>  isset($data->subject)?ucwords($data->subject):'',
                                    'suggestion_given_by' => (isset($user_details->first_name)?ucwords($user_details->first_name):'').' '.(isset($user_details->last_name)?ucwords($user_details->last_name):'')
                            ];
        if(array_key_exists('suggestions.sms',$permissions))
        {
            $arr_sms_data = $this->built_sms_data($details,$status);
            $sms_status   = $this->CommonDataService->send_sms($arr_sms_data,$this->school_id);
        }
        if(array_key_exists('suggestions.email',$permissions))
        {
            $arr_mail_data = $this->built_mail_data($details,$status);
            $email_status  = $this->EmailService->send_mail($arr_mail_data,$this->school_id);
        }
        //return $result;
        return true;
    }

    public function built_mail_data($arr_data,$status)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [];
            if($status!='approved' && $status!='rejected')
            {
                $arr_built_content = [
                                      'FIRST_NAME'         => $arr_data['first_name'],
                                      'TITLE'              => $arr_data['subject'],
                                      'SCHOOL_ADMIN'       => $this->CommonDataService->get_school_name($this->school_id),
                                      'SUGGESTION_GIVEN_BY'=> $arr_data['suggestion_given_by']
                                     ];    
            }
            else
            {
                $arr_built_content = [
                                      'FIRST_NAME'         => $arr_data['first_name'],
                                      'TITLE'              => $arr_data['subject'],
                                      'SCHOOL_ADMIN'       => $this->CommonDataService->get_school_name($this->school_id),
                                      'STATUS'             => ucwords($status)
                                     ];    
            }
            
            $arr_mail_data                        = [];
            if($status!='approved' && $status!='rejected')
            {
                $arr_mail_data['email_template_slug'] = 'suggestion_poll_raised';                   
            }
            else
            {
                $arr_mail_data['email_template_slug'] = 'suggestion_status';                      
            }
        
            $arr_mail_data['arr_built_content']   = $arr_built_content;
            $arr_mail_data['user']                = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }

    public function built_sms_data($arr_data,$status)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {

            $arr_built_content = [];

            if($status!='approved' && $status!='rejected')
            {
                $arr_built_content = [
                                      'TITLE'              => $arr_data['subject'],
                                      'SUGGESTION_GIVEN_BY'=> $arr_data['suggestion_given_by']
                                     ];    
            }
            else
            {
                $arr_built_content = [
                                      'TITLE'              => $arr_data['subject'],
                                      'STATUS'             => ucwords($status)
                                     ];    
            } 
           
            $arr_sms_data                      = [];
            if($status!='approved' && $status!='rejected')
            {
                $arr_sms_data['sms_template_slug'] = 'suggestion_poll_raised';                   
            }
            else
            {
                $arr_sms_data['sms_template_slug'] = 'suggestion_status';                      
            }    
                
            $arr_sms_data['arr_built_content'] = $arr_built_content;
            $arr_sms_data['user']              = $arr_data;
            $arr_sms_data['mobile_no']         = $arr_data['mobile_no'];

            return $arr_sms_data;
        }
        return FALSE;
    }
    
}