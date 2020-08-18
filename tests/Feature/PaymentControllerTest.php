<?php

use GuzzleHttp\Client;
use Faker\Factory as Faker;
use GuzzleHttp\HandlerStack;
use Illuminate\Http\Response;
use GuzzleHttp\Psr7\Response as MockResponse;
use GuzzleHttp\Handler\MockHandler;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class PaymentControllerTest extends TestCase
{
    public function mockBoletoResponse() {
        //Setting mock client
        $body = '<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>
                    <transaction>
                        <date>2020-07-26T12:42:56.000-03:00</date>
                        <code>02B62EF5-1824-4FCC-824A-9607348C6D08</code>
                        <type>1</type>
                        <status>1</status>
                        <lastEventDate>2020-07-26T12:42:59.000-03:00</lastEventDate>
                        <paymentMethod>
                            <type>2</type>
                            <code>202</code>
                        </paymentMethod>
                        <paymentLink>https://sandbox.pagseguro.uol.com.br/checkout/payment/booklet/print.jhtml?c=c0b4c231ab35affff4f36b511aa200813a220daec9db33d941d0005fd226f013f86940b74c2b5e55</paymentLink>
                        <grossAmount>114.00</grossAmount>
                        <discountAmount>0.00</discountAmount>
                        <feeAmount>6.09</feeAmount>
                        <netAmount>107.91</netAmount>
                        <extraAmount>10.00</extraAmount>
                        <installmentCount>1</installmentCount>
                        <itemCount>3</itemCount>
                        <items>
                            <item>
                                <id>1</id>
                                <description>Produto 1</description>
                                <quantity>2</quantity>
                                <amount>2.00</amount>
                            </item>
                            <item>
                                <id>2</id>
                                <description>Produto 2</description>
                                <quantity>1</quantity>
                                <amount>60.00</amount>
                            </item>
                            <item>
                                <id>3</id>
                                <description>Produto 3</description>
                                <quantity>2</quantity>
                                <amount>20.00</amount>
                            </item>
                        </items>
                        <sender>
                            <name>Mateus Soares</name>
                            <email>chagaswc89@sandbox.pagseguro.com.br</email>
                            <phone>
                                <areaCode>48</areaCode>
                                <number>991510980</number>
                            </phone>
                            <documents>
                                <document>
                                    <type>CPF</type>
                                    <value>71783955082</value>
                                </document>
                            </documents>
                        </sender>
                    </transaction>';

        $headers = [
            'Content-Type' => 'application/xml'
        ];

        $mock = new MockHandler( [ new MockResponse( 200, $headers, $body ) ] );
        $handlerStack = HandlerStack::create( $mock );

        $client = new Client( [ 'handler' => $handlerStack ] );
        $this->app->instance('GuzzleHttp\Client', $client);
    }

    public function testPayWithBoletoExpectingSuccess()
    {   
        $this->mockBoletoResponse();
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
                    'description' => $faker->text(100),
                    'quantity' => $faker->numberBetween(1, 100),
                    'amount' => $faker->randomFloat(2,1, 10000)
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseOk();
        $this->seeJsonStructure([
            'message',
            'data' => [
                'paymentLink'
            ]
        ]);
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
        $faker = Faker::create('pt_BR');

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
        $faker = Faker::create('pt_BR');

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
        $faker = Faker::create('pt_BR');

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
        $faker = Faker::create('pt_BR');

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
                    'id' => $faker->text(1000) // Creates a string with a length bigger than 36
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
                    'description' => $faker->text(1000) // Creates a string with a length bigger than 100
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items.0.description' => [ 'The items.0.description may not be greater than 100 characters.' ]
        ]);
    }
    
    public function testSendingTooBigItemQuantityExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'items' => [
                [
                    'quantity' => $faker->numberBetween(101) // Creates a string with a length bigger than 100
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
                    'amount' => $faker->numberBetween(10001) // Creates a string with a length bigger than 10000
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items.0.amount' => [ 'The items.0.amount may not be greater than 10000.' ]
        ]);
    }

    public function testPaySedingAddressRequiredAsTrueButNotSendingStreetExpectingUnprocessableEntity()
    {
        $data = [
            'shipping' => [
                'addressRequired' => true
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.street' => [ 'The shipping.street field is required when shipping.address required is true.' ]
        ]);
    }
    
    public function testPaySedingTooBigStreetExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'shipping' => [
                'street' => $faker->text(1000)
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.street' => [ 'The shipping.street may not be greater than 80 characters.' ]
        ]);
    }
}