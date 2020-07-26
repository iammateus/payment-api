<?php

namespace App\Helpers;
use Faker\Factory as Faker;

trait ItemCreatorTrait
{
	public function createItem(): array
	{
		$faker = Faker::create();

		$item = [
			'id' => $faker->numberBetween(1),
			'description' => $faker->text(),
			'quantity' => $faker->numberBetween(1),
			'amount' => $faker->randomFloat()
		];

		return $item;
	}
}