<?php

use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class HealthcheckTest extends TestCase
{
    public function testHealthcheckExpectingSuccess()
    {
        $this->get('/healthcheck');

        $this->assertResponseStatus(Response::HTTP_OK);

        $this->seeJsonEquals([
            'The server is running (Canvas)'
        ]);
    }
}
