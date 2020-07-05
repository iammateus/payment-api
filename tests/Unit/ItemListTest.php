<?php

use Faker\Factory as Faker;
use App\Classes\Item;
use App\Classes\ItemList;

class ItemListTest extends TestCase 
{
	public function testItemContructor()
	{
		$items = new ItemList();
		$this->assertInstanceOf(ItemList::class, $items);
	}
	
	public function testItemContructorWithAllParams()
	{
		$faker = Faker::create();

		$id = $faker->numberBetween(1);
		$description = $faker->text();
		$quantity = $faker->numberBetween(1);
		$amount = $faker->randomFloat();

		$itemParam = new Item( $id, $description, $quantity, $amount );
		$param = [ $itemParam ];

		$items = new ItemList( $param );
		$this->assertInstanceOf(ItemList::class, $items);

		$content = $items->getContent();
		$this->assertIsArray($content);

		$item = $content[0];
		$this->assertInstanceOf(Item::class, $item);
	}
}