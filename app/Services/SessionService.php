<?php

namespace App\Services;

use App\Helpers\ResponseParser;
use App\Helpers\SimpleXMLElementParser;
use GuzzleHttp\Client;

class SessionService
{
    private Client $client;
    
    public function __construct (Client $client)
    {
        $this->client = $client;
    }

    public function getSessionTokenFromPagseguroApi (): array
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

        $response = ResponseParser::parseXml($response);

        $response = SimpleXMLElementParser::parseToArray($response);

        return $response;
    }
}
