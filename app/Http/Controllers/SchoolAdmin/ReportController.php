<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Traits\MultiActionTrait;
use App\Models\UserModel;
use App\Models\UserRoleModel;   
use App\Models\RoleModel;
use App\Models\UserTranslationModel;


/*Activity Log */
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;   
/*Activity Log */


use Validator;
use Session;
use Flash;
use File;
use Sentinel;
use DB;
use Datatables;
use Excel;




class ReportController extends Controller
{
     use MultiActionTrait;
    
    public function __construct(ActivityLogsModel $activity_logs,
    	 						UserModel $user,
                                UserRoleModel $user_role_model,
                                UserTranslationModel $user_details_model,
                                RoleModel $role_model)
    {

        $this->ActivityLogsModel        = $activity_logs; /* Activity Model */
		$this->arr_view_data            = [];
        $this->module_url_path          = url(config('app.project.admin_panel_slug')."/report");
        $this->module_title             = "Report";
        $this->module_url_slug          = "report";
        $this->module_view_folder       = "admin.report";
			


		$this->UserModel                    = $user;
        $this->UserRoleModel                = $user_role_model;
        $this->UserTranslationModel        = $user_details_model;
        $this->RoleModel                    = $role_model;
		/* Activity Section */
        $this->obj_data          = Sentinel::getUser();
        $this->first_name        = $this->obj_data->first_name;
        $this->last_name         = $this->obj_data->last_name;
        $this->ip_address        = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;  
        /* Activity Section */


        $this->theme_color              = theme_color();
    }   

    /* NEW SCHOOL ADMIN */
    public function users(Request $request)
    { 
        $objcity = $arr_city = $objcountry = $arr_country = []; 
        $objcity = $this->UserTranslationModel->select('city')->where('city','<>','')->groupBy('city')->orderBy('city','ASC')->get();
        $objcountry = $this->UserTranslationModel->select('country')->where('country','<>','')->groupBy('country')->orderBy('country','ASC')->get();

        if(isset($objcity))
        {
          $arr_city = $objcity->toArray();
        }

        if(isset($objcountry))
        {
          $arr_country = $objcountry->toArray();
        }

        if(\Request::has('type'))
        {
            if((\Request::has('type') == 'schooladmin') || (\Request::has('type') =='parent')  || (\Request::has('type') == 'student') || (\Request::has('type') == 'professor') )
            {
                $type = $request->input('type');
            }
                
        } 

        if(\Request::has('status'))
        {
            $status =  \Request::get('status'); 
        } 

        if(\Request::has('city'))
        {
            $city =  \Request::get('city'); 
        } 
        if(\Request::has('country'))
        {
            $country =  \Request::get('country'); 
        } 

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        if(\Request::has('start_date') && \Request::has('end_date') && \Request::has('start_date')!='' && \Request::has('end_date')!='' )
        {   
              dd('date');
            $start_date = date_create($start_date);
              $end_date = date_create($end_date);

              $startDate = date_format($start_date,'Y-m-d');
              $endDate = date_format($end_date,'Y-m-d'); 
        
         }

        if(isset($type))
        {
            $role = config('app.project.role_slug.'.$type.'_role_slug');

            $user_details             = $this->UserModel->getTable();
            $prefixed_user_details    = DB::getTablePrefix().$this->UserModel->getTable();

            $user_details_table          = $this->UserTranslationModel->getTable();
            $prefixed_user_details_table = DB::getTablePrefix().$this->UserTranslationModel->getTable();

            $user_role_table          = $this->UserRoleModel->getTable();

            $role_table               = $this->RoleModel->getTable();


            $obj_user = [];
            $obj_user = DB::table($user_details)
                                    ->select(DB::raw($prefixed_user_details.".id as id,".
                                                     $prefixed_user_details.".email as email, ".
                                                     $prefixed_user_details.".mobile_no as mobile_no, ".
                                                     $prefixed_user_details.".is_active as is_active, ".
                                                     $prefixed_user_details.".birth_date as birth_date, ".
                                                     $prefixed_user_details.".gender as gender, ".
                                                     $prefixed_user_details.".address as address, ".
                                                     $prefixed_user_details.".last_login as last_login, ".
                                                     $prefixed_user_details.".created_at as created_at, ".
                                                     $role_table.".slug as role_slug, ".
                                                     $prefixed_user_details_table.".city as city, ".
                                                     $prefixed_user_details_table.".country as country, ".
                                                     $prefixed_user_details_table.".locale as locale, ".
                                                     "CONCAT(".$prefixed_user_details_table.".first_name,' ',"
                                                              .$prefixed_user_details_table.".last_name) as user_name"
                                                     ))
                                    ->join($user_details_table,$user_details.'.id','=',$user_details_table.'.user_id')
                                    ->join($user_role_table,$user_details.'.id','=',$user_role_table.'.user_id')
                                    ->join($role_table, function ($join) use($role_table,$user_role_table,$role) {
                                        $join->on($role_table.'.id', '=',$user_role_table.'.role_id')
                                             ->where('slug','=',$role);
                                    })
                                    ->where($user_details_table.'.locale','en');

                                if(isset($status))
                                {
                                    $obj_user->where($user_details.'.is_active',$status);
                                }

                                if(isset($city))
                                {
                                    $obj_user->where($prefixed_user_details_table.'.city',$city);
                                }

                                if(isset($country))
                                {
                                    $obj_user->where($prefixed_user_details_table.'.country',$country);
                                }

                                if((isset($startDate) && $startDate!="none" ) && (isset($endDate) && $endDate!="none" ))
                                {
                                     $obj_user->whereRaw("date(".$prefixed_user_details.".created_at) >='".$startDate."' and date(".$prefixed_user_details.".created_at) <='".$endDate."'");
                                    
                                }
            $obj_product =  $obj_user->orderBy($user_details.'.created_at','DESC')
                                    ->get();

            $this->arr_view_data['arr_country']     = $arr_country;
            $this->arr_view_data['arr_city']        = $arr_city;
            $this->arr_view_data['type']            = $type;
            $this->arr_view_data['obj_arr_data']    = $obj_product;
            $this->arr_view_data['page_title']      = "Manage ".$type.' '.str_plural($this->module_title);
            $this->arr_view_data['module_title']    = str_plural($this->module_title);
            $this->arr_view_data['module_url_path'] = $this->module_url_path;
            $this->arr_view_data['theme_color']     = $this->theme_color;
             
            return view($this->module_view_folder.'.users', $this->arr_view_data);
            }
        else
        {
            return redirect()->back();
        }
    }   

