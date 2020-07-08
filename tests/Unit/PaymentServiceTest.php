<?php

use App\Classes\ItemList;
use App\Helpers\ItemCreatorTrait;
use Faker\Factory as Faker;
use App\Http\Controllers\PaymentService;

class PaymentServiceTest extends TestCase
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