<?php

use App\Classes\Item;
use App\Helpers\ItemCreatorTrait;

class ItemTest extends TestCase 
{
	use ItemCreatorTrait;
	
	public function testItemContructorWithAllParams()
	{
		$item = $this->createItem();

		$this->assertInstanceOf(Item::class, $item);
		
		$this->assertNotNull($item->id);
		$this->assertNotNull($item->description);
		$this->assertNotNull($item->quantity);
		$this->assertNotNull($item->amount);
	}
}