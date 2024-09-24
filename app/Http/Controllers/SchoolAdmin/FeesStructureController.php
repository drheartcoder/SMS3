<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\FeesModel;
use App\Models\FeesSchoolModel;
use App\Models\BrotherhoodModel;
use App\Common\Services\CommonDataService;

use DB;
use DataTables;
use Session;
use Sentinel;
use Flash;

class FeesStructureController extends Controller
{
    public function __construct(CommonDataService $CommonDataService){
    	
    	
        $this->module_url_path          =   url(config('app.project.school_admin_panel_slug')."/fees_structure");
        $this->module_view_folder       =   "schooladmin.fees_structure";
        $this->module_title             =   translation("payment_management");
        $this->theme_color              =   theme_color();

        $this->arr_view_data                    = [];
        $this->arr_view_data['page_title']      = translation('payment_management');
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['module_icon']     = 'fa fa-money';
        $this->arr_view_data['create_icon']     = 'fa fa-plus-circle';
        $this->arr_view_data['edit_icon']       = 'fa fa-edit';
        $this->arr_view_data['theme_color']     = $this->theme_color;
        
        $this->school_id                = Session::has('school_id')?Session::get('school_id'):0;
        $this->academic_year            = Session::has('academic_year')?Session::get('academic_year'):0;

        $this->first_name               = $this->last_name = $this->ip_address ='';
        $obj_data                       = Sentinel::getUser();
        
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->id                = $obj_data->id;  
        }

