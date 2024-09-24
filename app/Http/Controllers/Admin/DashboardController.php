<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\UserModel;
use App\Common\Services\CommonDataService;
 

use App\Models\RoleModel;
use App\Models\LevelModel;
use App\Models\UserRoleModel;
use App\Models\MemberShipModel;
use App\Models\SuggestionModel;
use App\Models\ContactEnquiryModel;
use App\Models\UserTranslationModel;
use App\Models\LevelTranslationModel;





use DB;
use Session;
use Sentinel;


		  					 													
class DashboardController extends Controller
{	  	                 
	public function __construct(UserModel $user,
								CommonDataService $common
								)
	{
		$this->arr_view_data          = [];
		$this->module_title           = "Dashboard";
		$this->UserModel              = $user;
		$this->CommonDataService	  =	$common;
		$this->module_view_folder     = "admin.dashboard";
		$this->admin_url_path         = url(config('app.project.admin_panel_slug'));
		$this->theme_color            = theme_color();
  	    $this->LevelModel 			  = new LevelModel();
        $this->RoleModel 			  = new RoleModel();
        $this->UserRoleModel  		  = new UserRoleModel();
        $this->MemberShipModel 		  = new MemberShipModel();
        $this->SuggestionModel 		  = new SuggestionModel();
        $this->ContactEnquiryModel 	  = new ContactEnquiryModel();
        $this->UserTranslationModel   = new UserTranslationModel();
        $this->LevelTranslationModel  = new LevelTranslationModel();

        $this->role 					  = config('app.project.admin_panel_slug');
		$user = Sentinel::check();
    	if(isset($user->id)){
    		$this->user_id = $user->id;
    		$this->first_name        = $user->first_name;
            $this->last_name         = $user->last_name;
    	}
    	else
    	{
    		return redirect($this->admin_url_path);
    	}
			
		if(Session::has('locale'))
        {
            $this->locale = Session::get('locale');
        }
        else
        {
            $this->locale = 'en';
        }


		$arr_current_user_access =[];
        $role = Sentinel::findRoleBySlug($this->role);
        $this->arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [];
	}
	   
