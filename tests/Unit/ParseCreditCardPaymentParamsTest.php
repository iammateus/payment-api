<?php

use Faker\Factory as Faker;
use App\Services\PaymentService;

class ParseCreditCardPaymentParamsTest extends TestCase
{
    /**
     * Fake a valid options array to be parsed
     */
    public function fakeOptions()
    {
        $faker = Faker::create('pt_BR');

        return [
            'creditCard' => [
                'token' => $faker->text(),
                'holder' => [
                    'name' => $faker->name(),
                    'cpf' => $faker->cpf(false)
                ]
            ]
        ];
    }

    public function testParseCreditCardPaymentParams()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
    }

    public function testParseCreditCardPaymentParamsParsingCreditCardToken()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['creditCardToken'], $options['creditCard']['token']);
    }

    public function testParseCreditCardPaymentParamsParsingCreditCardHolderName()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['creditCardHolderName'], $options['creditCard']['holder']['name']);
    }

    public function testParseCreditCardPaymentParamsParsingCreditCardHolderCpf()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['creditCardHolderCpf'], $options['creditCard']['holder']['cpf']);
    }
}
