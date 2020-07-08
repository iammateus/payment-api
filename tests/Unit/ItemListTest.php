<?php

use App\Classes\Item;
use App\Classes\ItemList;
use App\Helpers\ItemCreatorTrait;

class ItemListTest extends TestCase 
{
	use ItemCreatorTrait;

	public function testItemContructor()
	{
		$items = new ItemList();
		$this->assertInstanceOf(ItemList::class, $items);
	}
	
	public function testItemContructorWithAllParams()
	{
		$item = $this->createItem();;
		$list = [ $item ];

		$items = new ItemList( $list );
		$this->assertInstanceOf(ItemList::class, $items);

		$content = $items->getContent();
		$this->assertIsArray($content);

		$item = $content[0];
		$this->assertInstanceOf(Item::class, $item);
	}
}