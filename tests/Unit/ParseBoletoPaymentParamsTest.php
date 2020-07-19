<?php

use Faker\Factory as Faker;
use App\Http\Controllers\PaymentService;

class ParseBoletoPaymentParamsTest extends TestCase 
{
	private PaymentService $paymentService;

	public function setUp(): void
	{
        parent::setUp();
        $this->paymentService = app(PaymentService::class);
	}

	public function testParseBoletosPaymentParamsTest()
	{
		$parsed = $this->paymentService->parseBoletoPaymentParams();
		$this->assertIsArray($parsed);
	}
	
	public function testParseBoletosPaymentParamsTestPaymentMethod()
	{
		$parsed = $this->paymentService->parseBoletoPaymentParams();
		$this->assertNotNull($parsed['paymentMethod']);
		$this->assertEquals('boleto', $parsed['paymentMethod']);
	}
}