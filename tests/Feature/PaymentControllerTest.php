<?php

use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class PaymentControllerTest extends TestCase
{
    public function testGetPaymentSessionTokenExpectingSuccess()
    {
        $this->get('/payment-session');
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'message',
            'data' => [
                'token'
            ]
        ]);
    }
    
}