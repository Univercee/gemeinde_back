<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//------------------------ PAGES ------------------------
$router->get('/', function () use ($router) {return view('portal.index');});
$router->get('/signup', function(){return view('portal.signinup');});
$router->get('/profile',function(){return view('portal.profile');});
$router->get('/file',function(){return view('portal.file');});			//to test getter and setter



//------------------------ MIDDLEWARE ------------------------
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->post('/profile','ProfileController@userInfo');
});



//------------------------ API ------------------------
$router->group(['prefix' => 'api'], function ($router) {
	$router->get('/', function () use ($router) {return view('api.index');});
	$router->post('/keys', 'ConfigController@getKeys');
	$router->post('/getavatar/', 'ProfileController@getAvatar');
	$router->post('/gravatar', 'EmailAuthController@gravatar');
	//setter
	$router->post("/file",['as'=>'file', 'uses'=> 'ProfileController@setAvatar']);
	

	$router->group(['prefix' => 'locations'], function ($router) {
		$router->get('/{zipcode}/services',"LocationController@getServicesByZipCode");
		$router->get('/', 'LocationController@getLocationsHaveServices');
	});
	

	$router->group(['prefix' => 'auth'], function ($router) {
		//email
		$router->post('/email', 'EmailAuthController@identification');
		$router->get('/email/verify/{key}', 'EmailAuthController@authentication');
		//telegram
		$router->post('/tg/verify', 'TelegramAuthController@authentication');
	});


	$router->group(['prefix' => 'profile'], function ($router) {
		$router->get('/profileHead', 'ProfileController@getProfileHead');
		$router->post('/profileHead', 'ProfileController@setProfileHead');
	});
});
