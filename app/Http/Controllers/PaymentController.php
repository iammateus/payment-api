<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\SessionService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /* 
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    } 
    */

    public function store ( Request $request ): JsonResponse
    {
        $this->validate($request, [
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
            'shipping' => 'required',
            'shipping.addressRequired' => 'required|boolean',
            'extraAmount' => 'required|numeric',
            'items' => 'required|array',
            'items.*.id' => 'required',
            'items.*.description' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.amount' => 'required|numeric'
        ]);

        return response()->json();
    } 
}
