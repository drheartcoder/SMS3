<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Common\Traits\MultiActionTrait;

use App\Models\QuestionCategoryModel;
use App\Models\SchoolTemplateModel;
use App\Models\SchoolTemplateTranslationModel;


use Validator;
use Flash;
use Session;
use DB;
use Datatables;
use Sentinel;
class SchoolTemplateController extends Controller
{
	use MultiActionTrait;
    public function __construct(
    								LanguageService 	  $language,
    								QuestionCategoryModel $question_category,
    								SchoolTemplateModel   $school_template,
    								SchoolTemplateTranslationModel $school_template_translation
    							)
    {
		$this->arr_view_data 	          = [];
		$this->QuestionCategoryModel      = $question_category;
		$this->SchoolTemplateModel        = $school_template;
		$this->BaseModel                  = $this->SchoolTemplateModel;
		$this->LanguageService            = $language;
    	$this->SchoolTemplateTranslationModel = $school_template_translation;
		
		$this->module_url_path 	          = url(config('app.project.admin_panel_slug')."/school_template");
		$this->module_view_folder         = "admin.school_template";
		$this->module_title               = translation('school_template');
		$this->theme_color                = theme_color();
		$this->module_icon                = 'fa fa-server';
		$this->create_icon                = 'fa fa-plus-circle';
		$this->edit_icon                  = 'fa fa-edit';
    }
    public function index()
    {
        $arr_template = [];
        $obj_template = $this->BaseModel
                             ->with("get_question_category")
                             ->orderBy('position','asc')
                             ->get(); 
        if($obj_template)
        {
            $arr_template = $obj_template->toArray();
        }                     
        $this->arr_view_data['arr_template']   = $arr_template;                     
        $this->arr_view_data['page_title']      = translation("manage")." ".$this->module_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;       

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }
    public function get_records(Request $request)
    {
        $obj_custom = $this->get_school_template_records($request);

       
        $role = Sentinel::findRoleById(1);
                
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;

        $json_result  = Datatables::of($obj_custom);

        $json_result = $json_result->blacklist(['id']);                    

        /* Modifying Columns */
        $json_result =  $json_result->editColumn('enc_id',function($data)
                        {
                            return  base64_encode(($data->school_template_id));
                        })
                        ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                        {
                            $build_status_btn=$build_edit_action=$build_delete_action='';
                            
                                if(array_key_exists('school_template.update',$arr_current_user_access))
                                {
                                    if($data->is_active != null && $data->is_active == "0")
                                    {   
                                        $build_status_btn = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-dangers" title="Lock" href="'.$this->module_url_path.'/activate/'.base64_encode($data->school_template_id).'" 
                                        onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-lock"></i></a>';
                                    }
                                    elseif($data->is_active != null && $data->is_active == "1")
                                    {
                                        $build_status_btn = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" title="Unlock" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->school_template_id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-unlock"></i></a>';
                                    }    

                                    $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->school_template_id);
                                    $build_edit_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip call_loader btn-delets" href="'.$edit_href.'" title="Edit"><i class="fa fa-edit" ></i></a>';
                                }
                                if(array_key_exists('school_template.delete',$arr_current_user_access)){
                                $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->school_template_id);
                                $build_delete_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip call_loader btn-delets" href="'.$delete_href.'" title="Delete" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                }    
                            
                            return $build_status_btn.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_delete_action;  
                        })
                         ->editColumn('build_checkbox',function($data)
                         {
                            return '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->school_template_id).'" value="'.base64_encode($data->school_template_id).'" /><label for="mult_change_'.base64_encode($data->school_template_id).'"></label></div>'; 
                        
                            
                        }) 
                        ->editColumn('build_required',function($data){
                            if($data->is_required==0)
                            {
                                $check_href =  $this->module_url_path.'/change_to_require/'.base64_encode($data->school_template_id);
                                $build_required_action = '<a href="'.$check_href.'" title="Change to require"><i class="fa fa-times" ></i></a>';
                            }
                            else
                            {
                                $check_href =  $this->module_url_path.'/change_to_not_require/'.base64_encode($data->school_template_id);
                                $build_required_action = '<a href="'.$check_href.'" title="Change to not require"><i class="fa fa-check " aria-hidden="true"></i></a>';
                            }  
                            return $build_required_action;

                        }) 
                        ->editColumn('build_title',function($data)
                        {
                            return translation($data->slug);
                        })                   
                        ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);
    }
    public function get_school_template_records(Request $request)
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

        $custom_translation_table = $this->SchoolTemplateTranslationModel->getTable();   

       
        $prefixed_question_table = DB::getTablePrefix().$this->QuestionCategoryModel->getTable();                                                              
      
        $obj_custom = DB::table($custom_table)
                        ->select(DB::raw(   
                                            $prefixed_custom_table.".id as school_template_id,".
                                            $prefixed_custom_table.".question_category_id,".
                                            $custom_translation_table.".options,".
                                            $custom_translation_table.".title,".

                                            $prefixed_custom_table.".is_active,".
                                            $prefixed_custom_table.".is_required,".
                                            $prefixed_question_table.".id as question_id,".
                                            $prefixed_question_table.".name as q_category,".
                                            $prefixed_question_table.".slug"
                                        ))
                                        ->whereNull($prefixed_custom_table.'.deleted_at')
                                        ->where($custom_translation_table.'.locale','=',$locale)
                                        ->orderBy($prefixed_custom_table.'.id','Desc')
                                        ->leftJoin($prefixed_question_table, $prefixed_question_table.'.id','=',$prefixed_custom_table.'.question_category_id')
                                        ->leftJoin($custom_translation_table,$custom_translation_table.'.school_template_id',' = ',$custom_table.'.id');
        /* Filtering Logic*/
        $arr_search_column = $request->input('column_filter');
        if(isset($arr_search_column['q_name']) && $arr_search_column['q_name']!="")
        {
            $search_term = $arr_search_column['q_name'];
            $obj_custom = $obj_custom->having('name','LIKE', '%'.$search_term.'%');
        }
        

        return $obj_custom ;
    }
    public function create()
    {
        $arr_question_category = [];
        $obj_question_category = $this->QuestionCategoryModel->get();
        if($obj_question_category)
        {
        	$arr_question_category = $obj_question_category -> toArray(); 
        }

        $this->arr_view_data['page_title']   = translation('add')." ".$this->module_title;
        $this->arr_view_data['module_title'] = str_plural($this->module_title);
                
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_question_category']= $arr_question_category;

        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['create_icon'] = $this->create_icon;
        
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }
    public function store(Request $request)
    {
        $validations = '';
        $arr_lang    = $this->LanguageService->get_all_language();       
		$arr_rules   = [];
        $arr_rules['title'] = "required";
        
        $validator = Validator::make($request->all(),$arr_rules);
       
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $title       = $request->input('title');
        $exist       = $this->SchoolTemplateTranslationModel->where('title',$title)->first();
        if($exist)
        {
            Flash::error(translation('this_field_is_available'));
            return redirect()->back();
        }

        $q_category    = $request->input('q_category');
        $q_category_id = $this->QuestionCategoryModel->where('slug',$q_category)->first();
        $q_category_id = (isset($q_category_id->id)) ? $q_category_id->id : '0';
        $is_required   = ($request->has('required')) ? $request->input('required') :0;
        if(count($request->input('validations'))>0)
        {
            $validations = implode(',',$request->input('validations'));
        }

        $data['question_category_id']  = $q_category_id;
        $data['is_required']           = $is_required;
        $data['validations']           = $validations;
        $status        = $this->BaseModel->create($data);                   
        if($status)
        {
            /* insert record into translation table */
            if(sizeof($arr_lang) > 0 )
            {
                foreach($arr_lang as $lang) 
                {            
                    
                    $title       = $request->input('title');
                    
                    $option_array=[];
                    if($q_category=='multiple' || $q_category=='single' || $q_category=='dropdown')
                    {
                        $option_count = $request->input('option_count');  
                        for($i=0; $i<$option_count ;$i++)
                        {
                            $count = $i+1;
                            $option = $request->input('options_'.($count)); 
                            if($option!='')
                            {
                                array_push($option_array,$option);
                            }
                        }    
                    }
                    if(count($option_array)>0)
                    {
                        $options = implode(",", $option_array);    
                    }
                    else
                    {
                        $options = "";   
                    }
                    $translation = $status->translateOrNew($lang['locale']);
                    $translation->school_template_id   = $status->id;
                    $translation->title  = $title;
                    $translation->options  = $options;
                    $translation->save();
                    Flash::success(str_singular($this->module_title).' '.translation('created_successfully'));
                
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
    public function view()
    {
        $arr_lang = [];
       
        $arr_lang = $this->LanguageService->get_all_language();

        $arr_template = [];
        $obj_template = $this->BaseModel
                             ->with("get_question_category")
                             ->orderBy('position','asc')
                             ->get();                   
        if($obj_template)
        {
            $arr_template = $obj_template->toArray();
        }  
        
        $this->arr_view_data['page_title']   = translation('view')." ".$this->module_title;
        $this->arr_view_data['module_title'] = str_plural($this->module_title);
        $this->arr_view_data['edit_mode'] = TRUE;
        
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['arr_lang']        = $arr_lang;
        $this->arr_view_data['arr_template']    = $arr_template;

        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['create_icon'] = $this->create_icon;
        
        return view($this->module_view_folder.'.view',$this->arr_view_data);
    }
    public function change_to_require($enc_id)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }
        $static_page = $this->BaseModel->where('id',base64_decode($enc_id))->first();
        $update = FALSE;
        if($static_page)
        {

            $update = $static_page->update(['is_required'=>1]);
        }

        if($update)
        {
            Flash::success($this->module_title.' '.translation('changed_to_required'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('activation'));
        }

        return redirect()->back();
    }
    public function change_to_not_require($enc_id)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }
        $static_page = $this->BaseModel->where('id',base64_decode($enc_id))->first();
        $update = FALSE;
        if($static_page)
        {

            $update = $static_page->update(['is_required'=>0]);
        }

        if($update)
        {
            Flash::success($this->module_title.' '.translation('changed_to_not_required'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('activation'));
        }
        return redirect()->back();
    }
    public function edit($enc_id=FALSE)
    {/*
        $arr_lang = [];
       
        $arr_lang = $this->LanguageService->get_all_language();*/
        $arr_question_category = [];
        $obj_question_category = $this->QuestionCategoryModel
                                      ->get();
        if($obj_question_category)
        {
            $arr_question_category = $obj_question_category 
                                    -> toArray(); 
        }
        $id = base64_decode($enc_id);
        $obj_template = $this->SchoolTemplateModel
                             ->where("id",$id)
                             ->first();  

        $this->arr_view_data['page_title']   = translation('edit')." ".$this->module_title;
        $this->arr_view_data['module_title'] = str_plural($this->module_title);
        /*$this->arr_view_data['edit_mode'] = TRUE;*/
        
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        /*$this->arr_view_data['arr_lang']        = $arr_lang;*/
        $this->arr_view_data['arr_question_category']= $arr_question_category;
        $this->arr_view_data['obj_template']= $obj_template;

        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['edit_icon'] = $this->edit_icon;
        
        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }
    public function update(Request $request)
    {
        $id = $request->has('id') ? base64_decode($request->input('id')) : '0';
        $arr_lang =  $this->LanguageService->get_all_language();      
        $arr_rules   = [];
        $arr_rules['title'] = "required";
       
        $validator = Validator::make($request->all(),$arr_rules);
       
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }  

        /*$exist       = $this->SchoolTemplateTranslationModel->where('school_template_id','!=',$id)->first();
        if($exist)
        {
            Flash::error(translation('this_field_is_available'));
            return redirect()->back();
        }*/

        $q_category = $request->input('q_category');
        $q_category_id = $this->QuestionCategoryModel->where('slug',$q_category)->first();
        $q_category_id = (isset($q_category_id->id)) ? $q_category_id->id : '0';
        $is_required = ($request->has('required')) ? $request->input('required') :0;
        
        $validations = '';
        if(count($request->input('validations'))>0)
        {
            $validations = implode(',',$request->input('validations'));
        }

        $data['question_category_id'] = $q_category_id;
        $data['is_required']          = $is_required;
        if($q_category=='multiple' || $q_category=='single' || $q_category=='dropdown')
        {
            $data['validations']          = '';
        }
        elseif ($q_category=='short' || $q_category=='long' || $q_category=='latitude' || $q_category=='longitude') 
        {
            $data['validations']          = $validations;
        }

        $status = $this->BaseModel->where("id",$id)->update($data);                   
        if($status)
        {
            $status = $this->BaseModel
                            ->where("id",$id)
                            ->first();    
            /* update record into translation table */
            if(sizeof($arr_lang) > 0 )
            {
                foreach($arr_lang as $lang) 
                {            
                   
                    $title       = $request->input('title');
                     
                    $option_array=[];
                    if($q_category=='multiple' || $q_category=='single' || $q_category=='dropdown')
                    {
                        $option_count = $request->input('option_count');

                        for($i=0; $i<$option_count ;$i++)
                        {
                            $count = $i+1;
                            $option = $request->input('options_'.($count)); 
                            if($option!='')
                            {
                                array_push($option_array,$option);
                            }
                        }    
                    }
                    if(count($option_array)>0)
                    {
                        $options = implode(",", $option_array);    
                    }
                    else
                    {
                        $options = "";   
                    }
                    $translation = $status->translateOrNew($lang['locale']);
                    $translation->school_template_id   = $status->id;
                    $translation->title  = $title;
                    $translation->options  = $options;
                    $translation->save();
                    Flash::success(str_singular($this->module_title).' '.translation('updated_successfully'));
                
                }
            } 
            /*------------------------------------------------------*/
            Flash::success(str_plural($this->module_title).' '.translation('updated_successfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_updating').' '.str_singular($this->module_title));
        }

        return redirect()->back();
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

                $this->BaseModel->where('id',$id)->update(['position' => $order_number]);
                
            }
        }

        $data['status'] = "SUCCESS";
        return $data;
    }
}
