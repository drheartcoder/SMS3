<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Common\Traits\MultiActionTrait;

use App\Models\DailyMealsModel;
use App\Models\WeeklyMealsModel;
use App\Models\CanteenProductsModel;
use App\Models\CanteenProductTypesModel;
use App\Common\Services\CommonDataService;
use Datatables;
use Validator;
use Sentinel;
use Session;
use Flash;
use File;
use Mail;
use DB;

class DailyMealsController extends Controller
{
    use MultiActionTrait ;
   
    public function __construct(CommonDataService $CommonDataService)
    {   
        $this->WeeklyMealsModel             = new WeeklyMealsModel();
        $this->DailyMealsModel              = new DailyMealsModel();
        $this->CanteenProductTypesModel     = new CanteenProductTypesModel();
        $this->CanteenProductsModel         = new CanteenProductsModel();
        $this->BaseModel                    = $this->DailyMealsModel;
        
        $this->food_items_base_img_path     = public_path().config('app.project.img_path.food_items');
        $this->food_items_public_img_path   = url('/').config('app.project.img_path.food_items');

        $this->arr_view_data                = [];

        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')."/daily_meals");
        $this->module_title                 = translation("daily_meal");
        $this->module_view_folder           = "schooladmin.daily_meals";
        $this->school_id                    = Session::has('school_id')?Session::get('school_id'):0;
        $this->arr_view_data                = [];
        
        $this->theme_color                  = theme_color();
        $this->module_icon                  = "fa-apple";
        $this->create_icon                  = "fa-plus-circle";
        $this->edit_icon                    = "fa-edit";
        $this->view_icon                    = "fa-eye";
        $this->CommonDataService            = $CommonDataService;
       
    }   
 
    
    /*-----Daily Meal Module--------

    Authar : Sayali B
    Date   : 13/06/2018    
    
    ---------------------------*/

