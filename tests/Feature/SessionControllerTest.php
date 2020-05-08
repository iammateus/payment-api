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
    }
}