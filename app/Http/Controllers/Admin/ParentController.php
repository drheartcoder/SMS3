<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Traits\MultiActionTrait;
use App\Common\Services\CommonDataService;
use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\ParentModel;
use App\Models\SchoolParentModel;
use App\Models\UserRoleModel;   
use App\Models\RoleModel;
use App\Models\ActivationModel;
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
                                    SchoolParentModel $SchoolParentModel,
                                    CommonDataService $CommonDataService

                                )
    {
        $this->UserModel                    = $user;
        $this->UserTranslationModel         = $translation;
        $this->UserRoleModel                = $user_role_model;
        $this->RoleModel                    = $role_model;
        $this->ParentModel                  = $parent;
        $this->BaseModel                    = $this->UserModel;
        $this->SchoolParentModel            = $SchoolParentModel;
        $this->ActivityLogsModel            = $activity_logs; /* Activity Model */  
        $this->CommonDataService            = $CommonDataService; 

        $this->user_profile_base_img_path   = public_path().config('app.project.img_path.user_profile_images');
        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/users/parent");
        
        $this->module_title                 = translation("parent");
        $this->modyle_url_slug              = translation("parent");

        $this->module_view_folder           = "admin.users";
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
        $this->arr_view_data['role']            = 'parent';
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    function get_users_details(Request $request,$type,$fun_type='')
    {     
        $role =$type;
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
        

        $obj_user = DB::table($school_parent_details)
                                ->select(DB::raw($school_parent_details.".id,".
                                                 $school_parent_details.".parent_id as user_id,".
                                                 $school_parent_details.".school_id as school_id,".
                                                 $prefixed_user_details.".email as email, ".
                                                 $school_parent_details.".is_active as is_active, ".
                                                 $user_details.".national_id as national_id,".
                                                 $parent_details.".parent_no,".
                                                 $user_details.".mobile_no as mobile_no,".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
                                                          .$prefixed_user_trans_table.".last_name) as user_name"
                                                 ))
                                ->join($parent_details,$school_parent_details.'.parent_id','=',$parent_details.'.user_id')
                                ->join($user_details,$parent_details.'.user_id','=',$user_details.'.id')
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                ->where($user_trans_table.'.locale','=',$locale)
                                ->whereNull($school_parent_details.'.deleted_at')
                                ->orderBy($school_parent_details.'.id','DESC');
       
        /* ---------------- Filtering Logic ----------------------------------*/                    
            
        if($fun_type == 'export'){
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
                                 ->orWhereRaw("(".$school_parent_details.".school_id LIKE '%".$search_term."%') ")
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
        
        $role = Sentinel::findRoleById(1);    
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;
       
        $obj_user        = $this->get_users_details($request,$type);

        $role = 'parent';



        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('users.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data) 
                            {
                                return base64_encode($data->user_id);
                            })
                            ->editColumn('user_name',function($data)
                            {
                                if($data->user_name!=null && $data->user_name!='')
                                {
                                    $name = explode(' ', $data->user_name);
                                  
                                    return  ucfirst($name[0]).' '.ucfirst($name[1]);
                                }
                                else
                                {
                                    return  '-';
                                }
                            });
        }                    

        $json_result     = $json_result->editColumn('build_action_btn',function($data) use ($role,$arr_current_user_access)
                            {
                                if($role != null)
                                {       
 
                                    $view_href =  $this->module_url_path.'/view/'.base64_encode($data->user_id);
                                    $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>';
                                    
                                    return $build_view_action.'&nbsp;';  
                                }
                            })
                            ->editColumn('school_name',function($data)
                            {
                                if($data->school_id != null)
                                {
                                    return $this->CommonDataService->get_school_name($data->school_id);
                                }
                            })
                            ->editColumn('build_checkbox',function($data){
                           
                                
                                
                            return '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    public function view($enc_id)
    {   
        $id = base64_decode($enc_id);
     
        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
        
        $obj_user       =   $this->BaseModel
                            ->where('id','=',$id)
                            ->first();
                            
        $arr_user = [];
        if($obj_user)
        {
            $arr_user = $obj_user->toArray();
        }

        $parent_details =   $this->ParentModel
                            ->where('user_id',$id)
                            ->first();

        $arr_parent_details = [];
        if($parent_details)
        {
            $arr_parent_details = $parent_details->toArray();
        }

        $arr_data = [];                                    
        $arr_data['users'] = $arr_user;
        $arr_data['parent'] = $arr_parent_details;
        
        $this->arr_view_data['role']                         = 'parent';
        $this->arr_view_data['page_title']                   = translation("view").' parent ';
        $this->arr_view_data['module_title']                 = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['arr_data']                     = $arr_data;
        
        $this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
        $this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        
        return view($this->module_view_folder.'.view', $this->arr_view_data);


    }


    
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
    | export() : Export List
    | Auther  : Vrajesh
    | Date    : 14-12-2018
    */

    public function export(Request $request)
    {
        $file_type = config('app.project.export_file_formate');
        $obj_data = $this->get_users_details($request,'school_admin','export');

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
                        $arr_fields['sr_no']        = translation('sr_no');
                        $arr_fields['name']         = translation('name');
                        $arr_fields['email']        = translation('email');
                        $arr_fields['school_name']  = translation('school_name');

                        $sheet->row(2, [ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                        $sheet->row(4, $arr_fields);
                        
                        // To set Colomn head
                        $j = 'A'; $k = '4';
                        for($i=0; $i<=3;$i++)
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
                                if($result->school_id != null && $result->school_id != '')
                                {
                                    $school_name = $this->CommonDataService->get_school_name($result->school_id);
                                }else{
                                    $school_name = '-';
                                }
                                $arr_tmp[$key]['sr_no']         = $count++;
                                $arr_tmp[$key]['name']          = $result->user_name;
                                $arr_tmp[$key]['email']         = $result->email;
                                $arr_tmp[$key]['school_name']   = $school_name;
                            }
                            $sheet->rows($arr_tmp);
                        }
                    });
                })->export($file_type);     
        }
        
        if($request->file_format == 'pdf')
        {
            $this->arr_view_data['arr_data'] = $obj_data;

            foreach($this->arr_view_data['arr_data'] as $key => $row)
            {
                $school_name = '';
                if($row->school_id != null && $row->school_id != '')
                {
                    $school_name = $this->CommonDataService->get_school_name($row->school_id);
                }else{
                    $school_name = '-';
                }
                $row->school_name = $school_name;
            }

            $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
            return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
        }
    }

}