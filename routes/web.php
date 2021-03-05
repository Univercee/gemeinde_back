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

$router->get('/', function () use ($router) {
    return view('portal.index');
});
$router->get('/email/verify/{key}', function () use ($router){
   return view('portal.verifypage');
});
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->post('/profile','ProfileController@userInfo');
});

$router->get('/signup', function(){return view('portal.signinup');});
$router->group(['prefix' => 'api'], function ($router) {

    $router->get('/', function () use ($router) {return view('api.index');});
    $router->post('/keys', 'ConfigController@getKeys');

    $router->get('/locations/{zipcode}/services',"LocationController@getServicesByZipCode");
    $router->get('/locations', 'LocationController@getLocationsHaveServices');

    $router->group(['prefix' => 'auth'], function ($router) {

        //email
        $router->post('/email', 'EmailAuthController@identification');
        $router->get('/email/verify/{key}', 'EmailAuthController@authentication');
        //telegram
        $router->post('/tg/verify', 'TelegramAuthController@authentication');
    });
});
