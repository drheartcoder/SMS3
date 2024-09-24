<?php
namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Common\Traits\MultiActionTrait;
use App\Models\CanteenProductsModel;
use App\Models\CanteenProductTypesModel;
use App\Models\WeeklyMealsModel;
use App\Models\DailyMealsModel;
use Datatables;
use Validator;
use Sentinel;
use Session;
use Flash;
use File;
use Mail;
use DB;

class WeeklyMealsController extends Controller
{
    use MultiActionTrait
    {
        delete as module_delete;
        multi_action as module_multiaction_delete;
    }

    public function __construct()
    {
        $this->CanteenProductTypesModel = new CanteenProductTypesModel();
        $this->CanteenProductsModel = new CanteenProductsModel();
        $this->WeeklyMealsModel = new WeeklyMealsModel();
        $this->DailyMealsModel = new DailyMealsModel();
        $this->BaseModel = $this->WeeklyMealsModel;
        
        $this->food_items_base_img_path = public_path() . config('app.project.img_path.food_items');
        $this->food_items_public_img_path = url('/') . config('app.project.img_path.food_items');

        $this->arr_view_data = [];
        $this->arr_view_data['base_path'] = $this->food_items_base_img_path;
        $this->arr_view_data['img_path'] = $this->food_items_public_img_path;

        $this->module_url_path = url(config('app.project.school_admin_panel_slug') . "/weekly_meals");
        $this->module_title = translation("weekly_meals");
        $this->module_view_folder = "schooladmin.weekly_meals";
        $this->school_id = Session::has('school_id') ? Session::get('school_id') : 0;
        $this->arr_view_data = [];

        $this->theme_color = theme_color();
        $this->module_icon = "fa-calendar-check-o";
        $this->create_icon = "fa-plus-circle";
        $this->edit_icon = "fa-edit";
        $this->view_icon = "fa-eye";

        /*----------------------------------------------------------------------------*/
    }

    /*-----Weekly Meal Module--------
    
    Authar : Sayali B
    Date   : 13/06/2018    
    
    ---------------------------*/

