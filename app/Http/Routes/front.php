<?php 
Route::group(array('prefix' => '/','middleware'=>['front']), function()
{
$route_slug       = "";
$module_controller = "Front\HomeController@";

Route::get('',['as' => $route_slug.'index',  'uses' => $module_controller.'index']);
 


Route::get('contactus/',							    		[	'as'	=> 'login', 'uses'	=> 'Front\HomeController@contact_us']);

Route::post('store_contact/',						 			[	'as'	=> 'login', 'uses'	=> 'Front\HomeController@store_contact']);


Route::get('faq/',								     			[	'as'	=> 'login', 'uses'	=> 'Front\FaqController@index']);

Route::post('store_faq/',				     					[	'as'	=> 'login', 'uses'	=> 'Front\FaqController@store_faq']);


Route::get('about_us/',  					    				[	'as'	=> 'about_us', 'uses'	=> 'Front\HomeController@about_us']);

Route::get('benifits/',  					    				[	'as'	=> 'benifits', 'uses'	=> 'Front\HomeController@benifits']);






Route::any('validate_reset_password_link/{enc_id}/{enc_reminder_code}', 
[	'as'	=> 'validate_reset_password_link', 'uses'	=> 'Front\AuthController@validate_reset_password_link']);

Route::any('reset_password/',  									[	'as'	=> 'reset_password', 'uses'	=> 'Front\AuthController@reset_password']);
});




Route::any('personal_details/',									[	'as'	=> 'personal_details', 'uses'	=> 'Front\AuthController@personal_details']);

Route::post('personal_store',									[	'as'	=> 'personal_store', 'uses'	=> 'Front\AuthController@personal_store']);

Route::get('business_details/',									[	'as'	=> 'business_details', 'uses'	=> 'Front\AuthController@business_details']);


Route::any('business_store/',									[	'as'	=> 'business_store', 'uses'	=> 'Front\AuthController@business_store']);


Route::any('finance_details/',									[	'as'	=> 'finance_details', 'uses'	=> 'Front\AuthController@finance_details']);

Route::any('finance_store/',									[	'as'	=> 'finance_store', 'uses'	=> 'Front\AuthController@finance_store']);



Route::any('welcome/',											[   'as'    =>'welcome','uses'=>'Front\AuthController@welcome']);


Route::any('process_login/',	   								[	'as'	=> 'process_login', 'uses'	=> 'Front\AuthController@process_login']);

Route::any('login/',	   								  		[	'as'	=> 'login', 'uses'	=> 'Front\AuthController@login']);

Route::any('store_user/',	   								  	[	'as'	=> 'store_user', 'uses'	=> 'Front\AuthController@store_user']);

Route::get('logout/',											[   'as'    => 'logout','uses' => 'Front\AuthController@logout']);

Route::any('verify/{user_id}/{activation_code}',	    	  	[	'as'	=> 'verify', 'uses'	=> 'Front\AuthController@verify']);

Route::any('process_forgot_password',	    				  	[	'as'	=> 'process_forgot_password', 'uses'	=> 'Front\AuthController@process_forgot_password']);



Route::any('email_exist/',  									[	'as'	=> 'email_exist', 'uses'	=> 'Front\AuthController@email_exist']);

Route::any('verify_email_phone/',  								[	'as'	=> 'verify_email_phone', 'uses'	=> 'Front\AuthController@verify_email_phone']);

Route::any('resend_otp/',  										[	'as'	=> 'resend_otp', 'uses'	=> 
'Front\AuthController@resend_otp']);

Route::any('validate_username/',  								[	'as'	=> 'validate_username', 'uses'	=> 'Front\AuthController@validate_username']);

Route::any('registration/',  									[	'as'	=> 'registration', 'uses'	=> 'Front\AuthController@registration']);



Route::any('get_shop/',[	'as'	=> 'get_shop', 'uses'	=> 'Common\CommonDataController@get_shop']);
Route::any('getbusiness_from/',									[	'as'	=> 'getbusiness_from', 'uses'	=> 'Common\CommonDataController@getbusiness_from']);

Route::any('get_subcategory/',									[	'as'	=> 'get_subcategory', 'uses'	=> 'Common\CommonDataController@get_subcategory']);


Route::any('get_itemproduct/',									[	'as'	=> 'get_itemproduct', 'uses'	=> 'Common\CommonDataController@get_itemproduct']);

Route::any('get_producttype/',									[	'as'	=> 'get_producttype', 'uses'	=> 'Common\CommonDataController@get_producttype']);




Route::any('remove_image/',										[	'as'	=> 'remove_image', 'uses'	=> 'Common\CommonDataController@remove_image']);


Route::any('delete_image/',									    [	'as'	=> 'delete_image', 'uses'	=> 'Common\CommonDataController@delete_image']);

Route::any('delete_quality/',									    [	'as'	=> 'delete_quality', 'uses'	=> 'Common\CommonDataController@delete_quality']);

Route::any('get_productbrand/',									[	'as'	=> 'get_productbrand', 'uses'	=> 'Common\CommonDataController@get_productbrand']);

Route::any('get_productquality/',								[	'as'	=> 'get_productquality', 'uses'	=> 'Common\CommonDataController@get_productquality']);

Route::any('get_productquantity/',								[	'as'	=> 'get_productquantity', 'uses'	=> 'Common\CommonDataController@get_productquantity']);


Route::any('/upload_profile_image',						['as' => 'upload_profile_image','uses' => 'Common\CommonDataController@upload_profile_image']);


Route::any('/upload_cover_image',						['as' => 'upload_cover_image','uses' => 'Common\CommonDataController@upload_cover_image']);

Route::any('/remove_catelog_image',						['as' => 'remove_catelog_image','uses' => 'Common\CommonDataController@remove_catelog_image']);

/*******************Global Search****************************/


Route::group(array('prefix' => '/retailer','middleware'=>['authenticate_user']), function()
{
	include(app_path('Http/Routes/Retailer/retailer.php'));
});

Route::group(array('prefix' => '/wholesaler','middleware'=>['authenticate_user']), function()
{
	include(app_path('Http/Routes/Wholesaler/wholesaler.php'));
});

Route::group(array('prefix' => '/distributor','middleware'=>['authenticate_user']), function()
{
	include(app_path('Http/Routes/Distributor/distributor.php'));
});

Route::group(array('prefix' => '/manufacturer','middleware'=>['authenticate_user']), function()
{
	include(app_path('Http/Routes/Manufacturer/manufacturer.php'));
});


/*******************Cron Job****************************/

Route::any('cronJob/',								[	'as'	=> 'cronJob', 'uses'	=> 'Common\CommonDataController@cronJob']);
