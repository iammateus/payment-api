<?php

use Faker\Factory as Faker;
use App\Classes\Item;

class ItemTest extends TestCase 
{
	public function testItemContructorWithAllParams()
	{
		$faker = Faker::create();

		$id = $faker->numberBetween(1);
		$description = $faker->text();
		
		$item = new Item($id, $description);
		$this->assertNotNull($item->id);
		
		$this->assertNotNull($item->description);
	}
}