<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Helpers\ResponseParser;
use App\Helpers\SimpleXMLElementParser;

class PaymentService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function makePagseguroPayment(array $options): array
    {
        switch ($options['method']) {
            case 'BOLETO':
                return $this->payWithBoleto($options);
            case 'CREDIT_CARD':
                return $this->payWithCreditCard($options);
        }
    }

    public function payWithBoleto(array $options): array
    {
        $methodParams = $this->parseBoletoPaymentParams($options);
        $defaultParams = $this->parseDefaultPaymentParams($options);
        $itemsParams = $this->parseItems($options['items']);
        $params = array_merge($defaultParams, $itemsParams, $methodParams);

        $xmlEncodedPagseguroResponse = $this->makePagseguroRequest($params);
        $xmlObjectPagseguroResponse = ResponseParser::parseXml($xmlEncodedPagseguroResponse);
        $arrayPagseguroResponse = SimpleXMLElementParser::parseToArray($xmlObjectPagseguroResponse);

        $response = $this->formatPaymentWithBoletoResponse($arrayPagseguroResponse);
        return $response;
    }

    public function formatPaymentWithBoletoResponse(array $arrayPagseguroResponse)
    {
        $response = [
            'paymentLink' => $arrayPagseguroResponse['paymentLink']
        ];

        return $response;
    }

    public function payWithCreditCard(array $options): array
    {
        return [];
    }

    public function makePagseguroRequest(array $paymentData): object
    {
        $link = env('PAGSEGURO_URL') . '/transactions';

        $response = $this->client->request(
            'POST',
            $link,
            [
                'form_params' => $paymentData,
                'query' => [
                    'email' => env('PAGSEGURO_EMAIL'),
                    'token' => env('PAGSEGURO_TOKEN'),
                ]
            ]
        );

        return $response;
    }

    /**
     * Parses default params of a payment request to Pagseguro's format
     */
    public function parseDefaultPaymentParams(array $options): array
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
            'shippingAddressRequired' => $options['shipping']['addressRequired'] ? 'true' : 'false',
            'extraAmount' => number_format($options['extraAmount'], 2, '.', '')
        ];

        if (isset($options['shipping']['street'])) {
            $parsed['shippingAddressStreet'] = $options['shipping']['street'];
        }

        if (isset($options['shipping']['number'])) {
            $parsed['shippingAddressNumber'] = $options['shipping']['number'];
        }
        if (isset($options['shipping']['district'])) {
            $parsed['shippingAddressDistrict'] = $options['shipping']['district'];
        }

        if (isset($options['shipping']['city'])) {
            $parsed['shippingAddressCity'] = $options['shipping']['city'];
        }

        if (isset($options['shipping']['state'])) {
            $parsed['shippingAddressState'] = $options['shipping']['state'];
        }

        if (isset($options['shipping']['country'])) {
            $parsed['shippingAddressCountry'] = $options['shipping']['country'];
        }

        if (isset($options['shipping']['postalCode'])) {
            $parsed['shippingAddressPostalCode'] = $options['shipping']['postalCode'];
        }

        if (isset($options['shipping']['cost'])) {
            $parsed['shippingCost'] = number_format($options['shipping']['cost'], 2, '.', '');
        }

        if (isset($options['shipping']['type'])) {
            $parsed['shippingType'] = $options['shipping']['type'];
        }

        if (isset($options['reference'])) {
            $parsed['reference'] = $options['reference'];
        }

        return $parsed;
    }

    /**
     * Parses items params of a payment request to Pagseguro's format
     */
    public function parseItems(array $list): array
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

    /**
     * Parses specific params of payment with boleto option to Pagseguro's format
     */
    public function parseBoletoPaymentParams(): array
    {
        $parsed = [
            'paymentMethod' => 'boleto',
        ];

        return $parsed;
    }

    /**
     * Parses specific params of payment with credit card to Pagseguro's format
     */
    public function parseCreditCardPaymentParams(array $options): array
    {
        $parsed = [
            'creditCardToken' => $options['creditCard']['token']
        ];

        return $parsed;
    }
}
