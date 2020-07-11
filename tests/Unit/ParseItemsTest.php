<?php

use App\Classes\ItemList;
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

		$list = [ $item ];

		$items = new ItemList( $list );

		$parsed = $this->paymentService->parseItems($items);
		$this->assertIsArray($parsed);

		$parsedId = $parsed['itemId1'];
		$this->assertNotEmpty($parsedId);
		$this->assertEquals($item->id, $parsedId);
		
		$parsedDescription = $parsed['itemDescription1'];
		$this->assertNotEmpty($parsedDescription);
		$this->assertEquals($item->description, $parsedDescription);
		
		$parsedQuantity = $parsed['itemQuantity1'];
		$this->assertNotEmpty($parsedQuantity);
		$this->assertEquals($item->quantity, $parsedQuantity);
		
		$parsedAmount = $parsed['itemAmount1'];
		$this->assertNotEmpty($parsedAmount);
		$this->assertEquals($item->amount, $parsedAmount);
    }

	public function testParseItemsWithEmptyArrayArgumentExpectingEmptyArray()
    {
		$items = new ItemList();
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