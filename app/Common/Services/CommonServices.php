<?php

namespace App\Common\Services;

use App\Models\AdvertisementModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use App\Models\ReviewModel;
use App\Models\UsersAdvertismentPlanSubscriptionsModel;
use DB;


class CommonServices
{
	public function __construct()
	{
		
	}


    public function send_sms($msg,$mobile_number)
    {	
    	$username="SMS3"; 
		$password="8639400331";
		$message=$msg;
		$sender="SMS3"; //ex:INVITE GOT THIS ID FROM DASHBORAD
        $mobile_number=$mobile_number;
		$url="login.bulksmsgateway.in/sendmessage.php?user=".urlencode($username)."&password=".urlencode($password)."&mobile=".urlencode($mobile_number)."&message=".urlencode($message)."&sender=".urlencode($sender)."&type=".urlencode('3');
		//q2dd($url);
	    $ch = curl_init();
        $headers = array(
                'Accept: application/json',
                'Content-Type: application/json',
            );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch); 

        if(curl_errno($ch))
        {
            echo curl_error($ch);
        }
        else
        {
        	//echo 'done';
        }
        /*print_r($output);*/
        curl_close($ch);
	}

	

}?>