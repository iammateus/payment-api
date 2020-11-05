<?php

use Faker\Factory as Faker;
use App\Services\PaymentService;

class ParseOnlineDebitPaymentParamsTest extends TestCase
{
    public function testParseOnlineDebitPaymentParams()
    {
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseOnlineDebitPaymentParams([]);
        $this->assertIsArray($result);
    }

    public function testParseOnlineDebitPaymentParamsParsingPaymentMethod()
    {
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseOnlineDebitPaymentParams([]);
        $this->assertEquals($result['paymentMethod'], 'eft');
    }
}
