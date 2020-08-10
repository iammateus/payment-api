<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseParser;
use GuzzleHttp\Client;

class SessionService extends Controller
{
    private Client $client;
    
    public function __construct (Client $client)
    {
        $this->client = $client;
    }

    public function store (): object
    {
        $email = env('PAGSEGURO_EMAIL');
        $token = env('PAGSEGURO_TOKEN');

        $link = env('PAGSEGURO_URL') . '/sessions';

        $response = $this->client->request('POST', $link, [
            'query' => [
                'email' => $email,
                'token' => $token
            ]
        ]);

        return ResponseParser::parseXml($response);
    }
}
