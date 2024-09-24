<?php
/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::get('cache_clear', function () {
		\Artisan::call('cache:clear');
			//  Clears route cache
		\Artisan::call('route:clear');
		\Cache::flush();
		\Artisan::call('optimize');
		exec('composer dump-autoload');

		dd("Cache cleared!");
	});


/*----------------------------------------------------------------------------------------
	Front Roles
----------------------------------------------------------------------------------------*/

Route::group(array('prefix' => '/','middleware'=>['web','front']), function()
{
	$route_slug       = "";
	$module_controller = "Front\HomeController@";

	Route::get('',['as' => $route_slug.'index',  'uses' => $module_controller.'index']);


	$route_slug       = "";
	$module_controller = "SchoolAdmin\AuthController@";

	Route::get('login',						[	'as' 	=> $route_slug.'index',  
												'uses'  => $module_controller.'login']);

	Route::post('login/process_login',		[	'as'	=> $route_slug.'process_login',
												'uses'	=> $module_controller.'process_login']);

	Route::any('login/role_login', 			[	'as'	=> $route_slug.'role_login',
												'uses'	=> $module_controller.'role_login']);

	Route::post('login/login_process',		[	'as'	=> $route_slug.'login_process',
												'uses'	=> $module_controller.'login_process']);

	Route::post('login/process_forgot_password',	
											[	'as'	=> $route_slug.'forgot_password',
												'uses'	=> $module_controller.'process_forgot_password']);

	Route::get('login/validate_admin_reset_password_link/{enc_id}/{enc_reminder_code}', 	
											[	'as'	=> $route_slug.'validate_admin_reset_password_link',
												'uses'	=> $module_controller.'validate_reset_password_link']);

	Route::post('login/reset_password',		[	'as'	=> $route_slug.'reset_password',
												'uses'	=> $module_controller.'reset_password']);
	
	Route::get('school_admin/logout',		[   'as'    => $route_slug.'index',  
												'uses'  => $module_controller.'logout']);

});

/*---------------------------------------------------------------------------------------
	End
-----------------------------------------------------------------------------------------*/


/*----------------------------------------------------------------------------------------
	Super Admin
----------------------------------------------------------------------------------------*/

$admin_path = config('app.project.admin_panel_slug');

Route::group(['middleware' => ['web']], function ()use($admin_path)  
{
	include(app_path('Http/Routes/Admin/route_admin.php'));
});

/*----------------------------------------------------------------------------------------
	School Admin
----------------------------------------------------------------------------------------*/


$schooladmin_path = config('app.project.role_slug.school_admin_role_slug');
Route::group(['middleware' => ['web']], function ()use($schooladmin_path) 
{
	include(app_path('Http/Routes/SchoolAdmin/schooladmin.php'));
});		

/*----------------------------------------------------------------------------------------
	Student
----------------------------------------------------------------------------------------*/

$student_path = config('app.project.role_slug.student_role_slug');

Route::group(['middleware' => ['web']], function ()use($student_path) 
{
	include(app_path('Http/Routes/Student/student.php'));
});		

/*----------------------------------------------------------------------------------------
	Professor
-------------------------------------------------------------------------------------------*/

$professor_path = config('app.project.role_slug.professor_role_slug');

Route::group(['middleware' => ['web']],function()use($professor_path)
{
	include(app_path('Http/Routes/Professor/professor.php'));
});

/*----------------------------------------------------------------------------------------
	Parent
-------------------------------------------------------------------------------------------*/

$parent_path = config('app.project.role_slug.parent_role_slug');

Route::group(['middleware' => ['web']],function()use($parent_path)
{
	include(app_path('Http/Routes/Parent/parent.php'));
});



