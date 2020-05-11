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
}