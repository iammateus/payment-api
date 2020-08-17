<?php

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Http\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response as MockResponse;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class SessionControllerTest extends TestCase
{
    public function mockSessionResponse() {
        //Setting mock client
        $body = '<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>
                    <session>
                        <id>ccf72f57023c407a94bc663bc1f4e949</id>
                    </session>';

        $headers = [
            'Content-Type' => 'application/xml'
        ];

        $mock = new MockHandler( [ new MockResponse( 200, $headers, $body ) ] );
        $handlerStack = HandlerStack::create( $mock );

        $client = new Client( [ 'handler' => $handlerStack ] );
        $this->app->instance('GuzzleHttp\Client', $client);
    }

    public function testGetSessionExpectingSuccess()
    {
        $this->mockSessionResponse();
        $this->get('/session');
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'message',
            'data' => [
                'token'
            ]
        ]);

        $decodedResponse = json_decode($this->response->getContent());
        $this->assertNotEmpty($decodedResponse->data->token);
        $this->assertIsString($decodedResponse->data->token);
    }
}