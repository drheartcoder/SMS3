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
use App\Models\UserTranslationModel;
use App\Models\NotificationModel;
use App\Models\NotificationSettingsModel;
use App\Models\ProfessorModel;
use App\Models\EmployeeModel;

use Datatables;
use Validator;
use Sentinel;
use Session;
use Flash;
use DB;

class StockDistributionController extends Controller
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
        $this->BaseModel                    = $this->StockDistributedModel;
        $this->LevelModel                   = new LevelModel();
        $this->LevelClassModel              = new LevelClassModel();
        $this->CommonDataService            = $CommonDataService;
        $this->UserTranslationModel         = new UserTranslationModel();
        $this->NotificationModel            = new NotificationModel();
        $this->NotificationSettingsModel    = new NotificationSettingsModel();
        $this->EmployeeModel                = new EmployeeModel();
        $this->ProfessorModel               = new ProfessorModel();

        $this->stock_product_base_img_path     = public_path().config('app.project.img_path.stock_products');
        $this->stock_product_public_img_path   = url('/').config('app.project.img_path.stock_products');
        
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')."/stock/stock_distribution");
        
        $this->LanguageService              = $langauge;
        $this->module_title                 = translation("stock_distribution");
        $this->module_view_folder           = "schooladmin.stock_distribution";
        $this->school_id                    = Session::has('school_id')?Session::get('school_id'):0;
        $this->academic_year                = Session::has('academic_year')?Session::get('academic_year'):0;
        
        $this->theme_color                  = theme_color();
        $this->module_icon                  = "fa fa-cubes";
        $this->create_icon                  = "fa fa-plus-circle";
        $this->edit_icon                    = "fa fa-edit";
        $this->view_icon                    = "fa fa-eye";

         $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;
        }
      
    }   
 
    /*-----Stock Distribution Module--------

    Authar : sayali B
    Date   : 26/06/2018
    
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
        $arr_academic_year = $arr_products = [];

        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 

        if($academic_year)
        {
            $arr_academic_year = explode(',',$academic_year);
        }

        $obj_products   =   $this->StockReceivedModel
                                 ->where('school_id',$this->school_id)
                                 ->where('available_stock','!=',0)
                                 ->whereIn('academic_year_id',$arr_academic_year)
                                 ->get();

        if(isset($obj_products) && $obj_products!=null)
        {
            $this->arr_view_data['arr_products'] = $obj_products->toArray();    
        }
        else
        {
            Flash::error(translation('no_records_available'));
            return redirect()->back();
        }

        $this->arr_view_data['page_title']      = translation("add").' '.str_singular($this->module_title);
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
        
        $arr_rules['user_type']             = "required|alpha";
        $arr_rules['product_name']          = "required|numeric";
        $arr_rules['user']                  = "required|numeric";
        $arr_rules['distributed_quantity']  = "required|numeric";
        $arr_rules['quantity']              = "required|numeric";
        $arr_rules['date']                  = "required|date";
        
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

        $arr_data['school_id']           = $school_id;
        $arr_data['product_id']          = trim($request->input('product_name'));
        $arr_data['user_role']           = trim($request->input('user_type'));
        $arr_data['user_id']             = trim($request->input('user'));
        $arr_data['quantity_distributed']= trim($request->input('distributed_quantity'));
        $arr_data['academic_year_id']    = $academic_year;
        $arr_data['distribution_date']   = trim($request->input('date'));
        $arr_data['level_id']            = 0;
        $arr_data['class_id']            = 0;       
    
        $create_product = $this->BaseModel->create($arr_data);
        $settings = [];
        if(trim($request->input('user_type')) == config('app.project.role_slug.professor_role_slug'))
        {
            $settings = $this->ProfessorModel->with('notifications')->where('user_id',trim($request->input('user')))->where('school_id',$this->school_id)->where('is_active',1)->where('has_left',0)->first();
        }
        if(trim($request->input('user_type')) == config('app.project.role_slug.employee_role_slug'))
        {
            $settings = $this->EmployeeModel->with('notifications')->where('user_id',trim($request->input('user')))->where('school_id',$this->school_id)->where('is_active',1)->where('has_left',0)->first();
        }
        $arr_settings = [];
        if(isset($settings) && count($settings)>0 && !is_null($settings))
        {
            $arr_settings = $settings->toArray();
        }

        $this->StockReceivedModel->where('id',$request->input('product_name'))->update(['available_stock'=>$request->input('quantity')]);
        
        if($create_product)
        {
            if(isset($arr_settings) && count($arr_settings)>0)
            {
                if(isset($arr_settings['notifications']['notification_permission']) && $arr_settings['notifications']['notification_permission']!=null)
                {
                    $obj_product = $this->BaseModel->with('get_stock')->where('id',$create_product->id)->first();
                    
                    $permissions = json_decode($arr_settings['notifications']['notification_permission'],true);
                    
                    if(array_key_exists('stock.app',$permissions))
                    {
                     
                        $arr_notification = [];
                        $arr_notification['school_id']          =  $this->school_id;
                        $arr_notification['from_user_id']       =  $this->user_id;
                        $arr_notification['to_user_id']         =  $arr_settings['user_id'];
                        $arr_notification['user_type']          =  config('app.project.role_slug.school_admin_role_slug');
                        $arr_notification['notification_type']  =  'Stock Distribution';
                        $arr_notification['title']              =  'Stock Distribution:School Admin distributed stock  of '.$obj_product['get_stock']['product_name'].' with quantity '.$obj_product->quantity_distributed.' to you';
                        $result = $this->NotificationModel->create($arr_notification);
                    }
                    elseif(array_key_exists('stock.sms',$permissions))
                    {

                    }
                    elseif (array_key_exists('stock.email',$permissions))
                    {
                        
                    }
                }
            }

            Flash::success(str_singular($this->module_title).' '.translation('added_successfully'));
            return redirect()->back();
        }
        else
        {
            Flash::error(translation('problem_occured_while_adding').' '.str_singular($this->module_title)); 
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

        $json_result     = $json_result->editColumn('product_name',function($data) 
                            { 
                                 
                                if($data->product_name!=null && $data->product_name!=''){

                                    return  ucwords($data->product_name);
                                }else{
                                    return  '-';
                                }

                            })
                            ->editColumn('date',function($data) 
                            { 
                                 
                                if($data->distribution_date!=null && $data->distribution_date!=''){

                                    return  getdateFormat($data->distribution_date);
                                }else{
                                    return  '-';
                                }

                            })
                            ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                            {
                                $build_delete_action ='';
                                       
                                    if(array_key_exists('stocks.delete', $arr_current_user_access))
                                    {
                                        $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                        $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                    }
                                return $build_delete_action;
                            })
                            ->editColumn('build_checkbox',function($data)use($arr_current_user_access){
                                $build_checkbox ='';
                                if(array_key_exists('stocks.delete', $arr_current_user_access))
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
        
        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }

        $stock_distribution_table           = $this->BaseModel->getTable();
        $prefixed_stock_distribution_table  = DB::getTablePrefix().$this->BaseModel->getTable();

        $stock_table                        = $this->StockReceivedModel->getTable();
        $prefixed_stock_table               = DB::getTablePrefix().$this->StockReceivedModel->getTable();

        $user_trans_table                   = $this->UserTranslationModel->getTable();
        $prefixed_user_trans_table          = DB::getTablePrefix().$this->UserTranslationModel->getTable();

        $obj_stock_items = DB::table($stock_distribution_table)
                                ->select(DB::raw($prefixed_stock_distribution_table.".id as id,".
                                                 $prefixed_stock_distribution_table.".product_id as product_id,".
                                                 $stock_table.".product_name as product_name,".
                                                 $stock_table.".quantity as quantity,".
                                                 $prefixed_stock_distribution_table.".distribution_date as distribution_date,".
                                                 $prefixed_stock_distribution_table.".quantity_distributed as quantity_distributed,".
                                                 $prefixed_stock_distribution_table.".user_role as user_role,".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
                                                          .$prefixed_user_trans_table.".last_name) as user_name"
                                                 )) 
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$stock_distribution_table.'.user_id')
                                ->join($stock_table,$stock_table.'.id', ' = ',$stock_distribution_table.'.product_id')
                                ->where($stock_distribution_table.'.school_id','=', $school_id)
                                ->where($stock_distribution_table.'.academic_year_id','=',$this->academic_year)
                                ->where($user_trans_table.'.locale','=', $locale)
                                ->whereNull($stock_distribution_table.'.deleted_at')
                                ->orderBy('id');
                                
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
        if(!is_numeric($id))
        {
             Flash::error(translation('something_went_wrong'));
             return redirect()->back();
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
        
        $arr_rules['user_type']             = "required|alpha";
        $arr_rules['product_name']          = "required|numeric";
        $arr_rules['user']                  = "required|numeric";
        $arr_rules['distributed_quantity']  = "required|numeric";
        $arr_rules['quantity']              = "required|numeric";
        $arr_rules['date']                  = "required|date";
        
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
        $arr_data['product_id']          = trim($request->input('product_id'));
        $arr_data['quantity']            = $request->input('quantity');
        $arr_data['product_name']        = trim($request->input('product_name'));
        $arr_data['total_price']         = trim($request->input('total_price'));
        $arr_data['price']               = trim($request->input('unit_price'));
        $arr_data['date_created']        = $request->input('date');
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

    public function view($enc_id)
    {
        
            $id = base64_decode($enc_id);   
        if(!is_numeric($id))
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

   
   /*
    | get_users() : get users list on role
    | Auther  : sayali B
    | Date    : 26-06-2018
    */
    public function get_users(Request $request)
    {
        $options = '';
        $users_data  = [];
        $role = $request->input('role');
        if($role == config('app.project.role_slug.employee_role_slug'))
        {
            $users_data = $this->CommonDataService->get_employees();
            if(isset($users_data) && count($users_data)>0)
            {
                $options .= "<option value= '' >".translation('select_user')."</option>";
                foreach($users_data as $data)
                {       
                        $options .= "<option value='".$data->user_id."'>".ucwords($data->user_name)." (".$data->national_id.")</option>"; 
                    
                }
            }
        }

        elseif($role == config('app.project.role_slug.professor_role_slug'))
        {
            $users_data = $this->CommonDataService->get_professor_by_year($this->school_id,$this->academic_year);  
            if(isset($users_data) && count($users_data)>0)
            {
                $options .= "<option value= '' >".translation('select_user')."</option>";
                foreach($users_data as $data)
                {
                        $options .= "<option value='".$data->user_id."'>".ucwords($data->user_name)." (".$data->national_id.")</option>"; 
                    
                }
            } 
        }        
        return $options;
    }

    public function get_quantity(Request $request)
    {
        $product_id = $request->input('product');

        $obj_quatity =  $this->StockReceivedModel
                             ->select('available_stock')
                             ->where('id',$product_id)
                             ->first();

        if(isset($obj_quatity) && $obj_quatity!=null)
        {
            return $obj_quatity->available_stock;
        }
    }
}