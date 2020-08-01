<?php

use Faker\Factory as Faker;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class PaymentControllerTest extends TestCase
{
    public function testPayWithBoletoExpectingSuccess()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'method' => $faker->randomElement( ['BOLETO'] ),
            'sender' => [
                'name' => $faker->name(),
                'document' => [
                    'type' =>  $faker->randomElement( ['CPF'] ),
                    'value' => $faker->cpf(false)
                ],
                'phone' => [
                    'areaCode' => $faker->areaCode(),
                    'number' => $faker->numberBetween(10000000, 999999999)
                ],
                'email' => $faker->email,
                'hash' => $faker->word()
            ],
            'shipping' => [
                //@TODO: Improve validation in a way that some fields are required depending on this field
                'addressRequired' => false
            ],
            'extraAmount' => 0,
            'items' => [
                [
                    'id' => $faker->text(36),
                    'description' => $faker->text(110),
                    'quantity' => $faker->numberBetween(1, 100),
                    'amount' => $faker->randomFloat(1, 10000)
                ]
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

    public function testPayWithoutSedingSenderDocumentExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.document' => [ 'The sender.document field is required.' ]
        ]);
    }

    public function testPayWithoutSedingSenderDocumentTypeExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.document.type' => [ 'The sender.document.type field is required.' ]
        ]);
    }
    
    public function testPaySedingSenderInvalidDocumentTypeExpectingUnprocessableEntity()
    {
        $faker = Faker::create();

        $data = [
            'sender' => [
                'document' => [
                    'type' => $faker->numberBetween()
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.document.type' => [ 'The selected sender.document.type is invalid.' ]
        ]);
    }

    public function testPayWithoutSedingSenderDocumentValueExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.document.value' => [ 'The sender.document.value field is required.' ]
        ]);
    }
    
    public function testPaySedingInvalidSenderDocumentCPFValueExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'sender' => [
                'document' => [
                    'type' => 'CPF',
                    'value' => $faker->cnpj(),
                ]
            ]
        ];
        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.document.value' => [ 'The sender.document.value is not a valid document.' ]
        ]);
    }
    
    public function testPaySedingInvalidSenderDocumentCNPJValueExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'sender' => [
                'document' => [
                    'type' => 'CNPJ',
                    'value' => $faker->cpf(false),
                ]
            ]
        ];
        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.document.value' => [ 'The sender.document.value is not a valid document.' ]
        ]);
    }
    
    public function testPaySedingValidSenderDocumentCNPJ()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'sender' => [
                'document' => [
                    'type' => 'CNPJ',
                    'value' => $faker->cnpj(false),
                ]
            ]
        ];
        $this->post('/payment', $data);
        $this->dontSeeJson([
            'sender.document.value' => [ 'The sender.document.value is not a valid document.' ]
        ]);
    }
    
    public function testPaySedingValidSenderDocumentCPF()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'sender' => [
                'document' => [
                    'type' => 'CPF',
                    'value' => $faker->cpf(false),
                ]
            ]
        ];
        $this->post('/payment', $data);
        $this->dontSeeJson([
            'sender.document.value' => [ 'The sender.document.value is not a valid document.' ]
        ]);
    }

    public function testPayWithoutSedingSenderPhoneExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.phone' => [ 'The sender.phone field is required.' ]
        ]);
    }

    public function testPayWithoutSedingSenderPhoneAreaCodeExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.phone.areaCode' => [ 'The sender.phone.area code field is required.' ]
        ]);
    }
    
    public function testPaySedingInvalidSenderPhoneAreaCodeExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'sender' => [
                'phone' => [
                    'areaCode' => $faker->numberBetween(100)
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.phone.areaCode' => [ 'The sender.phone.area code must be a valid Brazil area code.' ]
        ]);
    }

    public function testPayWithoutSedingSenderPhoneNumberExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.phone.number' => [ 'The sender.phone.number field is required.' ]
        ]);
    }
    
    public function testPaySedingInvalidSenderPhoneNumberExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'sender' => [
                'phone' => [
                    'number' => $faker->numberBetween(10000000000)
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.phone.number' => [ 'The sender.phone.number must be between 8 and 9 digits.' ]
        ]);
    }
    
    public function testPayWithoutSedingSenderEmailExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.email' => [ 'The sender.email field is required.' ]
        ]);
    }
    
    public function testPaySedingInvalidSenderEmailExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'sender' => [
                'email' => $faker->text()
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.email' => [ 'The sender.email must be a valid email address.' ]
        ]);
    }
    
    public function testPayWithotSedingShippingDataExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping' => [ 'The shipping field is required.' ]
        ]);
    }
    
    public function testPayWithotSedingShippingAddressRequiredExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.addressRequired' => [ 'The shipping.address required field is required.' ]
        ]);
    }
    
    public function testPaySedingInvalidShippingAddressRequiredExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'shipping' => [
                'addressRequired' => $faker->text()
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.addressRequired' => [ 'The shipping.address required field must be true or false.' ]
        ]);
    }
    
    public function testPayWithoutSedingExtraAmountExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'extraAmount' => [ 'The extra amount field is required.' ]
        ]);
    }
    
    public function testPaySedingInvalidExtraAmountExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');
        
        $data = [
            'extraAmount' => $faker->text()
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'extraAmount' => [ 'The extra amount must be a number.' ]
        ]);
    }

    public function testPayWithoutSendingItemsExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items' => [ 'The items field is required.' ]
        ]);
    }
    
    public function testPaySendingInvalidItemsExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'items' => $faker->text()
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items' => [ 'The items must be an array.' ]
        ]);
    }
    
    public function testPayWithoutSendingItemIdExpectingUnprocessableEntity()
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
            'items.0.id' => [ 'The items.0.id field is required.' ]
        ]);
    }
    
    public function testPayWithoutSendingItemDescriptionExpectingUnprocessableEntity()
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
            'items.0.description' => [ 'The items.0.description field is required.' ]
        ]);
    }
    
    public function testPayWithoutSendingItemQuantityExpectingUnprocessableEntity()
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
            'items.0.quantity' => [ 'The items.0.quantity field is required.' ]
        ]);
    }
    
    public function testPaySendingInvalidFormatItemQuantityExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'items' => [
                [
                    'quantity' => $faker->text()
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = json_decode($this->response->getContent(), true);
        $this->assertContains('The items.0.quantity must be an integer.', $response['items.0.quantity']);
    }

    public function testPaySendingItemQuantityZeroExpectingUnprocessableEntity()
    {
        $data = [
            'items' => [
                [
                    'quantity' => 0
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items.0.quantity' => [ 'The items.0.quantity must be at least 1.' ]
        ]);
    }
    
    public function testPayWithoutSendingItemAmountExpectingUnprocessableEntity()
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
            'items.0.amount' => [ 'The items.0.amount field is required.' ]
        ]);
    }
   
    public function testPaySendingInvalidItemAmountExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'items' => [
                [
                    'amount' => $faker->text()
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items.0.amount' => [ 'The items.0.amount must be a number.' ]
        ]);
    }

    public function testPayWithoutSendSenderHashExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.hash' => [ 'The sender.hash field is required.' ]
        ]);
    }

    public function testSendingTooBigItemIdExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'items' => [
                [
                    'id' => $faker->text() // Creates a string with a length bigger than 36
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items.0.id' => [ 'The items.0.id may not be greater than 36 characters.' ]
        ]);
    }
    
    public function testSendingTooBigItemDescriptionExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'items' => [
                [
                    'description' => $faker->text() // Creates a string with a length bigger than 110
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items.0.description' => [ 'The items.0.description may not be greater than 110 characters.' ]
        ]);
    }
    
    public function testSendingTooBigItemQuantityExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'items' => [
                [
                    'quantity' => $faker->numberBetween(101) // Creates a string with a length bigger than 110
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items.0.quantity' => [ 'The items.0.quantity may not be greater than 100.' ]
        ]);
    }
    
    public function testSendingTooBigAmountQuantityExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'items' => [
                [
                    'amount' => $faker->numberBetween(10001) // Creates a string with a length bigger than 110
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items.0.amount' => [ 'The items.0.amount may not be greater than 10000.' ]
        ]);
    }
}