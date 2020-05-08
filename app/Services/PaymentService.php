<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use App\Utils\ResponseParser;

class PaymentService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client( [ 'base_uri' => env('PAGSEGURO_URL') ] );
    }

    public function session(): object
    {
        $email = env('PAGSEGURO_EMAIL');
        $token = env('PAGSEGURO_TOKEN');

        $response = $this->client->request('POST', 'sessions', [
            'query' => [
                'email' => $email,
                'token' => $token
            ]
        ]);

        $response = ResponseParser::parse($response);
    
        return $response;
    }

    public function pay(array $paymentOptions): object
    {
        $email = env('PAGSEGURO_EMAIL');
        $token = env('PAGSEGURO_TOKEN');

        $paymentParams = [];

        switch ($paymentOptions['method']) {
            case 'BOLETO':
                $paymentParams = $this->getBoletoPaymentParams($paymentOptions);
            break;
            case 'CREDIT_CARD':
                $paymentParams = $this->getCreditCardPaymentParams($paymentOptions);
            break;
            case 'ONLINE_DEBIT':
                $paymentParams = $this->getOnlineDebitPaymentParams($paymentOptions);
            break;
            default:
                throw new Exception('Payment method not supported', 1);
            break;
        }

        $defaultParams = $this->getDefaultPaymentParams($paymentOptions);

        $itemsParams = $this->formatItemsFromArray($paymentOptions['items']);
        
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

    public function getDefaultPaymentParams(array $paymentOptions): array
    {
        $notificationUrl = env('PAGSEGURO_NOTIFICATION_URL');

        $params = [
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
            'extraAmount' => $paymentOptions['extraAmount']
        ];

        return $params;
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
            'creditCardHolderName' => $paymentOptions['creditCard']['holder']['name'],
            'creditCardHolderCPF' => $paymentOptions['creditCard']['holder']['document']['value'],
            'creditCardHolderBirthDate' => $paymentOptions['creditCard']['holder']['birthDate'],
            'creditCardHolderAreaCode' => $paymentOptions['creditCard']['holder']['phone']['areaCode'],
            'creditCardHolderPhone' => $paymentOptions['creditCard']['holder']['phone']['number'],
            'billingAddressStreet' => $paymentOptions['billing']['street'],
            'billingAddressNumber' => $paymentOptions['billing']['number'],
            'billingAddressComplement' => $paymentOptions['billing']['complement'],
            'billingAddressDistrict' => $paymentOptions['billing']['district'],
            'billingAddressPostalCode' => $paymentOptions['billing']['postalCode'],
            'billingAddressCity' => $paymentOptions['billing']['city'],
            'billingAddressState' => $paymentOptions['billing']['state'],
            'billingAddressCountry' => 'BRA'
        ];

        return $params;
    }
    
    public function getOnlineDebitPaymentParams(array $paymentOptions): array
    {
        $params = [
            'paymentMethod' => 'eft',
            'bankName' => $paymentOptions['bank']['name']
        ];

        return $params;
    }
}
