<?php

use Faker\Factory as Faker;
use App\Services\PaymentService;

class FormatPaymentWithBoletoResponseTest extends TestCase
{
    public function testFormatPaymentWithBoletoResponseExpectingAFormattedArray()
    {
        $faker = Faker::create();
        $mock = Mockery::mock(PaymentService::class)->makePartial();

        $data = [
            'paymentLink' => $faker->url
        ];

        $result = $mock->formatPaymentWithBoletoResponse($data);

        $expectedResult = [
            'paymentLink' => $data['paymentLink']
        ];

        $this->assertIsArray($result);
        $this->assertEquals($result, $expectedResult);
    }

    public function testFormatPaymentWithBoletoResponseWithoutSendingParamExpectingTypeError()
    {
        $this->expectException(TypeError::class);
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $mock->formatPaymentWithBoletoResponse();
    }

    public function testFormatPaymentWithBoletoResponseWithoutSendingValidParamExpectingTypeError()
    {
        $this->expectException(TypeError::class);
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $mock->formatPaymentWithBoletoResponse('a invalid param');
    }
}
