<?php

use App\Model\Canvas;
use GuzzleHttp\Client;

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

$router->get('/payment-session', function () use ($router) {

    $client = new Client( [ 'base_uri' => env('PAGSEGURO_URL') ] );

    $email = env('PAGSEGURO_EMAIL');
    $token = env('PAGSEGURO_TOKEN');

    $endpoint = '/sessions?email='.$email.'&token='.$token;

    $response = $client->request('POST', $endpoint);
    
    $response = new SimpleXMLElement($response->getBody()->getContents());

    $token = (string) $response->id;

    return response()->json([
        'message' => 'SUCCESS',
        'data' => [
            'token' => $token
        ]
    ]);

});

$router->post('/payment', 'PaymentController@pay');

