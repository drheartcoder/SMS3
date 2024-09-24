<?php
	Route::group(['prefix' => $schooladmin_path,'middleware'=>['schooladmin']], function ()
	{

		$module_permission = "module_permission:";

		$route_slug        = "schooladmin_auth_";
		$module_controller = "SchoolAdmin\AuthController@";

	   /*----------------------------------------------------------------------------------------
			Admin Home Route
		----------------------------------------------------------------------------------------*/
		Route::any('/setSession',   			[	'as'	=> 'setSession',
													'uses'	=> 'Controller@setSession']);

		/*Route::any('/setLanguage',   			[	'as'	=> 'setLanguage',
													'uses'	=> 'Controller@setLanguage']);*/

		Route::any('/change_first_time',		[	'as'	=> $route_slug.'change_first_time',
													'uses'	=> 'Controller@change_first_time1']);

		Route::get('/',              			[	'as'	=> $route_slug.'login',
													'uses'	=> $module_controller.'login']);
		
		Route::get('login',          			[	'as'	=> $route_slug.'login',
													'uses'	=> $module_controller.'login']);
												
		/*Route::post('process_login',  			[	'as'	=> $route_slug.'process_login',
													'uses'	=> $module_controller.'process_login']);

		Route::get('role_login',    			[	'as'	=> $route_slug.'role_login',
													'uses'	=> $module_controller.'role_login']);*/

		Route::get('change_password', 			[	'as'	=> $route_slug.'change_password',
													'uses'	=> $module_controller.'change_password']);

		Route::get('edit_profile', 				[	'as'	=> $route_slug.'edit_profile',
													'uses'	=> $module_controller.'edit_profile']);

		Route::post('update_password',			[	'as'	=> $route_slug.'change_password' ,
													'uses'	=> $module_controller.'update_password']);

		/*Route::post('process_forgot_password',	[	'as'	=> $route_slug.'forgot_password',
													'uses'	=> $module_controller.'process_forgot_password']);

		Route::get('validate_admin_reset_password_link/{enc_id}/{enc_reminder_code}', 	[	'as'	=> $route_slug.'validate_admin_reset_password_link',
																							'uses'	=> $module_controller.'validate_reset_password_link']);

		Route::post('reset_password',			[	'as'	=> $route_slug.'reset_password',
													'uses'	=> $module_controller.'reset_password']);*/

		Route::get('/get_users/{user_type}',	[	'as'		=> $route_slug.'get_users',
													'uses'		=>'Admin\DashboardController@get_users']);

		Route::any('change_email',  			[   'as'    => 'change_email',
															'uses'	=> 'ProfleController@change_email']);


		Route::any('get_cities',  				[   'as'    => 'get_cities',
													'uses'	=> 'Controller@get_cities']);

		Route::any('get_countries',  			[   'as'    => 'get_countries',
													'uses'	=> 'Controller@get_countries']);


		/*----------------------------------------------------------------------------------------
			Dashboard
		----------------------------------------------------------------------------------------*/

		Route::get('/dashboard',						[	'as'		=> $route_slug.'dashboard',
															'uses'		=>'SchoolAdmin\DashboardController@index']);

		Route::get('/dashboard/get_dashboard_count',				[	'as'		=> $route_slug.'dashboard',
															'uses'		=>'Admin\DashboardController@get_dashboard_count']);

		/*Route::get('/logout',   						[	'as'		=> $route_slug.'logout',
															'uses'		=> $module_controller.'logout']);*/
												
		/*********  Activity Log *************/
		Route::group(array('prefix' => '/activity_log'), function() use ($module_permission)
		{
			$route_slug       = "activity_log";
			$module_controller = "SchoolAdmin\ActivityLogController@";
			$module_slug       = "activity_log";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_records',				[		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);


		});

		// /*----------------------------------------------------------------------------------------
		// 	Payment Settings  
		// ----------------------------------------------------------------------------------------*/

		$payment_setting_controller = "SchoolAdmin\PaymentSettingsController@";
		$payment_settings_slug	    = "payment_settings";
	

		Route::get('setting/payment_settings',                  [   'as'		=> $route_slug.'payment_settings_show',
															'uses' 		=> $payment_setting_controller.'index',
															'middleware'=> $module_permission.$payment_settings_slug.'.update']);


		Route::post('payment_settings/update', 			[   'as' 		=> $route_slug.'payment_settings_update',
															'uses' 		=> $payment_setting_controller.'update',
															'middleware'=> $module_permission.$payment_settings_slug.'.update']);
	

		/*----------------------------------------------------------------------------------------
			exam_type
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/exam_type'), function() use ($module_permission)
		{
			$route_slug       = 'schooladmin_exam_type_';
			$route_controller = 'SchoolAdmin\ExamTypeController@';
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

	 		Route::get('upload/{enc_id}',					[		'as'		=> $route_slug.'upload',
															'uses' 		=> $route_controller.'upload',
															'middleware'=> $module_permission.$module_slug.'.upload']);

		});

		/*----------------------------------------------------------------------------------------
			assessment_scale
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/assessment_scale'), function() use ($module_permission)
		{
			$route_slug       = 'schooladmin_assessment_scale_';
			$route_controller = 'SchoolAdmin\AssessmentScaleController@';
			$module_slug       = "assessment_scale";

			Route::get('/{enc_id?}',				[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);			
																					
			Route::get('/create',					[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

		    Route::post('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'delete', 
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

		});

		/*----------------------------------------------------------------------------------------
			attendance
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/attendance'), function() use ($module_permission)
		{
			$route_slug       = 'schooladmin_attendance_';
			$route_controller = 'SchoolAdmin\AttendanceController@';
			$module_slug       = "attendance";

			Route::get('/student',      			[		'as'		=> $route_slug.'student_index', 
															'uses' 		=> $route_controller.'student_index',
															'middleware'=> $module_permission.$module_slug.'.list']);
			
				

			Route::any('/getEmployeeRecord/{role}',	[		'as'		=> $route_slug.'get_employee_record', 
															'uses' 		=> $route_controller.'get_employee_record',
															'middleware'=> $module_permission.$module_slug.'.list']);					

			Route::any('/getRecords/{role}',		[		'as'		=> $route_slug.'get_records', 
															'uses' 		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);				
																					
			Route::get('/create/{user_type}',		[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

		    Route::post('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'delete', 
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

	 		Route::get('/view/{role}',              [		'as' 		=> $route_slug.'view', 
								  							'uses' 		=> $route_controller.'view',
								  							'middleware'=> $module_permission.$module_slug.'.list']);

	 		Route::get('/view_staff/{role}',        [		'as' 		=> $route_slug.'view_staff', 
								  							'uses' 		=> $route_controller.'view_staff',
								  							'middleware'=> $module_permission.$module_slug.'.list']);

	 		Route::get('/view_details/{role}/{enc_id}',    [		'as' 		=> $route_slug.'view_details', 
								  							'uses' 		=> $route_controller.'view_details',
								  							'middleware'=> $module_permission.$module_slug.'.list']);

	 		Route::post('/getClasses',   			[		'as' 		=> $route_slug.'getClasses', 
			    				  							'uses' 		=> $route_controller.'getClasses',
			 					  							'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/getPeriods',   			[		'as' 		=> $route_slug.'get_periods', 
			    				  							'uses' 		=> $route_controller.'get_periods',
			 					  							'middleware'=> $module_permission.$module_slug.'.list']);
			Route::post('/getData',     			[		'as' 		=> $route_slug.'get_data', 
			    				  							'uses' 		=> $route_controller.'get_data',
			 					  							'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/getStaffData/{enc_id}',	[		'as' 		=> $route_slug.'get_staff_data', 
			    				  							'uses' 		=> $route_controller.'get_staff_data',
			 					  							'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/get_attendance_data/{enc_id}',	[		'as' 		=> $route_slug.'get_attendance_data', 
			    				  							'uses' 		=> $route_controller.'get_attendance_data',
			 					  							'middleware'=> $module_permission.$module_slug.'.list']);
	
			Route::any('export',					[		'as' 		=> $route_slug.'export',
															'uses' 		=> $route_controller.'export',
															'middleware'=> $module_permission.$module_slug.'.export']);

			Route::get('/{user_type}',  			[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/build_table',  			[		'as'		=> $route_slug.'build_table', 
															'uses' 		=> $route_controller.'build_table',
															'middleware'=> $module_permission.$module_slug.'.list']);


		});

		/*----------------------------------------------------------------------------------------
			library
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/library'), function() use ($module_permission)
		{
			$route_slug       = 'schooladmin_library_';
			$route_controller = 'SchoolAdmin\LibraryController@';
			$module_slug       = "library";

			Route::post('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

			Route::post('/store_book',				[		'as' 		=> $route_slug.'store_book', 
			    				  							'uses' 		=> $route_controller.'store_book',
			 					  							'middleware'=> $module_permission.$module_slug.'.create_function(args, code)']);

			Route::get('/edit_category/{enc_id}',   [		'as' 		=> $route_slug.'edit', 
															'uses'		=> $route_controller.'edit',
			 							 					'middleware'=> $module_permission.$module_slug.'.edit']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('/checkCategory',			[		'as' 		=> $route_slug.'checkCategory', 
			 							 					'uses' 		=> $route_controller.'checkCategory',
			 							 					'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('getData',					[		'as'		=> $route_slug.'getData',
															'uses' 		=> $route_controller.'getData',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/delete_category/{enc_id}',	[		'as' 		=> $route_slug.'delete', 
			 							   					'uses' 		=> $route_controller.'delete',
			 							   					'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('/manage_library_contents',	[		'as'		=> $route_slug.'manage_books', 															'uses' 		=> $route_controller.'manage_books',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('create',     				[		'as'		=> $route_slug.'create',
															'uses' 		=> $route_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::any('edit/{enc_id}',				[		'as'		=> $route_slug.'edit',
															'uses' 		=> $route_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('/update_content/{enc_id}', [		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update_content',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/view/{enc_id}', 			[		'as' 		=> $route_slug.'view', 
			 							 					'uses' 		=> $route_controller.'view',
			 							 					'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/return_book/view/{enc_id}',[		'as' 		=> $route_slug.'return_view', 
			 							 					'uses' 		=> $route_controller.'return_view',
			 							 					'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/issue_book/',				[		'as' 		=> $route_slug.'issue', 
			 							 					'uses' 		=> $route_controller.'issue',
			 							 					'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/return_book/',				[		'as' 		=> $route_slug.'manage_return_books', 
			 							 					'uses' 		=> $route_controller.'manage_return_books',
			 							 					'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/store_issue_book',        [		'as' 		=> $route_slug.'issue_book', 
			 							 					'uses' 		=> $route_controller.'issue_book',
			 							 					'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('/reissue_book',            [		'as' 		=> $route_slug.'reissue_book', 
			 							 					'uses' 		=> $route_controller.'reissue_book',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/books_category/{enc_id?}', [		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('get_return_books',  	   	[		'as'		=> $route_slug.'get_return_books',
															'uses' 		=> $route_controller.'get_return_books',
															'middleware'=> $module_permission.$module_slug.'.get_return_books']);

			Route::any('get_users',			  	   	[		'as'		=> $route_slug.'get_users',
															'uses' 		=> $route_controller.'get_users',
															'middleware'=> $module_permission.$module_slug.'.get_users']);

			Route::any('get_user_no',			  	   	[		'as'		=> $route_slug.'get_user_no',
															'uses' 		=> $route_controller.'get_user_no',
															'middleware'=> $module_permission.$module_slug.'.get_user_no']);

			Route::get('/return/{enc_id?}', 		[		'as'		=> $route_slug.'return_book', 
															'uses' 		=> $route_controller.'return_book',
															'middleware'=> $module_permission.$module_slug.'.update']);

			
			Route::any('get_records/{status?}',  	[		'as'		=> $route_slug.'get_records',
															'uses' 		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.get_records']);

		});


		/*----------------------------------------------------------------------------------------
			course
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/course'), function() use ($module_permission)
		{
			$route_slug       = 'schooladmin_course_';
			$route_controller = 'SchoolAdmin\CourseController@';
			$module_slug       = "course";

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

	 		Route::get('/get_course_name_suggession',				[		'as' 		=> $route_slug.'get_course_name_suggession',
															'uses' 		=> $route_controller.'get_course_name_suggession',
															'middleware'=> $module_permission.$module_slug.'.list']);

	 		Route::get('/get_course_name',			[		'as' 		=> $route_slug.'get_course_name',
															'uses' 		=> $route_controller.'get_course_name',
															'middleware'=> $module_permission.$module_slug.'.list']);
		});



		// /*----------------------------------------------------------------------------------------
		// 	Contact Enquiry 
		// ----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/contact_support'), function () use ($module_permission)
		{
			$route_slug       = "admin_contact_support_";
			$module_slug       = "contact_support";
			$route_controller = "SchoolAdmin\ContactEnquiryController@";

			Route::get('/',					  		[		'as' 		=> $route_slug.'index',
													   		'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/view/{enc_id}',	   		[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.list']);


			Route::get('delete/{enc_id}',	   		[		'as' 		=> $route_slug.'delete',
															'uses'		=> $route_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::post('multi_action',		   		[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('create/',		   			[		'as'		=> $route_slug.'create',
															'uses'		=> $route_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);	
														
			Route::post('store',		   			[		'as'		=> $route_slug.'store',
															'uses'		=> $route_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);	

		});

		// /*----------------------------------------------------------------------------------------
		// 	Academic Year
		// ----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/academic_year'), function () use ($module_permission)
		{
			$route_slug       = "academic_year_";
			$module_slug       = "academic_year";
			$route_controller = "SchoolAdmin\AcademicYearController@";

			Route::get('/',					  		[		'as' 		=> $route_slug.'index',
													   		'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/view/{enc_id}',	   		[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.list']);


			Route::get('delete/{enc_id}',	   		[		'as' 		=> $route_slug.'delete',
															'uses'		=> $route_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::post('multi_action',		   		[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('create/',		   			[		'as'		=> $route_slug.'create',
															'uses'		=> $route_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);	
														
			Route::post('store',		   			[		'as'		=> $route_slug.'store',
															'uses'		=> $route_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);	

			Route::any('get_records',				[		'as'		=> $route_slug.'get_records',
															'uses' 		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.get_records']);

		});

		// /*----------------------------------------------------------------------------------------
		// 	Contact Enquiry Categories
		// ----------------------------------------------------------------------------------------*/


		Route::group(array('prefix' => '/enquiry_category'), function() use ($module_permission)
		{
			$route_slug       = "enquiry_category";
			$module_controller = "SchoolAdmin\EnquiryCategoryController@";
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
		 	Employee
		 ----------------------------------------------------------------------------------------*/


		Route::group(array('prefix' => '/employee'), function() use ($module_permission)
		{
			$route_slug       = "employee_";
			$module_controller = "SchoolAdmin\EmployeeStaffController@";
			$module_slug       = "employee";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_records',				[		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create/',					[		'as'		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('checkEmail',				[		'as' 		=> $route_slug.'checkEmail',
															'uses' 		=> $module_controller.'checkEmail',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('assign_role',    			[		'as' 		=> $route_slug.'assign_role',
															'uses' 		=> $module_controller.'assign_role',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('role_store',    			[		'as' 		=> $route_slug.'role_store',
															'uses' 		=> $module_controller.'role_store',
															'middleware'=> $module_permission.$module_slug.'.list']);

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

			Route::get('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('export',					[		'as' 		=> $route_slug.'export',
															'uses' 		=> $module_controller.'export',
															'middleware'=> $module_permission.$module_slug.'.export']);
			
			Route::get('download/{xls}',			[		'as'		=> $route_slug.'download_doc', 
					 	  			    					'uses'		=> $module_controller.'download_doc',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('import',					[		'as'		=> $route_slug.'import',
															'uses'		=> $module_controller.'import',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('has_left/{enc_id}',			[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'has_left',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('not_left',       			[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'not_left',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('check_existance',			[		'as'		=> $route_slug.'check_existance',
															'uses'		=> $module_controller.'check_existance',
															'middleware'=> $module_permission.$module_slug.'.list']);

		});

		/*----------------------------------------------------------------------------------------
		 	Driver
		 ----------------------------------------------------------------------------------------*/


		Route::group(array('prefix' => '/driver'), function() use ($module_permission)
		{
			$route_slug       = "driver_";
			$module_controller = "SchoolAdmin\DriverController@";
			$module_slug       = "driver";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_records/{role}',    	[		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create/',					[		'as'		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('checkEmail',				[		'as' 		=> $route_slug.'checkEmail',
															'uses' 		=> $module_controller.'checkEmail',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('assign_role',    			[		'as' 		=> $route_slug.'assign_role',
															'uses' 		=> $module_controller.'assign_role',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('role_store',    			[		'as' 		=> $route_slug.'role_store',
															'uses' 		=> $module_controller.'role_store',
															'middleware'=> $module_permission.$module_slug.'.list']);

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

			Route::get('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('export',					[		'as' 		=> $route_slug.'export',
																	'uses' 		=> $module_controller.'export',
																	'middleware'=> $module_permission.$module_slug.'.export']);
			
		});

		/*----------------------------------------------------------------------------------------
		 	Canteen
		 ----------------------------------------------------------------------------------------*/


		Route::group(array('prefix' => '/canteen'), function() use ($module_permission)
		{
			$route_slug       = "canteen_";
			$module_controller = "SchoolAdmin\CanteenController@";
			$module_slug       = "canteen";

			Route::get('/manage_canteen_staff',		[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_records',          	[		'as' 		=> $route_slug.'get_records',
										 					'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('add_canteen_staff/',		[		'as'		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('checkEmail',				[		'as' 		=> $route_slug.'checkEmail',
															'uses' 		=> $module_controller.'checkEmail',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('assign_role',    			[		'as' 		=> $route_slug.'assign_role',
															'uses' 		=> $module_controller.'assign_role',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('role_store',    			[		'as' 		=> $route_slug.'role_store',
															'uses' 		=> $module_controller.'role_store',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('edit_canteen_staff/{enc_id}',[		'as' 		=> $route_slug.'edit',
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

			Route::get('view_canteen_staff/{enc_id}',[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('export',					[		'as' 		=> $route_slug.'export',
															'uses' 		=> $module_controller.'export',
															'middleware'=> $module_permission.$module_slug.'.export']);

		});

		/*----------------------------------------------------------------------------------------
		 	Canteen
		 ----------------------------------------------------------------------------------------*/


		Route::group(array('prefix' => '/canteen_products'), function() use ($module_permission)
		{
			$route_slug       = "canteen_products_";
			$module_controller = "SchoolAdmin\CanteenProductsController@";
			$module_slug       = "canteen_products";

			Route::get('/',  						[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_products',          	[		'as' 		=> $route_slug.'get_records',
										 					'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/create',					[		'as'		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action', 			[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('delete/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

		});


		/*----------------------------------------------------------------------------------------
		 	weekly meal
		 ----------------------------------------------------------------------------------------*/


		Route::group(array('prefix' => '/weekly_meals'), function() use ($module_permission)
		{
			$route_slug       = "weekly_meal_";
			$module_controller = "SchoolAdmin\WeeklyMealsController@";
			$module_slug       = "weekly_meal";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_records',          	[		'as' 		=> $route_slug.'get_records',
										 					'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/create',		        	[		'as' 		=> $route_slug.'create',
										 					'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('/store', 	    			[		'as' 		=> $route_slug.'store',
															'uses'		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.update']);

			
			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'delete',
															'uses'		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::post('multi_action', 			[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update_stock', 			[		'as' 		=> $route_slug.'
				',
															'uses' 		=> $module_controller.'update_stock',
															'middleware'=> $module_permission.$module_slug.'.update']);

		});

		/*----------------------------------------------------------------------------------------
		 	daily meal
		 ----------------------------------------------------------------------------------------*/


		Route::group(array('prefix' => '/daily_meals'), function() use ($module_permission)
		{
			$route_slug       = "daily_meal_";
			$module_controller = "SchoolAdmin\DailyMealsController@";
			$module_slug       = "daily_meal";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_records',          	[		'as' 		=> $route_slug.'get_records',
										 					'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/add_weekly_meal',        	[		'as' 		=> $route_slug.'create',
										 					'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/create',					[		'as' 		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('/store',			 	    [		'as' 		=> $route_slug.'store',
															'uses'		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.update']);

			
			Route::get('/delete/{enc_id}', 			[		'as' 		=> $route_slug.'edit',
															'uses'		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::post('multi_action', 			[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update_stock', 			[		'as' 		=> $route_slug.'update_stock',
															'uses' 		=> $module_controller.'update_stock',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('get_weekly_meals', 		[		'as' 		=> $route_slug.'get_weekly_meals_data',
															'uses' 		=> $module_controller.'get_weekly_meals_data',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses' 		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses'		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update_stock',				[		'as'		=> $route_slug.'update_stock',
															'uses'		=> $module_controller.'update_stock',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('add_stock',				[		'as'		=> $route_slug.'add_stock',
															'uses'		=> $module_controller.'add_stock',
															'middleware'=> $module_permission.$module_slug.'.create']);


		});


		/*----------------------------------------------------------------------------------------
		 	claim
		 ----------------------------------------------------------------------------------------*/


		Route::group(array('prefix' => '/claim'), function() use ($module_permission)
		{
			$route_slug       = "claim_";
			$module_controller = "SchoolAdmin\ClaimController@";
			$module_slug       = "claim";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_records',          	[		'as' 		=> $route_slug.'get_records',
										 					'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/changePermission',        	[		'as' 		=> $route_slug.'change_permissions',
										 					'uses' 		=> $module_controller.'change_permissions',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/approve/{enc_id}', 			[		'as' 		=> $route_slug.'update',
																'uses'		=> $module_controller.'approve',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/reject/{enc_id}', 			[		'as' 		=> $route_slug.'update',
																'uses'		=> $module_controller.'reject',
																'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('/view/{enc_id}',			[		'as' 		=> $route_slug.'list',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/delete/{enc_id}', 			[		'as' 		=> $route_slug.'edit',
															'uses'		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::post('multi_action', 			[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/enable', 					[		'as' 		=> $route_slug.'enable',
															'uses' 		=> $module_controller.'enable',
															'middleware'=> $module_permission.$module_slug.'.update']);
			Route::get('/disable', 	         		[		'as' 		=> $route_slug.'disable',
															'uses' 		=> $module_controller.'disable',
															'middleware'=> $module_permission.$module_slug.'.update']);


		});


		/*----------------------------------------------------------------------------------------
		 	Professor
		 ----------------------------------------------------------------------------------------*/


		Route::group(array('prefix' => '/professor'), function() use ($module_permission)
		{
			$route_slug        = "professor_";
			$module_controller = "SchoolAdmin\ProfessorController@";
			$module_slug       = "professor";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_records/{user_type}',	[		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create/',					[		'as'		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('store/',					[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('checkEmail',				[		'as' 		=> $route_slug.'checkEmail',
															'uses' 		=> $module_controller.'checkEmail',
															'middleware'=> $module_permission.$module_slug.'.list']);

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
			
			Route::any('export',					[		'as' 		=> $route_slug.'export',
															'uses' 		=> $module_controller.'export',
															'middleware'=> $module_permission.$module_slug.'.export']);

			Route::get('download/{xls}',			[		'as'		=> $route_slug.'download_doc', 
					 	  			    					'uses'		=> $module_controller.'download_doc',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('import',					[		'as'		=> $route_slug.'import',
															'uses'		=> $module_controller.'import',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('has_left/{enc_id}',			[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'has_left',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('not_left',      			[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'not_left',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('check_existance',			[		'as'		=> $route_slug.'check_existance',
															'uses'		=> $module_controller.'check_existance',
															'middleware'=> $module_permission.$module_slug.'.list']);



		});



	 	/*----------------------------------------------------------------------------------------
		 Notifications
		----------------------------------------------------------------------------------------*/
		Route::group(array('prefix' => '/notification'), function() use ($module_permission)
		{
			$module_slug        = "notification";
			$module_controller = "SchoolAdmin\NotificationController@";

			Route::get('/',           					[		'as' => $module_slug.'index', 
														 		'uses' => $module_controller.'index',
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
			$module_controller = "SchoolAdmin\ReportController@";
			$module_slug       = "report";


			Route::get('/',            [   'as'		=> $route_slug.'users',
															'uses'		=> $module_controller.'users',
															'middleware'=> $module_permission.$module_slug.'.list']);	
			
			Route::any('/exportUsers',[   'as'		=> $route_slug.'exportUsers',
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
			$route_controller = "SchoolAdmin\FeesController@";

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
			$route_controller = "SchoolAdmin\ExamPeriodController@";

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
			$route_controller = "SchoolAdmin\SchoolTemplateController@";

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
		});


		/*----------------------------------------------------------------------------------------
			level
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/level_class'), function() use ($module_permission)
		{
			$route_slug       = 'level_class_';
			$route_controller = 'SchoolAdmin\LevelController@';
			$module_slug       = "level_class";

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

			Route::get('/delete_class/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
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



	 		/*----------------------------------------------------------------------------------------
			class
		----------------------------------------------------------------------------------------*/

			$route_controller = 'SchoolAdmin\ClassController@';

			Route::any('manage_new_classes',		[		'as'		=> $route_slug.'manage_school_classes',
															'uses' 		=> $route_controller.'manage_school_classes',
															'middleware'=> $module_permission.$module_slug.'.manage_school_classes']);

			Route::get('add_class',					[		'as'		=> $route_slug.'add_class',
															'uses' 		=> $route_controller.'add_class',
															'middleware'=> $module_permission.$module_slug.'.add_class']);

			Route::post('store_class',				[		'as'		=> $route_slug.'store',
															'uses' 		=> $route_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.store']);

			Route::any('get_class_records',  		[		'as'		=> $route_slug.'get_class_records',
															'uses' 		=> $route_controller.'get_class_records',
															'middleware'=> $module_permission.$module_slug.'.get_class_records']);

			Route::post('multi_action',				[		'as'		=> $route_slug.'multi_action',
															'uses' 		=> $route_controller.'multi_action',
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
			
			Route::any('export',					[		'as' 		=> $route_slug.'export',
															'uses' 		=> $route_controller.'export',
															'middleware'=> $module_permission.$module_slug.'.export']);

			

		});


		/*----------------------------------------------------------------------------------------
			exam_type
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/exam_type'), function() use ($module_permission)
		{
			$route_slug       = 'admin_exam_type_';
			$route_controller = 'SchoolAdmin\ExamTypeController@';
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

	 		Route::get('/get_exam_type_suggession',				[		'as' 		=> $route_slug.'get_exam_type_suggession',
															'uses' 		=> $route_controller.'get_exam_type_suggession',
															'middleware'=> $module_permission.$module_slug.'.list']);

	 		Route::get('/get_exam_type',			[		'as' 		=> $route_slug.'get_exam_type',
															'uses' 		=> $route_controller.'get_exam_type',
															'middleware'=> $module_permission.$module_slug.'.list']);
		});

		/*----------------------------------------------------------------------------------------
			exam_period
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/exam_period'), function() use ($module_permission)
		{
			$route_slug       = 'admin_exam_period_';
			$route_controller = 'SchoolAdmin\ExamPeriodController@';
			$module_slug       = "exam_period";

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

	 		Route::get('/get_exam_period_suggession',				[		'as' 		=> $route_slug.'get_exam_period_suggession',
															'uses' 		=> $route_controller.'get_exam_period_suggession',
															'middleware'=> $module_permission.$module_slug.'.list']);

	 		Route::get('/get_exam_period',			[		'as' 		=> $route_slug.'get_exam_period',
															'uses' 		=> $route_controller.'get_exam_period',
															'middleware'=> $module_permission.$module_slug.'.list']);
		});


		Route::group(['prefix'=>'keyword_translation'],function()
		{
			$route_slug        = "keyword_translation_";
			$module_controller = "SchoolAdmin\KeywordTranslationController@";

			Route::get('/',['as'=>$route_slug.'index',
							 	    'uses'=>$module_controller.'index']);

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

		$account_setting_controller = "SchoolAdmin\ProfileController@";
		$account_settings_slug = "profile";


		Route::get('profile', 													       [	'as'	=> $account_settings_slug.'profile',
																							'uses'	=> $account_setting_controller.'index']);

		Route::post('/profile/update', 													[	'as'	=> $account_settings_slug.'update_profile',
																							'uses'	=> $account_setting_controller.'update']);

		Route::any('change_email',  			[   'as'    => $account_settings_slug.'change_email',
															'uses'	=> $account_setting_controller.'change_email']);

		Route::any('/email_change/{enc_id}',  	[   'as'    => $account_settings_slug.'email_change',
														    'uses'	=> $account_setting_controller.'email_change']);

		Route::get('/validate_change_email_link/{enc_id}/{enc_reminder_code}',
														[   'as'    => $account_settings_slug.'validate_change_email_link',
												    		'uses'	=> $account_setting_controller.'validate_change_email_link']);

		Route::any('/process_change_email',	    [   'as'    => $account_settings_slug.'process_change_email',
															'uses'	=> $account_setting_controller.'process_change_email']);

		Route::any('setLanguage', 				[	'as'	=> $account_settings_slug.'set_language',
													'uses'	=> $account_setting_controller.'set_language']);


		// /*----------------------------------------------------------------------------------------
		// 	technical Profile
		// ----------------------------------------------------------------------------------------*/

		$account_setting_controller = "SchoolAdmin\TechnicalProfileController@";
		$account_settings_slug = "technical_profile";


		Route::get('technical_profile', 				[	'as'	=> $account_settings_slug.'profile',
															'uses'	=> $account_setting_controller.'index']);

		Route::post('/technical_profile/update', 		[	'as'	=> $account_settings_slug.'update_profile',
															'uses'	=> $account_setting_controller.'update']);

		

		Route::group(array('prefix' => '/sub_admin'), function() use ($module_permission)
		{
			$route_slug       = "sub_admin";
			$module_controller = "SchoolAdmin\SubAdminController@";
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

			/*----------------------------------------------------------------------------------------
		 	parent
		    ----------------------------------------------------------------------------------------*/


				Route::group(array('prefix' => '/parent'), function() use ($module_permission)
				{
					$route_slug       = "parent";
					$module_slug       = "parent";
					$module_controller = "SchoolAdmin\ParentController@";



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
					$module_controller = "SchoolAdmin\StudentController@";

					Route::get('/',								[		'as' 		=> $route_slug.'index',
																		'uses'		=> $module_controller.'index',
																		'middleware'=> $module_permission.$module_slug.'.list']);

					Route::get('activate/{enc_id}', 			[		'as' 		=> $route_slug.'activate',
																		'uses' 		=> $module_controller.'activate',
																		'middleware'=> $module_permission.$module_slug.'.update']);

					Route::get('deactivate/{enc_id}',			[		'as'		=> $route_slug.'deactivate',
																		'uses' 		=> $module_controller.'deactivate',
																	'middleware'=> $module_permission.$module_slug.'.update']);

					Route::post('multi_action',					[		'as' 		=> $route_slug.'multi_action',
																		'uses' 		=> $module_controller.'multi_action',
																		'middleware'=> $module_permission.$module_slug.'.update']);

					Route::any('get_records/{user_type}',   	[		'as' 		=> $route_slug.'get_records',
																		'uses' 		=> $module_controller.'get_records',
																		'middleware'=> $module_permission.$module_slug.'.list']);

					Route::any('view/{enc_id}',					[		'as' 		=> $route_slug.'view',
																		'uses' 		=> $module_controller.'view',
																		'middleware'=> $module_permission.$module_slug.'.update']);

					Route::get('delete/{enc_id}',	 			[		'as' 		=> $route_slug.'delete',
																		'uses' 		=> $module_controller.'delete',
																		'middleware'=> $module_permission.$module_slug.'.detete']);

					Route::get('promote_students/{enc_id}',		[		'as' 		=> $route_slug.'promote_students',
																		'uses' 		=> $module_controller.'promote_students',
																		'middleware'=> $module_permission.$module_slug.'.list']);

					Route::any('/download_document/{enc_id}',	[		'as' 		=> $route_slug.'download_document', 
			    				  										'uses' 		=> $module_controller.'download_document',
			 					  										'middleware'=> $module_permission.$module_slug.'.list']);

					Route::any('export',						[		'as' 		=> $route_slug.'export',
																		'uses' 		=> $module_controller.'export',
																		'middleware'=> $module_permission.$module_slug.'.export']);

					Route::get('has_left/{enc_id}',				[		'as' 		=> $route_slug.'edit',
																		'uses' 		=> $module_controller.'has_left',
																		'middleware'=> $module_permission.$module_slug.'.update']);

					Route::get('not_left/{enc_id}',				[		'as' 		=> $route_slug.'edit',
																		'uses' 		=> $module_controller.'not_left',
																		'middleware'=> $module_permission.$module_slug.'.update']);

					Route::get('edit/{enc_id}',					[		'as' 		=> $route_slug.'edit',
																		'uses' 		=> $module_controller.'edit',
																		'middleware'=> $module_permission.$module_slug.'.update']);

					Route::post('update/{enc_id}',				[		'as' 		=> $route_slug.'update',
																		'uses' 		=> $module_controller.'update',
																		'middleware'=> $module_permission.$module_slug.'.update']);

					Route::post('get_classes',					[		'as' 		=> $route_slug.'get_classes',
																		'uses' 		=> $module_controller.'get_classes',
																		'middleware'=> $module_permission.$module_slug.'.list']);

					Route::any('not_left',      				[		'as' 		=> $route_slug.'edit',
																		'uses' 		=> $module_controller.'not_left',
																		'middleware'=> $module_permission.$module_slug.'.update']);

					Route::any('check_existance',				[		'as'		=> $route_slug.'check_existance',
																		'uses'		=> $module_controller.'check_existance',
																		'middleware'=> $module_permission.$module_slug.'.list']);



				});


		// /*---------------------------------------------------------------------------------------
		// |	Email Template
		// -----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/email_template'), function() use ($module_permission)
		{
			$route_slug        = "admin_email_template_";
			$module_controller = "SchoolAdmin\EmailTemplateController@";
			$module_slug	   = 'email_template';

			Route::get('create',					[		'as'		=> $route_slug.'create',
								 						 	'uses' 		=> $module_controller.'create',
								 						 	'middleware'=> $module_permission.$module_slug.'.create']);


			Route::post('/store',					[		'as' 		=> $route_slug.'store',
			 					  							'uses' 		=> $module_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.create']);


			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
				 											'uses' 		=> $module_controller.'edit',
				 											'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('view/{enc_id}/{act_lng}',	[		'as' 		=> $route_slug.'edit',
				 											'uses' 		=> $module_controller.'view',
				 											'middleware'=> $module_permission.$module_slug.'.list']);


			Route::any('/update/{enc_id}',			[		'as'		=> $route_slug.'update',
										   					'uses' 		=> $module_controller.'update',
										   					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('change_enabled',			[		'as'		=>	$route_slug.'change_enabled',
															'uses'      =>	$module_controller.'change_enabled',
															'middleware'=>	$module_permission.$module_slug.'.change_enabled']);

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);
		});

		// /*---------------------------------------------------------------------------------------
		// |	Role
		// -----------------------------------------------------------------------------------------*/
			
		Route::group(array('prefix' => '/role'), function() use ($module_permission)
		{
			$route_slug        = "role_";
			$module_controller = "SchoolAdmin\RoleController@";
			$module_slug	   = 'role';

			Route::get('/',					[		'as'		=> $route_slug.'list',
								 						 	'uses' 		=> $module_controller.'index',
								 						 	'middleware'=> $module_permission.$module_slug.'.list']);

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

		// /*---------------------------------------------------------------------------------------
		// |	School Profile
		// -----------------------------------------------------------------------------------------*/
			
		Route::group(array('prefix' => '/school_profile'), function() use ($module_permission)
		{
			$route_slug        = "school_profile_";
			$module_controller = "SchoolAdmin\SchoolProfileController@";
			$module_slug	   = 'school_profile';

			Route::get('/',					[		'as'		=> $route_slug.'list',
								 						 	'uses' 		=> $module_controller.'index',
								 						 	'middleware'=> $module_permission.$module_slug.'.list']);

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

		/*----------------------------------------------------------------------------------------
		  School
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/school'), function () use ($module_permission)
		{
			$route_slug       = "school_";
			$module_slug       = "school";
			$route_controller = "SchoolAdmin\SchoolController@";

			Route::any('/',	   		                [		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('/update/{enc_id}',					[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('/store',					[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('/edit',					    [		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

		});

		/*----------------------------------------------------------------------------------------
		  Admission Configuration
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/admission_config'), function () use ($module_permission)
		{
			$route_slug       = "admission_config";
			$module_slug       = "admission_config_";
			$route_controller = "SchoolAdmin\AdmissionConfigController@";

			Route::post('/store',					[		'as' 		=> $route_slug.'create',
															'uses'		=> $route_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('/store_admission',					[		'as' 		=> $route_slug.'create',
															'uses'		=> 'SchoolAdmin\NewAdmissionController@store_admission',
															'middleware'=> $module_permission.$module_slug.'.detete']);

			Route::get('/check_admission_no_exist/{number?}',	   		                [		'as' 		=> $route_slug.'details',
															'uses'		=> 'SchoolAdmin\NewAdmissionController@check_admission_no_exist',
															'middleware'=> $module_permission.$module_slug.'.list']);


			Route::get('new_admission',	 		[			'as' 		=> $route_slug.'delete',
															'uses' 		=> 'SchoolAdmin\NewAdmissionController@create',
															'middleware'=> $module_permission.$module_slug.'.detete']);

			Route::post('checkEmail',				[		'as' 		=> $route_slug.'checkEmail',
															'uses' 		=> 'SchoolAdmin\NewAdmissionController@checkEmail',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('get_parent_details',			[		'as' 		=> $route_slug.'get_parent_details',
															'uses' 		=> 'SchoolAdmin\NewAdmissionController@get_parent_details',
															'middleware'=> $module_permission.$module_slug.'.list']);
			

			Route::post('get_classes',			[		'as' 		=> $route_slug.'get_classes',
															'uses' 		=> 'SchoolAdmin\NewAdmissionController@get_classes',
															'middleware'=> $module_permission.$module_slug.'.list']);


			Route::any('/update/{enc_id}',					[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('delete/{enc_id}',	 		[		'as' 		=> $route_slug.'delete',
															'uses' 		=> $route_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.detete']);


			Route::get('generate_admission_number',			[		'as' 		=> $route_slug.'generate_admission_number',
															'uses' 		=> 'SchoolAdmin\NewAdmissionController@generate_admission_number',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('check_brotherhood',				[		'as' 		=> $route_slug.'checkEmail',
															'uses' 		=> 'SchoolAdmin\NewAdmissionController@checkBrotherhood',
															'middleware'=> $module_permission.$module_slug.'.list']);



			Route::any('/{enc_id?}',	   		                [		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

		});


		/*----------------------------------------------------------------------------------------
		//  Room Management AND Room Assignemnt
 		// ----------------------------------------------------------------------------------------*/
		Route::group(array('prefix' => '/room'), function() use ($module_permission)
		{

		    Route::group(array('prefix'=>'/management'), function () use ($module_permission){

			    $route_slug        = "admin_users_";
			    $module_slug       = "admin_users";
			    $module_controller = "SchoolAdmin\RoomManagementController@";

				Route::get('/',							[	'as' 		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('/get_records',				[	'as' 		=> $route_slug.'get_records',
															'uses'		=> $module_controller.'get_records',
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

				Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);
			});

			Route::group(array('prefix'=>'/assignment'), function () use ($module_permission){

			    $route_slug        = "admin_users_";
			    $module_slug       = "admin_users";
			    $module_controller = "SchoolAdmin\RoomAssignmentController@";

				Route::get('/',							[	'as' 		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('/get_records',				[	'as' 		=> $route_slug.'get_records',
															'uses'		=> $module_controller.'get_records',
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

				Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);
			});

			Route::group(array('prefix'=>'/ajax'), function (){
				Route::any('/get_floors',				[	'as' 		=> 'get_floors',
															'uses'		=> 'SchoolAdmin\RoomAssignmentController@get_floors']);

				Route::any('/get_class',				[	'as' 		=> 'get_class',
															'uses'		=> 'SchoolAdmin\RoomAssignmentController@get_class']);
			});

		});

		/*----------------------------------------------------------------------------------------
		  Notification Settings
		----------------------------------------------------------------------------------------*/
		Route::group(array('prefix'=>'/notification_settings'), function () use ($module_permission)
		{

			$route_slug        = "notification_settings";
			$module_slug       = "notification_settings_";
			$route_controller  = "SchoolAdmin\NotificationSettingController@";

			Route::any('/',						[		'as' 		=> $route_slug.'index',
															'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.index']);

			Route::post('/store',					[		'as' 		=> $route_slug.'create',
															'uses'		=> $route_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

		});


		/*----------------------------------------------------------------------------------------
		  Calendar
		----------------------------------------------------------------------------------------*/
		Route::group(array('prefix'=>'/calendar'), function () use ($module_permission)
		{

			$route_slug        = "calendar";
			$module_slug       = "calendar";
			$route_controller  = "SchoolAdmin\CalendarController@";

			Route::any('/',						[		'as' 		=> $route_slug.'index',
															'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.index']);

			Route::post('/store',					[		'as' 		=> $route_slug.'create',
															'uses'		=> $route_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('/get_events',					[		'as' 		=> $route_slug.'list',
															'uses'		=> $route_controller.'get_events',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/update',					[		'as' 		=> $route_slug.'list',
															'uses'		=> $route_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/delete',					[		'as' 		=> $route_slug.'list',
															'uses'		=> $route_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('get_classes',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $route_controller.'get_class',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('export',					[		'as' 		=> $route_slug.'export',
																	'uses' 		=> $route_controller.'export',
																	'middleware'=> $module_permission.$module_slug.'.export']);


		});

		/*---------------------------------------------------------------------------------------
		|	Email Template
		/* -----------------------------------------------------------------------------------------*/
		Route::group(array('prefix' => '/sms_template'), function() use ($module_permission)
		{
			$route_slug        = "admin_sms_template_";
			$module_controller = "SchoolAdmin\SchoolSmsTemplateController@";
			$module_slug	   = 'sms_template';
			
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


		/*---------------------------------------------------------------------------------------
		| brotherhood
		/*------------------------------------------------------------------------------------------*/
		Route::group(array('prefix' => '/brotherhood'), function() use ($module_permission)
		{
			$route_slug        = "admin_brotherhood";
			$module_controller = "SchoolAdmin\BrotherhoodController@";
			$module_slug	   = 'brotherhood';

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_records',				[		'as' 		=> $route_slug.'get_records',
															'uses'		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/create',					[		'as' 		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create' ,
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('/store',		   			[		'as'		=> $route_slug.'store',
															'uses'  	=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);
			
 			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
				 											'uses' 		=> $module_controller.'edit',
				 											'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update/{enc_id}',			[		'as'		=> $route_slug.'update',
										   					'uses' 		=> $module_controller.'update',
										   					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}', 			[		'as' 		=> $route_slug.'edit',
																'uses'		=> $module_controller.'delete',
																'middleware'=> $module_permission.$module_slug.'.delete']);


		});

		/*---------------------------------------------------------------------------------------
		| exam
		/*------------------------------------------------------------------------------------------*/
		Route::group(array('prefix'=>'/exam'), function () use ($module_permission){

			    $route_slug        = "exam";
			    $module_slug       = "exam";
			    $module_controller = "SchoolAdmin\ExamController@";

				Route::get('/',							[	'as' 		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('/get_records',				[	'as' 		=> $route_slug.'get_records',
															'uses'		=> $module_controller.'get_records',
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

				Route::get('/view/{enc_id}',			[		'as' 		=> $route_slug.'list',
																'uses' 		=> $module_controller.'view',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::get('/delete/{enc_id}', 			[		'as' 		=> $route_slug.'edit',
																'uses'		=> $module_controller.'delete',
																'middleware'=> $module_permission.$module_slug.'.delete']);

				Route::get('/approve/{enc_id}', 		[		'as' 		=> $route_slug.'update',
																'uses'		=> $module_controller.'approve',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('/reject/{enc_id}', 			[		'as' 		=> $route_slug.'update',
																'uses'		=> $module_controller.'reject',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('get_classes',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'get_class',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('get_assessment_scale',		[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'get_assessment_scale',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('get_floor',				    [		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'get_floor',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('get_rooms',					[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'get_rooms',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('result/{enc_id}',			[		'as' 		=> $route_slug.'list',
																'uses' 		=> $module_controller.'result',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('store_result/{enc_id}',	[		'as' 		=> $route_slug.'list',
																'uses' 		=> $module_controller.'store_result',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('download/{format}',			[		'as' 		=> $route_slug.'list',
																'uses' 		=> $module_controller.'download_doc',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('upload',					[		'as' 		=> $route_slug.'list',
																'uses' 		=> $module_controller.'upload',
																'middleware'=> $module_permission.$module_slug.'.create']);

				Route::any('/get_courses',		   	    [	'as'		=> $route_slug.'get_courses',
															'uses'  	=> $module_controller.'get_courses',
															'middleware'=> $module_permission.$module_slug.'.list']);

				
			});


		/*---------------------------------------------------------------------------------------
		|	TimetableControllertable 
		/* -----------------------------------------------------------------------------------------*/
		Route::group(array('prefix' => '/timetable'), function() use ($module_permission)
		{
			$route_slug        = "admin_timetable_";
			$module_controller = "SchoolAdmin\TimetableController@";
			$module_slug	   = 'timetable';

			Route::get('/teaching_hours/{enc_id?}',	[		'as' 		=> $route_slug.'teaching_hours',
				 											'uses' 		=> $module_controller.'teaching_hours',
				 											'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/store_teaching_hours',	[		'as' 		=> $route_slug.'store_teaching_hours',
				 											'uses' 		=> $module_controller.'store_teaching_hours',
				 											'middleware'=> $module_permission.$module_slug.'.add']);

			Route::any('/update_teaching_hours/{enc_id}',
													[		'as' 		=> $route_slug.'update_teaching_hours',
				 											'uses' 		=> $module_controller.'update_teaching_hours',
				 											'middleware'=> $module_permission.$module_slug.'.update']);


		 	Route::post('/update_period_mapping',	[		'as' 		=> $route_slug.'update_period_mapping',
															'uses' 		=> $module_controller.'update_period_mapping',
															'middleware'=> $module_permission.$module_slug.'.add']);

		 	Route::any('/create/{arr_timetable_data?}', 
		 											[		'as'	 	 => $module_slug.'create_timetable',  
		 													'uses' 		 => $module_controller.'create_timetable',
		 													'middleware' => $module_permission.$module_slug.'.add']);


			Route::post('/delete', 					[		'as'		 => $module_slug.'_delete_period_teacher', 
															'uses' 		 => $module_controller.'delete_period_teacher',
															'middleware' => $module_permission.$module_slug.'.delete']);

			Route::get('/get_period_details', 		[		'as' 		 => $module_slug.'_get_period_details',  
															'uses' 		 => $module_controller.'get_period_details']);

			Route::get('/summary', 					[		'as' 		 => $module_slug.'timetable_summery',  
															'uses' 		 => $module_controller.'timetable_summery']);

			Route::post('/store_period_timimg',		[		'as' 		=> $route_slug.'store_period_timimg',
															'uses' 		=> $module_controller.'store_period_timimg',
															'middleware'=> $module_permission.$module_slug.'.add']);

			Route::any('/delete_time_table', 		[		'as'	 	 => $module_slug.'delete_time_table',  
		 													'uses' 		 => $module_controller.'delete_time_table' ]);
			
			
		 	Route::any('export',					[		'as' 		=> $route_slug.'export',
																	'uses' 		=> $module_controller.'export',
																	'middleware'=> $module_permission.$module_slug.'.export']);

		 	Route::get('/new',							[		'as' 		=> $route_slug.'timetable',
															'uses' 		=> $module_controller.'new_timetable',
															'middleware'=> $module_permission.$module_slug.'.list']);

		 	Route::get('/edit',							[		'as' 		=> $route_slug.'timetable',
															'uses' 		=> $module_controller.'edit_timetable',
															'middleware'=> $module_permission.$module_slug.'.list']);

		 	Route::post('/get_classes',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'get_class',
																'middleware'=> $module_permission.$module_slug.'.update']);


		});
		/*---------------------------------------------------------------------------------------
		|	Assign Subjects to the classes  
		/* -----------------------------------------------------------------------------------------*/
		Route::group(array('prefix'=>'/assign_courses'), function () use ($module_permission){

			    $route_slug        = "admin_assign_courses_";
			    $module_slug       = "admin_assign_courses_";
			    $module_controller = "SchoolAdmin\AssignCoursesController@";

				Route::get('/',							[	'as' 		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('/get_records',				[	'as' 		=> $route_slug.'get_records',
															'uses'		=> $module_controller.'get_records',
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

				Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);
				
				Route::post('get_courses',				[		'as' 		=> $route_slug.'get_courses',
																'uses' 		=> $module_controller.'get_courses',
																'middleware'=> $module_permission.$module_slug.'.list']);
			});

			/*---------------------------------------------------------------------------------------
			|                                            Course Material
			/*------------------------------------------------------------------------------------------*/

			Route::group(array('prefix'=>'/course_material'), function () use ($module_permission){
			
			    $route_slug        = "course_material";
			    $module_slug       = "course_material";
			    $module_controller = "SchoolAdmin\CourseMaterialController@";
			    

				Route::get('/',							[	'as' 		=> $route_slug.'index',
															'uses'		=> $module_controller.'index']);

				Route::get('/create',					[	'as' 		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create']);

				Route::post('/store',		   			[	'as'		=> $route_slug.'store',
															'uses'  	=> $module_controller.'store']);

				Route::get('get_classes',				[	'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'get_class']);

				Route::any('get_courses',				[	'as' 		=> $route_slug.'get_courses',
															'uses' 		=> $module_controller.'get_courses']);

				Route::get('/view/{enc_id}',			[	'as' 		=> $route_slug.'list',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.list']);

				Route::get('/delete/{enc_id}', 			[	'as' 		=> $route_slug.'edit',
															'uses'		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);

			    Route::post('multi_action',				[	'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			    Route::post('/delete_doc',		   			[	'as'		=> $route_slug.'store',
															'uses'  	=> $module_controller.'delete_doc',
															'middleware'=> $module_permission.$module_slug.'.create']);

			    Route::get('/download_document/{enc_id}',		   			[	'as'		=> $route_slug.'store',
															'uses'  	=> $module_controller.'download_document',
															'middleware'=> $module_permission.$module_slug.'.create']);;
			    
			});


			/*---------------------------------------------------------------------------------------
			| Transport Bus
			/*------------------------------------------------------------------------------------------*/
			Route::group(array('prefix' => '/transport_bus'), function() use ($module_permission)
			{
				$route_slug        = "admin_transport_bus";
				$module_controller = "SchoolAdmin\TransportBusController@";
				$module_slug	   = 'transport_bus';

				Route::get('/',							[		'as' 		=> $route_slug.'index',
																'uses' 		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('/get_records',				[		'as' 		=> $route_slug.'get_records',
																'uses'		=> $module_controller.'get_records',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::get('/create',					[		'as' 		=> $route_slug.'create',
																'uses' 		=> $module_controller.'create' ,
																'middleware'=> $module_permission.$module_slug.'.create']);

				Route::post('/store',		   			[		'as'		=> $route_slug.'store',
																'uses'  	=> $module_controller.'store',
																'middleware'=> $module_permission.$module_slug.'.create']);
				
	 			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
					 											'uses' 		=> $module_controller.'edit',
					 											'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('update/{enc_id}',			[		'as'		=> $route_slug.'update',
											   					'uses' 		=> $module_controller.'update',
											   					'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('/view/{enc_id}', 			[		'as' 		=> $route_slug.'view', 
			 							 						'uses' 		=> $module_controller.'view',
			 							 						'middleware'=> $module_permission.$module_slug.'.list']);				

				Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('/delete/{enc_id}', 			[		'as' 		=> $route_slug.'edit',
																'uses'		=> $module_controller.'delete',
																'middleware'=> $module_permission.$module_slug.'.delete']);

				Route::get('export/csv',				[		'as' 		=> $route_slug.'export_csv',
																'uses' 		=> $module_controller.'export_csv',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::get('export/pdf',				[		'as' 		=> $route_slug.'export_pdf',
																'uses' 		=> $module_controller.'export_pdf',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('/add_student/{enc_id}',		[		'as' 		=> 'list',
																'uses' 		=> $module_controller.'add_student',
														]);
				
				Route::post('/store_student/{enc_id}',	[		'as' 		=> 'list',
																'uses' 		=> $module_controller.'store_student',
														]);

				Route::any('/get_student',				[	'as' 		=> 'list',
															'uses' 		=> $module_controller.'get_student',
														]);

				Route::any('/view_map',				[	'as' 		=> 'list',
															'uses' 		=> $module_controller.'view_map',
														]);

				Route::get('/get_non_student_list',		[		'as' 		=> $route_slug.'list',
																'uses'		=> $module_controller.'get_non_student_list',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('export',					[		'as' 		=> $route_slug.'export',
															'uses' 		=> $module_controller.'export']);


			});


			/*---------------------------------------------------------------------------------------
			| Transport Route
			/*------------------------------------------------------------------------------------------*/
			Route::group(array('prefix' => '/transport_route'), function() use ($module_permission)
			{
				$route_slug        = "admin_transport_route";
				$module_controller = "SchoolAdmin\TransportRouteController@";
				$module_slug	   = 'transport_route';

				Route::get('/',							[		'as' 		=> $route_slug.'index',
																'uses' 		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('/get_records',				[		'as' 		=> $route_slug.'get_records',
																'uses'		=> $module_controller.'get_records',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::get('/create',					[		'as' 		=> $route_slug.'create',
																'uses' 		=> $module_controller.'create' ,
																'middleware'=> $module_permission.$module_slug.'.create']);

				Route::post('/store',		   			[		'as'		=> $route_slug.'store',
																'uses'  	=> $module_controller.'store',
																'middleware'=> $module_permission.$module_slug.'.create']);
				
	 			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
					 											'uses' 		=> $module_controller.'edit',
					 											'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('update/{enc_id}',			[		'as'		=> $route_slug.'update',
											   					'uses' 		=> $module_controller.'update',
											   					'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('/view/{enc_id}', 			[		'as' 		=> $route_slug.'view', 
			 							 						'uses' 		=> $module_controller.'view',
			 							 						'middleware'=> $module_permission.$module_slug.'.list']);				

				Route::post('multiple_delete',				[		'as' 		=> $route_slug.'multiple_delete',
																'uses' 		=> $module_controller.'multiple_delete',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('/delete/{enc_id}', 			[		'as' 		=> $route_slug.'edit',
																'uses'		=> $module_controller.'delete',
																'middleware'=> $module_permission.$module_slug.'.delete']);	

				Route::get('/get_non_student_list',		[		'as' 		=> $route_slug.'list',
																'uses'		=> $module_controller.'get_non_student_list',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::get('/get_assigned_student_list',[		'as' 		=> $route_slug.'list',
																'uses'		=> $module_controller.'get_assigned_student_list',
																'middleware'=> $module_permission.$module_slug.'.list']);
				
				Route::post('/get_bus_capacity',		[		'as' 		=> $route_slug.'list',
																'uses'		=> $module_controller.'get_bus_capacity',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::post('/check_if_route_exists',	[		'as' 		=> $route_slug.'list',
																'uses'		=> $module_controller.'check_if_route_exists',
																'middleware'=> $module_permission.$module_slug.'.list']);	

							
			});

			/*---------------------------------------------------------------------------------------
			| Fees Structure
			/*------------------------------------------------------------------------------------------*/
			Route::group(array('prefix' => '/fees_structure'), function() use ($module_permission)
			{
				$route_slug        = "fees_structure";
				$module_controller = "SchoolAdmin\FeesStructureController@";
				$module_slug	   = 'fees_structure';

				Route::get('/',							[		'as' 		=> $route_slug.'index',
																'uses' 		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);

	 			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
					 											'uses' 		=> $module_controller.'edit',
					 											'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('update/{enc_id}',			[		'as'		=> $route_slug.'update',
											   					'uses' 		=> $module_controller.'update',
											   					'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('create',					[		'as' 		=> $route_slug.'create',
					 											'uses' 		=> $module_controller.'create',
					 											'middleware'=> $module_permission.$module_slug.'.create']);

				Route::post('store',					[		'as' 		=> $route_slug.'create',
					 											'uses' 		=> $module_controller.'store',
					 											'middleware'=> $module_permission.$module_slug.'.create']);

				Route::post('update/{enc_id}',			[		'as'		=> $route_slug.'update',
											   					'uses' 		=> $module_controller.'update',
											   					'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('/view/{enc_id}', 			[		'as' 		=> $route_slug.'view', 
			 							 						'uses' 		=> $module_controller.'view',
			 							 						'middleware'=> $module_permission.$module_slug.'.list']);				

				Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('/delete/{enc_id}', 			[		'as' 		=> $route_slug.'edit',
																'uses'		=> $module_controller.'delete',
																'middleware'=> $module_permission.$module_slug.'.delete']);
			});


			/*---------------------------------------------------------------------------------------
			|	Survey 
			/* -----------------------------------------------------------------------------------------*/
			Route::group(array('prefix' => '/survey'), function() use ($module_permission)
			{
				$route_slug        = "admin_survey_";
				$module_controller = "SchoolAdmin\SurveyController@";
				$module_slug	   = 'survey';
				
	 			

			 	Route::get('/',								[		'as' 		=> $route_slug.'index',
																	'uses' 		=> $module_controller.'index',
																	'middleware'=> $module_permission.$module_slug.'.list']);

			 	Route::any('/get_records',					[		'as' 		=> $route_slug.'get_records',
																	'uses'		=> $module_controller.'get_records',
																	'middleware'=> $module_permission.$module_slug.'.list']);

			  	Route::get('/create',						[		'as' 		=> $route_slug.'create',
																	'uses' 		=> $module_controller.'create' ,
																	'middleware'=> $module_permission.$module_slug.'.create']);

				Route::post('/store',		   				[		'as'		=> $route_slug.'store',
																	'uses'  	=> $module_controller.'store',
																	'middleware'=> $module_permission.$module_slug.'.create']);

				Route::get('/edit/{enc_id}',    			[		'as'		=> $route_slug.'edit',
																	'uses' 		=> $module_controller.'edit',
																	'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('/update/{enc_id}',				[		'as' 		=> $route_slug.'update',
																	'uses' 		=> $module_controller.'update',
																	'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('/delete/{enc_id}', 				[		'as' 		=> $route_slug.'delete',
																	'uses'		=> $module_controller.'delete',
																	'middleware'=> $module_permission.$module_slug.'.delete']);

				Route::post('multi_action',					[		'as' 		=> $route_slug.'multi_action',
																	'uses' 		=> $module_controller.'multi_action',
																	'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('/view/{enc_id}',    			[		'as'		=> $route_slug.'view',
																	'uses' 		=> $module_controller.'view',
																	'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('/delete_survey_image',			[		'as'		=> $route_slug.'delete',
																	'uses' 		=> $module_controller.'delete_survey_image',
																	'middleware'=> $module_permission.$module_slug.'.delete']);

				Route::post('/update_question/{enc_id}/{survey_id}', ['as' 		=> $route_slug.'update',
																	'uses' 		=> $module_controller.'update_question',
																	'middleware'=> $module_permission.$module_slug.'.update']);

				Route::any('/delete_question/{enc_id}/{survey_id}', ['as' 		=> $route_slug.'update',
																	'uses' 		=> $module_controller.'delete_question',
																	'middleware'=> $module_permission.$module_slug.'.update']);


				Route::post('/store_questions_in_survey/{survey_id}', ['as' 		=> $route_slug.'update',
																	'uses' 		=> $module_controller.'store_questions_in_survey',
																	'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('/view_response/{enc_id}',    	[		'as'		=> $route_slug.'view',
																	'uses' 		=> $module_controller.'view_response',
																	'middleware'=> $module_permission.$module_slug.'.view']);

			 	Route::any('/get_response_records/{enc_id}',[		'as' 		=> $route_slug.'get_response_records',
																	'uses'		=> $module_controller.'get_response_records',
																	'middleware'=> $module_permission.$module_slug.'.list']);

			 	Route::get('/view_response_details/{enc_id}/{user_id}',[		'as' 		=> $route_slug.'view_response_details',
																	'uses'		=> $module_controller.'view_response_details',
																	'middleware'=> $module_permission.$module_slug.'.list']);

			 	Route::any('export',					[		'as' 		=> $route_slug.'export',
															'uses' 		=> $module_controller.'export']);


				Route::any('reply_survey/{enc_id}',					[		'as' 		=> $route_slug.'reply_survey',
															'uses' 		=> $module_controller.'reply_survey']);

				Route::post('/store_survey_reply/{survey_id}', ['as' => $route_slug . 'store_survey_reply', 'uses' => $module_controller . 'store_survey_reply']);

			
			});

			/*---------------------------------------------------------------------------------------
			| Task
			/*------------------------------------------------------------------------------------------*/
			Route::group(array('prefix' => '/task'), function() use ($module_permission)
			{
				$route_slug        = "task";
				$module_controller = "SchoolAdmin\TaskController@";
				$module_slug	   = 'task';

				Route::get('/',							[		'as' 		=> $route_slug.'index',
																'uses' 		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);

	 			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
					 											'uses' 		=> $module_controller.'edit',
					 											'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('update/{enc_id}',			[		'as'		=> $route_slug.'update',
											   					'uses' 		=> $module_controller.'update',
											   					'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('create',					[		'as' 		=> $route_slug.'create',
					 											'uses' 		=> $module_controller.'create',
					 											'middleware'=> $module_permission.$module_slug.'.create']);

				Route::post('store',					[		'as' 		=> $route_slug.'create',
					 											'uses' 		=> $module_controller.'store',
					 											'middleware'=> $module_permission.$module_slug.'.create']);


				Route::get('/view/{enc_id}', 			[		'as' 		=> $route_slug.'view', 
			 							 						'uses' 		=> $module_controller.'view',
			 							 						'middleware'=> $module_permission.$module_slug.'.list']);				

				Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('/delete/{enc_id}', 			[		'as' 		=> $route_slug.'edit',
																'uses'		=> $module_controller.'delete',
																'middleware'=> $module_permission.$module_slug.'.delete']);

				Route::get('/get_employees', 			[		'as' 		=> $route_slug.'list',
																'uses'		=> $module_controller.'get_employees',
																'middleware'=> $module_permission.$module_slug.'.list']);


 
				Route::get('/get_professors', 			[		'as' 		=> $route_slug.'list',
																'uses'		=> $module_controller.'get_professors',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::get('/getClasses',   			[		'as' 		=> $route_slug.'getClasses', 
			    				  							'uses' 		=> $module_controller.'getClasses',
			 					  							'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('/get_records',				[		'as' 		=> $route_slug.'get_records',
																	'uses'		=> $module_controller.'get_records',
																	'middleware'=> $module_permission.$module_slug.'.list']);

				Route::post('/change_status',			[	'as' 		=> 'list',
															'uses' 		=> $module_controller.'change_status',
														]);

				Route::any('/change_user_status',			[	'as' 		=> 'list',
															'uses' 		=> $module_controller.'change_user_status',
														]);

			});

			/*---------------------------------------------------------------------------------------
			| Club
			/*------------------------------------------------------------------------------------------*/
			Route::group(array('prefix' => '/club'), function() use ($module_permission)
			{
				$route_slug        = "club";
				$module_controller = "SchoolAdmin\ClubController@";
				$module_slug	   = 'club';

				Route::get('/',							[		'as' 		=> $route_slug.'index',
																'uses' 		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);

	 			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
					 											'uses' 		=> $module_controller.'edit',
					 											'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('update/{enc_id}',			[		'as'		=> $route_slug.'update',
											   					'uses' 		=> $module_controller.'update',
											   					'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('create',					[		'as' 		=> $route_slug.'create',
					 											'uses' 		=> $module_controller.'create',
					 											'middleware'=> $module_permission.$module_slug.'.create']);

				Route::post('store',					[		'as' 		=> $route_slug.'create',
					 											'uses' 		=> $module_controller.'store',
					 											'middleware'=> $module_permission.$module_slug.'.create']);


				Route::get('/view/{enc_id}', 			[		'as' 		=> $route_slug.'view', 
			 							 						'uses' 		=> $module_controller.'view',
			 							 						'middleware'=> $module_permission.$module_slug.'.list']);				

				Route::any('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('/delete/{enc_id}', 			[		'as' 		=> $route_slug.'edit',
																'uses'		=> $module_controller.'delete',
																'middleware'=> $module_permission.$module_slug.'.delete']);

				Route::get('/get_classes',   			[		'as' 		=> $route_slug.'get_classes', 
			    				  							'uses' 		=> $module_controller.'get_class',
			 					  							'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('/get_records',				[		'as' 		=> $route_slug.'get_records',
																	'uses'		=> $module_controller.'get_records',
																	'middleware'=> $module_permission.$module_slug.'.list']);

				Route::post('/change_status',			[	'as' 		=> 'list',
															'uses' 		=> $module_controller.'change_status',
														]);

				Route::any('/change_user_status',			[	'as' 		=> 'list',
															'uses' 		=> $module_controller.'change_user_status',
														]);

				Route::any('/add_student/{enc_id}',			[	'as' 		=> 'list',
															'uses' 		=> $module_controller.'add_student',
														]);

				Route::any('/get_student',		[	'as' 		=> 'list',
															'uses' 		=> $module_controller.'get_student',
														]);
				
				Route::post('/store_student/{enc_id}',		[	'as' 		=> 'list',
															'uses' 		=> $module_controller.'store_student',
														]);

			});

		/*----------------------------------------------------------------------------------------
			canteen_bookings
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/canteen_bookings'), function() use ($module_permission)
		{
			$route_slug       = 'schooladmin_canteen_bookings_';
			$route_controller = 'SchoolAdmin\CanteenBookingsController@';
			$module_slug       = "canteen_bookings";

			Route::get('/',							[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_records',				[		'as'		=> $route_slug.'list',
															'uses' 		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);			
																					
			Route::get('/create',					[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

		    Route::post('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'delete', 
			 							   					'uses' 		=> $route_controller.'delete',
			 							   					'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::post('multi_action',				[		'as'		=> $route_slug.'multi_action',
															'uses' 		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('add_to_cart',				[		'as'		=> $route_slug.'add_to_cart',
															'uses' 		=> $route_controller.'add_to_cart',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update_quantity',			[		'as'		=> $route_slug.'update_quantity',
															'uses' 		=> $route_controller.'update_quantity',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('delete_quantity',			[		'as'		=> $route_slug.'delete_quantity',
															'uses' 		=> $route_controller.'delete_quantity',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('checkout',					[		'as'		=> $route_slug.'list',
															'uses' 		=> $route_controller.'checkout',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('get_users',				[		'as'		=> $route_slug.'list',
															'uses' 		=> $route_controller.'get_users',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('get_cart_data',			[		'as'		=> $route_slug.'list',
															'uses' 		=> $route_controller.'get_cart_data',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('view/{enc_id}',			    [		'as'		=> $route_slug.'list',
															'uses' 		=> $route_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.list']);


	 		Route::any('change_delivery_status',	[		'as'		=> $route_slug.'update',
															'uses' 		=> $route_controller.'change_delivery_status',
															'middleware'=> $module_permission.$module_slug.'.update']);

	 		Route::any('change_payment_status',		[		'as'		=> $route_slug.'update',
															'uses' 		=> $route_controller.'change_payment_status',
															'middleware'=> $module_permission.$module_slug.'.update']);

	 		

		});

		/*----------------------------------------------------------------------------------------
			stock
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/stock'), function() use ($module_permission)
		{
			$route_slug       = 'schooladmin_stock_';
			$route_controller = 'SchoolAdmin\StockManagementController@';
			$module_slug       = "stock";

			Route::get('/',							[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_records',				[		'as'		=> $route_slug.'list',
															'uses' 		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);			
																					
			Route::get('/create',					[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('/create_distributed_stock', [		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create_distributed_stock',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

		    Route::post('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'delete', 
			 							   					'uses' 		=> $route_controller.'delete',
			 							   					'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('/edit/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
			 							   					'uses' 		=> $route_controller.'edit',
			 							   					'middleware'=> $module_permission.$module_slug.'.edit']);

			Route::post('multi_action',				[		'as'		=> $route_slug.'multi_action',
															'uses' 		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/view/{enc_id}',		    [		'as'		=> $route_slug.'list',
															'uses' 		=> $route_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.list']);
	 		

		});

		/*----------------------------------------------------------------------------------------
			stock_distribution
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/stock/stock_distribution'), function() use ($module_permission)
		{
			$route_slug       = 'schooladmin_stock_distribution_';
			$route_controller = 'SchoolAdmin\StockDistributionController@';
			$module_slug       = "stock_distribution";

			Route::get('/',							[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_records',				[		'as'		=> $route_slug.'list',
															'uses' 		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('get_users',				[		'as'		=> $route_slug.'list',
															'uses' 		=> $route_controller.'get_users',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/create',					[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

		    Route::post('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'delete', 
			 							   					'uses' 		=> $route_controller.'delete',
			 							   					'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('/edit/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
			 							   					'uses' 		=> $route_controller.'edit',
			 							   					'middleware'=> $module_permission.$module_slug.'.edit']);

			Route::post('multi_action',				[		'as'		=> $route_slug.'multi_action',
															'uses' 		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/view/{enc_id}',		    [		'as'		=> $route_slug.'list',
															'uses' 		=> $route_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/get_quantity',		    	[		'as'		=> $route_slug.'list',
															'uses' 		=> $route_controller.'get_quantity',
															'middleware'=> $module_permission.$module_slug.'.list']);
	 		

		});

		/*----------------------------------------------------------------------------------------
			suggestions
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/suggestions'), function() use ($module_permission)
		{
			$route_slug       = 'schooladmin_suggestion_';
			$route_controller = 'SchoolAdmin\SuggestionsController@';
			$module_slug       = "suggestion";

			
			Route::get('/employee_suggestions/{status}',		[		'as'		=> $route_slug.'manage_employee_suggestions', 
															'uses' 		=> $route_controller.'manage_employee_suggestions',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_records/{status}',		[		'as'		=> $route_slug.'list',
															'uses' 		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_employee_records/{status}',		[		'as'		=> $route_slug.'list',
															'uses' 		=> $route_controller.'get_employee_records',
															'middleware'=> $module_permission.$module_slug.'.list']);


			Route::get('/create',					[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

		    Route::post('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('/add_vote',				[		'as' 		=> $route_slug.'add_vote', 
			 							 					'uses' 		=> $route_controller.'add_vote',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'delete', 
			 							   					'uses' 		=> $route_controller.'delete',
			 							   					'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('/edit/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
			 							   					'uses' 		=> $route_controller.'edit',
			 							   					'middleware'=> $module_permission.$module_slug.'.edit']);

			Route::post('multi_action',				[		'as'		=> $route_slug.'multi_action',
															'uses' 		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/view/{status}/{enc_id}',    [		'as'		=> $route_slug.'list',
															'uses' 		=> $route_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/change_status',		    [		'as'		=> $route_slug.'update',
															'uses' 		=> $route_controller.'change_status',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('/raise_poll',		    	[		'as'		=> $route_slug.'update',
															'uses' 		=> $route_controller.'raise_poll',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/{status}',					[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

	 		
		});
		/*---------------------------------------------------------------------------------------
		| Payment
		/*------------------------------------------------------------------------------------------*/
		Route::group(array('prefix' => '/payment'), function()
		{
			$route_slug        = "payment";
			$module_controller = "SchoolAdmin\PaymentController@";
			$module_slug	   = 'payment';

			


			Route::post('/store_payment',			[		'as' 		=> $route_slug.'store_payment',
															'uses' 		=> $module_controller.'store_payment']);

			Route::post('/checkout',				[		'as' 		=> $route_slug.'checkout',
															'uses' 		=> $module_controller.'checkout']);

			Route::get('/{enc_id}',					[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index']);
		});


		/*---------------------------------------------------------------------------------------
		| Payment Transactions
		/*------------------------------------------------------------------------------------------*/
		Route::group(array('prefix' => '/transactions'), function()
		{
			$route_slug        = "transactions";
			$module_controller = "SchoolAdmin\TransactionController@";
			$module_slug	   = 'transactions';

			
			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index']);

			Route::post('/store_payment',			[		'as' 		=> $route_slug.'store_payment',
															'uses' 		=> $module_controller.'store_payment']);

			Route::post('/checkout',				[		'as' 		=> $route_slug.'checkout',
															'uses' 		=> $module_controller.'checkout']);

			Route::any('/get_records',				[		'as' 		=> $route_slug.'get_records',
															'uses'		=> $module_controller.'get_records']);

			Route::any('/view/{enc_id}',			[		'as' 		=> $route_slug.'view',
															'uses'		=> $module_controller.'view']);

			Route::any('/download_document/{enc_id}',[		'as' 		=> $route_slug.'view',
															'uses'		=> $module_controller.'download_document']);

			Route::post('/change_status',			[		'as' 		=> $route_slug.'view',
															'uses'		=> $module_controller.'change_status']);

			Route::any('export',					[		'as' 		=> $route_slug.'export',
															'uses' 		=> $module_controller.'export']);
			
		});


		/*----------------------------------------------------------------------------------------
		//  News 
 		// ----------------------------------------------------------------------------------------*/
		Route::group(array('prefix' => '/news'), function() use ($module_permission)
		{
 
			    $route_slug        = "admin_users_";
			    $module_slug       = "admin_users";
			    $module_controller = "SchoolAdmin\NewsController@";

				Route::get('/',							[	'as' 		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

				Route::any('/get_records',				[	'as' 		=> $route_slug.'get_records',
															'uses'		=> $module_controller.'get_records',
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

				Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('/view/{enc_id}', 			[		'as' 		=> $route_slug.'view',
																'uses'		=> $module_controller.'view',
																'middleware'=> $module_permission.$module_slug.'.view']);

				Route::any('/download_document/{enc_id}', [		'as' 		=> $route_slug.'download_document',
																'uses'		=> $module_controller.'download_document',
																'middleware'=> $module_permission.$module_slug.'.download_document']);


				Route::any('/delete_image', 			[		'as' 		=> $route_slug.'delete_image',
																'uses'		=> $module_controller.'delete_image',
																'middleware'=> $module_permission.$module_slug.'.delete_image']);

		 });

		/*-----------------------------------------------------------------------------------------------
										STUDENT BEHAVIOUR
		------------------------------------------------------------------------------------------------*/
		Route::group(['prefix' => 'student_behaviour'], function ()
		{
			$behaviourController = "SchoolAdmin\StudentBehaviourController@";
			Route::get('/',					['as'=> 'calendar',		'uses'	=> $behaviourController.'index']);

			Route::get('/create',			['as'=> 'create',		'uses'	=> $behaviourController.'create']);

			Route::get('/view/{enc_id}',	['as'=> 'view', 		'uses'	=> $behaviourController.'view']);

			Route::any('/getClasses',   	['as'=> 'getClasses', 	'uses'  => $behaviourController.'getClasses']);

			Route::any('/get_courses',   	['as'=> 'get_courses', 	'uses'  => $behaviourController.'get_courses']);

			Route::post('/get_students',   	['as'=> 'get_students', 'uses'  => $behaviourController.'get_students']);

			Route::post('/store',   	    ['as'=> 'store',        'uses'  => $behaviourController.'store']);
			Route::post('/store_period',   	['as'=> 'store_period', 'uses'  => $behaviourController.'store_period']);

			Route::any('/get_students_behaviour',   	    ['as'=> 'get_students_behaviour',        'uses'  => $behaviourController.'get_students_behaviour']);

			Route::any('/view/{enc_id}',    ['as'=> 'view',        'uses'  => $behaviourController.'view']);	
		});

		/*-----------------------------------------------------------------------------------------------
										PROFESSOR REPLACEMENT
		------------------------------------------------------------------------------------------------*/
		Route::group(['prefix' => 'professor_replacement'], function ()
		{
			$module_controller = "SchoolAdmin\ProfessorReplacementController@";
			
			Route::get('/',					[		'as'	=> 'calendar',																				'uses'	=> $module_controller.'index']);

			Route::get('/create',			[		'as'	=> 'calendar',																				'uses'	=> $module_controller.'create']);

			Route::any('/add',   			[		'as'	=> 'add', 																					'uses'  => $module_controller.'add']);

			/*Route::any('/get_timetable',   	[		'as'	=> 'get_timetable', 																		'uses'  => $module_controller.'get_professor_timetable']);*/

			Route::any('/get_details',   	[		'as'	=> 'get_details', 
													'uses'  => $module_controller.'get_details']);


			Route::any('/get_user_no',   	[		'as'	=> 'get_user_no', 
													'uses'  => $module_controller.'get_professor_no']);

			Route::any('/get_free_professors', [	'as'	=> 'get_free_professors',
												 	'uses'  => $module_controller.'get_free_professors']);

			Route::post('/store',   	    [		'as'	=> 'store',
											        'uses'  => $module_controller.'store']);

			Route::any('/get_records',		[	    'as' 	=> 'get_records',
													'uses'	=> $module_controller.'get_records']);

			Route::any('/edit/{enc_id}',	[	    'as' 	=> 'edit',
													'uses'	=> $module_controller.'edit']);

			Route::post('/update/{enc_id}', [		'as'	=> 'update',
													'uses'  => $module_controller.'update']);

			Route::post('multi_action', 	[		'as'	=> 'multi_action',
													'uses' 	=> $module_controller.'multi_action']);

			Route::get('delete/{enc_id}',	[		'as' 	=> 'delete',
													'uses' 	=> $module_controller.'delete']);

			/*Route::get('/create',			['as'=> 'create',		'uses'	=> $module_controller.'create']);

			Route::get('/view/{enc_id}',	['as'=> 'view', 		'uses'	=> $behaviourController.'view']);

			Route::any('/getClasses',   	['as'=> 'getClasses', 	'uses'  => $behaviourController.'getClasses']);

			Route::any('/get_courses',   	['as'=> 'get_courses', 	'uses'  => $behaviourController.'get_courses']);

			Route::post('/get_students',   	['as'=> 'get_students', 'uses'  => $behaviourController.'get_students']);

			
			Route::post('/store_period',   	['as'=> 'store_period', 'uses'  => $behaviourController.'store_period']);

			Route::any('/get_students_behaviour',   	    ['as'=> 'get_students_behaviour',        'uses'  => $behaviourController.'get_students_behaviour']);

			Route::any('/view/{enc_id}',    ['as'=> 'view',        'uses'  => $behaviourController.'view']);*/	
		});

		/*----------------------------------------------------------------------------------------
			gradebook fields
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/gradebook_fields'), function() use ($module_permission)
		{
			$route_slug       = 'gradebook_fields_';
			$route_controller = 'SchoolAdmin\GradebookFieldsController@';
			$module_slug       = "gradebook_fields";

					
																					
			Route::get('/create',					[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

		    Route::post('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

		    Route::get('/edit/{enc_id}',					[		'as' 		=> $route_slug.'edit', 
								  							'uses' 		=> $route_controller.'edit',
								  							'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'delete', 
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

	 		Route::get('/{enc_id?}',				[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);	

		});

		/*----------------------------------------------------------------------------------------
			gradebook fields
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/gradebook'), function() use ($module_permission)
		{
			$route_slug       = 'gradebook_';
			$route_controller = 'SchoolAdmin\GradebookController@';
			$module_slug       = "gradebook";

					
	 		Route::get('/',						[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);	

	 		Route::post('/store',				[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

	 		Route::post('/get_students',		[		'as' 		=> $route_slug.'list', 
			    				  							'uses' 		=> $route_controller.'get_students',
			 					  							'middleware'=> $module_permission.$module_slug.'.list']);

	 		Route::any('/getClasses',   	    [        'as'=> 'getClasses', 	
	 													 'uses'  => $route_controller.'getClasses' ]);


	 		Route::any('/generate_gradebook/{enc_id}',   [        'as'=> 'generate_gradebook', 	
	 													 'uses'  => $route_controller.'generate_gradebook' ]);


	 		Route::any('/generate_gradebook_for_all',   	    [        'as'=> 'generate_gradebook_for_all', 	
	 													 'uses'  => $route_controller.'generate_gradebook_for_all' ]);


		});


		/*----------------------------------------------------------------------------------------
			gradebook fields
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/educational_board'), function() use ($module_permission)
		{
			$route_slug       = 'educational_board_';
			$route_controller = 'SchoolAdmin\EducationalBoardController@';
			$module_slug       = "educational_board";

					
																					
			Route::get('/create',					[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

		    Route::post('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

		    Route::get('/edit/{enc_id}',					[		'as' 		=> $route_slug.'edit', 
								  							'uses' 		=> $route_controller.'edit',
								  							'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $route_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'delete', 
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

	 		
	 		Route::any('/get_employees', 			[		'as' 		=> $route_slug.'list',
															'uses'		=> $route_controller.'get_employees',
															'middleware'=> $module_permission.$module_slug.'.list']);
 
			Route::any('/get_professors', 			[		'as' 		=> $route_slug.'list',
															'uses'		=> $route_controller.'get_professors',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/{enc_id?}',				[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);	


		});


  });
?>
