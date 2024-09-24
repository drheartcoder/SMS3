<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Events\ActivityLogEvent;

use Session;
use App;

use App\Models\CityModel;
use App\Models\CountryModel;
use App\Models\StateModel;



class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /*---------------------------------------------------------
    |Activity Log
    ---------------------------------------------------------*/
    public function save_activity($ARR_DATA = [])
    {  
        
        if(isset($ARR_DATA) && sizeof($ARR_DATA)>0)
        {
            if(\Request::segment(2)!='' && sizeof(\Request::segment(2))>0)
            {    
                $ARR_EVENT_DATA                 = [];
                $ARR_EVENT_DATA['module_title'] = $ARR_DATA['MODULE_TITLE'];
                $ARR_EVENT_DATA['module_action']= $ARR_DATA['ACTION'];
              	
                event(new ActivityLogEvent($ARR_EVENT_DATA));

                return true;
            }
            return false;    
        }
        return false;
    }
    /*-------------------------------------------------------*/

    public function build_response( $status = 'success',
                                    $message = "",
                                    $arr_data = [],
                                    $response_format = 'json',
                                    $response_code = 200)
    {
        if($response_format == 'json')
        {
            $arr_response = [
                'status' => $status,
                'msg' => $message
            ];

            if(sizeof($arr_data)>0)
            {
                $arr_response['data'] = $arr_data;
            }
            return response()->json($arr_response,$response_code,[],JSON_UNESCAPED_UNICODE);    
        }   
    } 

    public function setSession(Request $request)
    {
        
        \Session::forget('academic_year');

        \Session::set('academic_year',$request->input('year'));
        
        return response()->json(array('status'=>'success'));
    }
            
    public function setLanguage(Request $request){


        \Session::forget('locale');
        
        \Session::set('locale',$request->input('lang'));
                
        \App::setlocale($request->input('lang'));
        
        return response()->json(array('status'=>'success'));
    }
     public function change_first_time1(Request $request)
    {
        $user = \Sentinel::check();
        
        $user->first_time_login = date('Y-m-d H:i:s');

        $user->save();
        return;
    }  

    public function get_cities(Request $request){
        
        $options    = "";
        $states_id  = $arr_cities = [];
        $country    = CountryModel::select('id')->where('country_name',$request->country)->first();
        $states     = StateModel::select('id')->where('country_id',$country->id)->get();

        if(isset($states) && $states!=null && count($states)>0)
        {
            $arr_states = $states->toArray();
            if(isset($arr_states) && count($arr_states)>0)
            {
                foreach ($arr_states as $key => $state) {
                    array_push($states_id, $state['id']);
                }
            }
            
            if(count($states_id)>0)  
            {
                $arr_cities = CityModel::where('city_name','like', '%'.$request->keyword.'%')->whereIn('state_id',$states_id)->groupBy('city_name')->get();  
            }
        }

        if(count($arr_cities)>0)
        {
            $options .= '<ul id="city-list">';
            foreach($arr_cities as $city)
            {
                 $options .= '<li onClick="hideBox(\''. $city->city_name .'\')">'.$city->city_name.'</li>';
            }
        }
        
        return $options;
    }  
    public function get_countries(Request $request){
       
        $options = "";
        $arr_countries = CountryModel::where('country_name','like', '%'.$request->keyword.'%')->get();

        if(count($arr_countries)>0)
        {
            $options .= '<ul id="country-list">';
            foreach($arr_countries as $country)
            {
                $options .= '<li onClick="selectCity(\''. $country->country_name .'\')">'.$country->country_name.'</li>';
            }
        }
        
        return $options;
    }  
}
