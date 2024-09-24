<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Common\Services\CommonDataService;
use App\Common\Traits\MultiActionTrait;

use App\Models\CanteenBookingsModel;
use App\Models\CanteenBookingDetailModel;
use App\Models\CanteenTransactionsModel;
use App\Models\TransactionDetailsModel;
use App\Models\UserModel;
use App\Models\UserTranslationModel;
use App\Models\UserRoleModel;
use App\Models\RoleModel;
use App\Models\CanteenProductsModel;
use App\Models\WeeklyMealsModel;
use App\Models\DailyMealsModel;
use App\Models\CartModel;

use Datatables;
use Validator;
use Sentinel;
use Session;
use Flash;
use File;
use Mail;
use DB;
use PDF;

class CanteenBookingsController extends Controller
{
    use MultiActionTrait
    {
        delete as module_delete ; 
    }

    public function __construct(CommonDataService $CommonDataService)
    {   
        $this->CommonDataService            = $CommonDataService;
        $this->CanteenBookingsModel         = new CanteenBookingsModel();
        $this->CanteenBookingDetailModel    = new CanteenBookingDetailModel();
        $this->CanteenTransactionsModel     = new CanteenTransactionsModel();
        $this->UserModel                    = new UserModel();
        $this->UserTranslationModel         = new UserTranslationModel();
        $this->CanteenProductsModel         = new CanteenProductsModel();
        $this->WeeklyMealsModel             = new WeeklyMealsModel();
        $this->DailyMealsModel              = new DailyMealsModel();
        $this->CartModel                    = new CartModel();
        $this->TransactionDetailsModel      = new TransactionDetailsModel();
        $this->BaseModel                    = $this->CanteenBookingsModel;
        $this->RoleModel                    = new RoleModel();
        $this->UserRoleModel                = new UserRoleModel();
        $this->module_url_path              = url(config('app.project.student_panel_slug')."/canteen_bookings");
        

        $this->module_title                 = translation("canteen_bookings");
        $this->module_view_folder           = "student.canteen_bookings";
        
        $this->school_id                    = Session::has('school_id')?Session::get('school_id'):0;
        $this->academic_year                = Session::has('academic_year')?Session::get('academic_year'):0;
        $this->user_no                      = Session::has('user_no')?Session::get('user_no'):0;
        
        $this->food_items_base_img_path     = public_path().config('app.project.img_path.food_items');
        $this->food_items_public_img_path   = url('/').config('app.project.img_path.food_items');

        $this->arr_view_data                = [];
        $this->arr_view_data['base_path']   = $this->food_items_base_img_path;
        $this->arr_view_data['img_path']    = $this->food_items_public_img_path;
        
        
        $this->theme_color                  = theme_color();
        $this->module_icon                  = "fa fa-apple";
        $this->create_icon                  = "fa fa-plus-circle";
        $this->edit_icon                    = "fa fa-edit";
        $this->view_icon                    = "fa fa-eye";


        $this->first_name   =   $this->last_name =  $this->user_id  =  $this->role  =  '';
        $obj_data                     =   Sentinel::getUser();
            
        if($obj_data)
        {
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->user_id           = $obj_data->id;
        }
    }   
 


