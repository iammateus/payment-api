<?php 

namespace App\Classes;

class Item
{
	public string $id;
	public string $description;

	public function __construct(string $id, string $description) {
		$this->id = $id;
		$this->description = $description;
	}
}