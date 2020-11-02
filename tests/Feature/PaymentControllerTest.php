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
    public function mockBoletoResponse()
    {
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

        $mock = new MockHandler([new MockResponse(Response::HTTP_OK, $headers, $body)]);
        $handlerStack = HandlerStack::create($mock);

        $client = new Client(['handler' => $handlerStack]);
        $this->app->instance('GuzzleHttp\Client', $client);
    }

    public function testPayWithBoletoExpectingSuccess()
    {
        $this->mockBoletoResponse();
        $faker = Faker::create('pt_BR');

        $data = [
            'method' => 'BOLETO',
            'sender' => [
                'name' => $faker->name(),
                'document' => [
                    'type' =>  'CNPJ',
                    'value' => $faker->cnpj(false)
                ],
                'phone' => [
                    'areaCode' => $faker->areaCode(),
                    'number' => $faker->numberBetween(10000000, 999999999)
                ],
                'email' => $faker->email,
                'hash' => $faker->word()
            ],
            'shipping' => [
                'addressRequired' => true,
                'street' => $faker->streetName,
                'number' => $faker->text(20),
                'district' => $faker->text(60),
                'city' => $faker->city,
                'state' => $faker->stateAbbr,
                'country' => $faker->randomElement(['BRA']),
                'postalCode' => $faker->numberBetween(10000000, 99999999),
                'cost' => $faker->randomFloat(),
                'type' => $faker->randomElement([1, 2, 3]),
            ],
            'reference' => $faker->text(200),
            'extraAmount' => 0,
            'items' => [
                [
                    'id' => $faker->text(36),
                    'description' => $faker->text(100),
                    'quantity' => $faker->numberBetween(1, 100),
                    'amount' => $faker->randomFloat(2, 1, 10000)
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

    public function testPayWithCreditCardExpectingSuccess()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'method' => 'CREDIT_CARD',
            'sender' => [
                'name' => $faker->name(),
                'document' => [
                    'type' =>  'CPF',
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
                'addressRequired' => true,
                'street' => $faker->streetName,
                'number' => $faker->text(20),
                'district' => $faker->text(60),
                'city' => $faker->city,
                'state' => $faker->stateAbbr,
                'country' => $faker->randomElement(['BRA']),
                'postalCode' => $faker->numberBetween(10000000, 99999999),
                'cost' => $faker->randomFloat(),
                'type' => $faker->randomElement([1, 2, 3]),
            ],
            'reference' => $faker->text(200),
            'extraAmount' => 0,
            'items' => [
                [
                    'id' => $faker->text(36),
                    'description' => $faker->text(100),
                    'quantity' => $faker->numberBetween(1, 100),
                    'amount' => $faker->randomFloat(2, 1, 10000)
                ]
            ],
            'creditCard' => [
                'holder' => [
                    'name' => $faker->text(50),
                    'birthDate' => $faker->date('d/m/Y'),
                    'document' => [
                        'type' => 'CPF',
                        'value' => $faker->cpf(false),
                    ],
                    'phone' => [
                        'areaCode' => $faker->areaCode(),
                        'number' => $faker->numberBetween(1000000, 999999999)
                    ]
                ],
                'token' => $faker->text(),
                'installment' => [
                    'quantity' => $faker->numberBetween(1, 18),
                    'value' => $faker->randomFloat(),
                ],
                'maxInstallmentNoInterest' => $faker->numberBetween(1, 18)
            ],
            'billing' => [
                'street' => $faker->text(80),
                'number' => $faker->word(),
                'district' => $faker->text(60),
                'city' => $faker->city,
                'state' => $faker->stateAbbr,
                'postalCode' => $faker->numberBetween(10000000, 99999999),
                'country' => $faker->randomElement(['BRA']),
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
            'method' => ['The method field is required.']
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
            'method' => ['The selected method is invalid.']
        ]);
    }

    public function testPayWithoutSendingSenderDataExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender' => ['The sender field is required.']
        ]);
    }

    public function testPayWithoutSendingSenderNameExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.name' => ['The sender.name field is required.']
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
            'sender.name' => ['The sender.name must have at last 2 words.']
        ]);
    }

    public function testPayWithoutSendingSenderDocumentExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.document' => ['The sender.document field is required.']
        ]);
    }

    public function testPayWithoutSendingSenderDocumentTypeExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.document.type' => ['The sender.document.type field is required.']
        ]);
    }

    public function testPaySendingSenderInvalidDocumentTypeExpectingUnprocessableEntity()
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
            'sender.document.type' => ['The selected sender.document.type is invalid.']
        ]);
    }

    public function testPayWithoutSendingSenderDocumentValueExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.document.value' => ['The sender.document.value field is required.']
        ]);
    }

    public function testPaySendingInvalidSenderDocumentCPFValueExpectingUnprocessableEntity()
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
            'sender.document.value' => ['The sender.document.value is not a valid document.']
        ]);
    }

    public function testPaySendingInvalidSenderDocumentCNPJValueExpectingUnprocessableEntity()
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
            'sender.document.value' => ['The sender.document.value is not a valid document.']
        ]);
    }

    public function testPaySendingValidSenderDocumentCNPJ()
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
            'sender.document.value' => ['The sender.document.value is not a valid document.']
        ]);
    }

    public function testPaySendingValidSenderDocumentCPF()
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
            'sender.document.value' => ['The sender.document.value is not a valid document.']
        ]);
    }

    public function testPayWithoutSendingSenderPhoneExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.phone' => ['The sender.phone field is required.']
        ]);
    }

    public function testPayWithoutSendingSenderPhoneAreaCodeExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.phone.areaCode' => ['The sender.phone.area code field is required.']
        ]);
    }

    public function testPaySendingInvalidSenderPhoneAreaCodeExpectingUnprocessableEntity()
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
            'sender.phone.areaCode' => ['The sender.phone.area code must be a valid Brazil area code.']
        ]);
    }

    public function testPayWithoutSendingSenderPhoneNumberExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.phone.number' => ['The sender.phone.number field is required.']
        ]);
    }

    public function testPaySendingInvalidSenderPhoneNumberExpectingUnprocessableEntity()
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
            'sender.phone.number' => ['The sender.phone.number must be between 8 and 9 digits.']
        ]);
    }

    public function testPayWithoutSendingSenderEmailExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.email' => ['The sender.email field is required.']
        ]);
    }

    public function testPaySendingInvalidSenderEmailExpectingUnprocessableEntity()
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
            'sender.email' => ['The sender.email must be a valid email address.']
        ]);
    }

    public function testPayWithotSendingShippingDataExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping' => ['The shipping field is required.']
        ]);
    }

    public function testPayWithotSendingShippingAddressRequiredExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.addressRequired' => ['The shipping.address required field is required.']
        ]);
    }

    public function testPaySendingInvalidShippingAddressRequiredExpectingUnprocessableEntity()
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
            'shipping.addressRequired' => ['The shipping.address required field must be true or false.']
        ]);
    }

    public function testPayWithoutSendingExtraAmountExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'extraAmount' => ['The extra amount field is required.']
        ]);
    }

    public function testPaySendingInvalidExtraAmountExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'extraAmount' => $faker->text()
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'extraAmount' => ['The extra amount must be a number.']
        ]);
    }

    public function testPayWithoutSendingItemsExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items' => ['The items field is required.']
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
            'items' => ['The items must be an array.']
        ]);
    }

    public function testPayWithoutSendingItemIdExpectingUnprocessableEntity()
    {
        $data = [
            'items' => [
                []
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items.0.id' => ['The items.0.id field is required.']
        ]);
    }

    public function testPayWithoutSendingItemDescriptionExpectingUnprocessableEntity()
    {
        $data = [
            'items' => [
                []
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items.0.description' => ['The items.0.description field is required.']
        ]);
    }

    public function testPayWithoutSendingItemQuantityExpectingUnprocessableEntity()
    {
        $data = [
            'items' => [
                []
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items.0.quantity' => ['The items.0.quantity field is required.']
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
            'items.0.quantity' => ['The items.0.quantity must be at least 1.']
        ]);
    }

    public function testPayWithoutSendingItemAmountExpectingUnprocessableEntity()
    {
        $data = [
            'items' => [
                []
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'items.0.amount' => ['The items.0.amount field is required.']
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
            'items.0.amount' => ['The items.0.amount must be a number.']
        ]);
    }

    public function testPayWithoutSendSenderHashExpectingUnprocessableEntity()
    {
        $this->post('/payment');
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'sender.hash' => ['The sender.hash field is required.']
        ]);
    }

    public function testPaySendingTooLongItemIdExpectingUnprocessableEntity()
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
            'items.0.id' => ['The items.0.id may not be greater than 36 characters.']
        ]);
    }

    public function testPaySendingTooLongItemDescriptionExpectingUnprocessableEntity()
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
            'items.0.description' => ['The items.0.description may not be greater than 100 characters.']
        ]);
    }

    public function testPaySendingTooBigItemQuantityExpectingUnprocessableEntity()
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
            'items.0.quantity' => ['The items.0.quantity may not be greater than 100.']
        ]);
    }

    public function testPaySendingTooBigAmountQuantityExpectingUnprocessableEntity()
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
            'items.0.amount' => ['The items.0.amount may not be greater than 10000.']
        ]);
    }

    public function testPaySendingAddressRequiredAsTrueButNotSendingStreetExpectingUnprocessableEntity()
    {
        $data = [
            'shipping' => [
                'addressRequired' => true
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.street' => ['The shipping.street field is required when shipping.address required is true.']
        ]);
    }

    public function testPaySendingTooLongStreetExpectingUnprocessableEntity()
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
            'shipping.street' => ['The shipping.street may not be greater than 80 characters.']
        ]);
    }

    public function testPaySendingAddressRequiredAsTrueButNotSendingNumberExpectingUnprocessableEntity()
    {
        $data = [
            'shipping' => [
                'addressRequired' => true
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.number' => ['The shipping.number field is required when shipping.address required is true.']
        ]);
    }

    public function testPaySendingTooLongNumberExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'shipping' => [
                'number' => $faker->text(1000)
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.number' => ['The shipping.number may not be greater than 20 characters.']
        ]);
    }

    public function testPaySendingAddressRequiredAsTrueButNotSendingDistrictExpectingUnprocessableEntity()
    {
        $data = [
            'shipping' => [
                'addressRequired' => true
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.district' => ['The shipping.district field is required when shipping.address required is true.']
        ]);
    }

    public function testPaySendingTooLongDistrictExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'shipping' => [
                'district' => $faker->text(1000)
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.district' => ['The shipping.district may not be greater than 60 characters.']
        ]);
    }

    public function testPaySendingAddressRequiredAsTrueButNotSendingCityExpectingUnprocessableEntity()
    {
        $data = [
            'shipping' => [
                'addressRequired' => true
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.city' => ['The shipping.city field is required when shipping.address required is true.']
        ]);
    }

    public function testPaySendingTooLongCityExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'shipping' => [
                'city' => $faker->text(1000)
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.city' => ['The shipping.city may not be greater than 60 characters.']
        ]);
    }

    public function testPaySendingTooSmallCityExpectingUnprocessableEntity()
    {
        $data = [
            'shipping' => [
                'city' => 'a'
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.city' => ['The shipping.city must be at least 2 characters.']
        ]);
    }

    public function testPaySendingAddressRequiredAsTrueButNotSendingCountryExpectingUnprocessableEntity()
    {
        $data = [
            'shipping' => [
                'addressRequired' => true
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.country' => ['The shipping.country field is required when shipping.address required is true.']
        ]);
    }

    public function testPaySendingInvalidCountryExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'shipping' => [
                'country' => $faker->word()
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.country' => ['The selected shipping.country is invalid.']
        ]);
    }

    public function testPaySendingAddressRequiredAsTrueButNotSendingPostalCodeExpectingUnprocessableEntity()
    {
        $data = [
            'shipping' => [
                'addressRequired' => true
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.postalCode' => ['The shipping.postal code field is required when shipping.address required is true.']
        ]);
    }

    public function testPaySendingInvalidPostalCodeExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'shipping' => [
                'postalCode' =>  $faker->numberBetween(1000000000)
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.postalCode' => ['The shipping.postal code must be 8 digits.']
        ]);
    }

    public function testPaySendingAddressRequiredAsTrueButNotSendingStateExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'shipping' => [
                'addressRequired' => true
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.state' => ['The shipping.state field is required when shipping.address required is true.']
        ]);
    }

    public function testPaySendingTooLongStateExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'shipping' => [
                'state' => $faker->text()
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.state' => ['The shipping.state must be 2 characters.']
        ]);
    }

    public function testPaySendingTooLongComplementExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'shipping' => [
                'complement' => $faker->text(1000)
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.complement' => ['The shipping.complement may not be greater than 40 characters.']
        ]);
    }

    public function testPaySendingNonNumericShippingCostExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'shipping' => [
                'cost' => $faker->text()
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.cost' => ['The shipping.cost must be a number.']
        ]);
    }

    public function testPaySendingInvalidShippingTypeExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'shipping' => [
                'type' => $faker->numberBetween(4)
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'shipping.type' => ['The selected shipping.type is invalid.']
        ]);
    }

    public function testPaySendingTooLongReferenceExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'reference' => $faker->text(1000)
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'reference' => ['The reference may not be greater than 200 characters.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardHolderExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder' => ['The credit card.holder field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardHolderNameExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.name' => ['The credit card.holder.name field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingIncompleteCreditCardHolderNameExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'creditCard' => [
                'holder' => [
                    'name' => $faker->word()
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.name' => ['The credit card.holder.name must have at last 2 words.']
        ]);
    }

    public function testPaySendingTooLongCreditCardHolderNameExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'creditCard' => [
                'holder' => [
                    'name' => $faker->text()
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.name' => ['The credit card.holder.name may not be greater than 50 characters.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardHolderDocumentExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.document' => ['The credit card.holder.document field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardHolderDocumentTypeExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.document.type' => ['The credit card.holder.document.type field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingInvalidCreditCardHolderDocumentTypeExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'creditCard' => [
                'holder' => [
                    'document' => [
                        'type' => $faker->word()
                    ]
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.document.type' => ['The selected credit card.holder.document.type is invalid.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardHolderDocumentValueExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.document.value' => ['The credit card.holder.document.value field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingInvalidCreditCardHolderDocumentValueOfTypeCpfExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'creditCard' => [
                'holder' => [
                    'document' => [
                        'type' => 'CPF',
                        'value' => $faker->cnpj(false),
                    ]
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->seeJson([
            'creditCard.holder.document.value' => ['The credit card.holder.document.value is not a valid document.']
        ]);
    }

    public function testPaySendingInvalidCreditCardHolderDocumentValueOfTypeCnpjExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'creditCard' => [
                'holder' => [
                    'document' => [
                        'type' => 'CNPJ',
                        'value' => $faker->cpf(false),
                    ]
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->seeJson([
            'creditCard.holder.document.value' => ['The credit card.holder.document.value is not a valid document.']
        ]);
    }

    public function testPaySendingValidCreditCardHolderDocumentValueOfTypeCpfExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'creditCard' => [
                'holder' => [
                    'document' => [
                        'type' => 'CPF',
                        'value' => $faker->cpf(false),
                    ]
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->dontSeeJson([
            'creditCard.holder.document.value' => ['The credit card.holder.document.value is not a valid document.']
        ]);
    }

    public function testPaySendingValidCreditCardHolderDocumentValueOfTypeCnpjExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'creditCard' => [
                'holder' => [
                    'document' => [
                        'type' => 'CNPJ',
                        'value' => $faker->cnpj(false),
                    ]
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->dontSeeJson([
            'creditCard.holder.document.value' => ['The credit card.holder.document.value is not a valid document.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardHolderBirthDateExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.birthDate' => ['The credit card.holder.birth date field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingInvalidCreditCardHolderBirthDateExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'method' => 'CREDIT_CARD',
            'creditCard' => [
                'holder' => [
                    'birthDate' => $faker->date()
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.birthDate' => ['The credit card.holder.birth date does not match the format d/m/Y.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardHolderPhoneExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.phone' => ['The credit card.holder.phone field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardHolderPhoneAreaCodeExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.phone.areaCode' => ['The credit card.holder.phone.area code field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingInvalidCreditCardHolderPhoneAreaCodeExpectingUnprocessableEntity()
    {
        $data = [
            'creditCard' => [
                'holder' => [
                    'phone' => [
                        'areaCode' => 'a'
                    ]
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.phone.areaCode' => ['The credit card.holder.phone.area code must be a valid Brazil area code.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardHolderPhoneNumberExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.phone.number' => ['The credit card.holder.phone.number field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingTooShortCreditCardHolderPhoneNumberExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'creditCard' => [
                'holder' => [
                    'phone' => [
                        'number' => $faker->numberBetween(0, 999999)
                    ]
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.phone.number' => ['The credit card.holder.phone.number must be between 7 and 9 digits.']
        ]);
    }

    public function testPaySendingTooLongCreditCardHolderPhoneNumberExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'creditCard' => [
                'holder' => [
                    'phone' => [
                        'number' => $faker->numberBetween(1000000000)
                    ]
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.holder.phone.number' => ['The credit card.holder.phone.number must be between 7 and 9 digits.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard' => ['The credit card field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardTokenExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.token' => ['The credit card.token field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardInstallmentExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.installment' => ['The credit card.installment field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardInstallmentQuantityExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.installment.quantity' => ['The credit card.installment.quantity field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingInvalidCreditCardInstallmentQuantityExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'creditCard' => [
                'installment' => [
                    'quantity' => $faker->word()
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $responseContent = json_decode($this->response->getContent(), true);
        $this->assertContains('The credit card.installment.quantity must be an integer.', $responseContent['creditCard.installment.quantity']);
    }

    public function testPaySendingTooSmallCreditCardInstallmentQuantityExpectingUnprocessableEntity()
    {
        $data = [
            'creditCard' => [
                'installment' => [
                    'quantity' => 0
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->seeJson([
            'creditCard.installment.quantity' => ['The credit card.installment.quantity must be at least 1.']
        ]);
    }

    public function testPaySendingTooBigCreditCardInstallmentQuantityExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'creditCard' => [
                'installment' => [
                    'quantity' => $faker->numberBetween(19)
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->seeJson([
            'creditCard.installment.quantity' => ['The credit card.installment.quantity may not be greater than 18.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardInstallmentValueExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.installment.value' => ['The credit card.installment.value field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingInvalidCreditCardInstallmentValueUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'creditCard' => [
                'installment' => [
                    'value' => $faker->word()
                ]
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->seeJson([
            'creditCard.installment.value' => ['The credit card.installment.value must be a number.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingCreditCardMaxInstallmentNoInterestExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.maxInstallmentNoInterest' => ['The credit card.max installment no interest field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingInvalidCreditCardMaxInstallmentNoInterestExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'creditCard' => [
                'maxInstallmentNoInterest' =>  $faker->word()
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'creditCard.maxInstallmentNoInterest' => ['The credit card.max installment no interest must be an integer.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingBillingAddressExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing' => ['The billing field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingBillingAddressStreetExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD'
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.street' => ['The billing.street field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingTooLongBillingAddressStreetExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'billing' => [
                'street' => $faker->text(1000)
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.street' => ['The billing.street may not be greater than 80 characters.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingBillingAddressNumberExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD',
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.number' => ['The billing.number field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingBillingAddressDistrictExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD',
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.district' => ['The billing.district field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingTooLongBillingAddressDistrictExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'billing' => [
                'district' => $faker->text(1000)
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.district' => ['The billing.district may not be greater than 60 characters.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingBillingAddressCityExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD',
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.city' => ['The billing.city field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingTooShortBillingAddressCityExpectingUnprocessableEntity()
    {
        $data = [
            'billing' => [
                'city' => 'a'
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.city' => ['The billing.city must be at least 2 characters.']
        ]);
    }

    public function testPaySendingTooLongBillingAddressCityExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'billing' => [
                'city' => $faker->text(1000)
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.city' => ['The billing.city may not be greater than 60 characters.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingBillingAddressStateExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD',
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.state' => ['The billing.state field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingInvalidBillingAddressStateExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'billing' => [
                'state' => $faker->text(10)
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.state' => ['The billing.state must be 2 characters.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingBillingAddressPostalCodeExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD',
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.postalCode' => ['The billing.postal code field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingTooShortBillingAddressPostalCodeExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'billing' => [
                'postalCode' => $faker->numberBetween(0, 9999999)
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.postalCode' => ['The billing.postal code must be 8 digits.']
        ]);
    }

    public function testPaySendingTooLongBillingAddressPostalCodeExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'billing' => [
                'postalCode' => $faker->numberBetween(100000000)
            ]
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.postalCode' => ['The billing.postal code must be 8 digits.']
        ]);
    }

    public function testPaySendingTooLongBillingAddressComplementExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'billing' => [
                'complement' => $faker->text(1000)
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.complement' => ['The billing.complement may not be greater than 40 characters.']
        ]);
    }

    public function testPayWithCreditCardWithoutSendingBillingAddressCountryExpectingUnprocessableEntity()
    {
        $data = [
            'method' => 'CREDIT_CARD',
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.country' => ['The billing.country field is required when method is CREDIT_CARD.']
        ]);
    }

    public function testPaySendingInvalidBillingCountryExpectingUnprocessableEntity()
    {
        $faker = Faker::create('pt_BR');

        $data = [
            'billing' => [
                'country' => $faker->text(1000)
            ],
        ];

        $this->post('/payment', $data);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJson([
            'billing.country' => ['The selected billing.country is invalid.']
        ]);
    }
}
