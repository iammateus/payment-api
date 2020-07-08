<?php

namespace App\Helpers;
use Faker\Factory as Faker;

use App\Classes\Item;

trait ItemCreatorTrait
{
	public function createItem(): Item
	{
		$faker = Faker::create();

		$id = $faker->numberBetween(1);
		$description = $faker->text();
		$quantity = $faker->numberBetween(1);
		$amount = $faker->randomFloat();
		
		$item = new Item($id, $description, $quantity, $amount);

		return $item;
	}
}