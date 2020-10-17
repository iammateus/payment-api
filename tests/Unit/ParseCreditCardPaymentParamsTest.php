<?php

use Faker\Factory as Faker;
use App\Services\PaymentService;

class ParseCreditCardPaymentParamsTest extends TestCase
{
    public function testParseCreditCardPaymentParamsTest()
    {
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams();
        $this->assertIsArray($result);
    }
}
