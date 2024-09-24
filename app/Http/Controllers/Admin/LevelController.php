<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Common\Traits\MultiActionTrait;
use App\Common\Services\CommonDataService;

/*level */
use App\Models\LevelModel;
use App\Models\LevelClassModel;
use App\Models\LevelTranslationModel;
use App\Models\SchoolTimeTableModel;

/*Activity Log */
use App\Models\ActivityLogsModel;   
use Session;
use Validator;
use Flash;
use Sentinel;
use DB;
use PDF;
use Datatables;

class LevelController extends Controller
{
    use MultiActionTrait;

	public function __construct(ActivityLogsModel $activity_logs,
                                LanguageService $language,
                                LevelModel $level,
                                LevelTranslationModel $level_translation,
                                CommonDataService $common_data_service
                                )
	{
        $this->arr_view_data 		= [];
		$this->LevelModel 	        = $level;
        $this->LevelTranslationModel= $level_translation;
        $this->BaseModel            = $this->LevelModel;
        $this->LevelClassModel      = new LevelClassModel();
        $this->SchoolTimeTableModel = new SchoolTimeTableModel();     
        $this->ActivityLogsModel    = $activity_logs; /* Activity Model */
        $this->LanguageService      = $language;
		$this->module_url_path 		= url(config('app.project.admin_panel_slug')."/level");
        $this->module_view_folder   = "admin.level";
        $this->module_title         = translation("level");
        $this->theme_color          = theme_color();
        $this->module_icon          = 'fa fa-graduation-cap';
        $this->create_icon          = 'fa fa-plus-circle';
        $this->edit_icon            = 'fa fa-edit';
        $this->view_icon            = 'fa fa-eye';
        $this->CommonDataService            = $common_data_service;

          /* Activity Section */
        $this->first_name = $this->last_name =$this->ip_address ='';
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->ip_address        = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;  
        }
        /* Activity Section */
	}

	public function index() 
	{	
        $arr_levels = [];
        $obj_levels = $this->LevelModel->orderBy('level_order','ASC')->get();
        if(isset($obj_levels) && !empty($obj_levels))
        {
            $arr_levels = $obj_levels->toArray();
        }

        if(isset($arr_levels) && !empty($arr_levels))
        {
            $this->arr_view_data['arr_levels']        = $arr_levels;    
        }
        $this->arr_view_data['page_title'] 			= translation("manage")." ".str_singular($this->module_title);
        $this->arr_view_data['module_title'] 		= $this->module_title;
        $this->arr_view_data['module_url_path'] 	= $this->module_url_path;
        $this->arr_view_data['theme_color']         = $this->theme_color;
        $this->arr_view_data['module_icon']         = $this->module_icon;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
	}

	public function create()
    { 
        $this->arr_view_data['page_title']      = translation("add")." ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['create_icon']     = $this->create_icon;


        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    public function store(Request $request)
    {

        $form_data = array();
        /* Fetch All Languages*/
        $arr_lang =  $this->LanguageService->get_all_language();

        $arr_rules['level']      =   ["required","regex:/^[a-zA-Z0-9 ]+$/"];  
        $messages['required']    =   translation('this_field_is_required');
        $messages['regex']       =   translation('letters_and_numbers_only'); 
        
        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
             return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $slug =  strslug($request->input('level'));

        $does_exists = $this->BaseModel->whereHas('translations',function($query) use($slug){
                                                        $query->where('slug','=',trim($slug));
                                                    })
                                                   ->count();

        if($does_exists>0)
        {
            Flash::error(str_singular($this->module_title).' '.translation('already_exists'));
            return redirect()->back();
        }

        $position = $this->LevelModel->max('level_order');
        $position = $position+1;

        $data['level_order'] = $position;
        $level     =    $this->BaseModel->create($data);
        $form_data =    $request->all();
        $level_id  =    $level->id;
        
        if($level)
        {
             /*-------------------------------------------------------
                |   Activity log Event
                --------------------------------------------------------*/
                    $arr_event                 = [];
                    $arr_event['ACTION']       = 'ADD';
                    $arr_event['MODULE_TITLE'] = $this->module_title;

                    $this->save_activity($arr_event);

                /*----------------------------------------------------------------------*/ 
                
                if(sizeof($arr_lang) > 0 )
                {  
                    foreach ($arr_lang as $lang) 
                    {            
                        $level_name   = $request->input('level');
                        
                        if(isset($level_name) && $level_name != '')
                        {  
                            $translation = $level->translateOrNew($lang['locale']);
                            $translation->level_name      = trim($level_name);
                            $translation->slug            = $slug;
                            $translation->level_id        = $level_id;
                            $translation->save();
                            
                            Flash::success($this->module_title .' '.translation('created_successfully'));
                        }
                    }
                } 
                else
                {
                    Flash::success(translation('problem occured while creating').' '.$this->module_title);
                }
        }

        return redirect()->back();
    }
    public function edit($enc_id)
    {
        $id       = base64_decode($enc_id);
        if(!is_numeric($id))
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }

        $obj_data = $this->BaseModel->where('id',$id)->first();

        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }
        else
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['enc_id']          = $enc_id;
        $this->arr_view_data['page_title']      = translation("edit")." ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['edit_icon']       = $this->edit_icon;

        return view($this->module_view_folder.'.edit',$this->arr_view_data);   
    }


    public function checkLevel(Request $request)
    {
        if(($this->LevelTranslationModel->where('level_name',$request->Input('level'))->count())> 0)
        {
            return response()->json(array('status'=>'error','msg'=>translation('this_level_is_already_exist')));
        }
        else
        {
            return response()->json(array('status'=>translation('success')));
        }
    }


    public function update(Request $request,$enc_id)
    {
        $form_data = array();
        $id =base64_decode($enc_id);
        if(!is_numeric($id))
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }
         /* Fetch All Languages*/
        $arr_lang =  $this->LanguageService->get_all_language();
        
        $arr_rules['level']     =   ["required","regex:/^[a-zA-Z0-9 ]+$/"];  
        $messages['required']   =   translation('this_field_is_required');
        $messages['regex']      =   translation('letters_and_numbers_only'); 
        

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
             return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        $slug =  strslug($request->input('level'));

        $does_exists = $this->BaseModel->whereHas('translations',function($query) use($slug){
                                                        $query->where('slug','=',trim($slug));
                                                    })
                                                   ->where('id','!=',$id)
                                                   ->count();

        if($does_exists>0)
        {
            Flash::error(str_singular($this->module_title).' '.translation('already_exists'));
            return redirect()->back();
        }  
        
        $fetched_level = $this->BaseModel->where('id',$id)
                                         ->first();
        $form_data  =   $request->all();

        if($fetched_level)
        {
                /*-------------------------------------------------------
                |   Activity log Event
                --------------------------------------------------------*/
                    $arr_event                 = [];
                    $arr_event['ACTION']       = 'EDIT';
                    $arr_event['MODULE_TITLE'] = $this->module_title;

                    $this->save_activity($arr_event);

                /*----------------------------------------------------------------------*/ 
                
               
                if(sizeof($arr_lang) > 0 )
                {  
                    foreach ($arr_lang as $lang) 
                    {       
                        $level_name   = $request->input('level');
                        if(isset($level_name) && $level_name != '' )
                        {  
                            $translation = $fetched_level->translateOrNew($lang['locale']);
                            
                            $translation->level_name      = trim($level_name);
                            $translation->slug            = $slug;

                            $translation->save();
                            
                            Flash::success($this->module_title .' updated successfully');
                        }

                    }
                } 
                else
                {
                    Flash::success('Problem occurred while updating '.$this->module_title);
                }
        }

        return redirect()->back();
    }
    
    function get_level_details(Request $request,$type='',$fun_type='')
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

        $level_details                  = $this->BaseModel->getTable();
        $prefixed_level_details         = DB::getTablePrefix().$this->BaseModel->getTable();
        $level_trans_details            = $this->LevelTranslationModel->getTable();
        $prefixed_level_trans_details   = DB::getTablePrefix().$this->LevelTranslationModel->getTable();

        $obj_level = DB::table($level_details)
                                ->select(DB::raw($prefixed_level_details.".id as id,".
                                                 $prefixed_level_details.".is_active as status,".
                                                 $prefixed_level_trans_details.".level_name,".
                                                 $prefixed_level_details.".level_order"))
                                ->join($level_trans_details,$level_details.'.id','=',$level_trans_details.'.level_id')
                                ->where($level_trans_details.'.locale','=',$locale)
                                ->whereNull($level_details.'.deleted_at')
                                ->orderBy($level_details.'.created_at','DESC');
   
        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
            $obj_level = $obj_level->WhereRaw("( (".$prefixed_level_trans_details.".level_name LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_level_details.".level_order LIKE '%".$search_term."%') )");
        }

        if($fun_type=="export"){
            return $obj_level->get();
        }else{
            return $obj_level;
        }

    }


    public function get_records(Request $request)
    {
        $arr_current_user_access =[];
        $role = Sentinel::findRoleById(1);
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;
        $obj_level        = $this->get_level_details($request);

        $json_result     = Datatables::of($obj_level);
        $json_result     = $json_result->blacklist(['id']);
        
        $level_update ='level.update';
        if(array_key_exists( $level_update , $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data)
                            {
                                return base64_encode($data->id);
                            });
        }                    

        $json_result     = $json_result->editColumn('level_name',function($data)
                            { 
                                $level_name =   '';
                                if($data->level_name != null)
                                {
                                    $level_name     =   $data->level_name; 
                                }  
                                        
                                return ucfirst($level_name);

                            })
                            ->editColumn('level_order',function($data)
                            { 
                                $level_order = '';
                                if($data->level_order != null)
                                {
                                    $level_order     =   $data->level_order; 
                                }  
                                        
                                return $level_order;

                            })                         
                            ->editColumn('build_action_btn',function($data) use ($arr_current_user_access,$level_update)
                            {
                                $nbsp = '&nbsp;';
                               
                                    if($data->status != null && $data->status == "0")
                                    {   

                                        $build_status_btn = '<a class="blue-color" title="'.translation('activate').'" href="'.$this->module_url_path.'/activate/'.base64_encode($data->id).'" 

                                            onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-lock"></i></a>';
                                    }
                                    elseif($data->status != null && $data->status == "1")
                                    {

                                       $build_status_btn = '<a class="light-blue-color" title="'.translation('deactivate').'" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-unlock"></i></a>';

                                    }
                                    $build_edit_action = '';
                                    if(array_key_exists($level_update,$arr_current_user_access)){

                                    $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                    $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';
                                    }
                                    $build_delete_action = '';
                                    if(array_key_exists($level_update,$arr_current_user_access)){
                                    $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                    $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                    }

                                   return $build_status_btn.$nbsp.$build_edit_action.$nbsp.$build_delete_action.$nbsp;
                              
                            })
                            ->editColumn('build_checkbox',function($data){
                                
                                    
                                    return '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>';
                                
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    public function arrange_locale_wise(array $arr_data)
    {
        if(sizeof($arr_data)>0)
        {
            foreach ($arr_data as $key => $data) 
            {
                unset($arr_data[$key]);

                $arr_data[$data['locale']] = $data;                    
            }

            return $arr_data;
        }
        else
        {
            return [];
        }
    } 

    public function rearrange_order_number(Request $request)
    {
        $arr_rules = [];
        
        $arr_rules['listItem'] = "required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            $data['status'] = "ERROR";
            $data['msg'] = "Something went wrong ! cannot order records,Please try again!";
            return $data;
        }

        $new_order = $request->input('listItem');
      
        if(is_array($new_order) && count($new_order) > 0)
        {
            
            foreach ($new_order as $key => $id) 
            {
                $order_number = $key + 1;
                $this->BaseModel->where('id',$id)->update(['level_order' => $order_number]);
                $this->LevelClassModel->where('level_id',$id)->update(['position'=>$order_number]);
                $this->SchoolTimeTableModel->where('level_id',$id)->update(['level_order'=>$order_number]);
            }
        }

        $data['status'] = "SUCCESS";
        return $data;
    }

    /*
    | export() : Export List
    | Auther  : Vrajesh
    | Date    : 14-12-2018
    */

    public function export(Request $request)
    {
        $file_type = config('app.project.export_file_formate');
        $obj_data = $this->get_level_details($request,'','export');

        if(sizeof($obj_data)<=0){
            Flash::error(translation("no_records_found_to_export"));
            return redirect()->back();
        }
        if(sizeof($obj_data)>500 && $request->file_format == $file_type){
            Flash::error(translation("too_many_records_to_export"));
            return redirect()->back();
        }
        if($request->file_format == $file_type){
            \Excel::create(ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid(), function($excel) use($obj_data) 
                {
                    $excel->sheet(ucwords($this->module_title), function($sheet) use($obj_data) 
                    {
                        $arr_fields['sr_no']   = translation('sr_no');
                        $arr_fields['level_name']   = translation('level_name');

                        // To format mobile bumber
                        $sheet->setColumnFormat([
                            'E' => "#",
                        ]);

                        $sheet->row(2, [ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                        $sheet->row(4, $arr_fields);
                        
                        // To set Colomn head
                        $j = 'A'; $k = '4';
                        for($i=0; $i<=1;$i++)
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
                                $arr_tmp[$key]['sr_no']         = $count++;
                                $arr_tmp[$key]['level_name']    = $result->level_name;
                            }
                            $sheet->rows($arr_tmp);
                        }
                    });
                })->export(config('app.project.export_file_formate'));     
        }
        
        if($request->file_format == 'pdf')
        {
            $this->arr_view_data['arr_data'] = $obj_data;

            $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
            return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
        }
    }
}