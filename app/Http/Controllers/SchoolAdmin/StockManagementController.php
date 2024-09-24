<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Common\Services\CommonDataService;
use App\Common\Traits\MultiActionTrait;
use App\Models\StockDistributedModel;
use App\Models\StockReceivedModel;
use App\Models\LevelModel;
use App\Models\LevelClassModel;

use Datatables;
use Validator;
use Sentinel;
use Session;
use Flash;
use DB;

class StockManagementController extends Controller
{
    use MultiActionTrait 
    {
        delete as module_delete ; 
        multi_action as module_multiaction_delete;  
    }

    public function __construct(
                                LanguageService $langauge,
                                CommonDataService $CommonDataService
                                )
    {   

        $this->StockDistributedModel        = new StockDistributedModel();
        $this->StockReceivedModel           = new StockReceivedModel();
        $this->BaseModel                    = $this->StockReceivedModel;
        $this->LevelModel                   = new LevelModel();
        $this->LevelClassModel              = new LevelClassModel();
        $this->CommonDataService            = $CommonDataService;

        $this->stock_product_base_img_path     = public_path().config('app.project.img_path.stock_products');
        $this->stock_product_public_img_path   = url('/').config('app.project.img_path.stock_products');
        
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')."/stock");
        
        $this->LanguageService              = $langauge;
        $this->module_title                 = translation("stock");
        $this->module_view_folder           = "schooladmin.stock";
        $this->school_id                    = Session::has('school_id')?Session::get('school_id'):0;
        $this->academic_year                = Session::has('academic_year')?Session::get('academic_year'):0;
        
        $this->theme_color                  = theme_color();
        $this->module_icon                  = "fa fa-cubes";
        $this->create_icon                  = "fa fa-plus-circle";
        $this->edit_icon                    = "fa fa-edit";
        $this->view_icon                    = "fa fa-eye";
      
    }   
 
    /*-----Cafeteria Items Module--------

    Authar : sayali B
    Date   : 11/06/2016    
    
    ---------------------------*/

    public function index()
    {
        $this->arr_view_data['page_title']           = translation("manage").' '.str_singular($this->module_title);
        $this->arr_view_data['module_title']         = str_singular($this->module_title);
        $this->arr_view_data['module_url_path']      = $this->module_url_path;
        $this->arr_view_data['theme_color']          = $this->theme_color;
        $this->arr_view_data['module_icon']          = $this->module_icon;
        

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    public function create()
    {
        $this->arr_view_data['page_title']      = translation("create").' '.str_singular($this->module_title);
        $this->arr_view_data['module_title']    = str_singular($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['create_icon']     = $this->create_icon;

        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }


    public function store(Request $request)
    {
        $school_id      = $this->school_id;
        $academic_year  = $this->academic_year;
        
        $arr_rules['product_id']          = "required|regex:/^[a-zA-Z0-9 ]+$/";
        $arr_rules['product_name']        = "required|regex:/^[a-zA-Z0-9 \-]+$/";
        $arr_rules['unit_price']          = "required|numeric";
        $arr_rules['total_price']         = "required|numeric";
        $arr_rules['quantity']            = "required|numeric";
        
        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'digits_between:10,14' => translation('please_enter_telephone_no_within_range_of_10_-_14'),
                            'digits_between:6,14'  => translation('please_enter_telephone_no_within_range_of_6_-_14'),
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'),
                            'alpha'                => translation('please_enter_letters_only')  
                        );
        
        
        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }   


        /*-----------------------------------Item Image--------------------------------------*/
        if ($request->file('product_image')) 
        {
            
            $arr_image_size = [];
            $arr_image_size = getimagesize($request->file('product_image'));

            if(!$arr_image_size)
            {
                Flash::error(translation('please_use_valid_image'));
                return redirect()->back(); 
            }

            $minHeight = 250;
            $minWidth  = 250;
            $maxHeight = 2000;
            $maxWidth  = 2000;

            if(($arr_image_size[0] < $minWidth || $arr_image_size[0] > $maxWidth) && ($arr_image_size[1] < $minHeight || $arr_image_size[1] > $maxHeight))
            {
                
                Flash::error(translation('please_upload_image_with_height_and_width_greater_than_or_equal_to_250_x_250_less_than_or_equal_to_2000_x_2000_for_best_result'));
                return redirect()->back();
            }

            $excel_file_name = $request->file('product_image');
            $file_extension   = strtolower($request->file('product_image')->getClientOriginalExtension()); 
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name       = sha1(uniqid().$excel_file_name.uniqid()).'.'.$file_extension;
                $request->file('product_image')->move($this->stock_product_base_img_path,$file_name);   
                $arr_data['image'] = $file_name; 
            }
            else
            {
                Flash::error('invalid_file_type_while_creating'.str_singular($this->module_title));
                return redirect()->back();
            }   
        }
        $does_exists_item_id = $this->BaseModel->where('school_id',$school_id)
                                               ->where('product_id',$request->input('product_id'))
                                               ->count();

