<?php

namespace App\Http\Controllers;

use App\Rules\StorePaymentRuleGroup;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function makePagseguroPayment(Request $request): JsonResponse
    {
        $rules = StorePaymentRuleGroup::getRules();

        $this->validate($request, $rules);

        $payment = $this->paymentService->makePagseguroPayment($request->all());

        return response()->json([
            'message' => 'SUCCESS',
            'data' => $payment
        ]);
    }
}