    	$this->FeesModel = new FeesModel();
    	$this->CommonDataService = $CommonDataService;
        $this->FeesSchoolModel = new FeesSchoolModel();
        $this->BrotherhoodModel  = new BrotherhoodModel();
    }

    /*
    | index() : redirecting to list page of fees structures
    | Auther : pooja k
    | Date : 7 Jun 2018
    */ 
    public function index(){
        
        $arr_data = [];

        $obj_data = $this->FeesSchoolModel
                                          ->with('get_level')
                                          ->whereHas('level_exists',function(){})  
                                          ->where('school_id',$this->school_id)
                                          ->where('academic_year_id',$this->academic_year)
                                          ->groupBy('level_id')
                                          ->get();

        if(isset($obj_data) && !empty($obj_data)){

            $arr_data =  $obj_data -> toArray();
        }  
        
        $this->arr_view_data['arr_data'] = $arr_data;
        $this->arr_view_data['module_title'] = $this->module_title;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
            
    }

    /*
    | create() : redirecting to create page of fees with data
    | Auther : pooja k
    | Date : 6 Jun 2018
    */ 
    public function create(){
        $this->module_title = translation('fees_structure');
    	$arr_fees = $arr_levels = [];

    	$obj_fees = $this->CommonDataService->get_fees();

    	if(empty($obj_fees)){

    		Flash::error(translation('no_fees_categories_available').' '.$this->module_title);
    		return redirect()->back(); 
    	}
    	else{

            $obj_levels = $this->CommonDataService->get_levels($this->academic_year);
            
            if(!empty($obj_levels)){

                $arr_fees = $obj_fees -> toArray();    

                $arr_levels = $obj_levels -> toArray();

                $this->arr_view_data['arr_fees'] = $arr_fees;
                $this->arr_view_data['arr_levels'] = $arr_levels;
                $this->arr_view_data['module_title'] = translation('add').' '.$this->module_title;

                return view($this->module_view_folder.'.create',$this->arr_view_data);
            }
            else{

                Flash::error(translation('no_levels_available').' '.$this->module_title);
                return redirect()->back();
            }
    		
    	}	 
    }

    /*
    | store() : store fees in database
    | Auther : pooja k
    | Date : 7 Jun 2018
    */
    public function store(Request $request)
    {
        if($request->has('count')){
            $count = $request->count;
            $level     = $request->input("level");
            $exist = $this->FeesSchoolModel->where('school_id',$this->school_id)
                                          ->where('academic_year_id',$this->academic_year)
                                          ->where('level_id',$level)
                                          ->count();
            if($exist)
            {
                Flash::error(translation('fees_structure_for_this_level_is_already_exist'));
                return redirect()->back();    
            }
            for($iterator=0 ;$iterator<$count ;$iterator++){
                $fees      = $request->input("real_fees_".$iterator);
                $frequency = $request->input("frequency_".$iterator);
                $amount    = $request->input("amount_".$iterator);
                $optional  = count($request->input("optional_".$iterator));

                if($fees!='' && $frequency!='' && $amount!='' && $level!='')
                {  
                    $arr_data=[];
                    $arr_data['school_id'] = $this->school_id;
                    $arr_data['level_id'] = $level;
                    $arr_data['fees_id'] = $fees;
                    $arr_data['frequency'] = $frequency;
                    $arr_data['amount'] = $amount;
                    if($optional==2)
                    {
                        $arr_data['is_optional'] = '1';    
                    }
                    else
                    {
                        $arr_data['is_optional'] = '0';
                    }
                    $arr_data['academic_year_id'] = $this->academic_year;
                    $this->FeesSchoolModel->create($arr_data);
                }
            }

            Flash::success(translation('fees_structure_created_successfully'));
            return redirect()->back();
        }
        else{
            Flash::error(translation('problem_occured'));
            return redirect()->back();
        }
    }

    /*
    | edit() : redirect to edit fees structure page with data
    | Auther : pooja k
    | Date : 7 Jun 2018
    */
    public function edit($enc_id)
    {
        $this->module_title = translation('fees_structure');
        $id = base64_decode($enc_id);

        $arr_data = $arr_fees = $arr_levels = [];

        $obj_fees = $this->CommonDataService->get_fees();

        if(empty($obj_fees)){

            Flash::error(translation('no_fees_categories_available'));
            return redirect()->back(); 
        }
        else{

            $obj_levels = $this->CommonDataService->get_levels($this->academic_year);
            if(!empty($obj_levels)){

                $arr_fees = $obj_fees -> toArray();    

                $arr_levels = $obj_levels -> toArray();

                $arr_data = $this->FeesSchoolModel->where('level_id',$id)->where('school_id',$this->school_id)->get();
               
                if(!empty($arr_data)){

                    $this->arr_view_data['arr_fees']   = $arr_fees;
                    $this->arr_view_data['arr_levels'] = $arr_levels;
                    $this->arr_view_data['arr_data']   = $arr_data;
                    $this->arr_view_data['module_title'] = translation('edit').' '.$this->module_title;
                    return view($this->module_view_folder.'.edit',$this->arr_view_data);
                }
                else{

                    Flash::error(translation('problem_occured'));
                    return redirect()->back();
                }
                
            }
            else{

                Flash::error(translation('no_levels_available').' '.$this->module_title);
                return redirect()->back();
            }
            
        }
    }

    /*
    | update() : store fees in database
    | Auther : pooja k
    | Date : 7 Jun 2018
    */
    public function update(Request $request, $enc_id=FALSE)
    {
        $this->module_title = translation('fees_structure');
        if($request->has('count')){
            $count = $request->count;
            $level     = $request->input("level");
            for($iterator=0 ;$iterator<$count ;$iterator++){
                $fees      = $request->input("real_fees_".$iterator);
                $frequency = $request->input("frequency_".$iterator);
                $amount    = $request->input("amount_".$iterator);
                $optional  = count($request->input("optional_".$iterator));
                if($fees!='' && $frequency!='' && $amount!='' && $level!=''){  
                 
                    if($optional==2){
                        $is_optional = '1';    
                    }
                    else{
                        $is_optional = '0';
                    }

                    $result = $this->FeesSchoolModel
                                          ->where('school_id',$this->school_id)
                                          ->where('academic_year_id',$this->academic_year)
                                          ->where('level_id',$level)
                                          ->where('fees_id',$fees)
                                          ->first();
                    if(isset($result) && !empty($result)) {
                        $result->is_optional = $is_optional;
                        $result->frequency   = $frequency;
                        $result->amount      = $amount;                      
                        $result->update();
                    } 
                    else{
                       
                        $arr_data=[];
                        $arr_data['school_id'] = $this->school_id;
                        $arr_data['level_id'] = $level;
                        $arr_data['fees_id'] = $fees;
                        $arr_data['frequency'] = $frequency;
                        $arr_data['is_optional'] = $is_optional;
                        $arr_data['academic_year_id'] = $this->academic_year;
                        $arr_data['amount'] = $amount;
                        $arr_data['academic_year_id'] = $this->academic_year;
                        $this->FeesSchoolModel->create($arr_data);
                    }
                    
                }
                else
                {
                }
            }
            $arr_fees =[];
            for($iterator=0 ;$iterator<$count ;$iterator++){
                $fees      = $request->input("real_fees_".$iterator);
                if($fees!=''){
                    array_push($arr_fees,$fees);
                }
            }
        
            $obj_fees = $this->FeesSchoolModel->select('id','fees_id')
                                          ->where('school_id',$this->school_id)
                                          ->where('academic_year_id',$this->academic_year)
                                          ->where('level_id',$level)
                                          ->get();
            if($obj_fees){
                $result_fees = $obj_fees->toArray();
                foreach($result_fees as $fee){
                    if(!in_array($fee['fees_id'], $arr_fees)){

                        $this->FeesSchoolModel->where('id',$fee['id'])->delete();
                    }
                }
            }                             

            Flash::success(translation('fees_structure_updated_successfully'));
            return redirect($this->module_url_path);
        }
        else{
            Flash::error(translation('problem_occured'));
            return redirect()->back();
        }
    }

    /*
    | edit() : view fees structure
    | Auther : pooja k
    | Date : 7 Jun 2018
    */
    public function view($enc_id)
    {
        $id = base64_decode($enc_id);

        $arr_years = $this->CommonDataService->get_academic_year_less_than($this->academic_year);
        if($arr_years=='')
        {
            Flash::error(translation('problem_occured'));
            return redirect()->back();
        }
        $arr_years = explode(',',$arr_years);

        $school_name = $this->CommonDataService->get_school_name();

        $school_address = $this->CommonDataService->get_school_address();

        $school_email = $this->CommonDataService->get_school_email();

        $school_logo = $this->CommonDataService->get_school_logo();

        $arr_data  = $arr_brother = [];

        $obj_data = $this->FeesSchoolModel
                                        ->with('get_fees','get_level')
                                        ->where('school_id',$this->school_id)
                                        ->where('academic_year_id',$this->academic_year)
                                        ->where('level_id',$id)
                                        ->get();

        if(!empty($obj_data)){
            $arr_data = $obj_data ->toArray();

            $obj_brother = $this->BrotherhoodModel
                                        ->where('school_id',$this->school_id)
                                        ->whereIn('academic_year_id',$arr_years)
                                        ->get();
                               
            if(!(empty($obj_brother) )) {


                $arr_brother = $obj_brother -> toArray();
               
                $this->arr_view_data['arr_brother']    = $arr_brother;
                $this->arr_view_data['arr_data']       = $arr_data;
                $this->arr_view_data['school_name']    = $school_name;
                $this->arr_view_data['school_address'] = $school_address;
                $this->arr_view_data['school_email']   = $school_email;
                $this->arr_view_data['school_logo']    = $school_logo;
                
                $this->arr_view_data['module_title'] = translation('edit').' '.$this->module_title;
                return view($this->module_view_folder.'.view',$this->arr_view_data);    
            }
            else{
                Flash::error(translation('problem_occured'));
                return redirect()->back();                
            }
            
        }
        else{

            Flash::error(translation('problem_occured'));
            return redirect()->back();
        }
    }
}
