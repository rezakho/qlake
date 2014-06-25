<?php

class DatabaseQueryBuilderTest extends PHPUnit_Framework_TestCase
{
	public function testSimpleSelect()
	{
		$query = $this->getQuery();

		$sql = $query->select('*')->from('table')->toSql();

		$this->assertEquals('SELECT * FROM `table`', $sql);
	}

	public function testSimpleColumnsSelect()
	{
		$query = $this->getQuery();

		$sql = $query->select('id', 'name')->from('table')->toSql();

		$this->assertEquals('SELECT `id`, `name` FROM `table`', $sql);
	}

	public function testAliasedColumnsSelect()
	{
		$query = $this->getQuery();

		$sql = $query->select('family AS f')->from('table')->toSql();

		$this->assertEquals('SELECT family AS f FROM `table`', $sql);
	}

	/*public function testAggregatedColumnsSelect()
	{
		$query = $this->getQuery();

		$sql = $query->select('COUNT(*)')->from('table')->toSql();

		$this->assertEquals('SELECT COUNT(*) FROM `table`', $sql);
	}*/

	public function getQuery()
	{
		return new DB;
	}
}