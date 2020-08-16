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
| Auth routes
|--------------------------------------------------------------------------
|
*/

$router->post('/auth/login', 'AuthController@login');

/*
|--------------------------------------------------------------------------
| Lists routes
|--------------------------------------------------------------------------
|
*/

$router->group(['prefix' => 'lists'], function () use ($router) {
    $router->get('/', 'ListController@index');
    $router->post('/', 'ListController@store');
    $router->get('/{list}', 'ListController@show');
    $router->put('/{list}', 'ListController@update');
    $router->delete('/{list}', 'ListController@destroy');
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
