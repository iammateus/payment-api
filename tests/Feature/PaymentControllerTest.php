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
            'method' => 'boleto',
            'sender' => [
                'name' => $faker->name,
                'email' => $faker->name,
                'hash' => $faker->name,
                'document' => [
                    'value' => $faker->name
                ],
                'phone' => [
                    'number' => $faker->name
                ],
            ],
            'items' => [
                [
                    'id' => $faker->name,
                    'description' => $faker->name,
                    'quantity' => $faker->name,
                    'amount' => $faker->name
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseOk();
    }

    public function testPayWithoutSendRequiredDataExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'method' => [ 'The method field is required.' ],
            'sender.name' => [ 'The sender.name field is required.' ],
            'sender.email' => [ 'The sender.email field is required.' ],
            'sender.hash' => [ 'The sender.hash field is required.' ],
            'sender.document.value' => [ 'The sender.document.value field is required.' ],
            'sender.phone.number' => [ 'The sender.phone.number field is required.' ],
            'items' => [ 'The items field is required.' ]
        ]);
    }
    
    public function testPayWithoutSendItemsRequiredDataExpectingUnprocessableEntity()
    {
        $data = [
            'items' => [
                [
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items.0.id' => [ 'The items.0.id field is required.' ],
            'items.0.description' => [ 'The items.0.description field is required.' ],
            'items.0.quantity' => [ 'The items.0.quantity field is required.' ],
            'items.0.amount' => [ 'The items.0.amount field is required.' ]
        ]);
    }
}