        if($does_exists_item_id>0)
        {
            Flash::error(str_singular($this->module_title).' '.translation('already_exists_with_this_product_id'));
            return redirect()->back()->withInput($request->all());;
        }

        $does_exists_item_name = $this->BaseModel->where('school_id',$school_id)
                                                 ->where('product_name',$request->input('product_name'))
                                                 ->count();
        if($does_exists_item_name>0)
        {
            Flash::error(str_singular($this->module_title).' '.translation('already_exists_with_this_product_name'));
            return redirect()->back()->withInput($request->all());;
        }
        else
        {

            $arr_data['school_id']           = $school_id;
            $arr_data['product_id']          = trim ($request->input('product_id'));
            $arr_data['product_name']        = trim($request->input('product_name'));
            $arr_data['quantity']            = trim($request->input('quantity'));
            $arr_data['available_stock']     = trim($request->input('quantity'));
            $arr_data['price']               = trim($request->input('unit_price'));
            $arr_data['total_price']         = trim($request->input('total_price'));
            $arr_data['academic_year_id']    = $academic_year;
            $arr_data['date_created']        = date('Y-m-d');
        
            $create_product = $this->BaseModel->create($arr_data);
        }
   
        if($create_product)
        {
            Flash::success(str_singular($this->module_title).' '.translation('created_successfully'));
            return redirect()->back();
        }
        else
        {
            Flash::error(translation('problem_occured_while_creating').' '.str_singular($this->module_title)); 
        }   

