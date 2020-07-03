<?php

use Faker\Factory as Faker;
use App\Classes\Item;
use App\Classes\ItemList;

class ItemListTest extends TestCase 
{
	public function testItemContructorWithAllParams()
	{
		$items = new ItemList();
		$this->assertInstanceOf(ItemList::class, $items);
	}
}