<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Traits\MultiActionTrait;

use App\Models\LevelModel;
use App\Models\EducationalBoardModel;
use App\Models\AdmissionConfigModel;
use App\Common\Services\CommonDataService;


use Validator;
use Session;
use Flash;
class AdmissionConfigController extends Controller
{
    use MultiActionTrait;
    public function __construct(
    							 	LevelModel $LevelModel,
                                    EducationalBoardModel $educational_board,
                                    AdmissionConfigModel $admission_config,
                                    CommonDataService $CommonDataService
    							)
    {

		$this->arr_view_data 	  = [];
		$this->EducationalBoardModel = $educational_board;
        $this->AdmissionConfigModel = $admission_config;
		$this->module_url_path 	  = url(config('app.project.role_slug.school_admin_role_slug')."/admission_config");
		$this->module_view_folder = "schooladmin.admission_config";
		$this->module_title       = translation('admission_configuration');
        $this->LevelModel         =   $LevelModel;
		$this->theme_color        = theme_color();
		$this->module_icon        = 'fa fa-cog';
		$this->create_icon        = 'fa fa-plus-circle';
        $this->edit_icon          = 'fa fa-edit';
        $this->school_id          = \Session::get('school_id');
        $this->BaseModel          = $admission_config;
        $this->CommonDataService  = $CommonDataService;
        $this->academic_year      = Session::get('academic_year');
    }

    public function index($enc_id=FALSE)
    {
        $id = 0;
        if($enc_id)
        {
            $id = base64_decode($enc_id);
        }
        $arr_levels = [];
        $obj_levels = $this->CommonDataService->get_levels($this->academic_year);
        if($obj_levels)
        {
            $arr_levels = $obj_levels->toArray();
        }
        
        $arr_admission_configs = [];
        $obj_admission_configs = $this->AdmissionConfigModel
                                            ->with('get_academic_year','get_level','get_education_board')
                                            ->where('school_id',$this->school_id)
                                            ->where('academic_year',$this->academic_year)
                                            ->get();
        if($obj_admission_configs)
        {
            $arr_admission_configs = $obj_admission_configs->toArray();
        }        

        $arr_edit_admission_config = [];
        if($id!=0)
        {
            $obj_edit_admission_config = $this->AdmissionConfigModel
                                            ->with('get_academic_year','get_level','get_education_board')
                                            ->where('id',$id)
                                            ->first();
            if($obj_edit_admission_config)
            {
                $arr_edit_admission_config = $obj_edit_admission_config->toArray();
            }    
        }

        $arr_boards = [];
        $obj_boards = $this->EducationalBoardModel->where('school_id',$this->school_id)->get();
        if($obj_boards)
        {
            $arr_boards = $obj_boards->toArray();
        }
    
        $this->arr_view_data['page_title']      = translation("manage")." ".$this->module_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;       
        $this->arr_view_data['arr_levels']     = $arr_levels;       
        $this->arr_view_data['arr_boards']     = $arr_boards;       
        $this->arr_view_data['arr_admission_configs']     = $arr_admission_configs;       
        $this->arr_view_data['arr_edit_admission_config']     = $arr_edit_admission_config;       
        $this->arr_view_data['enc_id']     = $enc_id;       

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }  
    public function store(Request $request)
    {
        $arr_rules =array(
                
                'educational_board' => 'required',
                'admission_open' => 'required|date',
                'application_fee' => 'required|numeric|min:0',
                'no_of_seats' => 'required|numeric|min:0',
                'admission_close' => 'required|date',
                'level' => 'required'

            );

        $messages = array(
                          
                            'numeric'              => translation('please_enter_digits_only'),
                            'required'             => translation('this_field_is_required'),
                            'application_fee.min'  => translation('please_enter_a_value_greater_than_or_equal_to_0'),
                            'no_of_seats.min'      => translation('please_enter_a_value_greater_than_or_equal_to_0'),
                             'date'                 => translation('please_enter_valid_date')
                        );

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
             return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $academic_year = \Session::get('academic_year');
        $educational_board = $request->input('educational_board');
        $admission_open = $request->input('admission_open'); 
        $application_fee = $request->input('application_fee');
        $no_of_seats = $request->input('no_of_seats');
        $admission_close = $request->input('admission_close');
        $level = $request->input('level');   

        $admission_open = date_create($admission_open);
        $admission_open = date_format($admission_open,'Y-m-d');

        $admission_close = date_create($admission_close);
        $admission_close = date_format($admission_close,'Y-m-d');

        if($level!='all'){
            
            $exist_count = $this->AdmissionConfigModel
                                    ->where('educational_board',$educational_board)
                                    ->where('academic_year',$academic_year)
                                    ->where('level',$level)
                                    ->where('school_id',$this->school_id)
                                    ->count();
            if($exist_count>0){
                Flash::error(tranlsation('already_exists'));
                return redirect($this->module_url_path);
            }
            $arr_data = [];
            $arr_data['academic_year']     = $academic_year;
            $arr_data['educational_board'] = $educational_board;
            $arr_data['admission_open']    = $admission_open;
            $arr_data['application_fee']   = $application_fee;
            $arr_data['no_of_seats']       = $no_of_seats;
            $arr_data['admission_close']   = $admission_close;
            $arr_data['level_id']          = $level;
            $arr_data['school_id']         = $this->school_id;

            $result = $this->AdmissionConfigModel->create($arr_data);

        }
        else{
            
            $exist_count = $this->AdmissionConfigModel
                            ->where('educational_board',$educational_board)
                            ->where('school_id',$this->school_id)
                            ->where('academic_year',$academic_year)
                            ->delete();
                            
            $arr_levels = [];

            $obj_levels = $this->CommonDataService->get_levels($this->academic_year);
            if($obj_levels)
            {
                $arr_levels = $obj_levels->toArray();
            }
            foreach($arr_levels as $level){
                

                $arr_data = [];
                $arr_data['academic_year']     = $academic_year;
                $arr_data['educational_board'] = $educational_board;
                $arr_data['admission_open']    = $admission_open;
                $arr_data['application_fee']   = $application_fee;
                $arr_data['no_of_seats']       = $no_of_seats;
                $arr_data['admission_close']   = $admission_close;
                $arr_data['level_id']          = $level['level_id'];
                $arr_data['school_id']         = $this->school_id;

                $result = $this->AdmissionConfigModel->create($arr_data);
            }

        }

        if($result)
        {
            Flash::success($this->module_title.' '.translation('created_successfully'));    
        }
        else
        {
            Flash::error(translation('something_went_wrong'));    
        }
        
        return redirect($this->module_url_path);

    }

