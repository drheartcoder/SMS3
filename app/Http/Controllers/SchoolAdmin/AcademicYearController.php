<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Common\Services\LanguageService;
use App\Common\Traits\MultiActionTrait;
use App\Models\AcademicYearModel;  
use Session;
use Validator;
use Flash;
use Sentinel;
use DB;
use Datatables;

class AcademicYearController extends Controller
{
    use MultiActionTrait;

    public function __construct(AcademicYearModel $year) 
    {
        $this->arr_view_data            =   [];
        $this->AcademicYearModel        =   $year;
        $this->BaseModel                =   $this->AcademicYearModel;
        $this->module_url_path          =   url(config('app.project.school_admin_panel_slug')."/academic_year");
        $this->module_view_folder       =   "schooladmin.academic_year";
        $this->module_title             =   translation("academic_year");
        $this->module_icon              =   'fa fa-calendar';
        $this->create_icon              =   'fa fa-plus-circle';
        $this->edit_icon                =   'fa fa-edit';

        $this->theme_color              =   theme_color();
        $this->school_id                =   Session::has('school_id')?Session::get('school_id'):0;
        $this->first_name               =   $this->last_name =$this->ip_address ='';
        $obj_data                       =   Sentinel::getUser();
        
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->id                = $obj_data->id;  
        }
    }
        /*
        | index() : academic year listing 
        | Auther : sayali 
        | Date : 10-05-2018
        */
    public function index() 
    {
        $this->arr_view_data['page_title']          = translation("manage")." ".str_singular($this->module_title);
        $this->arr_view_data['module_title']        = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['theme_color']         = $this->theme_color;
        $this->arr_view_data['module_icon']         = $this->module_icon;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

        /*
        | create() :load academic year create page
        | Auther : sayali
        | Date : 10-05-2018
        */
    public function create()
    {
        
        $this->arr_view_data['page_title']      = translation("create")." ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }
        /*
        | store() : academic year store 
        | Auther : sayali    
        | Date : 10-05-2018
        */        
    public function store(Request $request)
    {
        $year = '';
        $arr_rules['start_date']           =   'required|date';
        $arr_rules['end_date']             =   'required|date|after:start_date';

        $messages = array(
                            
                            'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date')

                        );

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        {
             return redirect()->back()->withErrors($validator)->withInput($request->all());
        }


        $date           =   explode('/', $request->input('start_date'));
        $start_date     =   $date[2].'-'.$date[0].'-'.$date[1];

        $date2          =   explode('/', $request->input('end_date'));
        $end_date       =   $date2[2].'-'.$date2[0].'-'.$date2[1];

        if(isset($date[2]) && !empty($date[2]) && isset($date2[2]) && !empty($date2[2]))
        {
            $year = $date[2].'-'.$date2[2];
            if($date[2] == $date2[2])
            {
                Flash::error(translation('invalid_academic_year'));
                return redirect()->back();
            }
        }


                                                    
        $does_exists = $this->BaseModel
                            ->where('academic_year','=',trim($year))
                            ->where('school_id',$this->school_id)
                            ->count();
        if($does_exists>0)
        {
            Flash::error(str_singular($this->module_title).' '.translation('already_exists'));
            return redirect()->back();
        }  
        
        $arr_data   =   [];
       
        $arr_data['school_id']         = $this->school_id;
        $arr_data['start_date']        = $start_date;
        $arr_data['end_date']          = $end_date;
        $arr_data['academic_year']     = $year;

        $academic_year     = $this->BaseModel->create($arr_data);


        
        if($academic_year)
        {                
            /* checked the redirection after creating the academic year  code done by padmashri*/  
            $does_exists = $this->BaseModel
                            ->where('academic_year','=',trim($year))
                            ->where('school_id',$this->school_id)
                            ->count(); 
            $type = '';
            if($does_exists == 1 ){

                if(Session::has('school_id') && Session::get('school_id')!='' && Session::get('school_id')>0){
                    $type =  'dashboard';
                }else{
                    $type = 'school';
                }
            }   

            /* checked the redirection after creating the academic year  code done by padmashri*/
            Flash::success($this->module_title .' '.translation('created_successfully'));       
            if($type!=''){
                return redirect($this->module_url_path.'/create?type='.$type);
            }else{
                return redirect()->back();
            }
        } 
        else
        {
            Flash::success(translation('problem_occurred_while_creating'),' '.$this->module_title);
            return redirect()->back();
        }
        
    }

        /*
        | get_exam_type_details() : academic year details using ajax 
        | Auther                  : Gaurav 
        | Date                    : 09-05-2018
        */
    function get_academic_year_details(Request $request)
    {     
        $school_id     = $this->school_id;
        $academic_year_details                  = $this->BaseModel->getTable();
        $prefixed_academic_year_details         = DB::getTablePrefix().$this->BaseModel->getTable();

        return DB::table($academic_year_details)
                                ->select(DB::raw($prefixed_academic_year_details.".id as id,".
                                                 $prefixed_academic_year_details.".academic_year as academic_year,".
                                                 $prefixed_academic_year_details.".start_date as start_date,".
                                                 $prefixed_academic_year_details.".end_date as end_date"))
                                ->whereNull($academic_year_details.'.deleted_at')
                                ->where($academic_year_details.'.school_id','=',$school_id)
                                ->orderBy($academic_year_details.'.created_at','DESC');

       
    }

        /*
        | get_records() : Exam Type get_records 
        | Auther        : Gaurav 
        | Date          : 09-05-2018
        */
    public function get_records(Request $request)
    {
        $arr_current_user_access =[];
        
        $role = Sentinel::findRoleBySlug(config('app.project.role_slug.school_admin_role_slug'));

        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;
        
        $obj_academic_year        = $this->get_academic_year_details($request);

        $json_result     = Datatables::of($obj_academic_year);
        $json_result     = $json_result->blacklist(['id']);
        
        if(array_key_exists('academic_year.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data) 
                            {
                                return base64_encode($data->id);
                            });
        }                    

        $json_result     = $json_result->editColumn('academic_year',function($data)
                            { 
                                 
                                if($data->academic_year!=null && $data->academic_year!=''){
                                    return  $data->academic_year;
                                }else{
                                    return  '-';
                                }

                            }) 
                            ->editColumn('start_date',function($data) 
                            {
                                if(isset($data->start_date) && $data->start_date!='')
                                {
                                    return getDateFormat($data->start_date);
                                }
                                else
                                {
                                    return '-';
                                }
                            })

                             ->editColumn('end_date',function($data) 
                            {
                                if(isset($data->end_date) && $data->end_date!='')
                                {
                                    return getDateFormat($data->end_date);
                                }
                                else
                                {
                                    return '-';
                                }
                            })
                            ->editColumn('build_action_btn',function($data) use ($arr_current_user_access)
                            {
                                $build_delete_action ='';

                                if(array_key_exists('academic_year.delete',$arr_current_user_access))
                                {
                                    $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                    $build_delete_action = '<a class="red-color" href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';
                                }

                                return $build_delete_action;
                               
                            })
                            ->editColumn('build_checkbox',function($data){
                                
                                    return '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                  
                             
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        
        return response()->json($build_result);
    }

        /*
        | arrange_locale_wise() : Exam Type arrange_locale_wise 
        | Auther                : Gaurav 
        | Date                  : 09-05-2018
        */
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
