<?php

namespace App\Http\Controllers;

use App\Classes\Item;
use GuzzleHttp\Client;
use App\Classes\ItemList;
use App\Helpers\ResponseParser;

class PaymentService extends Controller
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client( [ 'base_uri' => env('PAGSEGURO_URL') ] );
    }

    public function pay(array $options): object
    {
        $email = env('PAGSEGURO_EMAIL');
        $token = env('PAGSEGURO_TOKEN');

        $methodParams = $this->parseBoletoPaymentParams($options);

        $defaultParams = $this->parseDefaultPaymentParams($options);
        
        $items = [];

        foreach ($options['items'] as $item){
            $items[] = new Item($item['id'], $item['description'], $item['quantity'], $item['amount']);
        }

        $items = new ItemList($items);

        $itemsParams = $this->parseItems($items);
        
        $params = array_merge( $defaultParams, $itemsParams, $methodParams );

        $response = $this->client->request('POST', 'transactions',
            [
                'form_params' => $params,
                'query' => [
                    'email' => $email,
                    'token' => $token,
                ]
            ]
        );

        $response = ResponseParser::parseXml($response);

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

    public function parseItems (ItemList $items): array
    {
        $list = $items->getContent();
        $parsed = [];

        foreach ($list as $key => $item) {
            $parsedKey = $key + 1;
            $parsed['itemId' . $parsedKey] = $item->id;
            $parsed['itemDescription' . $parsedKey] = $item->description;
            $parsed['itemQuantity' . $parsedKey] = $item->quantity;
            $parsed['itemAmount' . $parsedKey] = number_format($item->amount, 2, '.', '');
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