    public function index(Request $request)
    {
        $date  = date('Y-m-d');
        $school_id    = $this->school_id;
        $data = $this->CartModel
                     ->with('get_product_details.get_product_type')
                     ->where('user_id',$this->user_id)
                     ->where('school_id',$school_id)
                     ->get();

        if(isset($data) && $data!=null)
        {
            $this->arr_view_data['data']          = $data->toArray();    
        }
        
        $this->arr_view_data['page_title']           = translation("manage").' '.str_singular($this->module_title);
        $this->arr_view_data['module_title']         = str_singular($this->module_title);
        $this->arr_view_data['module_url_path']      = $this->module_url_path;
        $this->arr_view_data['theme_color']          = $this->theme_color;
        $this->arr_view_data['module_icon']          = $this->module_icon;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    public function get_records(Request $request)
    {
        $arr_current_user_access =[];

        $role = Sentinel::findRoleBySlug(config('app.project.role_slug.student_role_slug'));
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;

        $obj_bookings    = $this->get_pre_bookings($request);

        $json_result     = Datatables::of($obj_bookings);

        $json_result     = $json_result->blacklist(['id']);

        $json_result =  $json_result->editColumn('quantity',function($data)
                        {
                            $quantity = 0;

                            if(isset($data->quantity) && sizeof($data->quantity)>0)
                            {
                                $quantity = $data->quantity;
                            }
                            return $quantity;
                        })
                        ->editColumn('order_type',function($data)
                        {
                            $order_type = "";

                            if(($data->order_type)== 'ONLINE')
                            {
                                $order_type = "<h5><span class='label label-info'><i class='fa fa-cart-plus'></i> ".translation('online')." </span></h5>"; 
                            }
                            elseif(($data->order_type)== 'CASH')
                            {
                           
                                $order_type = "<h5><span class='label label-success'><i class='fa fa-shopping-cart'></i> ".translation('cash')." </span></h5>";   
                            }

                            return $order_type;
                        })
                        ->editColumn('date',function($data)
                        {
                            if(isset($data->created_at) && sizeof($data->created_at)>0)
                            {
                                $date = date("Y-m-d",strtotime($data->created_at));
                            }
                            return $date;
                        })
                        ->editColumn('total_price',function($data)
                        {
                           return $data->total_price;
                        })
                       ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                            {
                                 $build_view_action = $build_delete_action ='';

                                $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                    $build_view_action = '<a href="'.$view_href.'" title="'.translation('view').'" class="green-color"><i class="fa fa-eye" ></i></a>';

                                if(array_key_exists('canteen_bookings.delete',$arr_current_user_access))
                                {
                                    if($data->payment_status == "DONE" && $data->delivery_status == "DELIVERED")
                                    {
                                        $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                        $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete()').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                    }
                                    else
                                    {
                                        $build_delete_action = '<a style="position: relative;" class="red-color" href="javascript:void(0)" title="'.translation('delete').'" ><i class="fa fa-trash" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>';
                                    }
                                }

                                return $build_view_action.'&nbsp;'.$build_delete_action;
                            })
                            ->editColumn('build_checkbox',function($data)use ($arr_current_user_access){
                                $build_checkbox = ""; 
                                if(array_key_exists('canteen_bookings.update',$arr_current_user_access) || array_key_exists('canteen_bookings.delete',$arr_current_user_access))
                                {
                                    if($data->payment_status == "DONE" && $data->delivery_status == "DELIVERED")
                                    {
                                        $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>';   
                                    }
                                    else
                                    {
                                        $build_checkbox = '<div> - </div>';
                                    }
                                } 
                                
                            return $build_checkbox;
                            })
                        ->editColumn('enc_id',function($data)
                        {
                            return  base64_encode(($data->id));
                        })
                        ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
        
    }

    function get_pre_bookings(Request $request,$fun_type='')
    {
        $school_id = $this->school_id;

        $locale    = '';
        
        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }

        /*------- Prefixed table name are required wherever we are using DB::raw calls -------------*/                       
        
        $booking_table                            = $this->BaseModel->getTable();
        $prefixed_booking_table                   = DB::getTablePrefix().$this->BaseModel->getTable();

        $canteen_booking_details_table              = $this->CanteenBookingDetailModel->getTable();
        $prefixed_canteen_booking_details_table     = DB::getTablePrefix().$this->CanteenBookingDetailModel->getTable();

        $user_table                               = $this->UserModel->getTable();
        $prefixed_user_table                      = DB::getTablePrefix().$this->UserModel->getTable();

        $user_trans_table                         = $this->UserTranslationModel->getTable();
        $prefixed_user_trans_table                = DB::getTablePrefix().$this->UserTranslationModel->getTable();

        $role_table                               = $this->RoleModel->getTable();
        $prefixed_role_table                      = DB::getTablePrefix().$this->RoleModel->getTable();

        $user_role_table                          = $this->UserRoleModel->getTable();
        $prefixed_user_role_table                 = DB::getTablePrefix().$this->UserRoleModel->getTable();

        $obj_pre_bookings = DB::table($booking_table)
                            ->select(DB::raw($prefixed_booking_table.".id as id,".
                                             $prefixed_booking_table.".created_at as created_at,".
                                             "CONCAT(".$prefixed_user_trans_table.".first_name,' ',".$prefixed_user_trans_table.".last_name) as customer_name,".
                                             $prefixed_booking_table.".total_price as total_price,".
                                             $prefixed_booking_table.".delivery_status as delivery_status,".
                                             $prefixed_booking_table.".payment_status as payment_status,".
                                             $prefixed_booking_table.".order_no as order_no,".
                                             $prefixed_booking_table.".order_type as order_type"
                                             )) 
                            ->where($booking_table.'.school_id','=', $school_id)
                            ->where($booking_table.'.customer_id','=',$this->user_id)
                            ->where($booking_table.'.academic_year_id','=', $this->academic_year)
                            ->whereNull($booking_table.'.deleted_at')
                            ->leftJoin($user_table,$user_table.'.id',' = ',$booking_table.'.customer_id')
                            ->leftJoin($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_table.'.id')
                            ->groupBy('id')
                            ->orderBy('id','DESC');
    

        if($fun_type=='export'){
            $search_term = $request->input('search');
        }else{
            $search = $request->input('search');
            $search_term = $search['value'];
        }
        if($request->has('search') && $search_term!="")
        {
            $obj_pre_bookings = $obj_pre_bookings ->WhereRaw("( (".$prefixed_booking_table.".order_no LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$prefixed_booking_table.".created_at LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$prefixed_booking_table.".total_price LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$prefixed_booking_table.".order_type LIKE '%".$search_term."%') ")
                                 ->orWhereRaw(" ( CONCAT(".$prefixed_user_trans_table.".first_name,'',".$prefixed_user_trans_table.".last_name)  LIKE  '%".$search_term."%' ) )");
                                 
                                 
        }
                                       
        
        if($fun_type=="export"){
            return $obj_pre_bookings->get();
        }else{

            return $obj_pre_bookings;
        }

    }