    public function exportUsers(Request $request)
    {  
    $page_title = $user_type = $start_date = $end_date = '' ; 
    $page_title ="User";  

        if(\Request::has('type'))
        {
            if((\Request::has('type') == 'schooladmin') || (\Request::has('type') =='parent')  || (\Request::has('type') == 'student') || (\Request::has('type') == 'professor') )
            {
                    $user_type =  \Request::get('type'); 
                    $page_title = "-".$user_type;    
            }
                
        } 

        if(\Request::has('status'))
        {
            $status =  \Request::get('status'); 
        } 

        if(\Request::has('city'))
        {
            $city =  \Request::get('city'); 
        } 
        if(\Request::has('country'))
        {
            $country =  \Request::get('country'); 
        } 

        if(\Request::has('start_date') && \Request::has('end_date') && \Request::has('start_date')!='' && \Request::has('end_date')!='' )
        {   
            $start_date =  \Request::get('start_date');
            $end_date   =  \Request::get('end_date');

            $start_date = date_create($start_date);
            $end_date = date_create($end_date);

            $startDate = date_format($start_date,'Y-m-d');
            $endDate = date_format($end_date,'Y-m-d'); 

            $page_title .= "-from-".date('d-m-Y',strtotime($startDate))." to ".date('d-m-Y',strtotime($endDate));   
        }

        $role = config('app.project.role_slug.'.$user_type.'_role_slug');

        $user_details             = $this->UserModel->getTable();
        $prefixed_user_details    = DB::getTablePrefix().$this->UserModel->getTable();

        $user_details_table          = $this->UserTranslationModel->getTable();
        $prefixed_user_details_table = DB::getTablePrefix().$this->UserTranslationModel->getTable();

        $user_role_table          = $this->UserRoleModel->getTable();

        $role_table               = $this->RoleModel->getTable();
         
        $res_query = '';
        $res_data = array();
        
        $res_query = DB::table($user_details)
                                    ->select(DB::raw($prefixed_user_details.".id as id,".
                                                     $prefixed_user_details.".email as email, ".
                                                     $prefixed_user_details.".mobile_no as mobile_no, ".
                                                     $prefixed_user_details.".is_active as is_active, ".
                                                     $prefixed_user_details.".birth_date as birth_date, ".
                                                     $prefixed_user_details.".gender as gender, ".
                                                     $prefixed_user_details.".address as address, ".
                                                     $prefixed_user_details.".last_login as last_login, ".
                                                     $prefixed_user_details.".created_at as created_at, ".
                                                     $role_table.".slug as role_slug, ".
                                                     $prefixed_user_details_table.".city as city, ".
                                                     $prefixed_user_details_table.".country as country, ".
                                                     $prefixed_user_details_table.".locale as locale, ".
                                                     "CONCAT(".$prefixed_user_details_table.".first_name,' ',"
                                                              .$prefixed_user_details_table.".last_name) as user_name"
                                                     ))
                                    ->join($user_details_table,$user_details.'.id','=',$user_details_table.'.user_id')
                                    ->join($user_role_table,$user_details.'.id','=',$user_role_table.'.user_id')
                                    ->join($role_table, function ($join) use($role_table,$user_role_table,$role) {
                                        $join->on($role_table.'.id', '=',$user_role_table.'.role_id')
                                             ->where('slug','=',$role);
                                    })
                                    ->where($user_details_table.'.locale','en');

                                    if(isset($status))
                                    {
                                        $res_query->where($user_details.'.is_active',$status);
                                    }
                                    if(isset($city))
                                    {
                                        $res_query->where($prefixed_user_details_table.'.city',$city);
                                    }

                                    if(isset($country))
                                    {
                                        $res_query->where($prefixed_user_details_table.'.country',$country);
                                    }
                                    if((isset($startDate) && $startDate!="none" ) && (isset($endDate) && $endDate!="none" ))
                                    {
                                         $res_query->whereRaw("date(".$prefixed_user_details.".created_at) >='".$startDate."' and date(".$prefixed_user_details.".created_at) <='".$endDate."'");
                                        
                                    }
                $res_data =  $res_query->orderBy($user_details.'.created_at','DESC')
                                    ->get();
        
            if(isset($res_data) && count($res_data)>0)
            {           
                $arr_tmp = array();
                 \Excel::create($page_title.'-'.date('d-m-Y'), function($excel) use($res_data) 
                  {
                      $excel->sheet('Client', function($sheet) use($res_data) 
                      {
                          $sheet->cell('A1', function($cell) 
                          {
                              $cell->setValue('Generated on :'.date("d-m-Y H:i:s"));
                          });

                          $sheet->row(2, array(
                                                   'Sr No',
                                                   'Email',
                                                   'Name',
                                                   'Phone',
                                                   'Date Of Birth',
                                                   'Gender',
                                                   'Address',
                                                   'City',
                                                   'Country'
                                              ));
                          $i=1;
                          
                          foreach($res_data as $key => $row)
                          {

                            $arr_tmp[$key][] = $i++;
                            $arr_tmp[$key][] = $row->email ? $row->email : '-';
                            $arr_tmp[$key][] = $row->user_name ? ucwords($row->user_name) : '-';
                            $arr_tmp[$key][] = $row->mobile_no ? $row->mobile_no : '-';
                            $arr_tmp[$key][] = $row->city ? $row->city : '-';
                            $arr_tmp[$key][] = $row->country ? $row->country : '-';

                          }

                          $sheet->rows($arr_tmp);                                      
                      });

                  })->export('xlsx');
            }
            else
            {
              Flash::error('No result found,for export');
              return redirect()->back();
            }                            

    }
    /* NEW USERS */
}
