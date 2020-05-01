<?php

namespace App\Http\Controllers;

use Exception;
use App\Utils\RequestParser;
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
            
            $response = RequestParser::parse($response);
        
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
            'senderName' => 'Jose Comprador',
            'senderCPF' => '72962940005',
            'senderAreaCode' => '11',
            'senderPhone' => '56273440',
            'senderEmail' => 'c83751767534161822146@sandbox.pagseguro.com.br',
            'senderHash' => $request->all()['sender']['hash'],
            'shippingAddressRequired' => 'true',
            'shippingAddressStreet' => 'Av. Brig. Faria Lima',
            'shippingAddressNumber' => '1384',
            'shippingAddressComplement' => '5o andar',
            'shippingAddressDistrict' => 'Jardim Paulistano',
            'shippingAddressPostalCode' => '01452002',
            'shippingAddressCity' => 'Sao Paulo',
            'shippingAddressState' => 'SP',
            'shippingAddressCountry' => 'BRA',
            'shippingType' => '1',
            'shippingCost' => '1.00',
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

        return response()->json($request->all());

    }


    /*
    This code will be removed soon

    public function pay (Request $request): JsonResponse
    {
        $email = env('PAGSEGURO_EMAIL');
        $token = env('PAGSEGURO_TOKEN');
        $url =  env('PAGSEGURO_URL') . 'transactions';

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
            'senderName' => 'Jose Comprador',
            'senderCPF' => '72962940005',
            'senderAreaCode' => '11',
            'senderPhone' => '56273440',
            'senderEmail' => 'c83751767534161822146@sandbox.pagseguro.com.br',
            'senderHash' => $request->all()['sender']['hash'],
            'shippingAddressRequired' => 'true',
            'shippingAddressStreet' => 'Av. Brig. Faria Lima',
            'shippingAddressNumber' => '1384',
            'shippingAddressComplement' => '5o andar',
            'shippingAddressDistrict' => 'Jardim Paulistano',
            'shippingAddressPostalCode' => '01452002',
            'shippingAddressCity' => 'Sao Paulo',
            'shippingAddressState' => 'SP',
            'shippingAddressCountry' => 'BRA',
            'shippingType' => '1',
            'shippingCost' => '1.00',
            'email' => $email,
            'token' => $token,
        ];

        $params =  http_build_query($params);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        $response = curl_exec($curl);
        curl_close($curl);

        return response()->json($request->all());
    }
    */
}
