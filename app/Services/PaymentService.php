<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Helpers\ResponseParser;
use App\Helpers\SimpleXMLElementParser;

class PaymentService extends Controller
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function pay(array $options): array
    {
        $email = env('PAGSEGURO_EMAIL');
        $token = env('PAGSEGURO_TOKEN');

        $methodParams = $this->parseBoletoPaymentParams($options);

        $defaultParams = $this->parseDefaultPaymentParams($options);
        
        $itemsParams = $this->parseItems($options['items']);
        
        $params = array_merge( $defaultParams, $itemsParams, $methodParams );

        $link = env('PAGSEGURO_URL') . '/transactions';

        $response = $this->client->request('POST', $link,
            [
                'form_params' => $params,
                'query' => [
                    'email' => $email,
                    'token' => $token,
                ]
            ]
        );

        $response = ResponseParser::parseXml($response);

        $response = SimpleXMLElementParser::parseToArray($response);

        return $response;
    }

    /**
     * Parsing default params of a payment request to Pagseguro
     *
     * @param array $options Internal service formatted data
     * @return array Pagseguro formatted data
     */
    public function parseDefaultPaymentParams (array $options) 
    {
        $parsed = [
            'paymentMode' => 'default',
            'currency' => 'BRL',
            'notificationURL' => env("PAGSEGURO_NOTIFICATION_URL"),
            'senderName' => $options['sender']['name'],
            'senderCPF' => $options['sender']['document']['value'],
            'senderAreaCode' => $options['sender']['phone']['areaCode'],
            'senderPhone' => $options['sender']['phone']['number'],
            'senderEmail' => $options['sender']['email'],
            'senderHash' => $options['sender']['hash'],
            'shippingAddressRequired' => $options['shipping']['addressRequired'],
            'extraAmount' => number_format($options['extraAmount'], 2, '.', '')
        ];
        
        return $parsed;
    }

    public function parseItems (array $list): array
    {
        $parsed = [];

        foreach ($list as $key => $item) {
            $parsedKey = $key + 1;
            $parsed['itemId' . $parsedKey] = $item['id'];
            $parsed['itemDescription' . $parsedKey] = $item['description'];
            $parsed['itemQuantity' . $parsedKey] = $item['quantity'];
            $parsed['itemAmount' . $parsedKey] = number_format($item['amount'], 2, '.', '');
        }

        return $parsed;
    }

    public function parseBoletoPaymentParams(): array
    {
        $parsed = [
            'paymentMethod' => 'boleto',
        ];

        return $parsed;
    }
}
