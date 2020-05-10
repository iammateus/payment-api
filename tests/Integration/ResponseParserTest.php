<?php

use App\Utils\ResponseParser;
use Faker\Factory as Faker;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;

class ResponseParserTest extends TestCase
{
    public function testParseXml ()
    {
        $faker = Faker::create();

        //Faking xml response body
        $text = $faker->text();
        $body = '<?xml version="1.0" encoding="UTF-8"?>
                    <data>
                        <text>'. $text .'</text>
                    </data>';

        $headers = [
            'Content-Type' => 'application/xml'
        ];

        $mock = new MockHandler( [ new Response( 200, $headers, $body ) ] );
        $handlerStack = HandlerStack::create( $mock );

        $client = new Client( [ 'handler' => $handlerStack ] );
        $response = $client->request('GET', '/');

        $response = ResponseParser::parseXml( $response );
        $this->assertIsObject( $response );
        
        $responseText = (string) $response->text;
        $this->assertEquals( $text, $responseText );
    }
}