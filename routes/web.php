<?php

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

$router->post('login', 'AuthenticationController@login');
$router->post('account/register', 'AuthenticationController@register');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->post('account/deposit', 'AccountsController@deposit');
    $router->get('account/balance', 'AccountsController@balance');

    $router->get('btc/price', 'CryptController@price');
    $router->post('btc/purchase', 'CryptController@purchase');
    $router->post('btc/sell', 'CryptController@sell');
    $router->get('btc', 'CryptController@position');
});
