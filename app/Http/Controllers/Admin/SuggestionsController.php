<?php

namespace App\Http\Controllers\Admin;

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
use App\Common\Services\CommonDataService;
/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
/*Activity Log */

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use PDF;
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
                                    CommonDataService $CommonDataService

                                )
    {
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->BaseModel                    = $this->UserModel;
        $this->ActivityLogsModel            = $activity_logs; /* Activity Model */   
        $this->SuggestionModel              = $suggestion;
        $this->SuggestionPollingModel       = $polling;
        $this->SchoolProfileTranslationModel= $school;
        $this->SchoolProfileModel           = $profile;
        $this->SchoolTemplateModel          = $template;
        $this->CommonDataService            = $CommonDataService;

        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/suggestions");
        
        $this->module_title                 = translation("suggestions");
        $this->modyle_url_slug              = translation("suggestions");

        $this->module_view_folder           = "admin.suggestions";
        $this->theme_color                  = theme_color();

        $this->first_name = $this->last_name =$this->ip_address ='';

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            
        }
        /* Activity Section */



    }   

    public function index(Request $request)
    {   
        $page_title = translation("manage")." ".str_plural($this->module_title);
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function get_records(Request $request,$type='')
    {
        
        
        $arr_current_user_access =[];
        $role = Sentinel::findRoleById(1);
                
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;
       
        $obj_user        = $this->get_suggestion_details($request,$type);

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
                            ->editColumn('school_no',function($data)
                            { 
                                 
                                if($data->school_no!=null && $data->school_no!='')
                                {
                                    return $data->school_no;
                                }
                                else
                                {
                                    return  '-';
                                }

                            })
                            ->editColumn('school_name',function($data)
                            { 
                                 
                                if($data->school_no!=null && $data->school_no!='')
                                {
                                    return $this->CommonDataService->get_school_name($data->school_no);
                                }
                                else
                                {
                                    return  '-';
                                }

                            })
                            ->editColumn('suggestion_date',function($data)
                            { 
                                 
                                if($data->suggestion_date!=null && $data->suggestion_date!='')
                                {
                                    return getDateFormat($data->suggestion_date);
                                }
                                else
                                {
                                    return  '-';
                                }

                            })
                            ->editColumn('build_action_btn',function($data) 
                            { 
                                    $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                    return '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>';                            
                                     
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    function get_suggestion_details(Request $request,$type,$fun_type='')
    {

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }

        $suggestion_table                     =     $this->SuggestionModel->getTable();                  
        $prefixed_suggestion_table            =     DB::getTablePrefix().$this->SuggestionModel->getTable();


        $suggestion_polling_table             =     $this->SuggestionPollingModel->getTable();                  
        $prefixed_suggestion_polling_table    =     DB::getTablePrefix().$this->SuggestionPollingModel->getTable();

        $school_trans_table                   =     $this->SchoolProfileTranslationModel->getTable();                  
        $prefixed_school_trans_table          =     DB::getTablePrefix().$this->SchoolProfileTranslationModel->getTable();

        $obj_user = DB::table($suggestion_table)
                                ->select(DB::raw($prefixed_suggestion_table.".id as id,".
                                                 $prefixed_suggestion_table.".subject as subject, ".
                                                 $prefixed_suggestion_table.".user_role as user_role, ".
                                                 $prefixed_suggestion_table.".status as status,".
                                                 $prefixed_suggestion_table.".suggestion_date as suggestion_date,".
                                                 $prefixed_suggestion_table.".school_id as school_no"))
                                ->where($suggestion_table.'.status','=','APPROVED')
                                ->orderBy($prefixed_suggestion_table.'.created_at','DESC');

        if($fun_type == 'export'){
            $search_term = $request->input('search');
        }else{
            $search = $request->input('search');
            $search_term = $search['value'];
        }

        if($request->has('search') && $search_term!="")
        {
            $obj_user = $obj_user->WhereRaw("( (".$prefixed_suggestion_table.".subject LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_suggestion_table.".school_id LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_suggestion_table.".user_role LIKE '%".$search_term."%' ) )");
        }

        if($fun_type=="export"){
            return $obj_user->get();
        }else{
            return $obj_user;
        }

    }


    public function view($enc_id)
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
                                 ->where('id',$id)
                                 ->first();
        $pollingUsers   =   $this->SuggestionPollingModel
                                 ->with('user_name')
                                 ->where('suggestion_id',$id)
                                 ->get();
        

        $obj_template    =   $this->SchoolTemplateModel
                                  ->with("get_question_category")
                                  ->where('is_active',1)
                                  ->orderBy('position','asc')
                                  ->get();

        $obj_school_translations    =   $this->SchoolProfileModel
                                             ->where('school_no',$suggestions->school_id)
                                             ->get()
                                             ->toArray();

        foreach ($obj_template as $key => $template) {
            if(strcmp($template['title'],'School name')==0)
            {
                $school_name = $obj_school_translations[$key]['value'];
            }
        }
        
        $arr_data   =   [];
        if($suggestions && $pollingUsers)
        {
            $arr_data['pollingUsers']  =   $pollingUsers->toArray();
            $arr_data['suggestions']   =   $suggestions->toArray();
        }

        $this->arr_view_data['page_title']                   = translation("view").' '.str_singular($this->module_title);
        $this->arr_view_data['module_title']                 = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['arr_data']                     = $arr_data;
        $this->arr_view_data['school_name']                  = $school_name;
        $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
        $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        
        return view($this->module_view_folder.'.view', $this->arr_view_data);

    }

    /*
    | export() : Export List
    | Auther  : Vrajesh
    | Date    : 14-12-2018
    */

    public function export(Request $request)
    {
        $file_type = config('app.project.export_file_formate');
        $obj_data = $this->get_suggestion_details($request,'','export');

        if(sizeof($obj_data)<=0){
            Flash::error(translation("no_records_found_to_export"));
            return redirect()->back();
        }
        if(sizeof($obj_data)>500 && $request->file_format == $file_type ){
            Flash::error(translation("too_many_records_to_export"));
            return redirect()->back();
        }
        if($request->file_format == $file_type){
            \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel) use($obj_data) 
                {
                    $excel->sheet(ucwords($this->module_title), function($sheet) use($obj_data) 
                    {
                        $arr_fields['sr_no']                = translation('sr_no');
                        $arr_fields['subject']              = translation('subject');
                        $arr_fields['suggestion_date']      = translation('suggestion_date');
                        $arr_fields['from_school_number']   = translation('from_school_number');
                        $arr_fields['from_school_name']     = translation('from_school_name');
                        $arr_fields['role']                 = translation('role');

                        $sheet->row(2, [ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                        $sheet->row(4, $arr_fields);
                        
                        // To set Colomn head
                        $j = 'A'; $k = '4';
                        for($i=0; $i<=6;$i++)
                        {
                            $sheet->cell($j.$k, function($cells) {
                                $cells->setBackground('#495b79');
                                $cells->setFontWeight('bold');
                                $cells->setAlignment('center');
                                $cells->setFontColor('#ffffff');
                            });
                            $j++;
                        }

                        if(sizeof($obj_data)>0)
                        {
                            $arr_tmp = [];
                            $count = 1;
                            foreach($obj_data as $key => $result)
                            {
                                $school_name = '';
                                if($result->school_no != null && $result->school_no != '')
                                {
                                    $school_name = $this->CommonDataService->get_school_name($result->school_no);
                                }else{
                                    $school_name = '-';
                                }
                                $arr_tmp[$key]['sr_no']             = $count++;
                                $arr_tmp[$key]['subject']           = $result->subject;
                                $arr_tmp[$key]['suggestion_date']   = $result->suggestion_date;
                                $arr_tmp[$key]['school_no']         = $result->school_no;
                                $arr_tmp[$key]['school_name']       = $school_name;
                                $arr_tmp[$key]['user_role']         = $result->user_role;
                            }
                            $sheet->rows($arr_tmp);
                        }
                    });
                })->export($file_type);     
        }
        
        if($request->file_format == 'pdf')
        {
            foreach($obj_data as $key => $row)
            {
                $school_name = '';
                if($row->school_no != null && $row->school_no != '')
                {
                    $school_name = $this->CommonDataService->get_school_name($row->school_no);
                }else{
                    $school_name = '-';
                }
                $row->school_name = $school_name;
            }

            $this->arr_view_data['arr_data'] = $obj_data;

            $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
            return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
        }
    }

    
}