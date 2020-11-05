<?php

use Faker\Factory as Faker;
use App\Services\PaymentService;

class ParseOnlineDebitPaymentParamsTest extends TestCase
{
    /**
     * Fake a valid options array to be parsed
     */
    public function fakeOptions()
    {
        $faker = Faker::create('pt_BR');

        return [
            'bank' => [
                'name' => $faker->randomElement(['BANRISUL', 'BANCO_BRASIL', 'BRADESCO', 'ITAU'])
            ]
        ];
    }

    public function testParseOnlineDebitPaymentParams()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseOnlineDebitPaymentParams($options);
        $this->assertIsArray($result);
    }

    public function testParseOnlineDebitPaymentParamsParsingPaymentMethod()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseOnlineDebitPaymentParams($options);
        $this->assertEquals($result['paymentMethod'], 'eft');
    }

    public function testParseOnlineDebitPaymentParamsParsingPaymentBankName()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseOnlineDebitPaymentParams($options);
        $this->assertEquals($result['bankName'], $options['bank']['name']);
    }
}
