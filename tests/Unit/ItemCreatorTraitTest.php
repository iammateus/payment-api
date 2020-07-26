<?php

use App\Classes\Item;
use App\Helpers\ItemCreatorTrait;

class ItemCreatorTraitTest extends TestCase
{
    public function testCreateItem()
    {
		$mock = $this->getMockForTrait(ItemCreatorTrait::class);
		$item = $mock->createItem();
		$this->assertIsArray($item);
    }
}