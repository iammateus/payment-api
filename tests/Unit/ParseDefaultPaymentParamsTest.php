<?php

use App\Http\Controllers\PaymentService;

class ParseDefaultPaymentParamsTest extends TestCase 
{
	private PaymentService $paymentService;

	public function setUp(): void
	{
        parent::setUp();
        $this->paymentService = app(PaymentService::class);
	}

	/**
	 * Fake a valid options array to be parsed
	 */ 
	public function fakeOptions()
	{
		return [
			'mode' => 'default'
		];
	}

	public function testParseDefaultPaymentParamsTest()
	{
		$options = $this->fakeOptions();
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertIsArray($parsed);
	}

	public function testParseDefaultPaymentParamsSendingInvalidArgumentTest()
	{
		$this->expectException(TypeError::class);
		$invalidParam = 'A invalid param';
		$this->paymentService->parseDefaultPaymentParams($invalidParam);
	}

	public function testParseDefaultPaymentParamsParsingPaymentMode()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertIsString($parsed['paymentMode']);
		$this->assertEquals($options['mode'], $parsed['paymentMode']);
	}
}