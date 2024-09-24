<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Common\Services\CommonDataService;
use App\Common\Traits\MultiActionTrait;
use App\Models\CanteenProductTypesModel;
use App\Models\CanteenProductsModel;
use App\Models\WeeklyMealsModel;
use App\Models\DailyMealsModel;

use Datatables;
use Validator;
use Sentinel;
use Session;
use Flash;
use DB;

class CanteenProductsController extends Controller
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

        $this->CanteenProductTypesModel     = new CanteenProductTypesModel();
        $this->CanteenProductsModel         = new CanteenProductsModel();
        $this->WeeklyMealsModel             = new WeeklyMealsModel();
        $this->DailyMealsModel              = new DailyMealsModel();
        $this->BaseModel                    = $this->CanteenProductsModel;

        $this->food_items_base_img_path     = public_path().config('app.project.img_path.food_items');
        $this->food_items_public_img_path   = url('/').config('app.project.img_path.food_items');

        $this->arr_view_data                = [];
        $this->arr_view_data['base_path']   = $this->food_items_base_img_path;
        $this->arr_view_data['img_path']    = $this->food_items_public_img_path;
        
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')."/canteen_products");
        
        $this->LanguageService              = $langauge;
        $this->module_title                 = translation("canteen_product");
        $this->module_view_folder           = "schooladmin.canteen_products";
        $this->school_id                    = Session::has('school_id')?Session::get('school_id'):0;
        
        $this->theme_color                  = theme_color();
        $this->module_icon                  = "fa fa-cutlery";
        $this->create_icon                  = "fa fa-plus-circle";
        $this->edit_icon                    = "fa fa-edit";
        $this->view_icon                    = "fa fa-eye";
        
        $this->CommonDataService  = $CommonDataService;
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
        $arr_types = [];
        $obj_types = $this->CanteenProductTypesModel->orderBy('type','ASC')->get();

        if(isset($obj_types) && !empty($obj_types))
        {
            $arr_types = $obj_types->toArray();
            $this->arr_view_data['arr_types']     = $arr_types;
        }


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

        /*--------------------School_id-------------------*/

        $school_id = $this->school_id;
        
        /*------------------------------------------------*/
        
        $arr_rules['product_id']          = "required|alpha_num";
        $arr_rules['product_name']        = "required";
        $arr_rules['product_price']       = "required|numeric|min:1";


        $message['required']              = translation('this_field_is_required');
        $message['numeric']               = translation('please_enter_digits_only');
        $messages['min']                  = translation('please_enter_a_value_greater_than_or_equal_to_1');
        $messages['alpha_num']            = translation('letters_and_numbers_only');
        
        
        $validator = Validator::make($request->all(),$arr_rules,$message);

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

            $excel_file_name = $request->file('product_image');
            $file_extension   = strtolower($request->file('product_image')->getClientOriginalExtension()); 
            if(in_array($file_extension,['png','jpg','jpeg']))
            {
                $file_name       = sha1(uniqid().$excel_file_name.uniqid()).'.'.$file_extension;
                $request->file('product_image')->move($this->food_items_base_img_path,$file_name);   
                $arr_data['product_image'] = $file_name; 
            }
            else
            {
                Flash::error('invalid_file_type_while_creating'.str_singular($this->module_title));
                return redirect()->back();
            }   
        }
        $does_exists_item_id = $this->CanteenProductsModel->where('school_id',$school_id)
                                               ->where('product_id',$request->input('product_id'))
                                               ->count();

        if($does_exists_item_id>0)
        {
            Flash::error(str_singular($this->module_title).' '.translation('already_exists_with_this_product_id'));
            return redirect()->back()->withInput($request->all());;
        }

        $does_exists_item_name = $this->CanteenProductsModel->where('school_id',$school_id)
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
            $arr_data['product_id']          = $request->input('product_id');
            $arr_data['product_type']        = $request->input('product_type');
            $arr_data['product_name']        = $request->input('product_name');
            $arr_data['description']         = $request->input('product_description');
            $arr_data['price']               = $request->input('product_price');
        
            $create_product = $this->CanteenProductsModel->create($arr_data);
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
        $role = Session::get('role');
        $arr_current_user_access =[];
    
        $arr_current_user_access = $this->CommonDataService->current_user_access();

        $obj_courses     = $this->get_canteen_items($request);

        $json_result     = Datatables::of($obj_courses);

        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('canteen.update', $arr_current_user_access))
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
                            ->editColumn('product_type',function($data) 
                            { 
                                 
                                if($data->product_type!=null && $data->product_type!=''){

                                    return  ucwords($data->product_type);
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
                            ->editColumn('product_image',function($data) 
                            { 
                                 
                                if($data->product_image!=null && $data->product_image!='' && file_exists($this->food_items_base_img_path.$data->product_image)){
                                    return '<img src="'.$this->food_items_public_img_path.'/'.$data->product_image.'" height="40" width="40">';
                                    
                                }else{
                                    return   '<img src="'.url('/').'/images/default_food.jpg" height="40" width="40">';
                                }

                            })
                            ->editColumn('product_price',function($data)
                            {   
                                if($data->product_price!=null && $data->product_price!=''){
                                   
                                    return $data->product_price;
                                }else{
                                    return  '-';
                                }
                            })
                           
                            ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                            {
                                    
                                $build_edit_action = $build_view_action = $build_delete_action ='';
                                       
                                    $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                    $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye" ></i></a>';

                                    if(array_key_exists('canteen.update', $arr_current_user_access))
                                    {

                                        $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                        $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';
                                    }

                                    if(array_key_exists('canteen.delete', $arr_current_user_access))
                                    {
                                        $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                        $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                    }

                                return $build_view_action.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_delete_action;
                            })
                            ->editColumn('build_checkbox',function($data)use($arr_current_user_access){
                                $build_checkbox ='';
                                if(array_key_exists('canteen.delete', $arr_current_user_access))
                                    {
                                        $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                    }
                            return $build_checkbox;
                            })
                            ->make(true);

        $build_result = $json_result->getData();
                            
                            
        
        return response()->json($build_result);
        
    }

    function get_canteen_items(Request $request)
    {
        $school_id =  $this->school_id;                     
        $canteen_item_table          = $this->CanteenProductsModel->getTable();
        $prefixed_canteen_item_table = DB::getTablePrefix().$this->CanteenProductsModel->getTable();
        $product_type_table          = $this->CanteenProductTypesModel->getTable();
        $prefixed_product_type_table = DB::getTablePrefix().$this->CanteenProductTypesModel->getTable();

        $obj_canteen_item = DB::table($canteen_item_table)
                                ->select(DB::raw($prefixed_canteen_item_table.".id as id,".
                                                 $prefixed_canteen_item_table.".product_id as product_id,".
                                                 $prefixed_canteen_item_table.".product_name as product_name,".
                                                 $prefixed_canteen_item_table.".product_image as product_image,".
                                                 $prefixed_canteen_item_table.".description as description,".
                                                 $prefixed_canteen_item_table.".price as product_price,".
                                                 $prefixed_product_type_table.".type as product_type"
                                                 )) 
                                ->join($product_type_table,$canteen_item_table.'.product_type','=',$product_type_table.'.id')
                                ->where($canteen_item_table.'.school_id','=', $school_id)
                                ->whereNull($canteen_item_table.'.deleted_at')
                                ->orderBy('id');
                                
        /* ---------------- Filtering Logic ----------------------------------*/                    

        $search = $request->input('search');
            $search_term = $search['value'];

            if($request->has('search') && $search_term!="")
            {
                $obj_canteen_item = $obj_canteen_item->WhereRaw("( (".$prefixed_canteen_item_table.".product_id LIKE '%".$search_term."%') ")
                                                     ->orWhereRaw("(".$prefixed_canteen_item_table.".product_name LIKE '%".$search_term."%') ")
                                                     ->orWhereRaw("(".$prefixed_canteen_item_table.".price LIKE '%".$search_term."%') ")
                                                     ->orWhereRaw("(".$prefixed_product_type_table.".type LIKE '%".$search_term."%'))");
                                     
                                     
            }

        return $obj_canteen_item;
    }


    public function edit($enc_id)
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

        $arr_types = [];
        $obj_types = $this->CanteenProductTypesModel->orderBy('type','ASC')->get();

        if(isset($obj_types) && !empty($obj_types))
        {
            $arr_types = $obj_types->toArray();
            $this->arr_view_data['arr_types']     = $arr_types;
        }

        $obj_canteen = $this->CanteenProductsModel->where('id',$id)->first(); 

        if(isset($obj_canteen) && count($obj_canteen)>0)
        {
            $arr_canteen_item = $obj_canteen->toArray();
            $this->arr_view_data['arr_canteen_item']         = $arr_canteen_item;
        }
        else
        {
            Flash::error(translation('something_went_wrong'));
             return redirect()->back();
        }
               
        $this->arr_view_data['page_title']                 = translation("edit").' '.str_singular($this->module_title);
        $this->arr_view_data['module_title']               = str_singular($this->module_title);
        $this->arr_view_data['food_items_public_img_path'] = $this->food_items_public_img_path;
        $this->arr_view_data['module_url_path']            = $this->module_url_path;
        $this->arr_view_data['theme_color']                = $this->theme_color;
        $this->arr_view_data['module_icon']                = $this->module_icon;
        $this->arr_view_data['edit_icon']                  = $this->edit_icon;
        $this->arr_view_data['enc_id']                     = $enc_id;

        return view($this->module_view_folder.'.edit',$this->arr_view_data);        
    }

    public function update(Request $request,$enc_id)
    {
        /*--------------------School_id---------------------*/

        $school_id = $this->school_id;
        $id = '';   
        /*------------------------------------------------*/
        if($enc_id)
        {
            $id =base64_decode($enc_id);
        }
        else
        {
            Flash::error(translation('something_went_wrong'));
            return redirect()->back();
        }

        $arr_rules = array();
        $arr_data  = [];
        
        $arr_rules['product_id']          = "required|alpha_num";
        $arr_rules['product_name']        = "required"; 
        $arr_rules['product_price']       = "required|numeric|min:1";

        $message['required']              = translation('this_field_is_required');
        $message['numeric']               = translation('please_enter_digits_only');
        $messages['min']                  = translation('please_enter_a_value_greater_than_or_equal_to_1');
        $messages['min']                  = translation('letters_and_numbers_only');
        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
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
                $file_name = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('product_image')->move($this->food_items_base_img_path , $file_name);
                if($isUpload)
                {
                    @unlink($this->food_items_base_img_path.$oldImage);
                    @unlink($this->food_items_base_img_path.'/thumb_50X50_'.$oldImage);
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

        $arr_data['product_image']       = $file_name;
        $arr_data['school_id']           = $school_id;
        $arr_data['product_id']          = $request->input('product_id');
        $arr_data['product_type']        = $request->input('product_type');
        $arr_data['product_name']        = $request->input('product_name');
        $arr_data['description']         = $request->input('product_description');
        $arr_data['price']               = $request->input('product_price');
    
        $update_product = $this->CanteenProductsModel->where('id',$id)->update($arr_data);

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
                                       ->with('get_weekly_meals.get_daily_meals')
                                       ->first(); 
        
        if($obj_details)
        {

            $arr_details = $obj_details->toArray();


            if(isset($arr_details['get_weekly_meals']) && sizeof($arr_details['get_weekly_meals'])>0)
            {
                foreach ($arr_details['get_weekly_meals'] as $key => $weekly_meals)
                {
                    if(isset($weekly_meals['get_daily_meals']) && sizeof($weekly_meals['get_daily_meals'])>0)
                    {
                        foreach ($weekly_meals['get_daily_meals'] as $key => $daily_meals) 
                        {
                            $delete_daily_meals = $this->DailyMealsModel->where('id',$daily_meals['id'])
                                                                        ->delete();    
                        }
                    }

                    $delete_weekly_meals = $this->WeeklyMealsModel->where('id',$weekly_meals['id'])
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

        $obj_canteen = $this->CanteenProductsModel
                            ->with('get_product_type')
                            ->where('id',$id)
                            ->first(); 

        if(isset($obj_canteen) && count($obj_canteen)>0)
        {
            $arr_canteen_item = $obj_canteen->toArray();
            $this->arr_view_data['arr_canteen_item']         = $arr_canteen_item;
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
                                       ->with('get_weekly_meals.get_daily_meals')
                                       ->first(); 
        if($obj_details)
        {

            $arr_details = $obj_details->toArray();

            if(isset($arr_details['get_weekly_meals']) && sizeof($arr_details['get_weekly_meals'])>0)
            {
                foreach ($arr_details['get_weekly_meals'] as $key => $weekly_meals)
                {
                    if(isset($weekly_meals['get_daily_meals']) && sizeof($weekly_meals['get_daily_meals'])>0)
                    {
                        foreach ($weekly_meals['get_daily_meals'] as $key => $daily_meals) 
                        {
                            $delete_daily_meals = $this->DailyMealsModel->where('id',$daily_meals['id'])
                                                                        ->delete();    
                        }
                    }

                    $delete_weekly_meals = $this->WeeklyMealsModel->where('id',$weekly_meals['id'])
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
