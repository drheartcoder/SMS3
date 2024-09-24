<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\ClubModel;
use App\Models\ClubStudentsModel;
use App\Common\Traits\MultiActionTrait;

use App\Common\Services\CommonDataService;

use Session;
use Validator;
use Flash;
use Sentinel;
class ClubController extends Controller
{
    public function __construct(CommonDataService $CommonDataService){

        $this->CommonDataService = $CommonDataService;
        $this->ClubModel         = new ClubModel();
        $this->ClubStudentsModel = new ClubStudentsModel();
        $this->BaseModel         = $this->ClubModel;
        $this->arr_view_data     = [];
        $this->module_url_path   = url(config('app.project.professor_panel_slug')).'/club';
        $this->module_title      = translation('club');
        
        $this->module_view_folder           = "professor.club";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-users';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');

        $this->arr_view_data['module_icon']  =  $this->module_icon;
        $this->arr_view_data['create_icon']  =  $this->create_icon;
        $this->arr_view_data['edit_icon']    =  $this->edit_icon;
        $this->arr_view_data['theme_color']  =  $this->theme_color;
        $this->arr_view_data['page_title']   =  $this->module_title;
        $this->arr_view_data['module_url_path']   =  $this->module_url_path;

        $obj_data          = Sentinel::getUser();
        if($obj_data){
            
            $this->user_id           = $obj_data->id;
        }
    }
    /*
    | index() : listing of clubs
    | Auther : Pooja K
    | Date : 19 June 2018
    */ 
    public function index(){

    	$arr_clubs =[];
        $obj_clubs = $this->ClubModel
        							->with('get_supervisor')
                                    ->where('school_id',$this->school_id)
                                    ->where('academic_year_id',$this->academic_year)
                                    ->where('supervisor_id',$this->user_id)
                                    ->orderBy('id','DESC')
                                    ->get();

        if(isset($obj_clubs) && !empty($obj_clubs)){

            $arr_clubs = $obj_clubs->toArray();
        }                            
        $this->arr_view_data['module_icon']  = $this->module_icon;
        $this->arr_view_data['arr_clubs']    = $arr_clubs;
        $this->arr_view_data['module_title'] = translation('manage').' '.$this->module_title;
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*
    | view() : view club details
    | Auther : Pooja K
    | Date : 19 June 2018
    */ 

    public function view($enc_id=FALSE){

        $id = base64_decode($enc_id);
        $obj_club = $this->ClubModel
                                    ->with('get_supervisor','get_students.get_user_details','get_students.get_level_class.level_details','get_students.get_level_class.class_details')
                                    ->where('id',$id)
                                    ->first();

        if(isset($obj_club) && !empty($obj_club)){

            $arr_club = $obj_club->toArray();

            $this->arr_view_data['module_icon']    = $this->module_icon;
            $this->arr_view_data['arr_data']       = $arr_club;
            $this->arr_view_data['module_title']   = translation('view').' '.$this->module_title;

            return view($this->module_view_folder.'.view', $this->arr_view_data);
        }
        Flash::success(translation('no_data_available'));
        return redirect()->back();
    }
}
