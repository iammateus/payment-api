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

        $documentType = $faker->randomElement( ['CPF', 'CNPJ'] );
        $documentValue = $faker->cpf(false);

        if($documentType === 'CNPJ'){
            $documentValue = $faker->cnpj(false);
        }

        echo PHP_EOL. '***** Testing with ' . $documentType . ' type of document' . PHP_EOL . PHP_EOL;

        $data = [
            'method' => $faker->randomElement( ['BOLETO'] ),
            'sender' => [
                'name' => $faker->name(),
                'document' => [
                    'type' => $documentType,
                    'value' => $documentValue,
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

}