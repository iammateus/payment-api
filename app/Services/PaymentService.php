<?php

namespace App\Http\Controllers;

use App\Classes\ItemList;

class PaymentService extends Controller
{
    /**
     * Parsing default params of a payment request to Pagseguro
     *
     * @param array $options Internal service formatted data
     * @return array Pagseguro formatted data
     */
    public function parseDefaultPaymentParams (array $options) {
        
        $parsed = [
            'paymentMode' => $options['mode'],
            'currency' => $options['currency'],
            'notificationURL' => $options['notificationURL'],
            'senderName' => $options['sender']['name'],
            'senderCPF' => $options['sender']['document']['value'],
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
            $parsed['itemAmount' . $parsedKey] = $item->amount;
        }

        return $parsed;
    }
}
