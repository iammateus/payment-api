<?php

namespace App\Utils\Tests;

use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;

trait UserLoggable
{
    use MakesHttpRequests;
    
    public function getApiToken($email, $password): string
    {
        $data = [
            'email' => $email,
            'password' => $password
        ];

        $this->post('/login', $data);

        $response = $this->response;
        $responseContent = json_decode($response->content());

        return $responseContent->data->api_token;
    }
}