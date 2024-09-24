<?php
Route::group(['prefix' => $admin_path,'middleware'=>['admin']], function ()
	{

		$module_permission = "module_permission:";

		$route_slug        = "admin_auth_";
		$module_controller = "Admin\AuthController@";

	   /*----------------------------------------------------------------------------------------
			Admin Home Route
		----------------------------------------------------------------------------------------*/

		Route::any('/setLanguage',   													[	'as'	=> 'setLanguage',
																							'uses'	=> 'Controller@setLanguage']);

		Route::get('/',              													[	'as'	=> $route_slug.'login',
																							'uses'	=> $module_controller.'login']);

		Route::get('login',          													[	'as'	=> $route_slug.'login',
																							'uses'	=> $module_controller.'login']);
												
		Route::post('process_login',  													[	'as'	=> $route_slug.'process_login',
																							'uses'	=> $module_controller.'process_login']);

		Route::get('change_password', 													[	'as'	=> $route_slug.'change_password',
																							'uses'	=> $module_controller.'change_password']);

		Route::get('edit_profile', 													    [	'as'	=> $route_slug.'edit_profile',
																							'uses'	=> $module_controller.'edit_profile']);

		Route::post('update_password',													[	'as'	=> $route_slug.'change_password' ,
																							'uses'	=> $module_controller.'update_password']);

		Route::post('process_forgot_password',											[	'as'	=> $route_slug.'forgot_password',
																							'uses'	=> $module_controller.'process_forgot_password']);

		Route::get('validate_admin_reset_password_link/{enc_id}/{enc_reminder_code}', 	[	'as'	=> $route_slug.'validate_admin_reset_password_link',
																							'uses'	=> $module_controller.'validate_reset_password_link']);

		Route::post('reset_password',													[	'as'	=> $route_slug.'reset_passsword',
																							'uses'	=> $module_controller.'reset_password']);


		Route::get('/get_users/{user_type}',											[	'as'	=> $route_slug.'get_users',
																							'uses'	=>'Admin\DashboardController@get_users']);

		Route::any('get_cities',  														[   'as'    => 'get_cities',
																							'uses'	=> 'Controller@get_cities']);

		Route::any('get_countries',  													[   'as'    => 'get_countries',
																							'uses'	=> 'Controller@get_countries']);


		/*----------------------------------------------------------------------------------------
			Dashboard
		----------------------------------------------------------------------------------------*/

		Route::get('/dashboard',						[	'as'		=> $route_slug.'dashboard',
															'uses'		=>'Admin\DashboardController@index']);

		Route::get('/dashboard/get_dashboard_count',				[	'as'		=> $route_slug.'dashboard',
															'uses'		=>'Admin\DashboardController@get_dashboard_count']);

		Route::get('/logout',   						[	'as'		=> $route_slug.'logout',
															'uses'		=> 'SchoolAdmin\AuthController@logout']);

		/*********  Activity Log *************/
		Route::group(array('prefix' => '/activity_log'), function() use ($module_permission)
		{
			$route_slug       = "activity_log";
			$module_controller = "Admin\ActivityLogController@";
			$module_slug       = "activity_log";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_records',				[		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

		});

		/*----------------------------------------------------------------------------------------
			Faq 
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/faq'), function() use ($module_permission)
		{
			$route_slug       = 'admin_faq_';
			$route_controller = 'Admin\FAQController@';
			$module_slug       = "faq";

			Route::get('/',							[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);
			
			Route::get('/create',					[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses'		=> $route_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as' 		=> $route_slug.'deactivate',
											  				'uses' 		=> $route_controller.'deactivate',
											  				'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',				[		'as'		=> $route_slug.'multi_action',
															'uses' 		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);
	 		
		});


		// /*----------------------------------------------------------------------------------------
		// 	Contact Enquiry 
		// ----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/contact_enquiry'), function () use ($module_permission)
		{
			$route_slug       = "admin_contact_enquiry_";
			$module_slug       = "contact_enquiry";
			$route_controller = "Admin\ContactEnquiryController@";

			Route::get('/',					  		[		'as' 		=> $route_slug.'index',
													   		'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/view/{enc_id}',	   		[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/reply/{enc_id}',	   		[		'as' 		=> $route_slug.'reply',
															'uses'		=> $route_controller.'reply',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/send_reply',	   			[		'as' 		=> $route_slug.'send_reply',
															'uses'		=> $route_controller.'send_reply',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('delete/{enc_id}',	   		[		'as' 		=> $route_slug.'delete',
															'uses'		=> $route_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::post('multi_action',		   		[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('export',					[		'as' 		=> $route_slug.'export',
															'uses' 		=> $route_controller.'export',
															'middleware'=> $module_permission.$module_slug.'.export']);	

		});

		// /*----------------------------------------------------------------------------------------
		// 	Contact Enquiry Categories
		// ----------------------------------------------------------------------------------------*/


		Route::group(array('prefix' => '/enquiry_category'), function() use ($module_permission)
		{
			$route_slug       = "enquiry_category";
			$module_controller = "Admin\EnquiryCategoryController@";
			$module_slug       = "enquiry_category";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create/',					[		'as'		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses' 		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses'		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action', 			[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('delete/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);


		});


	 	/*----------------------------------------------------------------------------------------
		 Notifications
		----------------------------------------------------------------------------------------*/
		Route::group(array('prefix' => '/notification'), function() use ($module_permission)
		{
			$module_slug        = "notification";
			$module_controller = "Admin\NotificationController@";

			Route::get('/',								[		'as' => $module_slug.'index', 
														 		'uses' => $module_controller.'index',
														 		'middleware'=> $module_permission.$module_slug.'.list']);
			Route::any('/view/{enc_id}',				[		'as' => $module_slug.'view', 
														 		'uses' => $module_controller.'view',
														 		'middleware'=> $module_permission.$module_slug.'.list']); 
			Route::post('multi_action',            		[		'as'		=> $module_slug.'multi_action',
																'uses'		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.list']);	
			Route::get('delete/{enc_id}',          		[		'as'		=> $module_slug.'delete',
																'uses'		=> $module_controller.'delete',
																'middleware'=> $module_permission.$module_slug.'.delete']);	
		
			
		});		

	 	/*----------------------------------------------------------------------------------------
		 Report Section
		----------------------------------------------------------------------------------------*/

		Route::group(['prefix'=>'report'],function() use ($module_permission)
		{
			$route_slug       = "report_";
			$module_controller = "Admin\ReportController@";
			$module_slug       = "report";


			Route::get('/',            			[   'as'		=> $route_slug.'users',
													'uses'		=> $module_controller.'users',
													'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::any('/get_record/{type}',  	[   'as'		=> $route_slug.'get_record',
													'uses'		=> $module_controller.'get_record',
													'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_city',			  	[   'as'		=> $route_slug.'get_city',
													'uses'		=> $module_controller.'get_city',
													'middleware'=> $module_permission.$module_slug.'.list']);	
			
			Route::any('/exportUsers',			[   'as'		=> $route_slug.'exportUsers',
													'uses'		=> $module_controller.'exportUsers',
													'middleware'=> $module_permission.$module_slug.'.list']);
		});		

		// /*----------------------------------------------------------------------------------------
		//  Fees
		// ----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/fees'), function () use ($module_permission)
		{
			$route_slug       = "fees_";
			$module_slug       = "fees";
			$route_controller = "Admin\FeesController@";

			Route::get('/',					  		[		'as' 		=> $route_slug.'index',
													   		'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/create',	   				[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('/store',	   				[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.store']);

			Route::post('/update',	   				[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.store']);

			Route::any('/edit/{enc_id}',	   		[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
			 							   					'uses' 		=> $route_controller.'delete',
			 							   					'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses'		=> $route_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as' 		=> $route_slug.'deactivate',
											  				'uses' 		=> $route_controller.'deactivate',
											  				'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',		   		[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('get_records',		   		[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);	
		});

		/*----------------------------------------------------------------------------------------
		  Exam Period
		 ----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/exam_period'), function () use ($module_permission)
		{
			$route_slug       = "exam_period_";
			$module_slug       = "exam_period";
			$route_controller = "Admin\ExamPeriodController@";

			Route::get('/',					  		[		'as' 		=> $route_slug.'index',
													   		'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/create',	   		[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('/store',	   		[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.store']);

			Route::post('/update',	   		[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.store']);

			Route::any('/edit/{enc_id}',	   		[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
			 							   					'uses' 		=> $route_controller.'delete',
			 							   					'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses'		=> $route_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as' 		=> $route_slug.'deactivate',
											  				'uses' 		=> $route_controller.'deactivate',
											  				'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',		   		[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('get_records',		   		[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);	
		});

		/*----------------------------------------------------------------------------------------
		  School Template
		 ----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/school_template'), function () use ($module_permission)
		{
			$route_slug       = "school_template_";
			$module_slug       = "school_template";
			$route_controller = "Admin\SchoolTemplateController@";

			Route::get('/',					  		[		'as' 		=> $route_slug.'index',
													   		'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/create',	   				[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('/store',	   				[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.store']);

			Route::any('/update',	   				[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/view',	   				[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.view']);

			Route::any('/edit/{enc_id}',	   		[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
			 							   					'uses' 		=> $route_controller.'delete',
			 							   					'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses'		=> $route_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as' 		=> $route_slug.'deactivate',
											  				'uses' 		=> $route_controller.'deactivate',
											  				'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',		   		[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('get_records',		   		[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::get('change_to_require/{enc_id}',[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'change_to_require',
															'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::get('change_to_not_require/{enc_id}',[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'change_to_not_require',
															'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::any('rearrange_order_number',     [		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'rearrange_order_number',
															'middleware'=> $module_permission.$module_slug.'.list']);	
		});

		/*----------------------------------------------------------------------------------------
		  School
		 ----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/school'), function () use ($module_permission)
		{

			$route_slug       = "school_";
			$module_slug       = "school";
			$route_controller = "Admin\SchoolController@";

			Route::get('/create/{enc_id}',			[		'as' 		=> $route_slug.'create',
															'uses'		=> $route_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('/store',        			[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::any('/edit/{enc_id}',	   		[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('/update/{enc_id}',			[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);
		});

		/*----------------------------------------------------------------------------------------
			level
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/level'), function() use ($module_permission)
		{
			$route_slug       = 'admin_level_';
			$route_controller = 'Admin\LevelController@';
			$module_slug       = "level";

			Route::get('/',							[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);
			
			Route::get('/create',					[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

		    Route::post('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

			Route::get('/edit/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
															'uses'		=> $route_controller.'edit',
			 							 					'middleware'=> $module_permission.$module_slug.'.edit']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
			 							   					'uses' 		=> $route_controller.'delete',
			 							   					'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses'		=> $route_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as' 		=> $route_slug.'deactivate',
											  				'uses' 		=> $route_controller.'deactivate',
											  				'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',				[		'as'		=> $route_slug.'multi_action',
															'uses' 		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

	 		Route::post('checkLevel',				[		'as'		=> $route_slug.'checkLevel',
															'uses' 		=> $route_controller.'checkLevel',
															'middleware'=> $module_permission.$module_slug.'.checkLevel']);

	 		Route::any('get_records',				[		'as'		=> $route_slug.'get_records',
															'uses' 		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.get_records']);

	 		Route::any('rearrange_order_number',    [		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'rearrange_order_number',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('export',					[		'as' 		=> $route_slug.'export',
															'uses' 		=> $route_controller.'export',
															'middleware'=> $module_permission.$module_slug.'.export']);	

		});

		/*----------------------------------------------------------------------------------------
			membership_plans
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/membership_plans'), function() use ($module_permission)
		{
			$route_slug       = 'membership_plans';
			$route_controller = 'Admin\MembershipController@';
			$module_slug      = "membership_plans";

			Route::get('/',							[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);
			
			Route::get('/create',					[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

		    Route::post('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

			Route::get('/edit/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
															'uses'		=> $route_controller.'edit',
			 							 					'middleware'=> $module_permission.$module_slug.'.edit']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
			 							   					'uses' 		=> $route_controller.'delete',
			 							   					'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses'		=> $route_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as' 		=> $route_slug.'deactivate',
											  				'uses' 		=> $route_controller.'deactivate',
											  				'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',				[		'as'		=> $route_slug.'multi_action',
															'uses' 		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

	 		Route::post('checkLevel',				[		'as'		=> $route_slug.'checkLevel',
															'uses' 		=> $route_controller.'checkLevel',
															'middleware'=> $module_permission.$module_slug.'.checkLevel']);

	 		Route::any('get_records',				[		'as'		=> $route_slug.'get_records',
															'uses' 		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

		});

		/*----------------------------------------------------------------------------------------
			exam_type
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/exam_type'), function() use ($module_permission)
		{
			$route_slug       = 'admin_exam_type_';
			$route_controller = 'Admin\ExamTypeController@';
			$module_slug       = "exam_type";

			Route::get('/',							[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);
			
			Route::get('/create',					[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

		    Route::post('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

			Route::get('/edit/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
															'uses'		=> $route_controller.'edit',
			 							 					'middleware'=> $module_permission.$module_slug.'.edit']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
			 							   					'uses' 		=> $route_controller.'delete',
			 							   					'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses'		=> $route_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as' 		=> $route_slug.'deactivate',
											  				'uses' 		=> $route_controller.'deactivate',
											  				'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',				[		'as'		=> $route_slug.'multi_action',
															'uses' 		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

	 		Route::post('checkExamType',			[		'as'		=> $route_slug.'checkExamType',
															'uses' 		=> $route_controller.'checkExamType',
															'middleware'=> $module_permission.$module_slug.'.checkExamType']);

	 		Route::any('get_records',				[		'as'		=> $route_slug.'get_records',
															'uses' 		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.get_records']);

		});

		Route::group(['prefix'=>'keyword_translation'],function()
		{
			$route_slug        = "keyword_translation_";
			$module_controller = "Admin\KeywordTranslationController@";

			Route::get('/',['as'=>$route_slug.'index',
							 	    'uses'=>$module_controller.'index']);

			Route::get('get_records',['as' => $route_slug.'get_records',
							 			'uses' => $module_controller.'get_records']);

			Route::get('get_records',['as' => $route_slug.'get_records',
							 			'uses' => $module_controller.'get_records']);

			Route::get('edit/{enc_id}',['as' => $route_slug.'edit',
							 			'uses' => $module_controller.'edit']);

			Route::post('update/',['as' => $route_slug.'update',
													   'uses' => $module_controller.'update']);

			Route::get('create/',['as' => $route_slug.'create',
									  'uses' => $module_controller.'create']);

			Route::post('store/',['as' => $route_slug.'store',
				 					  'uses' => $module_controller.'store']);

		});


		// /*----------------------------------------------------------------------------------------
		// 	Admin Profile
		// ----------------------------------------------------------------------------------------*/

		$account_setting_controller = "Admin\ProfileController@";
		$account_settings_slug = "profile";


		Route::get('profile', 													       [	'as'	=> $account_settings_slug.'profile',
																							'uses'	=> $account_setting_controller.'index']);

		Route::any('/profile/update', 													[	'as'	=> $account_settings_slug.'update_profile',
																							'uses'	=> $account_setting_controller.'update']);

		

		Route::group(array('prefix' => '/sub_admin'), function() use ($module_permission)
		{
			$route_slug       = "sub_admin";
			$module_controller = "Admin\SubAdminController@";
			$module_slug       = "sub_admin";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create/',					[		'as'		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update',					[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses' 		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses'		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action', 			[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('delete/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);


		});

		// /*----------------------------------------------------------------------------------------
		// 	Payment Settings  
		// ----------------------------------------------------------------------------------------*/

		$payment_setting_controller = "Admin\PaymentSettingsController@";
		$payment_settings_slug	    = "payment_settings";
	

		Route::get('setting/payment_settings',                  [   'as'		=> $route_slug.'payment_settings_show',
															'uses' 		=> $payment_setting_controller.'index',
															'middleware'=> $module_permission.$payment_settings_slug.'.update']);


		Route::post('payment_settings/update', 			[   'as' 		=> $route_slug.'payment_settings_update',
															'uses' 		=> $payment_setting_controller.'update',
															'middleware'=> $module_permission.$payment_settings_slug.'.update']);

		
		// /*----------------------------------------------------------------------------------------
		// 	Admin Roles
		// ----------------------------------------------------------------------------------------*/


		Route::group(array('prefix' => '/admin_users'), function() use ($module_permission)
		{
			$route_slug       = "admin_users_";
			$module_slug       = "admin_users";
			$module_controller = "Admin\AdminUserController@";



			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/create',					[		'as' 		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create' ,
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('/store',		   			[		'as'		=> $route_slug.'store',
															'uses'  	=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('/edit/{enc_id}',    		[		'as'		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}', 			[		'as' 		=> $route_slug.'edit',
															'uses'		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('activate/{enc_id}', 		[		'as' 		=> $route_slug.'activate',
															'uses' 		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses' 		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);
		});

		// /*----------------------------------------------------------------------------------------
		// 	Users
		// ----------------------------------------------------------------------------------------*/


		Route::group(array('prefix' => '/users'), function() use ($module_permission)
		{
			$route_slug       = "users";
			$module_slug       = "users";
			$module_controller = "Admin\UserController@";



			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('activate/{enc_id}', 		[		'as' 		=> $route_slug.'activate',
															'uses' 		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses' 		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('get_records/{user_type}',   [		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			/*----------------------------------------------------------------------------------------
		 	parent
		 	----------------------------------------------------------------------------------------*/


			Route::group(array('prefix' => '/parent'), function() use ($module_permission)
			{
				$route_slug       = "parent";
				$module_slug       = "parent";
				$module_controller = "Admin\ParentController@";



				Route::get('/',							[		'as' 		=> $route_slug.'index',
																'uses'		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::get('activate/{enc_id}', 		[		'as' 		=> $route_slug.'activate',
																'uses' 		=> $module_controller.'activate',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
																'uses' 		=> $module_controller.'deactivate',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::any('get_records/{user_type}',   [		'as' 		=> $route_slug.'get_records',
																'uses' 		=> $module_controller.'get_records',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
																'uses' 		=> $module_controller.'view',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::any('export',					[		'as' 		=> $route_slug.'export',
																'uses' 		=> $module_controller.'export',
																'middleware'=> $module_permission.$module_slug.'.export']);
			});

			/*----------------------------------------------------------------------------------------
			 	student
			 ----------------------------------------------------------------------------------------*/


			Route::group(array('prefix' => '/student'), function() use ($module_permission)
			{
				$route_slug       = "student";
				$module_slug       = "student";
				$module_controller = "Admin\StudentController@";



				Route::get('/',							[		'as' 		=> $route_slug.'index',
																'uses'		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::get('activate/{enc_id}', 		[		'as' 		=> $route_slug.'activate',
																'uses' 		=> $module_controller.'activate',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
																'uses' 		=> $module_controller.'deactivate',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::any('get_records/{user_type}',   [		'as' 		=> $route_slug.'get_records',
																'uses' 		=> $module_controller.'get_records',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
																'uses' 		=> $module_controller.'view',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::any('export',					[		'as' 		=> $route_slug.'export',
																'uses' 		=> $module_controller.'export',
																'middleware'=> $module_permission.$module_slug.'.export']);
			});

			/*----------------------------------------------------------------------------------------
			 	professor
			 ----------------------------------------------------------------------------------------*/


			Route::group(array('prefix' => '/professor'), function() use ($module_permission)
			{
				$route_slug       = "professor";
				$module_slug       = "professor";
				$module_controller = "Admin\ProfessorController@";



				Route::get('/',							[		'as' 		=> $route_slug.'index',
																'uses'		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::get('activate/{enc_id}', 		[		'as' 		=> $route_slug.'activate',
																'uses' 		=> $module_controller.'activate',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
																'uses' 		=> $module_controller.'deactivate',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::any('get_records/{user_type}',   [		'as' 		=> $route_slug.'get_records',
																'uses' 		=> $module_controller.'get_records',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
																'uses' 		=> $module_controller.'view',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::any('export',					[		'as' 		=> $route_slug.'export',
																'uses' 		=> $module_controller.'export',
																'middleware'=> $module_permission.$module_slug.'.export']);
			});

			/*----------------------------------------------------------------------------------------
			 	employee
			 ----------------------------------------------------------------------------------------*/


			Route::group(array('prefix' => '/employee'), function() use ($module_permission)
			{
				$route_slug       = "employee";
				$module_slug       = "employee";
				$module_controller = "Admin\EmployeeController@";



				Route::get('/',							[		'as' 		=> $route_slug.'index',
																'uses'		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::get('activate/{enc_id}', 		[		'as' 		=> $route_slug.'activate',
																'uses' 		=> $module_controller.'activate',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
																'uses' 		=> $module_controller.'deactivate',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::any('get_records/{user_type}',   [		'as' 		=> $route_slug.'get_records',
																'uses' 		=> $module_controller.'get_records',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
																'uses' 		=> $module_controller.'view',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::any('export',					[		'as' 		=> $route_slug.'export',
																'uses' 		=> $module_controller.'export',
																'middleware'=> $module_permission.$module_slug.'.export']);
			});
		});

		
		// /*----------------------------------------------------------------------------------------
		// 	Static Pages - CMS
		// ----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/static_pages'), function() use ($module_permission)
		{
			$route_slug        = "static_pages_";
			$module_controller = "Admin\StaticPageController@";
			$module_slug       = "static_pages";

			Route::get('/', 				 		[		'as'	 	=> $route_slug.'manage',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create',			 		[		'as' 		=> $route_slug.'create',
															'uses'		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('edit/{enc_id}',		 		[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('store',				 		[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);


			Route::post('update',	 				[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('delete/{enc_id}',	 		[		'as' 		=> $route_slug.'delete',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.detete']);


			Route::get('activate/{enc_id}',  		[		'as' 		=> $route_slug.'activate',
															'uses'		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses' 		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);


			Route::post('multi_action',		 		[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);
		});


		// /*---------------------------------------------------------------------------------------
		// |	Email Template
		// -----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/email_template'), function() use ($module_permission)
		{
			$route_slug        = "admin_email_template_";
			$module_controller = "Admin\EmailTemplateController@";
			$module_slug	   = 'email_template';

			Route::get('create',					[		'as'		=> $route_slug.'create',
								 						 	'uses' 		=> $module_controller.'create',
								 						 	'middleware'=> $module_permission.$module_slug.'.create']);


			Route::post('store/',					[		'as' 		=> $route_slug.'store',
			 					  							'uses' 		=> $module_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.create']);


			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
				 											'uses' 		=> $module_controller.'edit',
				 											'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('view/{enc_id}/{act_lng}',	[		'as' 		=> $route_slug.'edit',
				 											'uses' 		=> $module_controller.'view',
				 											'middleware'=> $module_permission.$module_slug.'.list']);


			Route::post('update/{enc_id}',			[		'as'		=> $route_slug.'update',
										   					'uses' 		=> $module_controller.'update',
										   					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('change_enabled',			[		'as'		=>	$route_slug.'change_enabled',
															'uses'      =>	$module_controller.'change_enabled',
															'middleware'=>	$module_permission.$module_slug.'.change_enabled']);

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);
		});

		// /*----------------------------------------------------------------------------------------
		// 	Admin Roles
		// ----------------------------------------------------------------------------------------*/


		Route::group(array('prefix' => '/users'), function() use ($module_permission)
		{
			$route_slug       = "users";
			$module_slug       = "users";
			$module_controller = "Admin\UserController@";



			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('activate/{enc_id}', 		[		'as' 		=> $route_slug.'activate',
															'uses' 		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses' 		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('get_records/{user_type}',   [		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			/*----------------------------------------------------------------------------------------
		 	parent
		 ----------------------------------------------------------------------------------------*/


				Route::group(array('prefix' => '/parent'), function() use ($module_permission)
				{
					$route_slug       = "parent";
					$module_slug       = "parent";
					$module_controller = "Admin\ParentController@";



					Route::get('/',							[		'as' 		=> $route_slug.'index',
																	'uses'		=> $module_controller.'index',
																	'middleware'=> $module_permission.$module_slug.'.list']);

					Route::get('activate/{enc_id}', 		[		'as' 		=> $route_slug.'activate',
																	'uses' 		=> $module_controller.'activate',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
																	'uses' 		=> $module_controller.'deactivate',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																	'uses' 		=> $module_controller.'multi_action',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::any('get_records/{user_type}',   [		'as' 		=> $route_slug.'get_records',
																	'uses' 		=> $module_controller.'get_records',
																	'middleware'=> $module_permission.$module_slug.'.list']);

					Route::any('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
																	'uses' 		=> $module_controller.'view',
																	'middleware'=> $module_permission.$module_slug.'.update']);
				});

				/*----------------------------------------------------------------------------------------
				 	student
				 ----------------------------------------------------------------------------------------*/


				Route::group(array('prefix' => '/student'), function() use ($module_permission)
				{
					$route_slug       = "student";
					$module_slug       = "student";
					$module_controller = "Admin\StudentController@";



					Route::get('/',							[		'as' 		=> $route_slug.'index',
																	'uses'		=> $module_controller.'index',
																	'middleware'=> $module_permission.$module_slug.'.list']);

					Route::get('activate/{enc_id}', 		[		'as' 		=> $route_slug.'activate',
																	'uses' 		=> $module_controller.'activate',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
																	'uses' 		=> $module_controller.'deactivate',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																	'uses' 		=> $module_controller.'multi_action',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::any('get_records/{user_type}',   [		'as' 		=> $route_slug.'get_records',
																	'uses' 		=> $module_controller.'get_records',
																	'middleware'=> $module_permission.$module_slug.'.list']);

					Route::any('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
																	'uses' 		=> $module_controller.'view',
																	'middleware'=> $module_permission.$module_slug.'.update']);
				});

				/*----------------------------------------------------------------------------------------
				 	professor
				 ----------------------------------------------------------------------------------------*/


				Route::group(array('prefix' => '/professor'), function() use ($module_permission)
				{
					$route_slug       = "professor";
					$module_slug       = "professor";
					$module_controller = "Admin\ProfessorController@";



					Route::get('/',							[		'as' 		=> $route_slug.'index',
																	'uses'		=> $module_controller.'index',
																	'middleware'=> $module_permission.$module_slug.'.list']);

					Route::get('activate/{enc_id}', 		[		'as' 		=> $route_slug.'activate',
																	'uses' 		=> $module_controller.'activate',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
																	'uses' 		=> $module_controller.'deactivate',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																	'uses' 		=> $module_controller.'multi_action',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::any('get_records/{user_type}',   [		'as' 		=> $route_slug.'get_records',
																	'uses' 		=> $module_controller.'get_records',
																	'middleware'=> $module_permission.$module_slug.'.list']);

					Route::any('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
																	'uses' 		=> $module_controller.'view',
																	'middleware'=> $module_permission.$module_slug.'.update']);
				});

				/*----------------------------------------------------------------------------------------
				 	employee
				 ----------------------------------------------------------------------------------------*/


				Route::group(array('prefix' => '/employee'), function() use ($module_permission)
				{
					$route_slug       = "employee";
					$module_slug       = "employee";
					$module_controller = "Admin\EmployeeController@";



					Route::get('/',							[		'as' 		=> $route_slug.'index',
																	'uses'		=> $module_controller.'index',
																	'middleware'=> $module_permission.$module_slug.'.list']);

					Route::get('activate/{enc_id}', 		[		'as' 		=> $route_slug.'activate',
																	'uses' 		=> $module_controller.'activate',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
																	'uses' 		=> $module_controller.'deactivate',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																	'uses' 		=> $module_controller.'multi_action',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::any('get_records/{user_type}',   [		'as' 		=> $route_slug.'get_records',
																	'uses' 		=> $module_controller.'get_records',
																	'middleware'=> $module_permission.$module_slug.'.list']);

					Route::any('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
																	'uses' 		=> $module_controller.'view',
																	'middleware'=> $module_permission.$module_slug.'.update']);
				});

		
		});		

		/*----------------------------------------------------------------------------------------
				 	school_admin
		----------------------------------------------------------------------------------------*/


				Route::group(array('prefix' => '/school_admin'), function() use ($module_permission)
				{
					$route_slug       = "school_admin";
					$module_slug       = "school_admin";
					$module_controller = "Admin\SchoolAdminController@";



					Route::get('/',							[		'as' 		=> $route_slug.'index',
																	'uses'		=> $module_controller.'index',
																	'middleware'=> $module_permission.$module_slug.'.list']);

					Route::get('activate/{enc_id}', 		[		'as' 		=> $route_slug.'activate',
																	'uses' 		=> $module_controller.'activate',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::get('create',			 		[		'as' 		=> $route_slug.'create',
																	'uses' 		=> $module_controller.'create',
																	'middleware'=> $module_permission.$module_slug.'.create']);

					Route::get('edit/{enc_id}',		 		[		'as' 		=> $route_slug.'edit',
																	'uses' 		=> $module_controller.'edit',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::post('update/{enc_id}',			[		'as' 		=> $route_slug.'update',
																	'uses' 		=> $module_controller.'update',
																	'middleware'=> $module_permission.$module_slug.'.update']);


					Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
																	'uses' 		=> $module_controller.'deactivate',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																	'uses' 		=> $module_controller.'multi_action',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::post('store',					[		'as' 		=> $route_slug.'store',
																	'uses' 		=> $module_controller.'store',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::post('checkEmail',				[		'as' 		=> $route_slug.'checkEmail',
																	'uses' 		=> $module_controller.'checkEmail',
																	'middleware'=> $module_permission.$module_slug.'.list']);

					Route::any('get_records/{user_type}',   [		'as' 		=> $route_slug.'get_records',
																	'uses' 		=> $module_controller.'get_records',
																	'middleware'=> $module_permission.$module_slug.'.list']);

					Route::any('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
																	'uses' 		=> $module_controller.'view',
																	'middleware'=> $module_permission.$module_slug.'.update']);
					
					Route::any('export',					[		'as' 		=> $route_slug.'export',
															'uses' 		=> $module_controller.'export',
															'middleware'=> $module_permission.$module_slug.'.export']);
				});
		/*----------------------------------------------------------------------------------------
			level
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/level'), function() use ($module_permission)
		{
			$route_slug       = 'admin_level_';
			$route_controller = 'Admin\LevelController@';
			$module_slug       = "level";

			Route::get('/',							[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);
			
			Route::get('/create',					[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

		    Route::post('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

			Route::get('/edit/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
															'uses'		=> $route_controller.'edit',
			 							 					'middleware'=> $module_permission.$module_slug.'.edit']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
			 							   					'uses' 		=> $route_controller.'delete',
			 							   					'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses'		=> $route_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as' 		=> $route_slug.'deactivate',
											  				'uses' 		=> $route_controller.'deactivate',
											  				'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',				[		'as'		=> $route_slug.'multi_action',
															'uses' 		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

	 		Route::post('checkLevel',				[		'as'		=> $route_slug.'checkLevel',
															'uses' 		=> $route_controller.'checkLevel',
															'middleware'=> $module_permission.$module_slug.'.checkLevel']);

	 		Route::any('get_records',				[		'as'		=> $route_slug.'get_records',
															'uses' 		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.get_records']);

		});

		/*----------------------------------------------------------------------------------------
			exam_type
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/exam_type'), function() use ($module_permission)
		{
			$route_slug       = 'admin_exam_type_';
			$route_controller = 'Admin\ExamTypeController@';
			$module_slug       = "exam_type";

			Route::get('/',							[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);
			
			Route::get('/create',					[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

		    Route::post('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

			Route::get('/edit/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
															'uses'		=> $route_controller.'edit',
			 							 					'middleware'=> $module_permission.$module_slug.'.edit']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
			 							   					'uses' 		=> $route_controller.'delete',
			 							   					'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses'		=> $route_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as' 		=> $route_slug.'deactivate',
											  				'uses' 		=> $route_controller.'deactivate',
											  				'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',				[		'as'		=> $route_slug.'multi_action',
															'uses' 		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

	 		Route::post('checkExamType',			[		'as'		=> $route_slug.'checkExamType',
															'uses' 		=> $route_controller.'checkExamType',
															'middleware'=> $module_permission.$module_slug.'.checkExamType']);

	 		Route::any('get_records',				[		'as'		=> $route_slug.'get_records',
															'uses' 		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.get_records']);

		});
		/*----------------------------------------------------------------------------------------
				 	suggestion
		----------------------------------------------------------------------------------------*/


		Route::group(array('prefix' => '/suggestions'), function() use ($module_permission)
		{
			$route_slug       = "suggestions";
			$module_slug       = "suggestions";
			$module_controller = "Admin\SuggestionsController@";


			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('get_records',   			[		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('export',					[		'as' 		=> $route_slug.'export',
															'uses' 		=> $module_controller.'export',
															'middleware'=> $module_permission.$module_slug.'.export']);
		});


		// /*----------------------------------------------------------------------------------------
		// 	Site Settings
		// ----------------------------------------------------------------------------------------*/
		$module_slug ='site_settings';

		Route::get('site_settings', 				[		'as' 		=> 'site_settings',
															'uses' 		=> 'Admin\SiteSettingController@index',
															'middleware'=> $module_permission.$module_slug.'.update']);

		Route::post('site_settings/update/{enc_id}',[		'as' 		=> 'site_settings',
																'uses' 		=> 'Admin\SiteSettingController@update',
																'middleware'=> $module_permission.$module_slug.'.update']);

		// /*---------------------------------------------------------------------------------------
		// |	Role
		// -----------------------------------------------------------------------------------------*/
			
		Route::group(array('prefix' => '/role'), function() use ($module_permission)
		{
			$route_slug        = "role_";
			$module_controller = "Admin\RoleController@";
			$module_slug	   = 'role';

			Route::get('/',					[		'as'		=> $route_slug.'list',
								 						 	'uses' 		=> $module_controller.'index',
								 						 	'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('approve/{enc_id}',					[		'as'		=> $route_slug.'approve',
								 						 	'uses' 		=> $module_controller.'approve',
								 						 	'middleware'=> $module_permission.$module_slug.'.approve']);


			Route::get('reject/{enc_id}',					[		'as' 		=> $route_slug.'reject',
			 					  							'uses' 		=> $module_controller.'reject',
			 					  							'middleware'=> $module_permission.$module_slug.'.reject']);

			Route::get('create',					[		'as'		=> $route_slug.'create',
								 						 	'uses' 		=> $module_controller.'create',
								 						 	'middleware'=> $module_permission.$module_slug.'.create']);


			Route::post('store/',					[		'as' 		=> $route_slug.'store',
			 					  							'uses' 		=> $module_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
				 											'uses' 		=> $module_controller.'edit',
				 											'middleware'=> $module_permission.$module_slug.'.update']);



			Route::post('update/{enc_id}',			[		'as'		=> $route_slug.'update',
										   					'uses' 		=> $module_controller.'update',
										   					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/get_roles',							[		'as' 		=> $route_slug.'get_roles',
															'uses' 		=> $module_controller.'get_roles',
															'middleware'=> $module_permission.$module_slug.'.list']);



		});



		/*---------------------------------------------------------------------------------------
		|	Email Template
		/* -----------------------------------------------------------------------------------------*/
		Route::group(array('prefix' => '/sms_template'), function() use ($module_permission)
		{
			$route_slug        = "admin_sms_template_";
			$module_controller = "Admin\SchoolSmsTemplateController@";
			$module_slug	   = 'sms_template';

			Route::get('create',					[		'as'		=> $route_slug.'create',
								 						 	'uses' 		=> $module_controller.'create',
								 						 	'middleware'=> $module_permission.$module_slug.'.create']);


			Route::post('store/',					[		'as' 		=> $route_slug.'store',
			 					  							'uses' 		=> $module_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.create']);


			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
				 											'uses' 		=> $module_controller.'edit',
				 											'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('view/{enc_id}/{act_lng}',	[		'as' 		=> $route_slug.'edit',
				 											'uses' 		=> $module_controller.'view',
				 											'middleware'=> $module_permission.$module_slug.'.list']);


			Route::post('update/{enc_id}',			[		'as'		=> $route_slug.'update',
										   					'uses' 		=> $module_controller.'update',
										   					'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('change_enabled',			[		'as'		=>	$route_slug.'change_enabled',
															'uses'      =>	$module_controller.'change_enabled',
															'middleware'=>	$module_permission.$module_slug.'.change_enabled']);
		});


		/*----------------------------------------------------------------------------------------
			notification modules
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/notification_modules'), function() use ($module_permission)
		{
			$route_slug       = 'admin_exam_type_';
			$route_controller = 'Admin\NotificationModulesController@';
			$module_slug       = "notification_modules";

			Route::get('/{role}',					[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);
			
			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses'		=> $route_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as' 		=> $route_slug.'deactivate',
											  				'uses' 		=> $route_controller.'deactivate',
											  				'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',				[		'as'		=> $route_slug.'multi_action',
															'uses' 		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

		});

  });
