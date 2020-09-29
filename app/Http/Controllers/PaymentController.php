<?php

namespace App\Http\Controllers;

use App\Rules\StorePaymentRuleGroup;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct ( PaymentService $paymentService )
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Makes the payment request to Pagseguro
     * TODO: Accept Credit Card and Online Debit options
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store ( Request $request ): JsonResponse
    {
        $rules = StorePaymentRuleGroup::getRules();

        $this->validate($request, $rules);

        $payment = $this->paymentService->store($request->all());

        return response()->json([
            'message' => 'SUCCESS',
            'data' => [
                'paymentLink' => $payment['paymentLink']
            ]
        ]);
    } 
}
