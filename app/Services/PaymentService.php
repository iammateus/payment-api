<?php

namespace App\Http\Controllers;

use App\Classes\ItemList;

class PaymentService extends Controller
{
    public function parseDefaultPaymentParams (array $options) {
        return [];
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