    public function index(Request $request)
    {
    	

    	$user_id = $this->user_id;
    	
    	$this->CommonDataService->assign_module_permission_to_admin(config('app.project.role_slug.admin_role_slug'));
    	

    	
    	$this->arr_view_data['arr_final_tile'] = $this->built_dashboard_tiles($request);
    	$this->arr_view_data['page_title']     = $this->module_title;
    	$this->arr_view_data['admin_url_path'] = $this->admin_url_path;
    	$this->arr_view_data['user_id']        = $user_id;
		return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

     /*---------------------------------
    index() : Show dashboard Tiles
    Auther  : Padmashri 
    Date    : 9th July 2018 
    ---------------------------------*/
    public function built_dashboard_tiles($request)
    {
        /*------------------------------------------------------------------------------
        | Note: Directly Use icon name - like, <i class="fa fa-users"></i> and use directly - 'user'
        ------------------------------------------------------------------------------*/
        $arr_current_user_access  = $this->arr_current_user_access;    
        $contactEnquieryCount =   $professorCount =  $parentCount = $studentCount = $levelCount = $employeeCount  = $suggestionCount  = 0;        

      

        $arr_final_tile = [];
        if($arr_current_user_access)
        {



            $URL     = url('/').'/'.$this->role.'/';
            $img_url = url('/').'/images/admin/';
            /******** Get all permissions given to logged user *******/
 			$professorCount = $this->get_user_details(config('app.project.role_slug.professor_role_slug'));
 			$employeeCount  = $this->get_user_details(config('app.project.role_slug.employee_role_slug'));
 			$parentCount    = $this->get_user_details(config('app.project.role_slug.parent_role_slug'));
 			$studentCount   = $this->get_user_details(config('app.project.role_slug.student_role_slug'));



 			$custom_table = $this->MemberShipModel->getTable();   
	        $prefixed_custom_table = DB::getTablePrefix().$this->MemberShipModel->getTable();
	        $membershipCount = DB::table($custom_table)
	                        ->select(DB::raw(   
	                                            $prefixed_custom_table.".id as id,".
	                                            $prefixed_custom_table.".plan_name as plan_name,".
	                                            $prefixed_custom_table.".duration_type as duration_type,".
	                                            $prefixed_custom_table.".duration_value as duration_value,".
	                                            $prefixed_custom_table.".price as price,".
	                                            $prefixed_custom_table.".stackholders as stackholders,".
	                                            $prefixed_custom_table.".is_active"
	                                        ))
	                                        ->whereNull($prefixed_custom_table.'.deleted_at')
	                                        ->orderBy($prefixed_custom_table.'.id','DESC')->count();


		  	$contactEnquieryCount =$this->ContactEnquiryModel->with(['enquiry_category' => function($ques){
           												 $ques->select('id','category_name');  }])->orderBy('id','DESC')->count();


	        

	        $suggestion_table           =  $this->SuggestionModel->getTable();                  
	        $prefixed_suggestion_table  =  DB::getTablePrefix().$this->SuggestionModel->getTable();
 			$suggestionCount = DB::table($suggestion_table)
	                                ->where($suggestion_table.'.status','=','APPROVED')
	                                ->orderBy($prefixed_suggestion_table.'.created_at','DESC')->count();


	        $level_details                  = $this->LevelModel->getTable();
	        $prefixed_level_details         = DB::getTablePrefix().$this->LevelModel->getTable();
	        $level_trans_details            = $this->LevelTranslationModel->getTable();
	        $prefixed_level_trans_details   = DB::getTablePrefix().$this->LevelTranslationModel->getTable();
	        $levelCount = DB::table($level_details)
	                                ->select(DB::raw($prefixed_level_details.".id as id,".
	                                                 $prefixed_level_details.".is_active as status,".
	                                                 $prefixed_level_trans_details.".level_name,".
	                                                 $prefixed_level_details.".level_order"))
	                                ->join($level_trans_details,$level_details.'.id','=',$level_trans_details.'.level_id')
	                                ->where($level_trans_details.'.locale','=',$this->locale)
	                                ->whereNull($level_details.'.deleted_at')
	                                ->orderBy($level_details.'.created_at','DESC')->count();                         

            if(in_array('users.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'users/professor',
                                      'fa_icons'        => '<i class="fa fa-users"></i>',
                                      'tile_color'      => 'border-bottm-1',
                                      'module_title'    => translation('professor'),
                                      'module_sub_title'=> translation('get_all_professor'),
                                      'total_count'     => $professorCount]; 
            }

            
            if(in_array('users.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'users/employee',
                                      'fa_icons'        => '<i class="fa fa-users"></i>',
                                      'tile_color'      => 'border-bottm-2',
                                      'module_title'    => translation('employee_staff'),
                                      'module_sub_title'=> translation('get_all_employees'),
                                      'total_count'     => $employeeCount]; 
            }

            if(in_array('users.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'users/parent',
                                      'fa_icons'        => '<i class="fa fa-users"></i>',
                                      'tile_color'      => 'border-bottm-3',
                                      'module_title'    => translation('parent'),
                                      'module_sub_title'=> translation('get_all_parents'),
                                      'total_count'     => $parentCount];       
            } 

            if(in_array('users.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'users/student',
                                      'fa_icons'        => '<i class="fa fa-users"></i>',
                                      'tile_color'      => 'border-bottm-4',
                                      'module_title'    => translation('student'),
                                      'module_sub_title'=> translation('get_all_students'),
                                      'total_count'     => $studentCount];       
            } 


              

            if(in_array('membership_plans.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'membership_plans',
                                      'fa_icons'        => '<i class="fa fa-bar-chart"></i>',
                                      'tile_color'      => 'border-bottm-5',
                                      'module_title'    => translation('membership_plan'),
                                      'module_sub_title'=> translation('check_membership_plans'),
                                      'total_count'     => $membershipCount];       
            } 


             if(in_array('contact_enquiry.list',$arr_current_user_access)){
                $arr_final_tile[] = [ 'module_url'      => $URL.'contact_enquiry',
                                      'fa_icons'        => '<i class="fa fa-info-circle"></i>',
                                      'tile_color'      => 'border-bottm-6',
                                      'module_title'    => translation('contact_enquiry'),
                                      'module_sub_title'=> translation('check_enquiries'),
                                      'total_count'     => $contactEnquieryCount];       
            }

            if(in_array('suggestions.list',$arr_current_user_access)){
              $arr_final_tile[] =   [   'module_url'      =>  $URL.'suggestions',
                                        'fa_icons'        => '<i class="fa fa-thumbs-up"></i>',
                                        'tile_color'      => 'border-bottm-7',
                                        'module_title'    => translation('suggestions'),
                                        'module_sub_title'=> translation('check_out_suggestions'),
                                        'total_count'     => $suggestionCount];       
                                   
            }
    		
    		if(in_array('level.list',$arr_current_user_access)){ 
                $arr_final_tile[] = [ 'module_url'      => $URL.'level',
                                      'fa_icons'        => '<i class="fa fa-graduation-cap"></i>',
                                      'tile_color'      => 'border-bottm-8',
                                      'module_title'    => translation('level'),
                                      'module_sub_title'=> translation('check_out_level'),
                                      'total_count'     => $levelCount];       
            }  
 
        }       
        return  $arr_final_tile;                          
    }
    

    function get_user_details($role){

        $user_details             = $this->UserModel->getTable();
        $prefixed_user_details    = DB::getTablePrefix().$this->UserModel->getTable();
        $user_role_table          = $this->UserRoleModel->getTable();

        $role_table               = $this->RoleModel->getTable();

        $user_trans_table             = $this->UserTranslationModel->getTable();                  
        $prefixed_user_trans_table    = DB::getTablePrefix().$this->UserTranslationModel->getTable();

    	$useCount = 0;
        $useCount = DB::table($user_details)
                                ->select(DB::raw($prefixed_user_details.".id as id,".
                                                 $prefixed_user_details.".email as email, ".
                                                 $prefixed_user_details.".is_active as is_active, ".
                                                 $prefixed_user_details.".last_login as last_login,".
                                                 
                                                 $role_table.".slug as role_slug,".
                                                 "CONCAT(".$prefixed_user_trans_table.".first_name,' ',"
                                                          .$prefixed_user_trans_table.".last_name) as user_name"
                                                 ))
                                ->join($user_role_table,$user_details.'.id','=',$user_role_table.'.user_id')
                                ->join($user_trans_table,$user_trans_table.'.user_id', ' = ',$user_details.'.id')
                                ->join($role_table, function ($join) use($role_table,$user_role_table,$role) {
                                    $join->on($role_table.'.id', '=',$user_role_table.'.role_id')
                                         ->where('slug','=',$role);
                                })
                                ->where($user_trans_table.'.locale','=',$this->locale)
                                ->orderBy($user_details.'.created_at','DESC')->count();

        return $useCount;
    }
}