    public function index(Request $request)
    {
          $school_id = $this->school_id;
        
        $this->arr_view_data['page_title']              = translation("manage").' '.str_singular($this->module_title);
        $this->arr_view_data['module_title']            = str_singular($this->module_title);
        $this->arr_view_data['module_url_path']         = $this->module_url_path;
        $this->arr_view_data['theme_color']             = $this->theme_color;
        $this->arr_view_data['module_icon']             = $this->module_icon;
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    public function create()
    {
        $school_id  = $this->school_id;
        $date       = Date('Y-m-d');
        $weekday    = date('l', strtotime($date));
        $obj_week_meals = $this->WeeklyMealsModel
                               ->with(['get_product_details'=>function($q){
                                $q->with('get_product_type');
                               }])
                               ->where('school_id',$school_id)
                               ->where('week_day',$weekday)
                               ->get();

        if(isset($obj_week_meals) && $obj_week_meals != null)
        {
            $arr_weekly_meals = $obj_week_meals->toArray();
            $this->arr_view_data['arr_weekly_meals']            = $arr_weekly_meals;
        }
    
        $this->arr_view_data['page_title']              = translation("create").' '.str_singular($this->module_title);
        $this->arr_view_data['module_title']            = str_singular($this->module_title);
        $this->arr_view_data['module_url_path']         = $this->module_url_path;
        $this->arr_view_data['theme_color']             = $this->theme_color;
        $this->arr_view_data['module_icon']             = $this->module_icon;
        $this->arr_view_data['base_path']   = $this->food_items_base_img_path;
        $this->arr_view_data['img_path']    = $this->food_items_public_img_path;
        
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    public function store(Request $request)
    {   
        
       /*--------------------School_id---------------------*/

        $school_id = $this->school_id;
        
        /*------------------------------------------------*/

        $arr_rules['date']            = "required";
        $arr_rules['checked_record']  = "required";
        $arr_rules['stock']     = "required";

        $messages = array(  'required'             => translation('this_field_is_required')
                         );

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
            Flash::error(translation('please_select_the_canteen_product_which_you_want_to_add_as_a').' '    .str_singular($this->module_title));
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }   

        /*----------------------------------------------------------------------------*/
        $date            = $request->input('date');
        $daily_stock     = $request->input('stock');
        $checked_record  = $request->input('checked_record');
        
         $obj_daily_meal = $this->BaseModel
                               ->where('school_id',$school_id)
                               ->where('date',$date)
                               ->get();


        $arr_exist_id = $arr_existance = [];
        if(isset($obj_daily_meal) && $obj_daily_meal!= null)
        {
            $arr_obj_daily_meal = $obj_daily_meal->toArray();
        }

        
        foreach ($checked_record as $key_check_record => $value_checked_record) 
        {
            
            $arr_data['school_id']          = $school_id;
            $arr_data['date']               = $date;
            $weekly_id                      = $checked_record[$key_check_record];
            
            $arr_data['weekly_meal_id']     = base64_decode($weekly_id);
            $arr_data['stock']              = $daily_stock[$weekly_id];
            $arr_data['available_stock']    = $daily_stock[$weekly_id];

            array_push($arr_exist_id,base64_decode($weekly_id));

            $does_exists = $this->BaseModel->where('school_id',$school_id)
                                           ->where('date',$arr_data['date'])
                                           ->where('weekly_meal_id',$arr_data['weekly_meal_id'])
                                           ->first();
            
            if($does_exists)
            {
                $id                  = $does_exists->id;

                $create_daily_meals =  $this->BaseModel->where('id',$id)
                                                       ->where('school_id',$school_id)
                                                       ->where('date',$arr_data['date'])
                                                       ->where('weekly_meal_id',$arr_data['weekly_meal_id'])
                                                       ->update($arr_data);
            }
            else
            {
                $create_daily_meals = $this->BaseModel->create($arr_data);    
            }
        }

        foreach($arr_obj_daily_meal as $key => $value)
        {
            if(!in_array($value['weekly_meal_id'],$arr_exist_id))
            {

                $delete_meal = $this->BaseModel->where('school_id',$school_id)
                                               ->where('date',$date)
                                               ->where('weekly_meal_id',$value['weekly_meal_id'])
                                               ->delete();  
            }
        }
        /*------------- Check if Data already exists ---------------------------------------- */
        
        if(sizeof($create_daily_meals)>0)
        {
            Flash::success(str_singular($this->module_title).' '.translation('created_successfully'));
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

        $obj_weekly_meals     = $this->get_weekly_meals($request);

        $json_result     = Datatables::of($obj_weekly_meals);

        $json_result     = $json_result->blacklist(['id']);
        
        $json_result     = $json_result->editColumn('quantity',function($data)
                                        {
                                            $quantity = 0;

                                            if(isset($data->quantity) && sizeof($data->quantity)>0)
                                            {
                                                $quantity = $data->quantity;
                                            }

                                            return $quantity;
                                        })
                                        ->editColumn('price',function($data)
                                        {
                                            return  $data->price;
                                        })
                                        ->editColumn('enc_id',function($data)
                                        {
                                            return  base64_encode($data->id);
                                        })
                                       ->editColumn('item_image',function($data)
                                        {
                                            $item_image = $this->food_items_public_img_path.($data->product_image);
                                            $image = '';
                                            if($data->product_image && file_exists($this->food_items_base_img_path.$data->product_image))
                                            {
                                                $image =  '<img src="'.$item_image.'" style="width:40px; height:40px;" />';
                                            }                          
                                            else
                                            {
                                                $image =  '<img src="'.url("/").'/images/default_food.jpg" style="width:40px; height:40px;" />';
                                            }
                                            return $image;
                                        })
                                       ->editColumn('daily_quantity',function($data)
                                        {
                                            if(isset($data->daily_quantity) && sizeof($data->daily_quantity)>0)
                                            {
                                                return '<input type="text" name="quantity" placeholder="'.translation('enter_daily_quantity').'" data-rule-digits="true" value="'.$data->daily_quantity.'" class="form-control" onChange="updateQuantity(this,\''.base64_encode($data->daily_meal_id).'\','.$data->id.')" />';  
                                            }
                                            else
                                            {
                                                return '<input type="text" name="quantity" placeholder="'.translation('enter_daily_quantity').'" data-rule-digits="true" class="form-control" onChange="addQuantity(this,'.$data->id.')" />';   
                                            }
                                        })
                                       ->editColumn('stock_sold',function($data)
                                        {
                                            $stock_sold = 0;
                                            if(isset($data->available_stock) && sizeof($data->available_stock)>0)
                                            {
                                                $stock_sold = $data->daily_quantity - $data->available_stock;
                                            }
                                            /*else
                                            {
                                                $stock_sold = '0';   
                                            }*/
                                            if($stock_sold<0)
                                            {
                                                $stock_sold = 0;
                                            }
                                            return  $stock_sold;
                                        })
                                        ->editColumn('build_action_btn',function($data) use($arr_current_user_access)
                                        {
                                            $built_delete = $built_is_active = "";

                                            if(array_key_exists('canteen.update',$arr_current_user_access))
                                            {
                                                
                                                if($data->is_active != null && $data->is_active == "1")
                                                {
                                                    $built_is_active = '<a class="light-blue-color" title="Unlock" href="'.$this->module_url_path.'/deactivate/'.base64_encode($data->daily_meal_id).'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_deactivate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-unlock"></i></a>';
                                                }
                                                else
                                                {
                                                    if(isset($data->daily_quantity))
                                                    {   
                                                        $built_is_active = '<a class="blue-color" title="Lock" href="'.$this->module_url_path.'/activate/'.base64_encode($data->daily_meal_id).'" 
                                                        onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_activate_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')" ><i class="fa fa-lock"></i></a>';
                                                    }
                                                    else
                                                    {
                                                        $built_is_active = '<a style="position: relative;" class="red-color" href="javascript:void(0)" title="'.translation('activate').'" ><i class="fa fa-lock" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                                    }

                                                }
                                            }

                                            if(array_key_exists('canteen.delete',$arr_current_user_access))
                                            {
                                                if(($data->daily_quantity)>0)
                                                {
                                                    $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->daily_meal_id);
                                                    $built_delete = '<a class="red-color" href="'.$delete_href.'" title="Delete" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>'; 
                                                }
                                                else
                                                {
                                                    $built_delete = '<a style="position: relative;" class="red-color" href="javascript:void(0)" title="'.translation('delete').'" ><i class="fa fa-trash" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                                }
                                            }
                                            return $built_is_active.'&nbsp;'.$built_delete;
                                            
                                        })
                                        ->editColumn('build_checkbox',function($data) use($arr_current_user_access)
                                        {
                                            $build_checkbox='';
                                            if(array_key_exists('canteen.update',$arr_current_user_access) || array_key_exists('canteen.delete',$arr_current_user_access))
                                            {
                                                if(($data->daily_quantity)>0)
                                                {
                                                    $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->daily_meal_id).'" value="'.base64_encode($data->daily_meal_id).'" /><label for="mult_change_'.base64_encode($data->daily_meal_id).'"></label></div>'; 
                                                }
                                                else
                                                {
                                                    $build_checkbox = '-';
                                                }
                                            }    
                                            return $build_checkbox;
                                        })
                                        ->make(true);

        $build_result = $json_result->getData();
            
        return response()->json($build_result);
        
    }

    function get_weekly_meals(Request $request)
    {
        $date                          = Date('Y-m-d');
        $school_id                     = $this->school_id;
        /* Prefixed table name are required wherever we are using DB::raw calls */

        $weekly_meal_table             = $this->WeeklyMealsModel->getTable();
        $prefixed_weekly_meal_table    = DB::getTablePrefix().$this->WeeklyMealsModel->getTable();

        $canteen_product_table         = $this->CanteenProductsModel->getTable();
        $prefixed_canteen_product_table= DB::getTablePrefix().$this->CanteenProductsModel->getTable();

        $product_type_table            = $this->CanteenProductTypesModel->getTable();
        $prefixed_product_type_table   = DB::getTablePrefix().$this->CanteenProductTypesModel->getTable();

        $daily_meal_table              = $this->BaseModel->getTable();
        $prefixed_daily_meal_table     = DB::getTablePrefix().$this->BaseModel->getTable();

        $obj_weekly_meals = DB::table($weekly_meal_table)
                            ->select(DB::raw($prefixed_weekly_meal_table.".id as id,".
                                             $prefixed_canteen_product_table.".product_id as product_id,".
                                             $prefixed_canteen_product_table.".product_name as product_name,".
                                             $prefixed_canteen_product_table.".product_image as product_image,".
                                             $prefixed_weekly_meal_table.".stock as quantity,".
                                             $prefixed_canteen_product_table.".price as price,".
                                             $prefixed_product_type_table.".type as type,".
                                             $prefixed_daily_meal_table.".stock as daily_quantity,".
                                             $prefixed_daily_meal_table.".id as daily_meal_id,".
                                             $prefixed_daily_meal_table.".is_active as is_active,".
                                             $prefixed_daily_meal_table.".available_stock as available_stock,".
                                             $prefixed_daily_meal_table.".date as stock_date"
                                             )) 
                            ->where($weekly_meal_table.'.school_id','=', $school_id)
                            ->whereNull($daily_meal_table.'.deleted_at')
                            ->where($daily_meal_table.'.date','=',$date)
                            ->leftJoin($canteen_product_table,$weekly_meal_table.'.item_id' ,'=', $canteen_product_table.'.id')
                            ->leftJoin($product_type_table,$canteen_product_table.'.product_type' ,'=', $product_type_table.'.id')
                            ->leftJoin($daily_meal_table,$daily_meal_table.'.weekly_meal_id' ,'=', $weekly_meal_table.'.id');

        /* ---------------- Filtering Logic ----------------------------------*/                    

        $arr_search_column = $request->input('column_filter');

        if(isset($arr_search_column['q_date']) && $arr_search_column['q_date']!="")
        {
            $search_term_date = $arr_search_column['q_date'];
            $week_day         = date('l', strtotime($search_term_date));

            $obj_weekly_meals = DB::table($weekly_meal_table)
                                ->select(DB::raw($prefixed_weekly_meal_table.".id as id,".
                                                 $prefixed_canteen_product_table.".product_id as product_id,".
                                                 $prefixed_canteen_product_table.".product_name as product_name,".
                                                 $prefixed_canteen_product_table.".product_image as product_image,".
                                                 $prefixed_weekly_meal_table.".stock as quantity,".
                                                 $prefixed_canteen_product_table.".price as price,".
                                                 $prefixed_product_type_table.".type as type,".
                                                 $prefixed_daily_meal_table.".stock as daily_quantity,".
                                                 $prefixed_daily_meal_table.".id as daily_meal_id,".
                                                 $prefixed_daily_meal_table.".is_active as is_active,".
                                                 $prefixed_daily_meal_table.".available_stock as available_stock,".
                                                 $prefixed_daily_meal_table.".date as stock_date"
                                                 )) 
                                ->where($weekly_meal_table.'.school_id','=', $school_id)
                                ->where($weekly_meal_table.'.week_day','=', $week_day)
                                ->leftJoin($canteen_product_table,$weekly_meal_table.'.item_id' ,'=', $canteen_product_table.'.id')
                                ->leftJoin($product_type_table,$canteen_product_table.'.product_type' ,'=', $product_type_table.'.id')
                                
                                ->leftJoin($daily_meal_table, function ($join) use ( $weekly_meal_table, $daily_meal_table, $search_term_date) {    
                                        $join->on($weekly_meal_table.'.id', '=', $daily_meal_table.'.weekly_meal_id')
                                        ->where($daily_meal_table.'.date','=',$search_term_date)
                                        ->whereNull($daily_meal_table.'.deleted_at');
                                });
                               
                                
        }
        return $obj_weekly_meals;
    }

    public function get_weekly_meals_data(Request $request)
    {
        $data = '';
        $school_id  = $this->school_id;
        $date       = $request->input('date');
        $weekday    = date('l', strtotime($date));
        $obj_week_meals = $this->WeeklyMealsModel
                               ->with(['get_product_details'=>function($q){
                                $q->with('get_product_type');
                               }])
                               ->where('school_id',$school_id)
                               ->where('week_day',$weekday)
                               ->get();

        if(isset($obj_week_meals) && $obj_week_meals != null)
        {
            $arr_weekly_meals = $obj_week_meals->toArray();

            if(isset($arr_weekly_meals) && count($arr_weekly_meals)>0)
            {
                foreach($arr_weekly_meals as $key =>$weekly_meal)
                {
                    $data .='<tr><td>';
                    $data .= '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="'.base64_encode($weekly_meal['id']).'" value="'.base64_encode($weekly_meal['id']).'" /><label for="'.base64_encode($weekly_meal['id']).'"></label></div>';
                    $data .='</td><td>';
                        if(isset($weekly_meal['get_product_details']['product_image']) && $weekly_meal['get_product_details']['product_image'] != '' && file_exists($weekly_meal['get_product_details']['product_image']))
                        {
                            $data .= '<img src="'.url('/').'/uploads/food_products/'.$weekly_meal['get_product_details']['product_image'].'" height="40px" width="50px">';
                        }
                        else
                        {
                            $data .= '<img src="'.url('/').'/images/default-old.png" height="40px" width="50px">';
                        }

                    $data .='</td><td>';
                    $data .= isset($weekly_meal['get_product_details']['get_product_type']['type'])?ucwords($weekly_meal['get_product_details']['get_product_type']['type']):'-';
                    $data .='</td><td>';
                    $data .=isset($weekly_meal['get_product_details']['product_id'])?$weekly_meal['get_product_details']['product_id']:0;
                    $data .='</td><td>';
                    $data .= isset($weekly_meal['get_product_details']['product_name'])?ucwords($weekly_meal['get_product_details']['product_name']):'-';
                    $data .='</td><td>';
                    $data .= isset($weekly_meal['stock'])?$weekly_meal['stock']:0;
                    $data .='</td><td>';
                    $data .= '<input type="text" name="stock['.base64_encode($weekly_meal['id']).']" id="stock_'.$weekly_meal['id'].'" class="form-control" style="width: 300px" data-rule-digits="true" data-rule-min="0" value="'.$weekly_meal['stock'].'"></td></tr>';
                }
            }
        }   
        return $data;   
    }

    public function update_stock(Request $request)
    {
        $weekly_id    = $request->input('weekly_id');
        $daily_id     = base64_decode($request->input('enc_id'));
        $date         = $request->input('date');
        $stock        = $request->input('stock');
        $update = $stock_changed = $available_stock = '';

        if(isset($daily_id) && $daily_id !='')
        {
            $obj_meal_data = $this->BaseModel
                                  ->where('id',$daily_id)
                                  ->where('school_id',$this->school_id)
                                  ->where('date',$date)
                                  ->first();

            if (isset($obj_meal_data) && $obj_meal_data!= null) 
            {
                if($obj_meal_data->stock>$stock)
                {
                    $stock_changed = $obj_meal_data->stock-$stock;
                    $available_stock= $obj_meal_data->available_stock-$stock_changed;
                }
                elseif($obj_meal_data->stock<$stock)
                {
                    $stock_changed = $stock-$obj_meal_data->stock;
                    $available_stock= $obj_meal_data->available_stock+$stock_changed;
                }
                else
                {
                    $stock_changed = $stock;
                    $available_stock = $obj_meal_data->available_stock;
                }

                if($available_stock>=0)
                {
                    $update     =   $this->BaseModel
                                         ->where('id',$daily_id)
                                         ->update(['stock'=>$stock,'available_stock'=>$available_stock]);    
                }
                else
                {
                    return response()->json(array('status'=>'error','stock'=>$obj_meal_data->stock,'msg'=>translation('provide_valid_quantity_of').' '.translation('stock')));
                }

                if($update)
                {
                     $obj_meal_data = $this->BaseModel
                                      ->where('id',$daily_id)
                                      ->first();
                     return response()->json(array('status'=>'success','stock'=>$obj_meal_data->stock,'msg'=>translation('stock').' '.translation('updated_successfully')));
                }
                else
                {
                    $obj_meal_data = $this->BaseModel
                                      ->where('id',$daily_id)
                                      ->first();
                     return response()->json(array('status'=>'error','stock'=>$obj_meal_data->stock,'msg'=>translation('problem_occured_while_updating').' '.translation('stock')));
                }

                
            }

            
        }
    }

    public function add_stock(Request $request)
    {
        $date       = $request->input('date');
        $stock      = $request->input('stock');
        $weekly_id  = $request->input('weekly_id');

        $create = '';

        if(isset($weekly_id) && $weekly_id !='')
        {
            $arr_data['school_id']      =   $this->school_id;
            $arr_data['weekly_meal_id'] =   $weekly_id;
            $arr_data['date']           =   $date;
            $arr_data['stock']          =   $stock;
            $arr_data['available_stock']     =   $stock;

            $create =   $this->BaseModel->create($arr_data);

            if($create)
            {
                 return response()->json(array('status'=>'success','stock'=>$create->stock,'msg'=>translation('stock').' '.translation('added_successfully')));
            }
            else
            {
                 return response()->json(array('status'=>'error','stock'=>'0','msg'=>translation('problem_occured_while_adding').' '.translation('stock')));
            }
        }
    }
}