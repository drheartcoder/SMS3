<?php
Route::group(['prefix' => $student_path, 'middleware' => ['student']], function ()
{

    $route_slug = "admin_auth_";
    $module_controller = "Student\AuthController@";

    /*----------------------------------------------------------------------------------------
    Admin Home Route
    ----------------------------------------------------------------------------------------*/
    /*Route::any('/setLanguage',   	[	'as'	=> 'setLanguage', 'uses'	=> 'Controller@setLanguage']);*/
    Route::get('/',                                     [   'as'    => $route_slug . 'login', 
                                                            'uses'  => $module_controller . 'login']);

    Route::get('login',                                 [   'as'    => $route_slug . 'login', 
                                                            'uses'  => $module_controller . 'login']);

    Route::post('process_login',                        [   'as'    => $route_slug . 'process_login', 
                                                            'uses'  => $module_controller . 'process_login']);

    Route::get('change_password', ['as' => $route_slug . 'change_password', 'uses' => $module_controller . 'change_password']);

    Route::post('update_password', ['as' => $route_slug . 'change_password', 'uses' => $module_controller . 'update_password']);

    Route::post('process_forgot_password', ['as' => $route_slug . 'forgot_password', 'uses' => $module_controller . 'process_forgot_password']);

    Route::get('validate_admin_reset_password_link/{enc_id}/{enc_reminder_code}', ['as' => $route_slug . 'validate_admin_reset_password_link', 'uses' => $module_controller . 'validate_reset_password_link']);

    Route::post('reset_password', ['as' => $route_slug . 'reset_password', 'uses' => $module_controller . 'reset_password']);

    Route::get('change_first_time', ['as' => $route_slug . 'change_first_time', 'uses' => $module_controller . 'change_first_time']);

    Route::any('get_cities',            [   'as'    => 'get_cities',
                                                'uses'  => 'Controller@get_cities']);

        Route::any('get_countries',         [   'as'    => 'get_countries',
                                                'uses'  => 'Controller@get_countries']);


    /*-----------------------------------------------------------------------------------------------
    							DASHBOARD 
    ------------------------------------------------------------------------------------------------*/
    Route::get('/dashboard', ['as' => $route_slug . 'dashboard', 'uses' => 'Student\DashboardController@index']);
    Route::get('/logout', ['as' => $route_slug . 'logout', 'uses' => 'SchoolAdmin\AuthController@logout']);

    /*-----------------------------------------------------------------------------------------------
    							PROFILE 
    ------------------------------------------------------------------------------------------------*/
    $profile_controller = "Student\ProfileController@";
    $profile_slug = "profile";

    Route::get('profile', ['as' => $profile_slug . 'profile', 'uses' => $profile_controller . 'edit_profile']);

    Route::post('/profile/update', ['as' => $profile_slug . 'update_profile', 'uses' => $profile_controller . 'update']);

    Route::any('/change_email', ['as' => 'change_email', 'uses' => $profile_controller . 'change_email']);

    Route::any('/email_change/{enc_id}', ['as' => 'email_change', 'uses' => $profile_controller . 'email_change']);

    Route::get('/validate_change_email_link/{enc_id}/{enc_reminder_code}', ['as' => 'validate_change_email_link', 'uses' => $profile_controller . 'validate_change_email_link']);

    Route::post('/process_change_email', ['as' => 'process_change_email', 'uses' => $profile_controller . 'process_change_email']);

    Route::any('/setLanguage', ['as' => 'setLanguage', 'uses' => $profile_controller . 'set_language']);

    /*----------------------------------------------------------------------------------------
    Notifications
    ----------------------------------------------------------------------------------------*/
    Route::group(array(
        'prefix' => '/notification'
    ) , function ()
    {
        $module_slug = "notification";
        $module_controller = "Student\NotificationController@";

        Route::get('/', ['as' => $module_slug . 'index', 'uses' => $module_controller . 'index']);

        Route::post('multi_action', ['as' => $module_slug . 'multi_action', 'uses' => $module_controller . 'multi_action']);

        Route::get('delete/{enc_id}', ['as' => $module_slug . 'delete', 'uses' => $module_controller . 'delete']);

    });

    /*-----------------------------------------------------------------------------------------------
    							CALENDAR 
    ------------------------------------------------------------------------------------------------*/
    Route::group(['prefix' => 'calendar'], function ()
    {
        $calendarController = "Student\CalendarController@";
        Route::get('/', ['as' => 'calendar', 'uses' => $calendarController . 'index']);

        Route::get('/get_events', ['as' => 'calendar', 'uses' => $calendarController . 'get_events']);

        Route::get('/view/{enc_id}', ['as' => 'calendar', 'uses' => $calendarController . 'view']);
    });

    /*---------------------------------------------------------------------------------------
    | exam
    /*------------------------------------------------------------------------------------------*/
    Route::group(array(
        'prefix' => '/exam'
    ) , function ()
    {

        $route_slug = "exam";
        $module_slug = "exam";
        $module_controller = "Student\ExamController@";

        Route::get('/', ['as' => $route_slug . 'index', 'uses' => $module_controller . 'index']);

        Route::any('/get_records', ['as' => $route_slug . 'get_records', 'uses' => $module_controller . 'get_records']);

        Route::get('/view/{enc_id}', ['as' => $route_slug . 'list', 'uses' => $module_controller . 'view']);

    });

    /*---------------------------------------------------------------------------------------
    |                                            Course Material
    /*------------------------------------------------------------------------------------------*/

    Route::group(array(
        'prefix' => '/course_material'
    ) , function ()
    {

        $route_slug = "course_material";
        $module_slug = "course_material";
        $module_controller = "Student\CourseMaterialController@";

        Route::get('/', ['as' => $route_slug . 'index', 'uses' => $module_controller . 'index']);

        Route::get('/view/{enc_id}', ['as' => $route_slug . 'list', 'uses' => $module_controller . 'view']);

        Route::get('/download_document/{enc_id}', ['as' => $route_slug . 'store', 'uses' => $module_controller . 'download_document']);

    });

    /*----------------------------------------------------------------------------------------
    								attendance
    ----------------------------------------------------------------------------------------*/

    Route::group(array(
        'prefix' => '/attendance'
    ) , function ()
    {

        $route_slug = 'student_attendance_';
        $route_controller = 'Student\AttendanceController@';
        $module_slug = "attendance";

        Route::get('/', ['as' => $route_slug . 'index', 'uses' => $route_controller . 'index']);

        Route::post('/getStudentData', ['as' => $route_slug . 'get_student_data', 'uses' => $route_controller . 'get_student_data']);


        Route::any('export',    [  'as'        => $route_slug.'export',
                                   'uses'      => $route_controller.'export']);
    });
    /*-----------------------------------------------------------------------------------------------
    							Homework 
    ------------------------------------------------------------------------------------------------*/
    Route::group(['prefix' => 'homework'], function ()
    {
        $module_controller = "Student\HomeworkController@";
        Route::get('/', ['as' => 'homework', 'uses' => $module_controller . 'index']);

        Route::get('/get_events', ['as' => 'homework', 'uses' => $module_controller . 'get_events']);

        Route::get('/get_records', ['as' => 'homework', 'uses' => $module_controller . 'get_records']);

        Route::get('get_classes', ['as' => 'multi_action', 'uses' => $module_controller . 'get_class', ]);

        Route::get('/view/{enc_id}', ['as' => 'list', 'uses' => $module_controller . 'view', ]);

        Route::post('/change_status', ['as' => 'list', 'uses' => $module_controller . 'change_status', ]);
        Route::any('export',          [ 'as'        => 'list'.'export', 'uses'      => $module_controller.'export']);
    });

    /*-----------------------------------------------------------------------------------------------
    							TIMETABLE 
    ------------------------------------------------------------------------------------------------*/
    Route::get('/timetable', ['as' => 'timetable', 'uses' => 'Student\TimetableController@index']);

    /*---------------------------------------------------------------------------------------
    | Task
    /*------------------------------------------------------------------------------------------*/
    Route::group(array(
        'prefix' => '/task'
    ) , function ()
    {
        $route_slug = "task";
        $module_controller = "Student\TaskController@";
        $module_slug = 'task';

        Route::get('/', ['as' => $route_slug . 'index', 'uses' => $module_controller . 'index']);

        Route::get('/view/{enc_id}', ['as' => $route_slug . 'view', 'uses' => $module_controller . 'view']);

        Route::post('/change_status', ['as' => 'list', 'uses' => $module_controller . 'change_status', ]);

        Route::any('/change_user_status', ['as' => 'list', 'uses' => $module_controller . 'change_user_status', ]);

    });

    /*---------------------------------------------------------------------------------------
    
    |	Survey
    /* -----------------------------------------------------------------------------------------*/
    Route::group(array(
        'prefix' => '/survey'
    ) , function ()
    {
        $route_slug = "student_survey_";
        $module_controller = "Student\SurveyController@";
        $module_slug = 'survey';

        Route::get('/', ['as' => $route_slug . 'index', 'uses' => $module_controller . 'index']);

        Route::any('/get_records', ['as' => $route_slug . 'get_records', 'uses' => $module_controller . 'get_records']);

        Route::get('/reply_survey/{survey_id}', ['as' => $route_slug . 'view', 'uses' => $module_controller . 'reply_survey']);

        Route::post('/store_survey_reply/{survey_id}', ['as' => $route_slug . 'store_survey_reply', 'uses' => $module_controller . 'store_survey_reply']);

        Route::get('/view_reply/{survey_id}', ['as' => $route_slug . 'view', 'uses' => $module_controller . 'view_reply']);

    });
    /* CLUB
     ------------------------------------------------------------------------------------------*/
    Route::group(array(
        'prefix' => '/club'
    ) , function ()
    {
        $route_slug = "club";
        $module_controller = "Student\ClubController@";
        $module_slug = 'club';

        Route::get('/', ['as' => $route_slug . 'index', 'uses' => $module_controller . 'index']);

        Route::get('/view/{enc_id}', ['as' => $route_slug . 'view', 'uses' => $module_controller . 'view']);

    });

    /*---------------------------------------------------------------------------------------
    | Bus Transport
    /*------------------------------------------------------------------------------------------*/
    Route::group(array(
        'prefix' => '/transport_bus'
    ) , function ()
    {
        $route_slug = "transport_bus";
        $module_controller = "Student\BusTransportController@";
        $module_slug = 'transport_bus';

        Route::get('/', ['as' => $route_slug . 'index', 'uses' => $module_controller . 'index']);

        Route::get('/get_non_student_list', ['as' => $route_slug . 'list', 'uses' => $module_controller . 'get_non_student_list']);

    });

    /*----------------------------------------------------------------------------------------
    canteen_bookings
    ----------------------------------------------------------------------------------------*/

    Route::group(array(
        'prefix' => '/canteen_bookings'
    ) , function ()
    {
        $route_slug = 'student_canteen_bookings_';
        $route_controller = 'Student\CanteenBookingsController@';
        $module_slug = "canteen_bookings";

        Route::get('/', ['as' => $route_slug . 'index', 'uses' => $route_controller . 'index']);

        Route::any('/get_records', ['as' => $route_slug . 'list', 'uses' => $route_controller . 'get_records']);

        Route::get('/create', ['as' => $route_slug . 'create', 'uses' => $route_controller . 'create']);

        Route::post('/store', ['as' => $route_slug . 'store', 'uses' => $route_controller . 'store']);

        Route::post('/update/{enc_id}', ['as' => $route_slug . 'update', 'uses' => $route_controller . 'update']);

        Route::get('/delete/{enc_id}', ['as' => $route_slug . 'delete', 'uses' => $route_controller . 'delete']);

        Route::post('multi_action', ['as' => $route_slug . 'multi_action', 'uses' => $route_controller . 'multi_action']);

        Route::post('add_to_cart', ['as' => $route_slug . 'add_to_cart', 'uses' => $route_controller . 'add_to_cart']);

        Route::post('update_quantity', ['as' => $route_slug . 'update_quantity', 'uses' => $route_controller . 'update_quantity']);

        Route::post('delete_quantity', ['as' => $route_slug . 'delete_quantity', 'uses' => $route_controller . 'delete_quantity']);

        Route::any('checkout', ['as' => $route_slug . 'list', 'uses' => $route_controller . 'checkout']);

        Route::post('get_cart_data', ['as' => $route_slug . 'list', 'uses' => $route_controller . 'get_cart_data']);

        Route::get('view/{enc_id}', ['as' => $route_slug . 'list', 'uses' => $route_controller . 'view']);
        Route::any('export',['as'=> $route_slug.'export', 'uses'=> $route_controller.'export']);

    });
    /*----------------------------------------------------------------------------------------
    //  News
    // ----------------------------------------------------------------------------------------*/
    Route::group(array(
        'prefix' => '/news'
    ) , function ()
    {

        $route_slug = "admin_users_";
        $module_slug = "admin_users";
        $module_controller = "Student\NewsController@";

        Route::get('/', ['as' => $route_slug . 'index', 'uses' => $module_controller . 'index']);

        Route::any('/get_records', ['as' => $route_slug . 'get_records', 'uses' => $module_controller . 'get_records']);

        Route::get('/view/{enc_id}', ['as' => $route_slug . 'view', 'uses' => $module_controller . 'view']);

        Route::any('/download_document/{enc_id}', ['as' => $route_slug . 'download_document', 'uses' => $module_controller . 'download_document']);

    });

    /*----------------------------------------------------------------------------------------
    suggestions
    ----------------------------------------------------------------------------------------*/

    Route::group(array(
        'prefix' => '/suggestions'
    ) , function ()
    {
        $route_slug = 'student_suggestion_';
        $route_controller = 'Student\SuggestionsController@';
        $module_slug = "suggestion";

        Route::get('/create', ['as' => $route_slug . 'create', 'uses' => $route_controller . 'create']);

        Route::post('/store', ['as' => $route_slug . 'store', 'uses' => $route_controller . 'store']);

        Route::post('/update/{enc_id}', ['as' => $route_slug . 'update', 'uses' => $route_controller . 'update']);

        Route::post('/add_vote', ['as' => $route_slug . 'add_vote', 'uses' => $route_controller . 'add_vote']);

        Route::get('/delete/{enc_id}', ['as' => $route_slug . 'delete', 'uses' => $route_controller . 'delete']);

        Route::get('/edit/{enc_id}', ['as' => $route_slug . 'edit', 'uses' => $route_controller . 'edit']);

        Route::post('multi_action', ['as' => $route_slug . 'multi_action', 'uses' => $route_controller . 'multi_action']);

        Route::get('/view/{status}/{enc_id}', ['as' => $route_slug . 'list', 'uses' => $route_controller . 'view']);

        Route::post('/change_status', ['as' => $route_slug . 'update', 'uses' => $route_controller . 'change_status']);

        Route::post('/raise_poll', ['as' => $route_slug . 'update', 'uses' => $route_controller . 'raise_poll']);

        Route::get('/{status}', ['as' => $route_slug . 'index', 'uses' => $route_controller . 'index']);

    });

    /*---------------------------------------------------------------------------------------
    | exam
    /*------------------------------------------------------------------------------------------*/
    Route::group(array(
        'prefix' => '/todo'
    ) , function ()
    {

        $route_slug = "todo";
        $module_slug = "todo";
        $module_controller = "Student\ToDoController@";

        Route::any('/delete_todo', ['as' => $route_slug . 'delete_todo', 'uses' => $module_controller . 'delete_todo']);

        Route::any('/mark_as_read_todo', ['as' => $route_slug . 'mark_as_read_todo', 'uses' => $module_controller . 'mark_as_read_todo']);

        Route::any('/store', ['as' => $route_slug . 'store', 'uses' => $module_controller . 'store']);

        Route::get('/', ['as' => $route_slug . 'index', 'uses' => $module_controller . 'index']);
        Route::any('export',    [  'as'        => $route_slug.'export',
                                   'uses'      => $module_controller.'export']);

    });

    /*----------------------------------------------------------------------------------------
    Notification Settings
    ----------------------------------------------------------------------------------------*/
    Route::group(array(
        'prefix' => '/notification_settings'
    ) , function ()
    {

        $route_slug = "notification_settings";
        $module_slug = "notification_settings_";
        $route_controller = "Student\NotificationSettingController@";

        Route::any('/', ['as' => $route_slug . 'index', 'uses' => $route_controller . 'index']);

        Route::post('/store', ['as' => $route_slug . 'create', 'uses' => $route_controller . 'store']);

    });
});