    public function index(Request $request)
    {
        $school_id = $this->school_id;
        $arr_days = config('app.project.week_days');
        if (isset($arr_days) && count($arr_days) > 0)
        {
            $this->arr_view_data['arr_days'] = $arr_days;
        }

        $this->arr_view_data['page_title'] = translation("manage") . ' ' . str_singular($this->module_title);
        $this->arr_view_data['module_title'] = str_singular($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;

        return view($this->module_view_folder . '.index', $this->arr_view_data);
    }

    public function create()
    {
        $school_id = $this->school_id;

        $arr_days = config('app.project.week_days');
        if (isset($arr_days) && count($arr_days) > 0)
        {
            $this->arr_view_data['arr_days'] = $arr_days;
        }

        $obj_canteen_products = $this
            ->CanteenProductsModel
            ->with('get_product_type')
            ->where('school_id', $school_id)->get();

        if ($obj_canteen_products)
        {
            $arr_canteen_products = $obj_canteen_products->toArray();
            $this->arr_view_data['arr_canteen_products'] = $arr_canteen_products;
        }

        $this->arr_view_data['page_title'] = translation("create") . ' ' . str_singular($this->module_title);
        $this->arr_view_data['module_title'] = str_singular($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color'] = $this->theme_color;
        $this->arr_view_data['module_icon'] = $this->module_icon;
        $this->arr_view_data['create_icon'] = $this->create_icon;

        return view($this->module_view_folder . '.create', $this->arr_view_data);
    }

    public function store(Request $request)
    {
        /*--------------------School_id---------------------*/

        $school_id = $this->school_id;

        /*------------------------------------------------*/

        $arr_rules['day'] = "required";
        $arr_rules['checked_record'] = "required";
        $arr_rules['stock'] = "required";

        $validator = Validator::make($request->all() , $arr_rules);

        if ($validator->fails())
        {
            Flash::error(translation('please_select_the_canteen_product_which_you_want_to_add_as_a') . ' ' . str_singular($this->module_title));
            return redirect()
                ->back()
                ->withErrors($validator)->withInput($request->all());
        }

        /*----------------------------------------------------------------------------*/
        $day = $request->input('day');
        $stock = $request->input('stock');
        $checked_record = $request->input('checked_record');

        $existance = $this
            ->BaseModel
            ->where('school_id', $school_id)->where('week_day', $day)->get();

        $arr_exist_id = $arr_existance = [];
        if (isset($existance) && $existance != null)
        {
            $arr_existance = $existance->toArray();
        }

        foreach ($checked_record as $key_check_record => $value_checked_record)
        {

            $arr_data['school_id'] = $school_id;
            $arr_data['week_day'] = $day;
            $menu_id = $checked_record[$key_check_record];
            $arr_data['item_id'] = base64_decode($menu_id); /*here item_id is primary key of tbl_canteen_items*/
            $arr_data['stock'] = $stock[$menu_id];
            array_push($arr_exist_id, base64_decode($menu_id));

            $does_exists = $this
                ->BaseModel
                ->where('school_id', $school_id)->where('week_day', $arr_data['week_day'])->where('item_id', $arr_data['item_id'])->first();

            if ($does_exists)
            {
                $id = $does_exists->id;

                $create_weekly_meals = $this
                    ->BaseModel
                    ->where('id', $id)->where('school_id', $school_id)->where('week_day', $arr_data['week_day'])->where('item_id', $arr_data['item_id'])->update($arr_data);
            }
            else
            {
                $create_weekly_meals = $this
                    ->BaseModel
                    ->create($arr_data);
            }
        }

        /*  foreach($arr_existance as $key => $value)
        {
            if(!in_array($value['item_id'],$arr_exist_id))
            {
        
                $delete_meal = $this->BaseModel->where('school_id',$school_id)
                                               ->where('week_day',$day)
                                               ->where('item_id',$value['item_id'])
                                               ->delete();  
            }
        }*/

        /*------------- Check if Data already exists ---------------------------------------- */

        if (sizeof($create_weekly_meals) > 0)
        {
            Flash::success(str_singular($this->module_title) . ' ' . translation('added_successfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_creating') . ' ' . str_singular($this->module_title));
        }

        return redirect()
            ->back();
    }

    public function get_records(Request $request)
    {
        $role = Sentinel::findRoleBySlug(config('app.project.role_slug.school_admin_role_slug'));
        $arr_current_user_access = isset($role->permissions) && ($role->permissions != '') ? $role->permissions : [];

        $obj_courses = $this->get_weekly_meals($request);

        $json_result = Datatables::of($obj_courses);

        $json_result = $json_result->blacklist(['id']);

        $json_result = $json_result->editColumn('quantity', function ($data)
        {
            $quantity = 0;
            if (isset($data->quantity) && sizeof($data->quantity) > 0)
            {
                return '<input type="text" name="stock[' . base64_encode($data->id) . ']" id="stock_' . $data->id . '" class="form-control" style="width: 200px" data-rule-number="true" data-rule-min="0" placeholder="0" value="' . $data->quantity . '" onChange="updateQuantity(this)">';
            }
            else
            {
                return '-';
            }
        })->editColumn('build_action_btn', function ($data) use ($role, $arr_current_user_access)
        {
            if (array_key_exists('canteen.delete', $arr_current_user_access))
            {
                $delete_href = $this->module_url_path . '/delete/' . base64_encode($data->id);
                $build_delete_action = '<a class="red-color" href="' . $delete_href . '" title="' . translation('delete') . '" onclick="return confirm_action(this,event,\'' . translation('do_you_really_want_to_delete_this_record') . ' ?\',\'' . translation("are_you_sure") . '\',\'' . translation('yes') . ' \',\'' . translation("no") . '\')" disabled=""><i class="fa fa-trash" ></i></a>';
            }
            return $build_delete_action;
        })->editColumn('build_checkbox', function ($data) use ($arr_current_user_access)
        {
            $build_checkbox = '';
            if (array_key_exists('canteen.update', $arr_current_user_access) || array_key_exists('canteen.delete', $arr_current_user_access))
            {
                $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_' . base64_encode($data->id) . '" value="' . base64_encode($data->id) . '" /><label for="mult_change_' . base64_encode($data->id) . '"></label></div>';
            }
            return $build_checkbox;
        })->editColumn('enc_id', function ($data)
        {
            return base64_encode(($data->id));
        })->editColumn('price', function ($data)
        {
            if (isset($data->price) && $data->price != '')
            {
                return $data->price;
            }
            else
            {
                return '-';
            }
        })->editColumn('type', function ($data)
        {
            if (isset($data->type) && $data->type != '')
            {
                return ucwords($data->type);
            }
            else
            {
                return '-';
            }
        })->editColumn('product_name', function ($data)
        {
            if (isset($data->product_name) && $data->product_name != '')
            {
                return ucwords($data->product_name);
            }
            else
            {
                return '-';
            }
        })
->editColumn('product_image', function ($data)
        {
            if (isset($data->product_image) && $data->product_image != '' && file_exists($this->food_items_base_img_path . $data->product_image))
            {
                return '<img src="' . $this->food_items_public_img_path . '/' . $data->product_image . '" height="40" width="40">';

            }
            else
            {
                return '<img src="' . url('/') . '/images/default_food.jpg" height="40" width="40">';
            }
        })
            ->make(true);

        $build_result = $json_result->getData();

        return response()
            ->json($build_result);

    }

    function get_weekly_meals(Request $request)
    {

        $school_id = $this->school_id;
        /* Prefixed table name are required wherever we are using DB::raw calls */
        $weekly_meal_table = $this
            ->BaseModel
            ->getTable();
        $prefixed_weekly_meal_table = DB::getTablePrefix() . $this
            ->BaseModel
            ->getTable();

        $canteen_product_table = $this
            ->CanteenProductsModel
            ->getTable();
        $prefixed_canteen_product_table = DB::getTablePrefix() . $this
            ->CanteenProductsModel
            ->getTable();

        $product_type_table = $this
            ->CanteenProductTypesModel
            ->getTable();
        $prefixed_product_type_table = DB::getTablePrefix() . $this
            ->CanteenProductTypesModel
            ->getTable();

        $obj_weekly_meals = DB::table($weekly_meal_table)->select(DB::raw($prefixed_weekly_meal_table . ".id as id," . $prefixed_canteen_product_table . ".product_id as product_id," . $prefixed_canteen_product_table . ".product_name as product_name," . $prefixed_canteen_product_table . ".product_image as product_image," . $prefixed_weekly_meal_table . ".stock as quantity," . $prefixed_canteen_product_table . ".price as price," . $prefixed_product_type_table . ".type as type"))->where($weekly_meal_table . '.school_id', '=', $school_id)->whereNull($weekly_meal_table . '.deleted_at')->where($weekly_meal_table . '.week_day', '=', 'monday')->leftJoin($canteen_product_table, $weekly_meal_table . '.item_id', '=', $canteen_product_table . '.id')->leftJoin($product_type_table, $canteen_product_table . '.product_type', '=', $product_type_table . '.id');

        /* ---------------- Filtering Logic ----------------------------------*/

        $arr_search_column = $request->input('column_filter');
        if (isset($arr_search_column['q_day']) && sizeof($arr_search_column['q_day']) > 0 && $arr_search_column['q_day'] != "")
        {
            $search_term = $arr_search_column['q_day'];

            $obj_weekly_meals = DB::table($weekly_meal_table)->select(DB::raw($prefixed_weekly_meal_table . ".id as id," . $prefixed_canteen_product_table . ".product_id as product_id," . $prefixed_canteen_product_table . ".product_name as product_name," . $prefixed_canteen_product_table . ".product_image as product_image," . $prefixed_canteen_product_table . ".price as price," . $prefixed_weekly_meal_table . ".stock as quantity," . $prefixed_product_type_table . ".type as type"))->where($weekly_meal_table . '.school_id', '=', $school_id)->whereNull($weekly_meal_table . '.deleted_at')->leftJoin($canteen_product_table, $weekly_meal_table . '.item_id', '=', $canteen_product_table . '.id')->leftJoin($product_type_table, $canteen_product_table . '.product_type', '=', $product_type_table . '.id')->where($weekly_meal_table . '.week_day', 'LIKE', '%' . $search_term . '%');
        }

        $search = $request->input('search');
        $search_term = $search['value'];

        if ($request->has('search') && $search_term != "")
        {
            $obj_weekly_meals = $obj_weekly_meals->WhereRaw("( (" . $product_type_table . ".type LIKE '%" . $search_term . "%') ")->orWhereRaw("(" . $canteen_product_table . ".product_name LIKE '%" . $search_term . "%') ")->orWhereRaw("(" . $canteen_product_table . ".product_id LIKE '%" . $search_term . "%') ")->orWhereRaw("(" . $weekly_meal_table . ".stock LIKE '%" . $search_term . "%') ")->orWhereRaw("(" . $canteen_product_table . ".price LIKE '%" . $search_term . "%') )");

        }
        return $obj_weekly_meals;
    }

    public function delete($enc_id)
    {
        $id = base64_decode($enc_id);
        $arr_details = [];
        $obj_details = $this
            ->BaseModel
            ->where('id', $id)->with('get_daily_meals')
            ->first();

        if (isset($obj_details) && $obj_details != null)
        {
            $arr_details = $obj_details->toArray();

            if (isset($arr_details['get_daily_meals']) && sizeof($arr_details['get_daily_meals']) > 0)
            {
                foreach ($arr_details['get_daily_meals'] as $key => $value)
                {
                    $delete_daily_meals = $this
                        ->DailyMealsModel
                        ->where('id', $value['id'])->delete();
                }
            }
        }

        return $this->module_delete($enc_id);
    }

    public function update_stock(Request $request)
    {
        $id = base64_decode($request->input('enc_id'));
        $day = $request->input('day');
        $stock = $request->input('stock');
        $update = '';
        if (isset($id) && $id != '')
        {
            $obj_meal_data = $this
                ->BaseModel
                ->where('id', $id)->where('school_id', $this->school_id)
                ->where('week_day', $day)->first();

            if (isset($obj_meal_data) && $obj_meal_data != null)
            {
                $update = $this
                    ->BaseModel
                    ->where('id', $id)->update(['stock' => $stock]);

            }

            if ($update)
            {
                $obj_meal_data = $this
                    ->BaseModel
                    ->where('id', $id)->first();

                return response()
                    ->json(array(
                    'status' => 'success',
                    'stock' => $obj_meal_data->stock,
                    'msg' => translation('stock') . ' ' . translation('updated_successfully')
                ));
            }
            else
            {
                $obj_meal_data = $this
                    ->BaseModel
                    ->where('id', $id)->first();
                return response()
                    ->json(array(
                    'status' => 'error',
                    'stock' => $obj_meal_data->stock,
                    'msg' => translation('problem_occured_while_updating') . ' ' . translation('stock')
                ));
            }
        }
    }

    public function multi_action(Request $request)
    {
        $arr_rules = array();
        $arr_rules['multi_action'] = "required";
        $arr_rules['checked_record'] = "required";

        $validator = Validator::make($request->all() , $arr_rules);

        if ($validator->fails())
        {
            Flash::error(translation('please_select_record_to_perform_multiaction'));
            return redirect()
                ->back()
                ->withErrors($validator)->withInput();
        }

        $multi_action = $request->input('multi_action');
        $checked_record = $request->input('checked_record');

        /* Check if array is supplied*/
        if (is_array($checked_record) && sizeof($checked_record) <= 0)
        {
            Flash::error(translation('problem_occured_while_doing') . ' ' . translation('multiaction'));
            return redirect()->back();
        }

        foreach ($checked_record as $record_id)
        {
            if ($multi_action == "delete")
            {
                $this->perform_delete(base64_decode($record_id));
                Flash::success($this->module_title . ' ' . strtolower(translation('deleted_succesfully')));
            }
        }

        return redirect()
            ->back();
    }

    public function perform_delete($id)
    {

        $obj_details = $this
            ->BaseModel
            ->where('id', $id)->with('get_daily_meals')
            ->first();
        if ($obj_details)
        {
            $arr_details = $obj_details->toArray();

            if (isset($arr_details['get_daily_meals']) && sizeof($arr_details['get_daily_meals']) > 0)
            {
                foreach ($arr_details['get_daily_meals'] as $key => $daily_meal)
                {
                    $delete_daily_meals = $this
                        ->DailyMealsModel
                        ->where('id', $daily_meal['id'])->delete();
                }
            }
        }

        $delete = $this
            ->BaseModel
            ->where('id', $id)->delete();

        if ($delete)
        {
            return true;
        }

        return false;
    }
}

