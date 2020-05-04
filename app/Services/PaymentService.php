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

        $paymentParams = [];

        switch ($paymentOptions['method']) {
            case 'BOLETO':
                $paymentParams = $this->getBoletoPaymentParams($paymentOptions);
            break;
            case 'CREDIT_CARD':
                $paymentParams = $this->getCreditCardPaymentParams($paymentOptions);
            break;
            default:
            break;
        }

        $defaultParams = [
            'paymentMode' => 'default',
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
            'extraAmount' => $paymentOptions['extraAmount']
        ];

        $params = array_merge( $defaultParams, $itemsParams, $paymentParams );

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

    public function getBoletoPaymentParams(array $paymentOptions): array
    {
        $params = [
            'paymentMethod' => 'boleto',
        ];

        return $params;
    }
    
    public function getCreditCardPaymentParams(array $paymentOptions): array
    {
        $params = [
            'paymentMethod' => 'creditCard',
            'creditCardToken' => $paymentOptions['creditCard']['token'],
            'installmentQuantity' => $paymentOptions['creditCard']['installment']['quantity'],
            'installmentValue' => number_format($paymentOptions['creditCard']['installment']['installmentAmount'], 2),
            'noInterestInstallmentQuantity' => $paymentOptions['creditCard']['maxInstallmentNoInterest'],
            'creditCardHolderName' => 'Jose Comprador',
            'creditCardHolderCPF' => '22111944785',
            'creditCardHolderBirthDate' => '27/10/1987',
            'creditCardHolderAreaCode' => '11',
            'creditCardHolderPhone' => '56273440',
            'billingAddressStreet' => 'Av. Brig. Faria Lima',
            'billingAddressNumber' => '1384',
            'billingAddressComplement' => '5o andar',
            'billingAddressDistrict' => 'Jardim Paulistano',
            'billingAddressPostalCode' => '01452002',
            'billingAddressCity' => 'Sao Paulo',
            'billingAddressState' => 'SP',
            'billingAddressCountry' => 'BRA'
        ];

        return $params;
    }

}
