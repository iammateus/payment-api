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
            'method' => $faker->randomElement( ['BOLETO'] ),
            'sender' => [
                'name' => $faker->name()
            ]
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

    public function testPayWithoutSendingSenderDataExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender' => [ 'The sender field is required.' ]
        ]);
    }
    
    public function testPayWithoutSendingSenderNameExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.name' => [ 'The sender.name field is required.' ]
        ]);
    }
    
    public function testPaySendingNotStringSenderNameExpectingUnprocessableEntity()
    {
        $faker = Faker::create();

        $data = [
            'sender' => [
                'name' => $faker->numberBetween()
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = json_decode($this->response->getContent(), true);
        $this->assertContains('The sender.name must be a string.', $response['sender.name']);
    }
    
    public function testPaySendingIncompleteNameExpectingUnprocessableEntity()
    {
        $faker = Faker::create();

        $data = [
            'sender' => [
                'name' => $faker->word()
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.name' => [ 'The sender.name must have at last 2 words.' ]
        ]);
    }
}