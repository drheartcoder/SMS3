<?php

namespace App\Common\Services;

use App\Models\EmailTemplateModel;
use App\Models\SchoolEmailTemplateModel;

use \Session;
use \Mail;

class EmailService
{
	public function __construct(
								EmailTemplateModel $email,
								SchoolEmailTemplateModel $school_email
							   )
	{
		$this->EmailTemplateModel  		= $email;
		$this->SchoolEmailTemplateModel = $school_email;
		$this->BaseModel           		= $this->EmailTemplateModel;

	}
	/*************** send mail to particular user ***************/
	public function send_mail($arr_mail_data = FALSE,$school_id=FALSE)
	{
		
		if(isset($arr_mail_data) && sizeof($arr_mail_data)>0)
		{
			$arr_email_template = [];
			$obj_email_template = '';
			if(isset($school_id) && $school_id!=null)
			{
				$obj_email_template = $this->SchoolEmailTemplateModel
										   ->where('slug',$arr_mail_data['email_template_slug'])
										   ->where('school_id',$school_id)
										   ->where('is_enabled',1)
										   ->first();
			}
			else
			{
				$obj_email_template = $this->EmailTemplateModel
										   ->where('slug',$arr_mail_data['email_template_slug'])
										   ->where('is_enabled',1)
										   ->first();
			}

			
			if($obj_email_template)
	      	{
	        	$arr_email_template = $obj_email_template->toArray();
	        	$user               = $arr_mail_data['user'];
	        	
	        	if(isset($arr_email_template['template_html']))
	        	{
		        	$content = $arr_email_template['template_html'];
		        					
		        	if(isset($arr_mail_data['arr_built_content']) && sizeof($arr_mail_data['arr_built_content'])>0)
		        	{
		        		foreach($arr_mail_data['arr_built_content'] as $key => $data)
		        		{
		        			$content = str_replace("##".$key."##",$data,$content);
		        		}
		        	}
		        	
		        	$content = view('email.front_general',compact('content'))->render();
		        	$content = html_entity_decode($content);
		        	
		        	$send_mail = Mail::send(array(),array(), function($message) use($user,$arr_email_template,$content){
			        	$name = isset($user['first_name']) ? $user['first_name']:"";
				        $message->from($arr_email_template['template_from_mail'], $arr_email_template['template_from']);
				        //$message->to('kuzazidok@veanlo.com', $name)
				        $message->to($user['email'], $name)
						          ->subject($arr_email_template['template_subject'])
						          ->setBody($content, 'text/html');
			        });

			        return $send_mail;
		        }
	        }
	    }
	    return false;    
	}
}

?>