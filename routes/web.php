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
$router->get('/signup', function () use ($router) {
    return view('portal.signinup');
});
$router->group(['prefix' => 'api'], function ($router) {

    $router->get('/', function () use ($router) {
        return view('api.index');
    });

    $router->get('/locations/{zipcode}/services',"LocationController@getServicesByZipCode");
    $router->get('/locations', 'LocationController@getLocationsHaveServices');
    $router->post('/signup', 'SigninupController@store');

    $router->get('/signup/key/{key}', 'SigninupController@onLinkClick');
    $router->get('/signup/testSetKey', 'SigninupController@testSetKeyView');
    $router->post('/signup/setLoginKey', 'SigninupController@setLoginKey');
});
