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
		$quantity = $faker->numberBetween(1);
		$amount = $faker->randomFloat();
		
		$item = new Item($id, $description, $quantity, $amount);
		
		$this->assertNotNull($item->id);
		$this->assertNotNull($item->description);
		$this->assertNotNull($item->quantity);
		$this->assertNotNull($item->amount);
	}
}