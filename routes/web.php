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

$router->get('/healthcheck', function () use ($router) {
    return response()->json([
        'The server is running (Canvas)'
    ]);
});

$router->get('/session', 'SessionController@store');

$router->post('/payment', 'PaymentController@store');
