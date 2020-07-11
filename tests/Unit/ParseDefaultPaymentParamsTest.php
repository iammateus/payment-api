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
	
	public function testParseDefaultPaymentParamsTest()
	{
		$this->paymentService->parseDefaultPaymentParams();
		//TODO: create this test
	}
}