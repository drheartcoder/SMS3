<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\ActivityLogsModel;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\UserTranslationModel;
use App\Events\ActivityLogEvent;

use Sentinel;
use Validator;
use Session;
use Flash;
use Datatables;
use DB;

class ActivityLogController extends Controller
{

	public function __construct(ActivityLogsModel $activity_logs,
                                UserTranslationModel $translation,
                                RoleModel $role,
                                UserModel $user
                                )
	{
        $this->arr_view_data      = [];
        $this->ActivityLogsModel  = $activity_logs;
        $this->BaseModel          = $this->ActivityLogsModel;
        $this->UserModel          = $user;
        $this->UserTranslationModel = $translation;
        $this->RoleModel            = $role;
        $this->module_url_path    = url(config('app.project.admin_panel_slug')."/activity_log");
        $this->module_view_folder = "admin.activity_logs";
        $this->arr_exempted_role  = config('app.project.base_roles');
        $this->module_title       = translation("activity_log");
        $this->theme_color        = theme_color();

        $this->module_icon        = "fa-history";
	}

    public function index()
    {
        $this->arr_view_data['page_title']      = translation("manage")." ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    public function get_records(Request $request)
    {
        $obj_activity    = $this->get_activity_log($request);

        $json_result     = Datatables::of($obj_activity);

        $json_result     = $json_result->blacklist(['id']);

        $json_result     = $json_result->editColumn('module_name',function($data) 
                            {
                                if($data->module_name != null)
                                {
                                    return $data->module_name;
                                }
                                else
                                {
                                    return "NA";
                                }
                            })
                            ->editColumn('enc_id',function($data) 
                            {
                                return  base64_encode(($data->id));
                            })
                            ->editColumn('date',function($data) 
                            {
                                return  date("Y-m-d",strtotime($data->date));
                            })
                            ->editColumn('action',function($data) 
                            {

                                $action = "";
                                if(($data->action)== 'REMOVED')
                                {
                                    $action = "<h5><span class='label label-important'><i class='fa fa-trash'></i> Remove </span></h5>";
                                }
                                elseif(($data->action)== 'EDIT')
                                {
                                    $action = "<h5><span class='label label-warning'><i class='fa fa-pencil-square-o'></i> Edit </span></h5>";
                                }
                                elseif(($data->action)== 'ADD')
                                {
                                    $action = "<h5><span class='label label-success'><i class='fa fa-plus-square-o'></i> Add </span></h5>";   
                                }

                                return $action;
                            })
                            ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);

    }

    function get_activity_log(Request $request)
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
        
        $activity_log_table           = $this->BaseModel->getTable();
        $prefixed_activity_log_table  = DB::getTablePrefix().$this->BaseModel->getTable();

        $user_table                   = $this->UserModel->getTable();
        $prefixed_user_table          = DB::getTablePrefix().$this->UserModel->getTable();

        $user_trans_table             = $this->UserTranslationModel->getTable();                  
        $prefixed_user_trans_table    = DB::getTablePrefix().$this->UserTranslationModel->getTable();

        $user_role                    = $this->RoleModel->getTable();

        $obj_activity_log = DB::table($activity_log_table)
                                ->select(DB::raw($prefixed_activity_log_table.".id as id,".
                                                 $prefixed_activity_log_table.".created_at as date,".   
                                                 $prefixed_activity_log_table.".module_title as module_name,".
                                                 $prefixed_activity_log_table.".module_action as action,".
                                                 $prefixed_activity_log_table.".user_id as user_id,".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
                                                          .$prefixed_user_trans_table.".last_name) as user_name"
                                                 ))
                                ->whereNull($activity_log_table.'.deleted_at')
                                ->orderBy($activity_log_table.'.id','DESC')
                                ->leftJoin($user_trans_table,$user_trans_table.'.user_id', ' = ',$activity_log_table.'.user_id')
                                ->leftJoin($user_role,$activity_log_table.'.role_id', ' = ',$user_role.'.id')
                                ->where($user_trans_table.'.locale','=',$locale);

        
        /* ---------------- Filtering Logic ----------------------------------*/                    

        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
            $obj_activity_log = $obj_activity_log->WhereRaw("( (".$activity_log_table.".created_at LIKE '%".$search_term."%') ")
                                                 ->orWhereRaw("(".$activity_log_table.".module_title LIKE '%".$search_term."%') ")
                                                 ->orWhereRaw("(".$activity_log_table.".module_action LIKE '%".$search_term."%') ")
                                                 ->orWhereRaw(" ( CONCAT(".$prefixed_user_trans_table.".first_name,'',".$prefixed_user_trans_table.".last_name)  LIKE  '%".$search_term."%' ) )");
        }


        return $obj_activity_log;
    }

}