<?php

use Faker\Factory as Faker;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class PaymentControllerTest extends TestCase
{
    public function testPayWithBoletoExpectingSuccess()
    {
        $this->post('/payment');
        $this->assertResponseOk();
    }
}