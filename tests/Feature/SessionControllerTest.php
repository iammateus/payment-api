<?php

use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class SessionControllerTest extends TestCase
{
    public function testGetSessionExpectingSuccess()
    {
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