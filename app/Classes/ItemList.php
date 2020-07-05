<?php 

namespace App\Classes;

class ItemList
{
	private array $content = [];

	public function __construct(array $items = []) {
		$this->content = $items;
	}

	public function getContent(): array
	{
		return $this->content;
	}
}