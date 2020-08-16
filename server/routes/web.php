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
    return $router->app->version();
});

/*
|--------------------------------------------------------------------------
| Users routes
|--------------------------------------------------------------------------
|
*/

$router->group(['prefix' => 'users'], function () use ($router) {
    $router->post('/', 'UserController@store');
    $router->get('/me', 'UserController@show');
    $router->put('/me', 'UserController@update');
    $router->delete('/me', 'UserController@destroy');
});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/login', 'AuthController@login');
});
