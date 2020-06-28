<?php

use App\Http\Controllers\PaymentService;

class PaymentServiceTest extends TestCase
{
	private PaymentService $paymentService;

	public function setUp(): void
	{
        parent::setUp();
        $this->paymentService = app(PaymentService::class);
	}
	
    public function testParseItems()
    {
		$parsed = $this->paymentService->parseItems();
		$this->assertIsArray($parsed);
    }
}