    public function update(Request $request,$enc_id=FALSE)
    {
        $arr_rules =array(
                
                'educational_board' => 'required',
                'admission_open' => 'required|date',
                'application_fee' => 'required|numeric|min:0',
                'no_of_seats' => 'required|numeric|min:0',
                'admission_close' => 'required|date',
                'level' => 'required'

            );

        $messages = array(
                          
                            'numeric'              => translation('please_enter_digits_only'),
                            'required'             => translation('this_field_is_required'),
                            'application_fee.min'  => translation('please_enter_a_value_greater_than_or_equal_to_0'),
                            'no_of_seats.min'      => translation('please_enter_a_value_greater_than_or_equal_to_0'),
                             'date'                 => translation('please_enter_valid_date')
                        );

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
             return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $academic_year = \Session::get('academic_year');
        $educational_board = $request->input('educational_board');
        $admission_open = $request->input('admission_open'); 
        $application_fee = $request->input('application_fee');
        $no_of_seats = $request->input('no_of_seats');
        $admission_close = $request->input('admission_close');
        $level = $request->input('level');   

        $admission_open = date_create($admission_open);
        $admission_open = date_format($admission_open,'Y-m-d');

        $admission_close = date_create($admission_close);
        $admission_close = date_format($admission_close,'Y-m-d');

        $arr_data = [];
        $arr_data['academic_year']     = $academic_year;
        $arr_data['educational_board'] = $educational_board;
        $arr_data['admission_open']    = $admission_open;
        $arr_data['application_fee']   = $application_fee;
        $arr_data['no_of_seats']       = $no_of_seats;
        $arr_data['admission_close']   = $admission_close;
        $arr_data['level_id']          = $level;

        $this->AdmissionConfigModel->where('id',base64_decode($enc_id))->update($arr_data);

        Flash::success($this->module_title.' '.translation('updated_successfully'));
        return redirect()->back();

    }

}