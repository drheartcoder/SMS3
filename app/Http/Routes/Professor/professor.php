<?php
	Route::group(['prefix' => $professor_path,'middleware'=>['professor']],function()
	{
		$module_permission = "module_permission:";
		$route_slug        = "professor_auth_";
		$module_controller = "Professor\AuthController@";

		Route::any('/setSession',				[	'as'	=> 'setSession',        
													'uses'	=> 'Controller@setSession']);

		Route::get('/',							[   'as'    => $route_slug.'login',
													'uses'	=> $module_controller.'login']);

		Route::get('login',						[   'as'    => $route_slug.'login',
													'uses'	=> $module_controller.'login']);

		Route::post('process_login',			[   'as'    => $route_slug.'process_login',
													'uses'	=> $module_controller.'process_login']);

		Route::get('change_password',			[	'as'    => $route_slug.'change_password',
													'uses'	=> $module_controller.'change_password']);

		Route::post('update_password',			[	'as'    => $route_slug.'change_password',
													'uses'	=> $module_controller.'update_password']);

		Route::post('process_forgot_password',  [   'as'    => $route_slug.'forgot_password',
													'uses'	=> $module_controller.'process_forgot_password']);

		Route::get('validate_admin_reset_password_link/{enc_id}/{enc_reminder_code}',
												[   'as'    => $route_slug.'validate_admin_reset_password_link',
												    'uses'	=> $module_controller.'validate_reset_password_link']);

		Route::post('reset_password',			[   'as'    => $route_slug.'reset_password',
													'uses'	=> $module_controller.'reset_password']);

		Route::get('change_first_time',			[   'as'    => $route_slug.'change_first_time',
													'uses'  => $module_controller.'change_first_time']);

		Route::any('get_cities',  				[   'as'    => 'get_cities',
													'uses'	=> 'Controller@get_cities']);

		Route::any('get_countries',  			[   'as'    => 'get_countries',
													'uses'	=> 'Controller@get_countries']);


/*-----------------------------------------------------------------------------------------------
											DASHBOARD 
------------------------------------------------------------------------------------------------*/
		Route::get('/dashboard',				[	'as'	=> $route_slug.'dashboard',
													'uses'	=>'Professor\DashboardController@index']);

		Route::get('/logout',					[	'as'    => $route_slug.'logout',
													'uses'  => 'SchoolAdmin\AuthController@logout']);

/*-----------------------------------------------------------------------------------------------
											PROFILE 
------------------------------------------------------------------------------------------------*/
		$profile_controller = "Professor\ProfileController@";
		$profile_slug = "profile";


		Route::get('profile',					[	'as'	=> $profile_slug.'profile',
													'uses'	=> $profile_controller.'index']);

		Route::get('edit_profile',				[ 	'as'	=> $profile_slug.'edit_profile',
													'uses'	=> $profile_controller.'edit_profile']);

		Route::post('/profile/update',			[	'as'	=> $profile_slug.'update_profile',	
													'uses'	=> $profile_controller.'update']);

		Route::any('/change_email',  	[   'as'    => $profile_slug.'change_email',
													'uses'	=> $profile_controller.'change_email']);

		Route::any('/email_change/{enc_id}',  	[   'as'    => $profile_slug.'email_change',
														    'uses'	=> $profile_controller.'email_change']);

		Route::get('/validate_change_email_link/{enc_id}/{enc_reminder_code}',
												[   'as'    => $profile_slug.'validate_change_email_link',
												    'uses'	=> $profile_controller.'validate_change_email_link']);

		Route::post('/process_change_email',
											  	[   'as'    => $profile_slug.'process_change_email',
													'uses'	=> $profile_controller.'process_change_email']);

		Route::any('/setLanguage',  			[	'as'	=> $profile_slug.'setLanguage',
													'uses'	=> $profile_controller.'set_language']);



/*----------------------------------------------------------------------------------------
		 Notifications
		----------------------------------------------------------------------------------------*/
		Route::group(array('prefix' => '/notification'), function() use ($module_permission)
		{
			$module_slug        = "notification";
			$module_controller = "Professor\NotificationController@";

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

/*-----------------------------------------------------------------------------------------------
											MESSAGES 
------------------------------------------------------------------------------------------------*/
		$notification_controller = "Professor\MessageController@";
		$notification_slug       = "message";

		Route::get('message',					[	'as'	=> $notification_slug.'message','uses'	=> $notification_controller.'index']);
/*---------------------------------------------------------------------------------------
|                                            exam
/*------------------------------------------------------------------------------------------*/
		Route::group(array('prefix'=>'/exam'), function () use ($module_permission){
			
			    $route_slug        = "exam";
			    $module_slug       = "exam";
			    $module_controller = "Professor\ExamController@";

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

				Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('get_classes',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'get_class',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('get_assessment_scale',		[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'get_assessment_scale',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('get_floor',					[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'get_floor',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('get_rooms',					[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'get_rooms',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::post('/get_courses',   			[	    'as'		=> 'get_courses', 	
				    											'uses'  	=> $module_controller.'get_courses']);

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
				Route::any('export',					[		'as' 		=> $route_slug.'export',
																'uses' 		=> $module_controller.'export',
																'middleware'=> $module_permission.$module_slug.'.export']);
	    });
		/*---------------------------------------------------------------------------------------
		|                                            Course Material
		/*------------------------------------------------------------------------------------------*/

				Route::group(array('prefix'=>'/course_material'), function () use ($module_permission){
				
				    $route_slug        = "course_material";
				    $module_slug       = "course_material";
				    $module_controller = "Professor\CourseMaterialController@";

					Route::get('/',							[	'as' 		=> $route_slug.'index',
																'uses'		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);


					Route::get('/create',					[	'as' 		=> $route_slug.'create',
																'uses' 		=> $module_controller.'create' ,
																		'middleware'=> $module_permission.$module_slug.'.create']);

					Route::post('/store',		   			[	'as'		=> $route_slug.'store',
																'uses'  	=> $module_controller.'store',
																'middleware'=> $module_permission.$module_slug.'.create']);

					Route::get('get_classes',				[	'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'get_class',
																'middleware'=> $module_permission.$module_slug.'.update']);

					Route::get('/view/{enc_id}',			[	'as' 		=> $route_slug.'list',
																'uses' 		=> $module_controller.'view',
																'middleware'=> $module_permission.$module_slug.'.list']);

					Route::get('/delete/{enc_id}', 			[	'as' 		=> $route_slug.'edit',
																'uses'		=> $module_controller.'delete',
																'middleware'=> $module_permission.$module_slug.'.delete']);

				    Route::post('multi_action',				[	'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

				    Route::post('/delete_doc',		   		[	'as'		=> $route_slug.'store',
																'uses'  	=> $module_controller.'delete_doc',
																'middleware'=> $module_permission.$module_slug.'.create']);

				    Route::get('/download_document/{enc_id}',[	'as'		=> $route_slug.'store',
																'uses'  	=> $module_controller.'download_document',
																'middleware'=> $module_permission.$module_slug.'.create']);

				    Route::post('/get_courses',   			[	'as'		=> 'get_courses', 	
				    											'uses'  	=> $module_controller.'get_courses']);
				    
				});





		/*----------------------------------------------------------------------------------------
												attendance
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/attendance'), function() use ($module_permission)
		{

			$route_slug       = 'professor_attendance_';
			$route_controller = 'Professor\AttendanceController@';
			$module_slug       = "attendance";	
			Route::any('export',    				[  		'as'        => $route_slug.'export',
                                   							'uses'      => $route_controller.'export']);

			Route::get('/{user_type}',  			[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::post('/getRecords/student',     	[		'as' 		=> $route_slug.'get_students_records', 
			    				  							'uses' 		=> $route_controller.'get_students_records',
			 					  							'middleware'=> $module_permission.$module_slug.'.list']);
		
																					
			Route::get('/create/{user_type}',		[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('/getClasses',   			[		'as' 		=> $route_slug.'getClasses', 
			    				  							'uses' 		=> $route_controller.'getClasses',
			 					  							'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/getPeriods',   			[		'as' 		=> $route_slug.'get_periods', 
			    				  							'uses' 		=> $route_controller.'get_periods',
			 					  							'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/getStudents',   			[		'as' 		=> $route_slug.'get_students', 
			    				  							'uses' 		=> $route_controller.'get_students',
			 					  							'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/getData',     			[		'as' 		=> $route_slug.'get_data', 
			    				  							'uses' 		=> $route_controller.'get_data',
			 					  							'middleware'=> $module_permission.$module_slug.'.list']);

			Route::any('/get_events',				[		'as' 		=> $route_slug.'get_events', 
			    				  							'uses' 		=> $route_controller.'get_events',
			 				  							    'middleware'=> $module_permission.$module_slug.'.list']);

		    Route::any('/store',					[		'as' 		=> $route_slug.'store', 
			    				  							'uses' 		=> $route_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.store']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
			 							 					'uses' 		=> $route_controller.'update',
			 							 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/view/{role}',              [		'as' 		=> $route_slug.'view', 
								  							'uses' 		=> $route_controller.'view',
								  							'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/view_professor/{enc_id}',  [		'as' 		=> $route_slug.'view_professor', 
								  							'uses' 		=> $route_controller.'view_professor',
								  							'middleware'=> $module_permission.$module_slug.'.list']);

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

	 		Route::post('/getTimetable', 			[		'as'		=> $route_slug.'get_timetable',
															'uses' 		=> $route_controller.'get_timetable',
															'middleware'=> $module_permission.$module_slug.'.checkExamType']);

	 		Route::get('/view_details/{role}/{enc_id}',    [		'as' 		=> $route_slug.'view_details', 
								  							'uses' 		=> $route_controller.'view_details',
								  							'middleware'=> $module_permission.$module_slug.'.list']);

	 		Route::any('/build_table',  			[		'as'		=> $route_slug.'build_table', 
															'uses' 		=> $route_controller.'build_table',
															'middleware'=> $module_permission.$module_slug.'.list']);
	 	 
	 		


		});

		/*-----------------------------------------------------------------------------------------------
											CALENDAR 
		------------------------------------------------------------------------------------------------*/
		Route::group(['prefix' => 'calendar'], function ()
		{
			$calendarController = "Professor\CalendarController@";
			Route::get('/',					['as'=> 'calendar','uses'	=> $calendarController.'index']);

			Route::get('/get_events',		['as'=> 'calendar','uses'	=> $calendarController.'get_events']);

			Route::get('/view/{enc_id}',		['as'=> 'calendar','uses'	=> $calendarController.'view']);
		});

		/*-----------------------------------------------------------------------------------------------
											Homework 
		------------------------------------------------------------------------------------------------*/
		Route::group(['prefix' => 'homework'], function ()
		{
			$module_controller = "Professor\HomeworkController@";
			Route::get('/',							['as'=> 'homework','uses'	=> $module_controller.'index']);

			Route::get('/get_events',				['as'=> 'homework','uses'	=> $module_controller.'get_events']);

			Route::get('/get_records',				['as'=> 'homework','uses'	=> $module_controller.'get_records']);
			
			Route::get('/create',					[	'as' 		=> 'homework',
														'uses' 		=> $module_controller.'create' ,
													]);

			Route::post('/store',		   			[	'as'		=> 'store',
														'uses'  	=> $module_controller.'store',
													]);

			Route::get('get_classes',				[		'as' 		=> 'multi_action',
															'uses' 		=> $module_controller.'get_class',
													]);

			Route::get('/view/{enc_id}',			[	'as' 		=> 'list',
														'uses' 		=> $module_controller.'view',
													]);

			Route::post('/change_status',			[	'as' 		=> 'list',
														'uses' 		=> $module_controller.'change_status',
													]);

			Route::post('/get_courses',   			[	'as'		=> 'get_courses', 	
				    									'uses'  	=> $module_controller.'get_courses']);

			Route::any('export',					[	'as' 		=> 'export',
														'uses' 		=> $module_controller.'export']);
		});
		/*-----------------------------------------------------------------------------------------------
											TIMETABLE 
		------------------------------------------------------------------------------------------------*/
		Route::get('/timetable',['as'=>  'timetable','uses'=>'Professor\TimetableController@index']);

		/*---------------------------------------------------------------------------------------
		|                                            claim
		/*------------------------------------------------------------------------------------------*/
		Route::group(array('prefix'=>'/claim'), function () use ($module_permission){
			
			    $route_slug        = "claim";
			    $module_slug       = "claim";
			    $module_controller = "Professor\ClaimController@";

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

				Route::get('/view/{enc_id}',			[		'as' 		=> $route_slug.'list',
																'uses' 		=> $module_controller.'view',
																'middleware'=> $module_permission.$module_slug.'.list']);

				Route::get('/delete/{enc_id}', 			[		'as' 		=> $route_slug.'edit',
																'uses'		=> $module_controller.'delete',
																'middleware'=> $module_permission.$module_slug.'.delete']);

				Route::post('multi_action',				[		'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('get_classes',				[		'as' 		=> $route_slug.'get_class',
																'uses' 		=> $module_controller.'get_class',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('get_students',				[		'as' 		=> $route_slug.'get_students',
																'uses' 		=> $module_controller.'get_students',
																'middleware'=> $module_permission.$module_slug.'.update']);

				Route::get('get_student_NationalId',	[		'as' 		=> $route_slug.'get_student_NationalId',
																'uses' 		=> $module_controller.'get_student_NationalId',
																'middleware'=> $module_permission.$module_slug.'.update']);
		});

		/*---------------------------------------------------------------------------------------
			| Task
			/*------------------------------------------------------------------------------------------*/
			Route::group(array('prefix' => '/task'), function() use ($module_permission)
			{
				$route_slug        = "task";
				$module_controller = "Professor\TaskController@";
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
			|	Survey 
			/* -----------------------------------------------------------------------------------------*/
			Route::group(array('prefix' => '/survey'), function() use ($module_permission)
			{
				$route_slug        = "professor_survey_";
				$module_controller = "Professor\SurveyController@";
				$module_slug	   = 'survey';
				
	 			

			 	Route::get('/',								[		'as' 		=> $route_slug.'index',
																	'uses' 		=> $module_controller.'index',
																	'middleware'=> $module_permission.$module_slug.'.list']);
			 	Route::any('/get_records',					[		'as' 		=> $route_slug.'get_records',
																	'uses'		=> $module_controller.'get_records',
																	'middleware'=> $module_permission.$module_slug.'.list']);

			  	Route::get('/reply_survey/{survey_id}',		[		'as'		=> $route_slug.'view',
																	'uses' 		=> $module_controller.'reply_survey',
																	'middleware'=> $module_permission.$module_slug.'.update']);

			  	Route::post('/store_survey_reply/{survey_id}',[		'as'		=> $route_slug.'store_survey_reply',
																	'uses' 		=> $module_controller.'store_survey_reply',
																	'middleware'=> $module_permission.$module_slug.'.update']);

			  	Route::get('/view_reply/{survey_id}',		[		'as'		=> $route_slug.'view',
																	'uses' 		=> $module_controller.'view_reply',
																	'middleware'=> $module_permission.$module_slug.'.update']);
			  	

			});

			/*---------------------------------------------------------------------------------------
			| Club
			/*------------------------------------------------------------------------------------------*/
			Route::group(array('prefix' => '/club'), function() use ($module_permission)
			{
				$route_slug        = "club";
				$module_controller = "Professor\ClubController@";
				$module_slug	   = 'club';

				Route::get('/',							[		'as' 		=> $route_slug.'index',
																'uses' 		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);


				Route::get('/view/{enc_id}', 			[		'as' 		=> $route_slug.'view', 
			 							 						'uses' 		=> $module_controller.'view',
			 							 						'middleware'=> $module_permission.$module_slug.'.list']);				


			});

		/*----------------------------------------------------------------------------------------
			canteen_bookings
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/canteen_bookings'), function() use ($module_permission)
		{
			$route_slug       = 'professor_canteen_bookings_';
			$route_controller = 'Professor\CanteenBookingsController@';
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
			//  News 
	 		// ----------------------------------------------------------------------------------------*/
			Route::group(array('prefix' => '/news'), function() 
			{
	 
				    $route_slug        = "admin_users_";
				    $module_slug       = "admin_users";
				    $module_controller = "Professor\NewsController@";

					Route::get('/',							[	'as' 		=> $route_slug.'index',
																'uses'		=> $module_controller.'index']);

					Route::any('/get_records',				[	'as' 		=> $route_slug.'get_records',
																'uses'		=> $module_controller.'get_records']);

					 

					Route::get('/view/{enc_id}', 			[		'as' 		=> $route_slug.'view',
																	'uses'		=> $module_controller.'view']);

					Route::any('/download_document/{enc_id}', [		'as' 		=> $route_slug.'download_document',
																	'uses'		=> $module_controller.'download_document']);

	 			 });

			//----------------------------------------------------------------------------------------
			//  Messages
	 		// ----------------------------------------------------------------------------------------*/
			Route::group(array('prefix' => '/message'), function() 
			{
	 
				    $route_slug        = "message";
				    $module_slug       = "message";
				    $module_controller = "Professor\MessageController@";

					Route::get('/',							[	'as' 		=> $route_slug.'index',
																'uses'		=> $module_controller.'index']);

					Route::any('/get_chat/{enc_id}',		[	'as' 		=> $route_slug.'get_chat',
																'uses'		=> $module_controller.'get_chat']);

					
					Route::any('/send_message',			[	'as' 		=> $route_slug.'send_message',
																'uses'		=> $module_controller.'send_message']);

	 		});

		/*----------------------------------------------------------------------------------------
			suggestions
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/suggestions'), function() use ($module_permission)
		{
			$route_slug       = 'professor_suggestion_';
			$route_controller = 'Professor\SuggestionsController@';
			$module_slug       = "suggestion";


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

		/*-----------------------------------------------------------------------------------------------
										STUDENT BEHAVIOUR
		------------------------------------------------------------------------------------------------*/
		Route::group(['prefix' => 'student_behaviour'], function ()
		{
			$behaviourController = "Professor\StudentBehaviourController@";
			Route::get('/',					['as'=> 'calendar',		'uses'	=> $behaviourController.'index']);

			Route::get('/create',			['as'=> 'create',		'uses'	=> $behaviourController.'create']);

			Route::get('/view/{enc_id}',	['as'=> 'view', 		'uses'	=> $behaviourController.'view']);

			Route::post('/getClasses',   	['as'=> 'getClasses', 	'uses'  => $behaviourController.'getClasses']);

			Route::post('/get_courses',   	['as'=> 'get_courses', 	'uses'  => $behaviourController.'get_courses']);

			Route::post('/get_students',   	['as'=> 'get_students', 'uses'  => $behaviourController.'get_students']);

			Route::post('/store',   	    ['as'=> 'store',        'uses'  => $behaviourController.'store']);

			Route::post('/update/{enc_id}', ['as'=> 'update',		'uses'  => $behaviourController.'update']);

			Route::any('/get_students_behaviour',   	    ['as'=> 'get_students_behaviour',        'uses'  => $behaviourController.'get_students_behaviour']);

			Route::any('/view/{enc_id}',    ['as'=> 'view',        	'uses'  => $behaviourController.'view']);

			Route::any('export',			['as'=> 'export',		'uses'	=> $behaviourController.'export']);
		});

		/*----------------------------------------------------------------------------------------
		  Notification Settings
		----------------------------------------------------------------------------------------*/
		Route::group(array('prefix'=>'/notification_settings'), function () use ($module_permission)
		{

			$route_slug        = "notification_settings";
			$module_slug       = "notification_settings_";
			$route_controller  = "Professor\NotificationSettingController@";

			Route::any('/',						[		'as' 		=> $route_slug.'index',
															'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.index']);

			Route::post('/store',					[		'as' 		=> $route_slug.'create',
															'uses'		=> $route_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

		});
});
?>