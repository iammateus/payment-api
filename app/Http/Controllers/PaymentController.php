<?php

namespace App\Http\Controllers;

use Exception;
use App\Utils\ResponseParser;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client( [ 'base_uri' => env('PAGSEGURO_URL') ] );
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
            return response()->json( [ 'error' => $e->getMessage() ], Response::HTTP_INTERNAL_SERVER_ERROR );
            
        }       
    }

    public function pay (Request $request): JsonResponse
    {
        $email = env('PAGSEGURO_EMAIL');
        $token = env('PAGSEGURO_TOKEN');
        $requestAll = $request->all();

        $params = [
            'paymentMode' => 'default',
            'paymentMethod' => 'boleto',
            'currency' => 'BRL',
            'extraAmount' => '0.00',
            'itemId1' => '0001',
            'itemDescription1' => 'Notebook Prata',
            'itemAmount1' => '24300.00',
            'itemQuantity1' => 1,
            'notificationURL' => 'https://sualoja.com.br/notifica.html',
            'reference' => 'REF1234',
            'senderName' => $requestAll['sender']['name'],
            'senderCPF' => $requestAll['sender']['document']['value'],
            'senderAreaCode' => $requestAll['sender']['phone']['areaCode'],
            'senderPhone' => $requestAll['sender']['phone']['number'],
            'senderEmail' => 'test@sandbox.pagseguro.com.br',
            'senderHash' => $requestAll['sender']['hash'],
            'shippingAddressRequired' => 'false',
            'email' => $email,
            'token' => $token,
        ];

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

        $paymentLink = (string) $response->paymentLink;

        return response()->json([
            'message' => 'SUCCESS',
            'data' => [
                'paymentLink' => $paymentLink
            ]
        ]);

    }

}
