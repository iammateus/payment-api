<?php

use Faker\Factory as Faker;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class PaymentControllerTest extends TestCase
{
    public function testPayWithBoletoExpectingSuccess()
    {
        $faker = Faker::create();

        $data = [
            'method' => $faker->randomElement( ['BOLETO'] )
        ];

        $this->post('/payment', $data);
        $this->assertResponseOk();
    }

    public function testPayWithoutSendingPaymentMethodExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'method' => [ 'The method field is required.' ]
        ]);
    }
    
    public function testPaySendingInvalidPaymentMethodExpectingUnprocessableEntity()
    {
        $faker = Faker::create();

        $data = [
            'method' => $faker->name()
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'method' => [ 'The selected method is invalid.' ]
        ]);
    }
}