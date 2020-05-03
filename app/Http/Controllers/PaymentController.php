<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Exception;
use App\Utils\ResponseParser;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    private Client $client;
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->client = new Client( [ 'base_uri' => env('PAGSEGURO_URL') ] );
        $this->paymentService = $paymentService;
    }

    public function session (): JsonResponse
    {
        $email = env('PAGSEGURO_EMAIL');
        $token = env('PAGSEGURO_TOKEN');
    
        try{
            $response = $this->client->request('POST', 'sessions', [
                'query' => [
                    'email' => $email,
                    'token' => $token
                ]
            ]);
            
            $response = ResponseParser::parse($response);
        
            $token = (string) $response->id;
        
            return response()->json([
                'message' => 'SUCCESS',
                'data' => [
                    'token' => $token
                ]
            ]);

        }catch(Exception $e){
            return response()->json( [ 'error' => 'Error while trying to generate payment session' ], Response::HTTP_INTERNAL_SERVER_ERROR );
            
        }       
    }

    public function pay (Request $request): JsonResponse
    {
        $paymentOptions = $request->all();

        $payment = $this->paymentService->pay($paymentOptions);

        $paymentLink = (string) $payment->paymentLink;

        return response()->json([
            'message' => 'SUCCESS',
            'data' => [
                'paymentLink' => $paymentLink
            ]
        ]);
    }

}
