<?php 

namespace App\Classes;

class Item
{
	public string $id;
	public string $description;
	public int $quantity;
	public float $amount;

	public function __construct( string $id , string $description, int $quantity, float $amount ) {
		$this->id = $id;
		$this->description = $description;
		$this->quantity = $quantity;
		$this->amount = $amount;
	}
}