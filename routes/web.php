<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use GuzzleHttp\Middleware;

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
$router->get('/', function(){return view('portal.index');});
$router->get('/signup', function(){return view('portal.signinup');});
$router->get('/profile',function(){return view('portal.profile');});
$router->get('/profiletest',function(){return view('portal.file');});



//------------------------ API ------------------------
$router->group(['prefix' => 'api'], function ($router) {

  $router->get('/services/location/{locationId}', 'ProfileController@servicesFlow');

  $router->post('/getavatar/', 'ProfileController@getAvatar');
  $router->post('/gravatar', 'EmailAuthController@gravatar');
  $router->post('/emailbykey', 'ProfileController@getUserInfo');
  $router->post('/channels', 'ProfileController@getChannels');
  //setter
  $router->post("/file", [
    'as' => 'file', 'uses' => 'ProfileController@setAvatar'
  ]);
  $router->get('/', function () use ($router) {
    return view('api.index');
  });
  $router->post('/keys', 'ConfigController@getKeys');

  $router->get('/locations/{zipcode}/services', "LocationController@getServicesByZipCode");
  $router->get('/locations', 'LocationController@getLocationsHaveServices');

  $router->group(['prefix' => 'auth'], function ($router) {
    //email
    $router->post('/email', 'EmailAuthController@identification');
    $router->get('/email/verify/{key}', 'EmailAuthController@authentication');
    //telegram
    $router->post('/tg/verify', 'TelegramAuthController@authentication');
    //telegram channel
    $router->post('/tg/channel', 'ProfileController@confirmChannel');
    //email channel
    $router->post('/email/channel', 'ProfileController@confirmEmailChannel');
    $router->get('/emailchannel/verify/{key}', 'ProfileController@authentication');
    $router->post('/emailchannel', 'ProfileController@identification');
  });


  $router->post('/keys', 'ConfigController@getKeys');

  $router->group(['prefix' => 'locations'], function ($router) {
    $router->get('/{zipcode}/services', "LocationController@getServicesByZipCode");
    $router->get('/', 'LocationController@getLocationsHaveServices');
  });

  $router->group(['prefix' => 'profile'], function ($router) {
    //lastname/firstname/language
    $router->get('/personalDetails', 'ProfileController@getPersonalDetails');
    $router->post('/personalDetails', 'ProfileController@setPersonalDetails');
    //avatar
    $router->get('/avatar', 'ProfileController@getAvatar');
    $router->post('/avatar', 'ProfileController@setAvatar');
    $router->delete('/avatar', 'ProfileController@deleteAvatar');
    //user locations
    $router->get('/userLocations', 'ProfileController@getUserLocations');
    $router->post('/userLocations', 'ProfileController@addUserLocation');
    $router->patch('/userLocations', 'ProfileController@setUserLocation');
    $router->delete('/userLocations', 'ProfileController@deleteUserLocation');
  });
});
