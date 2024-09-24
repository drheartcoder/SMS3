<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Models\FeesTranslationModel;
use App\Common\Traits\MultiActionTrait;
use App\Models\FeesModel;

use Validator;
use Flash;
use Session;
use DB;
use Datatables;

class FeesController extends Controller
{
    use MultiActionTrait;
    public function __construct(
    							 	LanguageService 	  $language,
    								FeesModel 			  $fees,
    								FeesTranslationModel  $fees_translation
    							)
    {
    	
		$this->arr_view_data 	  = [];
		$this->FeesModel          = $fees;
		$this->LanguageService    = $language;
		$this->BaseModel          = $this->FeesModel;
        $this->FeesTranslationModel  = $fees_translation;
		$this->module_url_path 	  = url(config('app.project.admin_panel_slug')."/fees");
		$this->module_view_folder = "admin.fees";
		$this->module_title       = translation('fees');
		$this->theme_color        = theme_color();
		$this->module_icon        = 'fa fa-money';
		$this->create_icon        = 'fa fa-plus-circle';
        $this->edit_icon          = 'fa fa-edit';

    }

    public function index()
    {
        $this->arr_view_data['page_title']      = translation("manage")." ".$this->module_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;       

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }  

    public function create()
    {
    
        $arr_lang = $this->LanguageService->get_all_language();

        $this->arr_view_data['page_title']   = translation('add')." ".$this->module_title;
        $this->arr_view_data['module_title'] = str_plural($this->module_title);
        $this->arr_view_data['edit_mode'] = TRUE;
        
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_lang']        = $arr_lang;

        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['create_icon'] = $this->create_icon;
        
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }
    public function store(Request $request)
    {   
        $arr_lang =  $this->LanguageService->get_all_language();      
		$arr_rules  =  [];
        foreach ($arr_lang as $lang) 
        {
            $arr_rules['name_'.$lang['locale']] = "required";
        }
        
        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $slug =  strslug($request->input('name_'.$arr_lang[0]['locale']));
        
        $does_exists = $this->BaseModel->whereHas('translations',function($query) use($slug){
                                                        $query->where('slug','=',trim($slug));
                                                    })
                                                   ->count();

        if($does_exists>0)
        {
            Flash::error(str_singular($this->module_title).' '.translation('already_exists'));
            return redirect()->back();
        }  
        $status = $this->BaseModel->create();                   
        if($status)
        {
            
            /* insert record into translation table */
            if(sizeof($arr_lang) > 0 )
            {
                foreach ($arr_lang as $lang) 
                {            
                
                    $fee_name       = $request->input('name_'.$lang['locale']);
                    $slug = strslug($fee_name);

                    if( (isset($fee_name)  && $fee_name != ''))
                    { 
                        $translation = $status->translateOrNew($lang['locale']);

                        $translation->fees_id   = $status->id;
                        $translation->title     = $fee_name;
                        $translation->slug      = $slug;
                        $translation->save();
                        Flash::success(str_singular($this->module_title).' '.translation('created_successfully'));
                    }
                }
            } 
            /*------------------------------------------------------*/
            Flash::success(str_plural($this->module_title).' '.translation('created_successfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_creating').' '.str_singular($this->module_title));
        }

        return redirect()->back();
    }
    public function edit($enc_id)
    {
        $id       = base64_decode($enc_id);
        $arr_lang = $arr_data = [];
        $this->arr_view_data['edit_mode'] = TRUE;
        $arr_lang = $this->LanguageService->get_all_language();

        $obj_data = $this->BaseModel->with('translations')->where('id',$id)->first();

        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
            $arr_data['translations'] = $this->arrange_locale_wise($arr_data['translations']);
        }
        $this->arr_view_data['page_title']      = translation("edit").' '.$this->module_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['arr_lang']        = $arr_lang;

        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['edit_icon']       = $this->edit_icon;

        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }
    public function update(Request $request)
    {
    	$arr_rules   = [];
        $arr_lang =  $this->LanguageService->get_all_language();      
        foreach ($arr_lang as $lang) 
        {
            $arr_rules['name_'.$lang['locale']] = "required";
        }

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
		
		$id          = base64_decode($request->input('level_id'));       
        

        $slug =  strslug($request->input('name_'.$arr_lang[0]['locale']));
        
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
		
		$status = $this->BaseModel->where('id',$id)->first();                   
        if($status)
        {
           
            /* insert record into translation table */
            if(sizeof($arr_lang) > 0 )
            {
                foreach ($arr_lang as $lang) 
                {            
                  
                    $fee_name       = $request->input('name_'.$lang['locale']);
                    if( (isset($fee_name)  && $fee_name != ''))
                    { 
                        $translation = $status->translateOrNew($lang['locale']);
                        $translation->fees_id    = $status->id;
                        $translation->title       = $fee_name;
                        $translation->save();
                        Flash::success(str_singular($this->module_title).' '.translation('created_successfully'));
                    }
                }
            } 
           
            Flash::success(str_plural($this->module_title).' '.translation('updated_successfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_updating').' '.str_singular($this->module_title));
        }
        return redirect()->back()->withInput($request->all());
    }
    public function get_records(Request $request)
    {

        $obj_custom = $this->get_fees_records($request);

        $arr_current_user_access = $request->user()->permissions;

        $json_result  = Datatables::of($obj_custom);

        $json_result = $json_result->blacklist(['id']);                    

        /* Modifying Columns */
        $json_result =  $json_result->editColumn('enc_id',function($data) 
                        {
                            return  base64_encode(($data->fees_id));
                        })
                        ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                        {
                            
                            if(array_key_exists('fees.update',$arr_current_user_access))
                            {
                                if($data->is_active != null && $data->is_active == "0")
                                {   
                                    $build_status_btn = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" title="Lock" href="'.$this->module_url_path.'/activate/'.base64_encode($data->fees_id).'" 
                                    onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-lock"></i></a>';
                                }
                                elseif($data->is_active != null && $data->is_active == "1")
                                {
                                    $build_status_btn = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" title="Unlock" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->fees_id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-unlock"></i></a>';
                                }    

                                $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->fees_id);
                                $build_edit_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip call_loader btn-delets" href="'.$edit_href.'" title="Edit"><i class="fa fa-edit" ></i></a>';
                            }
                            
                            return $build_status_btn.'&nbsp;'.$build_edit_action;  
                        })
                         ->editColumn('build_checkbox',function($data)
                         {
                            $build_checkbox = '';
                            if(array_key_exists('fees.update',$arr_current_user_access))
                            {
                                $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->fees_id).'" value="'.base64_encode($data->fees_id).'" /><label for="mult_change_'.base64_encode($data->fees_id).'"></label></div>'; 
                                
                            }
                            return $build_checkbox;
                                    
                         })                    
                            }
                        ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);
    }
    public function get_fees_records(Request $request)
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
        /* Prefixed table name are required wherever we are using DB::raw calls */                        
        $custom_table = $this->BaseModel->getTable();   
        $prefixed_custom_table = DB::getTablePrefix().$this->BaseModel->getTable();                                             

        $custom_translation_table = $this->FeesTranslationModel->getTable();   
        $prefixed_translation_table = DB::getTablePrefix().$this->FeesTranslationModel->getTable();                                             
      
        $obj_custom = DB::table($custom_table)
                        ->select(DB::raw(   
                                            $prefixed_custom_table.".id as fees_id,".
                                            $prefixed_translation_table.".title as title,".
                                            $prefixed_custom_table.".is_active"
                                        ))
                                        ->where($custom_translation_table.'.locale','=',$locale)
                                        ->whereNull($prefixed_custom_table.'.deleted_at')
                                        ->leftJoin($custom_translation_table,$custom_translation_table.'.fees_id',' = ',$custom_table.'.id');

        /* Filtering Logic*/
        $arr_search_column = $request->input('column_filter');
        if(isset($arr_search_column['q_name']) && $arr_search_column['q_name']!="")
        {
            $search_term = $arr_search_column['q_name'];
            $obj_custom = $obj_custom->having('title','LIKE', '%'.$search_term.'%');
        }
        

        return $obj_custom ;
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
}
