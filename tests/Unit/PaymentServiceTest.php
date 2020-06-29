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
		$items = [];
		$parsed = $this->paymentService->parseItems($items);
		$this->assertIsArray($parsed);
    }

	public function testParseItemsWithEmptyArrayArgumentExpectingEmptyArray()
    {
		$items = [];
		$parsed = $this->paymentService->parseItems($items);
		$this->assertIsArray($parsed);
		$this->assertEmpty($parsed);
    }

	public function testParseItemsWithNotArrayArgumentExpectingError()
    {
		$this->expectException(TypeError::class);
		$invalidParams = 'A invalid params';
		$parsed = $this->paymentService->parseItems($invalidParams);
	}
	
	public function testParseItemsWithoutArgumentExpectingError()
    {
		$this->expectException(TypeError::class);
		$parsed = $this->paymentService->parseItems();
    }
}