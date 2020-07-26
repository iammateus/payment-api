<?php

use App\Helpers\ItemCreatorTrait;
use Faker\Factory as Faker;
use App\Http\Controllers\PaymentService;

class ParseItemsTest extends TestCase
{
	use ItemCreatorTrait;

	private PaymentService $paymentService;

	public function setUp(): void
	{
        parent::setUp();
        $this->paymentService = app(PaymentService::class);
	}
	
    public function testParseItems()
    {
		$item = $this->createItem();

		$items = [ $item ];

		$parsed = $this->paymentService->parseItems($items);
		$this->assertIsArray($parsed);

		$parsedId = $parsed['itemId1'];
		$this->assertNotEmpty($parsedId);
		$this->assertEquals($item['id'], $parsedId);
		
		$parsedDescription = $parsed['itemDescription1'];
		$this->assertNotEmpty($parsedDescription);
		$this->assertEquals($item['description'], $parsedDescription);
		
		$parsedQuantity = $parsed['itemQuantity1'];
		$this->assertNotEmpty($parsedQuantity);
		$this->assertEquals($item['quantity'], $parsedQuantity);
		
		$parsedAmount = $parsed['itemAmount1'];
		$this->assertNotEmpty($parsedAmount);

		$intendedAmount = number_format($item['amount'], 2, '.', '');
		$this->assertEquals($intendedAmount, $parsedAmount);
    }

	public function testParseItemsWithEmptyArrayArgumentExpectingEmptyArray()
    {
		$parsed = $this->paymentService->parseItems([]);
		$this->assertIsArray($parsed);
		$this->assertEmpty($parsed);
    }

	public function testParseItemsWithNotArrayArgumentExpectingError()
    {
		$this->expectException(TypeError::class);
		$invalidParam = 'A invalid param';
		$parsed = $this->paymentService->parseItems($invalidParam);
	}
	
	public function testParseItemsWithoutArgumentExpectingError()
    {
		$this->expectException(TypeError::class);
		$parsed = $this->paymentService->parseItems();
    }
}