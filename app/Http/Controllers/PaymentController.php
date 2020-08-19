<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\PaymentService;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct ( PaymentService $paymentService )
    {
        $this->paymentService = $paymentService;
    }

    public function store ( Request $request ): JsonResponse
    {
        $rules = [
            'method' => 'required|in:BOLETO',
            'sender' => 'required',
            'sender.name' => 'required|string|min_words:2',
            'sender.document' => 'required',
            'sender.document.type' => 'required|in:CPF,CNPJ',
            'sender.document.value' => 'required|document:sender.document.type',
            'sender.phone' => 'required',
            'sender.phone.areaCode' => 'required|area_code',
            'sender.phone.number' => 'required|digits_between:8,9',
            'sender.email' => 'required|email',
            'sender.hash' => 'required',
            'shipping' => 'required',
            'shipping.addressRequired' => 'required|boolean',
            'shipping.street' => 'required_if:shipping.addressRequired,1|max:80',
            'shipping.number' => 'required_if:shipping.addressRequired,1|max:20',
            'shipping.district' => 'required_if:shipping.addressRequired,1|max:60',
            'shipping.city' => 'required_if:shipping.addressRequired,1|max:60|min:2',
            'shipping.country' => 'required_if:shipping.addressRequired,1|in:BRA',
            'extraAmount' => 'required|numeric',
            'items' => 'required|array',
            'items.*.id' => 'required|max:36',
            'items.*.description' => 'required|max:100',
            'items.*.quantity' => 'required|integer|min:1|max:100',
            'items.*.amount' => 'required|numeric|max:10000',
        ];

        $this->validate($request, $rules);

        $payment = $this->paymentService->pay($request->all());

        return response()->json([
            'message' => 'SUCCESS',
            'data' => [
                'paymentLink' => $payment['paymentLink']
            ]
        ]);
    } 
}
