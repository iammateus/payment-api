<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Utils\ResponseParser;

class PaymentService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client( [ 'base_uri' => env('PAGSEGURO_URL') ] );
    }

    public function pay(array $paymentOptions): object
    {
        $email = env('PAGSEGURO_EMAIL');
        $token = env('PAGSEGURO_TOKEN');

        $params = [
            'paymentMode' => 'default',
            'paymentMethod' => 'boleto',
            'currency' => 'BRL',
            'extraAmount' => '0.00',
            'itemId1' => '0001',
            'itemDescription1' => 'Notebook Prata',
            'itemAmount1' => '24300.00',
            'itemQuantity1' => 1,
            'notificationURL' => 'https://sualoja.com.br/notifica.html',
            'reference' => 'REF1234',
            'senderName' => $paymentOptions['sender']['name'],
            'senderCPF' => $paymentOptions['sender']['document']['value'],
            'senderAreaCode' => $paymentOptions['sender']['phone']['areaCode'],
            'senderPhone' => $paymentOptions['sender']['phone']['number'],
            'senderEmail' => 'test@sandbox.pagseguro.com.br',
            'senderHash' => $paymentOptions['sender']['hash'],
            'shippingAddressRequired' => 'false',
            'email' => $email,
            'token' => $token,
        ];

        $response = $this->client->request('POST', 'transactions',
            [
                'form_params' => $params,
                'query' => [
                    'email' => $email,
                    'token' => $token,
                ]
            ]
        );

        $response = ResponseParser::parse($response);

        return $response;
    }
}
