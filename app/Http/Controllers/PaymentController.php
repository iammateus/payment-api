<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function session (): JsonResponse
    {
        try{
            $response = $this->paymentService->session();
        
            $token = (string) $response->id;
        
            return response()->json([
                'message' => 'SUCCESS',
                'data' => [
                    'token' => $token
                ]
            ]);

        }catch(Exception $e){
            $message = 'Error while trying to generate payment session';
            Log::error($message, [$e]);
            return response()->json( [ 'error' => $message ], Response::HTTP_INTERNAL_SERVER_ERROR );
        }       
    }

    public function pay (Request $request): JsonResponse
    {
        try{
            $paymentOptions = $request->all();

            $payment = $this->paymentService->pay($paymentOptions);

            return response()->json([
                'message' => 'SUCCESS',
                'data' => $payment
            ]);

        }catch(Exception $e){
            $message = 'Error while trying to make payment';
            Log::error($message, [$e]);
            return response()->json( [ 'error' => $message ], Response::HTTP_INTERNAL_SERVER_ERROR );
            
        } 
    }

}
