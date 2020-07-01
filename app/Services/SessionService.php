<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseParser;
use GuzzleHttp\Client;

class SessionService extends Controller
{
    private Client $client;
    
    public function __construct ()
    {
        $this->client = new Client( [ 'base_uri' => env('PAGSEGURO_URL') ] );
    }

    public function store (): object
    {
        $email = env('PAGSEGURO_EMAIL');
        $token = env('PAGSEGURO_TOKEN');

        $response = $this->client->request('POST', 'sessions', [
            'query' => [
                'email' => $email,
                'token' => $token
            ]
        ]);

        return ResponseParser::parseXml($response);
    }
}
