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
$router->get('/signup', function(){return view('portal.signup');});
$router->get('/profile',function(){return view('portal.profile');});
$router->get('/profiletest',function(){return view('portal.file');});


$router->get('/file', function (){return view('portal.file');});

//------------------------ API ------------------------
$router->group(['prefix' => 'api'], function ($router) {

  $router->get('/', function () use ($router) {
    return view('api.index');
  });

  $router->get('/keys', 'ConfigController@getKeys');

  $router->group(['prefix' => 'auth'], function ($router) {
    //email
    $router->post('/email', 'EmailAuthController@authenticate');
    $router->get('/email/verify/{key}', 'EmailAuthController@verify');
    //telegram
    $router->post('/tg/verify', 'TelegramAuthController@authentication');
  });

  $router->group(['prefix' => 'locations'], function ($router) {
    $router->get('/{zipcode}/services', "LocationController@getServicesByZipCode");
    $router->get('/', 'LocationController@getLocationsHaveServices');
    $router->get('/all', 'LocationController@getAllLocations');
    //location services
    $router->get('/services/{locationId}/{user_location_id}', 'ProfileController@servicesFlow');
  });

  $router->group(['prefix' => 'profile'], function ($router) {
    //lastname, firstname, language
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
    //channels
    $router->get('/channels', 'ProfileController@getChannels');
    $router->post('/channels/email', 'ProfileController@addEmailChannel');
    $router->post('/channels/email/verify/{key}', 'ProfileController@emailChannelVerify');
    $router->delete('/channels/email/delete', 'ProfileController@deleteEmailChannel');
    $router->post('/channels/tg/verify', 'ProfileController@tgChannelVerify');
    $router->delete('/channels/tg/delete', 'ProfileController@deleteTgChannel');
    //
    $router->patch('/services', 'ProfileController@setUserServices');
    $router->delete('/services', 'ProfileController@deleteUserServices');
  });
});
