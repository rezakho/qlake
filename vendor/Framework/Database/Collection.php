<?php

namespace Framework\Database;

use PDOStatement;
use ArrayAccess;
use Countable;
use IteratorAggregate;

class Collection implements /*ArrayableInterface,*/ ArrayAccess, Countable, IteratorAggregate/*, JsonableInterface*/
{
	protected $statement;

	protected $items = [];

	protected $columns = [];


	public function __construct(array $items = [], array $columns = [])
	{
		$this->items = $items;

		$this->columns = $columns;$this->f = function(){};
	}



	public function offsetSet($offset, $value)
	{
		if (is_null($offset))
		{
			$this->items[] = $value;
		}
		else
		{
			$this->items[$offset] = $value;
		}
	}

	public function offsetExists($offset)
	{
		return isset($this->items[$offset]);
	}

	public function offsetUnset($offset)
	{
		unset($this->items[$offset]);
	}
	
	public function offsetGet($offset)
	{
		return $this->items[$offset] ?: null;
	}

	public function count()
	{
		return count($this->items);
	}

	public function getIterator()
	{
		return new ArrayIterator($this->items);
	}
}