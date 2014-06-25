<?php

class DatabaseQueryBuilderTest extends PHPUnit_Framework_TestCase
{
	public function testSimpleSelect()
	{
		$query = $this->getQuery();

		$sql = $query->select('*')->from('table')->toSql();

		$this->assertEquals('SELECT * FROM `table`', $sql);
	}

	public function testColumnSelect()
	{
		$query = $this->getQuery();

		$sql = $query->select('id', 'name')->from('table')->toSql();

		$this->assertEquals('SELECT `id`, `name` FROM `table`', $sql);
	}

	public function getQuery()
	{
		return new DB;
	}
}