        return redirect()->back();                                            
    }



    public function get_records(Request $request)
    {
        $role = Sentinel::findRoleBySlug(config('app.project.role_slug.school_admin_role_slug'));
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;

        $obj_stock       = $this->get_stock_items($request);

        $json_result     = Datatables::of($obj_stock);

        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('stocks.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data)
                            {
                                return base64_encode($data->id);
                            });
                            
        }                    

        $json_result     = $json_result->editColumn('product_id',function($data) 
                            { 
                                 
                                if($data->product_id!=null && $data->product_id!=''){

                                    return  $data->product_id;
                                }else{
                                    return  '-';
                                }

                            })
                            ->editColumn('product_name',function($data) 
                            { 
                                 
                                if($data->product_name!=null && $data->product_name!=''){

                                    return  ucwords($data->product_name);
                                }else{
                                    return  '-';
                                }

                            })
                            ->editColumn('date',function($data) 
                            { 
                                 
                                if($data->date_created!=null && $data->date_created!=''){

                                    return  getdateFormat($data->date_created);
                                }else{
                                    return  '-';
                                }

                            })
                            ->editColumn('total_price',function($data)
                            {   
                                if($data->total_price!=null && $data->total_price!=''){
                                   
                                    return $data->total_price;
                                }else{
                                    return  '-';
                                }
                            })
                           
                            ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                            {
                                    
                                $build_edit_action = $build_view_action = $build_delete_action ='';
                               
                                    if(array_key_exists('stocks.update', $arr_current_user_access))
                                    {
                                            $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                            $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit"></i></a>';
                                    }

                                    if(array_key_exists('stocks.delete', $arr_current_user_access))
                                    {
                                            $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                            $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('once_you_delete_record_all_distribution_details_will_also_get_deleted').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                        
                                    }
                                    /*$build_view_action.'&nbsp;'.*/
                                return $build_edit_action.'&nbsp;'.$build_delete_action;
                            })
                            ->editColumn('build_checkbox',function($data)use($arr_current_user_access){
                                $build_checkbox ='';
                                if(array_key_exists('stocks.update', $arr_current_user_access) && array_key_exists('stocks.delete', $arr_current_user_access))
                                    {
                                        $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                    }
                            return $build_checkbox;
                            })
                            ->make(true);

        $build_result = $json_result->getData();
                            
                            
        
        return response()->json($build_result);
        
    }

    function get_stock_items(Request $request)
    {
        $school_id =  $this->school_id;         
        $arr_academic_year = [];

        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }              
        
        $stock_table                 = $this->BaseModel->getTable();
        $prefixed_stock_table        = DB::getTablePrefix().$this->BaseModel->getTable();

        $obj_stock_items = DB::table($stock_table)
                                ->select(DB::raw($prefixed_stock_table.".id as id,".
                                                 $prefixed_stock_table.".product_id as product_id,".
                                                 $prefixed_stock_table.".product_name as product_name,".
                                                 $prefixed_stock_table.".total_price as total_price,".
                                                 $prefixed_stock_table.".date_created as date_created,".
                                                 $prefixed_stock_table.".price as price,".
                                                 $prefixed_stock_table.".quantity as quantity,".
                                                 $prefixed_stock_table.".available_stock as available_stock"
                                                 )) 
                                ->where($stock_table.'.school_id','=', $school_id)
                                ->whereIn($stock_table.'.academic_year_id',$arr_academic_year)
                                ->whereNull($stock_table.'.deleted_at')
                                ->orderBy('id','DESC');
                                
        /* ---------------- Filtering Logic ----------------------------------*/                    

        $search = $request->input('search');
            $search_term = $search['value'];

            if($request->has('search') && $search_term!="")
            {
                $obj_stock_items = $obj_stock_items->WhereRaw("( (".$prefixed_stock_table.".product_id LIKE '%".$search_term."%') ")
                                                     ->orWhereRaw("(".$prefixed_stock_table.".product_name LIKE '%".$search_term."%') ")
                                                     ->orWhereRaw("(".$prefixed_stock_table.".quantity LIKE '%".$search_term."%') ")
                                                     ->orWhereRaw("(".$prefixed_stock_table.".date_created LIKE '%".$search_term."%') ")
                                                     ->orWhereRaw("(".$prefixed_stock_table.".price LIKE '%".$search_term."%') ")
                                                     ->orWhereRaw("(".$prefixed_stock_table.".total_price LIKE '%".$search_term."%'))");
                                     
                                     
            }

        return $obj_stock_items;
    }


    public function edit($enc_id)
    {
        
        $id = base64_decode($enc_id);   
        if(!is_numeric($id)){
            Flash::error(translation('something_went_wrong'));
             return redirect($this->module_url_path);
        }

        $obj_stock = $this->BaseModel->where('id',$id)->first(); 

        if(isset($obj_stock) && count($obj_stock)>0)
        {
            $arr_stock = $obj_stock->toArray();
            $this->arr_view_data['arr_stock']         = $arr_stock;
        }
        else
        {
            Flash::error(translation('something_went_wrong'));
             return redirect()->back();
        }
               
        $this->arr_view_data['page_title']                 = translation("edit").' '.str_singular($this->module_title);
        $this->arr_view_data['module_title']               = str_singular($this->module_title);
        $this->arr_view_data['stock_product_public_img_path'] = $this->stock_product_public_img_path;
        $this->arr_view_data['module_url_path']            = $this->module_url_path;
        $this->arr_view_data['theme_color']                = $this->theme_color;
        $this->arr_view_data['module_icon']                = $this->module_icon;
        $this->arr_view_data['edit_icon']                  = $this->edit_icon;
        $this->arr_view_data['enc_id']                     = $enc_id;
        $this->arr_view_data['base_path']                  = $this->stock_product_base_img_path;
        $this->arr_view_data['img_path']                   = $this->stock_product_public_img_path;

        return view($this->module_view_folder.'.edit',$this->arr_view_data);        
    }

    public function update(Request $request,$enc_id)
    {
        /*--------------------School_id---------------------*/

        $school_id = $this->school_id;
        $id = '';   
        /*------------------------------------------------*/
        
            $id =base64_decode($enc_id);
        if(!is_numeric($id))
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }

        $arr_rules = array();
        $arr_data  = [];
        
        $arr_rules['product_id']          = "required|regex:/^[a-zA-Z0-9 ]+$/";
        $arr_rules['product_name']        = "required|regex:/^[a-zA-Z0-9 \-]+$/";
        $arr_rules['unit_price']          = "required|numeric";
        $arr_rules['total_price']         = "required|numeric";
        $arr_rules['quantity']            = "required|numeric";
        
        $messages = array(
                            'regex'                => translation('please_enter_valid_text_format'),
                            'email'                => translation('please_enter_valid_email'),
                            'numeric'              => translation('please_enter_digits_only'),
                            'digits_between:10,14' => translation('please_enter_telephone_no_within_range_of_10_-_14'),
                            'digits_between:6,14'  => translation('please_enter_telephone_no_within_range_of_6_-_14'),
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'),
                            'alpha'                => translation('please_enter_letters_only')  
                        );
        
        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all(),$messages);
        }   

    
        /*----------------------------- Image Upload starts here --------------------- */

        $is_new_file_uploaded = FALSE;
        $file_name = '';
        
        $oldImage = $request->input('old_image');
        if($request->hasFile('product_image'))
        { 
            $arr_image_size = [];
            $arr_image_size = getimagesize($request->file('product_image'));

            if(!$arr_image_size)
            {
                Flash::error('Please use valid image');
                return redirect()->back(); 
            }

            $minHeight = 250;
            $minWidth  = 250;
            $maxHeight = 2000;
            $maxWidth  = 2000;

            if(($arr_image_size[0] < $minWidth || $arr_image_size[0] > $maxWidth) && ($arr_image_size[1] < $minHeight || $arr_image_size[1] > $maxHeight))
            {
                
                Flash::error(translation('please_upload_image_with_height_and_width_greater_than_or_equal_to_250_x_250_less_than_or_equal_to_2000_x_2000_for_best_result'));
                return redirect()->back();
            }

            $file_name = $request->file('product_image');
            $file_extension = strtolower($request->file('product_image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name = sha1(uniqid().$file_name.uniqid()).'.'.$file_extension;
                
                $isUpload = $request->file('product_image')->move($this->stock_product_base_img_path , $file_name);
                if($isUpload)
                {
                    @unlink($this->stock_product_base_img_path.$oldImage);
                    @unlink($this->stock_product_base_img_path.'/thumb_50X50_'.$oldImage);
                }
            }
            else
            {
                Flash::error(translation('invalid_file_type_while_updating').' '.str_singular($this->module_title));
                return redirect()->back();
            }
        }
        else
        {
             $file_name = $oldImage;
        }       

        $arr_data['image']               = $file_name;
        $arr_data['school_id']           = $school_id;
        $arr_data['product_id']          = $request->input('product_id');
        $arr_data['product_name']        = $request->input('product_name');
        $arr_data['total_price']         = $request->input('total_price');
        $arr_data['price']               = $request->input('unit_price');
        $arr_data['academic_year_id']    = $this->academic_year;

        $update_product = $this->BaseModel->where('id',$id)->update($arr_data);

        if($update_product)
        {
            Flash::success(str_singular($this->module_title).' '.translation('updated_successfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_updating').' '.str_singular($this->module_title)); 
        }   

        return redirect()->back();                                  
    }


    public function delete($enc_id)
    {
        $id          = base64_decode($enc_id);
        $arr_details = [];   
        $obj_details = $this->BaseModel->where('id',$id)
                                       ->with('get_distribution_data')
                                       ->first(); 
        
        if($obj_details)
        {

            $arr_details = $obj_details->toArray();


            if(isset($arr_details['get_distribution_data']) && sizeof($arr_details['get_distribution_data'])>0)
            {
                foreach ($arr_details['get_distribution_data'] as $key => $data)
                {
                    $delete_stock_distribution = $this->StockDistributedModel->where('id',$data['id'])
                                                                        ->delete();   
                }
            }
        } 

        return $this->module_delete($enc_id);                                
    }


    public function arrange_locale_wise(array $arr_data)
    {
        if(sizeof($arr_data)>0)
        {
            foreach ($arr_data as $key => $data) 
            {
                $arr_tmp = $data;
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

    public function view($enc_id)
    {
         if($enc_id)
        {
            $id = base64_decode($enc_id);   
        }
        else
        {
             Flash::error(translation('something_went_wrong'));
             return redirect()->back();
        }

        $obj_stock = $this->BaseModel
                            ->where('id',$id)
                            ->first(); 

        if(isset($obj_stock) && count($obj_stock)>0)
        {
            $arr_stock = $obj_stock->toArray();
            $this->arr_view_data['arr_stock']         = $arr_stock;
        }
        else
        {
            Flash::error(translation('something_went_wrong'));
             return redirect()->back();
        }
        $this->arr_view_data['page_title']              = translation("view").' '.str_singular($this->module_title);
        $this->arr_view_data['module_title']            = str_singular($this->module_title);
        $this->arr_view_data['module_url_path']         = $this->module_url_path;
        $this->arr_view_data['theme_color']             = $this->theme_color;
        $this->arr_view_data['module_icon']             = $this->module_icon;
        $this->arr_view_data['view_icon']               = $this->view_icon;

        return view($this->module_view_folder.'.view',$this->arr_view_data);
    }

    public function multi_action(Request $request)
    {
        $arr_rules = array();
        $arr_rules['multi_action'] = "required";
        $arr_rules['checked_record'] = "required";
        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            Flash::error(translation('please_select_record_to_perform_multiaction'));
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $multi_action = $request->input('multi_action');
        $checked_record = $request->input('checked_record');

        /* Check if array is supplied*/
        if(is_array($checked_record) && sizeof($checked_record)<=0)
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction') );
            return redirect()->back();
        }

        foreach ($checked_record as $record_id) 
        {
            if($multi_action=="delete")
            {
               $this->perform_delete(base64_decode($record_id));    
               Flash::success($this->module_title.' '.strtolower(translation('deleted_succesfully'))); 
            } 
        }

        return redirect()->back();
    }

    public function perform_delete($id)
    {
        
        $obj_details = $this->BaseModel->where('id',$id)
                                       ->with('get_distribution_data')
                                       ->first(); 
        if($obj_details)
        {

            $arr_details = $obj_details->toArray();

            if(isset($arr_details['get_distribution_data']) && sizeof($arr_details['get_distribution_data'])>0)
            {
                foreach ($arr_details['get_distribution_data'] as $key => $data)
                {
                    $delete_distribution_data = $this->StockDistributedModel->where('id',$data['id'])
                                                                        ->delete();    
                }
            }
        } 

        $delete= $this->BaseModel->where('id',$id)->delete();
        if($delete)
        {  
            return TRUE;
        }

        return FALSE;
    }
    
}
