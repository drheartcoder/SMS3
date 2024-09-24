<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;

use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\ParentModel;
use App\Models\UserRoleModel;   
use App\Models\RoleModel;
use App\Models\ActivationModel;
use App\Models\StudentModel;
use App\Models\SchoolParentModel;
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
use Datatables;
use PDF;

class ParentController extends Controller
{
    use MultiActionTrait;
    public function __construct(    
                                    UserModel $user,
                                    UserTranslationModel $translation,
                                    UserRoleModel $user_role_model,
                                    RoleModel $role_model,
                                    ActivityLogsModel $activity_logs,
                                    ParentModel $parent,
                                    StudentModel $student,
                                    SchoolParentModel $school_parent,
                                    CommonDataService $CommonDataService
                                )
    {
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->ParentModel                  = $parent;
        $this->ActivityLogsModel            = $activity_logs; 
        $this->SchoolParentModel            = $school_parent;
        $this->BaseModel                    = $this->SchoolParentModel;
        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.role_slug.school_admin_role_slug')."/parent");
        
        $this->module_title                 = translation("parent");
        $this->modyle_url_slug              = translation("parent");

        $this->module_view_folder           = "schooladmin.parent";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-user';

        $this->school_id                    = \Session::has('school_id') ? \Session::get('school_id') : '0' ;
        $this->academic_year                = Session::get('academic_year');
        $this->CommonDataService            = $CommonDataService;
        $this->StudentModel                 = $student;


        $this->first_name = $this->last_name =$this->ip_address ='';

        $this->arr_view_data['page_title']      = translation("parent");
        $this->arr_view_data['base_url']      = $this->user_profile_base_img_path;
        $this->arr_view_data['image_path']      = $this->user_profile_public_img_path;

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            
            $this->user_id           = $obj_data->id; 
        }
        /* Activity Section */
    }   

    public function index(Request $request)
    {   

        $this->arr_view_data['role']            = 'parent';
        
        $this->arr_view_data['module_title']    = ucfirst(translation("manage"))." ".strtolower(str_plural($this->module_title));
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    function get_users_details(Request $request,$type,$fun_type='')
    {     
        
        $locale = '';

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }

        $user_details             = $this->UserModel->getTable();
        $prefixed_user_details    = DB::getTablePrefix().$this->UserModel->getTable();

        $user_trans_table             = $this->UserTranslationModel->getTable();                  
        $prefixed_user_trans_table    = DB::getTablePrefix().$this->UserTranslationModel->getTable();

        $parent_details             = $this->ParentModel->getTable();                  
        

        $school_parent_details    = $this->SchoolParentModel->getTable();                  

        $student    = $this->StudentModel->getTable();                   

        $obj_user = DB::table($student)
                                ->select(DB::raw($school_parent_details.".id as id,".
                                                 $prefixed_user_details.".email as email, ".
                                                 $school_parent_details.".is_active as is_active, ".
                                                 $user_details.".national_id as national_id,".
                                                 $parent_details.".parent_no,".
                                                 $user_details.".mobile_no as mobile_no,".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
                                                          .$prefixed_user_trans_table.".last_name) as user_name"
                                                 ))
                                ->join($parent_details,$student.'.parent_id','=',$parent_details.'.user_id')
                                ->join($school_parent_details,$school_parent_details.'.parent_id','=',$parent_details.'.user_id')
                                ->join($user_details,$parent_details.'.user_id','=',$user_details.'.id')
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                ->where($user_trans_table.'.locale','=',$locale)
                                ->where($school_parent_details.'.school_id',$this->school_id)
                                ->whereNull($school_parent_details.'.deleted_at')
                                ->where('academic_year_id',$this->academic_year)
                                ->groupBy($student.'.parent_id')
                                ->orderBy($school_parent_details.'.id','DESC');
       
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
                                     ->orWhereRaw("(".$user_details.".mobile_no LIKE '%".$search_term."%') ")
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
        
        $arr_current_user_access = $this->CommonDataService->current_user_access();
       
        $obj_user        = $this->get_users_details($request,$type);

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('users.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data)
                            {
                                return base64_encode($data->id);
                            });
        }                    

        $json_result     = $json_result

                            ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                            {
                                $build_edit_action = $build_status_btn = $build_view_action = $build_delete_action ='';

                                $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation("view").'"><i class="fa fa-eye" ></i></a>';

                                if(array_key_exists('parent.update',$arr_current_user_access))
                                {       

                                    if($data->is_active != null && $data->is_active == "0")
                                    {   
                                        $build_status_btn = '<a class="blue-color" title="'.translation("activate").'" href="'.$this->module_url_path.'/activate/'.base64_encode($data->id).'" 
                                        onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-lock"></i></a>';
                                    }
                                    elseif($data->is_active != null && $data->is_active == "1")
                                    {
                                        $build_status_btn = '<a title="'.translation("deactivate").'" class="light-blue-color" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-unlock"></i></a>';
                                    }
                                    
                                }
                                if(array_key_exists('parent.delete',$arr_current_user_access))
                                {
                                    $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                    $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation("delete").'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                }

                                return $build_view_action.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_status_btn.'&nbsp;'.$build_delete_action;
                            })
                             
                            ->editColumn('build_checkbox',function($data) use($arr_current_user_access)
                            {
                                $build_checkbox='';
                                if(array_key_exists('parent.update',$arr_current_user_access) || array_key_exists('parent.delete',$arr_current_user_access))
                                {
                                    $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                }    
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
            if(Session::has('locale')){
                $locale = Session::get('locale');
            }
            else{
                $locale = 'en';
            }
            
            $obj_user   =   $this->BaseModel
                                ->with('parent_details','user_details')
                                ->where('id','=',$id)
                                ->first();
            $arr_data = [];                    
            if(!empty($obj_user) && count($obj_user)>0) {
                
                $kids = $this->StudentModel
                                        ->with('get_user_details','get_level_class.class_details','get_level_class.level_details')
                                        ->where('parent_id',$obj_user->user_details->id)
                                        ->where('school_id',$this->school_id)
                                        ->where('has_left',0)
                                        ->get();
                

                $arr_data = $obj_user;
                $this->arr_view_data['module_title']                 = translation('view').' '.$this->module_title;
                $this->arr_view_data['module_url_path']              = $this->module_url_path;
                $this->arr_view_data['arr_data']                     = $arr_data;
                $this->arr_view_data['arr_kids']                     = $kids;

                $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
                $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;
                $this->arr_view_data['theme_color']                  = $this->theme_color;
                $this->arr_view_data['module_icon']       = $this->module_icon;
                
                return view($this->module_view_folder.'.view', $this->arr_view_data);    
            }                   
            else{

                Flash::error('no_data_available');
                return redirect()->back();
            }
            
        }
        Flash::error(translation('something_went_wrong'));
        return redirect($this->module_url_path);
    }


    //soft delete code here
    public function delete($enc_id = FALSE)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_delete(base64_decode($enc_id)))
        {
            $this->actionLogDelete();
            Flash::success($this->module_title.' deleted successfully');
        }
        else
        {
            Flash::error('Problem occured while '.$this->module_title.' deletion ');
        }

        return redirect()->back();
    }

    public function perform_delete($id)
    {
        $delete= $this->BaseModel->where('id',$id)->delete();
        $delete= $this->BuyerModel->where('user_id',$id)->delete();
        
        if($delete)
        {
            return TRUE;
        }

        return FALSE;
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
                            $arr_fields['parent_number']= translation('parent_number');
                            $arr_fields['name']           = translation('name');
                            $arr_fields['email']          = translation('email');
                            $arr_fields['national_id']    = translation('national_id');
                            $arr_fields['mobile_no']      = translation('mobile_no');
                            $arr_fields['active']         = translation('active_status');
                            
                            $sheet->row(2, ['',ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                            $sheet->row(4, $arr_fields);
                            if(sizeof($obj_data)>0) 
                            {
                                
                                $arr_tmp = [];
                                foreach($obj_data as $key => $result)
                                {
                                    $status = "";
                                    if($result->is_active==1)
                                    {
                                        $status = "Active";
                                    }
                                    elseif($result->is_active==0)
                                    {
                                        $status = "InActive";
                                    }
                                    $arr_tmp[$key]['id']             = intval($key+1);
                                    $arr_tmp[$key]['parent_number']  = $result->parent_no;
                                    $arr_tmp[$key]['name']           = ucwords($result->user_name);
                                    $arr_tmp[$key]['email']          = $result->email;
                                    $arr_tmp[$key]['national_id']    = $result->national_id;
                                    $arr_tmp[$key]['mobile_no']      = $result->mobile_no;
                                    $arr_tmp[$key]['active']         = $status;
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
}