    public function view($enc_id)
    {

       $id        = base64_decode($enc_id);
        $school_id = $this->school_id;
        
        $obj_records = $this->BaseModel->where('id',$id)
                                       ->with('get_user_details','get_bookings_details.product_details')
                                       ->first();

        if(isset($obj_records) && $obj_records!=null)
        {
            $arr_records = $obj_records->toArray();
            // /dd($arr_records);
            $this->arr_view_data['arr_data']             = $arr_records;
        }
        
        $this->arr_view_data['page_title']           = translation("view").' '.str_singular($this->module_title);
        $this->arr_view_data['module_title']         = str_singular($this->module_title);
        $this->arr_view_data['module_url_path']      = $this->module_url_path;
        $this->arr_view_data['theme_color']          = $this->theme_color;
        $this->arr_view_data['module_icon']          = $this->module_icon;
        $this->arr_view_data['view_icon']            = $this->view_icon;

        return view($this->module_view_folder.'.view',$this->arr_view_data);
    }

    public function print_doc($enc_id)
    {
        $id        = base64_decode($enc_id);
        $school_id = $this->school_id;
        
        $obj_records = $this->BaseModel->where('id',$id)
                                       ->with('customer_user_details.translations', 
                                               'customer_user_details.student_details.level_details', 
                                               'customer_user_details.student_details.class_details', 
                                              'booking_details')
                                       ->first();

        if($obj_records)
        {
            $arr_records                                          = $obj_records->toArray();

            $arr_customer_translations = isset($arr_records['customer_user_details']['translations']) && is_array($arr_records['customer_user_details']['translations']) ? $arr_records['customer_user_details']['translations'] : [];
            
            $arr_records['customer_user_details']['translations'] = $this->arrange_locale_wise($arr_customer_translations);
        }

        $this->arr_view_data['arr_data']             = $arr_records;
        $this->arr_view_data['arr_lang']             = $this->LanguageService->get_all_language();
        $this->arr_view_data['page_title']           = translation("view").' '.str_singular($this->module_title);
        $this->arr_view_data['module_title']         = str_singular($this->module_title);
        $this->arr_view_data['module_url_path']      = $this->module_url_path;
        $this->arr_view_data['theme_color']          = $this->theme_color;
        $this->arr_view_data['module_icon']          = $this->module_icon;
        $this->arr_view_data['view_icon']            = $this->view_icon;
        $this->arr_view_data['student_panel_slug'] = $this->student_panel_slug;
        $this->arr_view_data['currency']             = $this->currency;

        return view($this->module_view_folder.'.print_doc',$this->arr_view_data);
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

    public function print_records(Request $request)
    {
        $school_id = $this->school_id;
        
        $locale    = '';
        
        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }

        $user          = $request->input('q_user');
        $rf_id         = $request->input('q_date');
        $customer_name = $request->input('q_customer_name');
        $bill_total    = $request->input('q_bill_total');
        $order_type    = $request->input('q_order_type');

        if(isset($user) && sizeof($user)>0)
        {
            $user_role = $this->RoleModel->where('slug','=',$user)->select('id')->first(); 

            if($user_role)
            {
                $user_role_id = $user_role->toArray();
            }    
        }
            
        /*------- Prefixed table name are required wherever we are using DB::raw calls -------------*/                       
        
        $booking_table                            = $this->BaseModel->getTable();
        $prefixed_booking_table                   = DB::getTablePrefix().$this->BaseModel->getTable();

        $cafeteria_booking_details_table          = $this->CafeteriaBookingDetailsModel->getTable();
        $prefixed_cafeteria_booking_details_table = DB::getTablePrefix().$this->CafeteriaBookingDetailsModel->getTable();

        $user_table                               = $this->UserModel->getTable();
        $prefixed_user_table                      = DB::getTablePrefix().$this->UserModel->getTable();

        $user_trans_table                         = $this->UserTranslationModel->getTable();
        $prefixed_user_trans_table                = DB::getTablePrefix().$this->UserTranslationModel->getTable();

        $role_table                               = $this->RoleModel->getTable();
        $prefixed_role_table                      = DB::getTablePrefix().$this->RoleModel->getTable();

        $user_role_table                          = $this->UserRoleModel->getTable();
        $prefixed_user_role_table                 = DB::getTablePrefix().$this->UserRoleModel->getTable();

        $obj_pre_bookings = DB::table($booking_table)
                            ->select(DB::raw($prefixed_booking_table.".id as id,".
                                             $prefixed_booking_table.".created_at as created_at,".
                                             "CONCAT(".$prefixed_user_trans_table.".first_name,' ',".$prefixed_user_trans_table.".second_name,' ',".$prefixed_user_trans_table.".third_name,' ',".$prefixed_user_trans_table.".family_name) as customer_name,".
                                             $prefixed_booking_table.".bill_total as bill_total,".
                                             $prefixed_booking_table.".status as status"
                                             )) 
                            ->where($booking_table.'.school_id','=', $school_id)
                            ->where($booking_table.'.order_type','=', 'PREORDER')
                            ->whereNull($booking_table.'.deleted_at')
                            ->orderBy('created_at','DECS')
                            ->where($prefixed_user_role_table.'.role_id','=',$user_role_id)
                            ->where($prefixed_user_trans_table.'.locale', '=', $locale)
                            ->leftJoin($user_table,$user_table.'.id',' = ',$booking_table.'.customer_id')
                            ->leftJoin($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_table.'.id')
                            ->leftJoin($user_role_table,$booking_table.'.customer_id',' = ', $user_role_table.'.user_id');                      
        
        /* ---------------- Filtering Logic ----------------------------------*/
        
        if(isset($arr_search_column['q_date']) && $arr_search_column['q_date']!="")
        {
            $search_term      = $arr_search_column['q_date'];
            
            $obj_pre_bookings = $obj_pre_bookings->where($booking_table.'.rf_id','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['q_customer_name']) && $arr_search_column['q_customer_name']!="")
        {
            $search_term      = $arr_search_column['q_customer_name'];
            
            $obj_pre_bookings = $obj_pre_bookings->where(function($query) use($user_trans_table,$search_term)
            {    
                            return $query->where($user_trans_table.'.first_name','LIKE', '%'.$search_term.'%')
                                         ->orWhere($user_trans_table.'.second_name','LIKE', '%'.$search_term.'%')
                                         ->orWhere($user_trans_table.'.third_name','LIKE', '%'.$search_term.'%')
                                         ->orWhere($user_trans_table.'.family_name','LIKE', '%'.$search_term.'%');
            });      
        }

        if(isset($arr_search_column['q_bill_total']) && $arr_search_column['q_bill_total']!="")
        {
            $search_term      = $arr_search_column['q_bill_total'];

            $obj_pre_bookings = $obj_pre_bookings->where($booking_table.'.bill_total','LIKE', '%'.$search_term.'%');
            
        }

        if($obj_pre_bookings)
        {
            $arr_print_record = $obj_pre_bookings->get();
        }

        $this->arr_view_data['arr_data']        = $arr_print_record;
        $this->arr_view_data['page_title']      = translation("print").' '.str_singular($this->module_title);
        $this->arr_view_data['module_title']    = str_singular($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['view_icon']       = $this->view_icon;

        return view($this->module_view_folder.'.print_records',$this->arr_view_data);

    }

    public function status($enc_id)
    {
        $id = base64_decode($enc_id);
        
        $obj_records = $this->BaseModel->where('id',$id)
                                       ->first();

        if($obj_records)
        {
            $obj_records->status = "DONE";
            
            $records=$obj_records->save();

            if($records)
            {
                Flash::success($this->module_title.' '.translation('diliverd_successfully'));
            }
            else
            {
                Flash::success($this->module_title.' '.translation('diliverd_successfully'));
            }            
        }
        return redirect()->back();
    }

    public function create()
    {
        $date = Date('Y-m-d');
        $daily_meals_data = $this->DailyMealsModel
                                 ->with('weekly_meal.get_product_details.get_product_type','cart_details')
                                 ->where('date',$date)
                                 ->where('school_id',$this->school_id)
                                 ->get();

        if(isset($daily_meals_data) && $daily_meals_data != null)
        {
            $this->arr_view_data['arr_daily_meal']    = $daily_meals_data->toArray();    
        }
        $data = $this->CartModel
                     ->with('get_product_details.get_product_type')
                     ->where('user_id',$this->user_id)
                     ->where('school_id',$this->school_id)
                     ->where('date',$date)
                     ->get();

        if(isset($data) && $data != null)
        {
            $this->arr_view_data['arr_cart_data']    = $data->toArray();       
        }


        $this->arr_view_data['page_title']      = translation("add").' '.str_singular($this->module_title);
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['create_icon']     = $this->create_icon;

        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    public function add_to_cart(Request $request)
    {
        $record     =   [];
        $date       =   Date('Y-m-d');
        $school_id  =   $this->school_id;
        $product_id =   $request->input('item_id');
        $user_id    =   $this->user_id;
        $price      =   $request->input('item_price');
        $daily_id   =   $request->input('daily_id');

        $cart_data  =   $this->CartModel
                             ->with('get_daily_meals')
                             ->where('school_id',$school_id)
                             ->where('date',$date)
                             ->where('user_id',$user_id)
                             ->where('product_id',$product_id)
                             ->first();
        
        if(isset($cart_data) && $cart_data != null)
        {

            $total           =   $cart_data->price + $price;
            $quantity        =   $cart_data->quantity + $request->input('item_qty');
            $available_stock =  $cart_data['get_daily_meals']['available_stock'];

            if($quantity > $available_stock || $quantity == 0)
            {
               return $record = ['status' =>'error','stock' =>$available_stock,'price'=>$cart_data->price];
            }
            else
            {
                $this->CartModel->where('id',$cart_data->id)->update(['price'=>$total,'quantity'=>$quantity]);    
            }
            
        }
        else
        {
            $arr_data = [];

            $arr_data['product_id']     =   $product_id;
            $arr_data['daily_meal_id']  =   $daily_id;
            $arr_data['school_id']      =   $school_id;
            $arr_data['date']           =   $date;
            $arr_data['price']          =   $price;
            $arr_data['quantity']       =   $request->input('item_qty');
            $arr_data['user_id']        =   $user_id;

            $create = $this->CartModel->create($arr_data);
        }

        $record = $this->cart_model($date,$user_id);

        return $record;
    }

    public function cart_model($date,$user_id)
    {
        $record = '';
        $total  = 0;
        $data = $this->CartModel
                     ->with('get_product_details.get_product_type')
                     ->where('date',$date)
                     ->where('user_id',$user_id)
                     ->where('school_id',$this->school_id)
                     ->get();

        if(isset($data) && $data != null)
        {
            foreach ($data as $key => $data) {
                $total = $total + $data->price;
                $record .=  '<div class="content-list-li">';
                $record .=  '<div class="img-content-prnt">';
                if(isset($data['get_product_details']['product_image']) && $data['get_product_details']['product_image'] != '' && file_exists($this->food_items_base_img_path.$data['get_product_details']['product_image']))
                {
                    $image_path = url('/').'/uploads/food_products/'.$data['get_product_details']['product_image'];
                    $record .=  '<img src="'.$image_path.'" alt="" />';
                }
                else
                {
                    $record .=  '<img src="'.url('/').'/images/default-old.png" alt="" />';   
                }
                $record .=  '</div><div class="txts-content-prnt">';
                $record .=  '<div class="product-title-listname">';
                $record .=  isset($data['get_product_details']['product_name'])?ucwords($data['get_product_details']['product_name']):'';
                $record .=  '</div><div class="main-prices"><div class="totl-pric-nm">';
                $record .=  translation('unit_price');
                $record .=  '</div><div class="price-tlt">';
                $record .=  isset($data['get_product_details']['price'])?$data['get_product_details']['price']:0;
                $record .=  ' MAD</div></div><div class="main-prices">';
                $record .=  '<div class="totl-pric-nm">';
                $record .=  translation('total_price');
                $record .=  '</div><div class="price-tlt" id="total_'.$data['id'].'">';
                $record .=  isset($data['price'])?$data['price']:0;
                $record .=  ' MAD</div></div><div class="parent-qnty-info">';
                $record .=  '<button class="guest-btn btn-plus-guest"  onClick="updateRecord(this,'.$data['id'].',\'increment\','.$data['daily_meal_id'].');"  type="button" ><i class="fa fa-plus" aria-hidden="true"></i></button>';
               
                $record .=  '<input class="guest-input" value="';
                $record .=  isset($data['quantity'])?$data['quantity']:0;
                $record .=  '" type="text" disabled id="quantity_'.$data['id'].'">';
                $record .=  '<button class="guest-btn btn-minus-guest" onClick="updateRecord(this,'.$data['id'].',\'decrement\','.$data['daily_meal_id'].');" type="button" ><i class="fa fa-minus" aria-hidden="true"></i></button></div><div class="close-btnsadd"><a href="javascript:void(0)" class="closebtn-cteen" onClick="deleteRecord('.$data['id'].');"></a></div></div></div></div>';

            }

        }
        else
        {
            $record .= '<div><h4 style="color:red;text-align: center" >'.translation('no_items_available_in_cart').'</h4></div>';
        }

        $data_record['status'] = 'success';
        $data_record['record'] = $record;
        $data_record['total']  = number_format($total,2);
        return $data_record; 
    }

    /*
    | update_quantity() : update cart item(increase or decrease cart items quantity)
    | Auther  : sayali B
    | Date    : 20-06-2018
    */

    public function update_quantity(Request $request)
    {
        $id         = $request->input('id');
        $status     = $request->input('status');
        $date       = Date('Y-m-d');
        $school_id  = $this->school_id;
        $user_id    = $this->user_id;
        $total_amt  = $request->input('total_amt');

        $data   =   $this->CartModel
                         ->with('get_product_details')
                         ->where('id',$id) 
                         ->first();

        $available = $total = $total_amount = 0;
        if(isset($data) && $data != null)
        {
            if($status == 'increment')
            {   
                
                $stock_details  =   $this->DailyMealsModel->where('id',$data->daily_meal_id)->first();
                if(isset($stock_details) && $stock_details != null)
                {
                    $available = $stock_details->available_stock;
                    $total     = $data->price + $data->get_product_details->price;
                }
                $quantity = $data->quantity + 1;
                
                if($quantity <= $available)
                {
                    $arr_data['quantity'] = $quantity;
                    $arr_data['price']    = $total;
                    $total_amount = $total_amt + $data->get_product_details->price;
                    $update  = $this->CartModel->where('id',$id)->update($arr_data);

                    return response()->json(array('status'=>'success','data'=>$quantity,'price'=>$total,'total_price'=>number_format($total_amount,2)));
                }
                else
                {
                    return response()->json(array('status'=>'error','data'=>$data->quantity,'price'=>$data->price,'total_price'=>number_format($total_amt,2)));
                }
            }
            if($status == 'decrement')
            {
                $total     = $data->price - $data->get_product_details->price;
                $quantity = $data->quantity - 1;

                if($quantity > 0)
                {
                    $arr_data['quantity'] = $quantity;
                    $arr_data['price']    = $total;
                    $total_amount = $total_amt - $data->get_product_details->price;
                    $update  = $this->CartModel->where('id',$id)->update($arr_data);       
                    return response()->json(array('status'=>'success','data'=>$quantity,'price'=>$total,'total_price'=>number_format($total_amount,2)));
                }
                else
                {
                    return response()->json(array('status'=>'error','data'=>$data->quantity,'price'=>$data->price,'total_price'=>number_format($total_amt,2)));
                }
            }

        }

    }

    /*
    | delete_quantity() : delete cart item
    | Auther  : sayali B
    | Date    : 20-06-2018
    */
    public function delete_quantity(Request $request)
    {

        $id             = $request->input('id');
        $date           = Date('Y-m-d');
        $user_id        = $this->user_id;
        $delete_data    = $this->CartModel->where('id',$id)->delete();
        
        $record         = $this->cart_model($date,$user_id);
        return $record;
    }

     /*
    | checkout() : checkout page
    | Auther  : sayali B
    | Date    : 21-06-2018
    */
    public function checkout(Request $request)
    {
        $arr_data = $arr_daily_meal = $daily_ids = $stocks = $products = [];
        $date = Date('Y-m-d');
        $data = $this->CartModel
                     ->with('get_product_details.get_product_type')
                     ->where('user_id',$this->user_id)
                     ->where('school_id',$this->school_id)
                     ->where('date',$date)
                     ->get();

        if(isset($data) && $data !=null)
        {
            $arr_data = $data->toArray();
            $this->arr_view_data['arr_data']        = $data->toArray();
        }
       
        if(isset($arr_data) && count($arr_data)>0)
        {
            foreach ($arr_data as $key => $data) {
                $daily_meal = $this->DailyMealsModel->with('weekly_meal.get_product_details')->where('id',$data['daily_meal_id'])->first();
                if(isset($daily_meal) && $daily_meal != null)
                {
                    $arr_daily_meal = $daily_meal->toArray();
                    if($data['quantity']>$daily_meal['available_stock'] || $daily_meal['available_stock']<=0)
                    {
                        Flash::error(translation('no_stock_available_for').' '.$daily_meal['weekly_meal']['get_product_details']['product_name']);
                        return redirect()->back();
                    }
                }
            }
        }

        $this->arr_view_data['page_title']      = translation("your_order").'-'.translation("checkout");
        $this->arr_view_data['module_title']    = str_singular($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['view_icon']       = $this->view_icon;
        return view($this->module_view_folder.'.checkout',$this->arr_view_data);
      
    }

    /*
    | store() : store booking & transactions
    | Auther  : sayali B
    | Date    : 21-06-2018
    */
    public function store(Request $request)
    {
            $create_booking = '';
            $date       =  Date('Y-m-d');
            $user_id    =  $this->user_id;
            $selector   =  $request->input('selector');

            $total      =  floatval($request->input('total'));
            $arr_data   =  [];
            $order_no   =  $this->generate_order_no();

            $user       =  $this->UserModel->where('id',$user_id)->first();

            $cart_details = $this->CartModel->with('get_product_details')->where('user_id',$user_id)->where('date',$date)->get();
            if(isset($cart_details) && $cart_details != null)
            {

                $arr_details = $cart_details->toArray();
               
                foreach ($arr_details as $key => $details) 
                {
                    
                    $data1 = $this->DailyMealsModel->with('weekly_meal.get_product_details')->where('id',$details['daily_meal_id'])->first();

                    if(isset($data1) && count($data1)>0)
                    {
                        $arr_data = $data1->toArray();

                        if($arr_data['available_stock'] > 0)
                        {
                            $available = $arr_data['available_stock'] - $details['quantity'];
                            $update_data = '';
                            if($available<0)
                            {
                                Flash::error(translation('kindly_decrease_quantity_of').' '.$arr_data['weekly_meal']['get_product_details']['product_name']);
                                return redirect($this->module_url_path.'/create');
                            } 
                        } 
                        else
                        {
                            Flash::error(translation('no_stock_available_for').' '.$arr_data['weekly_meal']['get_product_details']['product_name'].' '.translation('please_remove_product_from_cart'));
                            return redirect($this->module_url_path.'/create');
                        }
                    }  
                                                

                }
            }   
                 /*if($update_data);
                                {*/
            $arr_data['school_id']              =   $this->school_id;
            $arr_data['customer_id']            =   $this->user_id;
            $arr_data['staff_id']               =   0;
            $arr_data['user_role']              =   'student';
            $arr_data['order_no']               =   $order_no;
            $arr_data['order_type']             =   'CASH';
            $arr_data['booking_date']           =   $date;
            $arr_data['total_price']            =   $total;
            $arr_data['payment_status']         =   'PENDING';
            $arr_data['customer_national_id']   =   $user->national_id;
            $arr_data['academic_year_id']       =   $this->academic_year;

            $create_booking = $this->BaseModel->create($arr_data);

            if($create_booking)
            {
                if(isset($cart_details) && $cart_details != null)
                {
                    $arr_details = $cart_details->toArray();
               
                    foreach ($arr_details as $key => $details) 
                    {
                        $data1 = $this->DailyMealsModel->with('weekly_meal.get_product_details')->where('id',$details['daily_meal_id'])->first();

                        if(isset($data1) && count($data1)>0)
                        {
                            $arr_data = $data1->toArray();
                            if($arr_data['available_stock'] > 0)
                            {
                                $data['booking_id']     =   $create_booking->id;
                                $data['item_id']        =   isset($details['product_id'])?$details['product_id']:0;
                                $data['item_name']      =   isset($details['get_product_details']['product_name'])?$details['get_product_details']['product_name']:'';
                                $data['price']          =   isset($details['price'])?$details['price']:'';
                                $data['quantity']       =   isset($details['quantity'])?$details['quantity']:'';
                                
                                $create_details  = $this->CanteenBookingDetailModel->create($data);
                                $available = $arr_data['available_stock'] - $details['quantity'];
                                if($available>=0)
                                {
                                    $record['available_stock'] = $available;
                                    $this->DailyMealsModel->where('id',$details['daily_meal_id'])->update($record);    
                                }
                            }
                        }
                        
                    }
                }

                $transaction=  '';

                if($selector == 'cash')
                {
                    $arr_translation = [];
                    $arr_translation['cust_id']         = $user_id;
                    $arr_translation['staff_id']        = 0;
                    $arr_translation['amount']          = $total;
                    $arr_translation['paid_date']       = $date;
                    $arr_translation['order_no']        = $order_no;
                    $arr_translation['order_type']      = 'CASH';

                    $transaction = $this->CanteenTransactionsModel->create($arr_translation);

                    if($transaction)
                    {
                
                            $arr_transaction_details ['amount']           = $total;
                            $arr_transaction_details ['order_no']         = $order_no;
                            $arr_transaction_details ['transaction_type'] = strtoupper($selector);
                            $arr_transaction_details ['payment_done_by']  = $user_id;
                            $arr_transaction_details ['payment_date']     = $date;
                            $arr_transaction_details ['approval_status']  = 'PENDING';
                            $arr_transaction_details ['school_id']        = $this->school_id;
                            $arr_transaction_details ['academic_year_id'] = $this->academic_year;
                            $arr_transaction_details ['user_no']          = $this->user_no;

                            $obj_transaction = $this->TransactionDetailsModel->create($arr_transaction_details);
                            $transaction_id = $obj_transaction->id;

                            $this->CartModel->where('date',$date)->where('user_id',$user_id)->where('school_id',$this->school_id)->delete();
                    }
                    else
                    {
                        Flash::error(translation('problem_occured_while').' '.$this->module_title);
                    }
                }
                Flash::success($this->module_title.' '.translation('done_successfully'));

            }
            else
            {
                Flash::error(translation('problem_occured_while').' '.$this->module_title);
            }
            return redirect($this->module_url_path.'/create');
    }

    /*
    | generate_order_no() : generate order no 
    | Auther  : sayali B
    | Date    : 22-06-2018
    */
    public function generate_order_no()
    {

        $today       = date("Ymd");
        $rand        = sprintf("%04d", rand(0,9999));
        $order_no    = 'ORD'. $today . $rand;
        
        $count = $this->BaseModel->where('order_no',$order_no)->count();
        if($count>0)
        {
            return $this->generate_order_no();
        }
        else
        {
            return $order_no;
        }
    }


    public function delete($enc_id)
    {
        $id     =   base64_decode($enc_id);

        $data = $this->BaseModel->with('get_bookings_details')->where('id',$id)->first();
        if(isset($data) && $data != null)
        {
            $arr_details = $data->toArray();
            if(isset($arr_details['get_bookings_details']) && count($arr_details['get_bookings_details'])>0)
            {
                foreach ($arr_details['get_bookings_details'] as $key => $detail) 
                {
                    $this->CanteenBookingDetailModel->where('id',$detail['id'])->delete();
                }
            }
            return $this->module_delete($enc_id);
        }
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
                                       ->with('get_bookings_details')
                                       ->first(); 
        if($obj_details)
        {
            $arr_details = $obj_details->toArray();

            if(isset($arr_details['get_bookings_details']) && sizeof($arr_details['get_bookings_details'])>0)
            {
                foreach ($arr_details['get_bookings_details'] as $key => $booking)
                {
                            $delete_daily_meals = $this->CanteenBookingDetailModel->where('id',$booking['id'])->delete();    
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
    
    /*
    | export() : Export List
    | Auther  : Padmashri
    | Date    : 14-12-2018
    */
    public function export(Request $request)
    {       
            $file_type = config('app.project.export_file_formate');

            $obj_data = $this->get_pre_bookings($request,'export');
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
                            $arr_fields['id']           = translation('sr_no');;
                            $arr_fields['order_no']     = translation('order_no');
                            $arr_fields['date']         = translation('date');
                            $arr_fields['total_amount'] = translation('total_amount').' MAD';
                            $arr_fields['order_type']   = translation('order_type');
                                
                            
                            
                            $sheet->row(2, ['',ucwords($this->module_title).' - '.date('d M Y'),'','','']);
                            $sheet->row(4, $arr_fields);

                            // To set Colomn head
                            $j = 'A'; $k = '4';
                            $totalHead = 4;
                            for($i=0; $i<=$totalHead;$i++)
                            {
                                $sheet->cell($j.$k, function($cells) {
                                    $cells->setBackground('#495b79');
                                    $cells->setFontWeight('bold');
                                    $cells->setAlignment('center');
                                    $cells->setFontColor('#ffffff');
                                });
                                $j++;
                            }
                            $sheet->setColumnFormat([
                                'D' => "#",
                            ]);


                            
                            if(sizeof($obj_data)>0) 
                            {
                                
                                $arr_tmp = [];
                                foreach($obj_data as $key => $result)
                                {   

                                    $order_type = '-';
                                    if(($result->order_type)== 'ONLINE')
                                    {
                                        $order_type = translation('online'); 
                                    }
                                    elseif(($result->order_type)== 'CASH')
                                    {
                                        $order_type = translation('cash'); 
                                    }

                                    $created_at = isset($result->created_at) && sizeof($result->created_at)>0?date("Y-m-d",strtotime($result->created_at)):'-';
                                    $arr_tmp[$key]['id']           = intval($key+1);
                                    $arr_tmp[$key]['order_no']     = $result->order_no;
                                    $arr_tmp[$key]['date']         = $created_at;
                                    $arr_tmp[$key]['total_amount'] = $result->total_price;
                                    $arr_tmp[$key]['order_type']   = $order_type;
                                    
                                    
                                }
                                $sheet->rows($arr_tmp);
                               
                            }
                        });
                    })->export($file_type);     
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
