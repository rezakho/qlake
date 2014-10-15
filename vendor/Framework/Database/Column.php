<?php

namespace Framework\Database;

class Column
{
	public function __construct($column)
	{
		$this->name = $column;
	}

	public function __tostring()
	{
		return $this->name;
	}
}