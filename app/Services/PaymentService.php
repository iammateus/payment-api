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
        $notificationUrl = env('PAGSEGURO_NOTIFICATION_URL');

        $itemsParams = $this->formatItemsFromArray($paymentOptions['items']);

        $params = [
            'paymentMode' => 'default',
            'paymentMethod' => 'boleto',
            'currency' => 'BRL',
            'notificationURL' => $notificationUrl,
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

        $params = array_merge( $params, $itemsParams );

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

    public function formatItemsFromArray(array $items): array
    {
        $formatedItems = [];

        foreach ($items as $key => $item) {
            $itemNumber = $key + 1;
            $formatedItems['itemId'.$itemNumber] = $item['id'];
            $formatedItems['itemDescription'.$itemNumber] = $item['description'];
            $formatedItems['itemQuantity'.$itemNumber] = $item['quantity'];
            $formatedItems['itemAmount'.$itemNumber] = $item['amount'];
        }

        return $formatedItems;
    }

}
